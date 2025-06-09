<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LoanTransactionSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Loan Transactions seeding (Revised Officer Logic)...');

        $auditUserForAudit = User::orderBy('id')->first();

        if (LoanApplication::count() === 0) {
            Log::warning('No Loan Applications found. Some transactions may not be linked effectively or created.');
        }
        if (Equipment::count() === 0) {
            Log::warning('No Equipment found. Transaction items may not link to specific equipment.');
        }
        if (User::count() === 0) {
            Log::error('No Users for officers/audit. Aborting LoanTransactionsSeeder.');

            return;
        }

        $bpmOfficerRoleNames = ['BPM Staff', 'Admin'];
        $officerIds = User::query()->whereHas('roles', function (Builder $q) use ($bpmOfficerRoleNames) {
            $q->whereIn('name', $bpmOfficerRoleNames);
        })->pluck('id');

        if ($officerIds->isEmpty()) {
            Log::warning('No BPM/Admin officers found based on roles. Transactions may use any user as fallback for officer assignments.');
            $officerIds = User::pluck('id');
            if ($officerIds->isEmpty()) {
                Log::error('No users at all to act as officers. Aborting LoanTransactionsSeeder.');

                return;
            }
        }

        $approvedApplications = LoanApplication::whereIn('status', [
            LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_PARTIALLY_ISSUED,
        ])
            ->with('loanApplicationItems') // <--- THIS IS THE CRUCIAL FIX HERE
            ->get();

        $availableEquipmentCollection = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->get()->keyBy('id');

        $issuedTransactionsCollection = new \Illuminate\Database\Eloquent\Collection;

        Log::info("Attempting to seed 'Issued' Loan Transactions for {$approvedApplications->count()} applications...");
        foreach ($approvedApplications as $application) {
            if ($availableEquipmentCollection->isEmpty() && $application->loanApplicationItems()->exists()) { // Check using correct relationship
                Log::warning("No more available equipment to issue for application ID: {$application->id}");
                break;
            }

            $transaction = LoanTransaction::factory()
                ->issue()
                ->for($application)
                ->create([
                    'issuing_officer_id' => $officerIds->random(),
                    'receiving_officer_id' => $application->user_id,
                ]);

            foreach ($application->loanApplicationItems as $appItem) { // <--- ENSURE THIS USES CORRECT RELATIONSHIP
                $quantityToIssue = $appItem->quantity_approved ?? $appItem->quantity_requested;
                for ($i = 0; $i < $quantityToIssue; $i++) {
                    if ($availableEquipmentCollection->isEmpty()) {
                        Log::warning("Ran out of available equipment while processing application ID: {$application->id}, item type: {$appItem->equipment_type}");
                        break 2;
                    }
                    $equipmentToIssue = $availableEquipmentCollection->pop();
                    if (! $equipmentToIssue) {
                        continue;
                    }

                    LoanTransactionItem::factory()
                        ->for($transaction)
                        ->for($equipmentToIssue, 'equipment')
                        ->for($appItem, 'loanApplicationItem')
                        ->issued()
                        ->create(['quantity_transacted' => 1]);
                    $equipmentToIssue->update(['status' => Equipment::STATUS_ON_LOAN]);
                }
            }
            if ($application->status !== LoanApplication::STATUS_ISSUED) {
                $application->update(['status' => LoanApplication::STATUS_ISSUED]);
            }
            $issuedTransactionsCollection->add($transaction);
            if ($issuedTransactionsCollection->count() >= 20) {
                break;
            }
        }
        Log::info("Created {$issuedTransactionsCollection->count()} 'Issued' Loan Transactions with items.");

        $transactionsToReturn = $issuedTransactionsCollection
            ->where('type', LoanTransaction::TYPE_ISSUE)
            ->take(ceil($issuedTransactionsCollection->count() * 0.7));

        Log::info("Attempting to seed 'Returned' Loan Transactions for {$transactionsToReturn->count()} issued transactions...");
        foreach ($transactionsToReturn as $issueTransaction) {
            if (! $issueTransaction->loanApplication) {
                continue;
            }

            $returnTransaction = LoanTransaction::factory()
                ->return()
                ->for($issueTransaction->loanApplication)
                ->create([
                    'returning_officer_id' => $issueTransaction->loanApplication->user_id,
                    'return_accepting_officer_id' => $officerIds->random(),
                    'related_transaction_id' => $issueTransaction->id,
                ]);

            foreach ($issueTransaction->loanTransactionItems as $issuedItem) {
                if (! $issuedItem->equipment) {
                    Log::warning("Skipping return for transaction item ID {$issuedItem->id} due to missing equipment data.");

                    continue;
                }
                LoanTransactionItem::factory()
                    ->for($returnTransaction)
                    ->for($issuedItem->equipment, 'equipment')
                    ->for($issuedItem->loanApplicationItem, 'loanApplicationItem')
                    ->returnedGood()
                    ->create([
                        'quantity_transacted' => $issuedItem->quantity_transacted,
                        'accessories_checklist_issue' => $issuedItem->accessories_checklist_issue,
                    ]);
                $issuedItem->equipment->update(['status' => Equipment::STATUS_AVAILABLE]);
            }
            if ($issueTransaction->loanApplication->status !== LoanApplication::STATUS_RETURNED) {
                $issueTransaction->loanApplication->update(['status' => LoanApplication::STATUS_RETURNED]);
            }
            if ($issueTransaction->status !== LoanTransaction::STATUS_COMPLETED && $issueTransaction->status !== LoanTransaction::STATUS_RETURNED) {
                $issueTransaction->update(['status' => LoanTransaction::STATUS_COMPLETED]);
            }
        }
        Log::info("Created {$transactionsToReturn->count()} 'Returned' Loan Transactions.");

        $remainingIssuedForOverdue = LoanApplication::where('status', LoanApplication::STATUS_ISSUED)
            ->whereDate('loan_end_date', '<', Carbon::now()->toDateString())
            ->limit(5)
            ->get();

        Log::info("Attempting to mark {$remainingIssuedForOverdue->count()} applications as overdue...");
        foreach ($remainingIssuedForOverdue as $app) {
            $app->update(['status' => LoanApplication::STATUS_OVERDUE]);
            $app->loanTransactions()
                ->where('type', LoanTransaction::TYPE_ISSUE)
                ->where('status', LoanTransaction::STATUS_ISSUED)
                ->update(['status' => LoanTransaction::STATUS_OVERDUE]);
        }
        Log::info('Marked applications and relevant transactions as overdue.');

        Log::info('Loan Transactions seeding complete.');
    }
}
