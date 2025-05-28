<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position Model.
 *
 * @property int $id
 * @property string $name
 * @property int|null $grade_id FK to grades table
 * @property string|null $description (Added for completeness)
 * @property bool $is_active (Added for completeness)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
class Position extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'positions';

    protected $fillable = [
        'name', 'grade_id', 'description', 'is_active',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
       'is_active' => true,
    ];

    // BlameableObserver handles created_by, updated_by, deleted_by. Boot method removed.

    protected static function newFactory(): PositionFactory
    {
        return PositionFactory::new();
    }

    // Relationships
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function users(): HasMany // Users holding this position
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

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Removed old Employee and Timeline relationships
}
