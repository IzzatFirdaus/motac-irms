<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlameableObserver
{
    /**
     * Handle the Model "creating" event.
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
     */
    public function updating(Model $model): void
    {
        if (Auth::check() && $this->hasBlameableColumn($model, 'updated_by')) {
            $model->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Model "deleting" event (for soft deletes).
     * EDITED: Added $model->save() to persist the change.
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
            $model->save(); // This line is crucial to save the deleted_by field.
        }
    }

    /**
     * Handle the Model "restoring" event (for soft deletes).
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
     * Check if the model's table has a specific blameable column.
     */
    private function hasBlameableColumn(Model $model, string $column): bool
    {
        return $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
