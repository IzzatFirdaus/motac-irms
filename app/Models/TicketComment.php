<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketComment model for Helpdesk system.
 * Stores comments and agent notes for tickets.
 */
class TicketComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Ticket this comment belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * The user (applicant or agent) who made this comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Attachments for this comment (polymorphic)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(TicketAttachment::class, 'attachable');
    }
}
