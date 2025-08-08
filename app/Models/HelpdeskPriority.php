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
