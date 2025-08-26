<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for Grade (Gred Perkhidmatan).
 *
 * - Uses static cache for User IDs to minimize repeated queries.
 * - Does NOT create related models in definition().
 * - All foreign keys should be provided via state or will be randomly selected from existing records.
 * - Suitable for batch seeding with minimal DB overhead.
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        // Static cache for User IDs (for blameable columns)
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $auditUserId = ! empty($userIds) ? Arr::random($userIds) : null;

        // Static Malaysian faker for realism and speed
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Generate a unique numeric level (1-70 typical for Malaysian civil service)
        $level = $this->faker->unique()->numberBetween(1, 70);

        // Name generation based on level, similar to seeder logic
        if ($level >= 70) {
            $name = 'Menteri (Ujian)';
        } elseif ($level >= 68) {
            $name = 'Timbalan Menteri (Ujian)';
        } elseif ($level >= 66) {
            $name = 'TURUS I ('.$level.')';
        } elseif ($level >= 64) {
            $name = 'TURUS II ('.$level.')';
        } elseif ($level >= 62) {
            $name = 'TURUS III ('.$level.')';
        } elseif ($level >= 60) {
            $name = 'JUSA A ('.$level.')';
        } elseif ($level >= 58) {
            $name = 'JUSA B ('.$level.')';
        } elseif ($level >= 56) {
            $name = 'JUSA C ('.$level.')';
        } elseif ($level >= 41) {
            // P&P grades like N41, F41, etc.
            $prefix = $this->faker->randomElement(['N', 'F', 'M', 'E', 'W', 'S', 'J', 'DG']);
            $name   = $prefix.$level;
        } else {
            // Support staff grades like N19, FT19, etc.
            $prefix = $this->faker->randomElement(['N', 'FT', 'W', 'H', 'KP', 'U', 'C']);
            $name   = $prefix.$level;
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-5 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft-deleted for tests
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name'                  => $name,
            'level'                 => $level,
            'min_approval_grade_id' => null, // Set via policy/seeder logic if needed
            'is_approver_grade'     => $level >= 41, // Typical threshold for approval (P&P and above)
            'description'           => $msFaker->optional(0.5)->sentence(),
            'service_scheme'        => $this->faker->optional(0.5)->randomElement(['Perkhidmatan Awam', 'Perkhidmatan Pendidikan', 'Perkhidmatan Sokongan']),
            'created_by'            => $auditUserId,
            'updated_by'            => $auditUserId,
            'deleted_by'            => $isDeleted ? $auditUserId : null,
            'created_at'            => $createdAt,
            'updated_at'            => $updatedAt,
            'deleted_at'            => $deletedAt,
        ];
    }

    /**
     * State for grades that are approver grades (level >= 41).
     */
    public function approverGrade(): static
    {
        return $this->state(function (array $attributes): array {
            $level  = $this->faker->unique()->numberBetween(41, 70);
            $prefix = $this->faker->randomElement(['N', 'F', 'M', 'E', 'W', 'S', 'J', 'DG']);

            return [
                'level'             => $level,
                'name'              => $prefix.$level,
                'is_approver_grade' => true,
            ];
        });
    }

    /**
     * State for grades that are NOT approver grades (level < 41).
     */
    public function nonApproverGrade(): static
    {
        return $this->state(function (array $attributes): array {
            $level  = $this->faker->unique()->numberBetween(1, 40);
            $prefix = $this->faker->randomElement(['N', 'FT', 'W', 'H', 'KP', 'U', 'C']);

            return [
                'level'             => $level,
                'name'              => $prefix.$level,
                'is_approver_grade' => false,
            ];
        });
    }

    /**
     * State for soft-deleted grade.
     */
    public function deleted(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = ! empty($userIds) ? Arr::random($userIds) : null;

        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
