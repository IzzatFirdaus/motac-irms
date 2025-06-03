<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PositionFactory extends EloquentFactory
{
    protected $model = Position::class;

    public function definition(): array
    {
        $auditUser = User::orderBy('id')->first();
        if (!$auditUser && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $auditUser = User::factory()->create(['name' => 'Default Audit User (PosFactory)']);
        }
        $auditUserId = $auditUser?->id;

        $gradeId = Grade::inRandomOrder()->value('id');
        if (!$gradeId && class_exists(Grade::class) && method_exists(Grade::class, 'factory')) {
            $defaultGrade = Grade::factory()->create([
                'name' => 'Default Grade (PosFactory-' . $this->faker->unique()->word() . ')',
                'level' => $this->faker->numberBetween(10, 50),
                'is_approver_grade' => $this->faker->boolean,
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId
            ]);
            $gradeId = $defaultGrade->id;
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->optional(0.7)->sentence(10, true), // Corrected from ->bs
            'is_active' => $this->faker->boolean(90),
            'grade_id' => $gradeId,
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
        return $this->state(fn(array $attributes) => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => ['is_active' => false]);
    }

    public function deleted(): static
    {
        $deleter = User::orderBy('id')->first();
         if (!$deleter && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $deleter = User::factory()->create(['name' => 'Deleter User Fallback (PosFactory)']);
        }

        return $this->state(fn(array $attributes) => [
            'deleted_at' => now(),
            'deleted_by' => $attributes['deleted_by'] ?? $deleter?->id,
            'is_active' => false,
        ]);
    }

    public function withDetails(string $name, Model|EloquentFactory|int|null $grade = null): static
    {
        $gradeId = null;
        if ($grade instanceof Model) {
            $gradeId = $grade->id;
        } elseif ($grade instanceof EloquentFactory) { // $grade here is a factory instance e.g. Grade::factory()
            $auditUser = User::orderBy('id')->first();
            if (!$auditUser && class_exists(User::class) && method_exists(User::class, 'factory')) {
                $auditUser = User::factory()->create(['name' => 'Audit User for Grade in PosFactory']);
            }
            if (method_exists($grade, 'create')) { // Check if it's a factory instance
                 $gradeId = $grade->create([
                    'name' => 'Grade for ' . $name . ' (' . $this->faker->unique()->word() .')', // Ensure unique name
                    'level' => $this->faker->numberBetween(10,50),
                    'created_by' => $auditUser?->id,
                    'updated_by' => $auditUser?->id
                    ])->id;
            }
        } elseif (is_int($grade)) {
            $gradeId = $grade;
        }

        return $this->state(function (array $attributes) use ($name, $gradeId) {
            $finalGradeId = $gradeId ?? $attributes['grade_id'] ?? null;
            if ($finalGradeId === null && Grade::count() > 0) {
                $finalGradeId = Grade::inRandomOrder()->value('id');
            }
            return [
                'name' => $name,
                'grade_id' => $finalGradeId,
            ];
        });
    }

    public function forGrade(Grade|int $grade): static
    {
        return $this->state(['grade_id' => $grade instanceof Grade ? $grade->id : $grade]);
    }
}
