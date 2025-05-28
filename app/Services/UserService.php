<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User; //
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final class UserService
{
    private const LOG_AREA = 'UserService:';

    /**
     * Creates a new user.
     *
     * @param  array<string, string|int|bool|null>  $data  User data for creation.
     */
    public function createUser(array $data): User
    {
        Log::info(self::LOG_AREA.' Attempting to create user.', ['data_keys' => array_keys($data)]);
        $preparedData = $this->prepareUserDataForCreation($data);

        return $this->executeInTransaction(
            fn () => User::create($preparedData),
            'create user',
            ['created_user_email' => $preparedData['email'] ?? 'N/A'] // Log context
        );
    }

    /**
     * Updates an existing user.
     *
     * @param  array<string, string|int|bool|null>  $data  User data for update.
     */
    public function updateUser(User $user, array $data): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to update user.', [
            'user_id' => $userId, 'data_keys' => array_keys($data),
        ]);
        $preparedData = $this->preparePasswordForUpdate($data);

        if (empty($preparedData)) { // Check if array is empty after potential unset of password
            Log::info(self::LOG_AREA.' No updatable data provided for user update.', ['user_id' => $userId]);

            return true; // No changes needed, considered successful in this context
        }

        $updated = $this->executeInTransaction(
            fn () => $user->update($preparedData),
            "update user {$userId}",
            ['user_id' => $userId, 'update_data_keys' => array_keys($preparedData)]
        );

        if (! $updated) {
            // This can happen if the data provided is identical to existing data,
            // or if an Eloquent event listener returns false.
            Log::info(self::LOG_AREA.' User update for ID '.$userId.' did not result in changes (data might be the same or update failed silently).');
        }

        return $updated;
    }

    /**
     * Retrieves a user by their ID.
     */
    public function getUserById(int $id): ?User
    {
        Log::debug(self::LOG_AREA.' Retrieving user by ID.', ['user_id' => $id]);
        /** @var User|null $user */
        $user = User::find($id);
        if (! $user) {
            Log::notice(self::LOG_AREA.' User not found by ID.', ['user_id' => $id]);
        }

        return $user;
    }

    /**
     * Retrieves all users, optionally with eager loaded relationships.
     *
     * @param  array<int, string>  $with  Eager load relationships
     */
    public function getAllUsers(array $with = []): EloquentCollection
    {
        $logContext = empty($with) ? ['with_relations' => 'none'] : ['with_relations' => implode(', ', $with)];
        Log::debug(self::LOG_AREA.' Retrieving all users.', $logContext);

        $query = User::query();
        if (! empty($with)) {
            $query->with($with);
        }

        return $query->get();
    }

    /**
     * Filters a user query by applicant's grade ID.
     * Assumes the User model has a 'grade' relationship.
     */
    public function filterByApplicantGrade(EloquentBuilder $query, ?int $applicantGradeId): EloquentBuilder
    {
        if ($applicantGradeId === null) {
            return $query;
        }
        Log::debug(self::LOG_AREA.' Filtering users by applicant grade ID.', ['applicant_grade_id' => $applicantGradeId]);

        return $query->whereHas('grade', function (EloquentBuilder $subQuery) use ($applicantGradeId): void {
            $subQuery->where('id', $applicantGradeId);
        });
    }

    /**
     * Retrieves users who could potentially approve an email application based on department, position, or grade.
     * This method finds users in a specific department AND (matching a specific position OR a specific grade).
     * Its specific use case should be clearly defined within the application's approval strategy,
     * as primary approval routing is typically handled by `ApprovalService`.
     * Assumes User model has 'department', 'position', and 'grade' relationships.
     *
     * >>> MOTAC System Design: Commenting this out as ApprovalService should handle finding approvers.
     */
    // public function getApproversForEmailApplicationApproval(
    //     int $applicantDepartmentId,
    //     int $requiredPositionId,
    //     int $requiredGradeId
    // ): EloquentCollection {
    //     $logContext = [
    //         'department_id' => $applicantDepartmentId,
    //         'position_id' => $requiredPositionId,
    //         'grade_id' => $requiredGradeId,
    //     ];
    //     Log::debug(self::LOG_AREA.' Getting potential email application approvers based on department, position/grade criteria.', $logContext);

    //     return User::query()
    //         ->whereHas('department', fn (EloquentBuilder $q) => $q->where('id', $applicantDepartmentId))
    //         ->where(function (EloquentBuilder $q) use ($requiredPositionId, $requiredGradeId): void {
    //             $q->whereHas('position', fn (EloquentBuilder $pq) => $pq->where('id', $requiredPositionId))
    //                 ->orWhereHas('grade', fn (EloquentBuilder $gq) => $gq->where('id', $requiredGradeId));
    //         })
    //         ->where('status', User::STATUS_ACTIVE) // Only active users
    //         ->get();
    // }

    /**
     * Retrieves users by a specific role name.
     * Relies on the User model using a role management system (e.g., Spatie/laravel-permission).
     */
    public function getUsersByRole(string $roleName): EloquentCollection
    {
        Log::debug(self::LOG_AREA.' Retrieving users with role.', ['role_name' => $roleName]);

        if (! method_exists(User::class, 'role') && ! method_exists(User::query(), 'role')) {
            Log::error(self::LOG_AREA." User model or query builder does not have a 'role' scope/method. Cannot get users by role '{$roleName}'.");

            return new EloquentCollection();
        }

        // This assumes 'role' is a scope, like from Spatie/laravel-permission.
        /** @phpstan-ignore-next-line */
        return User::role($roleName)->where('status', User::STATUS_ACTIVE)->get(); // Only active users
    }

    /**
     * Soft deletes a user.
     */
    public function deleteUser(User $user): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to delete user.', ['user_id' => $userId]);
        $this->ensureNotDeletingAuthenticatedUser($user);

        // deleted_by is handled by User model boot method
        $deleted = $this->executeInTransaction(
            fn () => $user->delete(),
            "delete user {$userId}",
            ['user_id' => $userId]
        );

        $this->logDeletionOutcome($user, $deleted);

        return (bool) $deleted;
    }

    /**
     * Deactivates a user by setting their status to inactive.
     */
    public function deactivateUser(User $user): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to deactivate user.', ['user_id' => $userId]);

        if (! defined(User::class.'::STATUS_INACTIVE')) { //
            Log::error(self::LOG_AREA.' User::STATUS_INACTIVE constant is not defined.');
            throw new RuntimeException('Konfigurasi status pengguna tidak lengkap.');
        }

        if ($user->status === User::STATUS_INACTIVE) {
            Log::info(self::LOG_AREA.' User is already inactive.', ['user_id' => $userId]);

            return true;
        }

        $updated = $this->performUserDeactivation($user);

        if (! $updated) {
            Log::warning(self::LOG_AREA.' User deactivation did not result in DB change for User ID: '.$userId);
        }

        return $updated;
    }

    /**
     * Executes a database transaction with standardized logging and error handling.
     *
     * @template T
     *
     * @param  \Closure(): T  $callback
     * @param  array<string, scalar|array|object|null>  $logContext
     * @return T
     *
     * @throws RuntimeException
     */
    private function executeInTransaction(\Closure $callback, string $actionDescription, array $logContext = [])
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            Log::info(self::LOG_AREA." {$actionDescription} successful.", $logContext);

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA." Failed to {$actionDescription}.", array_merge($logContext, [
                'exception_message' => $e->getMessage(), 'exception_class' => $e::class, 'trace' => $e->getTraceAsString(),
            ]));
            throw new RuntimeException("Gagal untuk {$actionDescription}: ".$e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Prepares user data for creation, hashing password and setting default status.
     *
     * @param  array<string, string|int|bool|null>  $data
     * @return array<string, string|int|bool|null>
     */
    private function prepareUserDataForCreation(array $data): array
    {
        if (isset($data['password']) && is_string($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Consider if a password should be mandatory or auto-generated if not provided
            unset($data['password']);
            Log::warning(self::LOG_AREA.' Password not provided for new user creation. User might not be able to log in.', ['email' => $data['email'] ?? 'N/A']);
        }

        if (! defined(User::class.'::STATUS_ACTIVE')) { //
            Log::critical(self::LOG_AREA.' User::STATUS_ACTIVE constant is not defined. Critical configuration error.');
            throw new RuntimeException('Konfigurasi status pengguna (aktif) tidak dijumpai.');
        }
        $data['status'] = $data['status'] ?? User::STATUS_ACTIVE;

        return $data;
    }

    /**
     * Prepares user data for update, hashing password if provided and changed.
     *
     * @param  array<string, string|int|bool|null>  $data
     * @return array<string, string|int|bool|null>
     */
    private function preparePasswordForUpdate(array $data): array
    {
        if (isset($data['password'])) { // Check if 'password' key exists
            if (is_string($data['password']) && $data['password'] !== '') {
                $data['password'] = Hash::make($data['password']);
            } else {
                // If password key exists but is empty or null, remove it to avoid setting an empty hashed password
                unset($data['password']);
            }
        }

        return $data;
    }

    /**
     * Ensures the authenticated user is not attempting to delete their own account.
     */
    private function ensureNotDeletingAuthenticatedUser(User $userToDelete): void
    {
        if (Auth::check() && $userToDelete->id === Auth::id()) {
            Log::warning(self::LOG_AREA.' Attempt to delete own account prevented.', [
                'user_id' => $userToDelete->id,
            ]);
            throw new RuntimeException('Anda tidak boleh memadam akaun anda sendiri.');
        }
    }

    /**
     * Logs the outcome of a user deletion attempt.
     */
    private function logDeletionOutcome(User $user, ?bool $deletedStatus): void
    {
        $userId = $user->id;
        if ($deletedStatus === true) {
            Log::info(self::LOG_AREA.' User soft deleted successfully.', ['user_id' => $userId]);
        } else {
            // Eloquent's delete() returns false if the model was not deleted (e.g. event listener returned false).
            // It doesn't return null unless an event specifically does.
            Log::warning(self::LOG_AREA.' User deletion failed or no action taken (delete event might have prevented it).', [
                'user_id' => $userId, 'delete_result' => $deletedStatus,
            ]);
        }
    }

    /**
     * Performs the actual deactivation update within a transaction.
     */
    private function performUserDeactivation(User $user): bool
    {
        return $this->executeInTransaction(
            fn () => $user->update(['status' => User::STATUS_INACTIVE]), //
            "deactivate user {$user->id}",
            ['user_id' => $user->id]
        );
    }
}
