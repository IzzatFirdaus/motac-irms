<?php

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Import the Blameable trait

/**
 * Position Model (Jawatan).
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1, positions table
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property int|null $grade_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Position newModelQuery()
 * @method static Builder<static>|Position newQuery()
 * @method static Builder<static>|Position onlyTrashed()
 * @method static Builder<static>|Position query()
 * @method static Builder<static>|Position search(?string $term)
 * @method static Builder<static>|Position whereCreatedAt($value)
 * @method static Builder<static>|Position whereCreatedBy($value)
 * @method static Builder<static>|Position whereDeletedAt($value)
 * @method static Builder<static>|Position whereDeletedBy($value)
 * @method static Builder<static>|Position whereDescription($value)
 * @method static Builder<static>|Position whereGradeId($value)
 * @method static Builder<static>|Position whereId($value)
 * @method static Builder<static>|Position whereIsActive($value)
 * @method static Builder<static>|Position whereName($value)
 * @method static Builder<static>|Position whereUpdatedAt($value)
 * @method static Builder<static>|Position whereUpdatedBy($value)
 * @method static Builder<static>|Position withTrashed()
 * @method static Builder<static>|Position withoutTrashed()
 * @mixin \Eloquent
 */
class Position extends Model
{
    use Blameable;
    use HasFactory;
    use SoftDeletes; // Use the Blameable trait

    protected $table = 'positions'; // Explicitly define table name for clarity

    /**
     * The attributes that are mass assignable.
     * System Design Reference: `fillable` array for mass assignment.
     */
    protected $fillable = [
        'name',
        'description',
        'grade_id',
        'is_active',
        // 'created_by', // Handled by Blameable trait
        // 'updated_by', // Handled by Blameable trait
        // 'deleted_by', // Handled by Blameable trait
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'grade_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default values for attributes.
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get options for dropdowns (e.g., for forms where all active positions are needed).
     */
    public static function getPositionOptions(): array
    {
        return static::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PositionFactory
    {
        return PositionFactory::new();
    }

    // Relationships

    /**
     * Get the grade associated with the position.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    /**
     * Get the users who hold this position.
     * System Design Reference: Crucial check: Ensure Position model has a users() relationship defined.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
    }

    // Blameable relationships (These rely on the blameable fields existing in your 'positions' table)
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

    /**
     * Scope a query to only include positions matching a given search term.
     * This allows using ->search($term) in queries.
     * System Design Reference: Position model has a scopeSearch($query, $term) method.
     *
     * @param  string|null  $term  The search term.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '' || $term === '0') {
            return $query;
        }

        $searchTerm = '%'.$term.'%';

        return $query->where(function (Builder $subQuery) use ($searchTerm): void {
            $subQuery->where($this->getTable().'.name', 'like', $searchTerm)
                ->orWhere($this->getTable().'.description', 'like', $searchTerm);
        })->orWhereHas('grade', function (Builder $gradeQuery) use ($searchTerm): void {
            $gradeQuery->where('name', 'like', $searchTerm);
        });
    }
}
