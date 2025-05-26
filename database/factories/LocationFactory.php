<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (LocFactory)']);
        $auditUserId = $auditUser->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'name' => $this->faker->unique()->company.' '.$this->faker->randomElement(['Office', 'Warehouse', 'Site', 'Branch', 'Storage Unit']),
            'description' => $this->faker->optional(0.7)->sentence(10),
            'address' => $this->faker->optional(0.8)->streetAddress, // Added
            'city' => $this->faker->optional(0.8)->city,           // Added
            'state' => $this->faker->optional(0.8)->state,          // Added
            'country' => $this->faker->optional(0.8)->country,      // Added
            'postal_code' => $this->faker->optional(0.8)->postcode,   // Added
            'is_active' => $this->faker->boolean(90),               // Added
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
        return $this->state(fn (array $attributes) => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    public function deleted(): static
    {
        $deleter = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Deleter User Fallback (LocFactory)']);

        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
            'deleted_by' => $attributes['deleted_by'] ?? $deleter->id,
        ]);
    }
}
