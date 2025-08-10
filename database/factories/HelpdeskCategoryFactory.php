<?php

namespace Database\Factories;

use App\Models\HelpdeskCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the HelpdeskCategory model.
 *
 * Generates categories for the helpdesk system, handling all fields including
 * audit columns and soft-delete fields as per migration/model.
 */
class HelpdeskCategoryFactory extends Factory
{
    protected $model = HelpdeskCategory::class;

    public function definition(): array
    {
        // Use Malaysian locale for sample data realism
        $msFaker = \Faker\Factory::create('ms_MY');

        // Example categories as per typical helpdesk systems
        $categoryNames = [
            'Hardware',
            'Software',
            'Network',
            'Account & Access',
            'Printer',
            'Email',
            'System Performance',
            'Other',
        ];
        $name = $this->faker->unique()->randomElement($categoryNames);

        // Audit user for blameable columns
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (HelpdeskCategoryFactory)'])->id;

        // Timestamps for creation/update/soft-delete
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => $name,
            'description' => $msFaker->optional(0.5)->sentence(10),
            'is_active'   => $this->faker->boolean(90),
            // Blameable columns
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
            'deleted_by'  => $isDeleted ? $auditUserId : null,
            'created_at'  => $createdAt,
            'updated_at'  => $updatedAt,
            'deleted_at'  => $deletedAt,
        ];
    }

    /**
     * State for an active category.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State for an inactive category.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State for a soft-deleted category.
     */
    public function deleted(): static
    {
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (HelpdeskCategoryFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
