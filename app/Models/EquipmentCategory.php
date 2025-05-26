<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EquipmentCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Facades\Auth; // Only needed if inline blameable in boot() is used

/**
 * Equipment Category Model.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategories
 * @property-read int|null $sub_categories_count
 *
 * @method static Builder<static>|EquipmentCategory active()
 * @method static EquipmentCategoryFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class EquipmentCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'equipment_categories';

    protected $fillable = [
        'name', 'description', 'is_active',
        // 'created_by', 'updated_by', // Typically handled by BlameableObserver
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /*
    // Commented out assuming global BlameableObserver handles created_by, updated_by, deleted_by.
    // If not, or for specific override, this can be enabled.
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model): void {
            if (Auth::check()) {
                $currentUser = Auth::user();
                if (is_null($model->created_by) && property_exists($model, 'created_by')) {
                    $model->created_by = $currentUser->id;
                }
                if (is_null($model->updated_by) && property_exists($model, 'updated_by')) {
                    $model->updated_by = $currentUser->id;
                }
            }
        });
        static::updating(function (self $model): void {
            if (Auth::check() && property_exists($model, 'updated_by') && !$model->isDirty('updated_by')) {
                $currentUser = Auth::user();
                $model->updated_by = $currentUser->id;
            }
        });
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function (self $model): void {
                if (Auth::check() && property_exists($model, 'deleted_by') && !$model->isDirty('deleted_by')) {
                    $currentUser = Auth::user();
                    $model->deleted_by = $currentUser->id;
                    $model->saveQuietly();
                }
            });
            static::restoring(function (self $model): void {
                if (property_exists($model, 'deleted_by')) {
                    $model->deleted_by = null;
                }
                if (Auth::check() && property_exists($model, 'updated_by') && !$model->isDirty('updated_by')) {
                    $currentUser = Auth::user();
                    $model->updated_by = $currentUser->id;
                }
            });
        }
    }
    */

    protected static function newFactory(): EquipmentCategoryFactory
    {
        return EquipmentCategoryFactory::new();
    }

    /** @return HasMany<Equipment> */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'equipment_category_id');
    }

    /** @return HasMany<SubCategory> */
    public function subCategories(): HasMany
    {
        // Assuming SubCategory model exists and has 'equipment_category_id'
        return $this->hasMany(SubCategory::class, 'equipment_category_id');
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

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /** @param Builder<EquipmentCategory> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
