<?php

namespace Database\Factories;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the LoanTransaction model.
 *
 * Generates loan transaction records (either 'issue' or 'return'),
 * with correct relationships to applications and officers,
 * and with all audit and soft-delete fields as per migration/model.
 */
class LoanTransactionFactory extends Factory
{
    protected $model = LoanTransaction::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic notes
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create a loan application to associate with this transaction
        $loanApplicationId = LoanApplication::inRandomOrder()->value('id') ?? LoanApplication::factory()->create()->id;

        // Officer for audit and transaction responsibility
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Officer (LoanTransactionFactory)'])->id;

        // Transaction type: either 'issue' or 'return'
        $type = $this->faker->randomElement([
            LoanTransaction::TYPE_ISSUE,
            LoanTransaction::TYPE_RETURN,
        ]);

        // Transaction date: issued sometime in the past year
        $transactionDate = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));

        // Issue/Return specific fields
        $issueNotes = null;
        $returnNotes = null;
        $issuingOfficerId = null;
        $receivingOfficerId = null;
        $issueTimestamp = null;
        $returningOfficerId = null;
        $returnAcceptingOfficerId = null;
        $returnTimestamp = null;

        // Accessories arrays for issue/return
        $accessoriesList = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $accessoriesChecklistOnIssue = null;
        $accessoriesChecklistOnReturn = null;

        if ($type === LoanTransaction::TYPE_ISSUE) {
            // Issue transaction
            $issuingOfficerId = $officerId;
            $receivingOfficerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
            $issueNotes = $msFaker->optional(0.3)->sentence(10);
            $issueTimestamp = $transactionDate->copy()->addMinutes($this->faker->numberBetween(0, 120));
            $accessoriesChecklistOnIssue = $this->faker->randomElements($accessoriesList, $this->faker->numberBetween(0, count($accessoriesList)));
        } else {
            // Return transaction
            $returningOfficerId = $officerId;
            $returnAcceptingOfficerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
            $returnNotes = $msFaker->optional(0.3)->sentence(10);
            $returnTimestamp = $transactionDate->copy()->addMinutes($this->faker->numberBetween(0, 120));
            $accessoriesChecklistOnReturn = $this->faker->randomElements($accessoriesList, $this->faker->numberBetween(0, count($accessoriesList)));
        }

        // Timestamps for creation/update and soft deletes
        $createdAt = $transactionDate->copy();
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $officerId : null;

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
            LoanTransaction::STATUS_RETURNED, // migration has 'returned'
        ];
        $status = $this->faker->randomElement($statusOptions);

        return [
            'loan_application_id' => $loanApplicationId,
            'type' => $type,
            'transaction_date' => $transactionDate,
            'issuing_officer_id' => $issuingOfficerId,
            'receiving_officer_id' => $receivingOfficerId,
            'accessories_checklist_on_issue' => $accessoriesChecklistOnIssue,
            'issue_notes' => $issueNotes,
            'issue_timestamp' => $issueTimestamp,
            'returning_officer_id' => $returningOfficerId,
            'return_accepting_officer_id' => $returnAcceptingOfficerId,
            'accessories_checklist_on_return' => $accessoriesChecklistOnReturn,
            'return_notes' => $returnNotes,
            'return_timestamp' => $returnTimestamp,
            'related_transaction_id' => null, // can be set via state
            'due_date' => $type === LoanTransaction::TYPE_ISSUE ? $transactionDate->copy()->addDays($this->faker->numberBetween(3, 14))->toDateString() : null,
            'status' => $status,
            'created_by' => $officerId,
            'updated_by' => $officerId,
            'deleted_by' => $deletedBy,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * State for an issue transaction.
     *
     * Ensures only issue-related fields are set.
     */
    public function asIssue(): static
    {
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        $accessoriesList = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(function (array $attributes) use ($officerId, $accessoriesList) {
            $transactionDate = $attributes['transaction_date'] ?? now();
            return [
                'type' => LoanTransaction::TYPE_ISSUE,
                'issuing_officer_id' => $officerId,
                'receiving_officer_id' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
                'issue_notes' => \Faker\Factory::create('ms_MY')->optional(0.3)->sentence(10),
                'issue_timestamp' => $transactionDate instanceof Carbon ? $transactionDate->copy()->addMinutes(random_int(0, 120)) : now(),
                'returning_officer_id' => null,
                'return_accepting_officer_id' => null,
                'return_notes' => null,
                'return_timestamp' => null,
                'accessories_checklist_on_issue' => fake()->randomElements($accessoriesList, fake()->numberBetween(0, count($accessoriesList))),
                'accessories_checklist_on_return' => null,
                'due_date' => $transactionDate instanceof Carbon ? $transactionDate->copy()->addDays(fake()->numberBetween(3, 14))->toDateString() : now()->addDays(7)->toDateString(),
                'status' => LoanTransaction::STATUS_ISSUED,
            ];
        });
    }

    /**
     * State for a return transaction.
     *
     * Ensures only return-related fields are set.
     */
    public function asReturn(): static
    {
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        $accessoriesList = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(function (array $attributes) use ($officerId, $accessoriesList) {
            $transactionDate = $attributes['transaction_date'] ?? now();
            return [
                'type' => LoanTransaction::TYPE_RETURN,
                'issuing_officer_id' => null,
                'receiving_officer_id' => null,
                'issue_notes' => null,
                'issue_timestamp' => null,
                'returning_officer_id' => $officerId,
                'return_accepting_officer_id' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
                'return_notes' => \Faker\Factory::create('ms_MY')->optional(0.3)->sentence(10),
                'return_timestamp' => $transactionDate instanceof Carbon ? $transactionDate->copy()->addMinutes(random_int(0, 120)) : now(),
                'accessories_checklist_on_issue' => null,
                'accessories_checklist_on_return' => fake()->randomElements($accessoriesList, fake()->numberBetween(0, count($accessoriesList))),
                'due_date' => null,
                'status' => LoanTransaction::STATUS_RETURNED_GOOD,
            ];
        });
    }

    /**
     * Mark the transaction as soft deleted.
     */
    public function deleted(): static
    {
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
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
