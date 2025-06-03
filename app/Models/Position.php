<?php

namespace App\Models;

use Database\Factories\PositionFactory; // Ensure this factory exists if you use it
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder; // Added for scopeSearch method
use Illuminate\Support\Str; // For Str::title if needed for accessors

/**
 * Position Model (Jawatan).
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1, positions table
 *
 * @property int $id
 * @property string $name (e.g., "Pegawai Teknologi Maklumat", "Pembantu Tadbir")
 * @property string|null $description
 * @property int|null $grade_id (FK to grades.id)
 * @property bool $is_active (default: true)
 * @property int|null $created_by (FK to users.id)
 * @property int|null $updated_by (FK to users.id)
 * @property int|null $deleted_by (FK to users.id)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Grade|null $grade The grade associated with this position.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users Users who hold this position.
 * @property-read int|null $users_count
 * @property-read \App\Models\User|null $creator User who created this record.
 * @property-read \App\Models\User|null $updater User who last updated this record.
 * @property-read \App\Models\User|null $deleter User who soft deleted this record.
 *
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position search(string $term) Scope for searching.
 * @mixin \Eloquent
 */
class Position extends Model
{
  use HasFactory;
  use SoftDeletes; // Ensure you use SoftDeletes if your table has a deleted_at column

  protected $table = 'positions';

  /**
   * The attributes that are mass assignable.
   */
  protected $fillable = [
    'name',
    'description',
    'grade_id',
    'is_active',
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
   * Get options for dropdowns.
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
   */
  public function users(): HasMany
  {
    return $this->hasMany(User::class, 'position_id');
  }

  // Blameable relationships (Ensure these foreign keys exist in your 'positions' table if used)
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
   * Scope a query to only include positions matching a given search term.
   * This allows using ->search($term) in queries.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @param  string|null  $term The search term.
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeSearch(Builder $query, ?string $term): Builder
  {
      if (empty($term)) {
          return $query;
      }

      // Ensure the table name is prefixed if joining or to avoid ambiguity
      return $query->where(function (Builder $subQuery) use ($term) {
          $searchTerm = '%' . $term . '%';
          $subQuery->where($this->getTable().'.name', 'like', $searchTerm)
                   ->orWhere($this->getTable().'.description', 'like', $searchTerm)
                   ->orWhereHas('grade', function (Builder $gradeQuery) use ($searchTerm) {
                       $gradeQuery->where('name', 'like', $searchTerm);
                   });
      });
  }
}
