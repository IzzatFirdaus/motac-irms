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
      ['code' => 'BPM'], // ** CRITICAL: Lookup by unique code **
      [
        'name' => 'Bahagian Pengurusan Maklumat (BPM MOTAC)',
        'branch_type' => Department::BRANCH_TYPE_HQ,
        'description' => 'Bahagian Pengurusan Maklumat Utama Sistem MOTAC.',
        'is_active' => true,
        'created_by' => $auditUserId,
        'updated_by' => $auditUserId,
      ]
    );
    Log::info("AdminUserSeeder: Ensured default department '{$defaultDept->name}' (Code: {$defaultDept->code}) exists.");

    $defaultGrade = Grade::updateOrCreate(
      ['name' => 'F41'],
      [
        'level' => 41,
        'is_approver_grade' => true,
        'created_by' => $auditUserId,
        'updated_by' => $auditUserId,
      ]
    );
    Log::info("AdminUserSeeder: Ensured default grade '{$defaultGrade->name}' exists.");

    $defaultPos = Position::updateOrCreate(
      ['name' => 'Pegawai Teknologi Maklumat Sistem'],
      [
        'description' => 'Pegawai Teknologi Maklumat Asas untuk pengurusan sistem.',
        'grade_id' => $defaultGrade->id,
        'is_active' => true,
        'created_by' => $auditUserId,
        'updated_by' => $auditUserId,
      ]
    );
    Log::info("AdminUserSeeder: Ensured default position '{$defaultPos->name}' exists.");

    $usersData = [
      [
        'role_name' => 'Admin',
        'name' => 'Pentadbir Sistem Utama',
        'email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'),
        'password' => env('ADMIN_PASSWORD', 'Motac.1234'),
        'title' => User::TITLE_ENCIK ?? 'Encik',
        'identification_number' => '800101010001',
        'mobile_number' => '0191234567',
        'personal_email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'),
        'motac_email' => 'admin.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACADM001',
        'service_status' => User::SERVICE_STATUS_TETAP ?? '1',
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU ?? '1',
        'status' => User::STATUS_ACTIVE ?? 'active',
        'level' => '16',
        'department_id' => $defaultDept->id,
        'position_id' => $defaultPos->id,
        'grade_id' => $defaultGrade->id,
      ],
      [
        'role_name' => 'BPM Staff',
        'name' => 'Staf Sokongan BPM',
        'email' => 'bpmstaff@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_PUAN ?? 'Puan',
        'identification_number' => '850202020002',
        'mobile_number' => '0122345678',
        'personal_email' => 'bpmstaff@motac.gov.my',
        'motac_email' => 'bpm.staff.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACBPM001',
        'service_status' => User::SERVICE_STATUS_KONTRAK_MYSTEP ?? '2',
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3',
        'status' => User::STATUS_ACTIVE ?? 'active',
        'level' => '10',
        'department_id' => $defaultDept->id,
        'position_id' => $defaultPos->id,
        'grade_id' => $defaultGrade->id,
      ],
      [
        'role_name' => 'IT Admin',
        'name' => 'Pegawai IT Admin',
        'email' => 'itadmin@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_ENCIK ?? 'Encik',
        'identification_number' => '820303030003',
        'mobile_number' => '0173456789',
        'personal_email' => 'itadmin@motac.gov.my',
        'motac_email' => 'it.admin.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACIT001',
        'service_status' => User::SERVICE_STATUS_TETAP ?? '1',
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU ?? '1',
        'status' => User::STATUS_ACTIVE ?? 'active',
        'level' => '12',
        'department_id' => $defaultDept->id,
        'position_id' => $defaultPos->id,
        'grade_id' => $defaultGrade->id,
      ],
      [
        'role_name' => 'User',
        'name' => 'Pengguna Biasa Sistem',
        'email' => 'pengguna01@motac.gov.my',
        'password' => 'Motac.1234',
        'title' => User::TITLE_CIK ?? 'Cik',
        'identification_number' => '900404040004',
        'mobile_number' => '0144567890',
        'personal_email' => 'pengguna01@motac.gov.my',
        'motac_email' => null,
        'user_id_assigned' => null,
        'service_status' => User::SERVICE_STATUS_PELAJAR_INDUSTRI ?? '3',
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3',
        'status' => User::STATUS_ACTIVE ?? 'active',
        'level' => 'LI',
        'department_id' => $defaultDept->id,
        'position_id' => Position::where('name', 'Pelajar Latihan Industri')->first()?->id ?? $defaultPos->id,
        'grade_id' => Grade::where('name', 'PELATIH')->first()?->id ?? $defaultGrade->id,
      ],
    ];

    $now = Carbon::now();

    foreach ($usersData as $data) {
      $userDataValues = [
        'name' => $data['name'],
        'password' => Hash::make($data['password']),
        'title' => $data['title'],
        'identification_number' => $data['identification_number'],
        'mobile_number' => $data['mobile_number'],
        'personal_email' => $data['personal_email'],
        'motac_email' => $data['motac_email'] ?? null,
        'user_id_assigned' => $data['user_id_assigned'] ?? null,
        'service_status' => $data['service_status'],
        'appointment_type' => $data['appointment_type'],
        'status' => $data['status'],
        'level' => $data['level'],
        'email_verified_at' => $now,
        'department_id' => $data['department_id'],
        'position_id' => $data['position_id'],
        'grade_id' => $data['grade_id'],
        'created_by' => $auditUserId,
        'updated_by' => $auditUserId,
      ];

      $user = User::firstOrCreate(['email' => $data['email']], $userDataValues);

      if ($auditUserId === null && $user->wasRecentlyCreated) {
        $auditUserId = $user->id;
        Log::info("AdminUserSeeder: Fallback audit user ID set to {$auditUserId} from first created user: {$user->email}.");
        if ($user->created_by === null) {
          $user->created_by = $auditUserId;
          $user->updated_by = $auditUserId;
          $user->saveQuietly();
        }
        if ($defaultDept->created_by === null && $auditUserId) {
          $defaultDept->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
        };
        if ($defaultGrade->created_by === null && $auditUserId) {
          $defaultGrade->update(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
        };
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
  }
}
