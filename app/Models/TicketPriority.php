<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketPriority model for Helpdesk system.
 * Represents priority levels like Low, Medium, High, etc.
 */
class TicketPriority extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'level',
        'color_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Tickets assigned with this priority
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}
