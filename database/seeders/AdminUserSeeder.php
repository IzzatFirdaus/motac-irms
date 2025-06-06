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
    Log::info('AdminUserSeeder: Starting Admin User seeding. Ensuring correct grade assignments for key users.');

    $auditUserId = User::orderBy('id')->value('id');
    Log::info("AdminUserSeeder: Using User ID {$auditUserId} for audit columns if available.");

    $defaultDept = Department::updateOrCreate(
      ['code' => 'BPM'],
      [
        'name' => 'Bahagian Pengurusan Maklumat (BPM MOTAC)',
        'branch_type' => Department::BRANCH_TYPE_HQ,
        'description' => 'Bahagian Pengurusan Maklumat Utama Sistem MOTAC.',
        'is_active' => true,
        'created_by' => $auditUserId,
        'updated_by' => $auditUserId,
      ]
    );

    // --- Ensure Critical Grades Exist ---
    $gradeF41 = Grade::updateOrCreate(
        ['name' => 'F41'],
        ['level' => 41, 'is_approver_grade' => true, 'description' => 'Pegawai Teknologi Maklumat', 'created_by' => $auditUserId, 'updated_by' => $auditUserId]
    );
    Log::info("AdminUserSeeder: Ensured Grade 'F41' exists with level 41.");

    $gradeF44 = Grade::updateOrCreate(
        ['name' => 'F44'],
        ['level' => 44, 'is_approver_grade' => true, 'description' => 'Pegawai Teknologi Maklumat Kanan', 'created_by' => $auditUserId, 'updated_by' => $auditUserId]
    );
    Log::info("AdminUserSeeder: Ensured Grade 'F44' exists with level 44 for approver eligibility.");

    // --- Ensure related positions exist for the grades ---
    $defaultPos = Position::updateOrCreate(
      ['name' => 'Pegawai Teknologi Maklumat Sistem'],
      ['grade_id' => $gradeF41->id, 'is_active' => true, 'created_by' => $auditUserId, 'updated_by' => $auditUserId]
    );
    $approverPos = Position::updateOrCreate(
      ['name' => 'Ketua Unit Aplikasi'],
      ['grade_id' => $gradeF44->id, 'is_active' => true, 'created_by' => $auditUserId, 'updated_by' => $auditUserId]
    );
    Log::info("AdminUserSeeder: Ensured default and approver-level positions exist.");

    // --- User Data Array ---
    $usersData = [
      [
        'role_name' => 'Admin', 'name' => 'Pentadbir Sistem Utama', 'email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'), 'password' => env('ADMIN_PASSWORD', 'Motac.1234'),
        'title' => User::TITLE_ENCIK, 'identification_number' => '800101010001', 'mobile_number' => '0191234567',
        'personal_email' => env('ADMIN_EMAIL', 'admin@motac.gov.my'), 'motac_email' => 'admin.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACADM001', 'service_status' => User::SERVICE_STATUS_TETAP,
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU, 'status' => User::STATUS_ACTIVE, 'level' => '16',
        'department_id' => $defaultDept->id,
        'position_id' => $approverPos->id,
        'grade_id' => $gradeF44->id,
      ],
      [
        'role_name' => 'BPM Staff', 'name' => 'Staf Sokongan BPM', 'email' => 'bpmstaff@motac.gov.my', 'password' => 'Motac.1234',
        'title' => User::TITLE_PUAN, 'identification_number' => '850202020002', 'mobile_number' => '0122345678',
        'personal_email' => 'bpmstaff@motac.gov.my', 'motac_email' => 'bpm.staff.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACBPM001', 'service_status' => User::SERVICE_STATUS_KONTRAK_MYSTEP,
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN, 'status' => User::STATUS_ACTIVE, 'level' => '10',
        'department_id' => $defaultDept->id,
        'position_id' => $defaultPos->id,
        'grade_id' => $gradeF41->id,
      ],
      [
        'role_name' => 'IT Admin', 'name' => 'Pegawai IT Admin', 'email' => 'itadmin@motac.gov.my', 'password' => 'Motac.1234',
        'title' => User::TITLE_ENCIK, 'identification_number' => '820303030003', 'mobile_number' => '0173456789',
        'personal_email' => 'itadmin@motac.gov.my', 'motac_email' => 'it.admin.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACIT001', 'service_status' => User::SERVICE_STATUS_TETAP,
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU, 'status' => User::STATUS_ACTIVE, 'level' => '12',
        'department_id' => $defaultDept->id,
        'position_id' => $defaultPos->id,
        'grade_id' => $gradeF41->id,
      ],
      [
        'role_name' => 'Approver', 'name' => 'Pegawai Penyokong (Approver)', 'email' => 'approver@motac.gov.my', 'password' => 'Motac.1234',
        'title' => User::TITLE_PUAN, 'identification_number' => '780505050005', 'mobile_number' => '01156789012',
        'personal_email' => 'approver@motac.gov.my', 'motac_email' => 'approver.spsb@motac.gov.my',
        'user_id_assigned' => 'MOTACAPP001', 'service_status' => User::SERVICE_STATUS_TETAP,
        'appointment_type' => User::APPOINTMENT_TYPE_BAHARU, 'status' => User::STATUS_ACTIVE, 'level' => '14',
        'department_id' => $defaultDept->id,
        'position_id' => $approverPos->id,
        'grade_id' => $gradeF44->id,
      ],
      [
        'role_name' => 'User', 'name' => 'Pengguna Biasa Sistem', 'email' => 'pengguna01@motac.gov.my', 'password' => 'Motac.1234',
        'title' => User::TITLE_CIK, 'identification_number' => '900404040004', 'mobile_number' => '0144567890',
        'personal_email' => 'pengguna01@motac.gov.my', 'motac_email' => null,
        'user_id_assigned' => null, 'service_status' => User::SERVICE_STATUS_PELAJAR_INDUSTRI,
        'appointment_type' => User::APPOINTMENT_TYPE_LAIN_LAIN, 'status' => User::STATUS_ACTIVE, 'level' => 'LI',
        'department_id' => $defaultDept->id,
        'position_id' => Position::where('name', 'Pelajar Latihan Industri')->first()?->id ?? $defaultPos->id,
        'grade_id' => Grade::where('name', 'PELATIH')->first()?->id ?? $gradeF41->id,
      ],
    ];

    $now = Carbon::now();

    foreach ($usersData as $data) {
      $userDataValues = [
        'name' => $data['name'], 'password' => Hash::make($data['password']),
        'title' => $data['title'], 'identification_number' => $data['identification_number'],
        'mobile_number' => $data['mobile_number'], 'personal_email' => $data['personal_email'],
        'motac_email' => $data['motac_email'], 'user_id_assigned' => $data['user_id_assigned'],
        'service_status' => $data['service_status'], 'appointment_type' => $data['appointment_type'],
        'status' => $data['status'], 'level' => $data['level'], 'email_verified_at' => $now,
        'department_id' => $data['department_id'], 'position_id' => $data['position_id'],
        'grade_id' => $data['grade_id'], 'created_by' => $auditUserId, 'updated_by' => $auditUserId,
      ];
      $user = User::updateOrCreate(['email' => $data['email']], $userDataValues);
      $role = Role::where('name', $data['role_name'])->first();
      if ($role && !$user->hasRole($data['role_name'])) {
        $user->assignRole($data['role_name']);
      }
      Log::info("AdminUserSeeder: Processed user {$user->email}. Assigned Grade ID: {$user->grade_id} (Level: {$user->grade?->level}).");
    }
    Log::info('AdminUserSeeder: Admin User seeding complete.');
  }
}
