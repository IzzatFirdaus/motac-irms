<?php
// User.php

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
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model for MOTAC System.
 */
class User extends Authenticatable
{
    use Blameable;
    use HasFactory;
    use Notifiable;
    use HasProfilePhoto;
    use HasApiTokens;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

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

    // Options for titles
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deactivated_at' => 'datetime',
        ];
    }

    // --- RELATIONSHIPS ---

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

    // --- NEW: Helpdesk Relationships ---

    /**
     * Get the helpdesk tickets created by the user.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'user_id');
    }

    /**
     * Get the helpdesk tickets assigned to the user (as an agent).
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'assigned_to_user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    // Blameable relationships
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

    public function hasGradeLevel(int $requiredGradeLevel): bool
    {
        if (! $this->grade) {
            return false;
        }
        return $this->grade->level >= $requiredGradeLevel;
    }

    public function getFullNameAttribute(): string
    {
        return ($this->title ? $this->title . ' ' : '') . $this->name;
    }
}
