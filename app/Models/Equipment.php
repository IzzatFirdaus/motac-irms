<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Equipment Model.
 *
 * @property int $id
 * @property string $asset_type Key for ASSET_TYPES_LABELS
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $tag_id MOTAC Tag ID
 * @property string|null $serial_number
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status Operational status key (e.g., STATUS_AVAILABLE)
 * @property string $condition_status Physical condition key (e.g., CONDITION_STATUS_GOOD)
 * @property string|null $current_location
 * @property string|null $notes
 * @property array|null $specifications JSON column for detailed specs
 * @property int|null $department_id (FK to departments.id as per design doc for equipment)
 * @property int|null $equipment_category_id (FK to equipment_categories.id)
 * @property int|null $sub_category_id (FK to sub_categories.id)
 * @property int|null $location_id (FK to locations.id)
 * @property string|null $item_code (Unique item code)
 * @property string|null $description (Detailed description)
 * @property float|null $purchase_price
 * @property string|null $acquisition_type (Enum: 'purchase', 'lease', etc.)
 * @property string|null $classification (Enum: 'asset', 'inventory', etc.)
 * @property string|null $funded_by
 * @property string|null $supplier_name
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read string $assetTypeLabel Accessor
 * @property-read string $statusLabel Accessor
 * @property-read string $conditionStatusLabel Accessor
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read \App\Models\SubCategory|null $subCategory
 * @property-read \App\Models\Location|null $physicalLocation
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 */
class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Asset Types (Referenced by LoanApplicationItem, approval-dashboard, issued-loans etc.)
    // Based on system design doc section 4.3 for equipment.asset_type enum
    public const ASSET_TYPE_LAPTOP = 'laptop';
    public const ASSET_TYPE_PROJECTOR = 'projector';
    public const ASSET_TYPE_PRINTER = 'printer';
    public const ASSET_TYPE_DESKTOP_PC = 'desktop_pc';
    public const ASSET_TYPE_MONITOR = 'monitor';
    public const ASSET_TYPE_OTHER_ICT = 'other_ict';
    // Add any other specific types if they exist as distinct enum values in your DB
    // For example, from my previous generic template:
    public const ASSET_TYPE_SCANNER = 'scanner';
    public const ASSET_TYPE_TABLET = 'tablet';
    public const ASSET_TYPE_WEBCAM = 'webcam';
    public const ASSET_TYPE_UPS = 'ups';
    public const ASSET_TYPE_NETWORK_SWITCH = 'network_switch';
    public const ASSET_TYPE_EXTERNAL_HARD_DRIVE = 'external_hard_drive';

    // Operational Statuses (Referenced by Helpers.php and Blade views)
    // Based on system design doc section 4.3 for equipment.status enum
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ON_LOAN = 'on_loan';
    public const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair';

    // Condition Statuses (Referenced by LoanTransactionItem and system design doc section 4.3 for equipment.condition_status enum)
    public const CONDITION_STATUS_NEW = 'new';
    public const CONDITION_STATUS_GOOD = 'good';
    public const CONDITION_STATUS_FAIR = 'fair';
    public const CONDITION_STATUS_MINOR_DAMAGE = 'minor_damage'; // From design doc
    public const CONDITION_STATUS_MAJOR_DAMAGE = 'major_damage'; // From design doc
    public const CONDITION_STATUS_UNSERVICEABLE = 'unserviceable';

    // Acquisition Types (from system design doc section 4.3 for equipment.acquisition_type enum)
    public const ACQUISITION_TYPE_PURCHASE = 'purchase';
    public const ACQUISITION_TYPE_LEASE = 'lease';
    public const ACQUISITION_TYPE_DONATION = 'donation';
    public const ACQUISITION_TYPE_TRANSFER = 'transfer';
    public const ACQUISITION_TYPE_OTHER = 'other_acquisition';

    // Classification Types (from system design doc section 4.3 for equipment.classification enum)
    public const CLASSIFICATION_ASSET = 'asset';
    public const CLASSIFICATION_INVENTORY = 'inventory';
    public const CLASSIFICATION_CONSUMABLE = 'consumable';
    public const CLASSIFICATION_OTHER = 'other_classification';


    public static array $ASSET_TYPES_LABELS = [
        self::ASSET_TYPE_LAPTOP => 'Komputer Riba',
        self::ASSET_TYPE_PROJECTOR => 'Proyektor',
        self::ASSET_TYPE_PRINTER => 'Pencetak',
        self::ASSET_TYPE_DESKTOP_PC => 'Komputer Meja',
        self::ASSET_TYPE_MONITOR => 'Monitor',
        self::ASSET_TYPE_SCANNER => 'Pengimbas',
        self::ASSET_TYPE_TABLET => 'Tablet',
        self::ASSET_TYPE_WEBCAM => 'Webcam',
        self::ASSET_TYPE_UPS => 'UPS',
        self::ASSET_TYPE_NETWORK_SWITCH => 'Network Switch',
        self::ASSET_TYPE_EXTERNAL_HARD_DRIVE => 'External Hard Drive',
        self::ASSET_TYPE_OTHER_ICT => 'Lain-lain Peralatan ICT',
    ];

    public static array $OPERATIONAL_STATUSES_LABELS = [
        self::STATUS_AVAILABLE => 'Tersedia',
        self::STATUS_ON_LOAN => 'Sedang Dipinjam',
        self::STATUS_UNDER_MAINTENANCE => 'Dalam Penyenggaraan',
        self::STATUS_DISPOSED => 'Telah Dilupus',
        self::STATUS_LOST => 'Hilang',
        self::STATUS_DAMAGED_NEEDS_REPAIR => 'Rosak (Perlu Pembaikan)',
    ];
    // Note: design doc also has 'lost' for condition_status, which overlaps with operational status.
    // Using distinct condition statuses here. If 'lost' is a condition, add:
    // public const CONDITION_STATUS_LOST = 'lost';


    public static array $CONDITION_STATUSES_LABELS = [
        self::CONDITION_STATUS_NEW => 'Baru',
        self::CONDITION_STATUS_GOOD => 'Baik',
        self::CONDITION_STATUS_FAIR => 'Sederhana Baik', // Working, with cosmetic issues or minor wear
        self::CONDITION_STATUS_MINOR_DAMAGE => 'Rosak Ringan',
        self::CONDITION_STATUS_MAJOR_DAMAGE => 'Rosak Teruk',
        self::CONDITION_STATUS_UNSERVICEABLE => 'Tidak Boleh Digunakan / Lupus',
        // self::CONDITION_STATUS_LOST => 'Hilang (Keadaan)', // If 'lost' is a condition
    ];

    public static array $ACQUISITION_TYPES_LABELS = [
        self::ACQUISITION_TYPE_PURCHASE => 'Pembelian',
        self::ACQUISITION_TYPE_LEASE => 'Sewaan',
        self::ACQUISITION_TYPE_DONATION => 'Sumbangan',
        self::ACQUISITION_TYPE_TRANSFER => 'Pindahan',
        self::ACQUISITION_TYPE_OTHER => 'Lain-lain Perolehan',
    ];

    public static array $CLASSIFICATION_LABELS = [
        self::CLASSIFICATION_ASSET => 'Aset',
        self::CLASSIFICATION_INVENTORY => 'Inventori',
        self::CLASSIFICATION_CONSUMABLE => 'Barang Guna Habis',
        self::CLASSIFICATION_OTHER => 'Lain-lain Klasifikasi',
    ];

    protected $table = 'equipment';

    protected $fillable = [
        'asset_type', 'brand', 'model', 'tag_id', 'serial_number',
        'purchase_date', 'warranty_expiry_date',
        'status', 'condition_status', 'current_location', 'notes', 'specifications',
        'department_id', 'equipment_category_id', 'sub_category_id', 'location_id',
        'item_code', 'description', 'purchase_price', 'acquisition_type', 'classification',
        'funded_by', 'supplier_name',
        // created_by, updated_by are handled by BlameableObserver if you have one
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'specifications' => 'array',
        'purchase_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    // Static methods for dropdown options
    public static function getAssetTypeOptions(): array
    {
        return self::$ASSET_TYPES_LABELS;
    }

    public static function getOperationalStatusOptions(): array
    {
        return self::$OPERATIONAL_STATUSES_LABELS;
    }

    public static function getConditionStatusOptions(): array
    {
        return self::$CONDITION_STATUSES_LABELS;
    }

    public static function getAcquisitionTypeOptions(): array
    {
        return self::$ACQUISITION_TYPES_LABELS;
    }

    public static function getClassificationOptions(): array
    {
        return self::$CLASSIFICATION_LABELS;
    }


    // Relationships
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

    public function physicalLocation(): BelongsTo // Changed name to avoid conflict if Location model is used elsewhere
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id');
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

    // Accessors
    public function getAssetTypeLabelAttribute(): string
    {
        return self::$ASSET_TYPES_LABELS[$this->asset_type] ?? Str::title(str_replace('_', ' ', (string) $this->asset_type));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$OPERATIONAL_STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return self::$CONDITION_STATUSES_LABELS[$this->condition_status] ?? Str::title(str_replace('_', ' ', (string) $this->condition_status));
    }

    public function updateStatusFromLoans(): void
    {
        $this->loadMissing('loanTransactionItems.loanTransaction');

        $isIssuedAndNotFullyReturned = $this->loanTransactionItems()
            ->whereHas('loanTransaction', function ($query) {
                $query->where('type', LoanTransaction::TYPE_ISSUE)
                      ->whereIn('status', [LoanTransaction::STATUS_ISSUED, LoanTransaction::STATUS_COMPLETED]);
            })
            ->where(function ($query) {
                // Item is issued but not yet returned in good/damaged/lost condition via a corresponding return transaction
                $query->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
                      ->orWhere(function ($subQuery) {
                          // Consider items that were part of a return transaction but are still pending inspection
                          $subQuery->where('status', LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION)
                                   ->whereHas('loanTransaction', fn ($q) => $q->where('type', LoanTransaction::TYPE_RETURN));
                      });
            })
            ->exists();

        $newStatus = $this->status;

        if (in_array($this->status, [self::STATUS_UNDER_MAINTENANCE, self::STATUS_DISPOSED, self::STATUS_LOST])) {
            // Do not override these critical manual statuses based on loan activity
        } elseif ($isIssuedAndNotFullyReturned) {
            $newStatus = self::STATUS_ON_LOAN;
        } else {
            // Not on loan and not in a fixed non-available state
            if (in_array($this->condition_status, [self::STATUS_DAMAGED_NEEDS_REPAIR, self::CONDITION_STATUS_MAJOR_DAMAGE, self::CONDITION_STATUS_UNSERVICEABLE])) {
                $newStatus = self::STATUS_DAMAGED_NEEDS_REPAIR;
            } else {
                $newStatus = self::STATUS_AVAILABLE;
            }
        }

        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->saveQuietly(); // Use saveQuietly if you don't want to trigger observers again for this update
        }
    }
}
