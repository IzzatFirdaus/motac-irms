<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketAttachment model for Helpdesk system.
 * Stores files attached to tickets or comments (polymorphic).
 */
class TicketAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent attachable model (ticket or comment)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
