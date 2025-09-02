<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
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
 * @property int                             $id
 * @property string                          $name
 * @property string                          $email
 * @property string|null                     $title
 * @property string|null                     $identification_number
 * @property string|null                     $passport_number
 * @property int|null                        $department_id
 * @property int|null                        $position_id
 * @property int|null                        $grade_id
 * @property string|null                     $phone_number
 * @property string                          $status
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string                          $password
 * @property string|null                     $remember_token
 * @property string|null                     $two_factor_secret
 * @property string|null                     $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $deactivated_at
 * @property string|null                     $mobile_number
 * @property string|null                     $motac_email
 * @property string|null                     $jawatan_gred
 * @property string|null                     $bahagian_unit
 * @property string|null                     $previous_department_name
 * @property string|null                     $previous_department_email
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $profile_photo_url
 * @property-read string $full_name
 * @property string|null                                                                                               $preferred_locale
 * @property string|null                                                                                               $motac_email
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 *
 * @method static \Illuminate\Database\Eloquent\Builder whereHasRole(string $role)
 *
 * @property string|null $level                   For "Aras" or floor level, as string
 * @property string|null $personal_email          If distinct from login email
 * @property string|null $user_id_assigned        Assigned User ID if different from email
 * @property string|null $service_status          Taraf Perkhidmatan. Keys defined in User model.
 * @property string|null $appointment_type        Pelantikan. Keys defined in User model.
 * @property int         $is_admin                Consider using Spatie roles exclusively.
 * @property int         $is_bpm_staff            Consider using Spatie roles exclusively.
 * @property string|null $profile_photo_path
 * @property int|null    $employee_id
 * @property string|null $two_factor_confirmed_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsAsApprover
 * @property-read int|null $approvals_as_approver_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsAssigned
 * @property-read int|null $approvals_assigned_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $assignedTickets
 * @property-read int|null $assigned_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $createdLoans
 * @property-read int|null $created_loans_count
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $responsibleForLoans
 * @property-read int|null $responsible_for_loans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketAttachment> $ticketAttachments
 * @property-read int|null $ticket_attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketComment> $ticketComments
 * @property-read int|null $ticket_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $updater
 *
 * Instance helpers from Spatie\Permission HasRoles/HasPermissions
 *
 * @method        bool                                               hasRole(string|array $roles, string|null $guard = null)
 * @method        bool                                               hasAnyRole(string|array $roles, string|null $guard = null)
 * @method        bool                                               hasAllRoles(string|array $roles, string|null $guard = null)
 * @method        bool                                               hasPermissionTo(string|\Spatie\Permission\Contracts\Permission $permission, string|null $guard = null)
 * @method        \Illuminate\Support\Collection                     getRoleNames()
 * @method static \Database\Factories\UserFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 *
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
    use TwoFactorAuthenticatable;

    // --- TITLE CONSTANTS ---
    public const TITLE_ENCIK = 'encik';

    public const TITLE_PUAN = 'puan';

    public const TITLE_CIK = 'cik';

    public const TITLE_DR = 'dr';

    public const TITLE_PROF = 'prof';

    public const TITLE_TUAN = 'tuan';

    public const TITLE_PUANHAJJAH = 'puanhajah';

    public const TITLE_DATUK = 'datuk';

    public const TITLE_DATIN = 'datin';

    public const TITLE_NONE = '';

    public static array $TITLE_OPTIONS = [
        self::TITLE_ENCIK      => 'Encik',
        self::TITLE_PUAN       => 'Puan',
        self::TITLE_CIK        => 'Cik',
        self::TITLE_DR         => 'Dr.',
        self::TITLE_PROF       => 'Prof.',
        self::TITLE_TUAN       => 'Tuan',
        self::TITLE_PUANHAJJAH => 'Puan Hajjah',
        self::TITLE_DATUK      => 'Datuk',
        self::TITLE_DATIN      => 'Datin',
        self::TITLE_NONE       => '',
    ];

    // --- STATUS CONSTANTS ---
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_PENDING = 'pending';

    // --- SERVICE STATUS CONSTANTS ---
    public const SERVICE_STATUS_TETAP = 'tetap';

    public const SERVICE_STATUS_KONTRAK_MYSTEP = 'kontrak_mystep';

    public const SERVICE_STATUS_PELAJAR_INDUSTRI = 'pelajar_industri';

    public const SERVICE_STATUS_OTHER_AGENCY = 'other_agency';

    // --- APPOINTMENT TYPE CONSTANTS ---
    public const APPOINTMENT_TYPE_BAHARU = 'baharu';

    public const APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN = 'kenaikan_pangkat_pertukaran';

    public const APPOINTMENT_TYPE_LAIN_LAIN = 'lain_lain';

    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'identification_number',
        'passport_number',
        'department_id',
        'position_id',
        'grade_id',
        'phone_number',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $appends = [
        'profile_photo_url',
        'full_name',
    ];

    /**
     * Casts for model properties.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'deactivated_at'    => 'datetime',
        ];
    }

    // --- RELATIONSHIPS ---

    /**
     * Department to which the user belongs.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Position (jawatan) of the user.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Grade (gred) of the user.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Loans created by the user (created_by).
     */
    public function createdLoans(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'created_by');
    }

    /**
     * Loans where the user is responsible officer.
     */
    public function responsibleForLoans(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'responsible_officer_id');
    }

    /**
     * Helpdesk tickets submitted by this user (as applicant).
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Helpdesk tickets assigned to this user (as agent/staff).
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Ticket comments made by this user.
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    /**
     * Ticket attachments uploaded by this user.
     */
    public function ticketAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'user_id');
    }

    /**
     * Approvals assigned to the user (as officer, FK = officer_id).
     */
    public function approvalsAssigned(): HasMany
    {
        return $this->hasMany(Approval::class, 'officer_id');
    }

    /**
     * Approvals where this user is an approver (report/activity).
     * This is used for user activity report with withCount.
     * Uses officer_id FK, as per your approvals table.
     */
    public function approvalsAsApprover(): HasMany
    {
        // NOTE: This must match the FK in your approvals table (officer_id).
        return $this->hasMany(Approval::class, 'officer_id');
    }

    /**
     * Creator user (for blameable/audit).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater user (for blameable/audit).
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Deleter user (for blameable/audit).
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // --- LOAN APPLICATIONS RELATIONSHIPS ---

    /**
     * Get all loan applications where the user is the applicant.
     * Used for user-specific loan application listings.
     */
    public function loanApplicationsAsApplicant(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'user_id');
    }

    // --- ROLE CONVENIENCE HELPERS ---

    /**
     * Returns true if the user has the Admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Returns true if the user has the BPM Staff role.
     */
    public function isBpmStaff(): bool
    {
        return $this->hasRole('BPM Staff');
    }

    /**
     * Returns true if the user has the IT Admin role.
     */
    public function isItAdmin(): bool
    {
        return $this->hasRole('IT Admin');
    }

    /**
     * Returns true if the user has the Approver role.
     */
    public function isApprover(): bool
    {
        return $this->hasRole('Approver');
    }

    /**
     * Returns true if the user has the HOD (Head of Department) role.
     */
    public function isHod(): bool
    {
        return $this->hasRole('HOD');
    }

    /**
     * Check if the user has at least the required grade level.
     */
    public function hasGradeLevel(int $requiredGradeLevel): bool
    {
        if (! $this->grade) {
            return false;
        }

        return $this->grade->level >= $requiredGradeLevel;
    }

    // --- ACCESSORS ---

    /**
     * Accessor for full name, including title if set.
     * Example: "Encik Ahmad" or just "Ahmad".
     */
    public function getFullNameAttribute(): string
    {
        return ($this->title ? (self::$TITLE_OPTIONS[$this->title] ?? $this->title).' ' : '').$this->name;
    }

    /**
     * Accessor for user's profile photo URL (provided by Jetstream).
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_url ?? '';
    }

    /**
     * Get a Bootstrap badge class for a given role name.
     * Used to visually distinguish user roles in the UI.
     */
    public static function getRoleBadgeClass(?string $role): string
    {
        if (! $role) {
            return 'bg-secondary';
        }

        return match (strtolower($role)) {
            'admin'     => 'bg-primary',
            'bpm staff' => 'bg-info',
            'it admin'  => 'bg-dark',
            'approver'  => 'bg-success',
            'hod'       => 'bg-warning',
            'user'      => 'bg-secondary',
            default     => 'bg-secondary',
        };
    }

    /**
     * Get available status options for user filtering and forms.
     * Returns an associative array of status keys and their label.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE    => __('Aktif'),
            self::STATUS_INACTIVE  => __('Tidak Aktif'),
            self::STATUS_SUSPENDED => __('Digantung'),
            self::STATUS_PENDING   => __('Menunggu'),
        ];
    }

    /**
     * Get available service status options for user forms and filters.
     * Returns an associative array of service status keys and their label.
     */
    public static function getServiceStatusOptions(): array
    {
        return [
            self::SERVICE_STATUS_TETAP            => __('Tetap'),
            self::SERVICE_STATUS_KONTRAK_MYSTEP   => __('Kontrak MyStep'),
            self::SERVICE_STATUS_PELAJAR_INDUSTRI => __('Pelajar Industri'),
            self::SERVICE_STATUS_OTHER_AGENCY     => __('Agensi Luar'),
        ];
    }

    /**
     * Get available appointment type options for user forms and filters.
     * Returns an associative array of appointment type keys and their label.
     */
    public static function getAppointmentTypeOptions(): array
    {
        return [
            self::APPOINTMENT_TYPE_BAHARU                      => __('Baharu'),
            self::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN => __('Kenaikan Pangkat/Pertukaran'),
            self::APPOINTMENT_TYPE_LAIN_LAIN                   => __('Lain-lain'),
        ];
    }

    /**
     * Get available level options for user forms and filters.
     * Returns an associative array of level keys and their label.
     */
    public static function getLevelOptions(): array
    {
        return [
            '1' => 'Aras 1',
            '2' => 'Aras 2',
            '3' => 'Aras 3',
            '4' => 'Aras 4',
            '5' => 'Aras 5',
        ];
    }

    /**
     * Get available title options for user forms and filters.
     * Returns an associative array of title keys and their label.
     */
    public static function getTitleOptions(): array
    {
        return self::$TITLE_OPTIONS;
    }
}
