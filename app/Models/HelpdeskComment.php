<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskComment Model.
 *
 * Stores comments on HelpdeskTicket, can be internal or external.
 *
 * @property int    $id
 * @property int    $ticket_id
 * @property int    $helpdesk_ticket_id
 * @property int    $user_id
 * @property string $comment
 * @property bool   $is_internal
 */
class HelpdeskComment extends Model
{
    use CreatedUpdatedDeletedBy, HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * The ticket this comment belongs to.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }

    /**
     * The user who made the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Attachments related to this comment (polymorphic).
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(HelpdeskAttachment::class, 'attachable');
    }

    /**
     * Determine if the comment is internal (not visible to applicant).
     */
    public function isInternal(): bool
    {
        return $this->is_internal;
    }

    /**
     * Determine if the comment is external (visible to applicant).
     */
    public function isExternal(): bool
    {
        return ! $this->is_internal;
    }

    /**
     * Get a short preview of the comment content.
     */
    public function getPreviewAttribute(): string
    {
        return str($this->comment)->limit(80);
    }
}
