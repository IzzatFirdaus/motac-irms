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
 * @property string|null $description
 * @property int|null $grade_level (Numeric level for sorting/comparison, if applicable)
 * @property string|null $service_scheme (e.g., "Skim Perkhidmatan Teknologi Maklumat")
 * @property int|null $created_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $updated_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $deleted_by (FK to users.id, typically handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
class Grade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     * 'created_by', 'updated_by', 'deleted_by' are often handled by observers.
     */
    protected $fillable = [
        'name',
        'description',
        'grade_level',
        'service_scheme',
        // 'created_by',
        // 'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'grade_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get options for dropdowns.
     */
    public static function getGradeOptions(): array
    {
        // Example: you might want to order these by level or name
        // return static::query()->orderBy('grade_level')->pluck('name', 'id')->all();
        return static::query()->pluck('name', 'id')->all();
    }

    // Relationships

    /**
     * Get the users who have this grade.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'grade_id');
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

    // Add other relationships or model logic as needed.
}
