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
 *
 *
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property string|null $applicant_title Snapshot: Applicant's title (e.g., Encik, Puan)
 * @property string|null $applicant_name Snapshot: Applicant's full name
 * @property string|null $applicant_identification_number Snapshot: Applicant's NRIC
 * @property string|null $applicant_passport_number Snapshot: Applicant's Passport No
 * @property string|null $applicant_jawatan_gred Snapshot: Applicant's Jawatan & Gred text
 * @property string|null $applicant_bahagian_unit Snapshot: Applicant's Bahagian/Unit text
 * @property string|null $applicant_level_aras Snapshot: Applicant's Aras (Level) text
 * @property string|null $applicant_mobile_number Snapshot: Applicant's mobile number
 * @property string|null $applicant_personal_email Snapshot: Applicant's personal email
 * @property string|null $service_status Key for Taraf Perkhidmatan, from User model options
 * @property string|null $appointment_type Key for Pelantikan, from User model options
 * @property string|null $previous_department_name For Kenaikan Pangkat/Pertukaran
 * @property string|null $previous_department_email For Kenaikan Pangkat/Pertukaran
 * @property \Illuminate\Support\Carbon|null $service_start_date For contract/intern
 * @property \Illuminate\Support\Carbon|null $service_end_date For contract/intern
 * @property string|null $purpose Purpose of application / Notes (Tujuan/Catatan)
 * @property string|null $proposed_email Applicant's proposed email or user ID
 * @property string|null $group_email Requested group email address
 * @property string|null $group_admin_name Name of Admin/EO/CC for group email
 * @property string|null $group_admin_email Email of Admin/EO/CC for group email
 * @property int|null $supporting_officer_id FK to users table if system user
 * @property string|null $supporting_officer_name Manually entered supporting officer name
 * @property string|null $supporting_officer_grade Manually entered supporting officer grade
 * @property string|null $supporting_officer_email Manually entered supporting officer email
 * @property string $status
 * @property bool $cert_info_is_true Semua maklumat adalah BENAR
 * @property bool $cert_data_usage_agreed BERSETUJU maklumat diguna pakai oleh BPM
 * @property bool $cert_email_responsibility_agreed BERSETUJU bertanggungjawab ke atas e-mel
 * @property \Illuminate\Support\Carbon|null $certification_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email
 * @property string|null $final_assigned_user_id
 * @property int|null $processed_by FK to users, IT Admin who processed
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $application_type_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \App\Models\User|null $processor
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\EmailApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantBahagianUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantJawatanGred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantLevelAras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantPassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantPersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereAppointmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertDataUsageAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertEmailResponsibilityAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertInfoIsTrue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertificationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereFinalAssignedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereFinalAssignedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupAdminEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePreviousDepartmentEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePreviousDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProposedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication withoutTrashed()
 * @mixin \Eloquent
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
  ]; //

  public static array $SERVICE_STATUSES_FOR_DISPLAY = [
    User::SERVICE_STATUS_TETAP => 'Tetap',
    User::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
    User::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
    User::SERVICE_STATUS_OTHER_AGENCY => 'Agensi Lain (Peti E-mel Sedia Ada)',
  ]; //

  protected $table = 'email_applications'; //

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
  ]; //

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
  ]; //

  protected $attributes = [
    'status' => self::STATUS_DRAFT,
    'cert_info_is_true' => false,
    'cert_data_usage_agreed' => false,
    'cert_email_responsibility_agreed' => false,
  ]; //

  public static function getStatusOptions(): array
  {
    return self::$STATUS_OPTIONS;
  } //

  public static function getStatuses(): array
  {
    return array_keys(self::$STATUS_OPTIONS);
  } //

  public static function getServiceStatusDisplayName(string $statusKey): string
  {
    if (method_exists(User::class, 'getServiceStatusDisplayName')) {
      return User::getServiceStatusDisplayName($statusKey);
    }

    return __(self::$SERVICE_STATUSES_FOR_DISPLAY[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)));
  } //

  protected static function newFactory(): EmailApplicationFactory
  {
    return EmailApplicationFactory::new();
  } //

  // --- RELATIONSHIPS ---
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  } //

  public function supportingOfficer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'supporting_officer_id');
  } //

  public function processor(): BelongsTo
  {
    return $this->belongsTo(User::class, 'processed_by');
  } //

  public function approvals(): MorphMany
  {
    return $this->morphMany(Approval::class, 'approvable');
  } //

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  } //

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  } //

  public function deleter(): BelongsTo
  {
    return $this->belongsTo(User::class, 'deleted_by');
  } //

  // --- ACCESSORS ---
  public function getStatusLabelAttribute(): string
  {
    return __(self::$STATUS_OPTIONS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
  } //

  /**
   * Get the Bootstrap CSS classes for the application status badge.
   * This uses high-contrast, accessible color classes from Bootstrap 5.3.
   *
   * @return string
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
   * Get the user-friendly label for the application type.
   * This accessor fixes the `MissingAttributeException` error.
   */
  public function getApplicationTypeLabelAttribute(): string
  {
    // If 'proposed_email' has a value, it's an Email Application.
    if (! empty($this->proposed_email)) {
      return __('Permohonan Emel');
    }

    // Otherwise, it's a User ID Application.
    return __('Permohonan ID Pengguna');
  }

  // --- HELPER METHODS ---
  public function isDraft(): bool
  {
    return $this->status === self::STATUS_DRAFT;
  } //

  public function isRejected(): bool
  {
    return $this->status === self::STATUS_REJECTED;
  } //

  public function isCompletedOrProvisionFailed(): bool
  {
    return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_PROVISION_FAILED]);
  } //

  public function areAllCertificationsComplete(): bool
  {
    return $this->cert_info_is_true &&
      $this->cert_data_usage_agreed &&
      $this->cert_email_responsibility_agreed;
  } //
}
