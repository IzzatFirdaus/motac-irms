<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskCategory Model.
 * 
 * Represents categories for helpdesk tickets.
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory active()
 * @method static \Database\Factories\HelpdeskCategoryFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory withoutTrashed()
 * @mixin \Eloquent
 * @mixin IdeHelperHelpdeskCategory
 */
class HelpdeskCategory extends Model
{
    use CreatedUpdatedDeletedBy;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Tickets under this category.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'category_id');
    }

    /**
     * Scope for active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this category is currently active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
