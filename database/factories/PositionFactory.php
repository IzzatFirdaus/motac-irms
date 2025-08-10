<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the Position model (Jawatan).
 *
 * Generates realistic positions for MOTAC, matching the official list used in the seeder.
 * Links to valid grades if possible. Sets all audit (blameable) columns.
 * Fully aligned with the migration (2013_11_01_132000_create_positions_table.php),
 * seeder, and Position model structure.
 *
 * NOTE: The use of unique() with a static list is REMOVED to prevent OverflowException.
 *       This factory allows duplicate position names if more than the unique list is needed.
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * The official list of MOTAC position names (as seen in the PositionSeeder).
     * This ensures generated names are consistent with actual system data.
     */
    private static array $motacPositions = [
        'Menteri', 'Timbalan Menteri', 'Ketua Setiausaha', 'Timbalan Ketua Setiausaha', 'Setiausaha Bahagian',
        'Setiausaha Akhbar', 'Setiausaha Sulit Kanan', 'Setiausaha Sulit', 'Pegawai Tugas-Tugas Khas',
        'Timbalan Setiausaha Bahagian', 'Ketua Unit', 'Pegawai Khas', 'Pegawai Media', 'Pengarah', 'Timbalan Pengarah',
        'Penolong Pengarah', 'Ketua Penolong Setiausaha Kanan (M)', 'Ketua Penolong Setiausaha (M)',
        'Penolong Setiausaha Kanan (M)', 'Penolong Setiausaha (M)', 'Pegawai Teknologi Maklumat (F)',
        'Pegawai Kebudayaan (B)', 'Penasihat Undang-Undang (L)', 'Pegawai Psikologi (S)', 'Akauntan (WA)',
        'Pegawai Hal Ehwal Islam (S)', 'Pegawai Penerangan (S)', 'Jurutera (J)', 'Kurator (S)', 'Jurukur Bahan (J)',
        'Arkitek (J)', 'Pegawai Arkib (S)', 'Juruaudit (W)', 'Perangkawan (E)', 'Pegawai Siasatan (P)',
        'Penguasa Imigresen (KP)', 'Pereka (B)', 'Peguam Persekutuan (L)', 'Penolong Pegawai Teknologi Maklumat',
        'Penolong Pegawai hal Ehwal Islam (S)', 'Penolong Pegawai Undang-Undang (L)',
        'Penolong Pegawai Teknologi Maklumat_2', 'Penolong Juruaudit', 'Penolong Jurutera', 'Penolong Pegawai Tadbir',
        'Penolong Pegawai Penerangan (S)', 'Penolong Pegawai Psikologi (S)', 'Penolong Pegawai Siasatan (P)',
        'Penolong Pegawai Arkib (S)', 'Jurufotografi', 'Penolong Penguasa Imigresen (KP)', 'Penolong Pustakawan (S)',
        'Setiausaha Pejabat (N)', 'Pembantu Setiausaha Pejabat (N)', 'Pembantu Tadbir (Perkeranian/Operasi) (N)',
        'Penolong Akauntan (W)', 'Pembantu Tadbir (Kewangan) (W)', 'Pembantu Operasi (N)',
        'Pembantu Keselamatan (KP)', 'Juruteknik Komputer (FT)', 'Pemandu Kenderaan (H)', 'Pembantu Khidmat (H)',
        'MySTEP', 'Pelajar Latihan Industri', 'Pegawai Imigresen',
    ];

    public function definition(): array
    {
        $msFaker = \Faker\Factory::create('ms_MY');

        // Pick a position name from the official list; duplicates allowed if factory exceeds unique count.
        $positionName = $this->faker->randomElement(self::$motacPositions);

        // Try to find a Grade that is linked to this position via position_id.
        $seededPosition = Position::where('name', $positionName)->first();
        $gradeId = null;
        if ($seededPosition) {
            $grade = Grade::where('position_id', $seededPosition->id)->inRandomOrder()->first();
            $gradeId = $grade?->id;
        }
        // Fallback: pick any grade if none was found for this position
        if (!$gradeId) {
            $gradeId = Grade::inRandomOrder()->value('id');
        }

        // Set blameable/audit columns (created_by, updated_by, etc.)
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (PositionFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'name' => $positionName,
            'description' => $msFaker->optional(0.7)->sentence(10, true),
            'is_active' => $this->faker->boolean(90),
            'grade_id' => $gradeId,
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => $isDeleted ? $auditUserId : null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * State for active position.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State for inactive position.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State for soft deleted position.
     */
    public function deleted(): static
    {
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (PositionFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'is_active' => false,
            'deleted_by' => $auditUserId,
        ]);
    }

    /**
     * State to assign this position to a specific grade.
     */
    public function forGrade(Grade|int $grade): static
    {
        return $this->state([
            'grade_id' => $grade instanceof Grade ? $grade->id : $grade,
        ]);
    }
}
