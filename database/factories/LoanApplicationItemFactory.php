<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for the LoanApplicationItem model.
 *
 * Generates records representing individual equipment requests within a loan application.
 * Handles all relationships, audit fields, and soft-delete columns as per migration/model.
 *
 * NOTE: Uses 'notes' field, matching the model and migration schema.
 */
class LoanApplicationItemFactory extends Factory
{
    protected $model = LoanApplicationItem::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic values
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create a loan application for this item
        $loanApplicationId = LoanApplication::inRandomOrder()->value('id') ?? LoanApplication::factory()->create()->id;

        // Pick a unique asset type from available equipment, or use a fallback
        $assetTypes = Equipment::query()->distinct()->pluck('asset_type')->all();
        $equipmentType = !empty($assetTypes)
            ? $this->faker->randomElement($assetTypes)
            : $this->faker->randomElement(['laptop', 'projector', 'printer', 'monitor', 'tablet', 'desktop']);

        // Quantity requested and approved
        $quantityRequested = $this->faker->numberBetween(1, 5);
        $quantityApproved = $this->faker->numberBetween(0, $quantityRequested); // Sometimes not fully approved

        // For audit columns (created_by, updated_by, etc.)
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (LoanApplicationItemFactory)'])->id;

        // Timestamps for creation/update/soft-delete
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? $this->faker->dateTimeBetween($updatedAt, 'now') : null;

        return [
            'loan_application_id' => $loanApplicationId,
            'equipment_type'      => $equipmentType,
            'quantity_requested'  => $quantityRequested,
            'quantity_approved'   => $quantityApproved,
            // Use 'notes' field, not 'item_notes'
            'notes'               => $msFaker->optional(0.15)->sentence(8),
            'status'              => LoanApplicationItem::STATUS_PENDING_APPROVAL,
            'quantity_issued'     => 0,
            'quantity_returned'   => 0,
            // Blameable columns
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
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (LoanApplicationItemFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
