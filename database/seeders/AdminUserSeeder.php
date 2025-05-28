<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User; // User model is key here
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role; // Assuming Spatie roles are used

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('AdminUserSeeder: Starting Admin User seeding...');

        // Ensure roles exist, or run RoleAndPermissionSeeder first.
        // This check is illustrative; actual role creation should be robust.
        if (Role::count() === 0) {
            Log::warning('AdminUserSeeder: No roles found. Consider running RoleAndPermissionSeeder first.');
            // Optionally: $this->call(RoleAndPermissionSeeder::class);
            // if (Role::count() === 0) {
            //     Log::error("AdminUserSeeder: Role seeding failed or roles still not found. AdminUserSeeder cannot assign roles effectively.");
            //     return; // Stop if roles are critical and missing
            // }
        }

        // Attempt to get a valid user ID for auditing. If no users exist, it might be null initially.
        $firstUser = User::orderBy('id')->first();
        $auditUserId = $firstUser?->id;

        // Default Department, Grade, Position
        // Using updateOrCreate to prevent duplicates and ensure required fields for creation.
        $defaultDept = Department::updateOrCreate(
            ['name' => 'Bahagian Pengurusan Maklumat (BPM MOTAC)'], // More specific name
            [
                'code' => 'BPM',
                'branch_type' => Department::BRANCH_TYPE_HQ, // Using model constant
                // 'is_active' => true, // is_active not in Department model schema provided previously
                'created_by' => $auditUserId, // Nullable, will be null if no users exist yet
                'updated_by' => $auditUserId, // Nullable
            ]
        );
        $defaultGrade = Grade::updateOrCreate(
            ['name' => 'F41'], // Example IT Grade
            [
                'level' => 41,
                'is_approver_grade' => true, // IT grades might be approvers for certain things
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]
        );
        $defaultPos = Position::updateOrCreate(
            ['name' => 'Pegawai Teknologi Maklumat'], // Example position
            [
                'grade_id' => $defaultGrade->id,
                // 'department_id' => $defaultDept->id, // Positions are not directly tied to departments in current model
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]
        );

        $usersData = [
            [
                'role_name' => 'Admin', // Spatie role name
                'name' => 'Pentadbir Sistem Utama', // This is the full name
                'email' => 'admin@motac.gov.my', // This is the primary login email (personal_email)
                'password' => 'MotacAdminPa$$wOrd', // Stronger default password
                'title' => 'Encik',
                'identification_number' => '800101010001', // NRIC without hyphens for consistency
                'mobile_number' => '0191234567', // Malaysian format
                'personal_email' => 'admin@motac.gov.my', // Should match 'email' if it's the primary
                'motac_email' => 'admin.it@motac.gov.my', // Official provisioned email
                'user_id_assigned' => 'MOTACADM001',
                'service_status' => User::SERVICE_STATUS_TETAP,       // CORRECTED CONSTANT
                'appointment_type' => User::APPOINTMENT_TYPE_BAHARU,    // CORRECTED CONSTANT
                'status' => User::STATUS_ACTIVE,
                'is_admin' => true, // Direct flag
                'is_bpm_staff' => true, // Admins can also be BPM staff
                'level' => '16',
                'department_id' => $defaultDept->id,
                'position_id' => $defaultPos->id,
                'grade_id' => $defaultGrade->id,
            ],
            [
                'role_name' => 'BPM Staff',
                'name' => 'Staf Sokongan BPM',
                'email' => 'bpmstaff@motac.gov.my',
                'password' => 'MotacBPMSt@ff1',
                'title' => 'Puan',
                'identification_number' => '850202020002',
                'mobile_number' => '0122345678',
                'personal_email' => 'bpmstaff@motac.gov.my',
                'motac_email' => 'bpm.staff@motac.gov.my',
                'user_id_assigned' => 'MOTACBPM001',
                'service_status' => User::SERVICE_STATUS_KONTRAK_MYSTEP, // CORRECTED CONSTANT
                'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN,   // CORRECTED CONSTANT
                'status' => User::STATUS_ACTIVE,
                'is_admin' => false,
                'is_bpm_staff' => true,
                'level' => '10',
                'department_id' => $defaultDept->id,
                'position_id' => $defaultPos->id,
                'grade_id' => $defaultGrade->id,
            ],
            [
                'role_name' => 'Approver', // General Approver role
                'name' => 'Ketua Unit ICT',
                'email' => 'ku.ict@motac.gov.my',
                'password' => 'MotacApprover1!',
                'title' => 'Dr.',
                'identification_number' => '750303030003',
                'mobile_number' => '0133456789',
                'personal_email' => 'ku.ict@motac.gov.my',
                'motac_email' => 'ketuaunit.ict@motac.gov.my',
                'user_id_assigned' => 'MOTACKU001',
                'service_status' => User::SERVICE_STATUS_TETAP,          // CORRECTED CONSTANT
                'appointment_type' => User::APPOINTMENT_TYPE_BAHARU,       // CORRECTED CONSTANT
                'status' => User::STATUS_ACTIVE,
                'is_admin' => false,
                'is_bpm_staff' => false,
                'level' => '14',
                'department_id' => $defaultDept->id, // Assuming HOD is in BPM for ICT matters
                'position_id' => $defaultPos->id, // Example, assign a Head of Unit position
                'grade_id' => $defaultGrade->id, // Example
            ],
            [
                'role_name' => 'User', // Standard User role
                'name' => 'Pengguna Biasa Sistem',
                'email' => 'pengguna01@motac.gov.my',
                'password' => 'MotacUserP@ss1',
                'title' => 'Cik',
                'identification_number' => '900404040004',
                'mobile_number' => '0144567890',
                'personal_email' => 'pengguna01@motac.gov.my',
                // 'motac_email' will be blank initially for normal users until provisioned
                // 'user_id_assigned' will be blank initially
                'service_status' => User::SERVICE_STATUS_PELAJAR_INDUSTRI, // CORRECTED CONSTANT
                'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN,    // CORRECTED CONSTANT
                'status' => User::STATUS_ACTIVE,
                'is_admin' => false,
                'is_bpm_staff' => false,
                'level' => 'Latihan Industri',
                'department_id' => $defaultDept->id, // Example
                'position_id' => $defaultPos->id, // Example
                'grade_id' => $defaultGrade->id, // Example
            ],
        ];

        $now = Carbon::now();

        foreach ($usersData as $data) {
            $userDataToCreate = [
                'name' => $data['name'],
                'email' => $data['email'], // Primary login email
                'password' => Hash::make($data['password']),
                'title' => $data['title'],
                'identification_number' => $data['identification_number'],
                'mobile_number' => $data['mobile_number'],
                'personal_email' => $data['personal_email'], // Should ideally match 'email'
                'motac_email' => $data['motac_email'] ?? null,
                'user_id_assigned' => $data['user_id_assigned'] ?? null,
                'service_status' => $data['service_status'],
                'appointment_type' => $data['appointment_type'],
                'status' => $data['status'],
                'is_admin' => $data['is_admin'],
                'is_bpm_staff' => $data['is_bpm_staff'],
                'level' => $data['level'],
                'email_verified_at' => $now, // Auto-verify seeded users
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'grade_id' => $data['grade_id'],
                // Blameable fields will be set by BlameableObserver IF a user is authenticated
                // during seeding. If not, they might remain null.
                // For seeders, explicitly setting them if $auditUserId is available is safer.
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ];

            $user = User::firstOrCreate(['email' => $data['email']], $userDataToCreate);

            // If this is the very first user created, their created_by might be null.
            // We can self-assign it or leave it null if observer doesn't run/no auth user.
            if ($auditUserId === null && $user->wasRecentlyCreated) {
                $auditUserId = $user->id; // Use this first created user for subsequent audit IDs
                // Optionally update the first user's audit fields if desired
                if ($user->created_by === null) {
                    $user->created_by = $auditUserId;
                    $user->updated_by = $auditUserId;
                    $user->saveQuietly();
                }
            } elseif ($user->wasRecentlyCreated && $user->created_by === null && $auditUserId !== null) {
                // For subsequent users, if observer didn't set it due to no auth, set it now
                $user->created_by = $auditUserId;
                $user->updated_by = $auditUserId;
                $user->saveQuietly();
            }

            $role = Role::where('name', $data['role_name'])->first();
            if ($role) {
                if (! $user->hasRole($data['role_name'])) {
                    $user->assignRole($data['role_name']);
                    Log::info("AdminUserSeeder: Assigned role '{$data['role_name']}' to user {$user->email}");
                }
            } else {
                Log::warning("AdminUserSeeder: Role '{$data['role_name']}' not found for user {$user->email}. Ensure RoleAndPermissionSeeder has run and defined this role.");
            }
            Log::info("AdminUserSeeder: Processed user {$data['email']}.");
        }
        Log::info('AdminUserSeeder: Admin User seeding complete.');
    }
}
