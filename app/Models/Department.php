<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Assuming you want soft deletes for departments

/**
 * Department Model.
 *
 * @property int $id
 * @property string $name
 * @property string $branch_type Enum: 'state', 'headquarters'
 * @property string|null $code
 * @property int|null $head_user_id Foreign key for Head of Department User
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $headOfDepartmentUser Accessor for HOD
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 */
class Department extends Model
{
    use HasFactory, SoftDeletes;

    public const BRANCH_TYPE_STATE = 'state';
    public const BRANCH_TYPE_HQ = 'headquarters';

    public static array $BRANCH_TYPE_LABELS = [
        self::BRANCH_TYPE_STATE => 'Pejabat Negeri',
        self::BRANCH_TYPE_HQ => 'Ibu Pejabat',
    ];

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'branch_type',
        'code',
        'head_user_id', // Added to fillable if you plan to set it via mass assignment
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function positions(): HasMany // If positions are directly tied to departments
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the user who is the head of this department.
     */
    public function headOfDepartmentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

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

    public static function getBranchTypeOptions(): array
    {
        return self::$BRANCH_TYPE_LABELS;
    }
}
