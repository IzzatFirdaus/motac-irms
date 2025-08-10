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

        // Notes for the transaction
        $notes = $msFaker->optional(0.3)->sentence(10);

        // Timestamps for creation/update and soft deletes
        $createdAt = $transactionDate->copy();
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $officerId : null;

        // Officer columns: responsible for issuance/return
        $issuingOfficerId = $type === LoanTransaction::TYPE_ISSUE ? $officerId : null;
        $returnAcceptingOfficerId = $type === LoanTransaction::TYPE_RETURN ? $officerId : null;

        return [
            'loan_application_id'        => $loanApplicationId,
            'type'                       => $type,
            'transaction_date'           => $transactionDate,
            'issuing_officer_id'         => $issuingOfficerId,
            'return_accepting_officer_id'=> $returnAcceptingOfficerId,
            'notes'                      => $notes,
            'created_by'                 => $officerId,
            'updated_by'                 => $officerId,
            'deleted_by'                 => $deletedBy,
            'created_at'                 => $createdAt,
            'updated_at'                 => $updatedAt,
            'deleted_at'                 => $deletedAt,
        ];
    }

    /**
     * State for an issue transaction.
     */
    public function asIssue(): static
    {
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        return $this->state([
            'type' => LoanTransaction::TYPE_ISSUE,
            'issuing_officer_id' => $officerId,
            'return_accepting_officer_id' => null,
        ]);
    }

    /**
     * State for a return transaction.
     */
    public function asReturn(): static
    {
        $officerId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        return $this->state([
            'type' => LoanTransaction::TYPE_RETURN,
            'issuing_officer_id' => null,
            'return_accepting_officer_id' => $officerId,
        ]);
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
     * Assign to a specific loan application.
     */
    public function forLoanApplication(LoanApplication|int $loanApp): static
    {
        return $this->state([
            'loan_application_id' => $loanApp instanceof LoanApplication ? $loanApp->id : $loanApp,
        ]);
    }
}
