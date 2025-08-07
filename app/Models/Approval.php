<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ApprovalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Approval Model.
 *
 * Represents an approval task for a polymorphic "approvable" (e.g., LoanApplication).
 *
 * @property int $id
 * @property string $approvable_type
 * @property int $approvable_id
 * @property string $stage
 * @property int $officer_id
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $resubmitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $officer
 * @property-read \Illuminate\Database\Eloquent\Model $approvable
 * @method static \Database\Factories\ApprovalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> query()
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereApprovableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereApprovableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereResubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<Approval> withoutTrashed()
 * @mixin \Eloquent
 */
class Approval extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_FORWARDED = 'forwarded'; // Used when an approval is forwarded to another officer

    // Approval stages
    public const STAGE_PENDING_HOD_REVIEW = 'pending_hod_review';
    public const STAGE_FINAL_APPROVAL = 'final_approval';
    public const STAGE_LOAN_SUPPORT_REVIEW = 'loan_support_review';
    public const STAGE_LOAN_APPROVER_REVIEW = 'loan_approver_review';
    public const STAGE_LOAN_BPM_REVIEW = 'loan_bpm_review';
    public const STAGE_GENERAL_REVIEW = 'general_review';
    public const STAGE_SUPPORT_REVIEW = 'support_review';

    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING => 'Menunggu Keputusan',
        self::STATUS_APPROVED => 'Diluluskan',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_CANCELED => 'Dibatalkan',
        self::STATUS_FORWARDED => 'Dimajukan',
    ];

    public static array $STAGES_LABELS = [
        self::STAGE_PENDING_HOD_REVIEW => 'Semakan Ketua Jabatan',
        self::STAGE_FINAL_APPROVAL => 'Kelulusan Akhir',
        self::STAGE_LOAN_SUPPORT_REVIEW => 'Semakan Sokongan Pinjaman',
        self::STAGE_LOAN_APPROVER_REVIEW => 'Semakan Pelulus Pinjaman',
        self::STAGE_LOAN_BPM_REVIEW => 'Semakan BPM Pinjaman',
        self::STAGE_GENERAL_REVIEW => 'Semakan Umum',
        self::STAGE_SUPPORT_REVIEW => 'Semakan Sokongan',
    ];

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'stage',
        'officer_id',
        'status',
        'notes',
        'approved_at',
        'rejected_at',
        'canceled_at',
        'resubmitted_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'canceled_at' => 'datetime',
        'resubmitted_at' => 'datetime',
    ];

    /**
     * Polymorphic relationship to the approvable item (e.g., LoanApplication).
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Officer who is responsible for approval task.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Get readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    /**
     * Get readable stage label.
     */
    public function getStageLabelAttribute(): string
    {
        return self::$STAGES_LABELS[$this->stage] ?? Str::title(str_replace('_', ' ', $this->stage));
    }

    /**
     * Get bootstrap color class for status (for badge display).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'text-bg-success',
            self::STATUS_REJECTED => 'text-bg-danger',
            self::STATUS_CANCELED => 'text-bg-dark',
            self::STATUS_PENDING => 'text-bg-warning',
            self::STATUS_FORWARDED => 'text-bg-info',
            default => 'text-bg-secondary',
        };
    }

    /**
     * Eager-load default relationships for display (approvable and officer).
     */
    public function loadDefaultRelationships(): self
    {
        if (! $this->relationLoaded('approvable')) {
            $this->load([
                'approvable' => function (MorphTo $morphTo): void {
                    $morphTo->morphWith([
                        LoanApplication::class => [
                            'user:id,name,department_id,grade_id,position_id',
                            'user.department:id,name',
                            'user.grade:id,name',
                            'user.position:id,name',
                            'loanApplicationItems',
                        ],
                    ]);
                },
            ]);
        }

        if (! $this->relationLoaded('officer')) {
            $this->load('officer:id,name');
        }

        // Optionally load creator if relevant
        if (method_exists($this, 'creator') && ! $this->relationLoaded('creator')) {
            $this->load('creator:id,name');
        }

        return $this;
    }
}
