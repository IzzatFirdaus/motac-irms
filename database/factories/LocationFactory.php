<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the Location model.
 *
 * Generates realistic office/location entries for the system,
 * including all audit and soft-delete fields as in the migration and model.
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        // Use Malaysian locale for address realism
        $msFaker = \Faker\Factory::create('ms_MY');

        // Get or create a user for blameable columns
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (LocationFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'        => 'Aras '.$this->faker->numberBetween(1, 18).', '.$this->faker->randomElement(['Sayap Kiri', 'Sayap Kanan', 'Menara']),
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
     * State for active location.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State for inactive location.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State for soft-deleted location.
     */
    public function deleted(): static
    {
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (LocationFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
