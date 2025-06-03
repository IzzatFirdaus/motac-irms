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
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property string $asset_type (Enum from ASSET_TYPE_CONSTANTS)
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $serial_number (Unique)
 * @property string|null $tag_id (Unique MOTAC Tag ID)
 * @property string|null $item_code (Unique item code)
 * @property string|null $description (Detailed description)
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status (Enum from STATUS_CONSTANTS, default: 'available')
 * @property string $condition_status (Enum from CONDITION_STATUS_CONSTANTS, default: 'good')
 * @property string|null $current_location
 * @property string|null $acquisition_type (Enum from ACQUISITION_TYPE_CONSTANTS)
 * @property string|null $classification (Enum from CLASSIFICATION_CONSTANTS)
 * @property string|null $funded_by
 * @property string|null $supplier_name
 * @property string|null $notes
 * @property array|null $specifications (JSON for detailed specs)
 *
 * @property int|null $department_id (FK to departments.id)
 * @property int|null $equipment_category_id (FK to equipment_categories.id)
 * @property int|null $sub_category_id (FK to sub_categories.id)
 * @property int|null $location_id (FK to locations.id for structured location)
 *
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\LoanTransactionItem|null $activeLoanTransactionItem
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Location|null $definedLocation
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read string $asset_type_label
 * @property-read string $condition_status_label
 * @property-read string $status_label
 * @property-read string|null $acquisition_type_label
 * @property-read string|null $classification_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read \App\Models\SubCategory|null $subCategory
 * @property-read \App\Models\User|null $updater
 *
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
 */
class Equipment extends Model
{
  use HasFactory;
  use SoftDeletes;

  // Constants from "Revision 3" (Section 4.3) [cite: 1]
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
    self::ASSET_TYPE_PROJECTOR => 'Projektor', // Corrected from Proyektor
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

  public static array $CLASSIFICATION_LABELS = [ // Corrected name to $CLASSIFICATION_LABELS from $CLASSIFICATIONS_LABELS
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
    'current_location', // As per Design Doc Section 4.3 [cite: 1]
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
    'specifications',
  ];

  protected $casts = [
    'purchase_date' => 'date:Y-m-d',
    'warranty_expiry_date' => 'date:Y-m-d',
    'specifications' => 'array',
    'purchase_price' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  protected $attributes = [
    'status' => self::STATUS_AVAILABLE,
    'condition_status' => self::CONDITION_GOOD,
  ];

  public static function getAssetTypeOptions(): array { return self::$ASSET_TYPES_LABELS; }
  public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
  public static function getConditionStatusOptions(): array { return self::$CONDITION_STATUSES_LABELS; }
  public static function getAcquisitionTypeOptions(): array { return self::$ACQUISITION_TYPES_LABELS; }
  public static function getClassificationOptions(): array { return self::$CLASSIFICATION_LABELS; } // Corrected name

  // For validation purposes (getting keys)
  public static function getAssetTypesList(): array { return array_keys(self::$ASSET_TYPES_LABELS); }
  public static function getOperationalStatusesList(): array { return array_keys(self::$STATUSES_LABELS); }
  public static function getConditionStatusesList(): array { return array_keys(self::$CONDITION_STATUSES_LABELS); }
  public static function getAcquisitionTypesList(): array { return array_keys(self::$ACQUISITION_TYPES_LABELS); }
  public static function getClassificationsList(): array { return array_keys(self::$CLASSIFICATION_LABELS); } // Corrected name


  protected static function newFactory(): EquipmentFactory
  {
    return EquipmentFactory::new();
  }

  // Relationships
  public function department(): BelongsTo { return $this->belongsTo(Department::class, 'department_id'); }
  public function equipmentCategory(): BelongsTo { return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id'); }
  public function subCategory(): BelongsTo { return $this->belongsTo(SubCategory::class, 'sub_category_id'); }
  public function definedLocation(): BelongsTo { return $this->belongsTo(Location::class, 'location_id'); }
  public function loanTransactionItems(): HasMany { return $this->hasMany(LoanTransactionItem::class, 'equipment_id'); }

  public function activeLoanTransactionItem(): \Illuminate\Database\Eloquent\Relations\HasOne // Type hint added
  {
    return $this->hasOne(LoanTransactionItem::class, 'equipment_id')
      ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
      ->latestOfMany('created_at');
  }

  public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
  public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
  public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

  // Accessors for translated labels
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

  // Business logic methods for status updates
  public function updateOperationalStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
  {
      if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
          Log::warning("Equipment ID {$this->id}: Invalid operational status update to '{$newStatus}'.", ['acting_user_id' => $actingUserId]);
          return false;
      }
      $oldStatus = $this->status;
      $this->status = $newStatus;
      $actingUserName = $actingUserId ? (User::find($actingUserId)?->name ?? 'Sistem') : 'Sistem';

      if ($reason) {
          $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Status Operasi ditukar dari '{$oldStatus}' kepada '{$newStatus}' oleh {$actingUserName} pada " . now()->translatedFormat('d/m/Y H:i A') . ". Sebab: {$reason}";
      }
      // BlameableObserver will handle updated_by based on Auth::id().
      // If actingUserId is provided and is different, it means a specific user (not necessarily Auth::id()) took the action.
      // The BlameableObserver should ideally be flexible enough, or this explicit set is a good override.
      if ($actingUserId) {
          $this->updated_by = $actingUserId;
      }

      $saved = $this->save();
      if ($saved) {
          Log::info("Equipment ID {$this->id} operational status updated.", ['old_status' => $oldStatus, 'new_status' => $newStatus, 'user_id' => $actingUserId]);
      } else {
          Log::error("Equipment ID {$this->id}: Failed to save operational status update.", ['old_status' => $oldStatus, 'new_status' => $newStatus, 'user_id' => $actingUserId]);
      }
      return $saved;
  }

  public function updatePhysicalConditionStatus(string $newCondition, ?string $reason = null, ?int $actingUserId = null): bool
  {
      if (!array_key_exists($newCondition, self::$CONDITION_STATUSES_LABELS)) {
          Log::warning("Equipment ID {$this->id}: Invalid condition status update to '{$newCondition}'.", ['acting_user_id' => $actingUserId]);
          return false;
      }
      $oldCondition = $this->condition_status;
      $this->condition_status = $newCondition;
      $actingUserName = $actingUserId ? (User::find($actingUserId)?->name ?? 'Sistem') : 'Sistem';

      if ($reason) {
           $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Status Keadaan Fizikal ditukar dari '{$oldCondition}' kepada '{$newCondition}' oleh {$actingUserName} pada " . now()->translatedFormat('d/m/Y H:i A') . ". Sebab: {$reason}";
      }
      if ($actingUserId) {
          $this->updated_by = $actingUserId;
      }
      $saved = $this->save();
      if ($saved) {
          Log::info("Equipment ID {$this->id} condition status updated.", ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
      } else {
          Log::error("Equipment ID {$this->id}: Failed to save physical condition status update.", ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
      }
      return $saved;
  }
}
