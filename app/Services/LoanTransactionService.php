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
use InvalidArgumentException;
use RuntimeException;

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
     * Processes a new equipment issuance transaction.
     * This is the public entry point for creating an issue transaction.
     *
     * @param  LoanApplication  $loanApplication  The parent loan application.
     * @param  array  $itemsPayload  The array of items being issued.
     * @param  User  $issuingOfficer  The officer performing the issuance.
     * @param  array  $transactionDetails  Additional details like transaction date and notes.
     * @return LoanTransaction The created loan transaction.
     *
     * @throws RuntimeException|InvalidArgumentException
     */
    public function processNewIssue(
        LoanApplication $loanApplication,
        array $itemsPayload,
        User $issuingOfficer,
        array $transactionDetails
    ): LoanTransaction {
        Log::info(self::LOG_AREA.sprintf('Processing new issue for LA ID: %d by User ID: %d', $loanApplication->id, $issuingOfficer->id), [
            'item_count' => count($itemsPayload),
            'details_keys' => array_keys($transactionDetails),
        ]);

        $itemDataForTransaction = array_map(function ($requestedItem): array {
            if (empty($requestedItem['equipment_id']) || empty($requestedItem['loan_application_item_id'])) {
                throw new InvalidArgumentException('Butiran item pengeluaran tidak lengkap.');
            }

            return [
                'equipment_id' => (int) $requestedItem['equipment_id'],
                'loan_application_item_id' => (int) $requestedItem['loan_application_item_id'],
                'notes' => $requestedItem['issue_item_notes'] ?? null,
            ];
        }, $itemsPayload);

        return $this->createTransaction(
            $loanApplication,
            LoanTransaction::TYPE_ISSUE,
            $issuingOfficer,
            $itemDataForTransaction,
            $transactionDetails
        );
    }

    /**
     * Processes the return of equipment items against an original issue transaction.
     *
     * @param  LoanTransaction  $issueTransaction  The original transaction where items were issued.
     * @param  array  $itemsPayload  The array of items being returned.
     * @param  User  $returnAcceptingOfficer  The officer processing the return.
     * @param  array  $transactionDetails  Additional details like return date and notes.
     * @return LoanTransaction The created return transaction.
     *
     * @throws RuntimeException|InvalidArgumentException
     */
    public function processExistingReturn(
        LoanTransaction $issueTransaction,
        array $itemsPayload,
        User $returnAcceptingOfficer,
        array $transactionDetails
    ): LoanTransaction {
        $loanApplication = $issueTransaction->loanApplication;
        Log::info(self::LOG_AREA.sprintf('Processing return for LA ID: %d against Issue Tx ID: %d', $loanApplication->id, $issueTransaction->id), [
            'item_count' => count($itemsPayload),
        ]);

        $itemDataForTransaction = array_map(function ($returnedItem) use ($issueTransaction): array {
            $originalIssuedItem = LoanTransactionItem::where('loan_transaction_id', $issueTransaction->id)
                ->find((int) ($returnedItem['loan_transaction_item_id'] ?? 0));

            if (! $originalIssuedItem) {
                throw new InvalidArgumentException(sprintf('Item transaksi asal dengan ID %s tidak sah untuk transaksi ini.', $returnedItem['loan_transaction_item_id']));
            }

            return [
                'equipment_id' => $originalIssuedItem->equipment_id,
                'loan_application_item_id' => $originalIssuedItem->loan_application_item_id,
                'notes' => $returnedItem['return_item_notes'] ?? null,
                'condition_on_return' => $returnedItem['condition_on_return'],
            ];
        }, $itemsPayload);

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
     * Core public method that handles all transaction database operations.
     * *** EDITED: Changed visibility from private to public. ***
     */
    public function createTransaction(
        LoanApplication $loanApplication,
        string $type,
        User $actingOfficer,
        array $itemData,
        array $extraDetails = []
    ): LoanTransaction {
        if ($itemData === []) {
            throw new InvalidArgumentException('Transaction must have at least one item.');
        }

        return DB::transaction(function () use ($loanApplication, $type, $actingOfficer, $itemData, $extraDetails): \App\Models\LoanTransaction {
            $transaction = $this->createTransactionRecord($loanApplication, $type, $actingOfficer, $extraDetails);

            foreach ($itemData as $item) {
                $this->processTransactionItem($transaction, $type, $item, $actingOfficer);
            }

            $transaction->status = ($type === LoanTransaction::TYPE_ISSUE)
                ? LoanTransaction::STATUS_ISSUED
                : $this->determineOverallReturnTransactionStatus($itemData);
            $transaction->save();

            $loanApplication->updateOverallStatusAfterTransaction();

            $this->dispatchNotifications($transaction, $type, $actingOfficer);

            return $transaction;
        });
    }

    /**
     * Helper to create the initial LoanTransaction model instance.
     */
    private function createTransactionRecord(LoanApplication $loanApplication, string $type, User $actingOfficer, array $extraDetails): LoanTransaction
    {
        // Start with details common to ALL transactions
        $baseDetails = [
            'loan_application_id' => $loanApplication->id,
            'type' => $type,
            'transaction_date' => $extraDetails['transaction_date'] ?? Carbon::now(),
            'status' => LoanTransaction::STATUS_PENDING, // Always start as 'pending'
        ];

        $specificDetails = [];
        if ($type === LoanTransaction::TYPE_ISSUE) {
            // Add details specific to an ISSUE transaction
            $specificDetails = [
                'issuing_officer_id' => $actingOfficer->id,
                'receiving_officer_id' => $extraDetails['receiving_officer_id'] ?? null,
                'issue_notes' => $extraDetails['issue_notes'] ?? null,
                'issue_timestamp' => $baseDetails['transaction_date'],
            ];
        } elseif ($type === LoanTransaction::TYPE_RETURN) {
            // Add details specific to a RETURN transaction
            $specificDetails = [
                'return_accepting_officer_id' => $actingOfficer->id,
                'returning_officer_id' => $extraDetails['returning_officer_id'] ?? null,
                'return_notes' => $extraDetails['return_notes'] ?? null,
                'return_timestamp' => $baseDetails['transaction_date'],
                'related_transaction_id' => $extraDetails['related_transaction_id'] ?? null,
            ];
        } else {
            // This is a safeguard against unexpected transaction types.
            throw new InvalidArgumentException('Invalid transaction type specified: '.$type);
        }

        // Merge the base and specific details to create the final data array
        $transactionData = array_merge($baseDetails, $specificDetails);

        return LoanTransaction::create($transactionData);
    }

    /**
     * Helper to process each item within a transaction.
     */
    private function processTransactionItem(LoanTransaction $transaction, string $type, array $item, User $actingOfficer): void
    {
        /** @var \App\Models\Equipment $equipment */ // *** EDITED: Added PHPDoc block to fix static analysis warning. ***
        $equipment = Equipment::findOrFail($item['equipment_id']);
        $txItemData = [
            'equipment_id' => $equipment->id,
            'loan_application_item_id' => $item['loan_application_item_id'] ?? null,
            'quantity_transacted' => 1,
            'item_notes' => $item['notes'] ?? null,
        ];

        if ($type === LoanTransaction::TYPE_ISSUE) {
            $txItemData['status'] = LoanTransactionItem::STATUS_ITEM_ISSUED;
            $this->equipmentService->changeOperationalStatus($equipment, Equipment::STATUS_ON_LOAN, $actingOfficer, 'Issued via Tx#'.$transaction->id);
        } else { // TYPE_RETURN
            $conditionOnReturn = $item['condition_on_return'];
            $txItemData['status'] = $this->determineItemStatusOnReturn($conditionOnReturn);
            $txItemData['condition_on_return'] = $conditionOnReturn;

            $newEquipmentOpStatus = $this->determineEquipmentStatusOnReturn($conditionOnReturn);
            $this->equipmentService->changeOperationalStatus($equipment, $newEquipmentOpStatus, $actingOfficer, 'Returned via Tx#'.$transaction->id);
        }

        $transactionItem = $transaction->loanTransactionItems()->create($txItemData);

        if ($transactionItem->loanApplicationItem) {
            $transactionItem->loanApplicationItem->recalculateQuantities();
        }
    }

    /**
     * Helper to dispatch notifications.
     */
    private function dispatchNotifications(LoanTransaction $transaction, string $type, User $actingOfficer): void
    {
        if ($transaction->loanApplication->user) {
            if ($type === LoanTransaction::TYPE_ISSUE) {
                $this->notificationService->notifyApplicantEquipmentIssued($transaction->loanApplication, $transaction, $actingOfficer);
            } elseif ($type === LoanTransaction::TYPE_RETURN) {
                $this->notificationService->notifyApplicantEquipmentReturned($transaction->loanApplication, $transaction, $actingOfficer);
            }
        }
    }

    /**
     * Determines the final operational status for a piece of equipment upon return.
     */
    private function determineEquipmentStatusOnReturn(string $conditionOnReturn): string
    {
        return match ($conditionOnReturn) {
            Equipment::CONDITION_LOST => Equipment::STATUS_LOST,
            Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE => Equipment::STATUS_UNDER_MAINTENANCE,
            Equipment::CONDITION_UNSERVICEABLE => Equipment::STATUS_DISPOSED,
            default => Equipment::STATUS_AVAILABLE,
        };
    }

    /**
     * Determines the status for a LoanTransactionItem based on the equipment's condition.
     */
    private function determineItemStatusOnReturn(string $conditionOnReturn): string
    {
        return match ($conditionOnReturn) {
            Equipment::CONDITION_MINOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            Equipment::CONDITION_MAJOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            Equipment::CONDITION_UNSERVICEABLE => LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
            Equipment::CONDITION_LOST => LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
            default => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
        };
    }

    /**
     * Determines the overall status for a RETURN transaction based on its items.
     */
    private function determineOverallReturnTransactionStatus(array $itemData): string
    {
        $hasDamage = false;
        $hasLost = false;

        foreach ($itemData as $item) {
            $condition = $item['condition_on_return'] ?? Equipment::CONDITION_GOOD;
            if ($condition === Equipment::CONDITION_LOST) {
                $hasLost = true;
            }

            if (in_array($condition, [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE])) {
                $hasDamage = true;
            }
        }

        if ($hasLost && $hasDamage) {
            return LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS;
        }

        if ($hasLost) {
            return LoanTransaction::STATUS_RETURNED_WITH_LOSS;
        }

        if ($hasDamage) {
            return LoanTransaction::STATUS_RETURNED_DAMAGED;
        }

        return LoanTransaction::STATUS_RETURNED_GOOD;
    }
}
