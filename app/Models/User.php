<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
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
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $title
 * @property string|null $identification_number
 * @property string|null $passport_number
 * @property int|null $department_id
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property string|null $phone_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $deactivated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $profile_photo_url
 * @property-read string $full_name
 */
class User extends Authenticatable
{
    use Blameable, HasFactory, Notifiable, HasProfilePhoto, HasApiTokens, TwoFactorAuthenticatable, HasRoles, SoftDeletes;

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
        self::TITLE_ENCIK => 'Encik',
        self::TITLE_PUAN => 'Puan',
        self::TITLE_CIK => 'Cik',
        self::TITLE_DR => 'Dr.',
        self::TITLE_PROF => 'Prof.',
        self::TITLE_TUAN => 'Tuan',
        self::TITLE_PUANHAJJAH => 'Puan Hajjah',
        self::TITLE_DATUK => 'Datuk',
        self::TITLE_DATIN => 'Datin',
        self::TITLE_NONE => '',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_PENDING = 'pending';

    // Service and appointment enums
    public const SERVICE_STATUS_TETAP = 'tetap';
    public const SERVICE_STATUS_KONTRAK_MYSTEP = 'kontrak_mystep';
    public const SERVICE_STATUS_PELAJAR_INDUSTRI = 'pelajar_industri';
    public const SERVICE_STATUS_OTHER_AGENCY = 'other_agency';

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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deactivated_at' => 'datetime',
        ];
    }

    // Relationships

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdLoans(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'created_by');
    }

    public function responsibleForLoans(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'responsible_officer_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'assigned_to_user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
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

    /**
     * Check if the user has at least the required grade level.
     */
    public function hasGradeLevel(int $requiredGradeLevel): bool
    {
        if (!$this->grade) {
            return false;
        }
        return $this->grade->level >= $requiredGradeLevel;
    }

    /**
     * Accessor for full name (with title).
     */
    public function getFullNameAttribute(): string
    {
        return ($this->title ? $this->title . ' ' : '') . $this->name;
    }
}
