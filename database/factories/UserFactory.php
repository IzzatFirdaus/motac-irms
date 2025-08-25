<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Optimized Factory for the User model.
 *
 * - Uses static caches for Department, Grade, and Position IDs to avoid repeated database queries.
 * - Never creates related models in definition() (for fast batch seeding).
 * - All foreign keys are randomly assigned from existing IDs, or can be set via seeder using state().
 * - Ensures all generated users have unique emails/IC numbers.
 * - Handles soft-deletes, pending status, and role assignment via states and afterCreating hooks.
 * - Uses fallback Faker instance to prevent null errors during early seeding phases.
 *
 * NOTE: Seeder should ensure referenced Departments, Grades, and Positions exist before using this factory.
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Static caches for foreign key IDs to minimize queries during batch seeding
        static $departmentIds, $gradeIds, $positionIds;
        if (!isset($departmentIds)) {
            $departmentIds = Department::pluck('id')->all();
        }
        if (!isset($gradeIds)) {
            $gradeIds = Grade::pluck('id')->all();
        }
        if (!isset($positionIds)) {
            $positionIds = Position::pluck('id')->all();
        }

        // Use a static ms_MY faker for localized names
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Fallback faker instance in case $this->faker is null (can happen during early seeding)
        $faker = $this->faker ?? \Faker\Factory::create();
        // Defensive: ensure $faker is an object with expected methods (some test harnesses may set it to null)
        if (!is_object($faker) || !method_exists($faker, 'bothify')) {
            $faker = \Faker\Factory::create();
        }

        // Pick random IDs from cached arrays, fallback to null if empty
        $departmentId = !empty($departmentIds) ? Arr::random($departmentIds) : null;
        $gradeId = !empty($gradeIds) ? Arr::random($gradeIds) : null;
        $positionId = !empty($positionIds) ? Arr::random($positionIds) : null;

        // Use $msFaker for Malaysian names
        $name = $msFaker->name();

        // Use fallback faker for unique values and standard fake data
        $identificationNumber = $faker->unique()->numerify('############');
        // Generate passport number defensively: fall back to a simple bothify if any faker chain fails
        try {
            $passportNumber = $faker->optional(0.15)->unique()->bothify('??########');
            if ($passportNumber === null) {
                // In rare cases optional()->unique() may yield null; ensure a non-null value
                $passportNumber = \Faker\Factory::create()->bothify('??########');
            }
        } catch (\Throwable $e) {
            $passportNumber = \Faker\Factory::create()->bothify('??########');
        }

        // Safe access to User title options with fallback
        $titleOptions = defined('App\Models\User::TITLE_ENCIK') ?
            (User::$TITLE_OPTIONS ?? [User::TITLE_ENCIK => 'Encik']) :
            ['encik' => 'Encik', 'puan' => 'Puan', 'cik' => 'Cik', 'tuan' => 'Tuan'];
        $titleKey = $faker->randomElement(array_keys($titleOptions));

        // Safe access to User status constants with fallback
        $statusOptions = [
            defined('App\Models\User::STATUS_ACTIVE') ? User::STATUS_ACTIVE : 'active',
            defined('App\Models\User::STATUS_INACTIVE') ? User::STATUS_INACTIVE : 'inactive',
            defined('App\Models\User::STATUS_SUSPENDED') ? User::STATUS_SUSPENDED : 'suspended',
            defined('App\Models\User::STATUS_PENDING') ? User::STATUS_PENDING : 'pending',
        ];

        return [
            // Core authentication columns
            'name' => $name,
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password for all factory users
            'remember_token' => Str::random(10),

            // Domain-specific columns (MOTAC additions)
            'title' => $titleKey,
            'identification_number' => $identificationNumber,
            'passport_number' => $passportNumber,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'grade_id' => $gradeId,

            // Status with fallback values - choose a stable default that matches migrations
            'status' => User::STATUS_ACTIVE,
        ];
    }

    /**
     * State: Mark email as unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * After-creation: Assign default 'User' role if not already assigned.
     * This is only for Eloquent model creation, not raw DB insert.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            // Check if Spatie Permission package is available and user has assignRole method
            if (class_exists(\Spatie\Permission\Models\Role::class) && method_exists($user, 'assignRole')) {
                if ($user->roles->isEmpty()) {
                    $userRole = \Spatie\Permission\Models\Role::where('name', 'User')->first();
                    if ($userRole) {
                        $user->assignRole($userRole);
                    }
                }
            }
        });
    }

    /**
     * State: Set user as pending (for UserSeeder).
     */
    public function pending(): static
    {
        $pendingStatus = defined('App\Models\User::STATUS_PENDING') ? User::STATUS_PENDING : 'pending';
        return $this->state(fn (array $attributes): array => [
            'status' => $pendingStatus,
        ]);
    }

    /**
     * State: Assign user as Admin.
     */
    public function asAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Admin');
            }
        });
    }

    /**
     * State: Assign user as BPM Staff.
     */
    public function asBpmStaff(): static
    {
        return $this->afterCreating(function (User $user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('BPM Staff');
            }
        });
    }

    /**
     * State: Assign user as IT Admin.
     */
    public function asItAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('IT Admin');
            }
        });
    }

    /**
     * State: Assign user as Approver.
     */
    public function asApprover(): static
    {
        return $this->afterCreating(function (User $user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Approver');
            }
        });
    }

    /**
     * State: Assign user as HOD and ensure Approver role.
     */
    public function asHod(): static
    {
        return $this->afterCreating(function (User $user): void {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('HOD');
                if (method_exists($user, 'hasRole') && !$user->hasRole('Approver')) {
                    $user->assignRole('Approver');
                }
            }
        });
    }

    /**
     * State: Mark user as soft-deleted for testing.
     * Also prefixes email and IC to maintain uniqueness.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'email' => 'deleted-' . ($attributes['email'] ?? Str::uuid().'@deleted.local'),
            'identification_number' => 'deleted-' . ($attributes['identification_number'] ?? Str::random(12)),
        ]);
    }
}
