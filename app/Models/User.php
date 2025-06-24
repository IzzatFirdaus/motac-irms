<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable; // EDITED: Import Blameable trait
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str; // Added for Str::title
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
 * @property string $name
 * @property string|null $title e.g., Encik, Puan, Dr.
 * @property string|null $identification_number NRIC
 * @property string|null $passport_number
 * @property int|null $department_id
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property string|null $level For "Aras" or floor level, as string
 * @property string|null $mobile_number
 * @property string|null $personal_email If distinct from login email
 * @property string|null $motac_email
 * @property string|null $user_id_assigned Assigned User ID if different from email
 * @property string|null $service_status Taraf Perkhidmatan. Keys defined in User model.
 * @property string|null $appointment_type Pelantikan. Keys defined in User model.
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $status
 * @property int $is_admin Consider using Spatie roles exclusively.
 * @property int $is_bpm_staff Consider using Spatie roles exclusively.
 * @property string|null $profile_photo_path
 * @property int|null $employee_id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBpmStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMotacEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreviousDepartmentEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreviousDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
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
    use Blameable;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable; // EDITED: Added Blameable trait for audit trails

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_PENDING = 'pending';

    public const SERVICE_STATUS_TETAP = '1';

    public const SERVICE_STATUS_KONTRAK_MYSTEP = '2';

    public const SERVICE_STATUS_PELAJAR_INDUSTRI = '3';

    public const SERVICE_STATUS_OTHER_AGENCY = '4';

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
        self::STATUS_PENDING => 'Menunggu Pengesahan',
    ];

    public static array $SERVICE_STATUS_LABELS = [
        self::SERVICE_STATUS_TETAP => 'Tetap',
        self::SERVICE_STATUS_KONTRAK_MYSTEP => 'Lantikan Kontrak / MyStep',
        self::SERVICE_STATUS_PELAJAR_INDUSTRI => 'Pelajar Latihan Industri (Ibu Pejabat Sahaja)',
        self::SERVICE_STATUS_OTHER_AGENCY => 'E-mel Sandaran (Agensi Lain di MOTAC)',
    ];

    public static array $APPOINTMENT_TYPE_LABELS = [
        self::APPOINTMENT_TYPE_BAHARU => 'Baharu',
        self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => 'Kenaikan Pangkat/Pertukaran',
        self::APPOINTMENT_TYPE_LAIN_LAIN => 'Lain-lain',
    ];

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'identification_number',
        'passport_number',
        'profile_photo_path',
        'position_id',
        'grade_id',
        'department_id',
        'level',
        'mobile_number',
        'personal_email',
        'motac_email',
        'user_id_assigned',
        'service_status',
        'appointment_type',
        'previous_department_name',
        'previous_department_email',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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

    public static function getStatusOptions(): array
    {
        return self::$STATUS_OPTIONS;
    }

    public static function getServiceStatusOptions(): array
    {
        return self::$SERVICE_STATUS_LABELS;
    }

    public static function getAppointmentTypeOptions(): array
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
            $levels[(string) $i] = (string) $i;
        }

        return $levels;
    }

    /**
     * Get the display name for a given service status key.
     */
    public static function getServiceStatusDisplayName(?string $statusKey): string
    {
        return __(self::$SERVICE_STATUS_LABELS[$statusKey] ?? Str::title(str_replace('_', ' ', (string) $statusKey)));
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
        return $this->hasMany(Approval::class, 'officer_id');
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

    // Helper methods for roles
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isBpmStaff(): bool
    {
        return $this->hasRole('BPM Staff');
    }

    public function isItAdmin(): bool
    {
        return $this->hasRole('IT Admin');
    }

    public function isApprover(): bool
    {
        return $this->hasRole('Approver');
    }

    public function isHod(): bool
    {
        return $this->hasRole('HOD');
    }

    public function routeNotificationForMail($notification = null): array|string
    {
        return $this->motac_email ?: $this->email;
    }
}
