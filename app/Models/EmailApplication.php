<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EmailApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Email Application Model.
 * (PHPDoc from your provided file, reflecting model structure)
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property string|null $previous_department_name For users transferring
 * @property string|null $previous_department_email For users transferring
 * @property \Illuminate\Support\Carbon|null $service_start_date For contract/intern
 * @property \Illuminate\Support\Carbon|null $service_end_date For contract/intern
 * @property string|null $purpose (Cadangan E-mel ID/Tujuan/Catatan)
 * @property string|null $proposed_email
 * @property string|null $group_email If applying for group email
 * @property string|null $group_admin_name (Nama Admin/EO/CC for Group Email)
 * @property string|null $group_admin_email (E-mel Admin/EO/CC for Group Email)
 * @property int|null $supporting_officer_id Links to User model for approval
 * @property string|null $supporting_officer_name Manual entry if not a system user
 * @property string|null $supporting_officer_grade Manual entry
 * @property string|null $supporting_officer_email Manual entry
 * @property string $status Application status [cite: 1]
 * @property bool $cert_info_is_true
 * @property bool $cert_data_usage_agreed
 * @property bool $cert_email_responsibility_agreed
 * @property \Illuminate\Support\Carbon|null $certification_timestamp [cite: 1]
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email Assigned by IT Admin
 * @property string|null $final_assigned_user_id Assigned by IT Admin
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user Applicant
 * @property-read \App\Models\User|null $supportingOfficerUser System user supporting this app
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read string $status_translated
 * @property-read bool $is_group_application Accessor
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
class EmailApplication extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PROVISION_FAILED = 'provision_failed';
    public const STATUS_CANCELLED = 'cancelled';

    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan',
        self::STATUS_PENDING_ADMIN => 'Menunggu Tindakan Pentadbir IT',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Proses)',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PROCESSING => 'Sedang Diproses',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_PROVISION_FAILED => 'Proses Gagal',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'email_applications';

    protected $fillable = [
        'user_id',
        'previous_department_name', 'previous_department_email',
        'service_start_date', 'service_end_date',
        'purpose',
        'proposed_email', 'group_email',
        'group_admin_name',
        'group_admin_email',
        'supporting_officer_id', 'supporting_officer_name', 'supporting_officer_grade', 'supporting_officer_email',
        'status', 'cert_info_is_true', 'cert_data_usage_agreed', 'cert_email_responsibility_agreed',
        'certification_timestamp', 'rejection_reason', 'final_assigned_email', 'final_assigned_user_id',
        // Applicant snapshot fields (e.g. applicant_title, applicant_name from StoreEmailApplicationRequest)
        // should be added here IF they are actually stored on this table and not just form fields.
        // Based on design doc table 4.2, they are not stored here.
    ];

    protected $casts = [
        'service_start_date' => 'date:Y-m-d',
        'service_end_date' => 'date:Y-m-d',
        'cert_info_is_true' => 'boolean',
        'cert_data_usage_agreed' => 'boolean',
        'cert_email_responsibility_agreed' => 'boolean',
        'certification_timestamp' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'cert_info_is_true' => false,
        'cert_data_usage_agreed' => false,
        'cert_email_responsibility_agreed' => false,
    ];

    protected static function newFactory(): EmailApplicationFactory
    {
        return EmailApplicationFactory::new();
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supportingOfficerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supporting_officer_id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
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

    public function getIsGroupApplicationAttribute(): bool
    {
        return !empty($this->group_email);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    // Static helper
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getStatusKeys(): array { return array_keys(self::$STATUSES_LABELS); }

    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning("EmailApplication ID {$this->id}: Invalid status transition '{$newStatus}'.", ['acting_user_id' => $actingUserId]);
            return false;
        }
        $oldStatus = $this->status;
        $this->status = $newStatus;
        // Add side effects if needed
        if ($newStatus === self::STATUS_REJECTED && $reason) {
            $this->rejection_reason = $reason;
        }
        $saved = $this->save();
        if ($saved) {
            Log::info("EmailApplication ID {$this->id} status transitioned from {$oldStatus} to {$newStatus}.", ['acting_user_id' => $actingUserId, 'reason' => $reason]);
        }
        return $saved;
    }
}
