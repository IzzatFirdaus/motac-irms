<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EquipmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Equipment Model.
 * (PHPDoc from your version in turn 24)
 * @property int $id
 * @property string $asset_type Enum from ASSET_TYPES
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $serial_number Unique serial number
 * @property string|null $tag_id Unique asset tag ID
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status Enum from STATUSES (operational status)
 * @property string|null $current_location Description of where it is (or location_id if FK)
 * @property string|null $notes General notes
 * @property string|null $condition_status Enum from CONDITION_STATUSES (physical condition)
 * @property int|null $department_id Owning/assigned department (FK to departments table)
 * @property int|null $equipment_category_id (If using categories)
 * @property int|null $sub_category_id
 * @property string|null $item_code
 * @property string|null $description
 * @property float|null $purchase_price
 * @property string|null $acquisition_type
 * @property string|null $classification
 * @property string|null $funded_by
 * @property string|null $supplier_name
 * @property int|null $location_id (FK to locations table, if current_location is not free text)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read \App\Models\SubCategory|null $subCategory
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property-read string $asset_type_label
 * @property-read string $status_label
 * @property-read string $condition_status_label
 */
class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    public const ASSET_TYPE_LAPTOP = 'laptop';
    public const ASSET_TYPE_PROJECTOR = 'projector';
    public const ASSET_TYPE_PRINTER = 'printer';
    public const ASSET_TYPE_DESKTOP_PC = 'desktop_pc';
    public const ASSET_TYPE_MONITOR = 'monitor';
    public const ASSET_TYPE_OTHER = 'other';

    public static array $ASSET_TYPES_LABELS = [
        self::ASSET_TYPE_LAPTOP => 'Komputer Riba',
        self::ASSET_TYPE_PROJECTOR => 'Projektor LCD',
        self::ASSET_TYPE_PRINTER => 'Pencetak',
        self::ASSET_TYPE_DESKTOP_PC => 'Komputer Meja',
        self::ASSET_TYPE_MONITOR => 'Monitor LCD',
        self::ASSET_TYPE_OTHER => 'Lain-lain Peralatan ICT',
    ];

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ON_LOAN = 'on_loan';
    public const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair';

    public static array $STATUSES_LABELS = [
        self::STATUS_AVAILABLE => 'Tersedia',
        self::STATUS_ON_LOAN => 'Sedang Dipinjam',
        self::STATUS_UNDER_MAINTENANCE => 'Dalam Penyelenggaraan',
        self::STATUS_DISPOSED => 'Dilupuskan',
        self::STATUS_LOST => 'Hilang',
        self::STATUS_DAMAGED_NEEDS_REPAIR => 'Rosak (Perlu Pembaikan)',
    ];

    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_MINOR_DAMAGE = 'minor_damage';
    public const CONDITION_MAJOR_DAMAGE = 'major_damage';
    public const CONDITION_UNSERVICEABLE = 'unserviceable';

    public static array $CONDITION_STATUSES_LABELS = [
        self::CONDITION_NEW => 'Baru',
        self::CONDITION_GOOD => 'Baik',
        self::CONDITION_FAIR => 'Sederhana',
        self::CONDITION_MINOR_DAMAGE => 'Rosak Ringan',
        self::CONDITION_MAJOR_DAMAGE => 'Rosak Teruk',
        self::CONDITION_UNSERVICEABLE => 'Tidak Boleh Guna',
    ];

    // From UpdateEquipmentRequest
    public static array $ACQUISITION_TYPES_LABELS = ['purchase' => 'Belian', 'lease' => 'Sewaan', 'donation' => 'Sumbangan', 'transfer' => 'Pindahan', 'other' => 'Lain-lain'];
    public static array $CLASSIFICATIONS_LABELS = ['asset' => 'Aset', 'inventory' => 'Inventori', 'consumable' => 'Barang Luak', 'other' => 'Lain-lain'];


    protected $table = 'equipment';

    protected $fillable = [
        'asset_type', 'brand', 'model', 'serial_number', 'tag_id',
        'purchase_date', 'warranty_expiry_date', 'status',
        'notes', 'condition_status', 'department_id', 'equipment_category_id',
        'sub_category_id', 'item_code', 'description', 'purchase_price',
        'acquisition_type', 'classification', 'funded_by', 'supplier_name',
        'location_id', 'current_location', // If location_id is primary, current_location might be for display/temp
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'purchase_date' => 'date:Y-m-d',
        'warranty_expiry_date' => 'date:Y-m-d',
        'purchase_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_AVAILABLE,
        'condition_status' => self::CONDITION_GOOD,
    ];

    protected static function newFactory(): EquipmentFactory
    {
        return EquipmentFactory::new();
    }

    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id'); // Assuming EquipmentCategory model exists
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id'); // Assuming SubCategory model exists
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id'); // Assuming Location model exists
    }

    public function creatorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updaterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getAssetTypeLabelAttribute(): string
    {
        return self::$ASSET_TYPES_LABELS[$this->asset_type] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $this->asset_type));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $this->status));
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return self::$CONDITION_STATUSES_LABELS[$this->condition_status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $this->condition_status));
    }

    public static function getAssetTypeOptions(): array { return self::$ASSET_TYPES_LABELS; }
    public static function getAssetTypesList(): array { return array_keys(self::$ASSET_TYPES_LABELS); } // For Rule::in

    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getOperationalStatusesList(): array { return array_keys(self::$STATUSES_LABELS); } // For Rule::in

    public static function getConditionStatusOptions(): array { return self::$CONDITION_STATUSES_LABELS; }
    public static function getConditionStatusesList(): array { return array_keys(self::$CONDITION_STATUSES_LABELS); } // For Rule::in

    public static function getAcquisitionTypeOptions(): array { return self::$ACQUISITION_TYPES_LABELS; }
    public static function getAcquisitionTypesList(): array { return array_keys(self::$ACQUISITION_TYPES_LABELS); }

    public static function getClassificationOptions(): array { return self::$CLASSIFICATIONS_LABELS; }
    public static function getClassificationsList(): array { return array_keys(self::$CLASSIFICATIONS_LABELS); }


    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    // Business logic methods (from your uploaded file, slightly adapted)
    public function updateOperationalStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
         if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) { // Use labels array for key check
            Log::warning("Equipment ID {$this->id}: Invalid operational status update to '{$newStatus}'.", ['acting_user_id' => $actingUserId]);
            return false;
        }
        $oldStatus = $this->status;
        $this->status = $newStatus;
        // Log change or add to notes if reason provided
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Status changed from {$oldStatus} to {$newStatus} by user {$actingUserId}. Reason: {$reason}";
        }
        return $this->save();
    }

    public function updatePhysicalConditionStatus(string $newCondition, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newCondition, self::$CONDITION_STATUSES_LABELS)) {
            Log::warning("Equipment ID {$this->id}: Invalid condition status update to '{$newCondition}'.", ['acting_user_id' => $actingUserId]);
            return false;
        }
        $oldCondition = $this->condition_status;
        $this->condition_status = $newCondition;
        if ($reason) {
             $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Condition changed from {$oldCondition} to {$newCondition} by user {$actingUserId}. Reason: {$reason}";
        }
        return $this->save();
    }
}
