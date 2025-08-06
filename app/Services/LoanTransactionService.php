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

        $itemDataForTransaction = array_map(function ($item) {
            return [
                'equipment_id' => $item['equipment_id'],
                'quantity_requested' => $item['quantity_requested'],
                'quantity_transacted' => $item['quantity_transacted'],
                'notes' => $item['notes'] ?? null,
                'accessories_checklist_on_issue' => json_encode($item['accessories'] ?? []),
            ];
        }, $itemsPayload);

        return DB::transaction(function () use ($loanApplication, $itemDataForTransaction, $issuingOfficer, $transactionDetails) {
            $transaction = $loanApplication->loanTransactions()->create([
                'type' => LoanTransaction::TYPE_ISSUE,
                'transaction_date' => Carbon::now(),
                'status' => LoanTransaction::STATUS_ISSUED,
                'issuing_officer_id' => $issuingOfficer->id,
                'issue_notes' => $transactionDetails['notes'] ?? null,
                'accessories_checklist_on_issue' => json_encode($transactionDetails['accessories'] ?? []),
            ]);

            foreach ($itemDataForTransaction as $itemData) {
                $transactionItem = $transaction->loanTransactionItems()->create($itemData);

                $equipment = Equipment::find($itemData['equipment_id']);
                if ($equipment) {
                    $equipment->status = Equipment::STATUS_ON_LOAN;
                    $equipment->current_loan_id = $transaction->id;
                    $equipment->save();
                    Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to ON_LOAN for transaction ID %d.', $equipment->id, $transaction->id));
                } else {
                    Log::error(self::LOG_AREA.sprintf('Equipment ID %d not found for transaction item creation.', $itemData['equipment_id']));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
            }

            $loanApplication->status = LoanApplication::STATUS_ISSUED;
            $loanApplication->issued_at = Carbon::now();
            $loanApplication->save();

            // Fix: Call the correct method on NotificationService
            $this->notificationService->notifyLoanIssued(
                $loanApplication->user,
                $loanApplication,
                $transaction
            );

            Log::info(self::LOG_AREA.sprintf('Loan Application ID %d successfully issued via transaction ID %d.', $loanApplication->id, $transaction->id));

            return $transaction;
        });
    }

    public function issueLoanTransaction(
        LoanTransaction $transaction,
        array $loanApplicationItems,
        array $accessories,
        ?string $notes
    ): void {
        DB::transaction(function () use ($transaction, $loanApplicationItems, $accessories, $notes) {
            $transaction->issue_notes = $notes;
            $transaction->accessories_checklist_on_issue = json_encode($accessories);
            $transaction->status = LoanTransaction::STATUS_ISSUED;
            $transaction->transaction_date = Carbon::now();
            $transaction->save();
            Log::info(self::LOG_AREA.sprintf('Loan Transaction ID %d updated and marked as ISSUED.', $transaction->id));

            foreach ($loanApplicationItems as $itemData) {
                $transactionItem = LoanTransactionItem::find($itemData['id']);

                if (!$transactionItem) {
                    Log::error(self::LOG_AREA.sprintf('Loan Transaction Item ID %d not found during issueLoanTransaction.', $itemData['id']));
                    throw new RuntimeException('Loan Transaction Item not found.');
                }

                $transactionItem->quantity_transacted = $itemData['quantity_transacted'];
                $transactionItem->notes = $itemData['notes'] ?? null;
                $transactionItem->save();

                $equipment = $transactionItem->equipment;
                if ($equipment) {
                    $equipment->status = Equipment::STATUS_ON_LOAN;
                    $equipment->current_loan_id = $transaction->id;
                    $equipment->save();
                    Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to ON_LOAN during issue transaction.', $equipment->id));
                } else {
                    Log::error(self::LOG_AREA.sprintf('Equipment not found for Loan Transaction Item ID %d during issue.', $transactionItem->id));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
            }

            $transaction->loanApplication->updateOverallStatusAfterTransaction();

            // Fix: Call the correct method on NotificationService
            $this->notificationService->notifyLoanIssued(
                $transaction->loanApplication->user,
                $transaction->loanApplication,
                $transaction
            );
        });
    }

    public function returnLoanTransaction(
        LoanTransaction $transaction,
        array $loanTransactionItemsData,
        array $accessories,
        ?string $notes
    ): void {
        DB::transaction(function () use ($transaction, $loanTransactionItemsData, $accessories, $notes) {
            $transaction->return_notes = $notes;
            $transaction->accessories_checklist_on_return = json_encode($accessories);
            $transaction->transaction_date = Carbon::now();
            $transaction->status = $this->determineOverallReturnTransactionStatus($loanTransactionItemsData);
            $transaction->save();
            Log::info(self::LOG_AREA.sprintf('Loan Transaction ID %d updated and marked as %s.', $transaction->id, $transaction->status));

            foreach ($loanTransactionItemsData as $itemData) {
                $transactionItem = LoanTransactionItem::find($itemData['id']);

                if (!$transactionItem) {
                    Log::error(self::LOG_AREA.sprintf('Loan Transaction Item ID %d not found during returnLoanTransaction.', $itemData['id']));
                    throw new RuntimeException('Loan Transaction Item not found.');
                }

                $transactionItem->quantity_returned = $itemData['quantity_transacted'];
                $transactionItem->condition_on_return = $itemData['equipment_condition_on_return'] ?? Equipment::CONDITION_GOOD;
                $transactionItem->item_notes = $itemData['return_notes'] ?? null;
                $transactionItem->return_status = $this->determineItemReturnStatus($transactionItem->condition_on_return);
                $transactionItem->accessories_checklist_on_return = json_encode($accessories);
                $transactionItem->save();

                $equipment = $transactionItem->equipment;
                if ($equipment) {
                    switch ($transactionItem->condition_on_return) {
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
                    $equipment->current_loan_id = null;
                    $equipment->save();
                    Log::info(self::LOG_AREA.sprintf('Equipment ID %d status set to %s during return transaction.', $equipment->id, $equipment->status));
                } else {
                    Log::error(self::LOG_AREA.sprintf('Equipment not found for Loan Transaction Item ID %d during return.', $transactionItem->id));
                    throw new RuntimeException('Equipment not found for transaction item.');
                }
            }

            $transaction->loanApplication->updateOverallStatusAfterTransaction();

            // Fix: Call the correct method on NotificationService
            $this->notificationService->notifyLoanReturned(
                $transaction->loanApplication->user,
                $transaction->loanApplication,
                $transaction
            );
        });
    }

    private function determineItemReturnStatus(string $conditionOnReturn): string
    {
        return match ($conditionOnReturn) {
            Equipment::CONDITION_MINOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            Equipment::CONDITION_MAJOR_DAMAGE => LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            Equipment::CONDITION_UNSERVICEABLE => LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
            Equipment::CONDITION_LOST => LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
            default => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
        };
    }

    private function determineOverallReturnTransactionStatus(array $itemData): string
    {
        $hasDamage = false;
        $hasLost = false;
        $allReturnedGood = true;

        foreach ($itemData as $item) {
            $condition = $item['equipment_condition_on_return'] ?? Equipment::CONDITION_GOOD;
            if ($condition === Equipment::CONDITION_LOST) {
                $hasLost = true;
                $allReturnedGood = false;
            }

            if (in_array($condition, [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE])) {
                $hasDamage = true;
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
}
