<?php

namespace Database\Factories;

use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HelpdeskPriorityFactory extends Factory
{
    protected $model = HelpdeskPriority::class;

    public function definition(): array
    {
        $msFaker = \Faker\Factory::create('ms_MY');
        $auditUserId = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        $level = $this->faker->unique()->numberBetween(1, 10);
        $name = '';
        $colorCode = null;

        if ($level <= 3) {
            $name = $msFaker->randomElement(['Rendah', 'Sederhana']);
            $colorCode = $this->faker->randomElement(['#4CAF50', '#FFEB3B']); // Green, Yellow
        } elseif ($level <= 7) {
            $name = $msFaker->randomElement(['Tinggi', 'Mendesak']);
            $colorCode = $this->faker->randomElement(['#FF9800', '#F44336']); // Orange, Red
        } else {
            $name = 'Kritikal';
            $colorCode = '#B71C1C'; // Dark Red
        }


        return [
            'name' => $name,
            'level' => $level,
            'color_code' => $colorCode,
            'description' => $msFaker->optional(0.7)->sentence,
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

    public function low(): static
    {
        return $this->state(['name' => 'Rendah', 'level' => 1, 'color_code' => '#4CAF50']);
    }

    public function medium(): static
    {
        return $this->state(['name' => 'Sederhana', 'level' => 5, 'color_code' => '#FFEB3B']);
    }

    public function high(): static
    {
        return $this->state(['name' => 'Tinggi', 'level' => 8, 'color_code' => '#F44336']);
    }
}
