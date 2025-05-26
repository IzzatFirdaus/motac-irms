<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto; // For profile_photo_url accessor
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // For Spatie roles and permissions
// If using PHP 8.1+ Enums, you might import them here:
// use App\Enums\UserStatusEnum;
// use App\Enums\ServiceStatusEnum;
// use App\Enums\AppointmentTypeEnum;

/**
 * User Model for MOTAC System.
 *
 * @property int $id
 * @property string|null $title
 * @property string $name
 * @property string|null $identification_number NRIC
 * @property string|null $passport_number
 * @property string|null $profile_photo_path Used by HasProfilePhoto trait
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property int|null $department_id
 * @property string|null $level Aras/Floor
 * @property string|null $mobile_number
 * @property string $email Unique personal email, used for login
 * @property string|null $motac_email Official MOTAC email
 * @property string|null $user_id_assigned Assigned User ID (e.g., for network access)
 * @property string|null $service_status Enum from SERVICE_STATUS_TYPES // Consider using PHP 8.1 Enums: ServiceStatusEnum::class
 * @property string|null $appointment_type Enum from APPOINTMENT_TYPES // Consider using PHP 8.1 Enums: AppointmentTypeEnum::class
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status Enum from STATUS_OPTIONS (active, inactive) // Consider using PHP 8.1 Enums: UserStatusEnum::class
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by Foreign key to users table
 * @property int|null $updated_by Foreign key to users table
 * @property int|null $deleted_by Foreign key to users table
 *
 * @property-read string $profile_photo_url Accessor from HasProfilePhoto
 * @property-read string|null $nric Accessor for identification_number
 * @property-read Department|null $department
 * @property-read Grade|null $grade
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmailApplication> $emailApplications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Approval> $approvalsMade As Approving Officer
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read User|null $creatorInfo
 * @property-read User|null $updaterInfo
 * @property-read User|null $deleterInfo
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    // Status constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public static array $STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
    ];

    // Service Status constants (align with system design 4.1 and MyMail form)
    public const SERVICE_STATUS_TETAP = 'tetap';
    public const SERVICE_STATUS_KONTRAK_MYSTEP = 'lantikan_kontrak_mystep';
    public const SERVICE_STATUS_PELAJAR_INDUSTRI = 'pelajar_latihan_industri';
    public const SERVICE_STATUS_OTHER_AGENCY = 'other_agency_existing_mailbox';
    public const SERVICE_STATUS_TYPE_4 = 'service_type_4'; // As per design doc (placeholder)
    public const SERVICE_STATUS_TYPE_7 = 'service_type_7'; // As per design doc (placeholder)

    public static array $SERVICE_STATUS_LABELS = [
        self::SERVICE_STATUS_TETAP => 'Tetap',
        self::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MySTEP',
        self::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        self::SERVICE_STATUS_OTHER_AGENCY => 'E-mel Sandaran (Agensi Lain)', // Based on interpretation of MyMail form logic for "menggunakan mailbox sedia ada di agensi utama"
        self::SERVICE_STATUS_TYPE_4 => 'Jenis Perkhidmatan 4', // Placeholder label, update as needed
        self::SERVICE_STATUS_TYPE_7 => 'Jenis Perkhidmatan 7', // Placeholder label, update as needed
    ];

    // Appointment Type constants (align with system design 4.1 and MyMail form)
    public const APPOINTMENT_TYPE_BAHARU = 'baharu';
    public const APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN = 'kenaikan_pangkat_pertukaran';
    public const APPOINTMENT_TYPE_LAIN_LAIN = 'lain_lain';

    public static array $APPOINTMENT_TYPE_LABELS = [
        self::APPOINTMENT_TYPE_BAHARU => 'Baharu',
        self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => 'Kenaikan Pangkat/Pertukaran',
        self::APPOINTMENT_TYPE_LAIN_LAIN => 'Lain-lain',
    ];

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', // Core
        'title', 'identification_number', 'passport_number', 'profile_photo_path', // Personal & Profile
        'position_id', 'grade_id', 'department_id', 'level', // Organizational
        'mobile_number', 'personal_email', 'motac_email', 'user_id_assigned', // Contact & System IDs
        'service_status', 'appointment_type', // Employment Status
        'previous_department_name', 'previous_department_email', // For transfers
        'status', // User account status (active/inactive)
        'email_verified_at', // Jetstream/Fortify
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', // Fortify 2FA
        // created_by, updated_by, deleted_by are typically handled by a BlameableObserver or Trait, not mass assignment.
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url', // From HasProfilePhoto trait
        'nric', // Accessor
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Automatically handled by Laravel
        'two_factor_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        // If using PHP 8.1+ Enums, cast them here:
        // 'status' => UserStatusEnum::class,
        // 'service_status' => ServiceStatusEnum::class,
        // 'appointment_type' => AppointmentTypeEnum::class,
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // Applications submitted by this user
    public function emailApplications(): HasMany
    {
        return $this->hasMany(EmailApplication::class, 'user_id');
    }

    // Applications submitted by this user
    public function loanApplicationsAsApplicant(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'user_id');
    }

    // Applications where this user is the responsible officer
    public function loanApplicationsAsResponsibleOfficer(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'responsible_officer_id');
    }

    // Applications where this user is the supporting officer
    public function loanApplicationsAsSupportingOfficer(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'supporting_officer_id');
    }

    // Approvals made by this officer
    public function approvalsMade(): HasMany
    {
        return $this->hasMany(Approval::class, 'officer_id');
    }

    // Blameable relationships (User who created/updated/deleted this record)
    public function creatorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updaterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Role checks (examples, tailor to your Spatie role names from RoleAndPermissionSeeder)
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isBpmStaff(): bool
    {
        return $this->hasRole('BPMStaff'); // Ensure 'BPMStaff' (or similar) role is defined in your RoleAndPermissionSeeder
    }

    public function isItAdmin(): bool
    {
        return $this->hasRole('IT Admin');  // Ensure 'IT Admin' role is defined
    }

    public function isSupportingOfficer(): bool
    {
        return $this->hasRole('SupportingOfficer'); // Ensure 'SupportingOfficer' role is defined
    }

     public function isHod(): bool
    {
        return $this->hasRole('HOD'); // Ensure 'HOD' role is defined
    }

    // Notification routing for mail
    public function routeNotificationForMail($notification = null): array|string
    {
        // Prioritize motac_email if available for official notifications,
        // fallback to primary login email (personal_email which is stored in 'email' field)
        return $this->motac_email ?? $this->email;
    }

    // Accessor for NRIC (identification_number)
    public function getNricAttribute(): ?string
    {
        return $this->identification_number;
    }

    // Static helper methods for dropdowns, using defined labels
    public static function getServiceStatusOptions(): array
    {
        return self::$SERVICE_STATUS_LABELS;
    }

    public static function getAppointmentTypeOptions(): array
    {
        return self::$APPOINTMENT_TYPE_LABELS;
    }

    public static function getStatusOptions(): array
    {
        return self::$STATUS_OPTIONS;
    }
}
