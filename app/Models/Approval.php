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
 * Represents an approval task for various approvable items (e.g., EmailApplication, LoanApplication).
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.4
 *
 * @property int $id
 * @property string $approvable_type Model class name (e.g., EmailApplication::class, LoanApplication::class)
 * @property int $approvable_id ID of the model instance being approved
 * @property int $officer_id User ID of the approving/rejecting officer
 * @property string|null $stage Approval stage identifier (e.g., 'email_support_review', 'loan_hod_review')
 * @property string $status Enum: 'pending', 'approved', 'rejected'
 * @property string|null $comments Officer's comments regarding the decision
 * @property \Illuminate\Support\Carbon|null $approval_timestamp Timestamp of when the approval/rejection decision was made
 * @property int|null $created_by User ID of the creator of this approval record
 * @property int|null $updated_by User ID of the last updater of this approval record
 * @property int|null $deleted_by User ID of the deleter (for soft deletes)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Model|\Eloquent $approvable Polymorphic relation to the item being approved.
 * @property-read \App\Models\User $officer The User who is assigned to make the approval decision.
 * @property-read \App\Models\User|null $creator User who created this approval record.
 * @property-read \App\Models\User|null $updater User who last updated this approval record.
 * @property-read \App\Models\User|null $deleter User who soft-deleted this approval record.
 * @property-read string $statusTranslated Accessor for a human-readable, translated status.
 * @property-read string|null $stageTranslated Accessor for a human-readable, translated stage name.
 *
 * @method static ApprovalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovalTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval withoutTrashed()
 * @mixin \Eloquent
 */
class Approval extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Stage Constants (Refined for clarity and distinction) [cite: 97, 381]
    public const STAGE_EMAIL_SUPPORT_REVIEW = 'email_support_review';
    public const STAGE_EMAIL_ADMIN_REVIEW = 'email_admin_review'; // If IT Admin does a review/approval step

    public const STAGE_LOAN_SUPPORT_REVIEW = 'loan_support_review';
    public const STAGE_LOAN_HOD_REVIEW = 'loan_hod_review';
    public const STAGE_LOAN_BPM_REVIEW = 'loan_bpm_review';
    public const STAGE_GENERAL_REVIEW = 'general_review'; // Generic stage if needed

    // This was in your model. If used for a common 'Support Review' across different types.
    public const STAGE_SUPPORT_REVIEW = 'support_review';


    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING => 'Menunggu Keputusan',
        self::STATUS_APPROVED => 'Diluluskan',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    public static array $STAGES_LABELS = [
        self::STAGE_EMAIL_SUPPORT_REVIEW => 'Sokongan Permohonan E-mel (Pegawai Penyokong)',
        self::STAGE_EMAIL_ADMIN_REVIEW => 'Semakan Pentadbir E-mel (BPM/IT)',
        self::STAGE_LOAN_SUPPORT_REVIEW => 'Sokongan Permohonan Pinjaman (Pegawai Penyokong)',
        self::STAGE_LOAN_HOD_REVIEW => 'Kelulusan Ketua Jabatan (Pinjaman)',
        self::STAGE_LOAN_BPM_REVIEW => 'Semakan & Kelulusan Akhir BPM (Pinjaman)',
        self::STAGE_GENERAL_REVIEW => 'Peringkat Semakan Umum',
        self::STAGE_SUPPORT_REVIEW => 'Peringkat Sokongan Umum',
    ];

    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'officer_id',
        'stage',
        'status',
        'comments',
        'approval_timestamp',
        // created_by, updated_by are typically handled by observers or traits
    ];

    protected $casts = [
        'approval_timestamp' => 'datetime',
        'created_at' => 'datetime', // Eloquent handles these by default but explicit is fine
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING, // Default status for new approval tasks
    ];

    // Static helpers
    public static function getStatusOptions(): array
    {
        // Use translation helper for labels
        return array_map(fn($label) => __($label), self::$STATUSES_LABELS);
    }

    /**
     * Get all possible status values as a simple array of keys.
     *
     * @return array<string>
     */
    public static function getStatuses(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    public static function getStageOptions(): array // Renamed from getStages for consistency
    {
        return array_map(fn($label) => __($label), self::$STAGES_LABELS);
    }

    public static function getStageKeys(): array
    {
        return array_keys(self::$STAGES_LABELS);
    }

    public static function getStageDisplayName(?string $stageKey): string
    {
        if ($stageKey === null) {
            return __('Tidak Berkaitan');
        }
        return __(self::$STAGES_LABELS[$stageKey] ?? Str::title(str_replace('_', ' ', $stageKey)));
    }

    protected static function newFactory(): ApprovalFactory
    {
        return ApprovalFactory::new();
    }

    // Relationships
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    public function getStageTranslatedAttribute(): ?string
    {
        return $this->stage ? self::getStageDisplayName($this->stage) : null;
    }

    /**
     * Eager loads default relationships commonly needed when displaying approval information.
     * This helps prevent N+1 query problems.
     */
    public function loadDefaultRelationships(): self
    {
        if (!$this->relationLoaded('approvable')) {
            $this->load([
                'approvable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => [
                            'user:id,name,department_id,grade_id,position_id', // Select specific fields needed
                            'user.department:id,name',
                            'user.grade:id,name',
                            'user.position:id,name'
                        ],
                        LoanApplication::class => [
                            'user:id,name,department_id,grade_id,position_id', // Select specific fields needed
                            'user.department:id,name',
                            'user.grade:id,name',
                            'user.position:id,name',
                            'applicationItems' // Often needed to understand loan context
                        ],
                        // Add other approvable models here if necessary
                    ]);
                },
            ]);
        }
        if (!$this->relationLoaded('officer')) {
            $this->load('officer:id,name'); // Load only necessary fields
        }
        if (method_exists($this, 'creator') && !$this->relationLoaded('creator')) {
            $this->load('creator:id,name'); // Load only necessary fields
        }
        return $this;
    }
}
