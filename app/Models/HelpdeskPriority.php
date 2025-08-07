<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskPriority Model.
 *
 * Represents priority levels for helpdesk tickets.
 *
 * @property int $id
 * @property string $name
 * @property int $level
 * @property string|null $color_code
 */
class HelpdeskPriority extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $fillable = [
        'name',
        'level',
        'color_code',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'priority_id');
    }
}
