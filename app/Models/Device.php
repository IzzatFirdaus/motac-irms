<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DeviceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Facades\Auth; // Only needed if inline blameable in boot() is used

/**
 * @property int $id
 * @property string $name
 * @property string|null $serial_number
 * @property string|null $model
 * @property string|null $manufacturer
 * @property string|null $ip_address
 * @property string|null $type Type of device (e.g., fingerprint_scanner, card_reader)
 * @property string|null $location Description or FK to locations table. System design unclear, model uses string.
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fingerprint> $fingerprints
 * @property-read int|null $fingerprints_count
 * @property-read \App\Models\Location|null $definedLocation If 'location' was location_id FK
 *
 * @method static Builder<static>|Device active()
 * @method static Builder<static>|Device byName(string $name)
 * @method static Builder<static>|Device bySerialNumber(string $serialNumber)
 * @method static DeviceFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Device extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'devices';

    protected $fillable = [
        'name', 'serial_number', 'model', 'manufacturer',
        'ip_address', 'type', 'location', // 'location' is a string here based on your model
        'is_active',
        // 'created_by', 'updated_by', // Handled by BlameableObserver
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

    protected static function newFactory(): DeviceFactory
    {
        return DeviceFactory::new();
    }

    /** @return HasMany<Fingerprint> */
    public function fingerprints(): HasMany
    {
        // Assuming Fingerprint model exists and has 'device_id'
        return $this->hasMany(Fingerprint::class, 'device_id');
    }

    // If 'location' was meant to be a foreign key to the 'locations' table:
    // public function definedLocation(): BelongsTo
    // {
    //     return $this->belongsTo(Location::class, 'location_id'); // Assuming FK is location_id
    // }

    // Blameable relationships
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }


    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /** @param Builder<Device> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @param Builder<Device> $query */
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', '%' . $name . '%');
    }

    /** @param Builder<Device> $query */
    public function scopeBySerialNumber(Builder $query, string $serialNumber): Builder
    {
        return $query->where('serial_number', $serialNumber);
    }
}
