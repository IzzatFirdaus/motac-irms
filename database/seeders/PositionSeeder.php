<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
// Removed: use App\Models\Department; as positions are not directly linked to departments in Position table schema
use Faker\Factory as FakerFactory; // Keep for potential description generation if needed
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('ms_MY'); // Malaysian locale for Faker if generating descriptions
    }

    public function run(): void
    {
        Log::info('Starting Position seeding (Revision 3)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('positions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated positions table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (PosSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for PositionSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in PositionSeeder.");
        }

        $grades = Grade::all()->keyBy('name'); // Key by name for easier lookup

        if ($grades->isEmpty()) {
            Log::error('No Grades found. Please run GradesSeeder first. Skipping PositionSeeder.');
            return;
        }

        // Position names from Supplementary Document (Section 3: Jawatan)
        // Grade mapping requires careful attention to match grade names seeded by GradesSeeder.
        // is_active defaults to true as per design
        $positionsData = [
            // Format: ['name' => 'Position Name', 'grade_name' => 'Matching Grade Name from GradesSeeder', 'description' => 'Optional Description', 'is_active' => true],
            ['name' => 'Menteri', 'grade_name' => 'MENTERI', 'department_name' => 'Pejabat Menteri'],
            ['name' => 'Timbalan Menteri', 'grade_name' => 'TIMBALAN MENTERI', 'department_name' => 'Pejabat Timbalan Menteri'],
            ['name' => 'Ketua Setiausaha', 'grade_name' => 'TURUS III', 'department_name' => 'Pejabat Ketua Setiausaha (KSU)'],
            ['name' => 'Timbalan Ketua Setiausaha', 'grade_name' => 'JUSA A', 'department_name' => 'Pejabat Timbalan Ketua Setiausaha (Pengurusan)'], // Example, assign to relevant TKSU office
            ['name' => 'Setiausaha Bahagian', 'grade_name' => 'JUSA C', 'department_name' => 'Bahagian Pengurusan Maklumat'], // Example
            ['name' => 'Setiausaha Akhbar', 'grade_name' => 'N52', 'department_name' => 'Pejabat Menteri'],
            ['name' => 'Setiausaha Sulit Kanan', 'grade_name' => 'N48', 'department_name' => 'Pejabat Menteri'],
            ['name' => 'Setiausaha Sulit', 'grade_name' => 'N44', 'department_name' => 'Pejabat Menteri'],
            ['name' => 'Pegawai Tugas-Tugas Khas', 'grade_name' => 'N48', 'department_name' => 'Pejabat Ketua Setiausaha (KSU)'],
            ['name' => 'Timbalan Setiausaha Bahagian', 'grade_name' => 'N52', 'department_name' => 'Bahagian Pengurusan Maklumat'], // Example
            ['name' => 'Ketua Unit', 'grade_name' => 'N48', 'department_name' => 'Bahagian Pengurusan Maklumat'], // Example
            ['name' => 'Pegawai Khas', 'grade_name' => 'N41', 'department_name' => 'Pejabat Menteri'],
            ['name' => 'Pegawai Media', 'grade_name' => 'S44', 'department_name' => 'Komunikasi Korporat'],
            ['name' => 'Pengarah', 'grade_name' => 'N52', 'department_name' => 'MOTAC Johor'], // Example: State Director
            ['name' => 'Timbalan Pengarah', 'grade_name' => 'N48', 'department_name' => 'MOTAC Johor'], // Example
            ['name' => 'Penolong Pengarah', 'grade_name' => 'N41', 'department_name' => 'MOTAC Johor'], // Example
            ['name' => 'Ketua Penolong Setiausaha Kanan (M)', 'grade_name' => 'M52', 'department_name' => 'Pentadbiran'],
            ['name' => 'Ketua Penolong Setiausaha (M)', 'grade_name' => 'M48', 'department_name' => 'Pentadbiran'],
            ['name' => 'Penolong Setiausaha Kanan (M)', 'grade_name' => 'M44', 'department_name' => 'Pentadbiran'],
            ['name' => 'Penolong Setiausaha (M)', 'grade_name' => 'M41', 'department_name' => 'Pentadbiran'],
            ['name' => 'Pegawai Teknologi Maklumat (F)', 'grade_name' => 'FT41', 'department_name' => 'Bahagian Pengurusan Maklumat'],
            ['name' => 'Pegawai Kebudayaan (B)', 'grade_name' => 'B41', 'department_name' => 'Dasar Kebudayaan'],
            ['name' => 'Penasihat Undang-Undang (L)', 'grade_name' => 'L54', 'department_name' => 'Perundangan'],
            ['name' => 'Pegawai Psikologi (S)', 'grade_name' => 'S41', 'department_name' => 'Bahagian Sumber Manusia'],
            ['name' => 'Akauntan (WA)', 'grade_name' => 'W41', 'department_name' => 'Akaun'],
            ['name' => 'Pegawai Hal Ehwal Islam (S)', 'grade_name' => 'S41', 'department_name' => 'Pentadbiran'], // Generic assignment
            ['name' => 'Pegawai Penerangan (S)', 'grade_name' => 'S41', 'department_name' => 'Komunikasi Korporat'],
            ['name' => 'Jurutera (J)', 'grade_name' => 'J41', 'department_name' => 'Pembangunan Prasarana'],
            ['name' => 'Kurator (S)', 'grade_name' => 'S41', 'department_name' => 'Dasar Kebudayaan'], // Example
            ['name' => 'Jurukur Bahan (J)', 'grade_name' => 'J41', 'department_name' => 'Pembangunan Prasarana'],
            ['name' => 'Arkitek (J)', 'grade_name' => 'J41', 'department_name' => 'Pembangunan Prasarana'],
            ['name' => 'Pegawai Arkib (S)', 'grade_name' => 'S41', 'department_name' => 'Pentadbiran'], // Generic assignment
            ['name' => 'Juruaudit (W)', 'grade_name' => 'W41', 'department_name' => 'Audit Dalam'],
            ['name' => 'Perangkawan (E)', 'grade_name' => 'E41', 'department_name' => 'Dasar Pelancongan dan Hubungan Antarabangsa'], // Example
            ['name' => 'Pegawai Siasatan (P)', 'grade_name' => 'KP41', 'department_name' => 'Integriti'], // Assuming P maps to KP scheme and Integriti
            ['name' => 'Penguasa Imigresen (KP)', 'grade_name' => 'KP41', 'department_name' => 'Pelesenan dan Penguatkuasaan Pelancongan'], // Example
            ['name' => 'Pereka (B)', 'grade_name' => 'B41', 'department_name' => 'Komunikasi Korporat'], // Example
            ['name' => 'Peguam Persekutuan (L)', 'grade_name' => 'L48', 'department_name' => 'Perundangan'],
            ['name' => 'Penolong Pegawai Teknologi Maklumat', 'grade_name' => 'FT29', 'department_name' => 'Bahagian Pengurusan Maklumat'],
            ['name' => 'Penolong Pegawai hal Ehwal Islam (S)', 'grade_name' => 'S29', 'department_name' => 'Pentadbiran'],
            ['name' => 'Penolong Pegawai Undang-Undang (L)', 'grade_name' => 'L29', 'department_name' => 'Perundangan'],
            ['name' => 'Penolong Juruaudit', 'grade_name' => 'W29', 'department_name' => 'Audit Dalam'],
            ['name' => 'Penolong Jurutera', 'grade_name' => 'J29', 'department_name' => 'Pembangunan Prasarana'],
            ['name' => 'Penolong Pegawai Tadbir', 'grade_name' => 'N29', 'department_name' => 'Pentadbiran'],
            ['name' => 'Penolong Pegawai Penerangan (S)', 'grade_name' => 'S29', 'department_name' => 'Komunikasi Korporat'],
            ['name' => 'Penolong Pegawai Psikologi (S)', 'grade_name' => 'S29', 'department_name' => 'Bahagian Sumber Manusia'],
            ['name' => 'Penolong Pegawai Siasatan (P)', 'grade_name' => 'KP29', 'department_name' => 'Integriti'],
            ['name' => 'Penolong Pegawai Arkib (S)', 'grade_name' => 'S29', 'department_name' => 'Pentadbiran'],
            ['name' => 'Jurufotografi', 'grade_name' => 'B29', 'department_name' => 'Komunikasi Korporat'],
            ['name' => 'Penolong Penguasa Imigresen (KP)', 'grade_name' => 'KP29', 'department_name' => 'Pelesenan dan Penguatkuasaan Pelancongan'],
            ['name' => 'Penolong Pustakawan (S)', 'grade_name' => 'S29', 'department_name' => 'Pentadbiran'], // Example
            ['name' => 'Setiausaha Pejabat (N)', 'grade_name' => 'N29', 'department_name' => 'Pentadbiran'],
            ['name' => 'Pembantu Setiausaha Pejabat (N)', 'grade_name' => 'N19', 'department_name' => 'Pentadbiran'],
            ['name' => 'Pembantu Tadbir (Perkeranian/Operasi) (N)', 'grade_name' => 'N19', 'department_name' => 'Pentadbiran'],
            ['name' => 'Penolong Akauntan (W)', 'grade_name' => 'W29', 'department_name' => 'Akaun'],
            ['name' => 'Pembantu Tadbir (Kewangan) (W)', 'grade_name' => 'W19', 'department_name' => 'Akaun'],
            ['name' => 'Pembantu Operasi (N)', 'grade_name' => 'N11', 'department_name' => 'Pentadbiran'],
            ['name' => 'Pembantu Keselamatan (KP)', 'grade_name' => 'KP19', 'department_name' => 'Pentadbiran'], // Or specific security unit
            ['name' => 'Juruteknik Komputer (FT)', 'grade_name' => 'FT19', 'department_name' => 'Bahagian Pengurusan Maklumat'],
            ['name' => 'Pemandu Kenderaan (H)', 'grade_name' => 'H11', 'department_name' => 'Pentadbiran'],
            ['name' => 'MySTEP', 'grade_name' => 'MySTEP', 'department_name' => 'Bahagian Sumber Manusia'], // MySTEP staff often attached to BSM or specific divisions
            ['name' => 'Pelajar Latihan Industri', 'grade_name' => 'PELATIH', 'department_name' => 'Bahagian Sumber Manusia'], // Interns usually under BSM
            ['name' => 'Pegawai Imigresen', 'grade_name' => 'KP19', 'department_name' => 'Pelesenan dan Penguatkuasaan Pelancongan'],
        ];

        Log::info('Creating specific positions based on supplementary document (Revision 3)...');
        $createdCount = 0;
        foreach ($positionsData as $positionEntry) {
            $grade = $grades->get($positionEntry['grade_name']);

            if (!$grade) {
                Log::warning("Grade '{$positionEntry['grade_name']}' not found for position '{$positionEntry['name']}'. Skipping this position. Ensure GradesSeeder has run and names match.");
                continue;
            }

            $position = Position::firstOrCreate(
                ['name' => $positionEntry['name']], // Position names are unique
                [
                    'description' => $positionEntry['description'] ?? 'Jawatan ' . $positionEntry['name'],
                    'grade_id' => $grade->id,
                    'is_active' => $positionEntry['is_active'] ?? true, // Default to true
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ]
            );
            if ($position->wasRecentlyCreated) {
                $createdCount++;
            }
        }
        Log::info("Ensured/Created {$createdCount} specific positions.");

        $targetCount = count($positionsData) + 10; // Target a few more than explicitly defined
        $currentPositionCount = Position::count();

        if ($currentPositionCount < $targetCount && !$grades->isEmpty()) {
            $needed = $targetCount - $currentPositionCount;
            Log::info("Creating {$needed} additional random positions using factory...");
            Position::factory()
                ->count($needed)
                ->create([
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                    'is_active' => true,
                    // Factory should handle assigning a random valid grade_id from existing grades
                    'grade_id' => $grades->isNotEmpty() ? $grades->random()->id : Grade::factory()->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId])->id,
                ]);
            Log::info("Created {$needed} additional random positions.");
        }

        Log::info('Position seeding complete (Revision 3).');
    }
}
