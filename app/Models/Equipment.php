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
 *
 * @property int $id
 * @property string $asset_type Enum from ASSET_TYPES_LABELS keys
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $serial_number Unique serial number
 * @property string|null $tag_id Unique asset tag ID
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status Enum from STATUSES_LABELS keys (operational status)
 * @property string|null $current_location Description of where it is (aligns with System Design 4.3)
 * @property string|null $notes General notes
 * @property string|null $condition_status Enum from CONDITION_STATUSES_LABELS keys (physical condition)
 * @property int|null $department_id Owning/assigned department (FK to departments table) - From uploaded model
 * @property int|null $equipment_category_id (FK to equipment_categories table) - From uploaded model
 * @property int|null $sub_category_id (FK to sub_categories table) - From uploaded model
 * @property string|null $item_code Unique item code - From uploaded model
 * @property string|null $description - From uploaded model
 * @property float|null $purchase_price - From uploaded model
 * @property string|null $acquisition_type Enum from ACQUISITION_TYPES_LABELS keys - From uploaded model
 * @property string|null $classification Enum from CLASSIFICATIONS_LABELS keys - From uploaded model
 * @property string|null $funded_by - From uploaded model
 * @property string|null $supplier_name - From uploaded model
 * @property int|null $location_id (FK to locations table) - From uploaded model
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
 * @property-read \App\Models\Location|null $definedLocation Relationship for location_id
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $assetTypeLabel Accessor: asset_type_label (consistent naming)
 * @property-read string $statusLabel Accessor: status_label
 * @property-read string $conditionStatusLabel Accessor: condition_status_label
 * @property-read string|null $acquisitionTypeLabel Accessor: acquisition_type_label
 * @property-read string|null $classificationLabel Accessor: classification_label
 */
class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    public const ASSET_TYPE_LAPTOP = 'laptop';
    public const ASSET_TYPE_PROJECTOR = 'projector';
    public const ASSET_TYPE_PRINTER = 'printer';
    public const ASSET_TYPE_DESKTOP_PC = 'desktop_pc';
    public const ASSET_TYPE_MONITOR = 'monitor';
    public const ASSET_TYPE_OTHER = 'other_ict'; // Made distinct from CLASSIFICATION_OTHER

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
    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair'; // Matches system design

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
    public const CONDITION_MINOR_DAMAGE = 'minor_damage'; // Matches system design
    public const CONDITION_MAJOR_DAMAGE = 'major_damage'; // Matches system design
    public const CONDITION_UNSERVICEABLE = 'unserviceable'; // Matches system design

    public static array $CONDITION_STATUSES_LABELS = [
        self::CONDITION_NEW => 'Baru',
        self::CONDITION_GOOD => 'Baik',
        self::CONDITION_FAIR => 'Sederhana',
        self::CONDITION_MINOR_DAMAGE => 'Rosak Ringan',
        self::CONDITION_MAJOR_DAMAGE => 'Rosak Teruk',
        self::CONDITION_UNSERVICEABLE => 'Tidak Boleh Digunakan', // Changed from 'Tidak Boleh Guna'
    ];

    // Fields from uploaded model, not in core System Design 4.3 for equipment table
    public const ACQUISITION_TYPE_PURCHASE = 'purchase';
    public const ACQUISITION_TYPE_LEASE = 'lease';
    public const ACQUISITION_TYPE_DONATION = 'donation';
    public const ACQUISITION_TYPE_TRANSFER = 'transfer';
    public const ACQUISITION_TYPE_OTHER = 'other_acquisition'; // Made distinct

     public static array $ACQUISITION_TYPES_LABELS = [
        self::ACQUISITION_TYPE_PURCHASE => 'Belian',
        self::ACQUISITION_TYPE_LEASE => 'Sewaan',
        self::ACQUISITION_TYPE_DONATION => 'Sumbangan',
        self::ACQUISITION_TYPE_TRANSFER => 'Pindahan',
        self::ACQUISITION_TYPE_OTHER => 'Lain-lain (Perolehan)',
    ];

    public const CLASSIFICATION_ASSET = 'asset';
    public const CLASSIFICATION_INVENTORY = 'inventory';
    public const CLASSIFICATION_CONSUMABLE = 'consumable';
    public const CLASSIFICATION_OTHER = 'other_classification'; // Made distinct

     public static array $CLASSIFICATIONS_LABELS = [
        self::CLASSIFICATION_ASSET => 'Aset',
        self::CLASSIFICATION_INVENTORY => 'Inventori',
        self::CLASSIFICATION_CONSUMABLE => 'Barang Luak',
        self::CLASSIFICATION_OTHER => 'Lain-lain (Klasifikasi)',
    ];

    protected $table = 'equipment';

    protected $fillable = [
        // Core fields from System Design 4.3
        'asset_type', 'brand', 'model', 'serial_number', 'tag_id',
        'purchase_date', 'warranty_expiry_date', 'status',
        'current_location',
        'notes', 'condition_status',
        // Additional fields from your provided model
        'department_id', 'equipment_category_id',
        'sub_category_id', 'item_code', 'description', 'purchase_price',
        'acquisition_type', 'classification', 'funded_by', 'supplier_name', 'location_id',
        // created_by, updated_by, deleted_by handled by BlameableObserver
    ];

    protected $casts = [
        'purchase_date' => 'date:Y-m-d',
        'warranty_expiry_date' => 'date:Y-m-d',
        'purchase_price' => 'decimal:2',
        // Add casts for enums if using PHP 8.1 native enums
        // 'asset_type' => AssetTypeEnum::class,
        // 'status' => EquipmentStatusEnum::class,
        // 'condition_status' => EquipmentConditionEnum::class,
    ];

    protected $attributes = [ // Default values for new instances
        'status' => self::STATUS_AVAILABLE,
        'condition_status' => self::CONDITION_GOOD, // Changed from NEW to GOOD as per common initial state
    ];

    protected static function newFactory(): EquipmentFactory
    {
        return EquipmentFactory::new();
    }

    // Relationships
    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id');
    }

    // Relationships for additional fields from your model
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function definedLocation(): BelongsTo // For location_id
    {
        return $this->belongsTo(Location::class, 'location_id');
    }


    // Blameable Relationships
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

    // Accessors for labels
    public function getAssetTypeLabelAttribute(): string
    {
        return self::$ASSET_TYPES_LABELS[$this->asset_type] ?? Str::title(str_replace('_', ' ', (string) $this->asset_type));
    }
    // Alias for Blade consistency if used in some views
    public function getAssetTypeDisplayAttribute(): string
    {
        return $this->getAssetTypeLabelAttribute();
    }


    public function getStatusLabelAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return self::$CONDITION_STATUSES_LABELS[$this->condition_status] ?? Str::title(str_replace('_', ' ', (string) $this->condition_status));
    }

    public function getAcquisitionTypeLabelAttribute(): ?string
    {
        return $this->acquisition_type ? (self::$ACQUISITION_TYPES_LABELS[$this->acquisition_type] ?? Str::title(str_replace('_', ' ', (string) $this->acquisition_type))) : null;
    }

    public function getClassificationLabelAttribute(): ?string
    {
        return $this->classification ? (self::$CLASSIFICATIONS_LABELS[$this->classification] ?? Str::title(str_replace('_', ' ', (string) $this->classification))) : null;
    }


    // Static methods for dropdown options/validation
    public static function getAssetTypesList(): array { return array_keys(self::$ASSET_TYPES_LABELS); }
    public static function getAssetTypeOptions(): array { return self::$ASSET_TYPES_LABELS; }

    public static function getOperationalStatusesList(): array { return array_keys(self::$STATUSES_LABELS); }
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }

    public static function getConditionStatusesList(): array { return array_keys(self::$CONDITION_STATUSES_LABELS); }
    public static function getConditionStatusOptions(): array { return self::$CONDITION_STATUSES_LABELS; }

    public static function getAcquisitionTypesList(): array { return array_keys(self::$ACQUISITION_TYPES_LABELS); }
    public static function getAcquisitionTypeOptions(): array { return self::$ACQUISITION_TYPES_LABELS; }

    public static function getClassificationsList(): array { return array_keys(self::$CLASSIFICATIONS_LABELS); }
    public static function getClassificationOptions(): array { return self::$CLASSIFICATIONS_LABELS; }


    // Scopes
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE)
                     ->whereIn('condition_status', [self::CONDITION_NEW, self::CONDITION_GOOD, self::CONDITION_FAIR]);
    }

    // Business logic methods
    public function updateOperationalStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning("Equipment ID {$this->id}: Invalid operational status update to '{$newStatus}'.", ['acting_user_id' => $actingUserId]);
            return false;
        }
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $actingUserName = $actingUserId ? (User::find($actingUserId)?->name ?? 'System') : 'System';

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Status changed from {$oldStatus} to {$newStatus} by {$actingUserName}: {$reason} on " . now()->toDateTimeString();
        }
        Log::info("Equipment ID {$this->id} operational status updated.", ['old_status' => $oldStatus, 'new_status' => $newStatus, 'user_id' => $actingUserId]);
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
        $actingUserName = $actingUserId ? (User::find($actingUserId)?->name ?? 'System') : 'System';

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Condition changed from {$oldCondition} to {$newCondition} by {$actingUserName}: {$reason} on " . now()->toDateTimeString();
        }
        Log::info("Equipment ID {$this->id} condition status updated.", ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
        return $this->save();
    }
}
