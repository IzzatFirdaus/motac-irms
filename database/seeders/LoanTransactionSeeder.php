<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // For collections of Eloquent models (supports ->load())
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as SupportCollection; // For ID lists, etc.
use Illuminate\Support\Facades\Log;

/**
 * Optimized seeder for LoanTransaction and LoanTransactionItem models.
 *
 * - Batch-creates "issue" and "return" transactions for approved loan applications.
 * - Minimizes database queries by caching officer/equipment IDs and eager-loading relationships.
 * - Handles status and item state updates efficiently.
 * - Avoids calling instance ->update() on values that static analysis may infer as stdClass by using whereKey() updates.
 */
class LoanTransactionSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Loan Transactions seeding (Optimized)...');

        // Ensure there are users and equipment before proceeding
        if (User::count() === 0 || Equipment::count() === 0) {
            Log::error('LoanTransactionSeeder requires at least one User and one Equipment record. Aborting.');

            return;
        }

        $officerIds = $this->getOfficerIds(); // SupportCollection of user IDs
        if ($officerIds->isEmpty()) {
            Log::error('No users available to act as officers. Aborting.');

            return;
        }

        // Seed issue transactions for approved/partially issued applications
        $issuedTransactions = $this->seedIssueTransactions($officerIds);

        // Seed return transactions for a subset of issued transactions
        $this->seedReturnTransactions($issuedTransactions, $officerIds);

        // Mark some applications as overdue
        $this->markOverdueApplications();

        Log::info('Loan Transactions seeding complete.');
    }

    /**
     * Creates 'issue' transactions for approved/partially issued loan applications.
     *
     * @param SupportCollection $officerIds A list of officer user IDs to assign.
     *
     * @return EloquentCollection<LoanTransaction> A collection of created issue transactions.
     */
    private function seedIssueTransactions(SupportCollection $officerIds): EloquentCollection
    {
        // Get applications ready to be issued and eager-load items
        $approvedApplications = LoanApplication::query()
            ->whereIn('status', [
                LoanApplication::STATUS_APPROVED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
            ])
            ->with('loanApplicationItems')
            ->get();

        // Cache available equipment (only those with status AVAILABLE)
        $availableEquipment = Equipment::query()
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->get()
            ->keyBy('id');

        $issuedTransactions = new EloquentCollection();

        Log::info(sprintf("Creating 'Issued' Loan Transactions for %s applications...", $approvedApplications->count()));

        foreach ($approvedApplications as $application) {
            // If there are items to issue but no equipment left, stop early to avoid empty loops
            if ($availableEquipment->isEmpty() && $application->loanApplicationItems()->exists()) {
                Log::warning('No more available equipment to issue for application ID: ' . $application->id);
                break;
            }

            // Create an issue transaction for this application
            /** @var LoanTransaction $transaction */
            $transaction = LoanTransaction::factory()
                ->asIssue()
                ->forLoanApplication($application)
                ->create([
                    'issuing_officer_id'   => $officerIds->random(),
                    'receiving_officer_id' => $application->user_id,
                ]);

            // For each item requested/approved, issue equipment
            foreach ($application->loanApplicationItems as $appItem) {
                $quantityToIssue = $appItem->quantity_approved ?? $appItem->quantity_requested;

                for ($i = 0; $i < $quantityToIssue; $i++) {
                    if ($availableEquipment->isEmpty()) {
                        Log::warning('Ran out of equipment for application ID: ' . $application->id);
                        break 2; // Break out of both loops
                    }

                    // Shift the first available equipment (O(1) on a keyed collection)
                    /** @var Equipment|null $equipmentToIssue */
                    $equipmentToIssue = $availableEquipment->shift();
                    if (! $equipmentToIssue) {
                        continue;
                    }

                    // Create a transaction item for the equipment
                    LoanTransactionItem::factory()
                        ->forTransaction($transaction)
                        ->forEquipment($equipmentToIssue)
                        ->forLoanApplicationItem($appItem)
                        ->issued()
                        ->create([
                            'quantity_transacted' => 1,
                        ]);

                    // Update equipment status to "on loan"
                    Equipment::whereKey($equipmentToIssue->getKey())->update(['status' => Equipment::STATUS_ON_LOAN]);
                }
            }

            // Update the application's workflow status after transaction
            // Using instance method for domain logic; underlying update inside the model remains on Eloquent.
            $application->updateOverallStatusAfterTransaction();

            $issuedTransactions->push($transaction);

            // Limit for demo/dev environments to avoid excessive seeding
            if ($issuedTransactions->count() >= 20) {
                break;
            }
        }

        Log::info(sprintf("Created %d 'Issued' Loan Transactions.", $issuedTransactions->count()));

        return $issuedTransactions;
    }

    /**
     * Creates 'return' transactions for a subset of issued transactions.
     *
     * @param EloquentCollection<LoanTransaction> $issuedTransactions
     */
    private function seedReturnTransactions(EloquentCollection $issuedTransactions, SupportCollection $officerIds): void
    {
        // Eager-load relationships on the Eloquent collection (->load is available on Eloquent collections)
        $issuedTransactions->load(
            'loanApplication',
            'loanTransactionItems.equipment',
            'loanTransactionItems.loanApplicationItem'
        );

        // Return approximately 70% of the issued transactions
        $transactionsToReturn = $issuedTransactions->take((int) ceil($issuedTransactions->count() * 0.7));

        Log::info(sprintf("Creating 'Returned' Loan Transactions for %d issued transactions...", $transactionsToReturn->count()));

        /** @var LoanTransaction $issueTransaction */
        foreach ($transactionsToReturn as $issueTransaction) {
            if (! $issueTransaction->loanApplication) {
                continue;
            }

            // Create a return transaction referencing the issue transaction
            /** @var LoanTransaction $returnTransaction */
            $returnTransaction = LoanTransaction::factory()
                ->asReturn()
                ->forLoanApplication($issueTransaction->loanApplication)
                ->relatedTo($issueTransaction)
                ->create([
                    'returning_officer_id'        => $issueTransaction->loanApplication->user_id,
                    'return_accepting_officer_id' => $officerIds->random(),
                ]);

            // For each issued item, create a corresponding returned item
            foreach ($issueTransaction->loanTransactionItems as $issuedItem) {
                if (! $issuedItem->equipment) {
                    Log::warning(sprintf('Skipping return for item ID %s due to missing equipment.', $issuedItem->id));

                    continue;
                }

                LoanTransactionItem::factory()
                    ->forTransaction($returnTransaction)
                    ->forEquipment($issuedItem->equipment)
                    ->forLoanApplicationItem($issuedItem->loanApplicationItem)
                    ->returnedGood()
                    ->create([
                        'quantity_transacted' => $issuedItem->quantity_transacted,
                    ]);

                // Update equipment status back to "available"
                Equipment::whereKey($issuedItem->equipment->getKey())->update(['status' => Equipment::STATUS_AVAILABLE]);
            }

            // Update application overall status (domain logic on model)
            $issueTransaction->loanApplication->updateOverallStatusAfterTransaction();

            // Avoid static analysis warning about stdClass::update() by using whereKey() update
            LoanTransaction::whereKey($issueTransaction->getKey())->update([
                'status' => LoanTransaction::STATUS_COMPLETED,
            ]);
        }

        Log::info(sprintf("Created %d 'Returned' Loan Transactions.", $transactionsToReturn->count()));
    }

    /**
     * Mark issued applications that are past their end date as overdue.
     * Uses whereKey() update to avoid any static analysis confusion.
     */
    private function markOverdueApplications(): void
    {
        $appsToMarkOverdue = LoanApplication::query()
            ->where('status', LoanApplication::STATUS_ISSUED)
            ->whereDate('loan_end_date', '<', Carbon::now()->toDateString())
            ->limit(5)
            ->get();

        Log::info(sprintf('Marking %s applications as overdue...', $appsToMarkOverdue->count()));

        /** @var LoanApplication $app */
        foreach ($appsToMarkOverdue as $app) {
            // Update via query builder to sidestep "stdClass::update()" static-analysis warnings
            LoanApplication::whereKey($app->getKey())->update([
                'status' => LoanApplication::STATUS_OVERDUE,
            ]);
        }

        Log::info('Marked relevant applications as overdue.');
    }

    /**
     * Retrieves User IDs for BPM/Admin roles, with a fallback to any user.
     * Returns a SupportCollection of IDs (not an Eloquent collection).
     */
    private function getOfficerIds(): SupportCollection
    {
        $bpmOfficerRoleNames = ['BPM Staff', 'Admin'];

        $ids = User::query()
            ->whereHas('roles', fn (Builder $q) => $q->whereIn('name', $bpmOfficerRoleNames))
            ->pluck('id');

        if ($ids->isEmpty()) {
            Log::warning('No BPM/Admin officers found. Falling back to all users.');

            return User::pluck('id'); // Support\Collection
        }

        return $ids; // Support\Collection
    }
}
