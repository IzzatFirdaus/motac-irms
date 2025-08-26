<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for Department model.
 *
 * - Uses static cache for User IDs to minimize repeated DB queries.
 * - Never creates related models in definition(); all related records must exist.
 * - All foreign keys can be provided via state; otherwise, picked randomly from existing records.
 * - Use with a seeder that ensures users exist before creating departments.
 * - Includes fallback values for constants to prevent errors during early seeding.
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        // Static cache for User IDs (for blameable columns and HOD)
        static $userIds;
        if (! isset($userIds)) {
            // Cache all user IDs to avoid repeated DB queries during batch seeding
            $userIds = User::pluck('id')->all();
        }

        // Pick a random user ID or null if no users exist
        $auditUserId = ! empty($userIds) ? Arr::random($userIds) : null;
        // Optionally assign a head of department with 30% chance, or null
        $headOfDeptId = $this->faker->optional(0.3)->passthrough($auditUserId);

        // Use a static Malaysian faker for more realistic department names/descriptions
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Generate timestamps for created_at/updated_at (simulate older and newer records)
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-5 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        // 1% chance to be soft deleted
        $isDeleted = $this->faker->boolean(1);
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        // Safe access to Department constants with fallbacks
        $branchTypeHq    = defined('App\Models\Department::BRANCH_TYPE_HQ') ? Department::BRANCH_TYPE_HQ : 'headquarters';
        $branchTypeState = defined('App\Models\Department::BRANCH_TYPE_STATE') ? Department::BRANCH_TYPE_STATE : 'state';

        return [
            // Department name: randomly choose "Jabatan", "Bahagian", or "Unit" + a unique word
            'name'                  => $this->faker->randomElement(['Jabatan', 'Bahagian', 'Unit']).' '.$this->faker->unique()->word(),
            'description'           => $msFaker->optional(0.7)->sentence(10, true),
            'branch_type'           => $this->faker->randomElement([$branchTypeHq, $branchTypeState]),
            'code'                  => strtoupper($this->faker->unique()->bothify('???###')),
            'is_active'             => $this->faker->boolean(95),
            'head_of_department_id' => $headOfDeptId,
            'created_by'            => $auditUserId,
            'updated_by'            => $auditUserId,
            'deleted_by'            => null,
            'created_at'            => $createdAt,
            'updated_at'            => $updatedAt,
            'deleted_at'            => $deletedAt,
        ];
    }

    /**
     * State: Force headquarters branch type.
     */
    public function hq(): static
    {
        $branchTypeHq = defined('App\Models\Department::BRANCH_TYPE_HQ') ? Department::BRANCH_TYPE_HQ : 'headquarters';

        return $this->state(fn (array $attributes): array => [
            'branch_type' => $branchTypeHq,
        ]);
    }

    /**
     * State: Force state branch type.
     */
    public function stateBranch(): static
    {
        $branchTypeState = defined('App\Models\Department::BRANCH_TYPE_STATE') ? Department::BRANCH_TYPE_STATE : 'state';

        return $this->state(fn (array $attributes): array => [
            'branch_type' => $branchTypeState,
        ]);
    }

    /**
     * State: Mark department as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    /**
     * State: Mark department as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    /**
     * State: Assign a specific head of department.
     */
    public function withHeadOfDepartment(User|int $user): static
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->state(fn (array $attributes): array => ['head_of_department_id' => $userId]);
    }

    /**
     * State: Mark department as soft deleted.
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
