<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates critical roles and users required for the system to function correctly,
     * such as administrators, BPM staff, and users with specific grades for approval workflows.
     */
    public function run(): void
    {
        Log::info('AdminUserSeeder: Starting the seeding of critical roles and administrative users.');

        // Use the first user (typically the very first admin) for audit columns.
        $auditUserId = User::orderBy('id')->value('id');

        // --- 1. Ensure all necessary Roles exist ---
        $roles = ['Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD', 'User'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        Log::info('AdminUserSeeder: Ensured all core application roles exist.');

        // --- 2. Ensure a default Department exists for seeded users ---
        $defaultDept = Department::updateOrCreate(
            ['code' => 'BPM'],
            [
                'name'        => 'Bahagian Pengurusan Maklumat (BPM MOTAC)',
                'branch_type' => Department::BRANCH_TYPE_HQ,
                'is_active'   => true,
                'created_by'  => $auditUserId,
                'updated_by'  => $auditUserId,
            ]
        );

        // --- 3. Ensure Critical Grades for Approval Workflow Exist ---
        config('motac.approval.min_loan_support_grade_level', 41);

        $gradeF41 = Grade::updateOrCreate(
            ['name' => 'F41'],
            ['level' => 41, 'is_approver_grade' => true, 'description' => 'Pegawai Teknologi Maklumat']
        );
        $gradeF44 = Grade::updateOrCreate(
            ['name' => 'F44'],
            ['level' => 44, 'is_approver_grade' => true, 'description' => 'Pegawai Teknologi Maklumat Kanan']
        );
        Log::info('AdminUserSeeder: Ensured grades F41 (Level 41) and F44 (Level 44) exist for testing approval logic.');

        // --- 4. Ensure related Positions exist ---
        $defaultPos  = Position::updateOrCreate(['name' => 'Pegawai Teknologi Maklumat Sistem'], ['grade_id' => $gradeF41->id, 'is_active' => true]);
        $approverPos = Position::updateOrCreate(['name' => 'Ketua Unit Aplikasi'], ['grade_id' => $gradeF44->id, 'is_active' => true]);
        Log::info('AdminUserSeeder: Ensured default and approver-level positions exist.');

        // --- 5. Define and Seed Users ---
        $defaultPassword = Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Motac.1234'));
        $now             = Carbon::now();

        // List of emails that are considered as seeded admins/developers/maintainers.
        // These users should always have both 'Admin' and 'BPM Staff' roles for full system access.
        $seededAdminEmails = [
            env('ADMIN_EMAIL', 'admin@motac.gov.my'),
            // Add more emails here if you add more seeded admins/developers in the future.
            'izzatfirdaus@motac.gov.my', // system developer/maintainer
        ];

        $usersData = [
            // Admin user with a high grade, making them eligible as a supporting officer.
            [
                'role_name'             => 'Admin', 'name' => 'Pentadbir Sistem Utama', 'email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'),
                'identification_number' => '800101010001', 'grade_id' => $gradeF44->id, 'position_id' => $approverPos->id,
            ],
            // BPM Staff for handling issuance and returns. Grade is sufficient for loan approval scenarios.
            [
                'role_name'             => 'BPM Staff', 'name' => 'Staf Sokongan BPM', 'email' => 'bpmstaff@motac.gov.my',
                'identification_number' => '850202020002', 'grade_id' => $gradeF41->id, 'position_id' => $defaultPos->id,
            ],
            // IT Admin for email provisioning tasks.
            [
                'role_name'             => 'IT Admin', 'name' => 'Pegawai IT Admin', 'email' => 'itadmin@motac.gov.my',
                'identification_number' => '820303030003', 'grade_id' => $gradeF41->id, 'position_id' => $defaultPos->id,
            ],
            // A dedicated 'Approver' user with a high grade, suitable for testing approval flows.
            [
                'role_name'             => 'Approver', 'name' => 'Pegawai Penyokong (Approver)', 'email' => 'approver@motac.gov.my',
                'identification_number' => '780505050005', 'grade_id' => $gradeF44->id, 'position_id' => $approverPos->id,
            ],
            // A standard user with a grade level that does not meet the approval threshold.
            [
                'role_name'             => 'User', 'name' => 'Pengguna Biasa Sistem', 'email' => 'pengguna01@motac.gov.my',
                'identification_number' => '900404040004',
                'grade_id'              => Grade::firstOrCreate(['name' => 'N19'], ['level' => 19])->id,
                'position_id'           => Position::firstOrCreate(['name' => 'Pembantu Tadbir'])->id,
            ],
            // System developer/maintainer: must always be both Admin and BPM Staff
            [
                'role_name'             => 'Admin', 'name' => 'Izzat Firdaus (System Developer)', 'email' => 'izzatfirdaus@motac.gov.my',
                'identification_number' => '980328145171', 'grade_id' => $gradeF44->id, 'position_id' => $approverPos->id,
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'                  => $data['name'],
                    'password'              => $defaultPassword,
                    'identification_number' => $data['identification_number'],
                    'department_id'         => $defaultDept->id,
                    'position_id'           => $data['position_id'],
                    'grade_id'              => $data['grade_id'],
                    'status'                => User::STATUS_ACTIVE,
                    'email_verified_at'     => $now,
                    'created_by'            => $auditUserId,
                    'updated_by'            => $auditUserId,
                ]
            );

            // Assign the specified primary role to the user
            if (! $user->hasRole($data['role_name'])) {
                $user->assignRole($data['role_name']);
            }

            // Ensure seeded admin/dev/maintainer always have both 'Admin' and 'BPM Staff' roles
            if (in_array($user->email, $seededAdminEmails, true)) {
                if (! $user->hasRole('Admin')) {
                    $user->assignRole('Admin');
                }
                if (! $user->hasRole('BPM Staff')) {
                    $user->assignRole('BPM Staff');
                }
                Log::info("AdminUserSeeder: Ensured {$user->email} has both Admin and BPM Staff roles.");
            }

            Log::info(sprintf("AdminUserSeeder: Processed user '%s' (%s). Assigned Role: '%s'. Assigned Grade Level: %s.",
                $user->name, $user->email, $data['role_name'], $user->grade?->level));
        }

        Log::info('AdminUserSeeder: Seeding of critical roles and administrative users has been completed successfully.');
    }
}
