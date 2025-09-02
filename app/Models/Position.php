<?php

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position Model (Jawatan).
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $grade_id
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\User|null $updater
 *
 * @method static \Database\Factories\PositionFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position search(?string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Position extends Model
{
    use Blameable;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'positions';

    protected $fillable = [
        'name',
        'description',
        'grade_id',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'grade_id'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Options for position dropdowns (active only).
     */
    public static function getPositionOptions(): array
    {
        return static::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }

    protected static function newFactory(): PositionFactory
    {
        return PositionFactory::new();
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
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
     * Scope for searching positions.
     */
    public function scopeSearch($query, ?string $term)
    {
        if ($term === null || $term === '' || $term === '0') {
            return $query;
        }

        $searchTerm = '%'.$term.'%';

        return $query->where(function ($subQuery) use ($searchTerm): void {
            $subQuery->where($this->getTable().'.name', 'like', $searchTerm)
                ->orWhere($this->getTable().'.description', 'like', $searchTerm);
        })->orWhereHas('grade', function ($gradeQuery) use ($searchTerm): void {
            $gradeQuery->where('name', 'like', $searchTerm);
        });
    }
}
