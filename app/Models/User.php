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
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model for MOTAC System.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1
 *
 * @property int $id
 * @property string|null $title
 * @property string $name
 * @property string|null $identification_number (NRIC)
 * @property string|null $passport_number
 * @property string|null $profile_photo_path
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property int|null $department_id
 * @property string|null $level (Aras/Floor from MyMail form)
 * @property string|null $mobile_number
 * @property string $email (Login email, typically personal)
 * @property string|null $motac_email (Official MOTAC email)
 * @property string|null $user_id_assigned (e.g., network ID)
 * @property string|null $service_status (Enum from SERVICE_STATUS_CONSTANTS)
 * @property string|null $appointment_type (Enum from APPOINTMENT_TYPE_CONSTANTS)
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status (Enum: 'active', 'inactive', default: 'active')
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $employee_id
 * @property string|null $full_name
 * @property string|null $nric NRIC / Identification Number
 * @property string|null $personal_email
 * @property int $is_admin
 * @property int $is_bpm_staff
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsMade
 * @property-read int|null $approvals_made_count
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailApplication> $emailApplications
 * @property-read int|null $email_applications_count
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read int|null $loan_applications_as_responsible_officer_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read int|null $loan_applications_as_supporting_officer_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $position
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $updater
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppointmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBpmStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMotacEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserIdAssigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles; // For Spatie roles and permissions
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    // Status Constants from "Revision 3" (Section 4.1)
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // Service Status Constants from "Revision 3" (Section 4.1) & MyMail Supplementary Doc
    public const SERVICE_STATUS_TETAP = 'tetap';                                 // MyMail Value "1"
    public const SERVICE_STATUS_KONTRAK_MYSTEP = 'lantikan_kontrak_mystep';     // MyMail Value "2"
    public const SERVICE_STATUS_PELAJAR_INDUSTRI = 'pelajar_latihan_industri';  // MyMail Value "3"
    public const SERVICE_STATUS_OTHER_AGENCY = 'other_agency_existing_mailbox'; // For backup email config
    public const SERVICE_STATUS_TYPE_4 = 'service_type_4';                      // Placeholder for MyMail service_type_id "4"
    public const SERVICE_STATUS_TYPE_7 = 'service_type_7';                      // Placeholder for MyMail service_type_id "7"

    // Appointment Type Constants from "Revision 3" (Section 4.1) & MyMail Supplementary Doc
    public const APPOINTMENT_TYPE_BAHARU = 'baharu';                                 // MyMail Value "1"
    public const APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN = 'kenaikan_pangkat_pertukaran'; // MyMail Value "2"
    public const APPOINTMENT_TYPE_LAIN_LAIN = 'lain_lain';                           // MyMail Value "3"

    // Title Constants from "Revision 3" (Section 4.1)
    public const TITLE_ENCIK = 'Encik';
    public const TITLE_PUAN = 'Puan';
    public const TITLE_CIK = 'Cik';
    public const TITLE_DR = 'Dr.';
    public const TITLE_IR = 'Ir.'; // Added for completeness
    // Add other titles (Prof., Datuk, etc.) if defined in your dropdowns or system needs

    public static array $STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
    ];

    // Labels for dropdowns, matching MyMail and "Revision 3" (Section 4.1)
    public static array $SERVICE_STATUS_LABELS = [
        self::SERVICE_STATUS_TETAP => 'Tetap',
        self::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
        self::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        self::SERVICE_STATUS_OTHER_AGENCY => 'E-mel Sandaran (Staf Agensi Lain di MOTAC)', // Custom label matching design context
        self::SERVICE_STATUS_TYPE_4 => 'Perkhidmatan Jenis 4 (MyMail Specific - To be confirmed)', // From MyMail HTML if '4' exists
        self::SERVICE_STATUS_TYPE_7 => 'Perkhidmatan Jenis 7 (MyMail Specific - To be confirmed)', // From MyMail HTML if '7' exists
    ];

    public static array $APPOINTMENT_TYPE_LABELS = [
        self::APPOINTMENT_TYPE_BAHARU => 'Baharu',
        self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => 'Kenaikan Pangkat/Pertukaran',
        self::APPOINTMENT_TYPE_LAIN_LAIN => 'Lain-lain',
    ];

    public static array $TITLE_OPTIONS = [
        self::TITLE_ENCIK => 'Encik',
        self::TITLE_PUAN => 'Puan',
        self::TITLE_CIK => 'Cik',
        self::TITLE_DR => 'Dr.',
        self::TITLE_IR => 'Ir.',
    ];


    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', // Core
        'title', 'identification_number', 'passport_number', 'profile_photo_path', // Personal Details
        'position_id', 'grade_id', 'department_id', 'level', // Organizational
        'mobile_number', 'motac_email', 'user_id_assigned', // Contact & System IDs
        'service_status', 'appointment_type', // Employment Status
        'previous_department_name', 'previous_department_email', // For transfers
        'status', 'email_verified_at', // Account Status
        // created_by, updated_by are handled by BlameableObserver
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url', // From HasProfilePhoto trait
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Ensures password is automatically hashed
        'two_factor_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE, // Default status for new users
    ];

    public static function getStatusOptions(): array { return self::$STATUS_OPTIONS; }
    public static function getServiceStatusOptions(): array { return self::$SERVICE_STATUS_LABELS; }
    public static function getAppointmentTypeOptions(): array { return self::$APPOINTMENT_TYPE_LABELS; }
    public static function getTitleOptions(): array { return self::$TITLE_OPTIONS; }
    // Get Level (Aras) options as per MyMail form (1-18)
    public static function getLevelOptions(): array {
        return array_combine(range(1, 18), range(1, 18));
    }


    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    // Relationships (as per Revision 3)
    public function department(): BelongsTo { return $this->belongsTo(Department::class, 'department_id'); }
    public function grade(): BelongsTo { return $this->belongsTo(Grade::class, 'grade_id'); }
    public function position(): BelongsTo { return $this->belongsTo(Position::class, 'position_id'); }

    public function emailApplications(): HasMany { return $this->hasMany(EmailApplication::class, 'user_id'); }
    public function loanApplicationsAsApplicant(): HasMany { return $this->hasMany(LoanApplication::class, 'user_id'); }
    public function loanApplicationsAsResponsibleOfficer(): HasMany { return $this->hasMany(LoanApplication::class, 'responsible_officer_id'); }
    public function loanApplicationsAsSupportingOfficer(): HasMany { return $this->hasMany(LoanApplication::class, 'supporting_officer_id'); }
    public function approvalsMade(): HasMany { return $this->hasMany(Approval::class, 'officer_id'); }

    // Blameable relationships
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }


    // Helper methods for roles
    public function isAdmin(): bool { return $this->hasRole('Admin'); } //
    public function isBpmStaff(): bool { return $this->hasRole('BPM Staff'); } //
    public function isItAdmin(): bool { return $this->hasRole('IT Admin'); } //

    /**
     * Specifies the user's email address for notifications.
     * Prefers official MOTAC email if available, otherwise uses login email.
     */
    public function routeNotificationForMail($notification = null): array|string
    {
        return $this->motac_email ?: $this->email;
    }
}
