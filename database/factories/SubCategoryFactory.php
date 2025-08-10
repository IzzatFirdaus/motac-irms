<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for SubCategory model.
 *
 * Generates sub-categories for ICT equipment, ensuring foreign key relationships,
 * audit fields, and realistic data. Matches migration, seeder, and model structure.
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Get or create an EquipmentCategory for FK
        $equipmentCategoryId = EquipmentCategory::inRandomOrder()->value('id');
        if (!$equipmentCategoryId) {
            // Ensure at least one EquipmentCategory exists
            $equipmentCategoryId = EquipmentCategory::factory()->create()->id;
        }

        // Get or create a user for blameable fields
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (SubCategoryFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'equipment_category_id' => $equipmentCategoryId,
            'name' => $msFaker->unique()->words(2, true).' Sub-Kategori',
            'description' => $msFaker->optional(0.7)->sentence,
            'is_active' => $this->faker->boolean(95),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => $isDeleted ? $auditUserId : null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * Mark sub-category as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * Mark sub-category as soft deleted.
     */
    public function deleted(): static
    {
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (SubCategoryFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $auditUserId,
        ]);
    }

    /**
     * Set a specific equipment category for the sub-category.
     */
    public function forEquipmentCategory(EquipmentCategory|int $equipmentCategory): static
    {
        $equipmentCategoryId = $equipmentCategory instanceof EquipmentCategory
            ? $equipmentCategory->id
            : $equipmentCategory;

        return $this->state([
            'equipment_category_id' => $equipmentCategoryId,
        ]);
    }
}
