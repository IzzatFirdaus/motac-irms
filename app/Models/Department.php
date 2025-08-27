<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Department Model.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property string|null                     $branch_type
 * @property string|null                     $code
 * @property bool                            $is_active
 * @property int|null                        $head_of_department_id
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
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
 */
class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const BRANCH_TYPE_STATE = 'state';

    public const BRANCH_TYPE_HQ = 'headquarters';

    public static array $BRANCH_TYPE_LABELS = [
        self::BRANCH_TYPE_STATE => 'Pejabat Negeri',
        self::BRANCH_TYPE_HQ    => 'Ibu Pejabat',
    ];

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'branch_type',
        'code',
        'description',
        'is_active',
        'head_of_department_id',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Branch type options for dropdowns.
     */
    public static function getBranchTypeOptions(): array
    {
        return array_map(fn ($label) => __($label), self::$BRANCH_TYPE_LABELS);
    }

    /**
     * Display label for branch type.
     */
    public static function getBranchTypeLabel(string $typeKey): string
    {
        return __(self::$BRANCH_TYPE_LABELS[$typeKey] ?? Str::title(str_replace('_', ' ', $typeKey)));
    }

    /**
     * Users in this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Head of this department.
     */
    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
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

    /**
     * Accessor for branch type label.
     */
    public function getBranchTypeLabelAttribute(): string
    {
        return self::getBranchTypeLabel($this->branch_type);
    }

    /**
     * Helpdesk tickets for this department.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }
}
