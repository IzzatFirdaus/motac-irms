<?php

namespace Database\Factories;

use App\Models\HelpdeskCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HelpdeskCategoryFactory extends Factory
{
    protected $model = HelpdeskCategory::class;

    public function definition(): array
    {
        $msFaker = \Faker\Factory::create('ms_MY');
        $auditUserId = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'name' => $msFaker->unique()->randomElement(['Perkakasan', 'Perisian', 'Rangkaian', 'Akaun Pengguna', 'E-mel', 'Pencetak', 'Lain-lain']),
            'description' => $msFaker->optional(0.7)->sentence,
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
        return $this->state(['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
