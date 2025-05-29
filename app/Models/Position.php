<?php

namespace App\Models;

use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Position Model (Jawatan).
 *
 * @property int $id
 * @property string $name (e.g., "Pegawai Teknologi Maklumat", "Pembantu Tadbir")
 * @property string|null $code (Optional position code)
 * @property string|null $description
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
class Position extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'positions';

    /**
     * The attributes that are mass assignable.
     * 'created_by', 'updated_by', 'deleted_by' are often handled by observers (e.g., BlameableObserver)
     * and might not need to be in fillable if set automatically.
     */
    protected $fillable = [
      'name',
      'code',
      'description',
      // 'created_by', // Uncomment if you need to set these manually at times
      // 'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      'created_at' => 'datetime',
      'updated_at' => 'datetime',
      'deleted_at' => 'datetime',
    ];

    /**
     * Get options for dropdowns, typically value => label.
     * Example: Position::getOptions()->pluck('name', 'id');
     */
    public static function getPositionOptions(): array
    {
        // You might want to order these, e.g., by name
        return static::query()->pluck('name', 'id')->all();
    }

    protected static function newFactory(): PositionFactory
    {

        return PositionFactory::new();
    }

    // Relationships

    /**
     * Get the users who hold this position.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
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
    // For example, if positions are categorized or have specific permissions:
    // public function category(): BelongsTo
    // {
    //    return $this->belongsTo(PositionCategory::class, 'position_category_id');
    // }
}
