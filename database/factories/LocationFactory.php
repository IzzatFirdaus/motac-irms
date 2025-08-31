<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for the Location model.
 *
 * - Uses static caches for related User IDs to reduce repeated DB queries.
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with seeder that ensures users exist before creating locations.
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        // Static cache for User IDs (for blameable columns)
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick random user IDs or null if none exist
        $auditUserId = ! empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for address realism and speed
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            // Example: 'Aras 3, Sayap Kanan, Menara A'
            'name' => 'Aras '.$this->faker->numberBetween(1, 20).', '.
                $this->faker->randomElement(['Sayap Kiri', 'Sayap Kanan', 'Menara', 'Blok', 'Pejabat', 'Bilik', 'Unit']),
            'description' => $msFaker->optional(0.7)->sentence(10),
            'address'     => $msFaker->optional(0.8)->streetAddress,
            'city'        => $msFaker->optional(0.8)->city,
            'state'       => $msFaker->optional(0.8)->state,
            'country'     => 'Malaysia',
            'postal_code' => $msFaker->optional(0.8)->postcode,
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
     * State: Active location.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State: Inactive location.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State: Soft-deleted location.
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
