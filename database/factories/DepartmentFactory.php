<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $auditUserId = User::inRandomOrder()->first()?->id;

        $name = $this->faker->randomElement(['Jabatan', 'Bahagian', 'Unit']).' '.$this->faker->unique()->word();
        $code = Str::upper($this->faker->unique()->bothify('???###'));

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-5 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        $isDeleted = $this->faker->boolean(1);
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name' => $name,
            'description' => $msFaker->optional(0.7)->sentence(10, true),
            'branch_type' => $this->faker->randomElement([
                Department::BRANCH_TYPE_HQ,
                Department::BRANCH_TYPE_STATE,
            ]),
            'code' => $code,
            'is_active' => $this->faker->boolean(95),
            'head_of_department_id' => $this->faker->optional(0.3)->passthrough(User::inRandomOrder()->first()?->id),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    public function hq(): static
    {
        return $this->state(
            fn (array $attributes): array => [
                'branch_type' => Department::BRANCH_TYPE_HQ,
            ]
        );
    }

    public function stateBranch(): static
    {
        return $this->state(
            fn (array $attributes): array => [
                'branch_type' => Department::BRANCH_TYPE_STATE,
            ]
        );
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    public function withHeadOfDepartment(User|int $user): static
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->state(fn (array $attributes): array => ['head_of_department_id' => $userId]);
    }
}
