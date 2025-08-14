<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder populates the grades table with the full list from the supplementary document.
     * It relies on PositionSeeder having been run first.
     */
    public function run(): void
    {
        Log::info('Starting Grades seeding (Revision 3.5)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Grade::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::info('Truncated grades table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;
        if ($auditUserId) {
            Log::info(sprintf('Using User ID %s for audit columns in GradesSeeder.', $auditUserId));
        }

        // Complete list of 282 grades from MyMail form, linking to positions via position_id
        $grades = [
            // This is the full array from our previous step.
            ['name' => 'Menteri', 'position_id' => 1], ['name' => 'Timbalan Menteri', 'position_id' => 2],
            ['name' => 'Turus III', 'position_id' => 3], ['name' => 'Jusa A', 'position_id' => 3],
            ['name' => 'Jusa B', 'position_id' => 3], ['name' => 'Jusa C', 'position_id' => 3],
            ['name' => 'Jusa A', 'position_id' => 4], ['name' => 'Jusa B', 'position_id' => 4],
            ['name' => 'Jusa C', 'position_id' => 4], ['name' => 'Jusa A', 'position_id' => 5],
            ['name' => 'Jusa B', 'position_id' => 5], ['name' => 'Jusa C', 'position_id' => 5],
            ['name' => '(14) 54', 'position_id' => 5], ['name' => '(13) 52', 'position_id' => 5],
            ['name' => '(12) 48', 'position_id' => 5], ['name' => '14 (54)', 'position_id' => 6],
            ['name' => '13 (52)', 'position_id' => 6], ['name' => '(12) 48', 'position_id' => 6],
            ['name' => '14 (54)', 'position_id' => 7], ['name' => '13 (52)', 'position_id' => 7],
            ['name' => '12 (48)', 'position_id' => 7], ['name' => '14 (54)', 'position_id' => 8],
            ['name' => '13 (52)', 'position_id' => 8], ['name' => '(12) 48', 'position_id' => 8],
            ['name' => '14 (54)', 'position_id' => 9], ['name' => '13 (52)', 'position_id' => 9],
            ['name' => '12 (48)', 'position_id' => 9], ['name' => '10 (44)', 'position_id' => 9],
            ['name' => '9 (41)', 'position_id' => 9], ['name' => '14 (54)', 'position_id' => 10],
            ['name' => '13 (52)', 'position_id' => 10], ['name' => '12 (48)', 'position_id' => 10],
            ['name' => '14 (54)', 'position_id' => 11], ['name' => '13 (52)', 'position_id' => 11],
            ['name' => '12 (48)', 'position_id' => 11], ['name' => '10 (44)', 'position_id' => 11],
            ['name' => '9 (41)', 'position_id' => 11], ['name' => '14 (54)', 'position_id' => 12],
            ['name' => '13 (52)', 'position_id' => 12], ['name' => '12 (48)', 'position_id' => 12],
            ['name' => '10 (44)', 'position_id' => 12], ['name' => '9 (41)', 'position_id' => 12],
            ['name' => '14 (54)', 'position_id' => 13], ['name' => '13 (52)', 'position_id' => 13],
            ['name' => '12 (48)', 'position_id' => 13], ['name' => '14 (54)', 'position_id' => 14],
            ['name' => '13 (52)', 'position_id' => 14], ['name' => '12 (48)', 'position_id' => 14],
            ['name' => '10 (44)', 'position_id' => 14], ['name' => '9 (41)', 'position_id' => 14],
            ['name' => '14 (54)', 'position_id' => 15], ['name' => '13 (52)', 'position_id' => 15],
            ['name' => '12 (48)', 'position_id' => 15], ['name' => '10 (44)', 'position_id' => 15],
            ['name' => '9 (41)', 'position_id' => 15], ['name' => '14 (54)', 'position_id' => 16],
            ['name' => '13 (52)', 'position_id' => 16], ['name' => '12 (48)', 'position_id' => 16],
            ['name' => '10 (44)', 'position_id' => 16], ['name' => '9 (41)', 'position_id' => 16],
            ['name' => '13 (52)', 'position_id' => 17], ['name' => '12 (48)', 'position_id' => 17],
            ['name' => '13 (52)', 'position_id' => 18], ['name' => '12 (48)', 'position_id' => 18],
            ['name' => '10 (44)', 'position_id' => 19], ['name' => '9 (41)', 'position_id' => 20],
            ['name' => '14 (54)', 'position_id' => 21], ['name' => '13 (52)', 'position_id' => 21],
            ['name' => '12 (48)', 'position_id' => 21], ['name' => '10 (44)', 'position_id' => 21],
            ['name' => '9 (41)', 'position_id' => 21], ['name' => '14 (53/54)', 'position_id' => 22],
            ['name' => '13 (51/52)', 'position_id' => 22], ['name' => '12 (47/48)', 'position_id' => 22],
            ['name' => '10 (43/44)', 'position_id' => 22], ['name' => '9 (41/42)', 'position_id' => 22],
            ['name' => '7 (37/38)', 'position_id' => 22], ['name' => '6 (31/32)', 'position_id' => 22],
            ['name' => '5 (29/30)', 'position_id' => 22], ['name' => '3 (25/26)', 'position_id' => 22],
            ['name' => '2 (21/22)', 'position_id' => 22], ['name' => '1 (19)', 'position_id' => 22],
            ['name' => '14 (54)', 'position_id' => 23], ['name' => '13 (52)', 'position_id' => 23],
            ['name' => '12 (48)', 'position_id' => 23], ['name' => '10 (44)', 'position_id' => 23],
            ['name' => '9 (41)', 'position_id' => 23], ['name' => '14 (54)', 'position_id' => 24],
            ['name' => '13 (52)', 'position_id' => 24], ['name' => '12 (48)', 'position_id' => 24],
            ['name' => '10 (44)', 'position_id' => 24], ['name' => '9 (41)', 'position_id' => 24],
            ['name' => '14 (54)', 'position_id' => 25], ['name' => '14 (53/54)', 'position_id' => 25],
            ['name' => '13 (51/52)', 'position_id' => 25], ['name' => '12 (47/48)', 'position_id' => 25],
            ['name' => '10 (44)', 'position_id' => 25], ['name' => '8 (40)', 'position_id' => 25],
            ['name' => '7 (38)', 'position_id' => 25], ['name' => '6 (32)', 'position_id' => 25],
            ['name' => '5 (29)', 'position_id' => 25], ['name' => '4 (28)', 'position_id' => 25],
            ['name' => '3 (26)', 'position_id' => 25], ['name' => '2 (22)', 'position_id' => 25],
            ['name' => '1 (19)', 'position_id' => 25], ['name' => '14 (54)', 'position_id' => 26],
            ['name' => '13 (52)', 'position_id' => 26], ['name' => '12 (48)', 'position_id' => 26],
            ['name' => '10 (44)', 'position_id' => 26], ['name' => '9 (41)', 'position_id' => 26],
            ['name' => '14 (54)', 'position_id' => 27], ['name' => '13 (52)', 'position_id' => 27],
            ['name' => '12 (48)', 'position_id' => 27], ['name' => '10 (44)', 'position_id' => 27],
            ['name' => '9 (41)', 'position_id' => 27], ['name' => '14 (54)', 'position_id' => 28],
            ['name' => '13 (52)', 'position_id' => 28], ['name' => '12 (48)', 'position_id' => 28],
            ['name' => '10 (44)', 'position_id' => 28], ['name' => '9 (41)', 'position_id' => 28],
            ['name' => '14 (54)', 'position_id' => 29], ['name' => '13 (52)', 'position_id' => 29],
            ['name' => '12 (48)', 'position_id' => 29], ['name' => '10 (44)', 'position_id' => 29],
            ['name' => '9 (41)', 'position_id' => 29], ['name' => '14 (54)', 'position_id' => 30],
            ['name' => '13 (52)', 'position_id' => 30], ['name' => '12 (48)', 'position_id' => 30],
            ['name' => '10 (44)', 'position_id' => 30], ['name' => '9 (41)', 'position_id' => 30],
            ['name' => '14 (54)', 'position_id' => 31], ['name' => '13 (52)', 'position_id' => 31],
            ['name' => '12 (48)', 'position_id' => 31], ['name' => '10 (44)', 'position_id' => 31],
            ['name' => '9 (41)', 'position_id' => 31], ['name' => '48', 'position_id' => 31],
            ['name' => '14 (54)', 'position_id' => 32], ['name' => '13 (52)', 'position_id' => 32],
            ['name' => '12 (48)', 'position_id' => 32], ['name' => '10 (44)', 'position_id' => 32],
            ['name' => '8 (40)', 'position_id' => 32], ['name' => '7 (38)', 'position_id' => 32],
            ['name' => '6 (32)', 'position_id' => 32], ['name' => '5 (29)', 'position_id' => 32],
            ['name' => '14 (54)', 'position_id' => 33], ['name' => '13 (52)', 'position_id' => 33],
            ['name' => '12 (48)', 'position_id' => 33], ['name' => '10 (44)', 'position_id' => 33],
            ['name' => '9 (41)', 'position_id' => 33], ['name' => '14 (54)', 'position_id' => 34],
            ['name' => '13 (52)', 'position_id' => 34], ['name' => '12 (48)', 'position_id' => 34],
            ['name' => '10 (44)', 'position_id' => 34], ['name' => '9 (41)', 'position_id' => 34],
            ['name' => '14 (53/54)', 'position_id' => 35], ['name' => '13 (51/52)', 'position_id' => 35],
            ['name' => '12 (47/48)', 'position_id' => 35], ['name' => '10 (43/44)', 'position_id' => 35],
            ['name' => '9 (41/42)', 'position_id' => 35], ['name' => '14 (54)', 'position_id' => 36],
            ['name' => '13 (52)', 'position_id' => 36], ['name' => '12 (48)', 'position_id' => 36],
            ['name' => '10 (44)', 'position_id' => 36], ['name' => '9 (41)', 'position_id' => 36],
            ['name' => '14 (53/54)', 'position_id' => 37], ['name' => '13 (51/52)', 'position_id' => 37],
            ['name' => '12 (47/48)', 'position_id' => 37], ['name' => '10 (43/44)', 'position_id' => 37],
            ['name' => '9 (41/42)', 'position_id' => 37], ['name' => '7 (37/38)', 'position_id' => 37],
            ['name' => '6 (31/32)', 'position_id' => 37], ['name' => '5 (29/30)', 'position_id' => 37],
            ['name' => '3 (25/26)', 'position_id' => 37], ['name' => '2 (21/22)', 'position_id' => 37],
            ['name' => '1 (19)', 'position_id' => 37], ['name' => '14 (54)', 'position_id' => 38],
            ['name' => '13 (52)', 'position_id' => 38], ['name' => '12 (48)', 'position_id' => 38],
            ['name' => '10 (44)', 'position_id' => 38], ['name' => '9 (41)', 'position_id' => 38],
            ['name' => '8 (40)', 'position_id' => 39], ['name' => '7 (38)', 'position_id' => 39],
            ['name' => '6 (32)', 'position_id' => 39], ['name' => '5 (29)', 'position_id' => 39],
            ['name' => '8 (40)', 'position_id' => 40], ['name' => '7 (38)', 'position_id' => 40],
            ['name' => '6 (32)', 'position_id' => 40], ['name' => '5 (29)', 'position_id' => 40],
            ['name' => '8 (40)', 'position_id' => 41], ['name' => '7 (38)', 'position_id' => 41],
            ['name' => '6 (32)', 'position_id' => 41], ['name' => '5 (29)', 'position_id' => 41],
            ['name' => '8 (40)', 'position_id' => 42], ['name' => '7 (38)', 'position_id' => 42],
            ['name' => '6 (32)', 'position_id' => 42], ['name' => '5 (29/30)', 'position_id' => 42],
            ['name' => '8 (40)', 'position_id' => 43], ['name' => '7 (38)', 'position_id' => 43],
            ['name' => '6 (32)', 'position_id' => 43], ['name' => '5 (29)', 'position_id' => 43],
            ['name' => '8 (40)', 'position_id' => 44], ['name' => '7 (38)', 'position_id' => 44],
            ['name' => '6 (32)', 'position_id' => 44], ['name' => '5 (29)', 'position_id' => 44],
            ['name' => '8 (40)', 'position_id' => 45], ['name' => '7 (38)', 'position_id' => 45],
            ['name' => '6 (32)', 'position_id' => 45], ['name' => '5 (29)', 'position_id' => 45],
            ['name' => '8 (40)', 'position_id' => 46], ['name' => '7 (38)', 'position_id' => 46],
            ['name' => '6 (32)', 'position_id' => 46], ['name' => '5 (29)', 'position_id' => 46],
            ['name' => '8 (40)', 'position_id' => 47], ['name' => '7 (38)', 'position_id' => 47],
            ['name' => '6 (32)', 'position_id' => 47], ['name' => '5 (29)', 'position_id' => 47],
            ['name' => '8 (40)', 'position_id' => 48], ['name' => '7 (38)', 'position_id' => 48],
            ['name' => '6 (32)', 'position_id' => 48], ['name' => '5 (29)', 'position_id' => 48],
            ['name' => '8 (40)', 'position_id' => 49], ['name' => '7 (38)', 'position_id' => 49],
            ['name' => '6 (32)', 'position_id' => 49], ['name' => '5 (29)', 'position_id' => 49],
            ['name' => '8 (40)', 'position_id' => 40], // The noted duplicate entry from source
            ['name' => '7 (38)', 'position_id' => 50], ['name' => '6 (32)', 'position_id' => 50],
            ['name' => '5 (29)', 'position_id' => 50], ['name' => '8 (40)', 'position_id' => 51],
            ['name' => '7 (38)', 'position_id' => 51], ['name' => '6 (32)', 'position_id' => 51],
            ['name' => '5 (29)', 'position_id' => 51], ['name' => '8 (40)', 'position_id' => 52],
            ['name' => '7 (38)', 'position_id' => 52], ['name' => '6 (32)', 'position_id' => 52],
            ['name' => '5 (29)', 'position_id' => 52], ['name' => '8 (40)', 'position_id' => 53],
            ['name' => '7 (38)', 'position_id' => 53], ['name' => '6 (32)', 'position_id' => 53],
            ['name' => '5 (29/30)', 'position_id' => 53], ['name' => '2 (22)', 'position_id' => 54],
            ['name' => '1 (19)', 'position_id' => 54], ['name' => '4 (28)', 'position_id' => 55],
            ['name' => '3 (26)', 'position_id' => 55], ['name' => '2 (22)', 'position_id' => 55],
            ['name' => '1 (19)', 'position_id' => 55], ['name' => '4 (28)', 'position_id' => 56],
            ['name' => '3 (26)', 'position_id' => 56], ['name' => '2 (22)', 'position_id' => 56],
            ['name' => '1 (19)', 'position_id' => 56], ['name' => '4 (28)', 'position_id' => 57],
            ['name' => '3 (26)', 'position_id' => 57], ['name' => '2 (22)', 'position_id' => 57],
            ['name' => '1 (19)', 'position_id' => 57], ['name' => '4 (28)', 'position_id' => 58],
            ['name' => '3 (26)', 'position_id' => 58], ['name' => '2 (22)', 'position_id' => 58],
            ['name' => '1 (19)', 'position_id' => 58], ['name' => '4 (28)', 'position_id' => 59],
            ['name' => '3 (26)', 'position_id' => 59], ['name' => '2 (22)', 'position_id' => 59],
            ['name' => '1 (19)', 'position_id' => 59], ['name' => '4 (28)', 'position_id' => 60],
            ['name' => '3 (26)', 'position_id' => 60], ['name' => '2 (22)', 'position_id' => 60],
            ['name' => '1 (19)', 'position_id' => 60], ['name' => '4 (28)', 'position_id' => 61],
            ['name' => '3 (26)', 'position_id' => 61], ['name' => '2 (22)', 'position_id' => 61],
            ['name' => '1 (19)', 'position_id' => 61], ['name' => '4 (28)', 'position_id' => 62],
            ['name' => '3 (26)', 'position_id' => 62], ['name' => '2 (22)', 'position_id' => 62],
            ['name' => '1 (19)', 'position_id' => 62], ['name' => '9 (41)', 'position_id' => 63],
            ['name' => '5 (29)', 'position_id' => 63], ['name' => '1 (19)', 'position_id' => 63],
            ['name' => 'Pelajar Latihan Industri', 'position_id' => 64], ['name' => '1 (19)', 'position_id' => 65],
            ['name' => '2 (22)', 'position_id' => 65], ['name' => '3 (26)', 'position_id' => 65],
            ['name' => '4 (28)', 'position_id' => 65],
        ];

        // MODIFIED: Filter the source data for unique combinations before processing.
        // This programmatically removes the duplicate entry ('8 (40)' for position_id 40)
        // and guards against any other potential duplicates.
        $uniqueGrades = collect($grades)->unique(function (array $item): string {
            return $item['name'].'-'.$item['position_id'];
        });

        $dataToInsert = [];
        foreach ($uniqueGrades as $gradeData) {
            $level = $this->extractLevelFromName($gradeData['name']);
            $dataToInsert[] = [
                'name' => $gradeData['name'],
                'position_id' => $gradeData['position_id'],
                'level' => $level,
                'is_approver_grade' => $level && $level >= 9,
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Chunking the insert for better memory management and to avoid potential query size limits.
        foreach (array_chunk($dataToInsert, 200) as $chunk) {
            Grade::insert($chunk);
        }

        Log::info('Finished seeding '.count($dataToInsert).' unique grades.');
    }

    /**
     * Extracts the primary numeric level from a grade name string.
     * e.g., '14 (54)' -> 54, '9 (41/42)' -> 41, 'JUSA C' -> 56
     */
    private function extractLevelFromName(string $name): ?int
    {
        // Premier grades - highest priority
        if (str_contains($name, 'Menteri')) {
            return 90;
        }
        // Arbitrary high level for political appointees
        if (str_contains($name, 'Timbalan Menteri')) {
            return 80;
        }

        if (str_contains($name, 'TURUS I')) {
            return 76;
        }

        if (str_contains($name, 'TURUS II')) {
            return 74;
        }

        if (str_contains($name, 'TURUS III')) {
            return 72;
        }

        if (str_contains($name, 'JUSA A')) {
            return 70;
        }

        if (str_contains($name, 'JUSA B')) {
            return 68;
        }

        if (str_contains($name, 'JUSA C')) {
            return 66;
        }

        // Extracts number from parenthesis, e.g., (54) or (41/42) -> 41
        if (preg_match('/\((\d+)/', $name, $matches)) {
            return (int) $matches[1];
        }

        // For plain numbers like '48'
        if (is_numeric($name)) {
            return (int) $name;
        }

        return null; // Default for non-standard grades like 'Pelajar Latihan Industri'
    }
}
