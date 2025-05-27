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
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property string|null $previous_department_name For users transferring
 * @property string|null $previous_department_email For users transferring
 * @property \Illuminate\Support\Carbon|null $service_start_date For contract/intern
 * @property \Illuminate\Support\Carbon|null $service_end_date For contract/intern
 * @property string|null $application_reason_notes Renamed from 'purpose' to match design doc field name
 * @property string|null $proposed_email
 * @property string|null $group_email If applying for group email
 * @property string|null $contact_person_name Renamed from 'group_admin_name' to match design doc field name (Nama Admin/EO/CC)
 * @property string|null $contact_person_email Renamed from 'group_admin_email' to match design doc field name (E-mel Admin/EO/CC)
 * @property int|null $supporting_officer_id Links to User model for approval
 * @property string|null $supporting_officer_name Manual entry if not a system user
 * @property string|null $supporting_officer_grade Manual entry
 * @property string|null $supporting_officer_email Manual entry
 * @property string $status Application status
 * @property bool $cert_info_is_true
 * @property bool $cert_data_usage_agreed
 * @property bool $cert_email_responsibility_agreed
 * @property \Illuminate\Support\Carbon|null $certification_timestamp
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

    // Status constants as defined in system design document and model
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_ADMIN = 'pending_admin'; // MyMail form refers to this as "pending_admin" after supporting officer
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing'; // When IT Admin is actively creating the account
    public const STATUS_PROVISION_FAILED = 'provision_failed'; // If provisioning attempt fails
    public const STATUS_COMPLETED = 'completed'; // Email/ID successfully provisioned and notified
    // public const STATUS_CANCELLED = 'cancelled'; // Not explicitly in design doc status list but good to have.

    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai',
        self::STATUS_PENDING_ADMIN => 'Menunggu Tindakan Pentadbir IT', // Post-support approval
        self::STATUS_APPROVED => 'Diluluskan (Sedia Proses IT)', // Approved by final authority before provisioning
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PROCESSING => 'Sedang Diproses oleh IT',
        self::STATUS_PROVISION_FAILED => 'Proses Gagal oleh IT',
        self::STATUS_COMPLETED => 'Selesai (Telah Dimaklumkan)',
        // self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'email_applications';

    protected $fillable = [
        'user_id',
        'previous_department_name', 'previous_department_email', //
        'service_start_date', 'service_end_date', //
        'application_reason_notes', // Renamed from purpose, as per design
        'proposed_email', 'group_email', //
        'contact_person_name', // Renamed from group_admin_name, as per design (Nama Admin/EO/CC)
        'contact_person_email', // Renamed from group_admin_email, as per design (E-mel Admin/EO/CC)
        'supporting_officer_id', 'supporting_officer_name', 'supporting_officer_grade', 'supporting_officer_email', //
        'status', 'cert_info_is_true', 'cert_data_usage_agreed', 'cert_email_responsibility_agreed', //
        'certification_timestamp', 'rejection_reason', 'final_assigned_email', 'final_assigned_user_id', //
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

    /**
     * Checks if the application is in draft status.
     * Required by EmailApplicationPolicy.
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Checks if the application can be submitted.
     * Typically means it's in a draft state and owned by the user.
     * @param User|null $user The user attempting to submit. If null, uses authenticated user.
     * @return bool
     */
    public function canBeSubmitted(?User $user = null): bool // Changed User to ?User
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }
        // Check ownership and status
        return $this->isDraft() && (int)$this->user_id === (int)$user->id;
    }

    /**
     * Checks if all certification checkboxes are ticked and timestamp is present.
     * @return bool
     */
    public function areAllCertificationsComplete(): bool
    {
        return $this->cert_info_is_true &&
               $this->cert_data_usage_agreed &&
               $this->cert_email_responsibility_agreed &&
               $this->certification_timestamp !== null;
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

        if ($newStatus === self::STATUS_REJECTED && $reason) {
            $this->rejection_reason = $reason;
        }
        // Add other side effects based on status transitions if needed

        $saved = $this->save();
        if ($saved) {
            Log::info("EmailApplication ID {$this->id} status transitioned from {$oldStatus} to {$newStatus}.", ['acting_user_id' => $actingUserId, 'reason' => $reason]);
            // Dispatch events here if necessary (e.g., for notifications)
            // Example: event(new EmailApplicationStatusChanged($this, $oldStatus, $actingUserId));
        }
        return $saved;
    }
}
