<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskAttachment Model.
 *
 * Stores files attached to helpdesk tickets or comments (polymorphic).
 *
 * @property int $id
 * @property string $attachable_type
 * @property int $attachable_id
 * @property string $file_path
 * @property string $file_name
 * @property int $file_size
 * @property string $file_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class HelpdeskAttachment extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
