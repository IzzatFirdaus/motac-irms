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
 */
class HelpdeskPriorityFactory extends Factory
{
    protected $model = HelpdeskPriority::class;

    public function definition(): array
    {
        // Typical helpdesk priorities, in order of urgency
        $priorityNames = [
            ['name' => 'Tinggi',     'level' => 1, 'color' => '#e03131', 'description' => 'Aduan kritikal, tindakan segera diperlukan.'],
            ['name' => 'Sederhana',  'level' => 2, 'color' => '#f59f00', 'description' => 'Aduan memerlukan tindakan dalam masa terdekat.'],
            ['name' => 'Rendah',     'level' => 3, 'color' => '#40c057', 'description' => 'Aduan bukan kritikal, tindakan boleh ditangguhkan.'],
        ];

        $priority = $this->faker->randomElement($priorityNames);

        // Audit user for blameable columns
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (HelpdeskPriorityFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => $priority['name'],
            'level'       => $priority['level'],
            'color'       => $priority['color'],
            'description' => $priority['description'],
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
     * State for an active priority.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State for an inactive priority.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
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
