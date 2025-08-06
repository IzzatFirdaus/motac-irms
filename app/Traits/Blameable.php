<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait Blameable
 *
 * Provides relationships for blameable fields (created_by, updated_by, deleted_by).
 * Used in conjunction with the BlameableObserver to maintain audit trails.
 */
trait Blameable
{
    /**
     * Relationship: The user who created this model.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: The user who last updated this model.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: The user who soft deleted this model (if using soft deletes).
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
