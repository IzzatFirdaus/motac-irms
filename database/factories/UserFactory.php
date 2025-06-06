<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Make sure Log is imported
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
    $positionId = Position::inRandomOrder()->value('id');
    $gradeId = null; // Default to null

    // ##### START: MODIFIED FOR TESTING PURPOSES #####
    // FOR TESTING ONLY: Assign a grade with level < 41 and ensure it has a defined level.
    // This prevents factory-created users from having high grades (e.g., Grade 41+)
    // which might interfere with testing approval workflows that rely on specific
    // high-grade users from the AdminUserSeeder (like 'Approver').
    // To revert to production behavior (e.g., fully random grade assignment or different logic),
    // adjust or remove this block.

    $gradeForTesting = Grade::where('level', '<', 41)->whereNotNull('level')->inRandomOrder()->first(); // Ensure level is not null

    if ($gradeForTesting) {
        $gradeId = $gradeForTesting->id;
        // Log::debug("UserFactory: TESTING - Assigning grade with level < 41. ID: {$gradeId} (Level: {$gradeForTesting->level}).");
    } else {
        // Fallback if no grades < 41 with a defined level exist
        Log::warning("UserFactory: TESTING - No grades with level < 41 and non-null level found. Attempting fallback to any random grade with a non-null level.");
        $anyGradeWithLevel = Grade::whereNotNull('level')->inRandomOrder()->first();
        if ($anyGradeWithLevel) {
            $gradeId = $anyGradeWithLevel->id;
            Log::debug("UserFactory: TESTING - Using fallback random grade ID: {$gradeId} (Level: {$anyGradeWithLevel->level}).");
        } else {
            Log::warning('UserFactory: No grades with a non-null level found in the database at all. grade_id will be null for newly created user via factory.');
            // $gradeId remains null
        }
    }
    // ##### END: MODIFIED FOR TESTING PURPOSES #####

    if ($departmentId && !Department::find($departmentId)) {
        Log::warning("UserFactory: Randomly selected department_id {$departmentId} not found. Setting to null for user being created.");
        $departmentId = null;
    }
    if ($positionId && !Position::find($positionId)) {
        Log::warning("UserFactory: Randomly selected position_id {$positionId} not found. Setting to null for user being created.");
        $positionId = null;
    }
    if ($gradeId && !Grade::find($gradeId)) {
        Log::warning("UserFactory: Determined grade_id {$gradeId} not found in grades table. Setting to null for user being created.");
        $gradeId = null;
    }

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
        User::SERVICE_STATUS_OTHER_AGENCY ?? '4',
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
        } else {
            Log::warning("UserFactory: Default 'User' role not found. User {$user->email} created without this role.");
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
