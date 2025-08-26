<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for the Position model (Jawatan).
 *
 * - Uses static caches for related Grade and User IDs to avoid repeated DB hits.
 * - Does NOT create related models (Grade/User) inside definition().
 * - All foreign keys (grade_id, created_by, updated_by) can be set from the seeder/state, otherwise chosen randomly from existing records.
 * - Use with seeders that ensure grades and users exist before creating positions.
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
        // Static caches for Grade IDs and User IDs (for audit fields)
        static $gradeIds, $userIds, $msFaker;

        if (! isset($gradeIds)) {
            $gradeIds = Grade::pluck('id')->all();
        }
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        if (! isset($msFaker)) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Pick random grade/user IDs if available
        $gradeId     = ! empty($gradeIds) ? Arr::random($gradeIds) : null;
        $auditUserId = ! empty($userIds) ? Arr::random($userIds) : null;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        // Pick a position name from the official list. Duplicates allowed if factory exceeds unique count.
        $positionName = $this->faker->randomElement(self::$motacPositions);

        return [
            'name'        => $positionName,
            'description' => $msFaker->optional(0.7)->sentence(10, true),
            'is_active'   => $this->faker->boolean(90),
            'grade_id'    => $gradeId,
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
            'deleted_by'  => $isDeleted ? $auditUserId : null,
            'created_at'  => $createdAt,
            'updated_at'  => $updatedAt,
            'deleted_at'  => $deletedAt,
        ];
    }

    /**
     * State: Active position.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State: Inactive position.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State: Soft deleted position.
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
            'is_active'  => false,
            'deleted_by' => $deleterId,
        ]);
    }

    /**
     * State to assign this position to a specific grade.
     */
    public function forGrade(Grade|int $grade): static
    {
        $gradeId = $grade instanceof Grade ? $grade->id : $grade;

        return $this->state([
            'grade_id' => $gradeId,
        ]);
    }
}
