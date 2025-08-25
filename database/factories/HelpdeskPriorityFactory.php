<?php

namespace Database\Factories;

use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for HelpdeskPriority model.
 *
 * - Uses static caches for User IDs to reduce repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - Foreign key fields can be set via state; otherwise, selected randomly from existing users.
 * - For use with seeders that ensure users exist before priorities are created.
 */
class HelpdeskPriorityFactory extends Factory
{
    protected $model = HelpdeskPriority::class;

    public function definition(): array
    {
        // Always ensure at least one user exists for blameable columns
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) {
            $newUser = User::factory()->create();
            $userIds = [$newUser->id];
        }
        $auditUserId = Arr::random($userIds);

        // Typical helpdesk priorities for consistency in testing
        $priorityPresets = [
            ['name' => 'Low',      'level' => 10, 'color_code' => '#28a745'], // Green
            ['name' => 'Medium',   'level' => 20, 'color_code' => '#007bff'], // Blue
            ['name' => 'High',     'level' => 30, 'color_code' => '#ffc107'], // Yellow/Orange
            ['name' => 'Critical', 'level' => 40, 'color_code' => '#dc3545'], // Red
        ];
        static $usedNames = [];
        $priority = null;
        $available = array_filter($priorityPresets, function($preset) use ($usedNames) {
            return !in_array($preset['name'], $usedNames);
        });
        if (!empty($available)) {
            $priority = $this->faker->randomElement($available);
        } else {
            // If all default names are used, generate a unique name
            $priority = [
                'name' => $this->faker->unique()->word . '_' . $this->faker->unique()->randomNumber(5),
                'level' => $this->faker->unique()->numberBetween(41, 100),
                'color_code' => $this->faker->hexColor,
            ];
        }
        $usedNames[] = $priority['name'];

        // Use a static Malaysian faker for realism and speed
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => $priority['name'],
            'level'       => $priority['level'],
            'color_code'  => $priority['color_code'],
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
            'deleted_by'  => $isDeleted ? $auditUserId : null,
            'created_at'  => $createdAt,
            'updated_at'  => $updatedAt,
            'deleted_at'  => $deletedAt,
        ];
    }

    /**
     * State: Mark the priority as soft deleted.
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
}
