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
                $this->faker->unique()->bothify('?#########')
            ),
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'grade_id' => $gradeId,
            'level' => (string) $this->faker->numberBetween(1, 18),
            'mobile_number' => $msFaker->mobileNumber(false, true),
            'personal_email' => $this->faker->optional(0.3)->passthrough(
                $this->faker->unique()->safeEmail()
            ),
            'motac_email' => $this->faker->optional(0.5)->passthrough(
                Str::slug($name, '.').'@motac.gov.my'
            ),
            'user_id_assigned' => $this->faker->optional(0.2)->passthrough(
                $this->faker->unique()->bothify('MOTAC####')
            ),
            'service_status' => $this->faker->randomElement([
                User::SERVICE_STATUS_TETAP ?? '1',
                User::SERVICE_STATUS_KONTRAK_MYSTEP ?? '2',
                User::SERVICE_STATUS_PELAJAR_INDUSTRI ?? '3',
            ]),
            'appointment_type' => $this->faker->randomElement([
                User::APPOINTMENT_TYPE_BAHARU ?? '1',
                User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN ?? '2',
                User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3',
            ]),
            'previous_department_name' => null,
            'previous_department_email' => null,
            'status' => User::STATUS_ACTIVE ?? 'active',
        ];
    }

    public function canBeLoanApprover(): static
    {
        return $this->state(function (array $attributes) {
            $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
            $grade = Grade::where('level', '>=', $minSupportGradeLevel)->whereNotNull('level')->inRandomOrder()->first();

            if (! $grade) {
                Log::warning("UserFactory: No grade found with level >= {$minSupportGradeLevel}. Cannot create a loan approver.");
            }

            return ['grade_id' => $grade?->id];
        });
    }

    public function cannotBeLoanApprover(): static
    {
        return $this->state(function (array $attributes) {
            $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
            $grade = Grade::where('level', '<', $minSupportGradeLevel)->whereNotNull('level')->inRandomOrder()->first();

            if (! $grade) {
                Log::warning("UserFactory: No grade found with level < {$minSupportGradeLevel}. Cannot create a non-approver user reliably.");
            }

            return ['grade_id' => $grade?->id];
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if (class_exists(\Spatie\Permission\Models\Role::class) && $user->roles->isEmpty()) {
                $userRole = \Spatie\Permission\Models\Role::where('name', 'User')->first();
                if ($userRole) {
                    $user->assignRole($userRole);
                } else {
                    Log::warning("UserFactory: Default 'User' role not found. User {$user->email} created without this role.");
                }
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => User::STATUS_PENDING]);
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
        return $this->afterCreating(function (User $user) {
            $user->assignRole('HOD');
            if (! $user->hasRole('Approver')) {
                $user->assignRole('Approver');
            }
        });
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => ['deleted_at' => now()]);
    }
}
