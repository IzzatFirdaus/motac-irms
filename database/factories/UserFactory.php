<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $departmentId = Department::inRandomOrder()->value('id');
        $positionId = Position::inRandomOrder()->value('id');
        $gradeId = Grade::inRandomOrder()->value('id');

        $name = $msFaker->name();

        return [
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'title' => $this->faker->randomElement(array_keys(User::$TITLE_OPTIONS ?? [User::TITLE_ENCIK => 'Encik'])),
            'identification_number' => $msFaker->unique()->myKadNumber(),
            'passport_number' => $this->faker->optional(0.1)->passthrough(
                $msFaker->unique()->bothify('?#########')
            ),
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'grade_id' => $gradeId,
            'mobile_number' => $msFaker->unique()->phoneNumber(),
            'office_number' => $msFaker->optional(0.7)->unique()->phoneNumber(),
            'address' => $msFaker->address(),
            'city' => $msFaker->city(),
            'state' => $msFaker->state(),
            'postcode' => $msFaker->postcode(),
            'country' => 'Malaysia',
            'status' => $this->faker->randomElement([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_PENDING]),
            'last_login_at' => $this->faker->optional(0.8)->dateTimeThisYear(),
            'last_login_ip' => $this->faker->optional(0.8)->ipv4(),
            'remarks' => $this->faker->optional(0.3)->sentence(),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => ['email_verified_at' => null]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if (class_exists(\Spatie\Permission\Models\Role::class) && $user->roles->isEmpty()) {
                $userRole = \Spatie\Permission\Models\Role::where('name', 'User')->first();
                if ($userRole) {
                    $user->assignRole($userRole);
                } else {
                    Log::warning(sprintf("UserFactory: Default 'User' role not found. User %s created without this role.", $user->email));
                }
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => User::STATUS_PENDING]);
    }

    public function asAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('Admin'));
    }

    public function asBpmStaff(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('BPM Staff'));
    }

    public function asItAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('IT Admin'));
    }

    public function asApprover(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('Approver'));
    }

    public function asHod(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->assignRole('HOD');
            if (! $user->hasRole('Approver')) {
                $user->assignRole('Approver');
            }
        });
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
            'email' => 'deleted-'.$attributes['email'], // Mark email as deleted
            'identification_number' => 'deleted-'.$attributes['identification_number'], // Mark IC as deleted
        ]);
    }
}
