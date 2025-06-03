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
        Log::info('Starting Role and Permission seeding (Revision 3 - group_name fix)...');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Log::info('Cached permissions reset.');

        // Truncate existing tables
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table(config('permission.table_names.role_has_permissions'))->truncate();
        DB::table(config('permission.table_names.model_has_roles'))->truncate();
        DB::table(config('permission.table_names.model_has_permissions'))->truncate();
        DB::table(config('permission.table_names.roles'))->truncate();
        DB::table(config('permission.table_names.permissions'))->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Existing Spatie permission tables truncated.');

        // Define permissions grouped logically (group name is for seeder organization only)
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
                'view_equipment_categories', 'manage_equipment_categories',
                'view_sub_categories', 'manage_sub_categories',
                'view_locations', 'manage_locations',
                'view_departments', 'manage_departments',
                'view_grades', 'manage_grades',
                'view_positions', 'manage_positions',
                'view_leaves', 'manage_leaves',
                'view_contracts', 'manage_contracts',
                'view_holidays', 'manage_holidays',
                'view_centers', 'manage_centers',
            ],
            'System Management' => ['view_settings', 'manage_settings', 'view_audit_logs', 'run_reports', 'manage_imports', 'view_system_notifications'],
            'Role & Permission Management' => ['view_roles', 'manage_roles', 'view_permissions', 'manage_permissions'],
        ];

        Log::info('Creating permissions (Revision 3 - group_name fix)...');
        foreach ($permissionsByGroup as $group => $permissions) { // $group is now just for seeder code organization
            foreach ($permissions as $permissionName) {
                // Corrected: Removed 'group_name' as it's not a standard column
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }
        Log::info(Permission::count().' permissions created/ensured.');

        // Role names are Title Cased and standardized as per design doc
        $roles = [
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'BPM Staff', 'guard_name' => 'web'],
            ['name' => 'User', 'guard_name' => 'web'],
            ['name' => 'Approver', 'guard_name' => 'web'],
            ['name' => 'HOD', 'guard_name' => 'web'],
            ['name' => 'IT Admin', 'guard_name' => 'web'],
            // ['name' => 'HR Staff', 'guard_name' => 'web'], // Kept commented if not primary for MOTAC RMS
        ];

        Log::info('Creating roles (Revision 3)...');
        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
        }
        Log::info(Role::count().' roles created/ensured.');

        $allPermissionsCollection = Permission::all();

        // Assign permissions to roles
        $adminRole = Role::findByName('Admin', 'web');
        if ($adminRole) {
            $adminRole->syncPermissions($allPermissionsCollection);
        }

        $bpmRole = Role::findByName('BPM Staff', 'web');
        if ($bpmRole) {
            $bpmRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_users',
                    'view_equipment', 'view_equipment_details', 'manage_equipment_status',
                    'view_all_loan_applications', 'edit_loan_applications',
                    'process_loan_issuance', 'process_loan_return', 'view_loan_transactions', 'manage_loan_accessories_checklist',
                    'view_approval_tasks',
                    'act_on_approval_tasks',
                    'run_reports',
                    'view_locations', 'view_equipment_categories', 'view_sub_categories',
                ]);
            }));
        }

        $itAdminRole = Role::findByName('IT Admin', 'web');
        if ($itAdminRole) {
            $itAdminRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_users',
                    'view_all_email_applications', 'process_email_provisioning', 'manage_email_accounts',
                    'manage_equipment_masterlist', 'create_equipment', 'edit_equipment', 'delete_equipment',
                    'view_equipment', 'view_equipment_details',
                    'manage_settings', 'view_audit_logs', 'manage_imports',
                    'view_departments', 'manage_departments',
                    'view_positions', 'manage_positions',
                    'view_grades', 'manage_grades',
                ]);
            }));
        }

        $approverRole = Role::findByName('Approver', 'web');
        if ($approverRole) {
            $approverRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_approval_tasks', 'act_on_approval_tasks',
                    'view_loan_applications', // To view applications they might need to approve
                    'view_email_applications',// To view applications they might need to approve
                ]);
            }));
        }

        $hodRole = Role::findByName('HOD', 'web');
        if ($hodRole) {
            $hodPermissions = $allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_approval_tasks', 'act_on_approval_tasks',
                    'approve_loan_applications', 'reject_loan_applications',
                    'approve_email_applications', 'reject_email_applications',
                    'view_all_loan_applications',
                    'view_all_email_applications',
                ]);
            })->pluck('name')->toArray();
            $approverPermsForHod = Role::findByName('Approver', 'web')?->permissions->pluck('name')->toArray() ?? [];
            $hodRole->syncPermissions(array_unique(array_merge($hodPermissions, $approverPermsForHod)));
        }

        $userRole = Role::findByName('User', 'web');
        if ($userRole) {
            $userRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_equipment',
                    'create_loan_applications', 'view_loan_applications',
                    'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications',
                    'certify_loan_applications',
                    'create_email_applications', 'view_email_applications',
                    'edit_email_applications', 'cancel_email_applications', 'submit_email_applications',
                    'view_system_notifications',
                ]);
            }));
        }

        Log::info('Permissions assigned to roles (Revision 3).');

        $defaultUserRoleForAssignment = Role::where('name', 'User')->first();
        if ($defaultUserRoleForAssignment) {
            $usersWithoutRoles = User::doesntHave('roles')->get();
            if ($usersWithoutRoles->isNotEmpty()) {
                foreach ($usersWithoutRoles as $userInstance) {
                    // Avoid giving admin@example.com the 'User' role if it's the primary admin
                    if (strtolower($userInstance->email) !== env('ADMIN_EMAIL', 'admin@motac.gov.my')) { // Check against configured admin email
                        $userInstance->assignRole($defaultUserRoleForAssignment);
                    }
                }
                Log::info("Assigned '{$defaultUserRoleForAssignment->name}' role to {$usersWithoutRoles->count()} users who had no roles (excluding primary admin).");
            }
        } else {
            Log::warning("Default 'User' role not found. Cannot assign to users without roles.");
        }

        Log::info('Role and Permission seeding completed (Revision 3 - group_name fix).');
    }
}
