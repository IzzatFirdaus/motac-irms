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
 * @property int $id
 * @property string $name (e.g., "F41", "N19", "JUSA C")
 * @property int|null $level Numeric level for comparison/sorting (as per System Design & Livewire component)
 * @property int|null $min_approval_grade_id (FK to grades.id)
 * @property bool $is_approver_grade Can users of this grade approve applications? (System Design default: false)
 * @property string|null $description (Optional, kept from your file)
 * @property string|null $service_scheme (Optional, kept from your file, might be legacy)
 * @property int|null $created_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $updated_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $deleted_by (FK to users.id, typically handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Grade|null $minApprovalGrade Relationship for min_approval_grade_id
 * @property-read int|null $users_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereServiceScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @mixin \Eloquent
 */
class Grade extends Model
{
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

    // Relationships

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

    /**
     * Get the positions associated with this grade.
     * (Assuming a Position model exists and has a grade_id)
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'grade_id');
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who soft deleted this record.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
