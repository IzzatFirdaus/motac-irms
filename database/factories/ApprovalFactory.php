<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for Approval model.
 *
 * - Uses static caches for related User IDs to reduce repeated DB queries.
 * - Never creates related models in definition(); assumes required records exist.
 * - Accepts officer_id and other foreign keys via state for batch seeding efficiency.
 * - Uses static cached Faker for performance and realistic Malay data.
 * - All date fields, status, and stage are assigned with correct logic.
 *
 * Usage:
 *   - Ensure at least one User exists before using this factory.
 *   - Use state() to provide approvable_type/id and officer_id for best batch performance.
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        // Static cache for User IDs to avoid repeated DB queries
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $officerId = ! empty($userIds) ? Arr::random($userIds) : null;

        // Static faker for Malay locale
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Get valid stage and status keys
        $stageKeys = method_exists(Approval::class, 'getStageKeys')
            ? Approval::getStageKeys()
            : array_keys(Approval::$STAGES_LABELS ?? [Approval::STAGE_SUPPORT_REVIEW => 'Support Review']);
        $stage = $this->faker->randomElement($stageKeys ?: [Approval::STAGE_SUPPORT_REVIEW]);

        $statusKeys = method_exists(Approval::class, 'getStatusKeys')
            ? Approval::getStatusKeys()
            : array_keys(Approval::$STATUSES_LABELS ?? [
                Approval::STATUS_PENDING   => 'Pending',
                Approval::STATUS_APPROVED  => 'Approved',
                Approval::STATUS_REJECTED  => 'Rejected',
                Approval::STATUS_CANCELED  => 'Canceled',
                Approval::STATUS_FORWARDED => 'Forwarded',
            ]);
        $status = $this->faker->randomElement($statusKeys);

        // Generate relevant timestamps
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        // Decision timestamps (nullable, set according to status)
        $approvedAt    = $status === Approval::STATUS_APPROVED ? Carbon::parse($this->faker->dateTimeBetween($createdAt, $updatedAt)) : null;
        $rejectedAt    = $status === Approval::STATUS_REJECTED ? Carbon::parse($this->faker->dateTimeBetween($createdAt, $updatedAt)) : null;
        $canceledAt    = $status === Approval::STATUS_CANCELED ? Carbon::parse($this->faker->dateTimeBetween($createdAt, $updatedAt)) : null;
        $resubmittedAt = $status === Approval::STATUS_FORWARDED ? Carbon::parse($this->faker->dateTimeBetween($createdAt, $updatedAt)) : null;

        return [
            'approvable_type' => null,
            'approvable_id'   => null,
            'officer_id'      => $officerId,
            'stage'           => $stage,
            'status'          => $status,
            'notes'           => $msFaker->optional()->sentence(),
            'approved_at'     => $approvedAt,
            'rejected_at'     => $rejectedAt,
            'canceled_at'     => $canceledAt,
            'resubmitted_at'  => $resubmittedAt,
            'created_by'      => $officerId,
            'updated_by'      => $officerId,
            'deleted_by'      => null,
            'created_at'      => $createdAt,
            'updated_at'      => $updatedAt,
            'deleted_at'      => null,
        ];
    }

    /**
     * Assign a specific status and related timestamps/notes.
     */
    public function status(string $statusValue): static
    {
        return $this->state(function (array $attributes) use ($statusValue): array {
            $now = now();
            static $msFaker;
            if (! $msFaker) {
                $msFaker = \Faker\Factory::create('ms_MY');
            }
            $data = [
                'status' => $statusValue,
            ];
            // Set timestamp/notes based on status
            if ($statusValue === Approval::STATUS_APPROVED) {
                $data['approved_at'] = $attributes['approved_at'] ?? $now;
                $data['notes']       = $attributes['notes']       ?? $msFaker->optional()->sentence();
            } elseif ($statusValue === Approval::STATUS_REJECTED) {
                $data['rejected_at'] = $attributes['rejected_at'] ?? $now;
                $data['notes']       = $attributes['notes']       ?? $msFaker->sentence();
            } elseif ($statusValue === Approval::STATUS_CANCELED) {
                $data['canceled_at'] = $attributes['canceled_at'] ?? $now;
                $data['notes']       = $attributes['notes']       ?? $msFaker->optional()->sentence();
            } elseif ($statusValue === Approval::STATUS_FORWARDED) {
                $data['resubmitted_at'] = $attributes['resubmitted_at'] ?? $now;
                $data['notes']          = $attributes['notes']          ?? $msFaker->optional()->sentence();
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
            'notes'          => null,
            'approved_at'    => null,
            'rejected_at'    => null,
            'canceled_at'    => null,
            'resubmitted_at' => null,
        ]);
    }

    /**
     * State: Canceled approval record.
     */
    public function canceled(): static
    {
        return $this->status(Approval::STATUS_CANCELED);
    }

    /**
     * State: Forwarded approval record.
     */
    public function forwarded(): static
    {
        return $this->status(Approval::STATUS_FORWARDED);
    }

    /**
     * State: Set the approval stage.
     */
    public function stage(string $stage): static
    {
        $validStages = method_exists(Approval::class, 'getStageKeys')
            ? Approval::getStageKeys()
            : array_keys(Approval::$STAGES_LABELS ?? []);
        if (! in_array($stage, $validStages) && ! empty($validStages)) {
            // Optionally log a warning here
        }

        return $this->state(['stage' => $stage]);
    }

    /**
     * State: Mark the approval as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = ! empty($userIds) ? Arr::random($userIds) : null;

        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }

    /**
     * State: Assign this approval to a specific polymorphic approvable.
     */
    public function forApprovable(Model $approvable): static
    {
        return $this->state([
            'approvable_type' => $approvable->getMorphClass(),
            'approvable_id'   => $approvable->id,
        ]);
    }
}
