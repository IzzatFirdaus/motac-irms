<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ticket model for Helpdesk system (v4.0).
 * Represents a generic helpdesk ticket.
 *
 * @property int $id
 * @property string $subject
 * @property string $description
 * @property int $user_id
 * @property int|null $assigned_to_user_id
 * @property int $category_id
 * @property int $priority_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $resolution_notes
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'description',
        'user_id',
        'assigned_to_user_id',
        'category_id',
        'priority_id',
        'status',
        'due_date',
        'resolution_notes',
        'closed_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(TicketAttachment::class, 'attachable');
    }
}
