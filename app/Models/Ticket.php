<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ticket model for new Helpdesk system (v4.0).
 * This is the generic ticket model for the Helpdesk module,
 * distinct from the MOTAC-specific HelpdeskTicket (if both exist).
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

    /**
     * Belongs to the applicant (user who submitted the ticket)
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The agent assigned to this ticket
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Ticket's category (e.g., Hardware, Software)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Ticket's priority (e.g., Low, High)
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /**
     * Comments on this ticket
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    /**
     * Attachments for this ticket (polymorphic)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(TicketAttachment::class, 'attachable');
    }
}
