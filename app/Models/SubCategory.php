<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SubCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

// Explicit User, EquipmentCategory, Equipment imports

/**
 * 
 *
 * @property int $id
 * @property int $equipment_category_id
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
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \App\Models\EquipmentCategory $equipmentCategory
 * @property-read \App\Models\User|null $updater
 * @method static Builder<static>|SubCategory active()
 * @method static Builder<static>|SubCategory byCategory(int $categoryId)
 * @method static Builder<static>|SubCategory byName(string $name)
 * @method static \Database\Factories\SubCategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|SubCategory newModelQuery()
 * @method static Builder<static>|SubCategory newQuery()
 * @method static Builder<static>|SubCategory onlyTrashed()
 * @method static Builder<static>|SubCategory query()
 * @method static Builder<static>|SubCategory whereCreatedAt($value)
 * @method static Builder<static>|SubCategory whereCreatedBy($value)
 * @method static Builder<static>|SubCategory whereDeletedAt($value)
 * @method static Builder<static>|SubCategory whereDeletedBy($value)
 * @method static Builder<static>|SubCategory whereDescription($value)
 * @method static Builder<static>|SubCategory whereEquipmentCategoryId($value)
 * @method static Builder<static>|SubCategory whereId($value)
 * @method static Builder<static>|SubCategory whereIsActive($value)
 * @method static Builder<static>|SubCategory whereName($value)
 * @method static Builder<static>|SubCategory whereUpdatedAt($value)
 * @method static Builder<static>|SubCategory whereUpdatedBy($value)
 * @method static Builder<static>|SubCategory withTrashed()
 * @method static Builder<static>|SubCategory withoutTrashed()
 * @mixin \Eloquent
 */
class SubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_categories'; // Corrected: Removed string type

    protected $fillable = [
        'equipment_category_id', 'name', 'description', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        // ... (Standard boot logic for audit stamps from your file) ...
        static::creating(function (self $model): void {
            if (Auth::check()) {
                /** @var User $currentUser */
                $currentUser = Auth::user();
                if (is_null($model->created_by)) {
                    $model->created_by = $currentUser->id;
                }
                if (is_null($model->updated_by)) {
                    $model->updated_by = $currentUser->id;
                }
            }
        });
        static::updating(function (self $model): void {
            if (Auth::check() && ! $model->isDirty('updated_by')) {
                /** @var User $currentUser */
                $currentUser = Auth::user();
                $model->updated_by = $currentUser->id;
            }
        });
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function (self $model): void {
                if (Auth::check() && ! $model->isDirty('deleted_by')) {
                    /** @var User $currentUser */
                    $currentUser = Auth::user();
                    $model->deleted_by = $currentUser->id;
                    $model->saveQuietly();
                }
            });
            static::restoring(function (self $model): void {
                $model->deleted_by = null;
                if (Auth::check() && ! $model->isDirty('updated_by')) {
                    /** @var User $currentUser */
                    $currentUser = Auth::user();
                    $model->updated_by = $currentUser->id;
                }
            });
        }
    }

    protected static function newFactory(): SubCategoryFactory
    {
        return SubCategoryFactory::new();
    }

    /** @return BelongsTo<EquipmentCategory, SubCategory> */
    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    /** @return HasMany<Equipment> */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'sub_category_id');
    }

    /** @return BelongsTo<User, SubCategory> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, SubCategory> */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return BelongsTo<User, SubCategory> */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /** @param Builder<SubCategory> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @param Builder<SubCategory> $query */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('equipment_category_id', $categoryId);
    }

    /** @param Builder<SubCategory> $query */
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }
}
