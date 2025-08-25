<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\BlameableObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Equipment model for ICT inventory.
 *
 * Represents an equipment asset, including all core details and relationships.
 *
 * @property int $id
 * @property int|null $equipment_category_id
 * @property int|null $sub_category_id
 * @property string|null $item_code
 * @property string|null $tag_id
 * @property string|null $serial_number
 * @property string $asset_type
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $description
 * @property float|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status
 * @property string|null $condition_status
 * @property int|null $location_id
 * @property string|null $current_location
 * @property string|null $notes
 * @property string|null $classification
 * @property string|null $acquisition_type
 * @property string|null $funded_by
 * @property string|null $supplier_name
 * @property int|null $department_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
#[ObservedBy(BlameableObserver::class)]
class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    // Fillable properties must match the schema for mass-assignment and seeding
    protected $fillable = [
        'equipment_category_id',
        'sub_category_id',
        'item_code',
        'tag_id',
        'serial_number',
        'asset_type',
        'brand',
        'model',
        'description',
        'purchase_price',
        'purchase_date',
        'warranty_expiry_date',
        'status',
        'condition_status',
        'location_id',
        'current_location',
        'notes',
        'classification',
        'acquisition_type',
        'funded_by',
        'supplier_name',
        'department_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Casts for attributes to proper types
    protected $casts = [
        'equipment_category_id'   => 'integer',
        'sub_category_id'         => 'integer',
        'purchase_price'          => 'float',
        'purchase_date'           => 'date',
        'warranty_expiry_date'    => 'date',
        'location_id'             => 'integer',
        'department_id'           => 'integer',
        'created_by'              => 'integer',
        'updated_by'              => 'integer',
        'deleted_by'              => 'integer',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
        'deleted_at'              => 'datetime',
    ];

    // Asset type constants and labels
    public const ASSET_TYPE_LAPTOP = 'laptop';
    public const ASSET_TYPE_DESKTOP = 'desktop';
    public const ASSET_TYPE_MONITOR = 'monitor';
    public const ASSET_TYPE_PRINTER = 'printer';
    public const ASSET_TYPE_PROJECTOR = 'projector';
    public const ASSET_TYPE_SCANNER = 'scanner';
    public const ASSET_TYPE_CAMERA = 'camera';
    public const ASSET_TYPE_SERVER = 'server';
    public const ASSET_TYPE_NETWORK_DEVICE = 'network_device';
    public const ASSET_TYPE_SOFTWARE = 'software';
    public const ASSET_TYPE_OTHER = 'other';
    public const ASSET_TYPE_OTHER_ICT = 'other_ict';

    public static array $ASSET_TYPES_LABELS = [
        self::ASSET_TYPE_LAPTOP => 'Laptop',
        self::ASSET_TYPE_DESKTOP => 'Desktop',
        self::ASSET_TYPE_MONITOR => 'Monitor',
        self::ASSET_TYPE_PRINTER => 'Pencetak',
        self::ASSET_TYPE_PROJECTOR => 'Projektor',
        self::ASSET_TYPE_SCANNER => 'Pengimbas',
        self::ASSET_TYPE_CAMERA => 'Kamera',
        self::ASSET_TYPE_SERVER => 'Pelayan',
        self::ASSET_TYPE_NETWORK_DEVICE => 'Peranti Rangkaian',
        self::ASSET_TYPE_SOFTWARE => 'Perisian',
        self::ASSET_TYPE_OTHER => 'Lain-lain',
        self::ASSET_TYPE_OTHER_ICT => 'Lain-lain ICT',
    ];

    // Operational status constants
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ON_LOAN = 'on_loan';
    public const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair';

    // Condition status constants
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_MINOR_DAMAGE = 'minor_damage';
    public const CONDITION_MAJOR_DAMAGE = 'major_damage';
    public const CONDITION_UNSERVICEABLE = 'unserviceable';
    public const CONDITION_LOST = 'lost';

    // Optionally, add classification and acquisition_type enumerations if needed

    // --- Relationships ---

    /**
     * Equipment belongs to a category (ICT equipment category).
     */
    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    /**
     * Equipment belongs to a sub-category.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Equipment belongs to a location (branch/office/storage).
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Equipment belongs to a department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Equipment can have many loan transaction items (issue/return records).
     */
    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id', 'id');
    }

    /**
     * Accessor for the current loan item (if any).
     * @return HasMany
     */
    public function currentLoanItem(): HasMany
    {
        return $this->loanTransactionItems();
    }

    // --- Accessors ---

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * Get human-readable asset type label.
     */
    public function getAssetTypeLabelAttribute(): string
    {
        return self::$ASSET_TYPES_LABELS[$this->asset_type] ?? Str::title(str_replace('_', ' ', (string) $this->asset_type));
    }

    /**
     * Get human-readable condition status label.
     */
    public function getConditionStatusLabelAttribute(): string
    {
        return self::getConditionStatusesList()[$this->condition_status] ?? Str::title(str_replace('_', ' ', (string) $this->condition_status));
    }

    // --- Static Option Helpers ---

    /**
     * Get all asset type options as label array.
     * @return array
     */
    public static function getAssetTypeOptions(): array
    {
        return self::$ASSET_TYPES_LABELS;
    }

    /**
     * Get all status options as label array.
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => __('common.statuses.available'),
            self::STATUS_ON_LOAN => __('common.statuses.on_loan'),
            self::STATUS_UNDER_MAINTENANCE => __('common.statuses.under_maintenance'),
            self::STATUS_RETIRED => __('common.statuses.retired'),
            self::STATUS_LOST => __('common.statuses.lost'),
            self::STATUS_DAMAGED => __('common.statuses.damaged'),
            self::STATUS_DISPOSED => __('common.statuses.disposed'),
            self::STATUS_DAMAGED_NEEDS_REPAIR => __('common.statuses.damaged_needs_repair'),
        ];
    }

    /**
     * Get all condition statuses as label array.
     * @return array
     */
    public static function getConditionStatusesList(): array
    {
        return [
            self::CONDITION_NEW => 'Baru',
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_FAIR => 'Sederhana',
            self::CONDITION_MINOR_DAMAGE => 'Rosak Minor',
            self::CONDITION_MAJOR_DAMAGE => 'Rosak Major',
            self::CONDITION_UNSERVICEABLE => 'Tidak Boleh Digunakan',
            self::CONDITION_LOST => 'Hilang',
        ];
    }

    /**
     * Get the default accessories list for an equipment item.
     * @return array
     */
    public static function getDefaultAccessoriesList(): array
    {
        return [
            'Power Adapter',
            'Laptop Bag',
            'Mouse',
            'Keyboard',
            'Webcam',
            'HDMI Cable',
            'VGA Cable',
            'USB Cable',
            'Other Cable',
        ];
    }

    // --- Domain Logic ---

    /**
     * Update the physical condition of equipment and log the action.
     *
     * @param string $newCondition
     * @param int|null $actingUserId
     * @return bool
     */
    public function updatePhysicalCondition(string $newCondition, ?int $actingUserId = null): bool
    {
        $oldCondition = $this->condition_status;
        $this->condition_status = $newCondition;
        $saved = $this->save();

        if ($saved) {
            Log::info(sprintf('Equipment ID %d condition status updated.', $this->id), [
                'old_condition' => $oldCondition,
                'new_condition' => $newCondition,
                'user_id' => $actingUserId,
            ]);
        } else {
            Log::error(sprintf('Equipment ID %d: Failed to save physical condition status update.', $this->id), [
                'old_condition' => $oldCondition,
                'new_condition' => $newCondition,
                'user_id' => $actingUserId,
            ]);
        }
        return $saved;
    }

    /**
     * Returns a summary count of equipment grouped by status.
     * @return array
     */
    public static function getStatusSummary(): array
    {
        return static::query()
            ->select(['status', \DB::raw('count(*) as total')])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Calculates utilization rate (on loan or under maintenance / total) * 100.
     * @return float
     */
    public static function getUtilizationRate(): float
    {
        $totalEquipment = self::count();
        $onLoanEquipment = self::where(function ($query) {
            $query->where('status', self::STATUS_ON_LOAN)
                ->orWhere('status', self::STATUS_UNDER_MAINTENANCE);
        })->count();
        return $totalEquipment > 0 ? ($onLoanEquipment / $totalEquipment) * 100 : 0.0;
    }
}
