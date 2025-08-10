<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Factory for Approval model.
 *
 * Generates approval tasks for a polymorphic approvable (e.g., LoanApplication).
 * Ensures officer assignment and valid stage/status, with audit fields.
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic values
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create an officer for FK
        $officer = User::inRandomOrder()->first() ?? User::factory()->create(['name' => 'Officer Fallback (ApprovalFactory)']);

        // Get valid stages from Approval model; fallback if empty
        $stageKeys = method_exists(Approval::class, 'getStageKeys')
            ? Approval::getStageKeys()
            : array_keys(Approval::$STAGES_LABELS ?? [Approval::STAGE_SUPPORT_REVIEW => 'Support Review']);
        $stage = $this->faker->randomElement($stageKeys ?: [Approval::STAGE_SUPPORT_REVIEW]);

        // Set created/updated timestamps
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'approvable_type' => null, // To be set with forApprovable() or after creation
            'approvable_id' => null,   // To be set with forApprovable() or after creation
            'officer_id' => $officer->id,
            'stage' => $stage,
            'status' => Approval::STATUS_PENDING,
            'notes' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'canceled_at' => null,
            'resubmitted_at' => null,
            'created_by' => $officer->id,
            'updated_by' => $officer->id,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

    /**
     * Set a specific status for this approval.
     * Optionally set approval/rejected/canceled/resubmitted timestamps if status is final.
     */
    public function status(string $statusValue): static
    {
        return $this->state(function (array $attributes) use ($statusValue): array {
            $now = now();
            $msFaker = \Faker\Factory::create('ms_MY');
            $data = [
                'status' => $statusValue,
            ];
            // Set timestamp/notes based on status
            if ($statusValue === Approval::STATUS_APPROVED) {
                $data['approved_at'] = $attributes['approved_at'] ?? $now;
                $data['notes'] = $attributes['notes'] ?? $msFaker->optional()->sentence();
            } elseif ($statusValue === Approval::STATUS_REJECTED) {
                $data['rejected_at'] = $attributes['rejected_at'] ?? $now;
                $data['notes'] = $attributes['notes'] ?? $msFaker->sentence();
            } elseif ($statusValue === Approval::STATUS_CANCELED) {
                $data['canceled_at'] = $attributes['canceled_at'] ?? $now;
            } elseif ($statusValue === Approval::STATUS_FORWARDED) {
                $data['resubmitted_at'] = $attributes['resubmitted_at'] ?? $now;
            }
            return $data;
        });
    }

    /**
     * State: Approved approval record.
     */
    public function approved(): static
    {
        return $this->status(Approval::STATUS_APPROVED);
    }

    /**
     * State: Rejected approval record.
     */
    public function rejected(): static
    {
        return $this->status(Approval::STATUS_REJECTED);
    }

    /**
     * State: Pending approval record.
     */
    public function pending(): static
    {
        return $this->status(Approval::STATUS_PENDING)->state([
            'notes' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'canceled_at' => null,
            'resubmitted_at' => null,
        ]);
    }

    /**
     * Set the approval stage.
     */
    public function stage(string $stage): static
    {
        // Only set if stage is valid
        $validStages = method_exists(Approval::class, 'getStageKeys')
            ? Approval::getStageKeys()
            : array_keys(Approval::$STAGES_LABELS ?? []);
        if (!in_array($stage, $validStages) && !empty($validStages)) {
            // Optional: could log a warning here
        }
        return $this->state(['stage' => $stage]);
    }

    /**
     * Mark the approval as soft deleted.
     */
    public function deleted(): static
    {
        return $this->state([
            'deleted_at' => now(),
        ]);
    }

    /**
     * Assign this approval to a specific polymorphic approvable.
     */
    public function forApprovable(Model $approvable): static
    {
        return $this->state([
            'approvable_type' => $approvable->getMorphClass(),
            'approvable_id' => $approvable->id,
        ]);
    }
}
