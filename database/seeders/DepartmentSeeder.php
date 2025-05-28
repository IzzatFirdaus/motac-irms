<?php

namespace Database\Seeders;

use App\Models\Department; // Ensure this matches your Department model namespace and name
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Corrected class name to match typical PSR-4 autoloading with a singular filename
class DepartmentSeeder extends Seeder // Changed from DepartmentsSeeder to DepartmentSeeder
{
    public function run(): void
    {
        Log::info('Starting Department seeding (Revision 3 - Corrected)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated departments table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (DeptSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for DepartmentSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in DepartmentSeeder.");
        }

        $departments = [
            // Headquarters (Ibu Pejabat) - Using Department::BRANCH_TYPE_HQ from your model
            ['name' => 'Akaun', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'AKN', 'description' => 'Bahagian Akaun.', 'is_active' => true],
            ['name' => 'Audit Dalam', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'AUDIT', 'description' => 'Unit Audit Dalam.', 'is_active' => true],
            ['name' => 'Dasar Kebudayaan', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'DSK', 'description' => 'Bahagian Dasar Kebudayaan.', 'is_active' => true],
            ['name' => 'Dasar Pelancongan dan Hubungan Antarabangsa', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'DPHA', 'description' => 'Bahagian Dasar Pelancongan dan Hubungan Antarabangsa.', 'is_active' => true],
            ['name' => 'Hubungan Antarabangsa Kebudayaan', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'HAK', 'description' => 'Bahagian Hubungan Antarabangsa Kebudayaan.', 'is_active' => true],
            ['name' => 'Integriti', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'INTG', 'description' => 'Unit Integriti.', 'is_active' => true],
            ['name' => 'Kewangan', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'KEW', 'description' => 'Bahagian Kewangan.', 'is_active' => true],
            ['name' => 'Komunikasi Korporat', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'KOMKOR', 'description' => 'Unit Komunikasi Korporat.', 'is_active' => true],
            ['name' => 'KPI', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'KPIUNIT', 'description' => 'Unit KPI.', 'is_active' => true],
            ['name' => 'OSC MM2H', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'MM2H', 'description' => 'Pusat Sehenti Malaysia My Second Home.', 'is_active' => true],
            ['name' => 'Pejabat Ketua Setiausaha (KSU)', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PKSU', 'description' => 'Pejabat Ketua Setiausaha.', 'is_active' => true],
            ['name' => 'Pejabat Menteri', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PMEN', 'description' => 'Pejabat Menteri.', 'is_active' => true],
            ['name' => 'Pejabat Timbalan Ketua Setiausaha (Kebudayaan)', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PTKSK', 'description' => 'Pejabat Timbalan Ketua Setiausaha (Kebudayaan).', 'is_active' => true],
            ['name' => 'Pejabat Timbalan Ketua Setiausaha (Pelancongan)', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PTKSP', 'description' => 'Pejabat Timbalan Ketua Setiausaha (Pelancongan).', 'is_active' => true],
            ['name' => 'Pejabat Timbalan Ketua Setiausaha (Pengurusan)', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PTKSUR', 'description' => 'Pejabat Timbalan Ketua Setiausaha (Pengurusan).', 'is_active' => true],
            ['name' => 'Pejabat Timbalan Menteri', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PTM', 'description' => 'Pejabat Timbalan Menteri.', 'is_active' => true],
            ['name' => 'Pelesenan dan Penguatkuasaan Pelancongan', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PPP', 'description' => 'Bahagian Pelesenan dan Penguatkuasaan Pelancongan.', 'is_active' => true],
            ['name' => 'Pembangunan Industri', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PBI', 'description' => 'Bahagian Pembangunan Industri.', 'is_active' => true],
            ['name' => 'Pembangunan Prasarana', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PBPR', 'description' => 'Bahagian Pembangunan Prasarana.', 'is_active' => true],
            ['name' => 'Pengurusan Acara', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PGA', 'description' => 'Bahagian Pengurusan Acara.', 'is_active' => true],
            ['name' => 'Bahagian Pengurusan Maklumat', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'BPM', 'description' => 'Bahagian Pengurusan Maklumat. Responsible for IT infrastructure, system development, and ICT support.', 'is_active' => true],
            ['name' => 'Bahagian Pengurusan Sumber Manusia', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'BSM', 'description' => 'Bahagian Pengurusan Sumber Manusia. Handles employee relations, recruitment, training, and benefits.', 'is_active' => true],
            ['name' => 'Pentadbiran', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'PENT', 'description' => 'Bahagian Pentadbiran.', 'is_active' => true],
            ['name' => 'Perundangan', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'UU', 'description' => 'Unit Perundangan.', 'is_active' => true],
            ['name' => 'Sekretariat Visit Malaysia', 'branch_type' => Department::BRANCH_TYPE_HQ, 'code' => 'SVM', 'description' => 'Sekretariat Visit Malaysia.', 'is_active' => true],

            // State Offices (Pejabat Negeri) - Using Department::BRANCH_TYPE_STATE from your model
            ['name' => 'MOTAC Johor', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'JHR', 'description' => 'Pejabat MOTAC Negeri Johor.', 'is_active' => true],
            ['name' => 'MOTAC Kedah', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'KDH', 'description' => 'Pejabat MOTAC Negeri Kedah.', 'is_active' => true],
            ['name' => 'MOTAC Kelantan', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'KTN', 'description' => 'Pejabat MOTAC Negeri Kelantan.', 'is_active' => true],
            ['name' => 'MOTAC Melaka', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'MLK', 'description' => 'Pejabat MOTAC Negeri Melaka.', 'is_active' => true],
            ['name' => 'MOTAC N. Sembilan', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'NSN', 'description' => 'Pejabat MOTAC Negeri Sembilan.', 'is_active' => true],
            ['name' => 'MOTAC Pahang', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'PHG', 'description' => 'Pejabat MOTAC Negeri Pahang.', 'is_active' => true],
            ['name' => 'MOTAC Perak', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'PRK', 'description' => 'Pejabat MOTAC Negeri Perak.', 'is_active' => true],
            ['name' => 'MOTAC Perlis', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'PLS', 'description' => 'Pejabat MOTAC Negeri Perlis.', 'is_active' => true],
            ['name' => 'MOTAC Pulau Pinang', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'PNG', 'description' => 'Pejabat MOTAC Negeri Pulau Pinang.', 'is_active' => true],
            ['name' => 'MOTAC Sabah', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'SBH', 'description' => 'Pejabat MOTAC Negeri Sabah.', 'is_active' => true],
            ['name' => 'MOTAC Sarawak', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'SWK', 'description' => 'Pejabat MOTAC Negeri Sarawak.', 'is_active' => true],
            ['name' => 'MOTAC Selangor', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'SGR', 'description' => 'Pejabat MOTAC Negeri Selangor.', 'is_active' => true],
            ['name' => 'MOTAC Terengganu', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'TRG', 'description' => 'Pejabat MOTAC Negeri Terengganu.', 'is_active' => true],
            ['name' => 'MOTAC WP Kuala Lumpur / Putrajaya', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'KLP', 'description' => 'Pejabat MOTAC WP Kuala Lumpur / Putrajaya.', 'is_active' => true],
            ['name' => 'MOTAC WP Labuan', 'branch_type' => Department::BRANCH_TYPE_STATE, 'code' => 'LBN', 'description' => 'Pejabat MOTAC WP Labuan.', 'is_active' => true],
        ];

        Log::info('Creating/updating specific MOTAC departments from the defined list (Revision 3 - Corrected)...');
        foreach ($departments as $departmentData) {
            $departmentData['created_by'] = $auditUserId;
            $departmentData['updated_by'] = $auditUserId;
            $departmentData['head_of_department_id'] = $departmentData['head_of_department_id'] ?? null;

            Department::firstOrCreate(
                ['code' => $departmentData['code']],
                $departmentData
            );
        }
        Log::info('Finished creating/updating specific MOTAC departments.');

        $targetDepartmentCount = count($departments);
        $currentDepartmentCount = Department::count();

        if ($currentDepartmentCount < $targetDepartmentCount) {
            Log::warning("Current department count {$currentDepartmentCount} is less than defined list count {$targetDepartmentCount}. Some defined departments might not have been created due to issues (e.g. duplicate codes if logic was flawed).");
        }

        Log::info('Department seeding complete (Revision 3 - Corrected).');
    }
}
