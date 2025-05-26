<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem; // Alias removed for direct use
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // For column check
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class LoanTransactionService
{
    private const LOG_AREA = 'LoanTransactionService: ';

    public function __construct()
    {
        // Dependencies can be injected if needed
    }

    /**
     * Creates a loan transaction (issue or return) and its associated items.
     * Updates equipment statuses and loan application status accordingly.
     *
     * @param LoanApplication $loanApplication The parent loan application.
     * @param string $type Transaction type (LoanTransaction::TYPE_ISSUE or LoanTransaction::TYPE_RETURN).
     * @param User $actingOfficer The officer performing the transaction.
     * @param array<int, array{equipment_id: int, quantity: int, loan_application_item_id?: int|null, original_loan_transaction_item_id?: int|null, notes?: string|null, accessories_data?: array|string|null, condition_on_return?: string|null, item_status_on_return?: string|null}> $itemData Array of items involved in the transaction.
     * @param array<string, mixed> $extraDetails Additional details for LoanTransaction (e.g., transaction_date, officer IDs, notes).
     * @return LoanTransaction The created loan transaction.
     * @throws InvalidArgumentException If input data is invalid.
     * @throws ModelNotFoundException If related models (equipment) are not found.
     * @throws RuntimeException If a general error occurs.
     */
    public function createTransaction(
        LoanApplication $loanApplication,
        string $type,
        User $actingOfficer, // Officer initiating this transaction (e.g., BPM staff for issue/return acceptance)
        array $itemData,
        array $extraDetails = []
    ): LoanTransaction {
        $appIdLog = $loanApplication->id;
        $officerIdLog = $actingOfficer->id;

        Log::info(self::LOG_AREA . "Attempting to create loan transaction of type '{$type}' for LA ID {$appIdLog} by User ID {$officerIdLog}.", [
            'item_count' => count($itemData), 'details_keys' => array_keys($extraDetails),
        ]);

        if (!in_array($type, [LoanTransaction::TYPE_ISSUE, LoanTransaction::TYPE_RETURN], true)) {
            throw new InvalidArgumentException("Jenis transaksi tidak sah: {$type}.");
        }
        if (empty($itemData)) {
            throw new InvalidArgumentException('Data item transaksi tidak boleh kosong.');
        }

        DB::beginTransaction();
        try {
            $transactionDetails = [
                'loan_application_id' => $loanApplication->id,
                'type' => $type,
                'transaction_date' => $extraDetails['transaction_date'] ?? Carbon::now(),
                'status' => $extraDetails['status'] ?? LoanTransaction::STATUS_PENDING, // Default status
            ];

            // Set officer IDs based on transaction type and provided details
            if ($type === LoanTransaction::TYPE_ISSUE) {
                $transactionDetails['issuing_officer_id'] = $actingOfficer->id; // Officer creating the issue TX
                $transactionDetails['receiving_officer_id'] = $extraDetails['receiving_officer_id'] ?? $loanApplication->user_id; // Person receiving equipment
                $transactionDetails['issue_notes'] = $extraDetails['issue_notes'] ?? null;
                $transactionDetails['issue_timestamp'] = $transactionDetails['transaction_date']; // Align with transaction date
            } elseif ($type === LoanTransaction::TYPE_RETURN) {
                $transactionDetails['return_accepting_officer_id'] = $actingOfficer->id; // BPM staff accepting return
                $transactionDetails['returning_officer_id'] = $extraDetails['returning_officer_id'] ?? $loanApplication->user_id; // Person physically returning
                $transactionDetails['return_notes'] = $extraDetails['return_notes'] ?? null;
                $transactionDetails['return_timestamp'] = $transactionDetails['transaction_date'];
                $transactionDetails['related_transaction_id'] = $extraDetails['related_transaction_id'] ?? null;
            }

            /** @var LoanTransaction $transaction */
            $transaction = LoanTransaction::create($transactionDetails);
            $txIdLog = $transaction->id;

            foreach ($itemData as $item) {
                $equipment = Equipment::findOrFail($item['equipment_id']);
                $quantityTransacted = (int)($item['quantity'] ?? 1); // From service layer call

                $txItemData = [
                    'equipment_id' => $equipment->id,
                    'loan_application_item_id' => $item['loan_application_item_id'] ?? null,
                    'quantity_transacted' => $quantityTransacted,
                    'item_notes' => $item['notes'] ?? null,
                ];

                if ($type === LoanTransaction::TYPE_ISSUE) {
                    $txItemData['status'] = LoanTransactionItem::STATUS_ITEM_ISSUED;
                    $txItemData['accessories_checklist_issue'] = $item['accessories_data'] ?? null; // Passed as 'accessories_data'

                    $equipment->updateOperationalStatus(Equipment::STATUS_ON_LOAN, __("Dikeluarkan melalui Transaksi #:txId", ['txId' => $txIdLog]), $actingOfficer->id);
                    if ($equipment->condition_status === Equipment::CONDITION_NEW) { // Update from NEW to GOOD upon first issue
                        $equipment->updatePhysicalConditionStatus(Equipment::CONDITION_GOOD, __("Status keadaan ditukar kepada 'Baik' semasa pengeluaran pertama."), $actingOfficer->id);
                    }

                } elseif ($type === LoanTransaction::TYPE_RETURN) {
                    $conditionOnReturn = $item['condition_on_return'] ?? $equipment->condition_status; // If not specified, assume same as current
                    $itemStatusOnReturn = $item['item_status_on_return'] ?? LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD; // Get from form

                    $txItemData['status'] = $itemStatusOnReturn;
                    $txItemData['condition_on_return'] = $conditionOnReturn;
                    $txItemData['accessories_checklist_return'] = $item['accessories_data'] ?? null;

                    // Update equipment status based on the reported item status and condition
                    $newEquipmentOpStatus = Equipment::STATUS_AVAILABLE; // Default for return
                    if ($itemStatusOnReturn === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) {
                        $newEquipmentOpStatus = Equipment::STATUS_LOST;
                    } elseif (in_array($itemStatusOnReturn, [LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE, LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE, LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN])) {
                        $newEquipmentOpStatus = Equipment::STATUS_UNDER_MAINTENANCE; // Or specific damaged status
                        if ($itemStatusOnReturn === LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN) {
                             $newEquipmentOpStatus = Equipment::STATUS_DISPOSED; // Or pending disposal
                        }
                    }
                    $equipment->updateOperationalStatus($newEquipmentOpStatus, __("Dikembalikan melalui Transaksi #:txId. Status Item: :itemStatus", ['txId' => $txIdLog, 'itemStatus' => LoanTransactionItem::getStatusOptions()[$itemStatusOnReturn] ?? $itemStatusOnReturn]), $actingOfficer->id);
                    $equipment->updatePhysicalConditionStatus($conditionOnReturn, __("Keadaan semasa pemulangan Transaksi #:txId.", ['txId' => $txIdLog]), $actingOfficer->id);
                }
                /** @var LoanTransactionItem $transactionItem */
                $transactionItem = $transaction->loanTransactionItems()->create($txItemData);

                // Update quantities on the LoanApplicationItem
                if ($transactionItem->loanApplicationItem) {
                    $transactionItem->loanApplicationItem->recalculateQuantities();
                    $transactionItem->loanApplicationItem->save();
                }
                Log::debug(self::LOG_AREA . "Created LoanTransactionItem ID: {$transactionItem->id} for Tx ID: {$txIdLog}");
            }

            // Finalize transaction status if it was pending initially
            if ($transaction->status === LoanTransaction::STATUS_PENDING) {
                if ($type === LoanTransaction::TYPE_ISSUE) $transaction->status = LoanTransaction::STATUS_ISSUED;
                // For return, it might stay as PENDING_INSPECTION or move to a completed state based on items
                elseif ($type === LoanTransaction::TYPE_RETURN) {
                    // Example: if all items are good, set to RETURNED_GOOD. Complex logic needed here.
                    // For now, it's set via $extraDetails['status'] in service call for returns.
                    $transaction->status = $extraDetails['status'] ?? LoanTransaction::STATUS_RETURNED_GOOD;
                }
                $transaction->save();
            }

            $loanApplication->updateOverallStatusAfterTransaction();
            Log::debug(self::LOG_AREA . "Called updateOverallStatusAfterTransaction for LoanApplication ID: {$appIdLog}");

            DB::commit();
            Log::info(self::LOG_AREA . "Loan transaction ID {$txIdLog} of type '{$type}' created successfully.");
            return $transaction->fresh([
                'loanTransactionItems.equipment', 'loanApplication', 'issuingOfficer', 'returnAcceptingOfficer', 'receivingOfficer', 'returningOfficer',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Peralatan tidak ditemui semasa penciptaan transaksi untuk App ID {$appIdLog}.", ['exception_message' => $e->getMessage()]);
            throw new ModelNotFoundException(__('Satu atau lebih item peralatan tidak ditemui: ') . $e->getMessage(), $e->getCode(), $e);
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Argumen tidak sah semasa penciptaan transaksi untuk App ID {$appIdLog}: " . $e->getMessage());
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Ralat generik semasa penciptaan transaksi untuk App ID {$appIdLog}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal mencipta transaksi pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    public function findTransaction(int $id, array $with = []): ?LoanTransaction
    {
        Log::debug(self::LOG_AREA . "Finding loan transaction ID {$id}.", ['with' => $with]);
        try {
            $query = LoanTransaction::query();
            $defaultWith = ['loanApplication.user', 'loanTransactionItems.equipment', 'issuingOfficer', 'receivingOfficer', 'returningOfficer', 'returnAcceptingOfficer'];
            $query->with(array_unique(array_merge($defaultWith, $with)));

            /** @var LoanTransaction|null $transaction */
            $transaction = $query->find($id);
            if (!$transaction) Log::notice(self::LOG_AREA . "Loan transaction ID {$id} not found.");
            return $transaction;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA . "Error finding loan transaction ID {$id}: " . $e->getMessage());
            throw new RuntimeException(__('Gagal mendapatkan butiran transaksi pinjaman.'), 0, $e);
        }
    }

    public function getTransactions(array $filters = [], array $with = [], ?int $perPage = 15, string $sortBy = 'transaction_date', string $sortDirection = 'desc'): LengthAwarePaginator|SupportCollection
    {
        Log::debug(self::LOG_AREA . 'Getting transactions.', ['filters' => $filters, 'with' => $with, 'perPage' => $perPage]);
        $query = LoanTransaction::query();

        $defaultWith = ['loanApplication.user', 'issuingOfficer', 'returnAcceptingOfficer'];
        $query->with(array_unique(array_merge($defaultWith, $with)));


        if (!empty($filters['type'])) $query->where('type', $filters['type']);
        if (!empty($filters['loan_application_id'])) $query->where('loan_application_id', (int) $filters['loan_application_id']);
        if (!empty($filters['officer_id'])) {
            $officerId = (int) $filters['officer_id'];
            $query->where(fn($q) => $q->where('issuing_officer_id', $officerId)->orWhere('return_accepting_officer_id', $officerId)->orWhere('receiving_officer_id', $officerId)->orWhere('returning_officer_id', $officerId));
        }
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['date_from'])) $query->whereDate('transaction_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to'])) $query->whereDate('transaction_date', '<=', $filters['date_to']);


        $sortDirectionSafe = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['transaction_date', 'created_at', 'type', 'status', 'updated_at'];
        $safeSortBy = in_array($sortBy, $allowedSorts) && Schema::hasColumn((new LoanTransaction)->getTable(), $sortBy) ? $sortBy : 'transaction_date';

        $query->orderBy($safeSortBy, $sortDirectionSafe);

        return ($perPage !== null && $perPage > 0) ? $query->paginate($perPage) : $query->get();
    }

    public function updateTransaction(LoanTransaction $transaction, array $validatedData, User $actingOfficer): LoanTransaction
    {
        $txIdLog = $transaction->id;
        Log::info(self::LOG_AREA . "Attempting to update loan transaction ID {$txIdLog}.", ['data_keys' => array_keys($validatedData)]);
        DB::beginTransaction();
        try {
            // Only certain fields of a transaction should be updatable, e.g., notes, status (manual override), accessories.
            // Changing items or core details like type/date might require deleting and recreating.
            // For now, simple fill and save. Add specific logic based on what's editable.
            if (isset($validatedData['return_notes'])) $transaction->return_notes = $validatedData['return_notes'];
            if (isset($validatedData['issue_notes'])) $transaction->issue_notes = $validatedData['issue_notes'];
            if (isset($validatedData['status'])) $transaction->status = $validatedData['status'];
            // Add other updatable fields

            $transaction->save(); // BlameableObserver handles updated_by

            // If status or items changed significantly, the parent application status might need re-evaluation
            if ($transaction->loanApplication) {
                $transaction->loanApplication->updateOverallStatusAfterTransaction();
            }
            DB::commit();
            Log::info(self::LOG_AREA . "Loan transaction ID {$txIdLog} updated successfully by User ID {$actingOfficer->id}.");
            return $transaction->fresh($transaction->getRelations()); // Reload relations
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Error updating loan transaction ID {$txIdLog}: " . $e->getMessage(), ['data' => $validatedData]);
            throw new RuntimeException(__('Gagal mengemaskini transaksi pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function deleteTransaction(LoanTransaction $transaction, User $actingOfficer): bool
    {
        $txIdLog = $transaction->id;
        Log::info(self::LOG_AREA . "Attempting to delete loan transaction ID {$txIdLog} by User ID: {$actingOfficer->id}.");
        // Add authorization check if needed: $actingOfficer->can('delete', $transaction)

        DB::beginTransaction();
        try {
            $transactionType = $transaction->type;
            $loanApplication = $transaction->loanApplication()->first(); // Get a fresh instance

            /** @var SupportCollection<int, LoanTransactionItem> $itemsToRevert */
            $itemsToRevert = $transaction->loanTransactionItems()->with('equipment')->get();

            foreach ($itemsToRevert as $txItem) {
                if (!$txItem->equipment) continue;
                /** @var Equipment $equipment */
                $equipment = $txItem->equipment;
                $revertNote = __("Transaksi ID :txId (:type) dibatalkan oleh :officer.", ['txId' => $txIdLog, 'type' => $transactionType, 'officer' => $actingOfficer->name]);

                if ($transactionType === LoanTransaction::TYPE_ISSUE) {
                    // Equipment becomes available, condition status might need review if it was changed upon issue
                    $equipment->updateOperationalStatus(Equipment::STATUS_AVAILABLE, $revertNote, $actingOfficer->id);
                } elseif ($transactionType === LoanTransaction::TYPE_RETURN) {
                    // Equipment goes back to on_loan. Original condition before this return might be hard to ascertain
                    // without more complex logging or state tracking.
                    $equipment->updateOperationalStatus(Equipment::STATUS_ON_LOAN, $revertNote, $actingOfficer->id);
                    // Revert condition if it was changed by this specific return transaction.
                    // This requires knowing the condition *before* this return transaction.
                    // For simplicity, this example does not revert condition status here.
                }
                // Soft delete the transaction item
                $txItem->delete();
            }

            $deleted = $transaction->delete(); // Soft delete the transaction itself

            if ($deleted && $loanApplication) {
                $loanApplication->updateOverallStatusAfterTransaction();
                Log::debug(self::LOG_AREA . "Called updateOverallStatusAfterTransaction for LA ID: {$loanApplication->id} after deleting Tx ID {$txIdLog}");
            }

            DB::commit();
            Log::info(self::LOG_AREA . "Loan transaction ID {$txIdLog} and its items processed for deletion.");
            return (bool) $deleted;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Error deleting loan transaction ID {$txIdLog}: " . $e->getMessage());
            throw new RuntimeException(__('Gagal memadam transaksi pinjaman: ') . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
