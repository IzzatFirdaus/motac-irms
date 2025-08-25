<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketAttachment Model (Helpdesk Ticket Attachment).
 *
 * Represents file attachments for helpdesk tickets and comments.
 *
 * @property int $id
 * @property int $ticket_id
 * @property int|null $comment_id
 * @property int $user_id
 * @property string $filename
 * @property string $filepath
 * @property string|null $mime_type
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\TicketComment|null $comment
 * @property-read \App\Models\User $user
 */
class TicketAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ticket_attachments';

    protected $fillable = [
        'ticket_id',
        'comment_id',
        'user_id',
        'filename',
        'filepath',
        'mime_type',
    ];

    protected $casts = [
        'ticket_id' => 'integer',
        'comment_id' => 'integer',
        'user_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Ticket this attachment belongs to.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Comment this attachment belongs to (optional).
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(TicketComment::class, 'comment_id');
    }

    /**
     * User who uploaded the attachment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
