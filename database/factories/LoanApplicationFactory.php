<?php

namespace Database\Factories;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for LoanApplication model.
 *
 * - Uses static caches for related User IDs to minimize repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with seeder that ensures users exist before creating loan applications.
 */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        // Static cache for User IDs (applicants and officers)
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $userId = !empty($userIds) ? Arr::random($userIds) : null;
        $officerId = !empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for realism and speed
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Date logic
        $applicationDate = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $loanStartDate = (clone $applicationDate)->addDays($this->faker->numberBetween(1, 10));
        $loanEndDate = (clone $loanStartDate)->addDays($this->faker->numberBetween(3, 14));
        $approvalDate = (clone $applicationDate)->addDays($this->faker->numberBetween(0, 5));

        // Status options (fallback to string if constants missing)
        $statuses = [
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
        $auditUserId = $userId;
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
            'user_id'                => $userId,
            'responsible_officer_id' => $officerId,
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

    /**
     * State: Certified application.
     * Sets applicant confirmation timestamp and submitted_at to simulate "certified" (if business logic uses it).
     */
    public function certified(): static
    {
        $now = now();
        return $this->state([
            'applicant_confirmation_timestamp' => $now,
            'submitted_at' => $now,
        ]);
    }

    /**
     * State: Pending support.
     */
    public function pendingSupport(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_PENDING_SUPPORT ?? 'pending_support',
        ]);
    }

    /**
     * State: Cancelled application.
     */
    public function cancelled(): static
    {
        return $this->state([
            'status' => LoanApplication::STATUS_CANCELLED ?? 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => null,
        ]);
    }

    /**
     * After-creation: Attach N items to this application.
     * Usage: ->withItems(2)
     */
    public function withItems(int $count = 1): static
    {
        return $this->afterCreating(function (LoanApplication $application) use ($count) {
            // Ensure related LoanApplicationItemFactory is optimized for bulk if needed
            \App\Models\LoanApplicationItem::factory()
                ->count($count)
                ->forLoanApplication($application)
                ->create([
                    'created_by' => $application->created_by,
                    'updated_by' => $application->updated_by,
                ]);
        });
    }
}
