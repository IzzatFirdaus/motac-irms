<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EquipmentCategoryFactory extends Factory
{
    protected $model = EquipmentCategory::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (EqCatFactory)']);
        $auditUserId = $auditUser->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            // Generate category name in Malay
            'name' => $msFaker->unique()->words(mt_rand(2, 4), true).' Kumpulan Kategori',
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

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    public function noDescription(): static
    {
        return $this->state(fn (array $attributes): array => ['description' => null]);
    }

    public function deleted(): static
    {
        $deleter = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Deleter User Fallback (EqCatFactory)']);

        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'deleted_by' => $attributes['deleted_by'] ?? $deleter->id,
        ]);
    }
}
