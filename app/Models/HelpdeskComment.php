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
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $comment
 * @property bool $is_internal
 */
class HelpdeskComment extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(HelpdeskAttachment::class, 'attachable');
    }
}
