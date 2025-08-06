<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class HelpdeskTicket extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    // Define status constants
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';
    // Add other statuses as needed based on your system design

    // Status options for use in factories and UI
    public const STATUS_OPTIONS = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_RESOLVED => 'Resolved',
        self::STATUS_CLOSED => 'Closed',
    ];

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'status',
        'priority_id',
        'user_id',
        'assigned_to_user_id',
        'closed_at',
        'resolution_notes',
        'sla_due_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'sla_due_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpdeskCategory::class, 'category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(HelpdeskPriority::class, 'priority_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class, 'ticket_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(HelpdeskAttachment::class, 'attachable');
    }

    /**
     * Get the latest comment for the ticket.
     */
    public function latestComment(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class, 'ticket_id')->latest();
    }
}
