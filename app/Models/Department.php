<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; // For Str::title

/**
 * Department Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1 (Database Schema for departments implies head_of_department_id)
 * Migration context: 2013_11_01_131800_create_departments_table.php uses head_of_department_id
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $branch_type Corresponds to MOTAC Negeri/Bahagian distinction
 * @property string|null $code Optional department code
 * @property bool $is_active
 * @property int|null $head_of_department_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $branch_type_label
 * @property-read \App\Models\User|null $headOfDepartment
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereBranchType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereHeadOfDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withoutTrashed()
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'description',
        'is_active',
        'head_of_department_id', // Corrected to match migration and design
        // created_by, updated_by are handled by BlameableObserver
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the options for branch types, typically for dropdowns.
     */
    public static function getBranchTypeOptions(): array
    {
        return array_map(fn ($label) => __($label), self::$BRANCH_TYPE_LABELS);
    }

    /**
     * Get the display label for a given branch type key.
     */
    public static function getBranchTypeLabel(string $typeKey): string
    {
        return __(self::$BRANCH_TYPE_LABELS[$typeKey] ?? Str::title(str_replace('_', ' ', $typeKey)));
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the user who is the head of this department.
     */
    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id'); // Corrected foreign key
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

    // Accessor for branch type label
    public function getBranchTypeLabelAttribute(): string
    {
        return self::getBranchTypeLabel($this->branch_type);
    }
}
