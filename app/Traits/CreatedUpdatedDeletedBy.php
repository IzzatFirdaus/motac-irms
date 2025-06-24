<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth; // Import SoftDeletes to check its usage

trait CreatedUpdatedDeletedBy
{
    public static function bootCreatedUpdatedDeletedBy(): void
    {
        // Get the ID of the authenticated user, or null if not authenticated or for system actions.
        // If you have a dedicated "System" user ID, you could use that instead of null for unauthenticated operations.
        $userId = Auth::check() ? Auth::id() : null; // For logging/notes if you ever decide to store name/identifier

        // Set created_by and updated_by when a model is being created.
        static::creating(function ($model) use ($userId): void {
            if (is_null($model->created_by)) { // Only set if not already set (e.g., by seeder)
                $model->created_by = $userId;
            }

            if (is_null($model->updated_by)) { // Also set updated_by on creation
                $model->updated_by = $userId;
            }
        });

        // Set updated_by when a model is being updated.
        static::updating(function ($model) use ($userId): void {
            // Only set if updated_by is not being manually changed during this update operation
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        // If the model uses SoftDeletes, set deleted_by when a model is being soft deleted.
        // The class_uses_recursive function checks if the SoftDeletes trait is used by the model or its parents.
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function ($model) use ($userId): void {
                // Only set if deleted_by is not being manually changed during this delete operation
                if (! $model->isDirty('deleted_by')) {
                    $model->deleted_by = $userId;
                    // When soft deleting, Eloquent handles the save. We just need to set the attribute.
                    // No need for $model->save() or $model->update() here as it will be part of the delete operation.
                    // However, if you needed to force save *only* this change without triggering other events:
                    // $model->saveQuietly();
                }
            });

            // Optional: Clear deleted_by when a model is being restored from soft delete.
            static::restoring(function ($model) use ($userId): void {
                $model->deleted_by = null;
                // Optionally, also update 'updated_by' on restore
                if (! $model->isDirty('updated_by')) {
                    $model->updated_by = $userId;
                }
            });
        }
    }
}
