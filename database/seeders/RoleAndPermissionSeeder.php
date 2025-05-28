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
        Log::info('Starting Role and Permission seeding (Revision 3)...');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Log::info('Cached permissions reset.');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table(config('permission.table_names.role_has_permissions'))->truncate();
        DB::table(config('permission.table_names.model_has_roles'))->truncate();
        DB::table(config('permission.table_names.model_has_permissions'))->truncate();
        DB::table(config('permission.table_names.roles'))->truncate();
        DB::table(config('permission.table_names.permissions'))->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Existing Spatie permission tables truncated.');

        // Define permissions based on Revision 3 functionalities
        $permissionsByGroup = [
            'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'assign_roles', 'view_user_roles'],
            // Employee Management might be out of scope for MOTAC System, but kept if general HRMS features are used
            'Employee Management' => ['view_employees', 'create_employees', 'edit_employees', 'delete_employees', 'view_employee_details'],

            'Equipment Management' => ['view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details', 'manage_equipment_status', 'manage_equipment_masterlist'], // manage_equipment_masterlist for Admin/IT Admin
            'Loan Applications' => ['create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications', 'certify_loan_applications', 'view_all_loan_applications', 'approve_loan_applications', 'reject_loan_applications'],
            'Loan Processing (BPM)' => ['process_loan_issuance', 'process_loan_return', 'view_loan_transactions', 'manage_loan_accessories_checklist'], // For BPM Staff
            'Email Applications' => ['create_email_applications', 'view_email_applications', 'edit_email_applications', 'cancel_email_applications', 'submit_email_applications', 'view_all_email_applications', 'approve_email_applications', 'reject_email_applications'],
            'Email Provisioning (IT)' => ['process_email_provisioning', 'manage_email_accounts'], // For IT Admin
            'Approvals' => ['view_approval_tasks', 'act_on_approval_tasks', 'view_all_approvals', 'view_approval_history'],
            'Master Data Management' => [
                'manage_master_data', // General permission
                'view_equipment_categories', 'manage_equipment_categories',
                'view_sub_categories', 'manage_sub_categories',
                'view_locations', 'manage_locations',
                'view_departments', 'manage_departments',
                'view_grades', 'manage_grades',
                'view_positions', 'manage_positions',
                // Other HRMS master data if applicable
                'view_leaves', 'manage_leaves',
                'view_contracts', 'manage_contracts',
                'view_holidays', 'manage_holidays',
                'view_centers', 'manage_centers',
            ],
            'System Management' => ['view_settings', 'manage_settings', 'view_audit_logs', 'run_reports', 'manage_imports', 'view_system_notifications'],
            'Role & Permission Management' => ['view_roles', 'manage_roles', 'view_permissions', 'manage_permissions'],
        ];

        Log::info('Creating permissions (Revision 3)...');
        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web', 'group_name' => $group]);
            }
        }
        Log::info(Permission::count().' permissions created/ensured.');

        // Role names are Title Cased and standardized as per design doc
        $roles = [
            ['name' => 'Admin', 'guard_name' => 'web'],      // Super Administrator
            ['name' => 'BPM Staff', 'guard_name' => 'web'],  // Bahagian Pengurusan Maklumat Staff for ICT Loans
            ['name' => 'User', 'guard_name' => 'web'],       // Regular MOTAC Staff
            ['name' => 'Approver', 'guard_name' => 'web'],   // General role for users who can approve (e.g., Supporting Officer, HOD for certain stages)
            ['name' => 'HOD', 'guard_name' => 'web'],        // Head of Department (Specific Approver type)
            ['name' => 'IT Admin', 'guard_name' => 'web'],   // For Email provisioning, system-level IT tasks
            // 'HR Staff' can be kept if HR specific functions from the base template are used, otherwise might be redundant for this specific system
            // ['name' => 'HR Staff', 'guard_name' => 'web'],
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
            $adminRole->syncPermissions($allPermissionsCollection); // Admin gets all permissions
        }

        $bpmRole = Role::findByName('BPM Staff', 'web');
        if ($bpmRole) {
            $bpmRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_users', // To see user details for loans
                    'view_equipment', 'view_equipment_details', 'manage_equipment_status', // Manage status during loan
                    'view_all_loan_applications', 'edit_loan_applications', // Review and manage applications
                    'process_loan_issuance', 'process_loan_return', 'view_loan_transactions', 'manage_loan_accessories_checklist',
                    'view_approval_tasks', // Can view tasks related to BPM stage
                    'act_on_approval_tasks', // For BPM approval stage
                    'run_reports', // BPM related reports
                    'view_locations', 'view_equipment_categories', 'view_sub_categories', // View relevant master data
                ]);
            }));
        }

        $itAdminRole = Role::findByName('IT Admin', 'web');
        if ($itAdminRole) {
            $itAdminRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_users', // To see user details for email accounts
                    'view_all_email_applications', 'process_email_provisioning', 'manage_email_accounts',
                    // IT Admin might also manage the master list of equipment if distinct from BPM's loan processing role
                    'manage_equipment_masterlist', 'create_equipment', 'edit_equipment', 'delete_equipment',
                    'view_equipment', 'view_equipment_details',
                    'manage_settings', 'view_audit_logs', 'manage_imports',
                    'view_departments', 'manage_departments', // May manage org structure in system
                    'view_positions', 'manage_positions',
                    'view_grades', 'manage_grades',
                ]);
            }));
        }

        $approverRole = Role::findByName('Approver', 'web'); // General approver (e.g. supporting officer)
        if ($approverRole) {
            $approverRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_approval_tasks', 'act_on_approval_tasks',
                    // View applications they need to act upon. Specific create/edit is for User role.
                    'view_loan_applications',
                    'view_email_applications',
                ]);
            }));
        }

        $hodRole = Role::findByName('HOD', 'web'); // Head of Department
        if ($hodRole) {
            // HODs are also Approvers, so they get Approver permissions + HOD specific ones
            $hodPermissions = $allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_approval_tasks', 'act_on_approval_tasks', // General approval actions
                    'approve_loan_applications', 'reject_loan_applications', // HOD specific stage for loans
                    'approve_email_applications', 'reject_email_applications', // HOD specific stage for email (if applicable)
                    'view_all_loan_applications', // View applications relevant to their department for approval
                    'view_all_email_applications',// View email applications relevant to their department for approval
                ]);
            })->pluck('name')->toArray();
             $approverPermsForHod = Role::findByName('Approver', 'web')?->permissions->pluck('name')->toArray() ?? [];
            $hodRole->syncPermissions(array_unique(array_merge($hodPermissions, $approverPermsForHod)));
        }


        $userRole = Role::findByName('User', 'web');
        if ($userRole) {
            $userRole->syncPermissions($allPermissionsCollection->filter(function ($permission) {
                return in_array($permission->name, [
                    'view_equipment', // View available equipment
                    'create_loan_applications', 'view_loan_applications', // Their own
                    'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications',
                    'certify_loan_applications',
                    'create_email_applications', 'view_email_applications', // Their own
                    'edit_email_applications', 'cancel_email_applications', 'submit_email_applications',
                    'view_system_notifications', // View their own notifications
                ]);
            }));
        }

        Log::info('Permissions assigned to roles (Revision 3).');

        // Assign default 'User' role to users without roles
        $defaultUserRoleForAssignment = Role::where('name', 'User')->first();
        if ($defaultUserRoleForAssignment) {
            // Detach all existing roles first to ensure clean assignment for users who should only be 'User'
            // User::chunk(200, function ($users) use ($defaultUserRoleForAssignment) {
            //     foreach ($users as $userInstance) {
            //         if ($userInstance->roles->isEmpty()) { // Only assign if they have no roles
            //             $userInstance->assignRole($defaultUserRoleForAssignment);
            //         }
            //     }
            // });
            // Log::info("Checked and assigned '{$defaultUserRoleForAssignment->name}' role to users who had no roles.");
            // More robust: find users that are NOT admin and assign User role if they have no other specific roles.
            // For now, simple assignment if no roles exist:
            $usersWithoutRoles = User::doesntHave('roles')->get();
            if ($usersWithoutRoles->isNotEmpty()) {
                foreach ($usersWithoutRoles as $userInstance) {
                    // Avoid giving 'Admin' user the 'User' role if they are the only one and being created.
                    // This logic might need adjustment based on AdminUserSeeder.
                    if (strtolower($userInstance->email) !== 'admin@example.com') { // Example admin email
                         $userInstance->assignRole($defaultUserRoleForAssignment);
                    }
                }
                Log::info("Assigned '{$defaultUserRoleForAssignment->name}' role to {$usersWithoutRoles->count()} users who had no roles (excluding potential default admin).");
            }


        } else {
            Log::warning("Default 'User' role not found. Cannot assign to users without roles.");
        }

        Log::info('Role and Permission seeding completed (Revision 3).');
    }
}
