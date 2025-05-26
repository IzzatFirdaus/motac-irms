<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Optional for logging specific observer actions

class BlameableObserver
{
    /**
     * Handle the Model "creating" event.
     * Sets created_by and updated_by with the current authenticated user's ID.
     */
    public function creating(Model $model): void
    {
        $userId = $this->getAuthenticatedUserId();
        if ($userId === null) {
            // Log::debug("BlameableObserver: No authenticated user for creating event on " . get_class($model) . ". Audit fields not set by observer.");
            return;
        }

        // Check if the attribute exists and is not already set or dirty
        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'created_by') && is_null($model->created_by) && ! $model->isDirty('created_by')) {
            $model->created_by = $userId;
        }

        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'updated_by') && is_null($model->updated_by) && ! $model->isDirty('updated_by')) {
            $model->updated_by = $userId;
        }
    }

    /**
     * Handle the Model "updating" event.
     * Sets updated_by with the current authenticated user's ID.
     */
    public function updating(Model $model): void
    {
        $userId = $this->getAuthenticatedUserId();
        if ($userId === null) {
            // Log::debug("BlameableObserver: No authenticated user for updating event on " . get_class($model) . ". 'updated_by' not set by observer.");
            return;
        }

        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'updated_by') && ! $model->isDirty('updated_by')) {
            $model->updated_by = $userId;
        }
    }

    /**
     * Handle the Model "deleting" event.
     * Sets deleted_by with the current authenticated user's ID for soft-deleting models.
     */
    public function deleting(Model $model): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            return; // Not a soft-deleting model, deleted_by is not applicable here.
        }

        // Do not set deleted_by if the model is being force deleted.
        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
            return;
        }

        $userId = $this->getAuthenticatedUserId();
        if ($userId === null) {
            // Log::debug("BlameableObserver: No authenticated user for deleting event on " . get_class($model) . ". 'deleted_by' not set by observer.");
            return;
        }

        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deleted_by') && is_null($model->deleted_by) && ! $model->isDirty('deleted_by')) {
            $model->deleted_by = $userId;
            // The SoftDeletes trait itself will handle saving the model with deleted_at and this new deleted_by.
        }
    }

    /**
     * Handle the Model "restoring" event.
     * Clears deleted_by and sets updated_by.
     */
    public function restoring(Model $model): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            return; // Not a soft-deleting model.
        }

        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deleted_by')) {
            $model->deleted_by = null;
        }

        $userId = $this->getAuthenticatedUserId();
        if ($userId !== null && $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'updated_by') && ! $model->isDirty('updated_by')) {
            $model->updated_by = $userId;
        }
    }

    /**
     * Get the ID of the currently authenticated user.
     */
    private function getAuthenticatedUserId(): ?int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->id;
    }
}
