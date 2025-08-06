<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes; // Added SoftDeletes trait

class HelpdeskAttachment extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes; // Added SoftDeletes

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    protected $casts = [
        'deleted_at' => 'datetime', // Added cast for soft deletes
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
