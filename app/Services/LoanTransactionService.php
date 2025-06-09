<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Service class for managing Loan Transactions (issuance and returns of equipment).
 * System Design Reference: Sections 3.1 (Services), 5.2, 9.3
 */
final class LoanTransactionService
{
    private const LOG_AREA = 'LoanTransactionService: ';

    private EquipmentService $equipmentService;

    private NotificationService $notificationService;

    public function __construct(EquipmentService $equipmentService, NotificationService $notificationService)
    {
        $this->equipmentService = $equipmentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Creates a loan transaction (issue or return) and its associated items.
     * This is the core private method that handles database operations.
     */
    public function createTransaction(
        LoanApplication $loanApplication,
        string $type,
        User $actingOfficer,
        array $itemData,
        array $extraDetails = []
    ): LoanTransaction {
        $appIdLog = $loanApplication->id;
        $officerIdLog = $actingOfficer->id;

        Log::info(self::LOG_AREA."Attempting to create loan transaction of type '{$type}' for LA ID {$appIdLog} by User ID {$officerIdLog}.", [
            'item_count' => count($itemData),
            'details_keys' => array_keys($extraDetails),
        ]);

        if (! in_array($type, [LoanTransaction::TYPE_ISSUE, LoanTransaction::TYPE_RETURN], true)) {
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
                'status' => $extraDetails['status_override'] ?? LoanTransaction::STATUS_PENDING,
            ];

            if ($type === LoanTransaction::TYPE_ISSUE) {
                $receivingOfficerUser = User::find($extraDetails['receiving_officer_id'] ?? null);
                if (! $receivingOfficerUser) {
                    throw new InvalidArgumentException('Pegawai Penerima (receiving_officer_id) tidak sah.');
                }

                $transactionDetails['issuing_officer_id'] = $actingOfficer->id;
                $transactionDetails['receiving_officer_id'] = $receivingOfficerUser->id;
                $transactionDetails['issue_notes'] = $extraDetails['issue_notes'] ?? null;
                $transactionDetails['issue_timestamp'] = $transactionDetails['transaction_date'];
            } elseif ($type === LoanTransaction::TYPE_RETURN) {
                $returningOfficerUser = User::find($extraDetails['returning_officer_id'] ?? null);
                if (! $returningOfficerUser) {
                    throw new InvalidArgumentException('Pegawai Yang Memulangkan (returning_officer_id) tidak sah.');
                }

                $transactionDetails['return_accepting_officer_id'] = $actingOfficer->id;
                $transactionDetails['returning_officer_id'] = $returningOfficerUser->id;
                $transactionDetails['return_notes'] = $extraDetails['return_notes'] ?? null;
                $transactionDetails['return_timestamp'] = $transactionDetails['transaction_date'];
                $transactionDetails['related_transaction_id'] = $extraDetails['related_transaction_id'] ?? null;
            }

            $transaction = LoanTransaction::create($transactionDetails);
            $txIdLog = $transaction->id;

            foreach ($itemData as $item) {
                $equipment = Equipment::findOrFail($item['equipment_id']);
                $quantityTransacted = (int) ($item['quantity'] ?? 1);

                $txItemData = [
                    'equipment_id' => $equipment->id,
                    'loan_application_item_id' => $item['loan_application_item_id'] ?? null,
                    'quantity_transacted' => $quantityTransacted,
                    'item_notes' => $item['notes'] ?? null,
                ];

                if ($type === LoanTransaction::TYPE_ISSUE) {
                    $txItemData['status'] = LoanTransactionItem::STATUS_ITEM_ISSUED;
                    $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $actingOfficer, __('Dikeluarkan melalui Transaksi Pinjaman #:txId', ['txId' => $txIdLog]));
                } elseif ($type === LoanTransaction::TYPE_RETURN) {
                    $conditionOnReturn = $item['condition_on_return'] ?? Equipment::CONDITION_GOOD;
                    $itemStatusOnReturn = $item['item_status_on_return'] ?? $this->determineItemStatusOnReturn($conditionOnReturn);

                    $txItemData['status'] = $itemStatusOnReturn;
                    $txItemData['condition_on_return'] = $conditionOnReturn;

                    $newEquipmentOpStatus = Equipment::STATUS_AVAILABLE;
                    if ($conditionOnReturn === Equipment::CONDITION_LOST) {
                        $newEquipmentOpStatus = Equipment::STATUS_LOST;
                    } elseif (in_array($conditionOnReturn, [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE])) {
                        $newEquipmentOpStatus = Equipment::STATUS_UNDER_MAINTENANCE;
                    } elseif ($conditionOnReturn === Equipment::CONDITION_UNSERVICEABLE) {
                        $newEquipmentOpStatus = Equipment::STATUS_DISPOSED;
                    }

                    $this->equipmentService->changeOperationalStatus($equipment, $newEquipmentOpStatus, $actingOfficer, __('Dikembalikan melalui Transaksi #:txId', ['txId' => $txIdLog]));
                    if ($conditionOnReturn !== $equipment->condition_status) {
                        $this->equipmentService->changeConditionStatus($equipment, $conditionOnReturn, $actingOfficer, __('Keadaan dikemaskini semasa pemulangan (Transaksi #:txId)', ['txId' => $txIdLog]));
                    }
                }
                $transactionItem = $transaction->loanTransactionItems()->create($txItemData);
                if ($transactionItem->loanApplicationItem) {
                    $transactionItem->loanApplicationItem->recalculateQuantities();
                }
            }

            $transaction->status = ($type === LoanTransaction::TYPE_ISSUE) ? LoanTransaction::STATUS_ISSUED : $this->determineOverallReturnTransactionStatus($itemData);
            $transaction->save();

            $loanApplication->updateOverallStatusAfterTransaction();
            DB::commit();

            Log::info(self::LOG_AREA."Loan transaction ID {$txIdLog} of type '{$type}' created successfully.");

            if ($loanApplication->user) {
                if ($type === LoanTransaction::TYPE_ISSUE) {
                    $this->notificationService->notifyApplicantEquipmentIssued($loanApplication, $transaction, $actingOfficer);
                } elseif ($type === LoanTransaction::TYPE_RETURN) {
                    $this->notificationService->notifyApplicantEquipmentReturned($loanApplication, $transaction, $actingOfficer);
                }
            }

            return $transaction->fresh(['loanTransactionItems.equipment', 'loanApplication', 'issuingOfficer:id,name', 'receivingOfficer:id,name', 'returningOfficer:id,name', 'returnAcceptingOfficer:id,name']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA."Error creating transaction for App ID {$appIdLog}: ".$e->getMessage(), ['exception' => $e]);
            throw new RuntimeException(__('Gagal mencipta transaksi pinjaman: ').$e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Processes a new equipment issuance transaction.
     */
    // --- EDITED CODE: START ---
    // The method signature is updated to be more explicit and decoupled from the form request.
    public function processNewIssue(
        LoanApplication $loanApplication,
        array $itemsPayload,
        User $issuingOfficer,
        array $transactionDetails
    ): LoanTransaction {
        // --- EDITED CODE: END ---
        Log::info(self::LOG_AREA."Processing new issue for LA ID: {$loanApplication->id} by User ID: {$issuingOfficer->id}", [
            'item_count' => count($itemsPayload),
            'details_keys' => array_keys($transactionDetails),
        ]);

        $itemDataForTransaction = [];
        foreach ($itemsPayload as $requestedItem) {
            $itemDataForTransaction[] = [
                'equipment_id' => (int) $requestedItem['equipment_id'],
                'quantity' => 1, // Each row represents one serialized item
                'loan_application_item_id' => (int) $requestedItem['loan_application_item_id'],
                'notes' => $requestedItem['issue_item_notes'] ?? null,
                'accessories_data' => $requestedItem['accessories_checklist_item'] ?? null,
            ];
        }

        // The details are now passed in a dedicated array.
        return $this->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_ISSUE,
            $issuingOfficer,
            $itemDataForTransaction,
            $transactionDetails
        );
    }

    /**
     * Processes the return of equipment items.
     */
    // --- EDITED CODE: START ---
    // The method signature is updated to be more explicit.
    public function processExistingReturn(
        LoanTransaction $issueTransaction,
        array $itemsPayload,
        User $returnAcceptingOfficer,
        array $transactionDetails
    ): LoanTransaction {
        // --- EDITED CODE: END ---
        $loanApplication = $issueTransaction->loanApplication;
        Log::info(self::LOG_AREA."Processing return against Issue Tx ID: {$issueTransaction->id} for LA ID: {$loanApplication->id} by User ID: {$returnAcceptingOfficer->id}", [
            'item_count' => count($itemsPayload),
        ]);

        $itemDataForTransaction = [];
        foreach ($itemsPayload as $returnedItem) {
            $originalIssuedItem = LoanTransactionItem::find((int) $returnedItem['loan_transaction_item_id']);
            if (! $originalIssuedItem || $originalIssuedItem->loan_transaction_id !== $issueTransaction->id) {
                throw new InvalidArgumentException("Item transaksi asal dengan ID {$returnedItem['loan_transaction_item_id']} tidak sah.");
            }
            $itemDataForTransaction[] = [
                'equipment_id' => $originalIssuedItem->equipment_id,
                'quantity' => 1, // Each row represents one serialized item
                'loan_application_item_id' => $originalIssuedItem->loan_application_item_id,
                'original_loan_transaction_item_id' => $originalIssuedItem->id,
                'notes' => $returnedItem['return_item_notes'] ?? null,
                'condition_on_return' => $returnedItem['condition_on_return'],
                'item_status_on_return' => $this->determineItemStatusOnReturn($returnedItem['condition_on_return']),
            ];
        }

        // The details are now passed in a dedicated array.
        $transactionDetails['related_transaction_id'] = $issueTransaction->id;

        return $this->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_RETURN,
            $returnAcceptingOfficer,
            $itemDataForTransaction,
            $transactionDetails
        );
    }

    /**
     * Finds a specific LoanTransaction by ID with eager loaded relationships.
     * The linter error PHP0408 ("Return value ... expected ...LoanTransaction|null, Illuminate\Database\Eloquent\Builder returned")
     * is a false positive. The code correctly returns a Model or null.
     */
    public function findTransaction(int $id, array $with = []): ?LoanTransaction
    {
        Log::debug(self::LOG_AREA."Finding loan transaction ID {$id}.", ['with' => $with]);
        try {
            $query = LoanTransaction::query();
            $defaultWith = LoanTransaction::getDefinedDefaultRelationsStatic(); // Assumes this static method exists on model
            $query->with(array_unique(array_merge($defaultWith, $with)));

            /** @var \App\Models\LoanTransaction|null $transaction */
            $transaction = $query->find($id); // This executes and returns Model|null

            if (! $transaction) {
                Log::notice(self::LOG_AREA."Loan transaction ID {$id} not found.");
            }

            return $transaction; // Correctly returns ?LoanTransaction
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA."Error finding loan transaction ID {$id}: ".$e->getMessage());
            throw new RuntimeException(__('Gagal mendapatkan butiran transaksi pinjaman.'), 0, $e);
        }
    }

    /**
     * Retrieves a paginated or full list of loan transactions based on filters.
     */
    public function getTransactions(
        array $filters = [],
        array $with = [],
        ?int $perPage = 15,
        string $sortBy = 'transaction_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator|SupportCollection {
        Log::debug(self::LOG_AREA.'Getting transactions.', compact('filters', 'with', 'perPage', 'sortBy', 'sortDirection'));
        $query = LoanTransaction::query();
        $defaultWith = LoanTransaction::getDefinedDefaultRelationsStatic();
        $query->with(array_unique(array_merge($defaultWith, $with)));

        // Apply filters
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['loan_application_id'])) {
            $query->where('loan_application_id', (int) $filters['loan_application_id']);
        }
        if (! empty($filters['officer_id'])) { // Search across all relevant officer fields
            $officerId = (int) $filters['officer_id'];
            $query->where(function ($q) use ($officerId) {
                $q->where('issuing_officer_id', $officerId)
                    ->orWhere('receiving_officer_id', $officerId)
                    ->orWhere('returning_officer_id', $officerId)
                    ->orWhere('return_accepting_officer_id', $officerId);
            });
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        // Apply sorting
        $sortDirectionSafe = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['transaction_date', 'created_at', 'type', 'status', 'updated_at', 'loan_application_id']; // Ensure these columns exist
        $safeSortBy = in_array($sortBy, $allowedSorts) && Schema::hasColumn((new LoanTransaction)->getTable(), $sortBy)
          ? $sortBy
          : 'transaction_date';

        $query->orderBy($safeSortBy, $sortDirectionSafe);

        return ($perPage !== null && $perPage > 0) ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Updates specified fields of an existing loan transaction.
     * This is intended for limited updates by authorized personnel (e.g., adding notes, changing status post-inspection).
     */
    public function updateTransaction(LoanTransaction $transaction, array $validatedData, User $actingOfficer): LoanTransaction
    {
        $txIdLog = $transaction->id;
        Log::info(self::LOG_AREA."Attempting to update loan transaction ID {$txIdLog} by User ID {$actingOfficer->id}.", ['data_keys' => array_keys($validatedData)]);
        DB::beginTransaction();
        try {
            // Only update fields that are explicitly passed in validatedData and are safe to update
            if (array_key_exists('return_notes', $validatedData)) {
                $transaction->return_notes = $validatedData['return_notes'];
            }
            if (array_key_exists('issue_notes', $validatedData)) {
                $transaction->issue_notes = $validatedData['issue_notes'];
            }
            if (array_key_exists('status', $validatedData)) {
                if (! in_array($validatedData['status'], LoanTransaction::getStatusesList(), true)) { // Assumes getStatusesList returns keys
                    throw new InvalidArgumentException("Status transaksi tidak sah: {$validatedData['status']}");
                }
                $transaction->status = $validatedData['status'];
            }
            // Add other updatable fields if necessary, e.g., transaction_date if it can be corrected.

            $transaction->save(); // updated_by will be handled by BlameableObserver or model event

            // If the transaction status change implies an update to the parent loan application status
            if ($transaction->loanApplication && $transaction->wasChanged('status')) {
                $transaction->loanApplication->updateOverallStatusAfterTransaction();
            }
            DB::commit();
            Log::info(self::LOG_AREA."Loan transaction ID {$txIdLog} updated successfully.");

            return $transaction->fresh(LoanTransaction::getDefinedDefaultRelationsStatic()); // Return fresh model with default relations
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA."Error updating loan transaction ID {$txIdLog}: ".$e->getMessage(), ['exception_class' => get_class($e), 'data' => $validatedData]);
            throw new RuntimeException(__('Gagal mengemaskini transaksi pinjaman: ').$e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Deletes a loan transaction (soft delete). This is a sensitive operation.
     * It should revert equipment statuses and update related application item quantities.
     */
    public function deleteTransaction(LoanTransaction $transaction, User $actingOfficer): bool
    {
        $txIdLog = $transaction->id;
        Log::warning(self::LOG_AREA."Attempting to delete loan transaction ID {$txIdLog} by User ID: {$actingOfficer->id}. This is a sensitive operation requiring status reversions.");
        DB::beginTransaction();
        try {
            $transactionType = $transaction->type;
            $loanApplication = $transaction->loanApplication()->firstOrFail(); // Ensure application exists

            /** @var SupportCollection<int, LoanTransactionItem> $itemsToProcess */
            $itemsToProcess = $transaction->loanTransactionItems()->with('equipment')->get();

            foreach ($itemsToProcess as $txItem) {
                if (! $txItem->equipment) {
                    Log::warning(self::LOG_AREA."Skipping item ID {$txItem->id} in Tx#{$txIdLog} as equipment not found (Equipment ID: {$txItem->equipment_id}).");

                    continue;
                }
                /** @var Equipment $equipment */
                $equipment = $txItem->equipment;
                $revertNote = __('Transaksi ID :txId (Jenis: :type) telah dibatalkan/dipadam oleh :officer.', ['txId' => $txIdLog, 'type' => $transaction->type_label, 'officer' => $actingOfficer->name]);

                if ($transactionType === LoanTransaction::TYPE_ISSUE) {
                    // If this was an issue, equipment status goes back to 'available' (or previous state if known)
                    if ($equipment->status === Equipment::STATUS_ON_LOAN) { // Only revert if it was marked on_loan by this tx
                        $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_AVAILABLE, $actingOfficer, $revertNote);
                    }
                } elseif ($transactionType === LoanTransaction::TYPE_RETURN) {
                    // If this was a return, equipment status goes back to 'on_loan'
                    // This assumes the equipment was 'available' or 'under_maintenance' due to this return.
                    // More complex logic might be needed if returns could happen from other statuses.
                    if (in_array($equipment->status, [Equipment::STATUS_AVAILABLE, Equipment::STATUS_UNDER_MAINTENANCE, Equipment::STATUS_DISPOSED, Equipment::STATUS_LOST])) {
                        $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $actingOfficer, $revertNote);
                        // Optionally, revert physical condition if this return transaction set it.
                        // This requires knowing the condition *before* this return transaction.
                    }
                }
                $txItem->delete(); // Soft delete the transaction item
            }

            $deleted = $transaction->delete(); // Soft delete the transaction itself

            // After deleting transaction and its items, recalculate quantities on all application items
            // and update the overall status of the loan application.
            if ($deleted && $loanApplication) {
                // Reload applicationItems to ensure we have the latest state after txItem deletions
                foreach ($loanApplication->loanApplicationItems()->get() as $appItem) {
                    $appItem->recalculateQuantities(); // This should sum up remaining valid tx items
                    $appItem->save();
                }
                $loanApplication->updateOverallStatusAfterTransaction();
                Log::debug(self::LOG_AREA."Called updateOverallStatusAfterTransaction for LA ID: {$loanApplication->id} after deleting Tx ID {$txIdLog}");
            }

            DB::commit();
            Log::info(self::LOG_AREA."Loan transaction ID {$txIdLog} and its items processed for deletion successfully.");

            return (bool) $deleted;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA."Error deleting loan transaction ID {$txIdLog}: ".$e->getMessage(), ['exception_class' => get_class($e), 'trace_snippet' => substr($e->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal memadam transaksi pinjaman: ').$e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Determines the status of a LoanTransactionItem upon return based on its physical condition.
     */
    private function determineItemStatusOnReturn(string $conditionOnReturn): string
    {
        switch ($conditionOnReturn) {
            case Equipment::CONDITION_MINOR_DAMAGE:
                return LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE;
            case Equipment::CONDITION_MAJOR_DAMAGE:
                return LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE;
            case Equipment::CONDITION_UNSERVICEABLE:
                return LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN;
            case Equipment::CONDITION_LOST:
                return LoanTransactionItem::STATUS_ITEM_REPORTED_LOST;
            case Equipment::CONDITION_GOOD:
            case Equipment::CONDITION_FAIR:
            case Equipment::CONDITION_NEW: // Unlikely for a return, but map to good
            default:
                return LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD;
        }
    }

    /**
     * Determines the overall status for a RETURN transaction based on the statuses of its items.
     */
    private function determineOverallReturnTransactionStatus(array $itemData): string
    {
        if (empty($itemData)) {
            return LoanTransaction::STATUS_COMPLETED;
        } // Or an error state if no items

        $hasPendingInspection = false;
        $hasDamaged = false;
        $hasLost = false;
        $allGood = true;

        foreach ($itemData as $item) {
            $status = $item['item_status_on_return'] ?? null;
            if ($status === LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION) {
                $hasPendingInspection = true;
            }

            if (in_array($status, [
                LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
                LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
                LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
            ])) {
                $hasDamaged = true;
            }
            if ($status === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) {
                $hasLost = true;
            }
            // If an item is not explicitly 'good', the overall transaction isn't 'all good'.
            if ($status !== LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD) {
                $allGood = false;
            }
        }

        if ($hasPendingInspection) {
            return LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION;
        }
        if ($hasLost && $hasDamaged) {
            return LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS;
        }
        if ($hasLost) {
            return LoanTransaction::STATUS_RETURNED_WITH_LOSS;
        }
        if ($hasDamaged) {
            return LoanTransaction::STATUS_RETURNED_DAMAGED;
        }
        if ($allGood) {
            return LoanTransaction::STATUS_RETURNED_GOOD;
        } // All items explicitly returned good

        // Fallback for mixed states not explicitly covered above, or if all items processed but with issues.
        // For example, if some items are good, some damaged (but none lost, none pending inspection).
        // This indicates the transaction is processed but not entirely "good".
        return LoanTransaction::STATUS_COMPLETED; // A general "completed" for other mixed scenarios.
        // Or could be LoanTransaction::STATUS_PARTIALLY_RETURNED if that fits.
        // The LoanTransaction model has STATUS_PARTIALLY_RETURNED, which could be used if some items were returned but not all expected for this transaction.
        // However, this helper determines status based on ITEMS IN THIS BATCH.
    }
}
