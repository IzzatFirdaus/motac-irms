<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $equipmentCategoryId = EquipmentCategory::inRandomOrder()->first()?->id;
        if (! $equipmentCategoryId && class_exists(EquipmentCategory::class) && method_exists(EquipmentCategory::class, 'factory')) {
            $equipmentCategoryId = EquipmentCategory::factory()->create()->id;
        }

        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $deletedAt = $this->faker->optional(0.1)->dateTimeBetween($createdAt, 'now');

        return [
            'equipment_category_id' => $equipmentCategoryId,
            'name' => $msFaker->unique()->words(2, true).' Sub-Kategori',
            'description' => $msFaker->optional(0.7)->sentence,
            'is_active' => $this->faker->boolean(95),
            'created_at' => Carbon::parse($createdAt),
            'updated_at' => Carbon::parse($updatedAt),
            'deleted_at' => $deletedAt ? Carbon::parse($deletedAt) : null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(
            fn (array $attributes): array => [
                'is_active' => false,
            ]
        );
    }

    public function trashed(): static
    {
        $deleterId = User::inRandomOrder()->first()?->id;
        if (! $deleterId && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $deleterId = User::factory()->create()->id;
        }

        return $this->state(
            fn (array $attributes): array => [
                'deleted_at' => Carbon::now(),
                'deleted_by' => $deleterId,
            ]
        );
    }

    public function forEquipmentCategory(EquipmentCategory|int $equipmentCategory): static
    {
        $equipmentCategoryId = $equipmentCategory instanceof EquipmentCategory
            ? $equipmentCategory->id
            : $equipmentCategory;

        return $this->state(
            fn (array $attributes): array => [
                'equipment_category_id' => $equipmentCategoryId,
            ]
        );
    }
}
