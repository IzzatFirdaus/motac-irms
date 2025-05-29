<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $file_name Original client-side filename
 * @property string|null $original_file_name Stored if different from file_name or for reference
 * @property string|null $file_path Storage path of the imported file
 * @property int|null $file_size Size in bytes
 * @property string|null $file_ext
 * @property string|null $file_type Type of data being imported
 * @property string $status
 * @property string|null $notes User-provided notes or comments about the import
 * @property string|null $details JSON containing import results, errors, parameters, etc.
 * @property int $total_rows Total rows detected or expected in the file
 * @property int $processed_rows Number of rows successfully processed
 * @property int|null $failed_rows Number of rows that failed during processing
 * @property string|null $completed_at Timestamp when import process finished (completed or failed)
 * @property int|null $user_id User who initiated the import
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFailedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
class Import extends Model
{
    use CreatedUpdatedDeletedBy;
    use HasFactory;
    use SoftDeletes;

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
