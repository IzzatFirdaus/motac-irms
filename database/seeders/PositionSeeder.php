<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Position seeding (Revision 3 - Aligned)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('positions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated positions table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (PosSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for PositionSeeder.");
        } elseif (!$auditUserId) {
            Log::error('No users for audit columns, and User factory could not be used. Please run UserSeeder first. Skipping PositionSeeder creation of new audit user.');
        }

        if ($auditUserId) {
            Log::info("Using User ID {$auditUserId} for audit columns in PositionSeeder.");
        }


        $grades = Grade::all()->keyBy('name');

        if ($grades->isEmpty()) {
            Log::warning('No Grades found. Calling GradesSeeder to ensure grades exist.');
            $this->call(GradesSeeder::class); // Assuming GradesSeeder exists
            $grades = Grade::all()->keyBy('name'); // Re-fetch grades
            if ($grades->isEmpty()) {
                Log::error('Failed to seed Grades. Positions cannot be reliably linked to grades. Skipping specific position creation.');
                return; // Exit if grades are essential and still not found
            }
        }

        // Position names from Supplementary Document (Section 3: Jawatan)
        // Grade mapping requires careful attention to match grade names seeded by GradesSeeder.
        // is_active defaults to true as per Position model
        $positionsData = [
            ['name' => 'Menteri', 'grade_name' => 'MENTERI', 'description' => 'Jawatan Menteri Kabinet'],
            ['name' => 'Timbalan Menteri', 'grade_name' => 'TIMBALAN MENTERI', 'description' => 'Jawatan Timbalan Menteri'],
            ['name' => 'Ketua Setiausaha', 'grade_name' => 'TURUS III', 'description' => 'Jawatan Ketua Setiausaha Kementerian'],
            ['name' => 'Timbalan Ketua Setiausaha', 'grade_name' => 'JUSA A', 'description' => 'Jawatan Timbalan Ketua Setiausaha'],
            ['name' => 'Setiausaha Bahagian', 'grade_name' => 'JUSA C', 'description' => 'Jawatan Setiausaha Bahagian'],
            ['name' => 'Setiausaha Akhbar', 'grade_name' => 'N52'],
            ['name' => 'Setiausaha Sulit Kanan', 'grade_name' => 'N48'],
            ['name' => 'Pegawai Teknologi Maklumat (F)', 'grade_name' => 'FT41'],
            ['name' => 'Penolong Pegawai Teknologi Maklumat', 'grade_name' => 'FT29'],
            ['name' => 'Pembantu Tadbir (Perkeranian/Operasi) (N)', 'grade_name' => 'N19'],
            ['name' => 'Juruteknik Komputer (FT)', 'grade_name' => 'FT19'],
            ['name' => 'MySTEP', 'grade_name' => 'MySTEP'],
            ['name' => 'Pelajar Latihan Industri', 'grade_name' => 'PELATIH'],
            // Add more positions as needed, ensuring 'grade_name' matches a name in your GradesSeeder
        ];

        Log::info('Creating specific positions based on supplementary document (Revision 3 - Aligned)...');
        $createdCount = 0;
        foreach ($positionsData as $positionEntry) {
            $grade = $grades->get($positionEntry['grade_name']);

            if (!$grade) {
                Log::warning("Grade '{$positionEntry['grade_name']}' not found for position '{$positionEntry['name']}'. Skipping this position. Ensure GradesSeeder has run and names match.");
                continue;
            }

            $position = Position::firstOrCreate(
                ['name' => $positionEntry['name']],
                [
                    'description' => $positionEntry['description'] ?? 'Jawatan ' . $positionEntry['name'],
                    'grade_id' => $grade->id,
                    'is_active' => $positionEntry['is_active'] ?? true,
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ]
            );
            if ($position->wasRecentlyCreated) {
                $createdCount++;
            }
        }
        Log::info("Ensured/Created {$createdCount} specific positions.");

        $targetFactoryCount = 20; // Number of additional random positions
        if ($grades->isNotEmpty() && class_exists(Position::class) && method_exists(Position::class, 'factory')) {
            Log::info("Creating {$targetFactoryCount} additional random positions using factory...");
            Position::factory()
                ->count($targetFactoryCount)
                ->create(); // Factory will handle grade_id and audit stamps
            Log::info("Created {$targetFactoryCount} additional random positions.");
        } else if ($grades->isEmpty()){
             Log::warning("Skipping factory creation of Positions as no Grades exist to link to.");
        } else {
            Log::error('App\Models\Position model or its factory not found. Cannot seed additional positions via factory.');
        }

        Log::info('Position seeding complete (Revision 3 - Aligned).');
    }
}
