<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Observer for automatically handling blameable fields (created_by, updated_by, deleted_by)
 * on Eloquent models. This observer is attached globally or to specific models to handle
 * audit trails for CRUD operations and soft deletes.
 *
 * Compatible with v4.0: No references to email provisioning or legacy modules.
 */
class BlameableObserver
{
    /**
     * Handle the Model "creating" event.
     * Sets created_by and updated_by to the current authenticated user's ID.
     */
    public function creating(Model $model): void
    {
        if (Auth::check()) {
            $userId = Auth::id();
            if ($this->hasBlameableColumn($model, 'created_by')) {
                $model->created_by = $userId;
            }
            if ($this->hasBlameableColumn($model, 'updated_by')) {
                $model->updated_by = $userId;
            }
        }
    }

    /**
     * Handle the Model "updating" event.
     * Sets updated_by to the current authenticated user's ID.
     */
    public function updating(Model $model): void
    {
        if (Auth::check() && $this->hasBlameableColumn($model, 'updated_by')) {
            $model->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Model "deleting" event (for soft deletes).
     * Sets deleted_by to the current authenticated user's ID.
     * If the model uses SoftDeletes, this ensures an audit trail for who initiated the delete.
     */
    public function deleting(Model $model): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive(get_class($model)))) {
            return;
        }
        if ($model->isForceDeleting()) {
            return;
        }
        if (Auth::check() && $this->hasBlameableColumn($model, 'deleted_by')) {
            $model->deleted_by = Auth::id();
            $model->save(); // Important: persists the deleted_by update before soft delete.
        }
    }

    /**
     * Handle the Model "restoring" event (for soft deletes).
     * Clears deleted_by and updates updated_by.
     */
    public function restoring(Model $model): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive(get_class($model)))) {
            return;
        }
        if ($this->hasBlameableColumn($model, 'deleted_by')) {
            $model->deleted_by = null;
        }
        if (Auth::check() && $this->hasBlameableColumn($model, 'updated_by')) {
            $model->updated_by = Auth::id();
        }
    }

    /**
     * Utility: Checks if the model's table has the given blameable column.
     */
    private function hasBlameableColumn(Model $model, string $column): bool
    {
        return $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
