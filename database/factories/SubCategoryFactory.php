<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for SubCategory model.
 *
 * - Uses static caches to minimize DB queries for related foreign keys (EquipmentCategory, User).
 * - Does NOT create related models in definition() (all related records must exist before using this factory in seeders).
 * - All foreign keys can be provided via state; otherwise, will pick randomly from existing records.
 * - Use with seeders that ensure equipment categories and users exist.
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        // Static cache for EquipmentCategory IDs
        static $categoryIds;
        if (!isset($categoryIds)) {
            $categoryIds = EquipmentCategory::pluck('id')->all();
        }

        // Static cache for User IDs
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick random related IDs or null if none exist
        $equipmentCategoryId = !empty($categoryIds) ? Arr::random($categoryIds) : null;
        $auditUserId = !empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for realism and speed
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(1); // ~1% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'equipment_category_id' => $equipmentCategoryId,
            'name' => $msFaker->unique()->words(2, true) . ' Sub-Kategori',
            'description' => $msFaker->optional(0.7)->sentence,
            'is_active' => $this->faker->boolean(95),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * State: Mark sub-category as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes): array => ['is_active' => false]);
    }

    /**
     * State: Mark sub-category as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = !empty($userIds) ? Arr::random($userIds) : null;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }

    /**
     * State: Assign a specific equipment category for the sub-category.
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
