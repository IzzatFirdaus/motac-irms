<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
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
    $departmentId = Department::inRandomOrder()->value('id');

    // ##### START: MODIFIED FOR TESTING PURPOSES #####
    // The original line to assign a random grade is commented out.
    // $gradeId = Grade::inRandomOrder()->value('id'); // Original line

    // FOR TESTING ONLY: Assign a grade with level < 41.
    // This prevents factory-created users from having high grades (e.g., Grade 41+)
    // which might interfere with testing approval workflows that rely on specific
    // high-grade users from the AdminUserSeeder (like 'Approver').
    // To revert to production behavior (random grade assignment), uncomment the original line
    // and comment out or remove the following block.
    $gradeForTesting = Grade::where('level', '<', 41)->inRandomOrder()->first();
    if (!$gradeForTesting) {
        // Fallback if no grades < 41 exist (e.g., fresh database before full grade seeding)
        // Try to get ANY grade, or log an error and use null.
        $gradeForTesting = Grade::inRandomOrder()->first();
        if (!$gradeForTesting) {
            Log::warning('UserFactory: No grades found in the database. grade_id will be null for newly created user via factory.');
            $gradeId = null;
        } else {
            $gradeId = $gradeForTesting->id;
            Log::debug("UserFactory: TESTING - No grades with level < 41 found. Using random grade ID: {$gradeId} (Level: {$gradeForTesting->level}).");
        }
    } else {
        $gradeId = $gradeForTesting->id;
        // Log::debug("UserFactory: TESTING - Assigning grade with level < 41. ID: {$gradeId} (Level: {$gradeForTesting->level}).");
    }
    // ##### END: MODIFIED FOR TESTING PURPOSES #####

    $positionId = Position::inRandomOrder()->value('id');

    // Ensure department, position, and grade IDs are valid or handle nulls if necessary
    // This is good practice to prevent foreign key constraint errors if related tables might be empty during testing setup.
    if ($departmentId && !Department::find($departmentId)) {
        Log::warning("UserFactory: Randomly selected department_id {$departmentId} not found. Setting to null.");
        $departmentId = null;
    }
    if ($positionId && !Position::find($positionId)) {
        Log::warning("UserFactory: Randomly selected position_id {$positionId} not found. Setting to null.");
        $positionId = null;
    }
     if ($gradeId && !Grade::find($gradeId)) { // Check if the determined gradeId is valid
        Log::warning("UserFactory: Determined grade_id {$gradeId} not found. Setting to null.");
        $gradeId = null;
    }


    return [
      'name' => $this->faker->name(),
      'email' => $this->faker->unique()->safeEmail(),
      'email_verified_at' => now(), // [cite: 95]
      'password' => Hash::make('password'), // Default password for factory users
      'remember_token' => Str::random(10),
      'profile_photo_path' => null, // [cite: 94]

      'title' => $this->faker->randomElement(array_keys(User::$TITLE_OPTIONS ?? [User::TITLE_ENCIK => 'Encik'])), // [cite: 94]
      'identification_number' => $this->faker->unique()->numerify('##############'), // [cite: 94]
      'passport_number' => $this->faker->optional(0.1)->passthrough(
        $this->faker->unique()->bothify('?#########')
      ), // [cite: 94]
      'department_id' => $departmentId, // [cite: 94]
      'position_id' => $positionId, // [cite: 94]
      'grade_id' => $gradeId, // Uses the gradeId determined by the testing logic above [cite: 94]
      'level' => (string) $this->faker->numberBetween(1, 18), // User's own 'level' field, not grade level [cite: 94]
      'mobile_number' => $this->faker->numerify('01#-#######'), // [cite: 94]
      'personal_email' => $this->faker->optional(0.3)->passthrough(
        $this->faker->unique()->safeEmail()
      ), // [cite: 94]
      'motac_email' => $this->faker->optional(0.5)->passthrough(
        $this->faker->unique()->safeEmail()
      ), // [cite: 94]
      'user_id_assigned' => $this->faker->optional(0.2)->passthrough(
        $this->faker->unique()->bothify('MOTAC####')
      ), // [cite: 94]
      'service_status' => $this->faker->randomElement([
        User::SERVICE_STATUS_TETAP ?? '1', // [cite: 94]
        User::SERVICE_STATUS_KONTRAK_MYSTEP ?? '2', // [cite: 94]
        User::SERVICE_STATUS_PELAJAR_INDUSTRI ?? '3', // [cite: 94, 187]
        User::SERVICE_STATUS_OTHER_AGENCY ?? '4',
      ]),
      'appointment_type' => $this->faker->randomElement([
        User::APPOINTMENT_TYPE_BAHARU ?? '1', // [cite: 94]
        User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN ?? '2', // [cite: 94]
        User::APPOINTMENT_TYPE_LAIN_LAIN ?? '3', // [cite: 94]
      ]),
      'previous_department_name' => null, // [cite: 94]
      'previous_department_email' => null, // [cite: 94]
      'status' => User::STATUS_ACTIVE ?? 'active', // Factory creates active users by default [cite: 94]
      // 'created_by', 'updated_by' are typically handled by BlameableObserver or within seeders. [cite: 92]
    ];
  }

  public function unverified(): static
  {
    return $this->state(fn(array $attributes) => ['email_verified_at' => null]);
  }

  public function configure(): static
  {
    return $this->afterCreating(function (User $user) {
      // Assign 'User' role by default if no other role is assigned and Spatie roles exist
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

  // Methods to assign specific roles after creating a user
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
      if (!$user->hasRole('Approver')) { // HODs are often also Approvers
        $user->assignRole('Approver');
      }
    });
  }
  public function deleted(): static
  {
    return $this->state(fn(array $attributes) => ['deleted_at' => now()]);
  }
}
