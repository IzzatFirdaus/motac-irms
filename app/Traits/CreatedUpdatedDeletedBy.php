<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Trait CreatedUpdatedDeletedBy
 *
 * Provides automatic management of created_by, updated_by, and deleted_by fields
 * for Eloquent models using model events. This trait is an alternative or complement
 * to using a global observer. It is compatible with Helpdesk and all v4.0 modules.
 */
trait CreatedUpdatedDeletedBy
{
    public static function bootCreatedUpdatedDeletedBy(): void
    {
        // Set created_by and updated_by on creation
        static::creating(function ($model) {
            $userId = Auth::check() ? Auth::id() : null;
            if (is_null($model->created_by)) {
                $model->created_by = $userId;
            }
            if (is_null($model->updated_by)) {
                $model->updated_by = $userId;
            }
        });

        // Set updated_by on update
        static::updating(function ($model) {
            $userId = Auth::check() ? Auth::id() : null;
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        // If using SoftDeletes, set deleted_by on delete (soft delete only)
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function ($model) {
                $userId = Auth::check() ? Auth::id() : null;
                if (! $model->isDirty('deleted_by')) {
                    $model->deleted_by = $userId;
                }
                // No need for explicit save, Eloquent will persist on delete
            });

            static::restoring(function ($model) {
                $userId = Auth::check() ? Auth::id() : null;
                $model->deleted_by = null;
                if (! $model->isDirty('updated_by')) {
                    $model->updated_by = $userId;
                }
            });
        }
    }
}
