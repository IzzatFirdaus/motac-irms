<?php

namespace Database\Factories;

use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the HelpdeskPriority model.
 *
 * Generates helpdesk priority entries for testing and seeding,
 * including all required fields, blameable/audit columns, and soft-delete columns.
 *
 * NOTE: This factory is fully aligned with the HelpdeskPriority model and migration:
 * - Uses 'color_code' (not 'color') for the color field.
 * - Does not generate 'description' or 'is_active', which are not present in schema.
 */
class HelpdeskPriorityFactory extends Factory
{
    protected $model = HelpdeskPriority::class;

    public function definition(): array
    {
        // Typical helpdesk priorities, in order of urgency
        $priorityPresets = [
            ['name' => 'Tinggi',     'level' => 1, 'color_code' => '#e03131'],
            ['name' => 'Sederhana',  'level' => 2, 'color_code' => '#f59f00'],
            ['name' => 'Rendah',     'level' => 3, 'color_code' => '#40c057'],
        ];

        $priority = $this->faker->randomElement($priorityPresets);

        // Find or create a user for blameable columns
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (HelpdeskPriorityFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => $priority['name'],
            'level'       => $priority['level'],
            'color_code'  => $priority['color_code'],
            // Blameable/audit fields
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
            'deleted_by'  => $isDeleted ? $auditUserId : null,
            'created_at'  => $createdAt,
            'updated_at'  => $updatedAt,
            'deleted_at'  => $deletedAt,
        ];
    }

    /**
     * State for an active priority.
     * (No 'is_active' field in schema, so this is a NO-OP but included for compatibility.)
     */
    public function active(): static
    {
        return $this;
    }

    /**
     * State for an inactive priority.
     * (No 'is_active' field in schema, so this is a NO-OP but included for compatibility.)
     */
    public function inactive(): static
    {
        return $this;
    }

    /**
     * State for a soft-deleted priority.
     */
    public function deleted(): static
    {
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (HelpdeskPriorityFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
