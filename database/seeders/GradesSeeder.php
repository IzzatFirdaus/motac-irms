<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradesSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Grades seeding (Revision 3)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('grades')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated grades table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (GradeSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for GradesSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in GradesSeeder.");
        }

        // Based on MyMail form, Gred 9 is a supporting officer grade [cite: 37]
        // Based on ICT Loan Form, Gred 41 is a supporting officer grade [cite: 8]
        // is_approver_grade will be true if the grade level is 9 or above.
        // Specific policies will check actual grade level against configured minimums for different approval types.
        $grades = [
            // Gred Penyokong (MyMail form) - Example Gred 9
            [
                'name' => '9', // Generic Grade 9, can be N9, W9 etc. in practice.
                'level' => 9,
                'is_approver_grade' => true, // Can approve (e.g., email applications)
                'min_approval_grade_id' => null, // Not relevant if policies check level
            ],
            // Support Group
            ['name' => 'N19', 'level' => 19, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N22', 'level' => 22, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N26', 'level' => 26, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N29', 'level' => 29, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N32', 'level' => 32, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N36', 'level' => 36, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT19', 'level' => 19, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            // FT29 from previous, likely also approver
            ['name' => 'FT29', 'level' => 29, 'is_approver_grade' => true, 'min_approval_grade_id' => null],


            // Management and Professional (Approvers >= 41 for ICT Loans)
            ['name' => 'N41', 'level' => 41, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N44', 'level' => 44, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N48', 'level' => 48, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N52', 'level' => 52, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'N54', 'level' => 54, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT41', 'level' => 41, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT44', 'level' => 44, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT48', 'level' => 48, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT52', 'level' => 52, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'FT54', 'level' => 54, 'is_approver_grade' => true, 'min_approval_grade_id' => null],

            // Premier Grades (JUSA, Turus - All Approvers)
            ['name' => 'JUSA C', 'level' => 56, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'JUSA B', 'level' => 58, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'JUSA A', 'level' => 60, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'TURUS III', 'level' => 62, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'TURUS II', 'level' => 64, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            ['name' => 'TURUS I', 'level' => 66, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
             // From MyMail dropdown grade list
            ['name' => 'Menteri', 'level' => 70, 'is_approver_grade' => true, 'min_approval_grade_id' => null], // Highest level
            ['name' => 'Timbalan Menteri', 'level' => 68, 'is_approver_grade' => true, 'min_approval_grade_id' => null],
            // Adding other values from the extensive MyMail Grade dropdown for completeness if they are distinct
            // The 'class' attribute in MyMail's grade options seems to link to Jawatan ID, not relevant for grade 'name' directly
            // Seeding all 282 grade variations from that dropdown might be excessive if 'level' and broad 'name' (like N41, FT41) are primary.
            // For now, the list above covers common grades. The system should allow adding more grades via UI.
        ];

        Log::info('Creating specific grades (Revision 3)...');
        foreach ($grades as $gradeData) {
            // Update is_approver_grade based on level >= 9
            $gradeData['is_approver_grade'] = ($gradeData['level'] >= 9);

            Grade::firstOrCreate(
                ['name' => $gradeData['name']], // Unique by name
                array_merge($gradeData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }
        Log::info('Ensured specific grades exist.');

        // The min_approval_grade_id field on the grades table might be less relevant
        // if policies directly use configured grade levels (e.g., from config/motac.php).
        // If it were to be used, it would point to a 'Grade' record that represents the minimum.
        // For example, find Grade '9' and set its ID to other lower grades if that was the logic.
        // However, since G9 itself is an approver grade, this field is not set for G9.
        // This part is simplified as policies will likely check the user's grade level directly.
        $grade9Instance = Grade::where('level', 9)->orderBy('id')->first();
        if ($grade9Instance) {
            Grade::where('is_approver_grade', true)
                // ->where('level', '>=', $grade9Instance->level) // Not strictly necessary as already set by level check
                // ->whereNull('min_approval_grade_id') // Only update if not set
                // This logic is a bit circular if min_approval_grade_id points to itself.
                // It's better handled by policies checking numeric levels.
                // No update needed here for min_approval_grade_id based on current design.
                ->update(['updated_by' => $auditUserId]); // Just ensure updated_by is set.
            Log::info("Updated 'updated_by' for approver grades using User ID {$auditUserId}. min_approval_grade_id logic relies on policies.");
        } else {
            Log::warning("Grade with level 9 not found. Check seeding for Gred '9'.");
        }


        Log::info('Grades seeding complete (Revision 3).');
    }
}
