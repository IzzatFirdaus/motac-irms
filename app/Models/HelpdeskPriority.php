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
 * @property int         $id
 * @property string      $name
 * @property int         $level
 * @property string|null $color_code
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $display_color_code
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Database\Factories\HelpdeskPriorityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority withoutTrashed()
 * @mixin \Eloquent
 */
class HelpdeskPriority extends Model
{
    use CreatedUpdatedDeletedBy;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'level',
        'color_code',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Tickets assigned with this priority.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'priority_id');
    }

    /**
     * Get the color code for display, fallback to a default color.
     */
    public function getDisplayColorCodeAttribute(): string
    {
        return $this->color_code ?: '#007bff';
    }

    /**
     * Get a human-readable label for this priority.
     */
    public function getLabelAttribute(): string
    {
        return sprintf('%s (Level %d)', $this->name, $this->level);
    }
}
