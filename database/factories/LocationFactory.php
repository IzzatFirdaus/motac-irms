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
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (LocFactory)']);
        $auditUserId = $auditUser->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'name' => 'Aras '.$this->faker->numberBetween(1, 18).', '.$this->faker->randomElement(['Sayap Kiri', 'Sayap Kanan', 'Menara']),
            'description' => $msFaker->optional(0.7)->sentence(10),
            'address' => $msFaker->optional(0.8)->streetAddress,
            'city' => $msFaker->optional(0.8)->city,
            'state' => $msFaker->optional(0.8)->state,
            'country' => 'Malaysia',
            'postal_code' => $msFaker->optional(0.8)->postcode,
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
