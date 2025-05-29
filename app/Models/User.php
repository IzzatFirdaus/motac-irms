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
use Laravel\Jetstream\HasProfilePhoto; // Trait for profile photo URL
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // For Spatie roles and permissions

/**
 * User Model for MOTAC System.
 * System Design Reference: Section 4.1 Users & Organizational Data
 * @property int $id
 * @property string|null $title (e.g., "Encik", "Puan", "Dr.")
 * @property string $name
 * @property string|null $identification_number (NRIC)
 * @property string|null $passport_number
 * @property string|null $profile_photo_path (Used by HasProfilePhoto trait)
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property int|null $department_id
 * @property string|null $level (Aras/Floor from MyMail form)
 * @property string|null $mobile_number
 * @property string $email (Unique personal email, used for login)
 * @property string|null $motac_email (Official MOTAC email)
 * @property string|null $user_id_assigned (Assigned User ID, e.g., network ID)
 * @property string|null $service_status (Enum: 'tetap', 'lantikan_kontrak_mystep', etc.)
 * @property string|null $appointment_type (Enum: 'baharu', 'kenaikan_pangkat_pertukaran', etc.)
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status (Enum: 'active', 'inactive')
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by (FK to users.id, handled by BlameableObserver)
 * @property int|null $updated_by (FK to users.id, handled by BlameableObserver)
 * @property int|null $deleted_by (FK to users.id, handled by BlameableObserver)
 *
 * @property-read string $profile_photo_url (Accessor from HasProfilePhoto)
 * @property-read string|null $nric (Accessor for identification_number)
 * @property-read Department|null $department
 * @property-read Grade|null $grade
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmailApplication> $emailApplications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Approval> $approvalsMade (Approvals made by this user)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read User|null $creator
 * @property-read User|null $updater
 * @property-read User|null $deleter
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // Constants from System Design 4.1 User Model and MyMail supplementary document
    public const SERVICE_STATUS_TETAP = 'tetap'; // MyMail Value "1"
    public const SERVICE_STATUS_KONTRAK_MYSTEP = 'lantikan_kontrak_mystep'; // MyMail Value "2"
    public const SERVICE_STATUS_PELAJAR_INDUSTRI = 'pelajar_latihan_industri'; // MyMail Value "3"
    public const SERVICE_STATUS_OTHER_AGENCY = 'other_agency_existing_mailbox'; // System Design
    public const SERVICE_STATUS_TYPE_4 = 'service_type_4'; // System Design (Placeholder for "Perkhidmatan Jenis 4")
    public const SERVICE_STATUS_TYPE_7 = 'service_type_7'; // System Design (Placeholder for "Perkhidmatan Jenis 7")


    public const APPOINTMENT_TYPE_BAHARU = 'baharu'; // MyMail Value "1"
    public const APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN = 'kenaikan_pangkat_pertukaran'; // MyMail Value "2"
    public const APPOINTMENT_TYPE_LAIN_LAIN = 'lain_lain'; // MyMail Value "3"

    // Titles for forms, based on MyMail and general Malaysian context
    public const TITLE_ENCIK = 'Encik';
    public const TITLE_PUAN = 'Puan';
    public const TITLE_CIK = 'Cik';
    public const TITLE_DR = 'Dr.';
    public const TITLE_IR = 'Ir.';
    public const TITLE_AR = 'Ar.';
    public const TITLE_SR = 'Sr.';
    public const TITLE_PROF = 'Prof.';
    public const TITLE_PROF_MADYA = 'Prof. Madya';
    public const TITLE_DATUK = 'Datuk';
    public const TITLE_DATO = 'Dato\'';

    public static array $STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
    ];

    public static array $SERVICE_STATUS_LABELS = [
        self::SERVICE_STATUS_TETAP => 'Tetap',
        self::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
        self::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        self::SERVICE_STATUS_OTHER_AGENCY => 'E-mel Sandaran (Staf Agensi Lain di MOTAC)',
        self::SERVICE_STATUS_TYPE_4 => 'Perkhidmatan Jenis 4', // Update with actual label from MyMail if it differs from 'other_agency_existing_mailbox'
        self::SERVICE_STATUS_TYPE_7 => 'Perkhidmatan Jenis 7', // Update with actual label
    ];
    // Added for direct Blade access as User::SERVICE_STATUS_OPTIONS
    public static array $SERVICE_STATUS_OPTIONS;

    public static array $APPOINTMENT_TYPE_LABELS = [
        self::APPOINTMENT_TYPE_BAHARU => 'Baharu',
        self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => 'Kenaikan Pangkat/Pertukaran',
        self::APPOINTMENT_TYPE_LAIN_LAIN => 'Lain-lain',
    ];
    // Added for direct Blade access as User::APPOINTMENT_TYPE_OPTIONS
    public static array $APPOINTMENT_TYPE_OPTIONS;

    public static array $TITLE_OPTIONS = [
        self::TITLE_ENCIK => 'Encik',
        self::TITLE_PUAN => 'Puan',
        self::TITLE_CIK => 'Cik',
        self::TITLE_DR => 'Dr.',
        self::TITLE_IR => 'Ir.',
        self::TITLE_AR => 'Ar.',
        self::TITLE_SR => 'Sr.',
        self::TITLE_PROF => 'Prof.',
        self::TITLE_PROF_MADYA => 'Prof. Madya',
        self::TITLE_DATUK => 'Datuk',
        self::TITLE_DATO => 'Dato\'',
    ];

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'title', 'identification_number', 'passport_number', 'profile_photo_path',
        'position_id', 'grade_id', 'department_id', 'level',
        'mobile_number', 'motac_email', 'user_id_assigned',
        'service_status', 'appointment_type',
        'previous_department_name', 'previous_department_email',
        'status',
        'email_verified_at',
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
        'nric',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function getStatusOptions(): array
    {
        return self::$STATUS_OPTIONS;
    }
    public static function getServiceStatusOptions(): array // This method returns _LABELS, keep for internal use
    {
        return self::$SERVICE_STATUS_LABELS;
    }
    public static function getAppointmentTypeOptions(): array // This method returns _LABELS, keep for internal use
    {
        return self::$APPOINTMENT_TYPE_LABELS;
    }
    public static function getTitleOptions(): array
    {
        return self::$TITLE_OPTIONS;
    }

    public static function getLevelOptions(): array
    {
        $levels = [];
        for ($i = 1; $i <= 18; $i++) {
            $levels[(string)$i] = (string)$i;
        }
        return $levels;
    }

    /**
     * The "booted" method of the model.
     * Used here to initialize static arrays that mirror _LABELS arrays for simpler Blade access.
     *
     * @return void
     */
    protected static function booted()
    {
        static::$SERVICE_STATUS_OPTIONS = self::$SERVICE_STATUS_LABELS;
        static::$APPOINTMENT_TYPE_OPTIONS = self::$APPOINTMENT_TYPE_LABELS;
    }

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
    public function emailApplications(): HasMany
    {
        return $this->hasMany(EmailApplication::class, 'user_id');
    }
    public function loanApplicationsAsApplicant(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'user_id');
    }
    public function loanApplicationsAsResponsibleOfficer(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'responsible_officer_id');
    }
    public function loanApplicationsAsSupportingOfficer(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'supporting_officer_id');
    }
    public function approvalsMade(): HasMany
    {
        return $this->hasMany(Approval::class, 'officer_id'); // 'officer_id' is from Approval model
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

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin'); // Standardized role name
    }
    public function isBpmStaff(): bool
    {
        return $this->hasRole('BPM Staff'); // Standardized role name
    }
    public function isItAdmin(): bool
    {
        return $this->hasRole('IT Admin'); // Standardized role name
    }

    public function routeNotificationForMail($notification = null): array|string
    {
        return $this->motac_email ?: $this->email;
    }

    public function getNricAttribute(): ?string
    {
        return $this->identification_number;
    }
}
