<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ticket Model (Helpdesk Ticket).
 *
 * Represents a helpdesk ticket submitted by users for ICT support.
 *
 * @property int $id
 * @property string $subject
 * @property string $description
 * @property int $user_id
 * @property int $department_id
 * @property int $category_id
 * @property int $priority_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property int|null $resolved_by
 * @property int|null $assigned_to
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\TicketCategory $category
 * @property-read \App\Models\TicketPriority $priority
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\User|null $resolver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketComment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketAttachment> $attachments
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = [
        'subject',
        'description',
        'user_id',
        'department_id',
        'category_id',
        'priority_id',
        'status',
        'resolved_at',
        'resolved_by',
        'assigned_to',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'department_id' => 'integer',
        'category_id' => 'integer',
        'priority_id' => 'integer',
        'resolved_by' => 'integer',
        'assigned_to' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * User who submitted the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Department related to the ticket.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Ticket category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Ticket priority.
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /**
     * User assigned to resolve the ticket.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * User who resolved the ticket.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Comments on the ticket.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    /**
     * Attachments for the ticket.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id');
    }
}
