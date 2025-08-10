<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for EquipmentCategory.
 *
 * Generates fake categories for ICT equipment, ensuring all required fields
 * (including blameable/audit fields) are present and values are valid according to migration/model.
 */
class EquipmentCategoryFactory extends Factory
{
    protected $model = EquipmentCategory::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create an audit user for created_by/updated_by fields
        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (EqCatFactory)']);
        $auditUserId = $auditUser->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            // Category name in Malay, 2-4 words with suffix
            'name' => $msFaker->unique()->words(mt_rand(2, 4), true).' Kategori',
            'description' => $msFaker->optional(0.8)->sentence,
            'is_active' => $this->faker->boolean(90),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

    /**
     * Mark the category as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    /**
     * Mark the category as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    /**
     * Mark the category as soft deleted.
     */
    public function deleted(): static
    {
        $deleter = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Deleter User Fallback (EqCatFactory)']);

        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'deleted_by' => $attributes['deleted_by'] ?? $deleter->id,
        ]);
    }
}
