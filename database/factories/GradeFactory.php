<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        $level = $this->faker->unique()->numberBetween(1, 70);
        $name = 'Gred Ujian ' . $level; // Default name, will be refined
        $serviceScheme = 'Am'; // Default service scheme

        // Detailed name and service scheme generation logic based on level
        if ($level >= 68) {
            $name = $level >= 70 ? 'Menteri (Contoh)' : 'Timbalan Menteri (Contoh)';
            $serviceScheme = 'Jawatan Politik';
        } elseif ($level >= 62) { // TURUS
            $turusMap = [62 => 'TURUS III', 63 => 'TURUS III', 64 => 'TURUS II', 65 => 'TURUS II', 66 => 'TURUS I', 67 => 'TURUS I'];
            $name = ($turusMap[$level] ?? 'TURUS Lain') . ' (Contoh)';
            $serviceScheme = 'Jawatan Turus';
        } elseif ($level >= 56) { // JUSA
            $jusaMap = [56 => 'JUSA C', 57 => 'JUSA C', 58 => 'JUSA B', 59 => 'JUSA B', 60 => 'JUSA A', 61 => 'JUSA A'];
            $name = ($jusaMap[$level] ?? 'JUSA Lain') . ' (Contoh)';
            $serviceScheme = 'Jawatan Utama Sektor Awam (JUSA)';
        } elseif ($level >= 41) { // Management & Professional (P&P)
            $prefix = $this->faker->randomElement(['N', 'F', 'M', 'E', 'W', 'S', 'J', 'L', 'P', 'B', 'Q', 'DG']);
            $name = $prefix . $level;
            $serviceScheme = 'Kumpulan Pengurusan dan Profesional';
        } elseif ($level >= 19) { // Support Group I
            $prefix = $this->faker->randomElement(['N', 'FT', 'W', 'S', 'H', 'KP', 'U', 'C']);
            $name = $prefix . $level;
            $serviceScheme = 'Kumpulan Sokongan I';
        } else { // 1-18, Support Group II / other operational
            $prefix = $this->faker->randomElement(['N', 'H', 'R', 'C']); // Common prefixes for lower grades
            if ($level == 1) { // Example for Pelajar Latihan Industri
                 $name = 'Pelajar Latihan Industri';
                 $serviceScheme = 'Latihan Industri';
            } else {
                $name = $prefix . $level;
                $serviceScheme = 'Kumpulan Sokongan II';
            }
        }
        // Append a unique suffix if there's a chance of name collision from different prefixes at same level
        // $name .= ' (' . $this->faker->unique()->lexify('???') . ')';


        $minAnyApprovalLevel = Config::get('motac.approval.min_general_approver_level', 9);

        return [
            'name' => $name,
            'level' => $level,
            'is_approver_grade' => ($level >= $minAnyApprovalLevel),
            'min_approval_grade_id' => null, // Typically set based on specific rules or null
            'description' => $this->faker->optional(0.7)->sentence(mt_rand(5, 12)),
            'service_scheme' => $serviceScheme,
            // 'created_by', 'updated_by', 'deleted_by' usually handled by Blameable trait/observer
            'deleted_at' => null, // For non-soft-deleted records
        ];
    }

    /**
     * Indicate that the grade is an approver grade.
     * Uses nullable type hint for PHP 8.0+ to address Intelephense warning.
     */
    public function approverGrade(?int $minLevelForThisState = null): static
    {
        return $this->state(function (array $attributes) use ($minLevelForThisState) {
            $generalMinApproverLevel = Config::get('motac.approval.min_general_approver_level', 9);
            $actualMinLevel = $minLevelForThisState ?? $generalMinApproverLevel;

            // Prioritize existing level if passed, otherwise generate appropriate level
            $level = $attributes['level'] ?? $this->faker->unique()->numberBetween($actualMinLevel, 70);
            if ($level < $actualMinLevel) { // Ensure the level respects the minimum for an approver
                $level = $this->faker->unique()->numberBetween($actualMinLevel, 70);
            }

            $name = $attributes['name'] ?? ('Gred Penyokong ' . $level . ' (' . Str::random(3) . ')');

            return [
                'name' => $name,
                'level' => $level,
                'is_approver_grade' => true,
            ];
        });
    }

    /**
     * Indicate that the grade is a non-approver grade.
     * Uses nullable type hint for PHP 8.0+ to address Intelephense warning.
     */
    public function nonApproverGrade(?int $maxLevelForThisState = null): static
    {
        return $this->state(function (array $attributes) use ($maxLevelForThisState) {
            $generalMinApproverLevel = Config::get('motac.approval.min_general_approver_level', 9);
            // Default max level for non-approver is one less than min approver level
            $defaultMaxNonApproverLevel = ($generalMinApproverLevel > 1) ? $generalMinApproverLevel - 1 : 1;
            $actualMaxLevel = $maxLevelForThisState ?? $defaultMaxNonApproverLevel;
            $actualMaxLevel = max(1, $actualMaxLevel); // Ensure it's at least 1

            $level = $attributes['level'] ?? $this->faker->unique()->numberBetween(1, $actualMaxLevel);
            if ($level > $actualMaxLevel) { // Ensure the level respects the maximum for a non-approver
                $level = $this->faker->unique()->numberBetween(1, $actualMaxLevel);
            }
            $level = max(1, $level); // Ensure level is at least 1

            $name = $attributes['name'] ?? ('Gred Sokongan ' . $level . ' (' . Str::random(3) . ')');

            return [
                'name' => $name,
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
        return $this->state(fn(array $attributes) => [
            'deleted_at' => now(),
            // 'deleted_by' would be handled by Blameable if used
        ]);
    }
}
