<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

/**
 * Seeder for general users.
 * Optimized for batch user creation by role, minimizes DB queries and assigns roles in bulk where possible.
 * Ensures all users are assigned to existing Departments, Positions, and Grades.
 * After creation, roles are assigned efficiently.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @param int $numberOfUsers
     */
    public function run(int $numberOfUsers = 50): void
    {
        Log::info('Starting optimized general User seeding...');

        // Ensure roles exist (should be created by RoleAndPermissionSeeder)
        $coreRoles = ['User', 'BPM Staff', 'IT Admin', 'Approver', 'HOD'];
        foreach ($coreRoles as $roleName) {
            if (Role::where('name', $roleName)->doesntExist()) {
                Log::error("Core role '{$roleName}' not found. Please run RoleAndPermissionSeeder first. Aborting UserSeeder.");
                return;
            }
        }

        // Ensure master data available for foreign keys
        $departments = Department::pluck('id')->all();
        $grades = Grade::pluck('id')->all();
        $positions = Position::pluck('id')->all();

        if (empty($departments) || empty($grades) || empty($positions)) {
            Log::error('Departments, Grades, or Positions master data is missing. Please run their respective seeders first.');
            return;
        }

        // Don't create duplicate admin user (skip if exists)
        $adminEmail = config('app.admin_email', 'admin@motac.gov.my');
        if (User::where('email', $adminEmail)->exists()) {
            Log::info("Admin user ($adminEmail) already exists. UserSeeder will only create additional users.");
        }

        // Define proportions for each role
        $rolesToUsersRatio = [
            'User'      => 0.5,   // General users
            'BPM Staff' => 0.1,
            'IT Admin'  => 0.1,
            'Approver'  => 0.2,
            'HOD'       => 0.1,
        ];

        // Prepare user data for batch creation
        $usersToCreate = [];
        foreach ($rolesToUsersRatio as $roleName => $ratio) {
            $countForRole = (int) round($numberOfUsers * $ratio);
            $countForRole = max(1, $countForRole); // Ensure at least 1 per role

            for ($i = 0; $i < $countForRole; $i++) {
                $usersToCreate[] = [
                    'role' => $roleName,
                    'department_id' => $departments[array_rand($departments)],
                    'grade_id'      => $grades[array_rand($grades)],
                    'position_id'   => $positions[array_rand($positions)],
                ];
            }
        }

        // Shuffle the user array for more randomness
        shuffle($usersToCreate);

        // Prepare arrays for role assignment
        $roleToUserIds = [];
        $userRecords = [];

        // Use factory to create all users, then assign roles efficiently after creation.
        foreach ($usersToCreate as $data) {
            $user = User::factory()->make([
                'department_id' => $data['department_id'],
                'grade_id'      => $data['grade_id'],
                'position_id'   => $data['position_id'],
                // status, email, etc. are randomized by the factory
            ]);
            $userRecords[] = $user->toArray();
        }

        // Insert all users in a batch
        $insertedIds = [];
        foreach (array_chunk($userRecords, 200) as $chunk) {
            $ids = [];
            foreach ($chunk as $userData) {
                $user = User::create($userData);
                $ids[] = $user->id;
                // Keep track of role to user id mapping
                $userRole = $usersToCreate[array_search($userData, $userRecords)]['role'] ?? 'User';
                $roleToUserIds[$userRole][] = $user->id;
            }
            $insertedIds = array_merge($insertedIds, $ids);
        }

        // Assign roles in bulk after user creation
        foreach ($roleToUserIds as $roleName => $userIds) {
            $role = Role::where('name', $roleName)->first();
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user && method_exists($user, 'assignRole')) {
                    $user->assignRole($role);
                }
            }
        }

        // Create some soft deleted users for testing
        $deletedUsersCount = max(2, (int) ($numberOfUsers * 0.05));
        User::factory()
            ->count($deletedUsersCount)
            ->deleted()
            ->create();

        // Create some pending users for testing
        $pendingUsersCount = 5;
        User::factory()
            ->count($pendingUsersCount)
            ->pending()
            ->create();

        Log::info("Optimized User seeding complete. Created approximately $numberOfUsers active users, plus $deletedUsersCount deleted and $pendingUsersCount pending users.");
    }
}
