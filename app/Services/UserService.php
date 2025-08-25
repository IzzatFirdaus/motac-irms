<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException; // Added import for built-in PHP exception
use RuntimeException;
use Throwable;

/**
 * Service class for managing User-related business logic.
 * System Design Reference: Sections 3.1 (Services), 9.1
 */
final class UserService
{
    private const LOG_AREA = 'UserService:';

    /**
     * Creates a new user.
     * Password should be provided in $data and will be hashed.
     * Default status is 'active'.
     * Blameable fields (created_by, updated_by) are expected to be handled by
     * the CreatedUpdatedDeletedBy trait or a BlameableObserver on the User model.
     *
     * @param  array<string, mixed>  $data  User data including 'password'.
     * @return User The newly created User model.
     *
     * @throws InvalidArgumentException If essential data like password is missing.
     * @throws RuntimeException If user creation fails at the database level.
     */
    public function createUser(array $data): User
    {
        Log::info(self::LOG_AREA.' Attempting to create user.', ['data_keys' => array_keys($data)]);
        $preparedData = $this->prepareUserDataForCreation($data);

        return $this->executeInTransaction(
            fn () => User::create($preparedData),
            'create user',
            ['created_user_email' => $preparedData['email'] ?? 'N/A']
        );
    }

    /**
     * Updates an existing user.
     * If 'password' is provided in $data and is not empty, it will be hashed and updated.
     * If 'password' key exists in $data but its value is empty, it's ignored (password not changed).
     * Blameable field (updated_by) is expected to be handled by User model's trait/observer.
     *
     * @param  User  $user  The User instance to update.
     * @param  array<string, mixed>  $data  Data to update.
     * @return bool True if the update resulted in changes being saved, false otherwise.
     *
     * @throws RuntimeException If user update fails due to an exception.
     */
    public function updateUser(User $user, array $data): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to update user.', [
            'user_id' => $userId, 'data_keys' => array_keys($data),
        ]);
        $preparedData = $this->preparePasswordForUpdate($data);

        if ($preparedData === []) {
            Log::info(self::LOG_AREA.' No updatable data provided for user update (after password preparation).', ['user_id' => $userId]);

            return true; // No actual changes needed, operation considered successful.
        }

        $updated = $this->executeInTransaction(
            fn () => $user->update($preparedData),
            'update user '.$userId,
            ['user_id' => $userId, 'update_data_keys' => array_keys($preparedData)]
        );

        if (! $updated) {
            Log::info(self::LOG_AREA.' User update for ID '.$userId.' did not result in changes or failed silently (event prevented save).');
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
     * @param  array<int, string>  $with  Relationships to eager load.
     * @return EloquentCollection<int, User>
     */
    public function getAllUsers(array $with = []): EloquentCollection
    {
        $logContext = $with === [] ? ['with_relations' => 'none'] : ['with_relations' => implode(', ', $with)];
        Log::debug(self::LOG_AREA.' Retrieving all users.', $logContext);

        $query = User::query();
        if ($with !== []) {
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
     * Retrieves users by a specific role name.
     * Relies on the User model using a role management system (e.g., Spatie/laravel-permission).
     * Only returns active users.
     */
    public function getUsersByRole(string $roleName): EloquentCollection
    {
        Log::debug(self::LOG_AREA.' Retrieving active users with role.', ['role_name' => $roleName]);

        if (! method_exists(User::class, 'role') && ! method_exists(User::query(), 'role')) {
            Log::error(self::LOG_AREA.sprintf(" User model or query builder does not have a 'role' scope/method. Cannot get users by role '%s'. Ensure Spatie/laravel-permission or similar is correctly set up on User model.", $roleName));

            return new EloquentCollection();
        }

        /** @phpstan-ignore-next-line */
        return User::role($roleName)->where('status', User::STATUS_ACTIVE)->get();
    }

    /**
     * Soft deletes a user. Prevents self-deletion.
     * Blameable field (deleted_by) is expected to be handled by User model's trait/observer.
     */
    public function deleteUser(User $user): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to soft delete user.', ['user_id' => $userId]);
        $this->ensureNotDeletingAuthenticatedUser($user);

        $deleted = $this->executeInTransaction(
            fn () => $user->delete(),
            'soft delete user '.$userId,
            ['user_id' => $userId]
        );
        $this->logDeletionOutcome($user, $deleted);

        return (bool) $deleted;
    }

    /**
     * Deactivates a user by setting their status to inactive.
     * Blameable field (updated_by) is expected to be handled by User model's trait/observer.
     */
    public function deactivateUser(User $user): bool
    {
        $userId = $user->id;
        Log::info(self::LOG_AREA.' Attempting to deactivate user.', ['user_id' => $userId]);

        if (! defined(User::class.'::STATUS_INACTIVE')) {
            Log::critical(self::LOG_AREA.' User::STATUS_INACTIVE constant is not defined in User model.');
            throw new RuntimeException('Konfigurasi status pengguna tidak lengkap untuk proses nyahaktif.');
        }

        if ($user->status === User::STATUS_INACTIVE) {
            Log::info(self::LOG_AREA.' User is already inactive.', ['user_id' => $userId]);

            return true;
        }

        $updated = $this->performUserDeactivation($user);

        if (! $updated) {
            Log::warning(self::LOG_AREA.' User deactivation did not result in DB change for User ID: '.$userId.' (possibly due to events or no actual change).');
        }

        return $updated;
    }

    /**
     * Executes a database callback within a transaction with standardized logging and error handling.
     *
     * @template T
     *
     * @param  \Closure(): T  $callback  The database operation to execute.
     * @param  string  $actionDescription  A description of the action for logging.
     * @param  array<string, mixed>  $logContext  Additional context for logging.
     * @return T The result of the callback.
     *
     * @throws RuntimeException If the transaction fails.
     */
    private function executeInTransaction(\Closure $callback, string $actionDescription, array $logContext = [])
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            Log::info(self::LOG_AREA.' Successfully '.$actionDescription.'.', $logContext);

            return $result;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to '.$actionDescription.'.', array_merge($logContext, [
                'exception_message' => $throwable->getMessage(),
                'exception_class' => get_class($throwable),
                'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500),
            ]));
            throw new RuntimeException(__('Gagal untuk ').$actionDescription.': '.$throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * Prepares user data for creation: hashes password and sets default status.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException if password is not provided or invalid.
     */
    private function prepareUserDataForCreation(array $data): array
    {
        if (isset($data['password']) && is_string($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            Log::error(self::LOG_AREA.' Password was not provided or was invalid during user creation preparation.', ['email' => $data['email'] ?? 'N/A']);
            // Correctly throwing the SPL InvalidArgumentException from global namespace
            throw new InvalidArgumentException(__('Kata laluan diperlukan untuk mencipta pengguna.'));
        }

        if (! defined(User::class.'::STATUS_ACTIVE')) {
            Log::critical(self::LOG_AREA.' User::STATUS_ACTIVE constant is not defined. Critical configuration error.');
            throw new RuntimeException('Konfigurasi status pengguna (aktif) tidak dijumpai.');
        }

        $data['status'] = $data['status'] ?? User::STATUS_ACTIVE;
        if (method_exists(User::class, 'getStatusKeys') && ! in_array($data['status'], User::getStatusKeys())) {
            Log::warning(self::LOG_AREA.sprintf("Invalid status '%s' provided for user creation. Defaulting to active.", $data['status']), ['email' => $data['email'] ?? 'N/A']);
            $data['status'] = User::STATUS_ACTIVE;
        }

        return $data;
    }

    /**
     * Prepares password for update: hashes if provided and non-empty, unsets if key exists but value is empty.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function preparePasswordForUpdate(array $data): array
    {
        if (array_key_exists('password', $data)) {
            if (! empty($data['password']) && is_string($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
        }

        return $data;
    }

    /**
     * Ensures the authenticated user is not attempting to delete their own account.
     *
     * @throws RuntimeException If self-deletion is attempted.
     */
    private function ensureNotDeletingAuthenticatedUser(User $userToDelete): void
    {
        if (Auth::check() && $userToDelete->id === Auth::id()) {
            Log::warning(self::LOG_AREA.' Attempt to delete own account prevented.', [
                'user_id' => $userToDelete->id,
            ]);
            throw new RuntimeException(__('Anda tidak boleh memadam akaun anda sendiri.'));
        }
    }

    /**
     * Logs the outcome of a user soft deletion attempt.
     */
    private function logDeletionOutcome(User $user, ?bool $deletedStatus): void
    {
        $userId = $user->id;
        if ($deletedStatus === true) {
            Log::info(self::LOG_AREA.' User soft deleted successfully.', ['user_id' => $userId]);
        } else {
            Log::warning(self::LOG_AREA.' User soft deletion failed or no action taken (delete event might have prevented it).', [
                'user_id' => $userId, 'delete_result' => $deletedStatus,
            ]);
        }
    }

    /**
     * Performs the actual user deactivation update within a transaction.
     */
    private function performUserDeactivation(User $user): bool
    {
        return $this->executeInTransaction(
            fn () => $user->update(['status' => User::STATUS_INACTIVE]),
            'deactivate user '.$user->id,
            ['user_id' => $user->id]
        );
    }
}
