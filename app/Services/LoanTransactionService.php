<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use InvalidArgumentException; // For specific error type

final class LoanTransactionService
{
    private const LOG_AREA = 'LoanTransactionService:';
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Create an issue transaction for a loan application.
     *
     * @param LoanApplication $loanApplication
     * @param User $issuingOfficer (BPM Staff)
     * @param User $receivingOfficer (Applicant/Representative)
     * @param array $itemsData Array of items to be issued, each with ['loan_application_item_id', 'equipment_id', 'quantity_issued', 'accessories_checklist_item'?, 'issue_item_notes'?]
     * @param string|null $overallIssueNotes
     * @param array|null $overallAccessories
     * @return LoanTransaction
     * @throws RuntimeException | InvalidArgumentException
     */
    public function createIssueTransaction(
        LoanApplication $loanApplication,
        User $issuingOfficer,
        User $receivingOfficer,
        array $itemsData,
        ?string $overallIssueNotes = null,
        ?array $overallAccessories = null // Overall accessories for the transaction if any
    ): LoanTransaction {
        Log::info(self::LOG_AREA.'Creating ISSUE transaction.', [
            'loan_application_id' => $loanApplication->id, 'issuing_officer_id' => $issuingOfficer->id
        ]);

        if (empty($itemsData)) {
            throw new InvalidArgumentException('Sekurang-kurangnya satu item peralatan mesti dipilih untuk pengeluaran.');
        }

        // Policy checks (e.g., can $issuingOfficer issue for this $loanApplication) should happen in controller.
        // Service assumes authorization is granted.

        return DB::transaction(function () use (
            $loanApplication, $issuingOfficer, $receivingOfficer, $itemsData, $overallIssueNotes, $overallAccessories
        ) {
            $transaction = $loanApplication->transactions()->create([
                'type' => LoanTransaction::TYPE_ISSUE,
                'transaction_date' => now(),
                'issuing_officer_id' => $issuingOfficer->id,
                'receiving_officer_id' => $receivingOfficer->id,
                'accessories_checklist_on_issue' => $overallAccessories, // Overall checklist
                'issue_notes' => $overallIssueNotes,
                'status' => LoanTransaction::STATUS_ISSUED,
                // created_by, updated_by via BlameableObserver
            ]);
            Log::info(self::LOG_AREA.'LoanTransaction (ISSUE) record created.', ['id' => $transaction->id]);

            $totalIssuedForApplication = 0;
            foreach ($itemsData as $itemData) {
                /** @var Equipment $equipment */
                $equipment = Equipment::findOrFail($itemData['equipment_id']);
                /** @var LoanApplicationItem $appItem */
                $appItem = LoanApplicationItem::findOrFail($itemData['loan_application_item_id']);

                if ($equipment->status !== Equipment::STATUS_AVAILABLE) {
                    throw new RuntimeException("Peralatan '{$equipment->tag_id} - {$equipment->brand} {$equipment->model}' tidak tersedia untuk dikeluarkan.");
                }
                $quantityToIssue = (int) $itemData['quantity_issued'];
                if ($quantityToIssue <= 0) continue;

                // Check against loan application item requested/approved quantities
                $remainingToIssueOnAppItem = ($appItem->quantity_approved ?? $appItem->quantity_requested) - $appItem->quantity_issued;
                if ($quantityToIssue > $remainingToIssueOnAppItem) {
                    throw new RuntimeException("Kuantiti untuk dikeluarkan ({$quantityToIssue}) bagi '{$appItem->equipment_type}' melebihi baki yang diluluskan/dimohon ({$remainingToIssueOnAppItem}).");
                }

                $transaction->loanTransactionItems()->create([
                    'loan_application_item_id' => $appItem->id,
                    'equipment_id' => $equipment->id,
                    'quantity_transacted' => $quantityToIssue,
                    'status' => LoanTransactionItem::STATUS_ITEM_ISSUED,
                    'accessories_checklist_issue' => $itemData['accessories_checklist_item'] ?? null, // Item-specific
                    'item_notes' => $itemData['issue_item_notes'] ?? null,
                ]);

                // Update equipment status
                $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $issuingOfficer, "Issued for LA: {$loanApplication->id}");

                // Update quantities on LoanApplicationItem
                $appItem->increment('quantity_issued', $quantityToIssue);
                $totalIssuedForApplication += $quantityToIssue;
            }

            // Update LoanApplication status (e.g., to ISSUED or PARTIALLY_ISSUED)
            $this->updateLoanApplicationStatusAfterTransaction($loanApplication);

            Log::info(self::LOG_AREA.'Issue transaction completed.', ['transaction_id' => $transaction->id, 'total_items_processed' => count($itemsData)]);
            return $transaction;
        });
    }

    /**
     * Create a return transaction.
     *
     * @param LoanApplication $loanApplication
     * @param LoanTransaction $originalIssueTransaction The issue transaction items are being returned against
     * @param User $returningOfficer (Applicant/Representative)
     * @param User $returnAcceptingOfficer (BPM Staff)
     * @param array $itemsData Array of items returned, each with ['loan_transaction_item_id' (original issued item ID), 'quantity_returned', 'condition_on_return', 'item_status_on_return', 'accessories_checklist_item'?, 'return_item_notes'?]
     * @param string|null $overallReturnNotes
     * @param array|null $overallAccessories
     * @return LoanTransaction
     * @throws RuntimeException | InvalidArgumentException
     */
    public function createReturnTransaction(
        LoanApplication $loanApplication,
        LoanTransaction $originalIssueTransaction, // Pass the specific issue transaction
        User $returningOfficer,
        User $returnAcceptingOfficer,
        array $itemsData,
        ?string $overallReturnNotes = null,
        ?array $overallAccessories = null // Overall accessories for the transaction if any
    ): LoanTransaction {
        Log::info(self::LOG_AREA.'Creating RETURN transaction.', [
            'loan_application_id' => $loanApplication->id, 'issue_transaction_id' => $originalIssueTransaction->id
        ]);

        if (empty($itemsData)) {
            throw new InvalidArgumentException('Sekurang-kurangnya satu item peralatan mesti dinyatakan untuk pemulangan.');
        }
        if ($originalIssueTransaction->type !== LoanTransaction::TYPE_ISSUE) {
            throw new InvalidArgumentException('Transaksi rujukan mestilah transaksi pengeluaran.');
        }


        return DB::transaction(function () use (
            $loanApplication, $originalIssueTransaction, $returningOfficer, $returnAcceptingOfficer, $itemsData, $overallReturnNotes, $overallAccessories
        ) {
            $returnTransaction = $loanApplication->transactions()->create([
                'type' => LoanTransaction::TYPE_RETURN,
                'transaction_date' => now(),
                'returning_officer_id' => $returningOfficer->id,
                'return_accepting_officer_id' => $returnAcceptingOfficer->id,
                'accessories_checklist_on_return' => $overallAccessories,
                'return_notes' => $overallReturnNotes,
                'related_transaction_id' => $originalIssueTransaction->id, // Link to the issue
                'status' => LoanTransaction::STATUS_COMPLETED, // Or set based on item conditions, e.g., RETURNED_GOOD, RETURNED_DAMAGED
                // created_by, updated_by via BlameableObserver
            ]);
            Log::info(self::LOG_AREA.'LoanTransaction (RETURN) record created.', ['id' => $returnTransaction->id]);

            $allGood = true;
            $anyLost = false;

            foreach ($itemsData as $itemData) {
                /** @var LoanTransactionItem $issuedItem */
                $issuedItem = LoanTransactionItem::where('loan_transaction_id', $originalIssueTransaction->id)
                                                ->findOrFail($itemData['loan_transaction_item_id']);
                /** @var Equipment $equipment */
                $equipment = Equipment::findOrFail($issuedItem->equipment_id);
                /** @var LoanApplicationItem $appItem */
                $appItem = LoanApplicationItem::findOrFail($issuedItem->loan_application_item_id);

                $quantityToReturn = (int) $itemData['quantity_returned'];
                if ($quantityToReturn <= 0) continue;

                // Basic check: cannot return more than was issued for this specific item line
                // More complex logic would be needed if partial returns of an issued item line were allowed and tracked
                if ($quantityToReturn > $issuedItem->quantity_transacted) { // quantity_transacted on $issuedItem is the originally issued quantity
                    throw new RuntimeException("Kuantiti pemulangan ({$quantityToReturn}) bagi '{$equipment->tag_id}' melebihi kuantiti asal dikeluarkan ({$issuedItem->quantity_transacted}).");
                }

                $returnTransaction->loanTransactionItems()->create([
                    'loan_application_item_id' => $appItem->id,
                    'equipment_id' => $equipment->id,
                    'quantity_transacted' => $quantityToReturn, // This is quantity_returned
                    'status' => $itemData['item_status_on_return'], // e.g., RETURNED_GOOD, REPORTED_LOST
                    'condition_on_return' => $itemData['condition_on_return'],
                    'accessories_checklist_return' => $itemData['accessories_checklist_item'] ?? null,
                    'item_notes' => $itemData['return_item_notes'] ?? null,
                ]);

                // Update equipment status and condition based on return
                $newEquipmentStatus = Equipment::STATUS_AVAILABLE; // Default
                if ($itemData['item_status_on_return'] === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) {
                    $newEquipmentStatus = Equipment::STATUS_LOST;
                    $anyLost = true;
                } elseif (in_array($itemData['condition_on_return'], [Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE])) {
                    $newEquipmentStatus = Equipment::STATUS_DAMAGED_NEEDS_REPAIR; // Or directly unserviceable
                    $allGood = false;
                } elseif ($itemData['condition_on_return'] === Equipment::CONDITION_MINOR_DAMAGE) {
                    $newEquipmentStatus = Equipment::STATUS_DAMAGED_NEEDS_REPAIR; // Or under_maintenance
                    $allGood = false;
                }
                $this->equipmentService->changeOperationalStatus($equipment, $newEquipmentStatus, $returnAcceptingOfficer, "Returned for LA: {$loanApplication->id}");
                $this->equipmentService->changeConditionStatus($equipment, $itemData['condition_on_return'], $returnAcceptingOfficer);


                // Update quantities on LoanApplicationItem
                $appItem->increment('quantity_returned', $quantityToReturn);
            }

            // Update overall return transaction status
            if ($anyLost) {
                // If any item is lost, the transaction might reflect that, e.g. if there's a specific status for it
                 $returnTransaction->status = LoanTransaction::STATUS_REPORTED_LOST; // If overall transaction can be marked as such
            } elseif (!$allGood) {
                $returnTransaction->status = LoanTransaction::STATUS_RETURNED_DAMAGED;
            } else {
                $returnTransaction->status = LoanTransaction::STATUS_RETURNED_GOOD;
            }
            $returnTransaction->save();


            // Update LoanApplication status
            $this->updateLoanApplicationStatusAfterTransaction($loanApplication);

            Log::info(self::LOG_AREA.'Return transaction completed.', ['transaction_id' => $returnTransaction->id, 'total_items_processed' => count($itemsData)]);
            return $returnTransaction;
        });
    }


    /**
     * Updates the parent LoanApplication status based on its items' issued and returned quantities.
     */
    public function updateLoanApplicationStatusAfterTransaction(LoanApplication $loanApplication): void
    {
        $loanApplication->refresh(); // Reload from DB to get latest item counts
        $totalRequested = 0;
        $totalApproved = 0;
        $totalIssued = 0;
        $totalReturned = 0;

        foreach ($loanApplication->applicationItems as $item) {
            $totalRequested += $item->quantity_requested;
            $totalApproved += $item->quantity_approved ?? $item->quantity_requested; // Fallback if approval step doesn't set quantity_approved
            $totalIssued += $item->quantity_issued;
            $totalReturned += $item->quantity_returned;
        }

        $newStatus = $loanApplication->status;

        if ($totalIssued === 0 && $loanApplication->status === LoanApplication::STATUS_APPROVED) {
            // Remains approved if nothing issued yet
            $newStatus = LoanApplication::STATUS_APPROVED;
        } elseif ($totalIssued > 0 && $totalIssued < $totalApproved) {
            $newStatus = LoanApplication::STATUS_PARTIALLY_ISSUED;
        } elseif ($totalIssued > 0 && $totalIssued >= $totalApproved) {
            // All approved items have been issued
            if ($totalReturned < $totalIssued) {
                // Check for overdue if not fully returned
                if (now()->gt($loanApplication->loan_end_date)) {
                    $newStatus = LoanApplication::STATUS_OVERDUE;
                } else {
                    $newStatus = LoanApplication::STATUS_ISSUED;
                }
            } elseif ($totalReturned >= $totalIssued) {
                // All issued items have been returned
                $newStatus = LoanApplication::STATUS_RETURNED;
            }
        }
        // If it was rejected or cancelled, it should stay that way unless a new action is taken.
        // If it was draft, it should have moved to pending_support etc.

        if ($newStatus !== $loanApplication->status) {
            Log::info(self::LOG_AREA."Updating LoanApplication status.", [
                'id' => $loanApplication->id, 'old_status' => $loanApplication->status, 'new_status' => $newStatus
            ]);
            $loanApplication->status = $newStatus;
            $loanApplication->save();
        }
    }
}
