<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Grade; // For assigning users to existing departments
use App\Models\Position;      // For assigning users to existing grades
use App\Models\User;   // For assigning users to existing positions
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role; // To check role existence

class UserSeeder extends Seeder
{
    public function run(int $numberOfUsers = 50): void // Allow overriding the number of users
    {
        Log::info('Starting general User seeding (Revision 3)...');

        // Ensure AdminUserSeeder has run and created necessary roles.
        // This seeder assumes roles like 'Admin', 'BPM Staff', 'IT Admin', 'User', 'Approver', 'HOD' exist.
        if (Role::where('name', 'User')->doesntExist()) {
            Log::error('Core roles not found. Please run RoleAndPermissionSeeder first. Aborting UserSeeder.');

            return;
        }

        // Check if Admin user from a potential AdminUserSeeder already exists.
        // This seeder should not recreate primary admin accounts.
        // Example check, adjust email if your AdminUserSeeder uses a different one.
        if (User::where('email', config('app.admin_email', 'admin@motac.gov.my'))->exists()) {
            Log::info('Admin user (e.g., '.config('app.admin_email', 'admin@motac.gov.my').') already exists, UserSeeder will create additional users.');
        }

        // Ensure there's some master data for users to belong to.
        // The factory should ideally handle picking random existing ones.
        if (Department::count() === 0 || Grade::count() === 0 || Position::count() === 0) {
            Log::warning('Departments, Grades, or Positions master data is missing. Users may not have these attributes fully set by the factory unless the factory creates them. Consider running their respective seeders first.');
            // Optionally, call them here if absolutely necessary, but DatabaseSeeder order is preferred.
            // $this->call([DepartmentSeeder::class, GradeSeeder::class, PositionSeeder::class]);
        }

        $rolesToUsersRatio = [
            'User' => 0.50,      // General users
            'BPM Staff' => 0.10,
            'IT Admin' => 0.10,
            'Approver' => 0.20,  // General approvers (e.g., supporting officers)
            'HOD' => 0.10,       // Heads of Department
        ];

        $totalCreated = 0;

        foreach ($rolesToUsersRatio as $roleName => $ratio) {
            $countForRole = (int) ($numberOfUsers * $ratio);
            if ($countForRole === 0 && $ratio > 0) { // Ensure at least 1 if ratio is small for small $numberOfUsers
                $countForRole = 1;
            }

            if ($countForRole > 0) {
                $factory = User::factory()->count($countForRole);
                $stateMethod = 'as'.str_replace(' ', '', $roleName); // e.g., asBpmStaff, asItAdmin

                if (method_exists($factory, $stateMethod) && Role::where('name', $roleName)->exists()) {
                    $factory->{$stateMethod}()->create();
                    Log::info(sprintf("Created %d users with role '%s'.", $countForRole, $roleName));
                    $totalCreated += $countForRole;
                } elseif ($roleName === 'User' && Role::where('name', $roleName)->exists()) {
                    // Default factory state should assign 'User' role
                    $factory->create(); // Assumes factory's configure() method assigns 'User' role by default
                    Log::info(sprintf("Created %d general users (default 'User' role).", $countForRole));
                    $totalCreated += $countForRole;
                } else {
                    Log::warning(sprintf("State method '%s' for role '%s' not found in UserFactory or role does not exist. Skipping these users.", $stateMethod, $roleName));
                }
            }
        }

        // Create some deleted users for testing soft delete functionalities
        if ($numberOfUsers > 0) { // Only if we are creating users
            $deletedUsersCount = max(2, (int) ($numberOfUsers * 0.05)); // Create a small number of deleted users
            User::factory()->count($deletedUsersCount)->deleted()->create();
            Log::info(sprintf('Created %d deleted user records.', $deletedUsersCount));
            $totalCreated += $deletedUsersCount; // These are also created users
        }

        // Create some pending users for testing pending status functionalities
        $pendingUsersCount = 5; // Example
        if (Role::where('name', 'User')->exists()) { // Assuming pending users get 'User' role
            User::factory()->count($pendingUsersCount)->pending()->create();
            Log::info(sprintf("Created %d users with 'pending' status.", $pendingUsersCount));
            $totalCreated += $pendingUsersCount;
        }

        Log::info(sprintf('General User seeding complete (Revision 3). Aimed for %d active users, total records processed: approximately %d.', $numberOfUsers, $totalCreated));
    }
}
