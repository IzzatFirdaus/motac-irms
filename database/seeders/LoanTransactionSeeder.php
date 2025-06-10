<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LoanTransactionSeeder extends Seeder
{
    /**
     * The main entry point for the seeder.
     * It orchestrates the seeding of different transaction types.
     */
    public function run(): void
    {
        Log::info('Starting Loan Transactions seeding...');

        if (User::count() === 0 || Equipment::count() === 0) {
            Log::error('Seeder requires at least one User and one Equipment record. Aborting LoanTransactionsSeeder.');
            return;
        }

        // Get BPM/Admin officer IDs, with a fallback to any user.
        $officerIds = $this->getOfficerIds();
        if ($officerIds->isEmpty()) {
            Log::error('No users available to act as officers. Aborting.');
            return;
        }

        $issuedTransactions = $this->seedIssueTransactions($officerIds);
        $this->seedReturnTransactions($issuedTransactions, $officerIds);
        $this->markOverdueApplications();

        Log::info('Loan Transactions seeding complete.');
    }

    /**
     * Creates 'issue' transactions for approved loan applications.
     *
     * @param Collection $officerIds
     * @return Collection The collection of newly created 'issue' transactions.
     */
    private function seedIssueTransactions(Collection $officerIds): Collection
    {
        $approvedApplications = LoanApplication::whereIn('status', [
            LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_PARTIALLY_ISSUED,
        ])->with('loanApplicationItems')->get();

        $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)->get()->keyBy('id');
        $issuedTransactions = new Collection();

        Log::info("Attempting to seed 'Issued' Loan Transactions for {$approvedApplications->count()} applications...");

        foreach ($approvedApplications as $application) {
            if ($availableEquipment->isEmpty() && $application->loanApplicationItems()->exists()) {
                Log::warning("No more available equipment to issue for application ID: {$application->id}");
                break;
            }

            $transaction = LoanTransaction::factory()->issue()->for($application)->create([
                'issuing_officer_id' => $officerIds->random(),
                'receiving_officer_id' => $application->user_id,
            ]);

            foreach ($application->loanApplicationItems as $appItem) {
                $quantityToIssue = $appItem->quantity_approved ?? $appItem->quantity_requested;
                for ($i = 0; $i < $quantityToIssue; $i++) {
                    if ($availableEquipment->isEmpty()) {
                        Log::warning("Ran out of equipment for application ID: {$application->id}");
                        break 2;
                    }
                    $equipmentToIssue = $availableEquipment->pop();
                    if (!$equipmentToIssue) continue;

                    LoanTransactionItem::factory()->for($transaction)->for($equipmentToIssue, 'equipment')->for($appItem, 'loanApplicationItem')->issued()->create(['quantity_transacted' => 1]);
                    $equipmentToIssue->update(['status' => Equipment::STATUS_ON_LOAN]);
                }
            }

            $application->updateOverallStatusAfterTransaction(); // Use model logic to update status
            $issuedTransactions->add($transaction);

            if ($issuedTransactions->count() >= 20) break;
        }

        Log::info("Created {$issuedTransactions->count()} 'Issued' Loan Transactions.");
        return $issuedTransactions;
    }

    /**
     * Creates 'return' transactions for a subset of the issued transactions.
     *
     * @param Collection $issuedTransactions
     * @param Collection $officerIds
     */
    private function seedReturnTransactions(Collection $issuedTransactions, Collection $officerIds): void
    {
        // Eager load relationships to prevent N+1 queries in the loop
        $issuedTransactions->load('loanApplication', 'loanTransactionItems.equipment', 'loanTransactionItems.loanApplicationItem');

        $transactionsToReturn = $issuedTransactions->take(ceil($issuedTransactions->count() * 0.7));

        Log::info("Attempting to seed 'Returned' Loan Transactions for {$transactionsToReturn->count()} issued transactions...");

        foreach ($transactionsToReturn as $issueTransaction) {
            if (!$issueTransaction->loanApplication) continue;

            $returnTransaction = LoanTransaction::factory()->return()->for($issueTransaction->loanApplication)->create([
                'returning_officer_id' => $issueTransaction->loanApplication->user_id,
                'return_accepting_officer_id' => $officerIds->random(),
                'related_transaction_id' => $issueTransaction->id,
            ]);

            foreach ($issueTransaction->loanTransactionItems as $issuedItem) {
                if (!$issuedItem->equipment) {
                    Log::warning("Skipping return for item ID {$issuedItem->id} due to missing equipment.");
                    continue;
                }
                LoanTransactionItem::factory()->for($returnTransaction)->for($issuedItem->equipment, 'equipment')->for($issuedItem->loanApplicationItem, 'loanApplicationItem')->returnedGood()->create(['quantity_transacted' => $issuedItem->quantity_transacted]);
                $issuedItem->equipment->update(['status' => Equipment::STATUS_AVAILABLE]);
            }

            $issueTransaction->loanApplication->updateOverallStatusAfterTransaction();
            $issueTransaction->update(['status' => LoanTransaction::STATUS_COMPLETED]);
        }

        Log::info("Created {$transactionsToReturn->count()} 'Returned' Loan Transactions.");
    }

    /**
     * Finds issued applications that are past their end date and marks them as overdue.
     */
    private function markOverdueApplications(): void
    {
        $appsToMarkOverdue = LoanApplication::where('status', LoanApplication::STATUS_ISSUED)
            ->whereDate('loan_end_date', '<', Carbon::now()->toDateString())
            ->limit(5)
            ->get();

        Log::info("Attempting to mark {$appsToMarkOverdue->count()} applications as overdue...");
        foreach ($appsToMarkOverdue as $app) {
            $app->transitionToStatus(LoanApplication::STATUS_OVERDUE);
        }
        Log::info('Marked relevant applications as overdue.');
    }

    /**
     * Retrieves User IDs for BPM/Admin roles, with a fallback to any user.
     *
     * @return Collection
     */
    private function getOfficerIds(): Collection
    {
        $bpmOfficerRoleNames = ['BPM Staff', 'Admin'];
        $officerIds = User::query()->whereHas('roles', fn (Builder $q) => $q->whereIn('name', $bpmOfficerRoleNames))->pluck('id');

        if ($officerIds->isEmpty()) {
            Log::warning('No BPM/Admin officers found. Using any user as fallback.');
            return User::pluck('id');
        }

        return $officerIds;
    }
}
