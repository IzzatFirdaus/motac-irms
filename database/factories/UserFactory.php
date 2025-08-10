<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory for the User model.
 *
 * - Aligned with all user-related migrations, including MOTAC columns.
 * - Foreign keys are always set to existing records, falling back to factory creation with unique values if empty.
 * - Only includes columns that exist in the DB schema/model (see migrations and User.php).
 * - Handles soft deletes and role assignment for seeder compatibility.
 * - Uses ms_MY for localized names and phone numbers.
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Use English faker for optional/unique chains to avoid null
        $faker = $this->faker ?? \Faker\Factory::create('en_US');
        // Use Malaysian locale for names and phone
        $msFaker = \Faker\Factory::create('ms_MY');

        // Ensure referenced records exist, or create with unique fallback
        $department = Department::inRandomOrder()->first();
        if (!$department) {
            $department = Department::factory()->create([
                'name' => 'Dept Autogen (UserFactory)',
                'code' => 'AUTO' . $faker->unique()->bothify('###'),
            ]);
        }
        $position = Position::inRandomOrder()->first();
        if (!$position) {
            $position = Position::factory()->create([
                'name' => 'Pos Autogen (UserFactory)',
            ]);
        }
        $grade = Grade::inRandomOrder()->first();
        if (!$grade) {
            $grade = Grade::factory()->create([
                'name' => 'Grade Autogen (UserFactory)',
            ]);
        }

        // Generate user data
        $name = $msFaker->name();
        $identificationNumber = $faker->unique()->numerify('############');
        $passportNumber = $faker->optional(0.15)->unique()->bothify('??########');
        $titleKey = $faker->randomElement(array_keys(User::$TITLE_OPTIONS ?? [User::TITLE_ENCIK => 'Encik']));

        return [
            // Core authentication columns
            'name' => $name,
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default for all seed users
            'remember_token' => Str::random(10),

            // MOTAC/MOTAC-added domain fields
            'title' => $titleKey,
            'identification_number' => $identificationNumber,
            'passport_number' => $passportNumber,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'grade_id' => $grade->id,
            'phone_number' => $msFaker->phoneNumber(), // Malaysian format
            'status' => $faker->randomElement([
                User::STATUS_ACTIVE,
                User::STATUS_INACTIVE,
                User::STATUS_SUSPENDED,
                User::STATUS_PENDING,
            ]),
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
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
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
        return $this->state(fn (array $attributes): array => [
            'status' => User::STATUS_PENDING,
        ]);
    }

    /**
     * State: Assign user as Admin.
     */
    public function asAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('Admin'));
    }

    /**
     * State: Assign user as BPM Staff.
     */
    public function asBpmStaff(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('BPM Staff'));
    }

    /**
     * State: Assign user as IT Admin.
     */
    public function asItAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('IT Admin'));
    }

    /**
     * State: Assign user as Approver.
     */
    public function asApprover(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('Approver'));
    }

    /**
     * State: Assign user as HOD and ensure Approver role.
     */
    public function asHod(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->assignRole('HOD');
            if (! $user->hasRole('Approver')) {
                $user->assignRole('Approver');
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
