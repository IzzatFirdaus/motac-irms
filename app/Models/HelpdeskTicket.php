<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskTicket Model.
 *
 * Main ticket model for the Helpdesk system.
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $description
 * @property int                             $category_id
 * @property string                          $status
 * @property int                             $priority_id
 * @property int                             $user_id
 * @property int|null                        $assigned_to_user_id
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\User|null $assignedTo
 * @property string|null                     $resolution_notes
 * @property string|null                     $resolution_details
 * @property \Illuminate\Support\Carbon|null $sla_due_at
 * @property int|null                        $closed_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class HelpdeskTicket extends Model
{
    use CreatedUpdatedDeletedBy;
    use HasFactory;
    use SoftDeletes;

    // Status constants for strict status management
    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    // Status options for UI and validation
    public const STATUS_OPTIONS = [
        self::STATUS_OPEN        => 'Open',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_RESOLVED    => 'Resolved',
        self::STATUS_CLOSED      => 'Closed',
    ];

    // Mass assignable attributes
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'status',
        'priority_id',
        'user_id',
        'assigned_to_user_id',
        'closed_at',
        'closed_by_id',
        'resolution_notes',
        'sla_due_at',
    ];

    // Casting attributes to appropriate data types
    protected $casts = [
        'closed_at'  => 'datetime',
        'sla_due_at' => 'datetime',
    ];

    /**
     * Backwards-compatible accessor: some templates refer to 'subject'.
     * Maps to 'title' column.
     */
    public function getSubjectAttribute(): string
    {
        return (string) ($this->attributes['title'] ?? $this->title ?? '');
    }

    /**
     * Backwards-compatible accessor: some UI references 'resolution_details'.
     * Maps to 'resolution_notes'.
     */
    public function getResolutionDetailsAttribute(): ?string
    {
        return $this->attributes['resolution_notes'] ?? $this->resolution_notes ?? null;
    }

    /**
     * Category of the ticket.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpdeskCategory::class, 'category_id');
    }

    /**
     * Priority level of the ticket.
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(HelpdeskPriority::class, 'priority_id');
    }

    /**
     * Applicant (user who created the ticket).
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user assigned to resolve the ticket.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * The user who closed the ticket (if applicable).
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_id');
    }

    /**
     * Comments associated with the ticket.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class, 'ticket_id');
    }

    /**
     * Attachments related to the ticket (polymorphic).
     */
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

    /**
     * Backwards-compatible alias used throughout services.
     */
    public function user(): BelongsTo
    {
        return $this->applicant();
    }

    /**
     * Query scope for open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Query scope for closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Returns true if the ticket is currently closed.
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Returns true if the ticket is currently open.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Returns true if the ticket is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Returns true if the ticket is resolved (but not closed).
     */
    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Get a human-readable label for the status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the last comment (if any).
     */
    public function lastComment()
    {
        return $this->comments()->latest()->first();
    }

    /**
     * Whether the ticket is overdue based on SLA.
     *
     * Returns false when SLA is null or ticket is closed.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        if (is_null($this->sla_due_at)) {
            return false;
        }

        return $this->sla_due_at->isPast();
    }
}
