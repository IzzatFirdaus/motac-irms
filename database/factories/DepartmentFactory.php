<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DepartmentFactory extends Factory
{
  protected $model = Department::class;

  public function definition(): array
  {
    $auditUserId = null;
    if (User::count() > 0) {
        $auditUserId = User::inRandomOrder()->first()?->id;
    } elseif (class_exists(User::class) && method_exists(User::class, 'factory')) {
        try {
            $auditUserInstance = User::factory()->make([
                'name' => 'Audit User (DeptFactory-' . $this->faker->unique()->word . ')',
                'email' => $this->faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'department_id' => null,
                'position_id' => null,
                'grade_id' => null,
            ]);
            // $auditUserInstance->save(); // Only save if DB persistence is strictly needed for the ID here
            // $auditUserId = $auditUserInstance->id;
        } catch (\Exception $e) {
            Log::error('DepartmentFactory: Could not create/make fallback audit user: ' . $e->getMessage());
        }
    }

    $name = $this->faker->unique()->company() . ' Department';
    $code = Str::upper($this->faker->unique()->bothify('???###'));

    $createdAt = Carbon::parse($this->faker->dateTimeBetween('-5 years', 'now'));
    $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt->toDateTimeString(), 'now'));

    $isDeleted = $this->faker->boolean(1); // Minimal chance for general factory
    $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt->toDateTimeString(), 'now')) : null;
    // $deleterId = $isDeleted ? (User::inRandomOrder()->first()?->id ?? $auditUserId) : null; // Blameable observer handles deleted_by

    return [
      'name' => $name,
      'description' => $this->faker->optional(0.7)->sentence(10, true),
      'branch_type' => $this->faker->randomElement([
          Department::BRANCH_TYPE_HQ,
          Department::BRANCH_TYPE_STATE
      ]),
      'code' => $code,
      'is_active' => $this->faker->boolean(95),
      'head_of_department_id' => $this->faker->optional(0.3)->passthrough(User::inRandomOrder()->first()?->id),
      // 'created_by', 'updated_by', 'deleted_by' are handled by BlameableObserver
      'created_at' => $createdAt,
      'updated_at' => $updatedAt,
      'deleted_at' => $deletedAt,
    ];
  }

  public function hq(): static
  {
    return $this->state(
      fn(array $attributes) => [
        'branch_type' => Department::BRANCH_TYPE_HQ,
      ]
    );
  }

  public function stateBranch(): static
  {
    return $this->state(
      fn(array $attributes) => [
        'branch_type' => Department::BRANCH_TYPE_STATE,
      ]
    );
  }

  public function active(): static
  {
    return $this->state(fn(array $attributes) => ['is_active' => true]);
  }

  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => ['is_active' => false]);
  }

  public function withHeadOfDepartment(User|int $user): static
  {
    $userId = $user instanceof User ? $user->id : $user;
    return $this->state(fn(array $attributes) => ['head_of_department_id' => $userId]);
  }
}
