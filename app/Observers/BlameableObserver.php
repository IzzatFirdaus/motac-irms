<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Trait for SoftDeletes check
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For detailed logging if issues arise

class BlameableObserver
{
    /**
     * Handle the Model "creating" event.
     * Sets created_by and updated_by with the current authenticated user's ID.
     * System Design Reference: 3.1, 8.1 - Audit Trails.
     */
    public function creating(Model $model): void
    {
        $userId = $this->getAuthenticatedUserId();

        // Only set if an authenticated user is available
        if ($userId !== null) {
            // Check if the 'created_by' column exists and is not already set
            if ($this->hasBlameableColumn($model, 'created_by') && is_null($model->created_by)) {
                $model->created_by = $userId;
            }
            // Check if the 'updated_by' column exists and is not already set
            if ($this->hasBlameableColumn($model, 'updated_by') && is_null($model->updated_by)) {
                $model->updated_by = $userId;
            }
        } else {
            // Log::debug("BlameableObserver: No authenticated user for 'creating' event on " . get_class($model) . ". Audit fields (created_by, updated_by) not set by observer.");
        }
    }

    /**
     * Handle the Model "updating" event.
     * Sets updated_by with the current authenticated user's ID.
     */
    public function updating(Model $model): void
    {
        $userId = $this->getAuthenticatedUserId();

        if ($userId !== null) {
            // Check if the 'updated_by' column exists.
            // No need to check isDirty for updated_by on update, as it should always be updated.
            if ($this->hasBlameableColumn($model, 'updated_by')) {
                $model->updated_by = $userId;
            }
        } else {
            // Log::debug("BlameableObserver: No authenticated user for 'updating' event on " . get_class($model) . ". 'updated_by' not set by observer.");
        }
    }

    /**
     * Handle the Model "deleting" event (for soft deletes).
     * Sets deleted_by with the current authenticated user's ID.
     */
    public function deleting(Model $model): void
    {
        // Ensure the model uses SoftDeletes trait
        if (!in_array(SoftDeletes::class, class_uses_recursive(get_class($model)), true)) {
            return; // Not a soft-deleting model
        }

        // Do not set deleted_by if the model is being force deleted
        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
            return;
        }

        $userId = $this->getAuthenticatedUserId();

        if ($userId !== null) {
            // Check if the 'deleted_by' column exists and is not already set
            if ($this->hasBlameableColumn($model, 'deleted_by') && is_null($model->deleted_by)) {
                // The observer sets the value. The SoftDeletes trait's listeners will then persist it
                // along with deleted_at when the model's delete method calls save().
                // To ensure it's saved, we modify the model attribute directly.
                // The actual save is handled by the delete operation of SoftDeletes.
                $model->deleted_by = $userId;

                // If the model is not configured to save on deleting (unlikely for SoftDeletes),
                // an explicit save might be needed, but SoftDeletes handles this.
                // $model->saveQuietly(); // Generally not needed here for SoftDeletes
            }
        } else {
            // Log::debug("BlameableObserver: No authenticated user for 'deleting' event on " . get_class($model) . ". 'deleted_by' not set by observer.");
        }
    }

    /**
     * Handle the Model "restoring" event (for soft deletes).
     * Clears deleted_by and sets updated_by.
     */
    public function restoring(Model $model): void
    {
        if (!in_array(SoftDeletes::class, class_uses_recursive(get_class($model)), true)) {
            return; // Not a soft-deleting model
        }

        if ($this->hasBlameableColumn($model, 'deleted_by')) {
            $model->deleted_by = null;
        }

        // Also set updated_by when restoring, as it's an update operation
        $userId = $this->getAuthenticatedUserId();
        if ($userId !== null && $this->hasBlameableColumn($model, 'updated_by')) {
            $model->updated_by = $userId;
        }
    }

    /**
     * Get the ID of the currently authenticated user.
     * Returns null if no user is authenticated (e.g., in console commands or seeders without actingAs).
     */
    private function getAuthenticatedUserId(): ?int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->id;
    }

    /**
     * Check if the model's table has a specific blameable column.
     */
    private function hasBlameableColumn(Model $model, string $column): bool
    {
        // Caching this schema check per model per request can optimize if called frequently.
        // For simplicity, direct check here.
        return $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
