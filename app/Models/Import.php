<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Import Model.
 *
 * Handles import job/file tracking for the system.
 *
 * @property int                             $id
 * @property string                          $file_name
 * @property int|null                        $file_size
 * @property string|null                     $file_ext
 * @property string|null                     $file_type
 * @property string|null                     $status
 * @property string|null                     $details
 * @property int|null                        $current
 * @property int|null                        $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Import extends Model
{
    use CreatedUpdatedDeletedBy, HasFactory, SoftDeletes;

    protected $fillable = [
        'file_name',
        'file_size',
        'file_ext',
        'file_type',
        'status',
        'details',
        'current',
        'total',
    ];
}
