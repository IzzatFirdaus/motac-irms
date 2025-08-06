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

        // ADJUSTMENT: Added more granular permissions and new Helpdesk permissions.
        $permissionsByGroup = [
            'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'Equipment Management' => ['view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details'],
            'Loan Applications' => [
                'create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications',
                'approve_loan_applications', 'reject_loan_applications', 'issue_loan_equipment', 'process_loan_return', 'view_loan_transactions',
            ],
            // REMOVED: 'Email Applications' => [...],
            'Approval Management' => ['view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history'],
            'Master Data Management' => ['view_departments', 'create_departments', 'edit_departments', 'delete_departments', 'view_positions', 'create_positions', 'edit_positions', 'delete_positions', 'view_grades', 'create_grades', 'edit_grades', 'delete_grades', 'view_contracts', 'create_contracts', 'edit_contracts', 'delete_contracts', 'view_locations', 'create_locations', 'edit_locations', 'delete_locations', 'view_centers', 'create_centers', 'edit_centers', 'delete_centers', 'view_equipment_categories', 'create_equipment_categories', 'edit_equipment_categories', 'delete_equipment_categories', 'view_sub_categories', 'create_sub_categories', 'edit_sub_categories', 'delete_sub_categories'],
            'System Settings' => ['manage_settings'],
            'Notification Management' => ['view_notifications', 'mark_notifications_as_read'],
            'Report Management' => ['view_equipment_reports', 'view_loan_reports', 'view_user_activity_reports'], // REMOVED: 'view_email_reports'
            // NEW: Helpdesk Management
            'Helpdesk Management' => [
                'create_helpdesk_tickets',
                'view_helpdesk_tickets',
                'edit_helpdesk_tickets',
                'delete_helpdesk_tickets',
                'assign_helpdesk_tickets',
                'close_helpdesk_tickets',
                'reopen_helpdesk_tickets',
                'view_any_helpdesk_tickets', // For agents/admins to see all tickets
                'view_helpdesk_reports',
                'manage_helpdesk_categories',
                'manage_helpdesk_priorities',
            ],
        ];

        foreach ($guards as $guard) {
            // Create permissions
            foreach ($permissionsByGroup as $groupName => $permissions) {
                Log::info("Creating permissions for group: {$groupName} (Guard: {$guard})");
                foreach ($permissions as $permissionName) {
                    Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guard]);
                }
            }

            // Create roles
            $roles = ['Admin', 'BPM Staff', 'IT Admin', 'Approver', 'HOD', 'User'];
            foreach ($roles as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            }

            // Assign permissions to roles
            // Admin role gets all permissions
            $adminPermissions = array_reduce($permissionsByGroup, 'array_merge', []);
            Role::findByName('Admin', $guard)->syncPermissions($adminPermissions);

            // BPM Staff role permissions
            Role::findByName('BPM Staff', $guard)->syncPermissions([
                'view_users', 'create_users', 'edit_users', 'view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'view_equipment_details',
                'approve_loan_applications', 'reject_loan_applications', 'issue_loan_equipment', 'process_loan_return', 'view_loan_transactions',
                'view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
                'view_equipment_reports', 'view_loan_reports', 'view_user_activity_reports', 'view_departments', 'view_positions', 'view_grades',
                // NEW: Helpdesk permissions for BPM Staff (can manage tickets)
                'view_helpdesk_tickets', 'edit_helpdesk_tickets', 'assign_helpdesk_tickets', 'close_helpdesk_tickets', 'reopen_helpdesk_tickets',
                'view_any_helpdesk_tickets', 'view_helpdesk_reports', 'manage_helpdesk_categories', 'manage_helpdesk_priorities',
            ]);

            // IT Admin role permissions
            Role::findByName('IT Admin', $guard)->syncPermissions([
                // REMOVED: 'view_all_email_applications', 'process_email_provisioning', 'view_any_admin_email_applications',
                'view_any_approvals', 'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
                // REMOVED: 'view_email_reports',
                // NEW: Helpdesk permissions for IT Admin (can manage tickets)
                'view_helpdesk_tickets', 'edit_helpdesk_tickets', 'assign_helpdesk_tickets', 'close_helpdesk_tickets', 'reopen_helpdesk_tickets',
                'view_any_helpdesk_tickets', 'view_helpdesk_reports', 'manage_helpdesk_categories', 'manage_helpdesk_priorities',
            ]);

            Role::findByName('Approver', $guard)->syncPermissions([
                'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
            ]);

            Role::findByName('HOD', $guard)->syncPermissions([
                'view_approval_tasks', 'act_on_approval_tasks', 'view_approval_history',
            ]);

            // User role permissions
            Role::findByName('User', $guard)->syncPermissions([
                'create_loan_applications', 'view_loan_applications', 'edit_loan_applications', 'cancel_loan_applications', 'submit_loan_applications',
                // REMOVED: 'create_email_applications', 'view_email_applications', 'edit_email_applications', 'cancel_email_applications', 'submit_email_applications',
                // NEW: Helpdesk permissions for regular users (can create and view their own tickets)
                'create_helpdesk_tickets', 'view_helpdesk_tickets',
            ]);
        }

        Log::info('Role and Permission seeding completed successfully.');
    }
}
