<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanTransactionItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * LoanTransactionItem Model.
 *
 * Represents a specific equipment item within a loan transaction (either an issue or a return).
 * Each record links a transaction (issue/return) with a specific equipment asset,
 * and may be associated with a LoanApplicationItem for workflow tracking.
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id
 * @property int|null $loan_application_item_id
 * @property int $quantity_transacted
 * @property string $status
 * @property string|null $condition_on_return
 * @property array|string|null $accessories_checklist_issue
 * @property array|string|null $accessories_checklist_return
 * @property string|null $item_notes
 * @property string|null $notes
 * @property int|null $quantity_returned
 * @property string|null $return_status
 * @property array|string|null $accessories_checklist_on_return
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read \App\Models\Equipment $equipment
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 */
class LoanTransactionItem extends Model
{
    use HasFactory, SoftDeletes;

    // ---- STATUS CONSTANTS (should match migration and business logic) ----
    public const STATUS_ITEM_ISSUED = 'issued';
    public const STATUS_ITEM_RETURNED = 'returned';
    public const STATUS_ITEM_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_ITEM_RETURNED_GOOD = 'returned_good';
    public const STATUS_ITEM_RETURNED_MINOR_DAMAGE = 'returned_minor_damage';
    public const STATUS_ITEM_RETURNED_MAJOR_DAMAGE = 'returned_major_damage';
    public const STATUS_ITEM_REPORTED_LOST = 'reported_lost';
    public const STATUS_ITEM_UNSERVICEABLE_ON_RETURN = 'unserviceable_on_return';

    // Labels for status constants for UI and reporting
    public static array $STATUSES_LABELS = [
        self::STATUS_ITEM_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_ITEM_RETURNED => 'Telah Dipulangkan (Umum)',
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION => 'Dipulangkan (Menunggu Semakan)',
        self::STATUS_ITEM_RETURNED_GOOD => 'Dipulangkan (Keadaan Baik)',
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE => 'Dipulangkan (Rosak Ringan)',
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE => 'Dipulangkan (Rosak Teruk)',
        self::STATUS_ITEM_REPORTED_LOST => 'Dilaporkan Hilang',
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN => 'Dipulangkan (Tidak Boleh Servis)',
    ];

    // Statuses applicable for returns
    public static array $RETURN_APPLICABLE_STATUSES = [
        self::STATUS_ITEM_RETURNED,
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION,
        self::STATUS_ITEM_RETURNED_GOOD,
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
        self::STATUS_ITEM_REPORTED_LOST,
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
    ];

    // Table name (explicit for clarity)
    protected $table = 'loan_transaction_items';

    // ---- MASS ASSIGNMENT ----
    protected $fillable = [
        'loan_transaction_id',
        'equipment_id',
        'loan_application_item_id',
        'quantity_transacted',
        'status',
        'condition_on_return',
        'accessories_checklist_issue',
        'accessories_checklist_return',
        'item_notes',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // ---- ATTRIBUTE CASTING ----
    protected $casts = [
        'quantity_transacted' => 'integer',
        'accessories_checklist_issue' => 'array',
        'accessories_checklist_return' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Default value for quantity (usually 1 for asset-based transactions)
    protected $attributes = [
        'quantity_transacted' => 1,
    ];

    /**
     * Factory reference for this model.
     */
    protected static function newFactory(): LoanTransactionItemFactory
    {
        return LoanTransactionItemFactory::new();
    }

    // ---- RELATIONSHIPS ----

    /**
     * The parent loan transaction (issue/return) for this item.
     */
    public function loanTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'loan_transaction_id');
    }

    /**
     * The equipment asset involved in this transaction item.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /**
     * The related loan application item, if any (links to the loan request).
     */
    public function loanApplicationItem(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationItem::class, 'loan_application_item_id');
    }

    // ---- ACCESSORS ----

    /**
     * Get a human-readable status label (localized if translation exists).
     */
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    /**
     * Alias for status label (for UI).
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusTranslatedAttribute();
    }

    /**
     * Get a human-readable label for the condition on return, if set.
     */
    public function getConditionOnReturnTranslatedAttribute(): ?string
    {
        if (!$this->condition_on_return) {
            return null;
        }
        // Use Equipment::getConditionStatusesList() to resolve label
        $conditionList = method_exists(Equipment::class, 'getConditionStatusesList')
            ? Equipment::getConditionStatusesList()
            : [
                Equipment::CONDITION_NEW => 'Baru',
                Equipment::CONDITION_GOOD => 'Baik',
                Equipment::CONDITION_FAIR => 'Sederhana',
                Equipment::CONDITION_MINOR_DAMAGE => 'Rosak Minor',
                Equipment::CONDITION_MAJOR_DAMAGE => 'Rosak Major',
                Equipment::CONDITION_UNSERVICEABLE => 'Tidak Boleh Digunakan',
                Equipment::CONDITION_LOST => 'Hilang',
            ];
        return __($conditionList[$this->condition_on_return] ?? Str::title(str_replace('_', ' ', (string) $this->condition_on_return)));
    }

    // ---- STATIC HELPERS ----

    /**
     * Get the list of all item status labels.
     */
    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }

    /**
     * Get the list of all valid item statuses.
     */
    public static function getStatusesList(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    /**
     * Get the list of statuses applicable to item returns.
     */
    public static function getReturnApplicableStatuses(): array
    {
        return self::$RETURN_APPLICABLE_STATUSES;
    }

    // ---- MODEL EVENTS ----

    /**
     * The "boot" method for model events.
     * Handles recalculation of parent LoanApplicationItem quantities and
     * updating parent LoanApplication status after save/delete.
     */
    protected static function boot()
    {
        parent::boot();

        // After save, recalculate parent item quantities and update parent application status
        static::saved(function (self $item): void {
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->saveQuietly();
                }
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });

        // After delete, recalculate as well
        static::deleted(function (self $item): void {
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->saveQuietly();
                }
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });
    }
}
