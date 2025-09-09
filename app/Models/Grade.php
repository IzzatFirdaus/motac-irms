<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Grade Model (Gred Perkhidmatan).
 *
 * @property int $id
 * @property string $name e.g., "41", "N19", "JUSA C"
 * @property int|null $level Numeric level for comparison/sorting
 * @property int|null $position_id
 * @property int|null $min_approval_grade_id
 * @property bool $is_approver_grade Can users of this grade approve applications?
 * @property string|null $description Optional description for the grade
 * @property string|null $service_scheme Optional service scheme, e.g., Perkhidmatan Tadbir dan Diplomatik
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read Grade|null $minApprovalGrade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereIsApproverGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereMinApprovalGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereServiceScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @mixin \Eloquent
 */
class Grade extends Model
{
    use Blameable;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     * Aligned with System Design and Livewire component usage.
     * 'created_by', 'updated_by', 'deleted_by' are often handled by observers.
     */
    protected $fillable = [
        'name',
        'level', // As per System Design & Livewire Component
        'position_id', // Link to Position model based on seeders
        'min_approval_grade_id',
        'is_approver_grade',
        'description', // Kept from your existing model, if still used
        'service_scheme', // Kept from your existing model, if still used
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer', // Changed from 'grade_level' to 'level'
        'position_id' => 'integer', // Link to Position model
        'min_approval_grade_id' => 'integer',
        'is_approver_grade' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get options for dropdowns.
     */
    public static function getGradeOptions(): array
    {
        return static::query()->orderBy('level')->orderBy('name')->pluck('name', 'id')->all();
    }

    /**
     * Get grades for a specific position (for dynamic dropdown filtering).
     */
    public static function getGradeOptionsForPosition(int $positionId): array
    {
        return static::query()
            ->where('position_id', $positionId)
            ->orderBy('level')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    // Relationships

    /**
     * Get the position this grade belongs to.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Defines the relationship to the minimum grade required for approval.
     * This is a self-referencing belongsTo relationship.
     */
    public function minApprovalGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'min_approval_grade_id');
    }

    /**
     * Get the users who have this grade.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'grade_id');
    }
}
