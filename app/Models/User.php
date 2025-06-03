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
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.1
 * Migration context: 2013_01_01_000000_create_users_table.php, 2013_11_01_132200_add_motac_columns_to_users_table.php
 *
 * @property int $id
 * @property string|null $title (e.g., "Encik", "Puan", "Dr.")
 * @property string $name
 * @property string|null $identification_number (NRIC)
 * @property string|null $passport_number
 * @property string|null $profile_photo_path
 * @property int|null $position_id (FK to positions.id)
 * @property int|null $grade_id (FK to grades.id)
 * @property int|null $department_id (FK to departments.id)
 * @property string|null $level (Aras/Floor)
 * @property string|null $mobile_number
 * @property string $email (Login email)
 * @property string|null $personal_email (From motac_columns migration)
 * @property string|null $motac_email (Official MOTAC email)
 * @property string|null $user_id_assigned (e.g., network ID)
 * @property string|null $service_status (Enum '1','2','3','4' - CRITICAL: Migration's ENUM definition must match all defined constants)
 * @property string|null $appointment_type (Enum '1','2','3' from migration)
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status (Enum: 'active', 'inactive', default: 'active')
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailApplication> $emailApplications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsMade
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @mixin \Eloquent
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

    // Service Status Constants - Ensure these are the KEYS stored in DB
    // CRITICAL: The migration for 'users' table, 'service_status' ENUM column
    // MUST include all these numeric string values.
    public const SERVICE_STATUS_TETAP = '1';
    public const SERVICE_STATUS_KONTRAK_MYSTEP = '2';
    public const SERVICE_STATUS_PELAJAR_INDUSTRI = '3';
    public const SERVICE_STATUS_OTHER_AGENCY = '4'; // For "E-mel Sandaran (Agensi Lain di MOTAC)"
    // Add other service_type_4, service_type_7 as distinct numeric strings if they become concrete requirements
    // e.g., public const SERVICE_STATUS_TYPE_4_LEGACY = '5'; // Example

    // Appointment Type Constants - Ensure these are the KEYS stored in DB
    public const APPOINTMENT_TYPE_BAHARU = '1';
    public const APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN = '2';
    public const APPOINTMENT_TYPE_LAIN_LAIN = '3';

    public const TITLE_ENCIK = 'Encik';
    public const TITLE_PUAN = 'Puan';
    public const TITLE_CIK = 'Cik';
    public const TITLE_DR = 'Dr.';
    public const TITLE_IR = 'Ir.';

    public static array $TITLE_OPTIONS = [
        self::TITLE_ENCIK => 'Encik',
        self::TITLE_PUAN => 'Puan',
        self::TITLE_CIK => 'Cik',
        self::TITLE_DR => 'Dr.',
        self::TITLE_IR => 'Ir.',
    ];

    public static array $STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
    ];

    public static array $SERVICE_STATUS_LABELS = [
        self::SERVICE_STATUS_TETAP => 'Tetap',
        self::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
        self::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        self::SERVICE_STATUS_OTHER_AGENCY => 'E-mel Sandaran (Agensi Lain di MOTAC)',
        // Add labels for other service statuses if defined
    ];

    public static array $APPOINTMENT_TYPE_LABELS = [
        self::APPOINTMENT_TYPE_BAHARU => 'Baharu',
        self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => 'Kenaikan Pangkat/Pertukaran',
        self::APPOINTMENT_TYPE_LAIN_LAIN => 'Lain-lain',
    ];

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'title', 'identification_number', 'passport_number', 'profile_photo_path',
        'position_id', 'grade_id', 'department_id', 'level',
        'mobile_number', 'personal_email',
        'motac_email', 'user_id_assigned',
        'service_status', 'appointment_type',
        'previous_department_name', 'previous_department_email',
        'status', 'email_verified_at',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $hidden = [
        'password', 'remember_token',
        'two_factor_recovery_codes', 'two_factor_secret',
    ];

    protected $appends = ['profile_photo_url'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
    ];

    public static function getStatusOptions(): array { return self::$STATUS_OPTIONS; }
    public static function getServiceStatusOptions(): array { return self::$SERVICE_STATUS_LABELS; }
    public static function getAppointmentTypeOptions(): array { return self::$APPOINTMENT_TYPE_LABELS; }
    public static function getTitleOptions(): array { return self::$TITLE_OPTIONS; }
    public static function getLevelOptions(): array {
        $levels = [];
        for ($i = 1; $i <= 18; $i++) {
            $levels[(string)$i] = (string)$i;
        }
        return $levels;
    }

    protected static function newFactory(): UserFactory { return UserFactory::new(); }

    // Relationships
    public function department(): BelongsTo { return $this->belongsTo(Department::class, 'department_id'); }
    public function grade(): BelongsTo { return $this->belongsTo(Grade::class, 'grade_id'); }
    public function position(): BelongsTo { return $this->belongsTo(Position::class, 'position_id'); }

    public function emailApplications(): HasMany { return $this->hasMany(EmailApplication::class, 'user_id'); }
    public function loanApplicationsAsApplicant(): HasMany { return $this->hasMany(LoanApplication::class, 'user_id'); }
    public function loanApplicationsAsResponsibleOfficer(): HasMany { return $this->hasMany(LoanApplication::class, 'responsible_officer_id'); }
    public function loanApplicationsAsSupportingOfficer(): HasMany { return $this->hasMany(LoanApplication::class, 'supporting_officer_id'); }
    public function approvalsMade(): HasMany { return $this->hasMany(Approval::class, 'officer_id'); }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Helper methods for roles
    public function isAdmin(): bool { return $this->hasRole('Admin'); }
    public function isBpmStaff(): bool { return $this->hasRole('BPM Staff'); }
    public function isItAdmin(): bool { return $this->hasRole('IT Admin'); }
    public function isApprover(): bool { return $this->hasRole('Approver'); }
    public function isHod(): bool { return $this->hasRole('HOD'); }

    public function routeNotificationForMail($notification = null): array|string
    {
        return $this->motac_email ?: $this->email;
    }
}
