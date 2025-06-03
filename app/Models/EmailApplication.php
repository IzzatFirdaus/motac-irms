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
 * Email Application Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.2
 * Migration context: 2025_04_22_083502_create_email_applications_table.php
 *
 * @property int $id
 * @property int $user_id Applicant's User ID
 *
 * @property string|null $applicant_title Snapshot of applicant's title
 * @property string|null $applicant_name Snapshot of applicant's name
 * @property string|null $applicant_identification_number Snapshot of applicant's NRIC
 * @property string|null $applicant_passport_number Snapshot of applicant's passport
 * @property string|null $applicant_jawatan_gred Snapshot of applicant's position & grade text
 * @property string|null $applicant_bahagian_unit Snapshot of applicant's department/unit text
 * @property string|null $applicant_level_aras Snapshot of applicant's level/floor text
 * @property string|null $applicant_mobile_number Snapshot of applicant's mobile number
 * @property string|null $applicant_personal_email Snapshot of applicant's personal email
 *
 * @property string|null $service_status From User model's service_status options (Numeric string '1', '2', '3', '4')
 * @property string|null $appointment_type From User model's appointment_type options (Numeric string '1', '2', '3')
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property \Illuminate\Support\Carbon|null $service_start_date
 * @property \Illuminate\Support\Carbon|null $service_end_date
 * @property string|null $purpose For "Tujuan/Catatan" (replaces application_reason_notes)
 * @property string|null $proposed_email For "Cadangan E-mel ID"
 * @property string|null $group_email For Group Email address if requested
 * @property string|null $group_admin_name For "Nama Admin/EO/CC" of Group Email (replaces contact_person_name)
 * @property string|null $group_admin_email For "E-mel Admin/EO/CC" of Group Email (replaces contact_person_email)
 * @property int|null $supporting_officer_id User ID from users table
 * @property string|null $supporting_officer_name Manual entry if not from system list
 * @property string|null $supporting_officer_grade Manual entry if not from system list
 * @property string|null $supporting_officer_email Manual entry if not from system list
 * @property string $status Enum values (e.g., 'draft', 'pending_support', 'cancelled')
 * @property bool $cert_info_is_true
 * @property bool $cert_data_usage_agreed
 * @property bool $cert_email_responsibility_agreed
 * @property \Illuminate\Support\Carbon|null $certification_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email
 * @property string|null $final_assigned_user_id
 * @property int|null $processed_by User ID of IT Admin who processed
 * @property \Illuminate\Support\Carbon|null $processed_at Timestamp of processing
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User $user Applicant
 * @property-read \App\Models\User|null $supportingOfficer Selected from system
 * @property-read \App\Models\User|null $processor User who processed the application (IT Admin)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 *
 * @method static EmailApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication query()
 * @mixin \Eloquent
 */
class EmailApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROVISION_FAILED = 'provision_failed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled'; // Added

    public static array $STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai',
        self::STATUS_PENDING_ADMIN => 'Menunggu Tindakan Pentadbir IT',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Penyediaan Akaun)',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PROCESSING => 'Sedang Diproses oleh Pentadbir IT',
        self::STATUS_PROVISION_FAILED => 'Proses Penyediaan Gagal',
        self::STATUS_COMPLETED => 'Selesai (Telah Dimaklumkan)',
        self::STATUS_CANCELLED => 'Dibatalkan', // Added
    ];

    public static array $SERVICE_STATUSES_FOR_DISPLAY = [
        User::SERVICE_STATUS_TETAP => 'Tetap',
        User::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
        User::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        User::SERVICE_STATUS_OTHER_AGENCY => 'Agensi Lain (Peti E-mel Sedia Ada)',
    ];

    protected $table = 'email_applications';

    protected $fillable = [
        'user_id',
        // Applicant Snapshot Fields (as per provided migration)
        'applicant_title',
        'applicant_name',
        'applicant_identification_number',
        'applicant_passport_number',
        'applicant_jawatan_gred',
        'applicant_bahagian_unit',
        'applicant_level_aras',
        'applicant_mobile_number',
        'applicant_personal_email',
        // Core Application Fields
        'service_status',
        'appointment_type',
        'previous_department_name',
        'previous_department_email',
        'service_start_date',
        'service_end_date',
        'purpose', // Replaces application_reason_notes
        'proposed_email',
        'group_email',
        'group_admin_name', // Replaces contact_person_name
        'group_admin_email', // Replaces contact_person_email
        'supporting_officer_id',
        'supporting_officer_name',
        'supporting_officer_grade',
        'supporting_officer_email',
        'status',
        'cert_info_is_true',
        'cert_data_usage_agreed',
        'cert_email_responsibility_agreed',
        'certification_timestamp',
        'submitted_at',
        'rejection_reason',
        'final_assigned_email',
        'final_assigned_user_id',
        'processed_by',
        'processed_at',
        // created_by, updated_by will be handled by BlameableObserver
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

    public static function getStatusOptions(): array { return self::$STATUS_OPTIONS; }

    /**
     * Get all possible status values as a simple array of keys for migration.
     * @return array<string>
     */
    public static function getStatuses(): array
    {
        return array_keys(self::$STATUS_OPTIONS);
    }

    public static function getServiceStatusDisplayName(string $statusKey): string
    {
        $userStatusOptions = method_exists(User::class, 'getServiceStatusOptions') ? User::getServiceStatusOptions() : self::$SERVICE_STATUSES_FOR_DISPLAY;
        return __( $userStatusOptions[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)) );
    }

    protected static function newFactory(): EmailApplicationFactory { return EmailApplicationFactory::new(); }

    // Relationships
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function supportingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'supporting_officer_id'); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUS_OPTIONS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    // Helper Methods
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
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
