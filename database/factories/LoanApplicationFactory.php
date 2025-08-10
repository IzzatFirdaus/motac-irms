<?php

namespace Database\Factories;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Factory for the LoanApplication model.
 *
 * Generates records for equipment loan applications, handling all relationships,
 * audit columns, and soft-delete fields as per migration/model/seeder.
 */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create users for applicant and responsible officer
        $applicant = User::inRandomOrder()->first() ?? User::factory()->create(['name' => 'Applicant (LoanApplicationFactory)']);
        $responsibleOfficer = User::inRandomOrder()->first() ?? User::factory()->create(['name' => 'Officer (LoanApplicationFactory)']);

        // Date logic: application, loan period, and approval
        $applicationDate = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $loanStartDate = (clone $applicationDate)->addDays($this->faker->numberBetween(1, 10));
        $loanEndDate = (clone $loanStartDate)->addDays($this->faker->numberBetween(3, 14));
        $approvalDate = (clone $applicationDate)->addDays($this->faker->numberBetween(0, 5));

        // Application status - pick from model constants
        $statuses = method_exists(LoanApplication::class, 'getStatusOptions')
            ? array_keys(LoanApplication::getStatusOptions())
            : [
                LoanApplication::STATUS_DRAFT ?? 'draft',
                LoanApplication::STATUS_PROCESSING ?? 'processing',
                LoanApplication::STATUS_PENDING_SUPPORT ?? 'pending_support',
                LoanApplication::STATUS_PENDING_APPROVER_REVIEW ?? 'pending_approver_review',
                LoanApplication::STATUS_PENDING_BPM_REVIEW ?? 'pending_bpm_review',
                LoanApplication::STATUS_APPROVED ?? 'approved',
                LoanApplication::STATUS_REJECTED ?? 'rejected',
                LoanApplication::STATUS_PARTIALLY_ISSUED ?? 'partially_issued',
                LoanApplication::STATUS_ISSUED ?? 'issued',
                LoanApplication::STATUS_RETURNED ?? 'returned',
                LoanApplication::STATUS_OVERDUE ?? 'overdue',
                LoanApplication::STATUS_CANCELLED ?? 'cancelled',
                LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION ?? 'partially_returned_pending_inspection',
                LoanApplication::STATUS_COMPLETED ?? 'completed',
            ];
        $status = $this->faker->randomElement($statuses);

        // For audit columns (created_by, updated_by, etc.)
        $auditUserId = $applicant->id;
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($loanEndDate, 'now')) : null;

        // Return location (fixed or random)
        $returnLocation = $this->faker->optional(0.8)->randomElement([
            'Unit ICT, Aras 1, MOTAC',
            'Stor Peralatan Aras 3',
            'Bahagian Pengurusan Maklumat',
            'Unit ICT, Putrajaya',
        ]);

        return [
            'user_id'                => $applicant->id,
            'responsible_officer_id' => $responsibleOfficer->id,
            'supporting_officer_id'  => null, // Optional, can be filled if needed
            'purpose'                => $msFaker->sentence(8), // Reason for application
            'location'               => $msFaker->city,
            'return_location'        => $returnLocation,
            'loan_start_date'        => $loanStartDate,
            'loan_end_date'          => $loanEndDate,
            'status'                 => $status,
            'rejection_reason'       => null,
            'applicant_confirmation_timestamp' => null,
            'submitted_at'           => null,
            'approved_by'            => null,
            'approved_at'            => $approvalDate,
            'rejected_by'            => null,
            'rejected_at'            => null,
            'cancelled_by'           => null,
            'cancelled_at'           => null,
            'admin_notes'            => $msFaker->optional(0.3)->sentence(10),
            'current_approval_officer_id' => null,
            'current_approval_stage'      => null,
            'created_by'             => $auditUserId,
            'updated_by'             => $auditUserId,
            'deleted_by'             => $isDeleted ? $auditUserId : null,
            'created_at'             => $applicationDate,
            'updated_at'             => $approvalDate,
            'deleted_at'             => $deletedAt,
        ];
    }

    /**
     * State: Approved application.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => LoanApplication::STATUS_APPROVED ?? 'approved',
                'approved_at' => now(),
                'approved_by' => $attributes['responsible_officer_id'] ?? null,
            ];
        });
    }

    /**
     * State: Draft application.
     */
    public function draft(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_DRAFT ?? 'draft',
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /**
     * State: Rejected application.
     */
    public function rejected(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_REJECTED ?? 'rejected',
            'rejected_at' => now(),
            'rejected_by' => null,
        ]);
    }

    /**
     * State: Application has been issued.
     */
    public function issued(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_ISSUED ?? 'issued',
        ]);
    }

    /**
     * State: Application is returned.
     */
    public function returned(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_RETURNED ?? 'returned',
        ]);
    }

    /**
     * State: Application is soft deleted.
     */
    public function deleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now(),
                'deleted_by' => $attributes['created_by'] ?? null,
            ];
        });
    }

    /**
     * Assigns to a specific user.
     */
    public function forUser(User|int $user): static
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->state([
            'user_id' => $userId,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Assigns a specific responsible officer.
     */
    public function withResponsibleOfficer(User|int $officer): static
    {
        return $this->state([
            'responsible_officer_id' => $officer instanceof User ? $officer->id : $officer,
        ]);
    }
}
