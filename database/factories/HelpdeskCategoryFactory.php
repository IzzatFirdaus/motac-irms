<?php

namespace Database\Factories;

use App\Models\HelpdeskCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for HelpdeskCategory model.
 *
 * - Uses static caches for related User IDs to minimize repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - Foreign keys can be set via state; otherwise chosen randomly from existing records.
 * - Use with seeder that ensures users exist before creating helpdesk categories.
 */
class HelpdeskCategoryFactory extends Factory
{
    protected $model = HelpdeskCategory::class;

    public function definition(): array
    {
        // Static cache for User IDs (for blameable columns)
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) {
            $newUser = User::factory()->create();
            $userIds = [$newUser->id];
        }
        $auditUserId = Arr::random($userIds);

        // Use static Malaysian faker for realism and speed
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        // Common helpdesk categories for more realistic/focused seeding
        $defaultCategories = [
            'Hardware',
            'Software',
            'Network',
            'Account & Access',
            'Printer',
            'Email',
            'System Performance',
            'Other'
        ];

        // Ensure unique category name per test run, even if default list is exhausted
        static $usedNames = [];
        $name = null;
        $available = array_diff($defaultCategories, $usedNames);
        if (!empty($available)) {
            $name = $this->faker->randomElement($available);
        } else {
            // If all default names are used, generate a unique name
            do {
                $name = $this->faker->unique()->word . '_' . $this->faker->unique()->randomNumber(5);
            } while (in_array($name, $usedNames));
        }
        $usedNames[] = $name;

        return [
            'name'        => $name,
            'description' => $msFaker->optional(0.6)->sentence(10),
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
