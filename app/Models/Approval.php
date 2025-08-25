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
 * This model is aligned with the updated approvals table which supports richer workflow:
 * - status as string (pending, approved, rejected, canceled, forwarded)
 * - dedicated decision timestamps (approved_at, rejected_at, canceled_at, resubmitted_at)
 * - notes field
 *
 * @property int $id
 * @property string $approvable_type
 * @property int $approvable_id
 * @property string|null $stage
 * @property int $officer_id
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $resubmitted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Model $approvable
 * @property-read \App\Models\User|null $officer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 *
 * @method static \Database\Factories\ApprovalFactory factory($count = null, $state = [])
 */
class Approval extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ---- STATUS CONSTANTS (must match DB check constraint) ----
    public const STATUS_PENDING   = 'pending';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_CANCELED  = 'canceled';
    public const STATUS_FORWARDED = 'forwarded'; // used when forwarded/resubmitted to another officer

    // ---- STAGE CONSTANTS (expandable as workflow evolves) ----
    public const STAGE_PENDING_HOD_REVIEW  = 'pending_hod_review';
    public const STAGE_FINAL_APPROVAL      = 'final_approval';
    public const STAGE_LOAN_SUPPORT_REVIEW = 'loan_support_review';
    public const STAGE_LOAN_APPROVER_REVIEW = 'loan_approver_review';
    public const STAGE_LOAN_BPM_REVIEW     = 'loan_bpm_review';
    public const STAGE_GENERAL_REVIEW      = 'general_review';
    public const STAGE_SUPPORT_REVIEW      = 'support_review';

    // Human-readable status labels (used by accessors/UI)
    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING   => 'Menunggu Keputusan',
        self::STATUS_APPROVED  => 'Diluluskan',
        self::STATUS_REJECTED  => 'Ditolak',
        self::STATUS_CANCELED  => 'Dibatalkan',
        self::STATUS_FORWARDED => 'Dimajukan',
    ];

    // Human-readable stage labels (used by accessors/UI)
    public static array $STAGES_LABELS = [
        self::STAGE_PENDING_HOD_REVIEW  => 'Semakan Ketua Jabatan',
        self::STAGE_FINAL_APPROVAL      => 'Kelulusan Akhir',
        self::STAGE_LOAN_SUPPORT_REVIEW => 'Semakan Sokongan Pinjaman',
        self::STAGE_LOAN_APPROVER_REVIEW => 'Semakan Pelulus Pinjaman',
        self::STAGE_LOAN_BPM_REVIEW     => 'Semakan BPM Pinjaman',
        self::STAGE_GENERAL_REVIEW      => 'Semakan Umum',
        self::STAGE_SUPPORT_REVIEW      => 'Semakan Sokongan',
    ];

    // Mass-assignable columns (aligned with migration)
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
        // blameable fields are normally filled by observers or seeders; include only if you plan to mass-assign them
        // 'created_by', 'updated_by', 'deleted_by',
    ];

    // Attribute casting
    protected $casts = [
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
        'canceled_at'   => 'datetime',
        'resubmitted_at'=> 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    /**
     * Factory reference override.
     */
    protected static function newFactory(): ApprovalFactory
    {
        return ApprovalFactory::new();
    }

    // ---- RELATIONSHIPS ----

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
     * Creator user (for blameable/audit).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater user (for blameable/audit).
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Deleter user (for blameable/audit).
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ---- ACCESSORS / PRESENTATION HELPERS ----

    /**
     * Get readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * Get readable stage label.
     */
    public function getStageLabelAttribute(): string
    {
        return self::$STAGES_LABELS[$this->stage] ?? Str::title(str_replace('_', ' ', (string) $this->stage));
    }

    /**
     * Get bootstrap color class for status (for badge display).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED  => 'text-bg-success',
            self::STATUS_REJECTED  => 'text-bg-danger',
            self::STATUS_CANCELED  => 'text-bg-dark',
            self::STATUS_PENDING   => 'text-bg-warning',
            self::STATUS_FORWARDED => 'text-bg-info',
            default                => 'text-bg-secondary',
        };
    }

    /**
     * Helper: returns all valid stage keys (for validation/factories/UI).
     */
    public static function getStageKeys(): array
    {
        return array_keys(self::$STAGES_LABELS);
    }

    /**
     * Helper: returns all valid status keys (for validation/factories/UI).
     */
    public static function getStatusKeys(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    /**
     * Eager-load default relationships for display (approvable and officer).
     * This method is handy for controller/view layers to ensure necessary relations are loaded.
     */
    public function loadDefaultRelationships(): self
    {
        if (! $this->relationLoaded('approvable')) {
            $this->load([
                'approvable' => function (MorphTo $morphTo): void {
                    // Preload useful relationships for supported approvable models.
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

        if (! $this->relationLoaded('creator')) {
            $this->load('creator:id,name');
        }

        return $this;
    }

    // ---- SCOPES (useful for querying) ----

    /**
     * Scope: approvals assigned to a given officer.
     */
    public function scopeByOfficer($query, int $officerId)
    {
        return $query->where('officer_id', $officerId);
    }

    /**
     * Scope: approvals for a particular stage.
     */
    public function scopeStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope: pending approvals.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: approved approvals.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: rejected approvals.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope: canceled approvals.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    /**
     * Scope: forwarded approvals.
     */
    public function scopeForwarded($query)
    {
        return $query->where('status', self::STATUS_FORWARDED);
    }
}
