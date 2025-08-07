<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Grade Model (Gred Perkhidmatan).
 *
 * Represents job grades in the system. Used for user profiles, positions, and approval levels.
 *
 * @property int $id
 * @property string $name
 * @property int|null $level
 * @property int|null $position_id
 * @property int|null $min_approval_grade_id
 * @property bool $is_approver_grade
 * @property string|null $description
 * @property string|null $service_scheme
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 */
class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grades';

    protected $fillable = [
        'name',
        'level',
        'min_approval_grade_id',
        'is_approver_grade',
        'description',
        'service_scheme',
    ];

    protected $casts = [
        'level' => 'integer',
        'min_approval_grade_id' => 'integer',
        'is_approver_grade' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Grade options for dropdowns.
     */
    public static function getGradeOptions(): array
    {
        return static::query()->orderBy('level')->orderBy('name')->pluck('name', 'id')->all();
    }

    public function minApprovalGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'min_approval_grade_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'grade_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'grade_id');
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
}
