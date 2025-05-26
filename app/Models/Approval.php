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
 * (PHPDoc from your provided file, confirmed alignment)
 * @property int $id
 * @property string $approvable_type Model class name (e.g., EmailApplication::class)
 * @property int $approvable_id ID of the model being approved
 * @property int $officer_id User ID of the approving/rejecting officer
 * @property string|null $stage Approval stage identifier
 * @property string $status Enum: 'pending', 'approved', 'rejected'
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $approval_timestamp Timestamp of approval/rejection
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $approvable Polymorphic relation
 * @property-read \App\Models\User $officer Approving User
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $statusTranslated Accessor: status_translated
 * @property-read string|null $stageTranslated Accessor: stage_translated
 */
class Approval extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING => 'Menunggu Keputusan',
        self::STATUS_APPROVED => 'Diluluskan',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    // Stage Constants (Refined for clarity and distinction)
    public const STAGE_EMAIL_SUPPORT_REVIEW = 'email_support_review'; // Simplified from previous for broader use
    public const STAGE_EMAIL_ADMIN_REVIEW = 'email_admin_review'; // New, for admin/BPM to review before provisioning

    public const STAGE_LOAN_SUPPORT_REVIEW = 'loan_support_review';
    public const STAGE_LOAN_HOD_REVIEW = 'loan_hod_review';
    public const STAGE_LOAN_BPM_REVIEW = 'loan_bpm_review'; // BPM checks/approves before issuance
    // Consider a generic stage if applicable across multiple types
    public const STAGE_GENERAL_REVIEW = 'general_review';


    public static array $STAGES_LABELS = [
        self::STAGE_EMAIL_SUPPORT_REVIEW => 'Sokongan Permohonan E-mel (Pegawai Penyokong)',
        self::STAGE_EMAIL_ADMIN_REVIEW => 'Semakan Pentadbir E-mel (BPM/IT)',
        self::STAGE_LOAN_SUPPORT_REVIEW => 'Sokongan Permohonan Pinjaman (Pegawai Penyokong)',
        self::STAGE_LOAN_HOD_REVIEW => 'Kelulusan Ketua Jabatan (Pinjaman)',
        self::STAGE_LOAN_BPM_REVIEW => 'Semakan & Kelulusan Akhir BPM (Pinjaman)',
        self::STAGE_GENERAL_REVIEW => 'Peringkat Semakan Umum',
    ];

    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type', 'approvable_id', 'officer_id', 'stage',
        'status', 'comments', 'approval_timestamp',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'approval_timestamp' => 'datetime',
        // 'status' => ApprovalStatusEnum::class, // If using PHP 8.1 Enums
        // 'stage' => ApprovalStageEnum::class, // If using PHP 8.1 Enums
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

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
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getStageTranslatedAttribute(): ?string
    {
        return $this->stage ? (self::$STAGES_LABELS[$this->stage] ?? Str::title(str_replace('_', ' ', (string) $this->stage))) : null;
    }

    // Static helpers
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getStages(): array { return self::$STAGES_LABELS; } // Renamed from getStageOptions for clarity
    public static function getStageKeys(): array { return array_keys(self::$STAGES_LABELS); }

    /**
     * Get display name for a given stage key.
     */
    public static function getStageDisplayName(?string $stageKey): string
    {
        if ($stageKey === null) {
            return __('Tidak Berkaitan');
        }
        return self::$STAGES_LABELS[$stageKey] ?? Str::title(str_replace('_', ' ', $stageKey));
    }
}
