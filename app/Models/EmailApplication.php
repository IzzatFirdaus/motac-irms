<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EmailApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth; // For transitionToStatus default acting user
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Email Application Model.
 * System Design Reference: Section 4.2 Email/User ID Applications
 * @property int $id
 * @property int $user_id (Applicant User ID)
 * @property string|null $previous_department_name (For users transferring)
 * @property string|null $previous_department_email (For users transferring)
 * @property \Illuminate\Support\Carbon|null $service_start_date (For contract/intern)
 * @property \Illuminate\Support\Carbon|null $service_end_date (For contract/intern)
 * @property string|null $application_reason_notes (Corresponds to "Tujuan/Catatan" from MyMail)
 * @property string|null $proposed_email (Corresponds to "Cadangan E-mel ID" from MyMail)
 * @property string|null $group_email (If applying for group email)
 * @property string|null $contact_person_name (Corresponds to "Nama Admin/EO/CC" from MyMail)
 * @property string|null $contact_person_email (Corresponds to "E-mel Admin/EO/CC" from MyMail)
 * @property int|null $supporting_officer_id (Links to User model for approval, from MyMail "MAKLUMAT PEGAWAI PENYOKONG")
 * @property string|null $supporting_officer_name (Manual entry if not a system user, from MyMail)
 * @property string|null $supporting_officer_grade (Manual entry, from MyMail "Gred Penyokong")
 * @property string|null $supporting_officer_email (Manual entry, from MyMail)
 * @property string $status (Application status, from STATUS_CONSTANTS)
 * @property bool $cert_info_is_true (Certification checkbox 1 from MyMail)
 * @property bool $cert_data_usage_agreed (Certification checkbox 2 from MyMail)
 * @property bool $cert_email_responsibility_agreed (Certification checkbox 3 from MyMail)
 * @property \Illuminate\Support\Carbon|null $certification_timestamp (Timestamp for certification)
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email (Assigned by IT Admin)
 * @property string|null $final_assigned_user_id (Assigned by IT Admin)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User $user (Applicant)
 * @property-read \App\Models\User|null $supportingOfficerUser (System user supporting this app)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read string $status_translated (Accessor)
 * @property-read bool $is_group_application (Accessor)
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
class EmailApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Status constants as defined in system design document (Section 4.2)
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support'; // After user submission, before supporting officer approval
    public const STATUS_PENDING_ADMIN = 'pending_admin'; // After supporting officer, before IT Admin processing
    public const STATUS_APPROVED = 'approved'; // This might signify IT Admin approval before actual provisioning. Or could be merged with processing.
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing'; // When IT Admin is actively creating the account
    public const STATUS_PROVISION_FAILED = 'provision_failed'; // If provisioning attempt by IT Admin fails
    public const STATUS_COMPLETED = 'completed'; // Email/ID successfully provisioned and user notified
    // public const STATUS_CANCELLED = 'cancelled'; // User cancels their draft, or admin cancels

    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai',
        self::STATUS_PENDING_ADMIN => 'Menunggu Tindakan Pentadbir IT',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Tindakan IT)', // Final approval before IT creates account
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PROCESSING => 'Sedang Diproses oleh Pentadbir IT',
        self::STATUS_PROVISION_FAILED => 'Proses Penyediaan Gagal',
        self::STATUS_COMPLETED => 'Selesai (Telah Dimaklumkan kepada Pemohon)',
        // self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'email_applications';

    protected $fillable = [
        'user_id',
        'previous_department_name', 'previous_department_email',
        'service_start_date', 'service_end_date',
        'application_reason_notes', // Matches "Tujuan/Catatan" from MyMail form
        'proposed_email', 'group_email',
        'contact_person_name', // Matches "Nama Admin/EO/CC" from MyMail form
        'contact_person_email', // Matches "E-mel Admin/EO/CC" from MyMail form
        'supporting_officer_id', // FK to users table
        'supporting_officer_name', // Manual entry from MyMail form
        'supporting_officer_grade', // Manual entry from MyMail form
        'supporting_officer_email', // Manual entry from MyMail form
        'status',
        'cert_info_is_true', 'cert_data_usage_agreed', 'cert_email_responsibility_agreed',
        'certification_timestamp',
        'rejection_reason', 'final_assigned_email', 'final_assigned_user_id',
        // created_by, updated_by, deleted_by are handled by BlameableObserver
    ];

    protected $casts = [
        'service_start_date' => 'date:Y-m-d',
        'service_end_date' => 'date:Y-m-d',
        'cert_info_is_true' => 'boolean',
        'cert_data_usage_agreed' => 'boolean',
        'cert_email_responsibility_agreed' => 'boolean',
        'certification_timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [ // Default values for new instances
        'status' => self::STATUS_DRAFT,
        'cert_info_is_true' => false,
        'cert_data_usage_agreed' => false,
        'cert_email_responsibility_agreed' => false,
    ];

    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }
    public static function getStatusKeys(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

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
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    public function getIsGroupApplicationAttribute(): bool
    {
        return !empty($this->group_email);
    }

    // Business Logic Methods
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeSubmitted(?User $user = null): bool
    {
        $user = $user ?? Auth::user(); // Use authenticated user if not provided
        if (!$user) {
            return false;
        }
        return $this->isDraft() && (int)$this->user_id === (int)$user->id;
    }

    public function areAllCertificationsComplete(): bool
    {
        return $this->cert_info_is_true &&
               $this->cert_data_usage_agreed &&
               $this->cert_email_responsibility_agreed &&
               $this->certification_timestamp !== null;
    }

    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning("EmailApplication ID {$this->id}: Invalid attempt to transition to status '{$newStatus}'.", [
                'current_status' => $this->status,
                'acting_user_id' => $actingUserId ?? Auth::id(),
            ]);
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $actingUserIdToSet = $actingUserId ?? Auth::id(); // Get acting user if not provided

        if ($newStatus === self::STATUS_REJECTED && $reason) {
            $this->rejection_reason = $reason;
        }
        // Add other side effects based on status transitions if needed
        // e.g., if ($newStatus === self::STATUS_COMPLETED) { $this->provisioned_at = now(); }

        $saved = $this->save();

        if ($saved) {
            Log::info("EmailApplication ID {$this->id} status transitioned from '{$oldStatus}' to '{$newStatus}'.", [
                'acting_user_id' => $actingUserIdToSet,
                'reason' => $reason,
            ]);
            // Consider dispatching an event for notifications or other actions
            // event(new \App\Events\EmailApplicationStatusChanged($this, $oldStatus, $actingUserIdToSet));
        } else {
            Log::error("EmailApplication ID {$this->id}: Failed to save status transition from '{$oldStatus}' to '{$newStatus}'.", [
                'acting_user_id' => $actingUserIdToSet,
            ]);
        }
        return $saved;
    }
}
