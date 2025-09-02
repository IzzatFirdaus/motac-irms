<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Service class to handle logic related to loan transactions (issue, return, etc.)
 * for ICT equipment.
 */
final class LoanTransactionService
{
    private const LOG_AREA = 'LoanTransactionService: ';

    private EquipmentService $equipmentService;

    private NotificationService $notificationService;

    public function __construct(EquipmentService $equipmentService, NotificationService $notificationService)
    {
        $this->equipmentService    = $equipmentService;
        $this->notificationService = $notificationService;
        // Touch dependencies for static analysis (no runtime effect)
        $this->markDependenciesAsRead();
    }

    /**
     * Process a new equipment issue transaction for a loan application.
     */
    public function processNewIssue(
        LoanApplication $loanApplication,
        array $itemsPayload,
        User $issuingOfficer,
        array $transactionDetails
    ): LoanTransaction {
        Log::info(self::LOG_AREA.sprintf('Processing new issue for LA ID: %d by User ID: %d', $loanApplication->id, $issuingOfficer->id), [
            'item_count'   => count($itemsPayload),
            'details_keys' => array_keys($transactionDetails),
        ]);

        // Normalize incoming item payload (IssueEquipmentRequest keys)
        $itemDataForTransaction = array_map(function ($item) {
            return [
                'equipment_id'             => $item['equipment_id'],
                'loan_application_item_id' => $item['loan_application_item_id'] ?? null,
                // quantity_issued from request maps to quantity_transacted in transaction items
                'quantity_transacted' => (int) ($item['quantity_issued'] ?? 1),
                // Set explicit status for issued items for downstream calculations
                'status' => \App\Models\LoanTransactionItem::STATUS_ITEM_ISSUED,
                // Prefer item-level notes key from request
                'item_notes' => $item['issue_item_notes'] ?? ($item['notes'] ?? null),
                // Item-level accessories checklist (if any)
                'accessories_checklist_issue' => $item['accessories_checklist_item'] ?? ($item['accessories'] ?? []),
            ];
        }, $itemsPayload);

        return DB::transaction(function () use ($loanApplication, $itemDataForTransaction, $issuingOfficer, $transactionDetails) {
            // Create the loan transaction of type "issue"
            $transaction = $loanApplication->loanTransactions()->create([
                'type' => LoanTransaction::TYPE_ISSUE,
                // Use provided transaction_date when available
                'transaction_date'     => isset($transactionDetails['transaction_date']) ? \Illuminate\Support\Carbon::parse((string) $transactionDetails['transaction_date']) : \now(),
                'status'               => LoanTransaction::STATUS_ISSUED,
                'issuing_officer_id'   => $issuingOfficer->id,
                'receiving_officer_id' => $transactionDetails['receiving_officer_id'] ?? null,
                // Overall notes/accessories at transaction level
                'issue_notes'                    => $transactionDetails['issue_notes']                ?? ($transactionDetails['notes'] ?? null),
                'accessories_checklist_on_issue' => $transactionDetails['accessories_checklist_item'] ?? ($transactionDetails['accessories'] ?? []),
            ]);

            // For each equipment item, create transaction item and update equipment status
            foreach ($itemDataForTransaction as $itemData) {
                $transactionItem = $transaction->loanTransactionItems()->create($itemData);

                $equipment = Equipment::find($itemData['equipment_id']);
                if (! $equipment instanceof Equipment) {
                    Log::error(self::LOG_AREA.sprintf('Equipment ID %d not found for transaction item creation.', $itemData['equipment_id']));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
                $equipment->status = Equipment::STATUS_ON_LOAN;
                $equipment->save();
                Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to ON_LOAN for transaction ID %d.', $equipment->id, $transaction->id));

            }

            // Update loan application status
            $loanApplication->status = LoanApplication::STATUS_ISSUED;
            $loanApplication->save();

            // Notify user/applicant of issue
            $this->notificationService->notifyLoanIssued($loanApplication->user, $loanApplication, $transaction);

            Log::info(self::LOG_AREA.sprintf('Loan Application ID %d successfully issued via transaction ID %d.', $loanApplication->id, $transaction->id));

            return $transaction;
        });
    }

    /**
     * Update an existing loan transaction as issued (when confirming issuance).
     */
    public function issueLoanTransaction(
        LoanTransaction $transaction,
        array $loanApplicationItems,
        array $accessories,
        ?string $notes
    ): void {
        DB::transaction(function () use ($transaction, $loanApplicationItems, $accessories, $notes) {
            $transaction->issue_notes                    = $notes;
            $transaction->accessories_checklist_on_issue = $accessories;
            $transaction->status                         = LoanTransaction::STATUS_ISSUED;
            $transaction->transaction_date               = \now();
            $transaction->save();
            Log::info(self::LOG_AREA.sprintf('Loan Transaction ID %d updated and marked as ISSUED.', $transaction->id));

            foreach ($loanApplicationItems as $itemData) {
                $transactionItem = LoanTransactionItem::find($itemData['id']);

                if (! ($transactionItem instanceof LoanTransactionItem)) {
                    Log::error(self::LOG_AREA.sprintf('Loan Transaction Item ID %d not found during issueLoanTransaction.', $itemData['id']));
                    throw new RuntimeException('Loan Transaction Item not found.');
                }

                $transactionItem->quantity_transacted = $itemData['quantity_transacted'];
                // Some code paths use 'notes' or 'item_notes' interchangeably
                $transactionItem->setAttribute('notes', $itemData['notes'] ?? $itemData['item_notes'] ?? null);
                $transactionItem->save();

                $equipment = $transactionItem->equipment;
                if (! $equipment instanceof Equipment) {
                    Log::error(self::LOG_AREA.sprintf('Equipment not found for Loan Transaction Item ID %d during issue.', $transactionItem->id));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
                $equipment->status = Equipment::STATUS_ON_LOAN;
                $equipment->save();
                Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to ON_LOAN during issue transaction.', $equipment->id));

            }

            $transaction->loanApplication->updateOverallStatusAfterTransaction();

            // Notify applicant of issue
            $this->notificationService->notifyLoanIssued(
                $transaction->loanApplication->user,
                $transaction->loanApplication,
                $transaction
            );
        });
    }

    /**
     * Update a loan transaction as returned, update equipment and notify applicant.
     */
    public function returnLoanTransaction(
        LoanTransaction $transaction,
        array $loanTransactionItemsData,
        array $accessories,
        ?string $notes
    ): void {
        DB::transaction(function () use ($transaction, $loanTransactionItemsData, $accessories, $notes) {
            $transaction->return_notes                    = $notes;
            $transaction->accessories_checklist_on_return = $accessories;
            $transaction->transaction_date                = Carbon::now();
            $transaction->status                          = $this->determineOverallReturnTransactionStatus($loanTransactionItemsData);
            $transaction->save();
            Log::info(self::LOG_AREA.sprintf('Loan Transaction ID %d updated and marked as %s.', $transaction->id, $transaction->status));

            foreach ($loanTransactionItemsData as $itemData) {
                $transactionItem = LoanTransactionItem::find($itemData['id']);

                if (! ($transactionItem instanceof LoanTransactionItem)) {
                    Log::error(self::LOG_AREA.sprintf('Loan Transaction Item ID %d not found during returnLoanTransaction.', $itemData['id']));
                    throw new RuntimeException('Loan Transaction Item not found.');
                }

                $transactionItem->setAttribute('quantity_returned', $itemData['quantity_transacted']);
                $transactionItem->setAttribute('condition_on_return', $itemData['equipment_condition_on_return'] ?? Equipment::CONDITION_GOOD);
                $transactionItem->setAttribute('item_notes', $itemData['return_notes'] ?? null);
                $transactionItem->setAttribute('return_status', $this->determineItemReturnStatus((string) $transactionItem->getAttribute('condition_on_return')));
                $transactionItem->setAttribute('accessories_checklist_on_return', $accessories);
                $transactionItem->save();

                $equipment = $transactionItem->equipment;
                if (! $equipment instanceof Equipment) {
                    Log::error(self::LOG_AREA.sprintf('Equipment not found for Loan Transaction Item ID %d during return.', $transactionItem->id));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
                switch ((string) $transactionItem->getAttribute('condition_on_return')) {
                    case Equipment::CONDITION_GOOD:
                        $equipment->status = Equipment::STATUS_AVAILABLE;
                        break;
                    case Equipment::CONDITION_MINOR_DAMAGE:
                    case Equipment::CONDITION_MAJOR_DAMAGE:
                    case Equipment::CONDITION_UNSERVICEABLE:
                        $equipment->status = Equipment::STATUS_DAMAGED;
                        break;
                    case Equipment::CONDITION_LOST:
                        $equipment->status = Equipment::STATUS_LOST;
                        break;
                    default:
                        $equipment->status = Equipment::STATUS_AVAILABLE;
                        break;
                }
                $equipment->save();
                Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to %s during return transaction.', $equipment->id, $equipment->status));

            }

            $transaction->loanApplication->updateOverallStatusAfterTransaction();

            // Notify applicant of return
            $this->notificationService->notifyLoanReturned(
                $transaction->loanApplication->user,
                $transaction->loanApplication,
                $transaction
            );
        });
    }

    /**
     * Process the return of equipment for an existing loan transaction (called from controller).
     *
     * @param LoanTransaction $loanTransaction        The original issue transaction.
     * @param array           $items                  An array of items being returned (expected structure: quantity, equipment_id, etc.).
     * @param User            $returnAcceptingOfficer The user accepting the returned items.
     * @param array           $details                Additional request details, e.g., accessories or notes.
     */
    public function processExistingReturn(
        LoanTransaction $loanTransaction,
        array $items,
        User $returnAcceptingOfficer,
        array $details = []
    ): void {
        DB::transaction(function () use ($loanTransaction, $items, $returnAcceptingOfficer, $details) {
            // Create a new LoanTransaction of type 'return'
            $returnTransaction                              = $loanTransaction->replicate();
            $returnTransaction->type                        = LoanTransaction::TYPE_RETURN;
            $returnTransaction->issuing_officer_id          = null;
            $returnTransaction->receiving_officer_id        = null;
            $returnTransaction->returning_officer_id        = $returnAcceptingOfficer->id;
            $returnTransaction->return_accepting_officer_id = $returnAcceptingOfficer->id;
            $returnTransaction->transaction_date            = \now();
            $returnTransaction->status                      = LoanTransaction::STATUS_RETURNED;
            $returnTransaction->related_transaction_id      = $loanTransaction->id;
            if (isset($details['return_notes'])) {
                $returnTransaction->return_notes = $details['return_notes'];
            }
            $returnTransaction->save();

            // Save returned items and update equipment status
            foreach ($items as $item) {
                // Try to link back to the original issued item to preserve application item linkage
                $originalIssuedItem      = isset($item['loan_transaction_item_id']) ? LoanTransactionItem::find((int) $item['loan_transaction_item_id']) : null;
                $linkedApplicationItemId = $originalIssuedItem?->loan_application_item_id;
                $equipmentIdFromOriginal = $originalIssuedItem?->equipment_id;
                $equipmentId             = $equipmentIdFromOriginal ?? ($item['equipment_id'] ?? null);

                if (! $equipmentId) {
                    // If we cannot determine equipment id, skip this item gracefully
                    Log::warning(self::LOG_AREA.'Skipping return item creation because equipment_id could not be determined.', [
                        'original_item_id' => $item['loan_transaction_item_id'] ?? null,
                    ]);

                    continue;
                }

                $returnTransaction->loanTransactionItems()->create([
                    'equipment_id'             => $equipmentId,
                    'loan_application_item_id' => $linkedApplicationItemId,
                    // Map quantity_returned from request into quantity_transacted field
                    'quantity_transacted' => (int) ($item['quantity_returned'] ?? $item['quantity_transacted'] ?? 1),
                    'status'              => $item['item_status_on_return'] ?? ($item['status'] ?? LoanTransactionItem::STATUS_ITEM_RETURNED),
                    'condition_on_return' => $item['condition_on_return']   ?? Equipment::CONDITION_GOOD,
                    'item_notes'          => $item['return_item_notes']     ?? ($item['item_notes'] ?? null),
                ]);

                $equipment = Equipment::find($equipmentId);
                if (! $equipment instanceof Equipment) {
                    continue;
                }
                // Set equipment status based on return condition
                switch ($item['condition_on_return'] ?? Equipment::CONDITION_GOOD) {
                    case Equipment::CONDITION_GOOD:
                        $equipment->status = Equipment::STATUS_AVAILABLE;
                        break;
                    case Equipment::CONDITION_MINOR_DAMAGE:
                    case Equipment::CONDITION_MAJOR_DAMAGE:
                    case Equipment::CONDITION_UNSERVICEABLE:
                        $equipment->status = Equipment::STATUS_DAMAGED;
                        break;
                    case Equipment::CONDITION_LOST:
                        $equipment->status = Equipment::STATUS_LOST;
                        break;
                    default:
                        $equipment->status = Equipment::STATUS_AVAILABLE;
                        break;
                }
                $equipment->save();
                Log::info(self::LOG_AREA.sprintf('Equipment ID %d status updated during existing return.', $equipment->id));

            }

            // Optionally update the original transaction status
            $loanTransaction->status = LoanTransaction::STATUS_RETURNED;
            $loanTransaction->save();

            // Update the parent loan application status
            if ($loanTransaction->loanApplication) {
                $loanTransaction->loanApplication->updateOverallStatusAfterTransaction();
            }

            // Notify user/applicant about return
            $this->notificationService->notifyLoanReturned(
                $loanTransaction->loanApplication->user,
                $loanTransaction->loanApplication,
                $returnTransaction
            );

            Log::info(self::LOG_AREA.sprintf(
                'Processed return for original transaction ID %d (return transaction ID %d) by user ID %d.',
                $loanTransaction->id,
                $returnTransaction->id,
                $returnAcceptingOfficer->id
            ));
        });
    }

    /**
     * Determines the item return status based on the condition on return.
     */
    private function determineItemReturnStatus(string $conditionOnReturn): string
    {
        return match ($conditionOnReturn) {
            Equipment::CONDITION_MINOR_DAMAGE  => LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            Equipment::CONDITION_MAJOR_DAMAGE  => LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            Equipment::CONDITION_UNSERVICEABLE => LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
            Equipment::CONDITION_LOST          => LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
            default                            => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
        };
    }

    /**
     * Determines the overall transaction return status by inspecting all items' conditions.
     */
    private function determineOverallReturnTransactionStatus(array $itemData): string
    {
        $hasDamage       = false;
        $hasLost         = false;
        $allReturnedGood = true;

        foreach ($itemData as $item) {
            $condition = $item['equipment_condition_on_return'] ?? Equipment::CONDITION_GOOD;
            if ($condition === Equipment::CONDITION_LOST) {
                $hasLost         = true;
                $allReturnedGood = false;
            }
            if (in_array($condition, [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE])) {
                $hasDamage       = true;
                $allReturnedGood = false;
            }
        }

        if ($hasLost && $hasDamage) {
            return LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS;
        }

        if ($hasLost) {
            return LoanTransaction::STATUS_ITEMS_REPORTED_LOST;
        }

        if ($hasDamage) {
            return LoanTransaction::STATUS_RETURNED_DAMAGED;
        }

        if ($allReturnedGood) {
            return LoanTransaction::STATUS_RETURNED_GOOD;
        }

        return LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION;
    }

    /**
     * Helper to satisfy static analysis: touch dependencies so they're considered read.
     * No runtime effect.
     */
    private function markDependenciesAsRead(): void
    {
        // Use noop ternary to read properties without causing unreachable-code warnings
        $void1 = $this->equipmentService    ?? null;
        $void2 = $this->notificationService ?? null;
    }
}
