<?php

namespace Database\Factories;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for the LoanTransaction model.
 *
 * - Uses static caches for related model IDs to minimize repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use this factory with a seeder that ensures users and loan applications exist before creating loan transactions.
 */
class LoanTransactionFactory extends Factory
{
    protected $model = LoanTransaction::class;

    public function definition(): array
    {
        // Static caches to minimize DB queries for related IDs
        static $loanApplicationIds;
        static $userIds;
        if (! isset($loanApplicationIds)) {
            $loanApplicationIds = LoanApplication::pluck('id')->all();
        }
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick related IDs or null if none exist (should be ensured by seeder)
        $loanApplicationId = ! empty($loanApplicationIds) ? Arr::random($loanApplicationIds) : null;
        $officerId         = ! empty($userIds) ? Arr::random($userIds) : null;
        $otherOfficerId    = ! empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for speed and realism
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Transaction type and relevant fields
        $type = $this->faker->randomElement([
            LoanTransaction::TYPE_ISSUE,
            LoanTransaction::TYPE_RETURN,
        ]);

        $transactionDate = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));

        // Fields for issue or return
        $issuingOfficerId             = null;
        $receivingOfficerId           = null;
        $issueNotes                   = null;
        $issueTimestamp               = null;
        $returningOfficerId           = null;
        $returnAcceptingOfficerId     = null;
        $returnNotes                  = null;
        $returnTimestamp              = null;
        $accessoriesList              = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $accessoriesChecklistOnIssue  = null;
        $accessoriesChecklistOnReturn = null;

        if ($type === LoanTransaction::TYPE_ISSUE) {
            $issuingOfficerId            = $officerId;
            $receivingOfficerId          = $otherOfficerId;
            $issueNotes                  = $msFaker->optional(0.3)->sentence(10);
            $issueTimestamp              = $transactionDate->copy()->addMinutes($this->faker->numberBetween(0, 120));
            $accessoriesChecklistOnIssue = $this->faker->randomElements($accessoriesList, $this->faker->numberBetween(0, count($accessoriesList)));
        } else {
            $returningOfficerId           = $officerId;
            $returnAcceptingOfficerId     = $otherOfficerId;
            $returnNotes                  = $msFaker->optional(0.3)->sentence(10);
            $returnTimestamp              = $transactionDate->copy()->addMinutes($this->faker->numberBetween(0, 120));
            $accessoriesChecklistOnReturn = $this->faker->randomElements($accessoriesList, $this->faker->numberBetween(0, count($accessoriesList)));
        }

        // Choose a valid status from migration/model
        $statusOptions = [
            LoanTransaction::STATUS_PENDING,
            LoanTransaction::STATUS_ISSUED,
            LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION,
            LoanTransaction::STATUS_RETURNED_GOOD,
            LoanTransaction::STATUS_RETURNED_DAMAGED,
            LoanTransaction::STATUS_ITEMS_REPORTED_LOST,
            LoanTransaction::STATUS_COMPLETED,
            LoanTransaction::STATUS_CANCELLED,
            LoanTransaction::STATUS_OVERDUE,
            LoanTransaction::STATUS_RETURNED_WITH_LOSS,
            LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS,
            LoanTransaction::STATUS_PARTIALLY_RETURNED,
            LoanTransaction::STATUS_RETURNED,
        ];
        $status = $this->faker->randomElement($statusOptions);

        // Timestamps for creation/update and soft deletes
        $createdAt = $transactionDate->copy();
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $officerId : null;

        return [
            'loan_application_id'             => $loanApplicationId,
            'type'                            => $type,
            'transaction_date'                => $transactionDate,
            'issuing_officer_id'              => $issuingOfficerId,
            'receiving_officer_id'            => $receivingOfficerId,
            'accessories_checklist_on_issue'  => $accessoriesChecklistOnIssue,
            'issue_notes'                     => $issueNotes,
            'issue_timestamp'                 => $issueTimestamp,
            'returning_officer_id'            => $returningOfficerId,
            'return_accepting_officer_id'     => $returnAcceptingOfficerId,
            'accessories_checklist_on_return' => $accessoriesChecklistOnReturn,
            'return_notes'                    => $returnNotes,
            'return_timestamp'                => $returnTimestamp,
            'related_transaction_id'          => null, // Can be set via state
            'due_date'                        => $type === LoanTransaction::TYPE_ISSUE ? $transactionDate->copy()->addDays($this->faker->numberBetween(3, 14))->toDateString() : null,
            'status'                          => $status,
            'created_by'                      => $officerId,
            'updated_by'                      => $officerId,
            'deleted_by'                      => $deletedBy,
            'created_at'                      => $createdAt,
            'updated_at'                      => $updatedAt,
            'deleted_at'                      => $deletedAt,
        ];
    }

    /**
     * State for an issue transaction.
     */
    public function asIssue(): static
    {
        // Use state to ensure proper type and fields for issue transaction
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $officerId       = ! empty($userIds) ? Arr::random($userIds) : null;
        $accessoriesList = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(function (array $attributes) use ($officerId, $accessoriesList) {
            $transactionDate = $attributes['transaction_date'] ?? now();

            return [
                'type'                            => LoanTransaction::TYPE_ISSUE,
                'issuing_officer_id'              => $officerId,
                'receiving_officer_id'            => $officerId,
                'issue_notes'                     => \Faker\Factory::create('ms_MY')->optional(0.3)->sentence(10),
                'issue_timestamp'                 => $transactionDate instanceof Carbon ? $transactionDate->copy()->addMinutes(random_int(0, 120)) : now(),
                'returning_officer_id'            => null,
                'return_accepting_officer_id'     => null,
                'return_notes'                    => null,
                'return_timestamp'                => null,
                'accessories_checklist_on_issue'  => fake()->randomElements($accessoriesList, fake()->numberBetween(0, count($accessoriesList))),
                'accessories_checklist_on_return' => null,
                'due_date'                        => $transactionDate instanceof Carbon ? $transactionDate->copy()->addDays(fake()->numberBetween(3, 14))->toDateString() : now()->addDays(7)->toDateString(),
                'status'                          => LoanTransaction::STATUS_ISSUED,
            ];
        });
    }

    /**
     * State for a return transaction.
     */
    public function asReturn(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $officerId       = ! empty($userIds) ? Arr::random($userIds) : null;
        $accessoriesList = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(function (array $attributes) use ($officerId, $accessoriesList) {
            $transactionDate = $attributes['transaction_date'] ?? now();

            return [
                'type'                            => LoanTransaction::TYPE_RETURN,
                'issuing_officer_id'              => null,
                'receiving_officer_id'            => null,
                'issue_notes'                     => null,
                'issue_timestamp'                 => null,
                'returning_officer_id'            => $officerId,
                'return_accepting_officer_id'     => $officerId,
                'return_notes'                    => \Faker\Factory::create('ms_MY')->optional(0.3)->sentence(10),
                'return_timestamp'                => $transactionDate instanceof Carbon ? $transactionDate->copy()->addMinutes(random_int(0, 120)) : now(),
                'accessories_checklist_on_issue'  => null,
                'accessories_checklist_on_return' => fake()->randomElements($accessoriesList, fake()->numberBetween(0, count($accessoriesList))),
                'due_date'                        => null,
                'status'                          => LoanTransaction::STATUS_RETURNED_GOOD,
            ];
        });
    }

    /**
     * Mark the transaction as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $officerId = ! empty($userIds) ? Arr::random($userIds) : null;

        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $officerId,
        ]);
    }

    /**
     * Assign this transaction to a specific loan application.
     */
    public function forLoanApplication(LoanApplication|int $loanApp): static
    {
        return $this->state([
            'loan_application_id' => $loanApp instanceof LoanApplication ? $loanApp->id : $loanApp,
        ]);
    }

    /**
     * Set a related transaction (for return referencing issue).
     */
    public function relatedTo(LoanTransaction|int $related): static
    {
        return $this->state([
            'related_transaction_id' => $related instanceof LoanTransaction ? $related->id : $related,
        ]);
    }
}
