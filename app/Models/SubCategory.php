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

// Removed: use Illuminate\Support\Facades\Auth;
// Removed: use Illuminate\Support\Str; // Not used

/**
 * SubCategory Model.
 * 
 * Defines sub-categories for ICT equipment, linked to EquipmentCategory.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 * Assumes a global BlameableObserver handles created_by, updated_by, deleted_by.
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

    protected $table = 'sub_categories';

    protected $fillable = [
        'equipment_category_id',
        'name',
        'description',
        'is_active',
        // created_by, updated_by, deleted_by are handled by BlameableObserver
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

    // Blameable fields (created_by, updated_by, deleted_by) assumed to be handled by a global BlameableObserver.
    // Model-specific boot method for blameable fields has been removed to rely on the observer.

    protected static function newFactory(): SubCategoryFactory
    {
        return SubCategoryFactory::new();
    }

    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'sub_category_id');
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('equipment_category_id', $categoryId);
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }
}
