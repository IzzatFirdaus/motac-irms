<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
  protected $model = User::class;

  public function definition(): array
  {
    $departmentId = Department::inRandomOrder()->value('id');
    $gradeId = Grade::inRandomOrder()->value('id');
    $positionId = Position::inRandomOrder()->value('id');

    return [
      'name' => $this->faker->name(),
      'email' => $this->faker->unique()->safeEmail(),
      'email_verified_at' => now(),
      'password' => Hash::make('password'),
      'remember_token' => Str::random(10),
      'profile_photo_path' => null,

      'title' => $this->faker->randomElement(array_keys(User::$TITLE_OPTIONS ?? [User::TITLE_ENCIK => 'Encik'])),
      'identification_number' => $this->faker->unique()->numerify('##############'),
      'passport_number' => $this->faker->optional(0.1)->passthrough(
        $this->faker->unique()->bothify('?#########')
      ),
      'department_id' => $departmentId,
      'position_id' => $positionId,
      'grade_id' => $gradeId,
      'level' => (string) $this->faker->numberBetween(1, 18),
      'mobile_number' => $this->faker->numerify('01#-#######'),
      'personal_email' => $this->faker->optional(0.3)->passthrough(
        $this->faker->unique()->safeEmail()
      ),
      'motac_email' => $this->faker->optional(0.5)->passthrough(
        $this->faker->unique()->safeEmail()
      ),
      'user_id_assigned' => $this->faker->optional(0.2)->passthrough(
        $this->faker->unique()->bothify('MOTAC####')
      ),
      'service_status' => $this->faker->randomElement([
        User::SERVICE_STATUS_TETAP ?? '1',
        User::SERVICE_STATUS_KONTRAK_MYSTEP ?? '2',
        User::SERVICE_STATUS_PELAJAR_INDUSTRI ?? '3',
        User::SERVICE_STATUS_OTHER_AGENCY ?? '4', // Added new status
      ]),
      'appointment_type' => $this->faker->randomElement([
        User::APPOINTMENT_TYPE_BAHARU ?? '1',
        User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN ?? '2',
        User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3',
      ]),
      'previous_department_name' => null,
      'previous_department_email' => null,
      'status' => User::STATUS_ACTIVE ?? 'active',
      // 'created_by', 'updated_by' handled by BlameableObserver or seeder.
    ];
  }

  public function unverified(): static
  {
    return $this->state(fn(array $attributes) => ['email_verified_at' => null]);
  }

  public function configure(): static
  {
    return $this->afterCreating(function (User $user) {
      if (class_exists(\Spatie\Permission\Models\Role::class) && $user->roles->isEmpty()) {
        $userRole = \Spatie\Permission\Models\Role::where('name', 'User')->first();
        if ($userRole) {
          $user->assignRole($userRole);
        }
      }
    });
  }

  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => User::STATUS_PENDING,
    ]);
  }

  public function asAdmin(): static
  {
    return $this->afterCreating(fn(User $user) => $user->assignRole('Admin'));
  }
  public function asBpmStaff(): static
  {
    return $this->afterCreating(fn(User $user) => $user->assignRole('BPM Staff'));
  }
  public function asItAdmin(): static
  {
    return $this->afterCreating(fn(User $user) => $user->assignRole('IT Admin'));
  }
  public function asApprover(): static
  {
    return $this->afterCreating(fn(User $user) => $user->assignRole('Approver'));
  }
  public function asHod(): static
  {
    return $this->afterCreating(function (User $user) {
      $user->assignRole('HOD');
      if (!$user->hasRole('Approver')) {
        $user->assignRole('Approver');
      }
    });
  }
  public function deleted(): static
  {
    return $this->state(fn(array $attributes) => ['deleted_at' => now()]);
  }
}
