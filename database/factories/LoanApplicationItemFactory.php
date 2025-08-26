<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for LoanApplicationItem model.
 *
 * - Uses static caches for related IDs to reduce DB queries on batch seeding.
 * - NEVER creates related models in definition().
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with a seeder that ensures all referenced models exist before creating items.
 */
class LoanApplicationItemFactory extends Factory
{
    protected $model = LoanApplicationItem::class;

    public function definition(): array
    {
        // Static cache for LoanApplication IDs
        static $loanApplicationIds;
        if (! isset($loanApplicationIds)) {
            $loanApplicationIds = LoanApplication::pluck('id')->all();
        }
        $loanApplicationId = $loanApplicationIds ? Arr::random($loanApplicationIds) : null;

        // Static cache for User IDs for audit fields
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $auditUserId = $userIds ? Arr::random($userIds) : null;

        // Static cache for available equipment types (asset_type column)
        static $assetTypes;
        if (! isset($assetTypes)) {
            $assetTypes = Equipment::query()->distinct()->pluck('asset_type')->all();
        }
        $equipmentType = ! empty($assetTypes)
            ? Arr::random($assetTypes)
            : $this->faker->randomElement(['laptop', 'projector', 'printer', 'monitor', 'tablet', 'desktop']);

        // Quantity logic
        $quantityRequested = $this->faker->numberBetween(1, 5);
        $quantityApproved  = $this->faker->numberBetween(0, $quantityRequested); // Sometimes not fully approved

        // Timestamp logic
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        // Use a static Malaysian faker for notes
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        return [
            'loan_application_id' => $loanApplicationId,
            'equipment_type'      => $equipmentType,
            'quantity_requested'  => $quantityRequested,
            'quantity_approved'   => $quantityApproved,
            'notes'               => $msFaker->optional(0.15)->sentence(8),
            'status'              => LoanApplicationItem::STATUS_PENDING_APPROVAL,
            'quantity_issued'     => 0,
            'quantity_returned'   => 0,
            'created_by'          => $auditUserId,
            'updated_by'          => $auditUserId,
            'deleted_by'          => $isDeleted ? $auditUserId : null,
            'created_at'          => $createdAt,
            'updated_at'          => $updatedAt,
            'deleted_at'          => $deletedAt,
        ];
    }

    /**
     * State: All requested items approved.
     */
    public function fullyApproved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity_approved' => $attributes['quantity_requested'] ?? 1,
            ];
        });
    }

    /**
     * State: No items approved.
     */
    public function notApproved(): static
    {
        return $this->state([
            'quantity_approved' => 0,
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

    /**
     * Mark this item as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = $userIds ? Arr::random($userIds) : null;

        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
