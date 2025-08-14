<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;

class PositionFactory extends EloquentFactory
{
    protected $model = Position::class;

    /**
     * The official list of MOTAC position names.
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

        // Pick a realistic position name from the official list.
        // The ->unique() modifier ensures we don't pick the same name twice in a single run.
        $positionName = $this->faker->unique()->randomElement(self::$motacPositions);

        // Find the corresponding seeded position to get its ID, which is used to find a related grade.
        $seededPosition = Position::where('name', $positionName)->first();

        // Find a real grade that is linked to this specific position.
        $gradeId = null;
        if ($seededPosition) {
            // Find a grade where the position_id matches our selected position's ID.
            $grade = Grade::where('position_id', $seededPosition->id)->inRandomOrder()->first();
            $gradeId = $grade?->id;
        }

        // Fallback logic: If seeders haven't been run, or no grade was found,
        // pick any random grade that exists.
        if (! $gradeId) {
            $gradeId = Grade::inRandomOrder()->value('id');
        }

        return [
            'name' => $positionName,
            'description' => $msFaker->optional(0.7)->sentence(10, true),
            'is_active' => $this->faker->boolean(90),
            'grade_id' => $gradeId,
            'created_at' => Carbon::parse($this->faker->dateTimeBetween('-3 years', 'now')),
            'updated_at' => fn (array $attributes): \Illuminate\Support\Carbon => Carbon::parse($this->faker->dateTimeBetween($attributes['created_at'], 'now')),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => ['is_active' => false]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'is_active' => false,
        ]);
    }

    public function forGrade(Grade|int $grade): static
    {
        return $this->state(['grade_id' => $grade instanceof Grade ? $grade->id : $grade]);
    }
}
