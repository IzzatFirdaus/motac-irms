<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use App\Notifications\EquipmentIssuedNotification;
use App\Notifications\EquipmentReturnedNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
// Assuming these specific notifications will be created/are available
use RuntimeException;
use Throwable;

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
     * Updates equipment statuses and loan application status accordingly.
     *
     * @param LoanApplication $loanApplication The parent loan application.
     * @param string $type Transaction type (LoanTransaction::TYPE_ISSUE or LoanTransaction::TYPE_RETURN).
     * @param User $actingOfficer The officer performing the transaction.
     * @param array<int, array{equipment_id: int, quantity: int, loan_application_item_id?: int|null, original_loan_transaction_item_id?: int|null, notes?: string|null, accessories_data?: array|string|null, condition_on_return?: string|null, item_status_on_return?: string|null}> $itemData Array of items involved in the transaction.
     * @param array<string, mixed> $extraDetails Additional details for LoanTransaction (e.g., transaction_date, officer IDs, notes).
     * @return LoanTransaction The created loan transaction.
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
                'status' => $extraDetails['status'] ?? LoanTransaction::STATUS_PENDING,
            ];

            if ($type === LoanTransaction::TYPE_ISSUE) {
                $transactionDetails['issuing_officer_id'] = $actingOfficer->id;
                $transactionDetails['receiving_officer_id'] = $extraDetails['receiving_officer_id'] ?? $loanApplication->user_id;
                $transactionDetails['issue_notes'] = $extraDetails['issue_notes'] ?? null;
                $transactionDetails['issue_timestamp'] = $transactionDetails['transaction_date'];
            } elseif ($type === LoanTransaction::TYPE_RETURN) {
                $transactionDetails['return_accepting_officer_id'] = $actingOfficer->id;
                $transactionDetails['returning_officer_id'] = $extraDetails['returning_officer_id'] ?? $loanApplication->user_id;
                $transactionDetails['return_notes'] = $extraDetails['return_notes'] ?? null;
                $transactionDetails['return_timestamp'] = $transactionDetails['transaction_date'];
                $transactionDetails['related_transaction_id'] = $extraDetails['related_transaction_id'] ?? null;
            }

            /** @var LoanTransaction $transaction */
            $transaction = LoanTransaction::create($transactionDetails);
            $txIdLog = $transaction->id;

            foreach ($itemData as $item) {
                $equipment = Equipment::findOrFail($item['equipment_id']);
                $quantityTransacted = (int)($item['quantity'] ?? 1);

                $txItemData = [
                    'equipment_id' => $equipment->id,
                    'loan_application_item_id' => $item['loan_application_item_id'] ?? null,
                    'quantity_transacted' => $quantityTransacted,
                    'item_notes' => $item['notes'] ?? null,
                ];

                if ($type === LoanTransaction::TYPE_ISSUE) {
                    $txItemData['status'] = LoanTransactionItem::STATUS_ITEM_ISSUED;
                    $txItemData['accessories_checklist_issue'] = is_array($item['accessories_data'] ?? null)
                        ? json_encode($item['accessories_data'])
                        : ($item['accessories_data'] ?? null);

                    $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $actingOfficer, __("Dikeluarkan melalui Transaksi #:txId", ['txId' => $txIdLog]));
                    if ($equipment->condition_status === Equipment::CONDITION_NEW) {
                        $this->equipmentService->changeConditionStatus($equipment, Equipment::CONDITION_GOOD, $actingOfficer, __("Status keadaan ditukar kepada 'Baik' semasa pengeluaran pertama."));
                    }

                } elseif ($type === LoanTransaction::TYPE_RETURN) {
                    $conditionOnReturn = $item['condition_on_return'] ?? $equipment->condition_status;
                    $itemStatusOnReturn = $item['item_status_on_return'] ?? LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD;

                    if (!in_array($conditionOnReturn, Equipment::getConditionStatusesList(), true)) {
                        throw new InvalidArgumentException("Status keadaan tidak sah '{$conditionOnReturn}' untuk equipment_id: {$equipment->id}.");
                    }
                    if (!in_array($itemStatusOnReturn, LoanTransactionItem::getStatusOptionsList(), true)) {
                        throw new InvalidArgumentException("Status item transaksi tidak sah '{$itemStatusOnReturn}' untuk equipment_id: {$equipment->id}.");
                    }

                    $txItemData['status'] = $itemStatusOnReturn;
                    $txItemData['condition_on_return'] = $conditionOnReturn;
                    $txItemData['accessories_checklist_return'] = is_array($item['accessories_data'] ?? null)
                        ? json_encode($item['accessories_data'])
                        : ($item['accessories_data'] ?? null);

                    $newEquipmentOpStatus = Equipment::STATUS_AVAILABLE;
                    if ($itemStatusOnReturn === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST || $conditionOnReturn === Equipment::CONDITION_LOST) {
                        $newEquipmentOpStatus = Equipment::STATUS_LOST;
                    } elseif (in_array($itemStatusOnReturn, [LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE, LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE, LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN])) {
                        $newEquipmentOpStatus = Equipment::STATUS_UNDER_MAINTENANCE;
                        if ($itemStatusOnReturn === LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN || $conditionOnReturn === Equipment::CONDITION_UNSERVICEABLE) {
                            $newEquipmentOpStatus = Equipment::STATUS_DISPOSED;
                        }
                    }
                    $this->equipmentService->changeOperationalStatus($equipment, $newEquipmentOpStatus, $actingOfficer, __("Dikembalikan melalui Transaksi #:txId. Status Item: :itemStatus", ['txId' => $txIdLog, 'itemStatus' => LoanTransactionItem::getStatusOptions()[$itemStatusOnReturn] ?? $itemStatusOnReturn]));
                    if ($conditionOnReturn !== $equipment->condition_status) {
                        $this->equipmentService->changeConditionStatus($equipment, $conditionOnReturn, $actingOfficer, __("Keadaan semasa pemulangan Transaksi #:txId.", ['txId' => $txIdLog]));
                    }
                }
                /** @var LoanTransactionItem $transactionItem */
                $transactionItem = $transaction->loanTransactionItems()->create($txItemData);

                if ($transactionItem->loanApplicationItem) {
                    $transactionItem->loanApplicationItem->recalculateQuantities();
                }
                Log::debug(self::LOG_AREA . "Created LoanTransactionItem ID: {$transactionItem->id} for Tx ID: {$txIdLog}");
            }

            if ($transaction->status === LoanTransaction::STATUS_PENDING) {
                if ($type === LoanTransaction::TYPE_ISSUE) {
                    $transaction->status = LoanTransaction::STATUS_ISSUED;
                } elseif ($type === LoanTransaction::TYPE_RETURN) {
                    $transaction->status = $extraDetails['status'] ?? $this->determineOverallReturnTransactionStatus($itemData); // Updated to use helper
                }
                $transaction->save();
            }

            $loanApplication->updateOverallStatusAfterTransaction();
            Log::debug(self::LOG_AREA . "Called updateOverallStatusAfterTransaction for LoanApplication ID: {$appIdLog}");

            DB::commit();
            Log::info(self::LOG_AREA . "Loan transaction ID {$txIdLog} of type '{$type}' created successfully.");

            // Send notifications
            if ($type === LoanTransaction::TYPE_ISSUE && class_exists(EquipmentIssuedNotification::class) && $loanApplication->user) {
                $this->notificationService->notifyApplicantStatusUpdate($loanApplication, 'N/A', $loanApplication->status); // Example generic status update, or specific
                // Or a more specific notification:
                // $this->notificationService->notifyGeneric($loanApplication->user, new EquipmentIssuedNotification($transaction));
            } elseif ($type === LoanTransaction::TYPE_RETURN && class_exists(EquipmentReturnedNotification::class) && $loanApplication->user) {
                $this->notificationService->notifyApplicantStatusUpdate($loanApplication, 'N/A', $loanApplication->status);
                // Or a more specific notification:
                // $this->notificationService->notifyGeneric($loanApplication->user, new EquipmentReturnedNotification($transaction));
            }


            return $transaction->fresh([
                'loanTransactionItems.equipment', 'loanApplication', 'issuingOfficer', 'returnAcceptingOfficer', 'receivingOfficer', 'returningOfficer',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA . "Model tidak ditemui semasa penciptaan transaksi untuk App ID {$appIdLog}.", ['exception_message' => $e->getMessage()]);
            throw new ModelNotFoundException(__('Satu atau lebih rekod berkaitan tidak ditemui: ') . $e->getMessage(), $e->getCode(), $e);
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

    /**
     * Processes a new equipment issuance transaction.
     *
     * @param LoanApplication $loanApplication The loan application.
     * @param array<int, int> $selectedEquipmentIds Array of specific equipment IDs to be issued.
     * @param array<string, mixed> $accessories Accessories checklist data.
     * @param string $issue_notes Notes for the issuance.
     * @param User $issuingOfficer The officer performing the issuance. (Note: Calling code must pass a User object)
     * @return LoanTransaction The created issue transaction.
     * @throws ModelNotFoundException If equipment is not found.
     * @throws InvalidArgumentException If equipment is not available or no valid items selected.
     */
    public function processNewIssue(
        LoanApplication $loanApplication,
        array $selectedEquipmentIds, // This was type hinted as array in your snippet, assuming array of int IDs
        array $accessories,
        string $issue_notes,
        User $issuingOfficer // Correctly type-hinted
    ): LoanTransaction {
        Log::info(self::LOG_AREA . "Processing new issue for LA ID: {$loanApplication->id}", compact('selectedEquipmentIds', 'accessories', 'issue_notes', 'issuingOfficer'));

        $itemData = [];
        foreach ($selectedEquipmentIds as $equipmentId) {
            $equipment = Equipment::find($equipmentId);
            if (!$equipment) {
                throw new ModelNotFoundException("Peralatan dengan ID {$equipmentId} tidak ditemui.");
            }
            // Ensure you are using the correct status constant from Equipment model
            if ($equipment->status !== Equipment::STATUS_AVAILABLE) {
                throw new InvalidArgumentException("Peralatan {$equipment->tag_id} tidak tersedia untuk dikeluarkan. Status semasa: {$equipment->statusLabel}"); // statusLabel is an accessor
            }

            $relatedLoanApplicationItem = $loanApplication->applicationItems()
                ->where('equipment_type', $equipment->asset_type)
                // Ensure 'quantity_requested' and 'quantity_issued' are correct column names
                ->whereRaw('quantity_requested > quantity_issued') // This logic depends on LoanApplicationItem being up-to-date
                ->first();

            $itemData[] = [
                'equipment_id' => (int)$equipmentId,
                'quantity' => 1, // Assuming one specific serialized item
                'loan_application_item_id' => $relatedLoanApplicationItem?->id,
                'notes' => null, // Specific notes for this item in this transaction can be added if available
                'accessories_data' => $accessories, // This seems to be global accessories for the transaction
            ];
        }

        if (empty($itemData)) {
            throw new InvalidArgumentException('Tiada item sah dipilih untuk pengeluaran.');
        }

        $transactionDetails = [
            'receiving_officer_id' => $loanApplication->user_id, // Assuming applicant receives
            'transaction_date' => now(),
            'issue_notes' => $issue_notes,
            'status' => LoanTransaction::STATUS_ISSUED, // Default status for a new, completed issue
        ];

        return $this->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_ISSUE,
            $issuingOfficer,
            $itemData,
            $transactionDetails
        );
    }

    /**
     * Processes the return of existing loan transaction items.
     *
     * @param LoanApplication $loanApplication The parent loan application.
     * @param array<int, int> $selectedTransactionItemIdsToReturn Array of LoanTransactionItem IDs that were issued and are now being returned.
     * @param array<int, array<string, mixed>> $itemSpecificDetails Details for each item being returned (keyed by LoanTransactionItem ID).
     * Example: [ loan_tx_item_id => ['condition_on_return' => 'good', 'notes' => '...', 'accessories_data' => ... , 'item_status_on_return' => ...]]
     * @param string $return_notes General notes for the return transaction.
     * @param User $returnAcceptingOfficer The officer accepting the return. (Note: Calling code must pass a User object)
     * @return LoanTransaction The created return transaction.
     * @throws ModelNotFoundException If an item is not found.
     * @throws InvalidArgumentException If item status is invalid or no valid items are selected.
     */
    public function processExistingReturn(
        LoanApplication $loanApplication,
        array $selectedTransactionItemIdsToReturn, // Assuming array of issued LoanTransactionItem IDs
        array $itemSpecificDetails,
        string $return_notes,
        User $returnAcceptingOfficer // Correctly type-hinted
    ): LoanTransaction {
        Log::info(self::LOG_AREA . "Processing return for LA ID: {$loanApplication->id}", compact('selectedTransactionItemIdsToReturn', 'itemSpecificDetails', 'return_notes', 'returnAcceptingOfficer'));

        $itemData = [];
        $firstRelatedIssueTransactionId = null;

        foreach ($selectedTransactionItemIdsToReturn as $issuedTxItemId) {
            $issuedItem = LoanTransactionItem::with('equipment', 'loanTransaction')->findOrFail($issuedTxItemId);

            if ($issuedItem->loanTransaction->loan_application_id !== $loanApplication->id) {
                throw new InvalidArgumentException("Item transaksi ID {$issuedTxItemId} tidak sepadan dengan permohonan pinjaman semasa.");
            }

            // Corrected Check: An item being returned should be in an 'issued' state.
            // 'Overdue' is a loan application status, not an item transaction status.
            if ($issuedItem->status !== LoanTransactionItem::STATUS_ITEM_ISSUED) { // Corrected line
                throw new InvalidArgumentException("Item ID {$issuedTxItemId} ({$issuedItem->equipment->tag_id}) tidak berstatus '".LoanTransactionItem::$STATUSES_LABELS[LoanTransactionItem::STATUS_ITEM_ISSUED]."'. Status semasa: {$issuedItem->statusTranslated}"); // statusTranslated is an accessor
            }

            if (!$firstRelatedIssueTransactionId && $issuedItem->loanTransaction->type === LoanTransaction::TYPE_ISSUE) {
                $firstRelatedIssueTransactionId = $issuedItem->loan_transaction_id;
            }

            $detailsForItem = $itemSpecificDetails[$issuedTxItemId] ?? [];
            $conditionFromForm = $detailsForItem['condition_on_return'] ?? $issuedItem->equipment->condition_status; // Default to current equipment condition if not specified
            // Determine item status on return based on condition, unless explicitly provided
            $itemStatusOnReturn = $detailsForItem['item_status_on_return'] ?? $this->determineItemStatusOnReturn($conditionFromForm);
            $itemNotes = $detailsForItem['notes'] ?? null;
            $accessoriesData = $detailsForItem['accessories_data'] ?? null;

            $itemData[] = [
                'equipment_id' => $issuedItem->equipment_id,
                'quantity' => $issuedItem->quantity_transacted, // Usually 1 for serialized items
                'loan_application_item_id' => $issuedItem->loan_application_item_id,
                'original_loan_transaction_item_id' => $issuedItem->id, // Link back to the specific item that was issued
                'notes' => $itemNotes,
                'accessories_data' => $accessoriesData,
                'condition_on_return' => $conditionFromForm,
                'item_status_on_return' => $itemStatusOnReturn,
            ];
        }

        if (empty($itemData)) {
            throw new InvalidArgumentException('Tiada item sah dipilih untuk pemulangan.');
        }

        $transactionDetails = [
            'returning_officer_id' => $loanApplication->responsible_officer_id ?? $loanApplication->user_id, // Person returning
            'transaction_date' => now(),
            'return_notes' => $return_notes,
            'related_transaction_id' => $firstRelatedIssueTransactionId, // Link to the issue transaction
             // The overall status of the return transaction
            'status' => $this->determineOverallReturnTransactionStatus($itemData),
        ];

        return $this->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_RETURN,
            $returnAcceptingOfficer,
            $itemData,
            $transactionDetails
        );
    }

    public function findTransaction(int $id, array $with = []): ?LoanTransaction
    {
        Log::debug(self::LOG_AREA . "Finding loan transaction ID {$id}.", ['with' => $with]);
        try {
            $query = LoanTransaction::query();
            // Assuming LoanTransaction model has a static method to get default relations array
            $defaultWith = LoanTransaction::getDefinedDefaultRelationsStatic();
            $query->with(array_unique(array_merge($defaultWith, $with)));

            /** @var LoanTransaction|null $transaction */
            $transaction = $query->find($id);
            if (!$transaction) {
                Log::notice(self::LOG_AREA . "Loan transaction ID {$id} not found.");
            }
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
        $defaultWith = LoanTransaction::getDefinedDefaultRelationsStatic();
        $query->with(array_unique(array_merge($defaultWith, $with)));

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['loan_application_id'])) {
            $query->where('loan_application_id', (int) $filters['loan_application_id']);
        }
        if (!empty($filters['officer_id'])) {
            $officerId = (int) $filters['officer_id'];
            $query->where(fn ($q) => $q->where('issuing_officer_id', $officerId)->orWhere('return_accepting_officer_id', $officerId)->orWhere('receiving_officer_id', $officerId)->orWhere('returning_officer_id', $officerId));
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $sortDirectionSafe = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['transaction_date', 'created_at', 'type', 'status', 'updated_at'];
        $safeSortBy = in_array($sortBy, $allowedSorts) && Schema::hasColumn((new LoanTransaction())->getTable(), $sortBy) ? $sortBy : 'transaction_date';

        $query->orderBy($safeSortBy, $sortDirectionSafe);

        return ($perPage !== null && $perPage > 0) ? $query->paginate($perPage) : $query->get();
    }

    public function updateTransaction(LoanTransaction $transaction, array $validatedData, User $actingOfficer): LoanTransaction
    {
        $txIdLog = $transaction->id;
        Log::info(self::LOG_AREA . "Attempting to update loan transaction ID {$txIdLog}.", ['data_keys' => array_keys($validatedData)]);
        DB::beginTransaction();
        try {
            if (isset($validatedData['return_notes'])) {
                $transaction->return_notes = $validatedData['return_notes'];
            }
            if (isset($validatedData['issue_notes'])) {
                $transaction->issue_notes = $validatedData['issue_notes'];
            }
            if (isset($validatedData['status'])) {
                if (!in_array($validatedData['status'], LoanTransaction::getStatusesList(), true)) {
                    throw new InvalidArgumentException("Status transaksi tidak sah: {$validatedData['status']}");
                }
                $transaction->status = $validatedData['status'];
            }

            $transaction->save();

            if ($transaction->loanApplication) {
                $transaction->loanApplication->updateOverallStatusAfterTransaction();
            }
            DB::commit();
            Log::info(self::LOG_AREA . "Loan transaction ID {$txIdLog} updated successfully by User ID {$actingOfficer->id}.");
            return $transaction->fresh($transaction->getRelations());
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

        DB::beginTransaction();
        try {
            $transactionType = $transaction->type;
            $loanApplication = $transaction->loanApplication()->first();

            /** @var SupportCollection<int, LoanTransactionItem> $itemsToRevert */
            $itemsToRevert = $transaction->loanTransactionItems()->with('equipment')->get();

            foreach ($itemsToRevert as $txItem) {
                if (!$txItem->equipment) {
                    continue;
                }
                /** @var Equipment $equipment */
                $equipment = $txItem->equipment;
                $revertNote = __("Transaksi ID :txId (:type) dibatalkan oleh :officer.", ['txId' => $txIdLog, 'type' => $transactionType, 'officer' => $actingOfficer->name]);

                if ($transactionType === LoanTransaction::TYPE_ISSUE) {
                    $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_AVAILABLE, $actingOfficer, $revertNote);
                } elseif ($transactionType === LoanTransaction::TYPE_RETURN) {
                    $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $actingOfficer, $revertNote);
                }
                $txItem->delete();
            }

            $deleted = $transaction->delete();

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

    private function determineItemStatusOnReturn(string $conditionOnReturn): string
    {
        // Ensure Equipment model constants are accessible, e.g., use App\Models\Equipment::CONSTANT_NAME
        switch ($conditionOnReturn) {
            case Equipment::CONDITION_MINOR_DAMAGE: // Assuming Equipment::CONDITION_MINOR_DAMAGE exists
                return LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE;
            case Equipment::CONDITION_MAJOR_DAMAGE: // Assuming Equipment::CONDITION_MAJOR_DAMAGE exists
                return LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE;
            case Equipment::CONDITION_UNSERVICEABLE: // Assuming Equipment::CONDITION_UNSERVICEABLE exists
                // In your LoanTransactionItem, this was STATUS_ITEM_UNSERVICEABLE_ON_RETURN
                return LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN;
            case Equipment::CONDITION_LOST: // This constant was in your Equipment model
                return LoanTransactionItem::STATUS_ITEM_REPORTED_LOST;
            case Equipment::CONDITION_GOOD:
            case Equipment::CONDITION_FAIR:
            case Equipment::CONDITION_NEW: // Assuming these exist in Equipment model
            default:
                return LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD;
        }
    }

    private function determineOverallReturnTransactionStatus(array $itemData): string
    {
        $hasDamaged = false;
        $hasLost = false;
        $allGood = true;

        foreach ($itemData as $item) {
            $status = $item['item_status_on_return'];
            if (in_array($status, [
                LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
                LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
                LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN, // Using the constant from LoanTransactionItem
            ])) {
                $hasDamaged = true;
                $allGood = false;
            }
            if ($status === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) {
                $hasLost = true;
                $allGood = false;
            }
            if ($status === LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION) {
                return LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION; // If any item is pending inspection, whole transaction is
            }
            if ($status !== LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD && $status !== LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) { // Consider only good or lost for allGood logic
                // If not good and not lost, means it's damaged or unserviceable
                if ($status !== LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE && $status !== LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE && $status !== LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN) {
                    $allGood = false; // Any other status apart from good or lost also makes it not "all good"
                }
            }
        }

        // Ensure LoanTransaction constants like STATUS_RETURNED_WITH_DAMAGE_AND_LOSS are defined in LoanTransaction.php
        // Based on the provided LoanTransaction.php, these more specific statuses are not there.
        // Let's use the existing ones: STATUS_RETURNED_GOOD, STATUS_RETURNED_DAMAGED, STATUS_ITEMS_REPORTED_LOST

        if ($hasLost && $hasDamaged) {
            // No specific constant for this combined state in provided LoanTransaction model.
            // Defaulting to a general "damaged" if also lost items are present.
            // Or perhaps STATUS_ITEMS_REPORTED_LOST takes precedence if any item is lost.
            // For now, if any lost, mark as such.
            return LoanTransaction::STATUS_ITEMS_REPORTED_LOST; // Or a new combined status if you add it.
        }
        if ($hasLost) {
            return LoanTransaction::STATUS_ITEMS_REPORTED_LOST; // From LoanTransaction.php constants
        }
        if ($hasDamaged) {
            return LoanTransaction::STATUS_RETURNED_DAMAGED; // From LoanTransaction.php constants
        }
        if ($allGood) {
            return LoanTransaction::STATUS_RETURNED_GOOD; // From LoanTransaction.php constants
        }
        // Fallback or if items are mixed in a way not covered above, might need more logic or a general complete.
        // For now, if any item is pending inspection, that becomes the transaction status.
        // If no items pending, and some not good, it would have hit damaged/lost.
        // If all items processed and none are good, it would be damaged or lost.
        return LoanTransaction::STATUS_COMPLETED; // Or another appropriate default.
    }
}
