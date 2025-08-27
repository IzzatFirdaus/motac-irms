<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location Model.
 *
 * Represents a physical location or branch for assets/equipment.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property string|null                     $address
 * @property string|null                     $city
 * @property string|null                     $state
 * @property string|null                     $country
 * @property string|null                     $postal_code
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'locations';

    protected $fillable = [
        'name', 'description',
        'address', 'city', 'state', 'country', 'postal_code',
        'is_active',
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

    protected static function newFactory(): LocationFactory
    {
        return LocationFactory::new();
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'location_id');
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }
}
