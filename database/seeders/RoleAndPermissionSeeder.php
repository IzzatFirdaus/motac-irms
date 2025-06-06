<?php

namespace Database\Seeders;

use App\Models\User; // Used for the fallback role assignment
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
  public function run(): void
  {
    Log::info('Starting Role and Permission seeding (Revision 4 - Multi-guard)...');
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    Log::info('Cached permissions reset.');

    // Truncate existing tables for a clean seed
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
    DB::table(config('permission.table_names.role_has_permissions'))->truncate();
    DB::table(config('permission.table_names.model_has_roles'))->truncate();
    DB::table(config('permission.table_names.model_has_permissions'))->truncate();
    DB::table(config('permission.table_names.roles'))->truncate();
    DB::table(config('permission.table_names.permissions'))->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    Log::info('Existing Spatie permission tables truncated.');

    // --- EDITED: Define guards to support both web and API ---
    $guards = ['web', 'sanctum'];

    // Define permissions grouped logically
    $permissionsByGroup = [
      'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'assign_roles', 'view_user_roles'],
      'Employee Management' => ['view_employees', 'create_employees', 'edit_employees', 'delete_employees', 'view_employee_details'],
      'Equipment Management' => ['view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details', 'manage_equipment_status', 'manage_equipment_masterlist'],
      'Loan Applications' => ['create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications', 'certify_loan_applications', 'view_all_loan_applications', 'approve_loan_applications', 'reject_loan_applications'],
      'Loan Processing (BPM)' => ['process_loan_issuance', 'process_loan_return', 'view_loan_transactions', 'manage_loan_accessories_checklist'],
      'Email Applications' => ['create_email_applications', 'view_email_applications', 'edit_email_applications', 'cancel_email_applications', 'submit_email_applications', 'view_all_email_applications', 'approve_email_applications', 'reject_email_applications'],
      'Email Provisioning (IT)' => ['process_email_provisioning', 'manage_email_accounts'],
      'Approvals' => ['view_approval_tasks', 'act_on_approval_tasks', 'view_all_approvals', 'view_approval_history'],
      'Master Data Management' => [
        'manage_master_data',
        'view_equipment_categories',
        'manage_equipment_categories',
        'view_sub_categories',
        'manage_sub_categories',
        'view_locations',
        'manage_locations',
        'view_departments',
        'manage_departments',
        'view_grades',
        'manage_grades',
        'view_positions',
        'manage_positions',
        'view_leaves',
        'manage_leaves',
        'view_contracts',
        'manage_contracts',
        'view_holidays',
        'manage_holidays',
        'view_centers',
        'manage_centers',
      ],
      'System Management' => ['view_settings', 'manage_settings', 'view_audit_logs', 'run_reports', 'manage_imports', 'view_system_notifications'],
      'Role & Permission Management' => ['view_roles', 'manage_roles', 'view_permissions', 'manage_permissions'],
    ];

    Log::info('Creating permissions for all guards...');
    // --- EDITED: Loop through guards to create permissions for each ---
    foreach ($guards as $guard) {
      foreach ($permissionsByGroup as $group => $permissions) {
        foreach ($permissions as $permissionName) {
          Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guard]);
        }
      }
    }
    Log::info(Permission::count() . ' total permission records created/ensured across all guards.');

    // Role names are Title Cased and standardized as per design doc
    $roleNames = ['Admin', 'BPM Staff', 'User', 'Approver', 'HOD', 'IT Admin'];

    Log::info('Creating roles for all guards...');
    // --- EDITED: Loop through guards to create roles for each ---
    foreach ($guards as $guard) {
      foreach ($roleNames as $roleName) {
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
      }
    }
    Log::info(Role::count() . ' total role records created/ensured across all guards.');

    // --- EDITED: Loop through guards to assign permissions correctly ---
    Log::info('Assigning permissions to roles for each guard...');
    foreach ($guards as $guard) {
      $allPermissionsForGuard = Permission::where('guard_name', $guard)->get();

      // Assign all permissions to Admin for the current guard
      $adminRole = Role::findByName('Admin', $guard);
      if ($adminRole) {
        $adminRole->syncPermissions($allPermissionsForGuard);
      }

      // BPM Staff Permissions
      $bpmRole = Role::findByName('BPM Staff', $guard);
      if ($bpmRole) {
        $bpmRole->syncPermissions($allPermissionsForGuard->filter(fn($p) => in_array($p->name, [
          'view_users',
          'view_equipment',
          'view_equipment_details',
          'manage_equipment_status',
          'view_all_loan_applications',
          'edit_loan_applications',
          'process_loan_issuance',
          'process_loan_return',
          'view_loan_transactions',
          'manage_loan_accessories_checklist',
          'view_approval_tasks',
          'act_on_approval_tasks',
          'run_reports',
          'view_locations',
          'view_equipment_categories',
          'view_sub_categories',
        ])));
      }

      // IT Admin Permissions
      $itAdminRole = Role::findByName('IT Admin', $guard);
      if ($itAdminRole) {
        $itAdminRole->syncPermissions($allPermissionsForGuard->filter(fn($p) => in_array($p->name, [
          'view_users',
          'view_all_email_applications',
          'process_email_provisioning',
          'manage_email_accounts',
          'manage_equipment_masterlist',
          'create_equipment',
          'edit_equipment',
          'delete_equipment',
          'view_equipment',
          'view_equipment_details',
          'manage_settings',
          'view_audit_logs',
          'manage_imports',
          'view_departments',
          'manage_departments',
          'view_positions',
          'manage_positions',
          'view_grades',
          'manage_grades',
        ])));
      }

      // Approver Permissions
      $approverRole = Role::findByName('Approver', $guard);
      if ($approverRole) {
        $approverRole->syncPermissions($allPermissionsForGuard->filter(fn($p) => in_array($p->name, [
          'view_approval_tasks',
          'act_on_approval_tasks',
          'view_loan_applications',
          'view_email_applications',
        ])));
      }

      // HOD Permissions (includes Approver permissions)
      $hodRole = Role::findByName('HOD', $guard);
      if ($hodRole) {
        $hodPermissions = $allPermissionsForGuard->filter(fn($p) => in_array($p->name, [
          'view_approval_tasks',
          'act_on_approval_tasks',
          'approve_loan_applications',
          'reject_loan_applications',
          'approve_email_applications',
          'reject_email_applications',
          'view_all_loan_applications',
          'view_all_email_applications',
        ]))->pluck('name')->toArray();
        $approverPermsForHod = Role::findByName('Approver', $guard)?->permissions->pluck('name')->toArray() ?? [];
        $hodRole->syncPermissions(array_unique(array_merge($hodPermissions, $approverPermsForHod)));
      }

      // User Permissions
      $userRole = Role::findByName('User', $guard);
      if ($userRole) {
        $userRole->syncPermissions($allPermissionsForGuard->filter(fn($p) => in_array($p->name, [
          'view_equipment',
          'create_loan_applications',
          'view_loan_applications',
          'edit_loan_applications',
          'cancel_loan_applications',
          'submit_loan_applications',
          'certify_loan_applications',
          'create_email_applications',
          'view_email_applications',
          'edit_email_applications',
          'cancel_email_applications',
          'submit_email_applications',
          'view_system_notifications',
        ])));
      }
    }
    Log::info('Permissions assigned to roles for all guards.');

    // Assign 'User' role to any users without a role (for web guard only)
    $defaultUserRoleForAssignment = Role::where('name', 'User')->where('guard_name', 'web')->first();
    if ($defaultUserRoleForAssignment) {
      $usersWithoutRoles = User::with('roles')->get()->filter(function ($user) {
        return $user->roles->isEmpty();
      });
      if ($usersWithoutRoles->isNotEmpty()) {
        Log::info("Found {$usersWithoutRoles->count()} users without any roles. Assigning default 'User' role...");
        foreach ($usersWithoutRoles as $userInstance) {
          if (strtolower($userInstance->email) !== env('ADMIN_EMAIL', 'admin@motac.gov.my')) {
            $userInstance->assignRole($defaultUserRoleForAssignment);
          }
        }
        Log::info("Assignment complete (excluding primary admin).");
      }
    } else {
      Log::warning("Default 'User' role for 'web' guard not found. Cannot assign to users without roles.");
    }

    Log::info('Role and Permission seeding completed (Revision 4 - Multi-guard).');
  }
}
