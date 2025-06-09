<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        // We use unique() to ensure each factory run produces a grade with a unique level
        $level = $this->faker->unique()->numberBetween(1, 70);
        $name = 'Gred Ujian '.$level; // Default name, refined below

        // Detailed name generation logic based on level for more realistic test data
        if ($level >= 68) {
            $name = $level >= 70 ? 'Menteri (Ujian)' : 'Timbalan Menteri (Ujian)';
        } elseif ($level >= 62) { // TURUS
            $turusMap = [62 => 'TURUS III', 64 => 'TURUS II', 66 => 'TURUS I'];
            $name = ($turusMap[$level] ?? 'TURUS Ujian').' ('.$level.')';
        } elseif ($level >= 56) { // JUSA
            $jusaMap = [56 => 'JUSA C', 58 => 'JUSA B', 60 => 'JUSA A'];
            $name = ($jusaMap[$level] ?? 'JUSA Ujian').' ('.$level.')';
        } elseif ($level >= 41) { // Management & Professional (P&P)
            $prefix = $this->faker->randomElement(['N', 'F', 'M', 'E', 'W', 'S', 'J', 'DG']);
            $name = $prefix.$level;
        } else { // Support Group
            $prefix = $this->faker->randomElement(['N', 'FT', 'W', 'H', 'KP', 'U', 'C']);
            $name = $prefix.$level;
        }

        // Get the minimum approval level from the config file, with a default fallback
        $minApprovalLevel = Config::get('motac.approval.min_general_view_approval_grade_level', 9);

        return [
            'name' => $name,
            'level' => $level,
            'is_approver_grade' => ($level >= $minApprovalLevel),
            'min_approval_grade_id' => null, // This is handled by application logic/policies
            'description' => 'Gred janaan kilang untuk tujuan ujian.',
            'deleted_at' => null,
        ];
    }

    /**
     * Indicate that the grade should be an approver grade.
     */
    public function approverGrade(): static
    {
        return $this->state(function (array $attributes) {
            $minLevel = Config::get('motac.approval.min_general_view_approval_grade_level', 9);
            // Ensure the generated level is at or above the minimum approval level
            $level = $this->faker->unique()->numberBetween($minLevel, 70);

            return [
                'level' => $level,
                'is_approver_grade' => true,
            ];
        });
    }

    /**
     * Indicate that the grade should be a non-approver grade.
     */
    public function nonApproverGrade(): static
    {
        return $this->state(function (array $attributes) {
            $minLevel = Config::get('motac.approval.min_general_view_approval_grade_level', 9);
            // Ensure the generated level is below the minimum approval level
            $level = $this->faker->unique()->numberBetween(1, $minLevel - 1);

            return [
                'level' => $level,
                'is_approver_grade' => false,
            ];
        });
    }

    /**
     * Indicate that the grade is soft deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
