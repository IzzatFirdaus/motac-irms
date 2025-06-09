<?php

namespace Database\Seeders;

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
        Log::info('Starting Role and Permission seeding...');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table(config('permission.table_names.role_has_permissions'))->truncate();
        DB::table(config('permission.table_names.model_has_roles'))->truncate();
        DB::table(config('permission.table_names.model_has_permissions'))->truncate();
        DB::table(config('permission.table_names.roles'))->truncate();
        DB::table(config('permission.table_names.permissions'))->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $guards = ['web', 'sanctum'];

        // ADJUSTMENT: Added more granular permissions for master data.
        $permissionsByGroup = [
            'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'Equipment Management' => ['view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details'],
            'Loan Applications' => ['create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications', 'view_all_loan_applications'],
            'Loan Processing (BPM)' => ['process_loan_issuance', 'process_loan_return', 'view_loan_transactions'],
            'Email Applications' => ['create_email_applications', 'view_email_applications', 'edit_email_applications', 'cancel_email_applications', 'submit_email_applications', 'view_all_email_applications'],
            'Email Provisioning (IT)' => ['process_email_provisioning', 'view_any_admin_email_applications'],
            'Approvals' => ['view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history'],
            'Departments' => ['view_departments', 'create_departments', 'edit_departments', 'delete_departments'],
            'Positions' => ['view_positions', 'create_positions', 'edit_positions', 'delete_positions'],
            'Grades' => ['view_grades', 'create_grades', 'edit_grades', 'delete_grades'],
            'Role & Permission Management' => ['manage_roles', 'manage_permissions'],
            'Reporting' => ['view_equipment_reports', 'view_loan_reports', 'view_email_reports', 'view_user_activity_reports'],
            'System Management' => ['view_system_logs'],
        ];

        foreach ($guards as $guard) {
            foreach ($permissionsByGroup as $permissions) {
                foreach ($permissions as $permissionName) {
                    Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guard]);
                }
            }
        }

        $roleNames = ['Admin', 'BPM Staff', 'User', 'Approver', 'HOD', 'IT Admin'];
        foreach ($guards as $guard) {
            foreach ($roleNames as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            }
        }

        foreach ($guards as $guard) {
            // Admin gets all permissions.
            Role::findByName('Admin', $guard)->syncPermissions(Permission::where('guard_name', $guard)->pluck('name'));

            // ADJUSTMENT: Full, corrected permission sets for each role.
            Role::findByName('BPM Staff', $guard)->syncPermissions([
                'view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details',
                'process_loan_issuance', 'process_loan_return', 'view_loan_transactions',
                'view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
                'view_equipment_reports', 'view_loan_reports', 'view_user_activity_reports', 'view_departments', 'view_positions', 'view_grades',
            ]);

            Role::findByName('IT Admin', $guard)->syncPermissions([
                'view_all_email_applications', 'process_email_provisioning', 'view_any_admin_email_applications',
                'view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
                'view_email_reports',
            ]);

            Role::findByName('Approver', $guard)->syncPermissions([
                'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
            ]);

            Role::findByName('HOD', $guard)->syncPermissions([
                'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
            ]);

            Role::findByName('User', $guard)->syncPermissions([
                'create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications',
                'create_email_applications', 'view_email_applications', 'edit_email_applications', 'cancel_email_applications', 'submit_email_applications',
            ]);
        }
        Log::info('Role and Permission seeding completed successfully.');
    }
}
