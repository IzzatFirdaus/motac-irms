<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\EquipmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property int $id
 * @property int|null $equipment_category_id
 * @property int|null $sub_category_id
 * @property string|null $item_code Unique internal identifier (from HRMS template)
 * @property string|null $tag_id MOTAC asset tag / No. Aset (from MOTAC Design)
 * @property string|null $serial_number Manufacturer Serial Number
 * @property string $asset_type Specific type of asset (e.g., laptop, projector - from MOTAC Design)
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $description Detailed description of the equipment
 * @property numeric|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status Operational status (e.g., available, on_loan - from MOTAC Design)
 * @property string $condition_status Physical condition (e.g., good, fair - from MOTAC Design)
 * @property int|null $location_id
 * @property string|null $current_location Free-text current location details (from MOTAC Design)
 * @property string|null $notes
 * @property string|null $classification Broad classification (from HRMS template)
 * @property string|null $acquisition_type How the equipment was acquired (from HRMS template)
 * @property string|null $funded_by e.g., Project Name, Grant ID (from HRMS template)
 * @property string|null $supplier_name Supplier name (from HRMS template)
 * @property int|null $department_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanTransactionItem|null $activeLoanTransactionItem
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Location|null $definedLocation
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read string $acquisition_type_label
 * @property-read string $asset_type_label
 * @property-read string $brand_model_serial
 * @property-read string $classification_label
 * @property-read string $condition_color_class
 * @property-read string $condition_status_label
 * @property-read string $name
 * @property-read string $status_color_class
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read \App\Models\SubCategory|null $subCategory
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\EquipmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereAcquisitionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereAssetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereConditionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCurrentLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereEquipmentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereFundedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereItemCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereWarrantyExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withoutTrashed()
 * @mixin \Eloquent
 */
class Equipment extends Model
{
    use Blameable;
    use HasFactory;
    use SoftDeletes;

    // --- CONSTANTS ---
    public const ASSET_TYPE_LAPTOP = 'laptop';

    public const ASSET_TYPE_PROJECTOR = 'projector';

    public const ASSET_TYPE_PRINTER = 'printer';

    public const ASSET_TYPE_DESKTOP_PC = 'desktop_pc';

    public const ASSET_TYPE_MONITOR = 'monitor';

    public const ASSET_TYPE_OTHER_ICT = 'other_ict';

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_ON_LOAN = 'on_loan';

    public const STATUS_UNDER_MAINTENANCE = 'under_maintenance';

    public const STATUS_DISPOSED = 'disposed';

    public const STATUS_LOST = 'lost';

    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair';

    public const STATUS_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';

    public const CONDITION_NEW = 'new';

    public const CONDITION_GOOD = 'good';

    public const CONDITION_FAIR = 'fair';

    public const CONDITION_MINOR_DAMAGE = 'minor_damage';

    public const CONDITION_MAJOR_DAMAGE = 'major_damage';

    public const CONDITION_UNSERVICEABLE = 'unserviceable';

    public const CONDITION_LOST = 'lost';

    public const ACQUISITION_TYPE_PURCHASE = 'purchase';

    public const ACQUISITION_TYPE_LEASE = 'lease';

    public const ACQUISITION_TYPE_DONATION = 'donation';

    public const ACQUISITION_TYPE_TRANSFER = 'transfer';

    public const ACQUISITION_TYPE_OTHER = 'other_acquisition';

    public const CLASSIFICATION_ASSET = 'asset';

    public const CLASSIFICATION_INVENTORY = 'inventory';

    public const CLASSIFICATION_CONSUMABLE = 'consumable';

    public const CLASSIFICATION_OTHER = 'other_classification';

    public static array $ASSET_TYPES_LABELS = [
        self::ASSET_TYPE_LAPTOP => 'Komputer Riba',
        self::ASSET_TYPE_PROJECTOR => 'Projektor',
        self::ASSET_TYPE_PRINTER => 'Pencetak',
        self::ASSET_TYPE_DESKTOP_PC => 'Komputer Meja',
        self::ASSET_TYPE_MONITOR => 'Monitor',
        self::ASSET_TYPE_OTHER_ICT => 'Lain-lain Peralatan ICT',
    ];

    public static array $STATUSES_LABELS = [
        self::STATUS_AVAILABLE => 'Tersedia',
        self::STATUS_ON_LOAN => 'Sedang Dipinjam',
        self::STATUS_UNDER_MAINTENANCE => 'Dalam Penyenggaraan',
        self::STATUS_DISPOSED => 'Telah Dilupus',
        self::STATUS_LOST => 'Hilang (Operasi)',
        self::STATUS_DAMAGED_NEEDS_REPAIR => 'Rosak (Perlu Pembaikan)',
        self::STATUS_RETURNED_PENDING_INSPECTION => 'Dipulangkan (Menunggu Semakan)',
    ];

    public static array $CONDITION_STATUSES_LABELS = [
        self::CONDITION_NEW => 'Baru',
        self::CONDITION_GOOD => 'Baik',
        self::CONDITION_FAIR => 'Sederhana Baik',
        self::CONDITION_MINOR_DAMAGE => 'Rosak Ringan',
        self::CONDITION_MAJOR_DAMAGE => 'Rosak Teruk',
        self::CONDITION_UNSERVICEABLE => 'Tidak Boleh Digunakan / Lupus',
        self::CONDITION_LOST => 'Hilang (Keadaan Fizikal)',
    ];

    public static array $ACQUISITION_TYPES_LABELS = [
        self::ACQUISITION_TYPE_PURCHASE => 'Pembelian',
        self::ACQUISITION_TYPE_LEASE => 'Sewaan',
        self::ACQUISITION_TYPE_DONATION => 'Sumbangan',
        self::ACQUISITION_TYPE_TRANSFER => 'Pindahan',
        self::ACQUISITION_TYPE_OTHER => 'Perolehan Lain',
    ];

    public static array $CLASSIFICATION_LABELS = [
        self::CLASSIFICATION_ASSET => 'Aset',
        self::CLASSIFICATION_INVENTORY => 'Inventori',
        self::CLASSIFICATION_CONSUMABLE => 'Barang Guna Habis',
        self::CLASSIFICATION_OTHER => 'Klasifikasi Lain',
    ];

    protected $table = 'equipment';

    protected $fillable = [
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'tag_id',
        'purchase_date',
        'warranty_expiry_date',
        'status',
        'current_location',
        'notes',
        'condition_status',
        'department_id',
        'equipment_category_id',
        'sub_category_id',
        'location_id',
        'item_code',
        'description',
        'purchase_price',
        'acquisition_type',
        'classification',
        'funded_by',
        'supplier_name',
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

    // --- STATIC GETTERS ---
    public static function getAssetTypeOptions(): array
    {
        return self::$ASSET_TYPES_LABELS;
    }

    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
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

    public static function getAssetTypesList(): array
    {
        return array_keys(self::$ASSET_TYPES_LABELS);
    }

    public static function getOperationalStatusesList(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    public static function getConditionStatusesList(): array
    {
        return array_keys(self::$CONDITION_STATUSES_LABELS);
    }

    public static function getAcquisitionTypesList(): array
    {
        return array_keys(self::$ACQUISITION_TYPES_LABELS);
    }

    public static function getClassificationsList(): array
    {
        return array_keys(self::$CLASSIFICATION_LABELS);
    }

    protected static function newFactory(): EquipmentFactory
    {
        return EquipmentFactory::new();
    }

    // --- RELATIONSHIPS ---
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

    public function definedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id');
    }

    public function activeLoanTransactionItem(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LoanTransactionItem::class, 'equipment_id')
            ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
            ->latestOfMany('created_at');
    }

    // --- ACCESSORS ---
    public function getAssetTypeLabelAttribute(): string
    {
        return __(self::$ASSET_TYPES_LABELS[$this->asset_type] ?? Str::title(str_replace('_', ' ', (string) $this->asset_type)));
    }

    public function getStatusLabelAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return __(self::$CONDITION_STATUSES_LABELS[$this->condition_status] ?? Str::title(str_replace('_', ' ', (string) $this->condition_status)));
    }

    public function getAcquisitionTypeLabelAttribute(): string
    {
        return __(self::$ACQUISITION_TYPES_LABELS[$this->acquisition_type] ?? Str::title(str_replace('_', ' ', (string) $this->acquisition_type)));
    }

    public function getClassificationLabelAttribute(): string
    {
        return __(self::$CLASSIFICATION_LABELS[$this->classification] ?? Str::title(str_replace('_', ' ', (string) $this->classification)));
    }

    /**
     * ADDED: A convenient accessor for a display name.
     */
    public function getNameAttribute(): string
    {
        return trim(($this->brand ?? '').' '.($this->model ?? ''));
    }

    /**
     * ADDED: Accessor for the operational status badge color class.
     */
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'text-bg-success',
            self::STATUS_ON_LOAN => 'text-bg-info',
            self::STATUS_UNDER_MAINTENANCE, self::STATUS_DAMAGED_NEEDS_REPAIR => 'text-bg-warning',
            self::STATUS_RETURNED_PENDING_INSPECTION => 'text-bg-primary',
            self::STATUS_DISPOSED, self::STATUS_LOST => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    /**
     * ADDED: Accessor for the physical condition status badge color class.
     */
    public function getConditionColorClassAttribute(): string
    {
        return match ($this->condition_status) {
            self::CONDITION_NEW, self::CONDITION_GOOD => 'text-bg-success',
            self::CONDITION_FAIR => 'text-bg-primary',
            self::CONDITION_MINOR_DAMAGE => 'text-bg-warning',
            self::CONDITION_MAJOR_DAMAGE, self::CONDITION_UNSERVICEABLE, self::CONDITION_LOST => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    public function getBrandModelSerialAttribute(): string
    {
        $parts = [];
        if (! empty($this->brand)) {
            $parts[] = $this->brand;
        }

        if (! empty($this->model)) {
            $parts[] = $this->model;
        }

        $description = implode(' ', $parts);
        if (! empty($this->serial_number)) {
            return $description.' (S/N: '.$this->serial_number.')';
        }
        if (! empty($this->tag_id)) {
            return $description.' (Tag ID: '.$this->tag_id.')';
        }

        return $description !== '' && $description !== '0' ? $description : __('Peralatan Tidak Bernama');
    }

    // --- BUSINESS LOGIC METHODS ---
    public function updateOperationalStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (! array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning(sprintf("Equipment ID %d: Invalid operational status update to '%s'.", $this->id, $newStatus), ['acting_user_id' => $actingUserId]);

            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $actingUserName = $actingUserId !== null && $actingUserId !== 0 ? (User::find($actingUserId)?->name ?? 'Sistem') : 'Sistem';

        if ($reason !== null && $reason !== '' && $reason !== '0') {
            $this->notes = ($this->notes ? $this->notes."\n" : '').sprintf("Status Operasi ditukar dari '%s' kepada '%s' oleh %s pada ", $oldStatus, $newStatus, $actingUserName).now()->translatedFormat('d/m/Y H:i A').('. Sebab: '.$reason);
        }

        if ($actingUserId !== null && $actingUserId !== 0) {
            $this->updated_by = $actingUserId;
        }

        $saved = $this->save();
        if ($saved) {
            Log::info(sprintf('Equipment ID %d operational status updated.', $this->id), ['old_status' => $oldStatus, 'new_status' => $newStatus, 'user_id' => $actingUserId]);
        } else {
            Log::error(sprintf('Equipment ID %d: Failed to save operational status update.', $this->id), ['old_status' => $oldStatus, 'new_status' => $newStatus, 'user_id' => $actingUserId]);
        }

        return $saved;
    }

    public function updatePhysicalConditionStatus(string $newCondition, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (! array_key_exists($newCondition, self::$CONDITION_STATUSES_LABELS)) {
            Log::warning(sprintf("Equipment ID %d: Invalid condition status update to '%s'.", $this->id, $newCondition), ['acting_user_id' => $actingUserId]);

            return false;
        }

        $oldCondition = $this->condition_status;
        $this->condition_status = $newCondition;
        $actingUserName = $actingUserId !== null && $actingUserId !== 0 ? (User::find($actingUserId)?->name ?? 'Sistem') : 'Sistem';

        if ($reason !== null && $reason !== '' && $reason !== '0') {
            $this->notes = ($this->notes ? $this->notes."\n" : '').sprintf("Status Keadaan Fizikal ditukar dari '%s' kepada '%s' oleh %s pada ", $oldCondition, $newCondition, $actingUserName).now()->translatedFormat('d/m/Y H:i A').('. Sebab: '.$reason);
        }

        if ($actingUserId !== null && $actingUserId !== 0) {
            $this->updated_by = $actingUserId;
        }

        $saved = $this->save();
        if ($saved) {
            Log::info(sprintf('Equipment ID %d condition status updated.', $this->id), ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
        } else {
            Log::error(sprintf('Equipment ID %d: Failed to save physical condition status update.', $this->id), ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
        }

        return $saved;
    }
}
