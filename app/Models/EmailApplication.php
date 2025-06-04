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
use Carbon\Carbon; // Ensure Carbon is imported

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
  public const STATUS_CANCELLED = 'cancelled';

  public static array $STATUS_OPTIONS = [
    self::STATUS_DRAFT => 'Draf',
    self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai',
    self::STATUS_PENDING_ADMIN => 'Menunggu Tindakan Pentadbir IT',
    self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Penyediaan Akaun)',
    self::STATUS_REJECTED => 'Ditolak',
    self::STATUS_PROCESSING => 'Sedang Diproses oleh Pentadbir IT',
    self::STATUS_PROVISION_FAILED => 'Proses Penyediaan Gagal',
    self::STATUS_COMPLETED => 'Selesai (Telah Dimaklumkan)',
    self::STATUS_CANCELLED => 'Dibatalkan',
  ];

  public static array $SERVICE_STATUSES_FOR_DISPLAY = [ // Moved from User model for context, or ensure User model's version is used consistently
    User::SERVICE_STATUS_TETAP => 'Tetap',
    User::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
    User::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
    User::SERVICE_STATUS_OTHER_AGENCY => 'Agensi Lain (Peti E-mel Sedia Ada)',
  ];

  protected $table = 'email_applications';

  protected $fillable = [
    'user_id',
    'applicant_title',
    'applicant_name',
    'applicant_identification_number',
    'applicant_passport_number',
    'applicant_jawatan_gred',
    'applicant_bahagian_unit',
    'applicant_level_aras',
    'applicant_mobile_number',
    'applicant_personal_email',
    'service_status',
    'appointment_type',
    'previous_department_name',
    'previous_department_email',
    'service_start_date',
    'service_end_date',
    'purpose',
    'application_reason_notes',
    'proposed_email',
    'group_email',
    'group_admin_name',
    'group_admin_email',
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
    return self::$STATUS_OPTIONS;
  }
  public static function getStatuses(): array
  {
    return array_keys(self::$STATUS_OPTIONS);
  }

  public static function getServiceStatusDisplayName(string $statusKey): string
  {
    if (method_exists(User::class, 'getServiceStatusDisplayName')) {
      return User::getServiceStatusDisplayName($statusKey);
    }
    return __(self::$SERVICE_STATUSES_FOR_DISPLAY[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)));
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

  // Accessors
  public function getStatusLabelAttribute(): string
  {
    return __(self::$STATUS_OPTIONS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
  }

  /**
   * Get the Bootstrap color class for the application status badge.
   *
   * @return string
   */
  public function getStatusColorAttribute(): string
  {
    return match ($this->status) {
      self::STATUS_DRAFT => 'secondary',
      self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_ADMIN => 'warning',
      self::STATUS_APPROVED => 'primary',
      self::STATUS_PROCESSING => 'info',
      self::STATUS_COMPLETED => 'success',
      self::STATUS_REJECTED, self::STATUS_PROVISION_FAILED, self::STATUS_CANCELLED => 'danger',
      default => 'dark', // Fallback color
    };
  }

  // Helper Methods
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
