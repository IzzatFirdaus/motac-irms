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

#[ObservedBy(BlameableObserver::class)]
class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tag_id',
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'warranty_end_date',
        'status',
        'location_id',
        'department_id',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // --- ASSET TYPE CONSTANTS ---
    public const ASSET_TYPE_LAPTOP = 'laptop';
    public const ASSET_TYPE_DESKTOP = 'desktop';
    public const ASSET_TYPE_DESKTOP_PC = 'desktop_pc';
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

    // Define the ASSET_TYPES_LABELS for display purposes
    public static array $ASSET_TYPES_LABELS = [
        self::ASSET_TYPE_LAPTOP => 'Laptop',
        self::ASSET_TYPE_DESKTOP => 'Desktop',
        self::ASSET_TYPE_DESKTOP_PC => 'Desktop PC',
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

    // --- STATUS CONSTANTS ---
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ON_LOAN = 'on_loan';
    public const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_DAMAGED_NEEDS_REPAIR = 'damaged_needs_repair';

    // --- CONDITION STATUS CONSTANTS ---
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_MINOR_DAMAGE = 'minor_damage';
    public const CONDITION_MAJOR_DAMAGE = 'major_damage';
    public const CONDITION_UNSERVICEABLE = 'unserviceable';
    public const CONDITION_LOST = 'lost';

    // --- ACQUISITION TYPE CONSTANTS ---
    public const ACQUISITION_TYPE_PURCHASE = 'purchase';
    public const ACQUISITION_TYPE_LEASE = 'lease';
    public const ACQUISITION_TYPE_DONATION = 'donation';
    public const ACQUISITION_TYPE_TRANSFER = 'transfer';
    public const ACQUISITION_TYPE_OTHER = 'other';

    // --- CLASSIFICATION CONSTANTS ---
    public const CLASSIFICATION_ASSET = 'asset';
    public const CLASSIFICATION_INVENTORY = 'inventory';
    public const CLASSIFICATION_CONSUMABLE = 'consumable';
    public const CLASSIFICATION_OTHER = 'other';

    // Relationships
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function currentLoanItem(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'equipment_id', 'id');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getAssetTypeLabelAttribute(): string
    {
        return self::$ASSET_TYPES_LABELS[$this->asset_type] ?? Str::title(str_replace('_', ' ', (string) $this->asset_type));
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return self::getConditionStatusesList()[$this->condition_status] ?? Str::title(str_replace('_', ' ', (string) $this->condition_status));
    }

    // Helper methods

    /**
     * Returns the asset type labels for use in form select options.
     * This method resolves the "getAssetTypeOptions" error.
     *
     * @return array
     */
    public static function getAssetTypeOptions(): array
    {
        return self::$ASSET_TYPES_LABELS;
    }

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

    public function updatePhysicalCondition(string $newCondition, ?int $actingUserId = null): bool
    {
        $oldCondition = $this->condition_status;
        $this->condition_status = $newCondition;
        $saved = $this->save();
        if ($saved) {
            Log::info(sprintf('Equipment ID %d condition status updated.', $this->id), ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
        } else {
            Log::error(sprintf('Equipment ID %d: Failed to save physical condition status update.', $this->id), ['old_condition' => $oldCondition, 'new_condition' => $newCondition, 'user_id' => $actingUserId]);
        }

        return $saved;
    }

    /**
     * Returns a summary count of equipment grouped by status.
     *
     * @return array<string, int>
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
     * Calculates equipment utilization rate (on loan / total) * 100.
     *
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
