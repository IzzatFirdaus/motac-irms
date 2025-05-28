<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Grade Model.
 *
 * @property int $id
 * @property string $name Example: "41", "N19", "JUSA C"
 * @property int|null $level Numeric representation for comparison (e.g., 41, 19, 54)
 * @property int|null $min_approval_grade_id FK to grades table itself (for approval hierarchy)
 * @property bool $is_approver_grade Can this grade generally act as an approver
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read \App\Models\Grade|null $minApprovalGrade
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 */
class Grade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'grades';

    protected $fillable = [
        'name',
        'level',
        'min_approval_grade_id',
        'is_approver_grade',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'level' => 'integer',
        'is_approver_grade' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_approver_grade' => false,
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function minApprovalGrade(): BelongsTo // The grade that is the minimum required to approve actions related to this grade
    {
        return $this->belongsTo(Grade::class, 'min_approval_grade_id');
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
}
