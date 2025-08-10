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
 * relationships, audit columns, and soft-delete fields as per migration/model/seeder.
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
            'Aduan ICT',
            'Aduan Peralatan',
            'Aduan Sistem',
            'Permintaan Umum',
            'Permintaan Akses',
            'Lain-lain'
        ];
        $name = $this->faker->unique()->randomElement($categoryNames);

        // Generate a slug from the category name
        $slug = \Str::slug($name);

        // Optionally generate a parent category (null for top-level)
        $parentId = null;
        if ($this->faker->boolean(20)) { // 20% chance this is a sub-category
            $existingParent = HelpdeskCategory::inRandomOrder()->whereNull('parent_id')->first();
            if ($existingParent) {
                $parentId = $existingParent->id;
            }
        }

        // Audit user
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (HelpdeskCategoryFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $msFaker->optional(0.5)->sentence(10),
            'parent_id'   => $parentId,
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

    /**
     * Set as a sub-category for a given parent category.
     */
    public function forParent(HelpdeskCategory|int $parent): static
    {
        return $this->state([
            'parent_id' => $parent instanceof HelpdeskCategory ? $parent->id : $parent,
        ]);
    }
}
