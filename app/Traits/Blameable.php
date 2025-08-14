<?php

declare(strict_types=1); // Strict types for better code quality and type consistency

namespace App\Traits; // Define the namespace for the trait

use App\Models\User; // Import the User model as it's related to blameable fields
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo for relationship definitions

trait Blameable
{
    /**
     * Get the user who created this model.
     * This method defines a "belongs to" relationship to the User model,
     * linking the 'created_by' foreign key on the current model to the 'id'
     * primary key on the User model.
     * System Design Reference: BlameableObserver (which the trait complements) automatically populates created_by, updated_by, and deleted_by fields.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this model.
     * This method defines a "belongs to" relationship to the User model,
     * linking the 'updated_by' foreign key on the current model to the 'id'
     * primary key on the User model.
     * System Design Reference: BlameableObserver (which the trait complements) automatically populates created_by, updated_by, and deleted_by fields.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who soft deleted this model.
     * This method defines a "belongs to" relationship to the User model,
     * linking the 'deleted_by' foreign key on the current model to the 'id'
     * primary key on the User model. This is used in conjunction with soft deletes.
     * System Design Reference: BlameableObserver (which the trait complements) automatically populates created_by, updated_by, and deleted_by fields.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
