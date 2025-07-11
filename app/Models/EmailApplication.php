<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EmailApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Represents an application for an Email account or User ID.
 */
class EmailApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    // --- STATUS CONSTANTS ---
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROVISION_FAILED = 'provision_failed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'email_applications';

    protected $fillable = [
        'user_id', 'applicant_title', 'applicant_name', 'applicant_identification_number',
        'applicant_passport_number', 'applicant_jawatan_gred', 'applicant_bahagian_unit',
        'applicant_level_aras', 'applicant_mobile_number', 'applicant_personal_email',
        'service_status', 'appointment_type', 'previous_department_name', 'previous_department_email',
        'service_start_date', 'service_end_date', 'purpose', 'application_reason_notes',
        'proposed_email', 'group_email', 'group_admin_name', 'group_admin_email',
        'supporting_officer_id', 'supporting_officer_name', 'supporting_officer_grade', 'supporting_officer_email',
        'status', 'cert_info_is_true', 'cert_data_usage_agreed', 'cert_email_responsibility_agreed',
        'certification_timestamp', 'submitted_at', 'rejection_reason', 'final_assigned_email',
        'final_assigned_user_id', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'service_start_date' => 'date:Y-m-d',
        'service_end_date' => 'date:Y-m-d',
        'cert_info_is_true' => 'boolean',
        'cert_data_usage_agreed' => 'boolean',
        'cert_email_responsibility_agreed' => 'boolean',
        'certification_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'cert_info_is_true' => false,
        'cert_data_usage_agreed' => false,
        'cert_email_responsibility_agreed' => false,
    ];

    public static function getStatusOptions(): array
    {
        $statuses = [
            self::STATUS_DRAFT, self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_ADMIN,
            self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_PROCESSING,
            self::STATUS_PROVISION_FAILED, self::STATUS_COMPLETED, self::STATUS_CANCELLED,
        ];

        return collect($statuses)->mapWithKeys(function ($status) {
            return [$status => __('email_applications.statuses.' . $status)];
        })->all();
    }

    protected static function newFactory(): EmailApplicationFactory
    {
        return EmailApplicationFactory::new();
    }

    // --- RELATIONSHIPS ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supportingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supporting_officer_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    // --- ACCESSORS ---

    /**
     * Get the translated status label with a fallback.
     */
    public function getStatusLabelAttribute(): string
    {
        $key = 'email_applications.statuses.' . $this->status;
        $translation = __($key);

        // If the translation returns the key itself, it means it's not found.
        // Provide a human-readable fallback.
        if ($translation === $key) {
            return Str::title(str_replace('_', ' ', (string) $this->status));
        }

        return $translation;
    }

    /**
     * Get the Bootstrap CSS classes for the application status badge.
     */
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'badge bg-secondary-subtle text-secondary-emphasis',
            self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_ADMIN => 'badge bg-warning-subtle text-warning-emphasis',
            self::STATUS_PROCESSING => 'badge bg-primary-subtle text-primary-emphasis',
            self::STATUS_APPROVED => 'badge bg-info-subtle text-info-emphasis',
            self::STATUS_COMPLETED => 'badge bg-success-subtle text-success-emphasis',
            self::STATUS_REJECTED, self::STATUS_PROVISION_FAILED, self::STATUS_CANCELLED => 'badge bg-danger-subtle text-danger-emphasis',
            default => 'badge bg-dark-subtle text-dark-emphasis',
        };
    }

    /**
     * Get the user-friendly label for the application type with a fallback.
     */
    public function getApplicationTypeLabelAttribute(): string
    {
        $typeKey = !empty($this->proposed_email) ? 'email' : 'user_id';
        $translationKey = 'email_applications.types.' . $typeKey;
        $translation = __($translationKey);

        // If the translation returns the key itself, it means it's not found.
        // Provide a human-readable fallback.
        if ($translation === $translationKey) {
            return Str::title(str_replace('_', ' ', $typeKey) . ' Application');
        }

        return $translation;
    }

    // --- HELPER METHODS ---
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompletedOrProvisionFailed(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_PROVISION_FAILED]);
    }

    public function areAllCertificationsComplete(): bool
    {
        return $this->cert_info_is_true &&
            $this->cert_data_usage_agreed &&
            $this->cert_email_responsibility_agreed;
    }
}
