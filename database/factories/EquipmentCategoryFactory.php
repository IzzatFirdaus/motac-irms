<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for EquipmentCategory.
 *
 * - Uses static caches for related User IDs to reduce repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with seeder that ensures users exist before creating equipment categories.
 */
class EquipmentCategoryFactory extends Factory
{
    protected $model = EquipmentCategory::class;

    public function definition(): array
    {
        // Static cache for User IDs (for blameable columns)
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick random user IDs or null if none exist
        $auditUserId = ! empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for realism and speed
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        // Use a pool of common ICT equipment category names for realism
        $categoryNames = [
            'Komputer Riba', 'Projektor LCD', 'Pencetak', 'Peralatan Rangkaian',
            'Peranti Input/Output', 'Storan Mudah Alih', 'Komputer Meja', 'Peralatan ICT Lain',
        ];

        return [
            'name'        => $this->faker->unique()->randomElement($categoryNames).' '.$this->faker->unique()->word(),
            'description' => $msFaker->optional(0.8)->sentence(),
            'is_active'   => $this->faker->boolean(90),
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
            'deleted_by'  => $isDeleted ? $auditUserId : null,
            'created_at'  => $createdAt,
            'updated_at'  => $updatedAt,
            'deleted_at'  => $deletedAt,
        ];
    }

    /**
     * State: Mark the category as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    /**
     * State: Mark the category as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    /**
     * State: Mark the category as soft deleted.
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
}
