<?php

namespace Database\Factories;

use App\Models\Grade; // Corrected: Department model is not directly related to Position
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model; // For type hinting in withDetails
use Illuminate\Support\Carbon;

class PositionFactory extends EloquentFactory
{
    protected $model = Position::class;

    public function definition(): array
    {
        // Ensure a default user exists for audit fields
        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (PosFactory)']);
        $auditUserId = $auditUser->id;

        // Ensure a default grade exists if none are found
        $gradeId = Grade::inRandomOrder()->value('id');
        if (!$gradeId) {
            $defaultGrade = Grade::factory()->create([
                'name' => 'Default Grade (PosFactory)', // Ensure required fields for Grade are provided
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId
            ]);
            $gradeId = $defaultGrade->id;
        }

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        return [
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->optional(0.6)->bs,
            // 'vacancies_count' => $this->faker->numberBetween(0, 5), // Removed as not in schema
            'is_active' => $this->faker->boolean(90), // Consistent with schema
            'grade_id' => $gradeId, // Consistent with schema (nullable, so random existing or newly created is fine)
            // 'department_id' => $departmentId, // Removed as not in schema
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
        $deleter = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Deleter User Fallback (PosFactory)']);

        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
            'deleted_by' => $attributes['deleted_by'] ?? $deleter->id,
            'is_active' => false, // Typically, soft-deleted records are marked inactive
        ]);
    }

    /**
     * Define a position with specific details.
     * Department association has been removed as it's not in the Position schema.
     */
    public function withDetails(string $name, Model|EloquentFactory|int|null $grade = null): static
    {
        $gradeId = null;
        if ($grade instanceof Model) {
            $gradeId = $grade->id;
        } elseif ($grade instanceof EloquentFactory) {
            // Ensure factory creates with necessary audit fields if Grade factory needs them
            $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Audit User for Grade in PosFactory']);
            $gradeId = $grade->create(['created_by' => $auditUser->id, 'updated_by' => $auditUser->id])->id;
        } elseif (is_int($grade)) {
            $gradeId = $grade;
        }

        return $this->state(function (array $attributes) use ($name, $gradeId) {
            $finalGradeId = $gradeId ?? $attributes['grade_id'] ?? null; // Use provided, existing, or null
            if ($finalGradeId === null && !Grade::count() === 0) { // If still null and grades exist, pick one
                $finalGradeId = Grade::inRandomOrder()->value('id');
            }
            // If $finalGradeId is still null here and grade_id is non-nullable in DB, it might cause an error.
            // The schema says grade_id is nullable, so null is acceptable.

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

    // Removed forDepartment() method as department_id is not part of Position model
    // public function forDepartment(Department|int $department): static
    // {
    //     return $this->state(['department_id' => $department instanceof Department ? $department->id : $department]);
    // }
}
