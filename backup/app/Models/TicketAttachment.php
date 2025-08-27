<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketAttachment model for Helpdesk system.
 * Stores files attached to tickets or comments (polymorphic).
 *
 * @property int                             $id
 * @property string                          $attachable_type
 * @property int                             $attachable_id
 * @property string                          $file_path
 * @property string                          $file_name
 * @property int                             $file_size
 * @property string                          $file_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class TicketAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

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
     * Polymorphic parent relation: ticket or comment.
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
