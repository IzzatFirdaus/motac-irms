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
  public function run(): void
  {
    Log::info('AdminUserSeeder: Starting Admin User seeding (Corrected for Department code)...');

    $auditUserId = null;
    if (User::count() > 0) {
      $firstUser = User::orderBy('id')->first();
      $auditUserId = $firstUser?->id;
    } elseif (class_exists(User::class) && method_exists(User::class, 'factory')) {
      try {
        // Ensure UserFactory is corrected to not use 'current_team_id' if it causes issues
        $fallbackAuditUser = User::factory()->create([
          'name' => 'Audit Fallback (AdminSeeder)',
          'email' => 'audit_fallback_adminseeder@motac.gov.my',
        ]);
        $auditUserId = $fallbackAuditUser->id;
        Log::info("AdminUserSeeder: Created a fallback audit user with ID {$auditUserId}.");
      } catch (\Exception $e) {
        Log::error("AdminUserSeeder: Failed to create fallback audit user via factory: " . $e->getMessage());
      }
    }

    if ($auditUserId) {
      Log::info("AdminUserSeeder: Using User ID {$auditUserId} for initial audit columns where applicable.");
    } else {
      Log::warning("AdminUserSeeder: No existing users found and fallback audit user could not be created. Audit fields might be null.");
    }

    $defaultDept = Department::updateOrCreate(
      ['code' => 'BPM'], // [cite: 97]
      [
        'name' => 'Bahagian Pengurusan Maklumat (BPM MOTAC)', // [cite: 97]
        'branch_type' => Department::BRANCH_TYPE_HQ, // [cite: 97]
        'description' => 'Bahagian Pengurusan Maklumat Utama Sistem MOTAC.', // [cite: 97]
        'is_active' => true, // [cite: 97]
        'created_by' => $auditUserId, // [cite: 92]
        'updated_by' => $auditUserId, // [cite: 92]
      ]
    );
    Log::info("AdminUserSeeder: Ensured default department '{$defaultDept->name}' (Code: {$defaultDept->code}) exists.");

    $approverGrade = Grade::updateOrCreate(
      ['name' => 'F41'], // [cite: 100]
      [
        'level' => 41, // [cite: 100]
        'is_approver_grade' => true, // [cite: 100]
        'created_by' => $auditUserId, // [cite: 92]
        'updated_by' => $auditUserId, // [cite: 92]
      ]
    );
    Log::info("AdminUserSeeder: Ensured approver grade '{$approverGrade->name}' exists.");


    $defaultGrade = Grade::updateOrCreate(
      ['name' => 'F41'], // This will likely update the same record as $approverGrade if name is unique key [cite: 100]
      [
        'level' => 41, // [cite: 100]
        'is_approver_grade' => true, // [cite: 100]
        'created_by' => $auditUserId, // [cite: 92]
        'updated_by' => $auditUserId, // [cite: 92]
      ]
    );
    Log::info("AdminUserSeeder: Ensured default grade '{$defaultGrade->name}' exists.");

    $defaultPos = Position::updateOrCreate(
      ['name' => 'Pegawai Teknologi Maklumat Sistem'], // [cite: 99]
      [
        'description' => 'Pegawai Teknologi Maklumat Asas untuk pengurusan sistem.', // [cite: 99]
        'grade_id' => $defaultGrade->id, // [cite: 99]
        'is_active' => true, // [cite: 99]
        'created_by' => $auditUserId, // [cite: 92]
        'updated_by' => $auditUserId, // [cite: 92]
      ]
    );
    Log::info("AdminUserSeeder: Ensured default position '{$defaultPos->name}' exists.");

    $usersData = [
      [
        'role_name' => 'Admin', // [cite: 8]
        'name' => 'Pentadbir Sistem Utama',
        'email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'),
        'password' => env('ADMIN_PASSWORD', 'Motac.1234'),
        'title' => User::TITLE_ENCIK ?? 'Encik', // [cite: 94]
        'identification_number' => '800101010001', // [cite: 94]
        'mobile_number' => '0191234567', // [cite: 94]
        'personal_email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'), // [cite: 94]
        'motac_email' => 'admin.spsb@motac.gov.my', // [cite: 94]
        'user_id_assigned' => 'MOTACADM001', // [cite: 94]
        'service_status' => User::SERVICE_STATUS_TETAP ?? '1', // [cite: 94]
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU ?? '1', // [cite: 94]
        'status' => User::STATUS_ACTIVE ?? 'active', // [cite: 94]
        'level' => '16', // This is a user-level field, distinct from grade->level [cite: 94]
        'department_id' => $defaultDept->id, // [cite: 94]
        'position_id' => $defaultPos->id, // [cite: 94]
        'grade_id' => $defaultGrade->id, // Will assign grade with level 41 [cite: 94]
      ],
      [
        'role_name' => 'BPM Staff', // [cite: 8]
        'name' => 'Staf Sokongan BPM',
        'email' => 'bpmstaff@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_PUAN ?? 'Puan', // [cite: 94]
        'identification_number' => '850202020002', // [cite: 94]
        'mobile_number' => '0122345678', // [cite: 94]
        'personal_email' => 'bpmstaff@motac.gov.my', // [cite: 94]
        'motac_email' => 'bpm.staff.spsb@motac.gov.my', // [cite: 94]
        'user_id_assigned' => 'MOTACBPM001', // [cite: 94]
        'service_status' => User::SERVICE_STATUS_KONTRAK_MYSTEP ?? '2', // [cite: 94]
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3', // [cite: 94]
        'status' => User::STATUS_ACTIVE ?? 'active', // [cite: 94]
        'level' => '10', // [cite: 94]
        'department_id' => $defaultDept->id, // [cite: 94]
        'position_id' => $defaultPos->id, // [cite: 94]
        'grade_id' => $defaultGrade->id, // Will assign grade with level 41 [cite: 94]
      ],
      [
        'role_name' => 'IT Admin', // [cite: 8]
        'name' => 'Pegawai IT Admin',
        'email' => 'itadmin@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_ENCIK ?? 'Encik', // [cite: 94]
        'identification_number' => '820303030003', // [cite: 94]
        'mobile_number' => '0173456789', // [cite: 94]
        'personal_email' => 'itadmin@motac.gov.my', // [cite: 94]
        'motac_email' => 'it.admin.spsb@motac.gov.my', // [cite: 94]
        'user_id_assigned' => 'MOTACIT001', // [cite: 94]
        'service_status' => User::SERVICE_STATUS_TETAP ?? '1', // [cite: 94]
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU ?? '1', // [cite: 94]
        'status' => User::STATUS_ACTIVE ?? 'active', // [cite: 94]
        'level' => '12', // [cite: 94]
        'department_id' => $defaultDept->id, // [cite: 94]
        'position_id' => $defaultPos->id, // [cite: 94]
        'grade_id' => $defaultGrade->id, // Will assign grade with level 41 [cite: 94]
      ],
      [
        'role_name' => 'Approver',
        'name' => 'Pegawai Penyokong (Approver)',
        'email' => 'approver@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_PUAN ?? 'Puan', // [cite: 94]
        'identification_number' => '780505050005', // [cite: 94]
        'mobile_number' => '01156789012', // [cite: 94]
        'personal_email' => 'approver@motac.gov.my', // [cite: 94]
        'motac_email' => 'approver.spsb@motac.gov.my', // [cite: 94]
        'user_id_assigned' => 'MOTACAPP001', // [cite: 94]
        'service_status' => User::SERVICE_STATUS_TETAP ?? '1', // [cite: 94]
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU ?? '1', // [cite: 94]
        'status' => User::STATUS_ACTIVE ?? 'active', // [cite: 94]
        'level' => '14', // [cite: 94]
        'department_id' => $defaultDept->id, // [cite: 94]
        'position_id' => $defaultPos->id, // [cite: 94]
        'grade_id' => $approverGrade->id, // Will assign grade with level 41 [cite: 94]
      ],
      [
        'role_name' => 'User',
        'name' => 'Pengguna Biasa Sistem',
        'email' => 'pengguna01@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_CIK ?? 'Cik', // [cite: 94]
        'identification_number' => '900404040004', // [cite: 94]
        'mobile_number' => '0144567890', // [cite: 94]
        'personal_email' => 'pengguna01@motac.gov.my', // [cite: 94]
        'motac_email' => null, // [cite: 94]
        'user_id_assigned' => null, // [cite: 94]
        'service_status' => User::SERVICE_STATUS_PELAJAR_INDUSTRI ?? '3', // [cite: 94, 187]
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3', // [cite: 94]
        'status' => User::STATUS_ACTIVE ?? 'active', // [cite: 94]
        'level' => 'LI', // [cite: 94]
        'department_id' => $defaultDept->id, // [cite: 94]
        'position_id' => Position::where('name', 'Pelajar Latihan Industri')->first()?->id ?? $defaultPos->id, // [cite: 94, 268]
        'grade_id' => Grade::where('name', 'PELATIH')->first()?->id ?? $defaultGrade->id, // May get grade level 41 if 'PELATIH' not found [cite: 94]
      ],
    ];

    $now = Carbon::now();

    foreach ($usersData as $data) {
      $userDataValues = [
        'name' => $data['name'],
        'password' => Hash::make($data['password']),
        'title' => $data['title'], // [cite: 94]
        'identification_number' => $data['identification_number'], // [cite: 94]
        'mobile_number' => $data['mobile_number'], // [cite: 94]
        'personal_email' => $data['personal_email'], // [cite: 94]
        'motac_email' => $data['motac_email'] ?? null, // [cite: 94]
        'user_id_assigned' => $data['user_id_assigned'] ?? null, // [cite: 94]
        'service_status' => $data['service_status'], // [cite: 94]
        'appointment_type' => $data['appointment_type'], // [cite: 94]
        'status' => $data['status'], // [cite: 94]
        'level' => $data['level'], // This is the User's 'level' field, not related to Grade's 'level' [cite: 94]
        'email_verified_at' => $now, // [cite: 95]
        'department_id' => $data['department_id'], // [cite: 94]
        'position_id' => $data['position_id'], // [cite: 94]
        'grade_id' => $data['grade_id'], // This carries the ID of the Grade [cite: 94]
        'created_by' => $auditUserId, // [cite: 92]
        'updated_by' => $auditUserId, // [cite: 92]
      ];

      $user = User::updateOrCreate(['email' => $data['email']], $userDataValues);

      if ($auditUserId === null && $user->wasRecentlyCreated) {
        $auditUserId = $user->id;
        Log::info("AdminUserSeeder: Fallback audit user ID set to {$auditUserId} from first created user: {$user->email}.");
        if ($user->created_by === null) {
          $user->created_by = $auditUserId;
          $user->updated_by = $auditUserId;
          $user->saveQuietly();
        }
        // Ensure related models also get audit IDs if created in this initial pass
        if ($defaultDept->created_by === null && $auditUserId) {
          $defaultDept->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
        };
        if ($defaultGrade->created_by === null && $auditUserId) {
          $defaultGradeToUpdate = Grade::find($defaultGrade->id);
          if ($defaultGradeToUpdate && $defaultGradeToUpdate->created_by === null) {
            $defaultGradeToUpdate->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
          }
        };
        if ($approverGrade->created_by === null && $auditUserId && $approverGrade->id !== $defaultGrade->id) {
          $approverGradeToUpdate = Grade::find($approverGrade->id);
          if ($approverGradeToUpdate && $approverGradeToUpdate->created_by === null) {
            $approverGradeToUpdate->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
          }
        } elseif ($approverGrade->id === $defaultGrade->id && $defaultGrade->created_by !== $auditUserId) {
          // If they are the same record and it wasn't updated above, update it.
          $gradeToUpdate = Grade::find($defaultGrade->id);
          if ($gradeToUpdate && $gradeToUpdate->created_by === null) {
            $gradeToUpdate->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
          }
        }

        if ($defaultPos->created_by === null && $auditUserId) {
          $defaultPos->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
        };
      } elseif ($user->wasRecentlyCreated && $user->created_by === null && $auditUserId !== null) {
        $user->created_by = $auditUserId;
        $user->updated_by = $auditUserId;
        $user->saveQuietly();
      }


      $role = Role::where('name', $data['role_name'])->first();
      if ($role) {
        if (!$user->hasRole($data['role_name'])) {
          $user->assignRole($data['role_name']);
        }
      } else {
        Log::warning("AdminUserSeeder: Role '{$data['role_name']}' not found for user {$user->email}. Ensure RoleAndPermissionSeeder has run and defined this role.");
      }
      Log::info("AdminUserSeeder: Processed user {$data['email']}.");
    }
    Log::info('AdminUserSeeder: Admin User seeding complete.');

    // ##### START: CODE FOR TESTING PURPOSES #####
    // This section deactivates any users whose emails are not in the predefined list.
    // This is useful for ensuring a clean testing environment with only known seeded users active.
    // To revert to production behavior (where all users might be active),
    // this block should be commented out or removed.
    Log::info('AdminUserSeeder: TESTING - Deactivating users not in the core seeded list.');
    $activeTestUserEmails = [
      env('ADMIN_EMAIL', 'admin@motac.gov.my'), // Ensure Admin email from .env is included
      'bpmstaff@motac.gov.my',
      'itadmin@motac.gov.my',
      'approver@motac.gov.my',
      'pengguna01@motac.gov.my'
    ];
    // Add the audit fallback email if it was created, to ensure it remains active
    $auditFallbackEmail = 'audit_fallback_adminseeder@motac.gov.my';
    if (User::where('email', $auditFallbackEmail)->exists()) {
      $activeTestUserEmails[] = $auditFallbackEmail;
    }

    $deactivatedCount = User::whereNotIn('email', array_unique($activeTestUserEmails))
      ->where('status', User::STATUS_ACTIVE) // Only update those that are currently active
      ->update(['status' => User::STATUS_INACTIVE]);
    Log::info("AdminUserSeeder: TESTING - Deactivated {$deactivatedCount} users.");
    // ##### END: CODE FOR TESTING PURPOSES #####
  }
}
