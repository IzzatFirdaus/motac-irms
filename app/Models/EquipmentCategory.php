<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * EquipmentCategory Model.
 * 
 * Represents a type/category of ICT equipment. Used for organizing equipment and subcategories.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory active()
 * @method static \Database\Factories\EquipmentCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory withoutTrashed()
 * @mixin \Eloquent
 */
class EquipmentCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'equipment_categories';

    protected $fillable = [
        'name', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Equipment belonging to this category.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'equipment_category_id');
    }

    /**
     * Subcategories under this equipment category.
     */
    public function subCategories(): HasMany
    {
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

    /**
     * Scope: Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
