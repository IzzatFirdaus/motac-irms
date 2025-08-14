<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
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
