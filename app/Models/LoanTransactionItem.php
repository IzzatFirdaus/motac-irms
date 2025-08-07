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
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id
 * @property int|null $loan_application_item_id
 * @property int $quantity_transacted
 * @property string $status
 * @property string|null $condition_on_return
 * @property array|null $accessories_checklist_issue
 * @property array|null $accessories_checklist_return
 * @property string|null $item_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class LoanTransactionItem extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants for a loan transaction item
    public const STATUS_ITEM_ISSUED = 'issued';
    public const STATUS_ITEM_RETURNED = 'returned';
    public const STATUS_ITEM_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_ITEM_RETURNED_GOOD = 'returned_good';
    public const STATUS_ITEM_RETURNED_MINOR_DAMAGE = 'returned_minor_damage';
    public const STATUS_ITEM_RETURNED_MAJOR_DAMAGE = 'returned_major_damage';
    public const STATUS_ITEM_REPORTED_LOST = 'reported_lost';
    public const STATUS_ITEM_UNSERVICEABLE_ON_RETURN = 'unserviceable_on_return';

    // Labels for status constants
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

    public static array $RETURN_APPLICABLE_STATUSES = [
        self::STATUS_ITEM_RETURNED,
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION,
        self::STATUS_ITEM_RETURNED_GOOD,
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
        self::STATUS_ITEM_REPORTED_LOST,
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
    ];

    protected $table = 'loan_transaction_items';

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
    ];

    protected $casts = [
        'quantity_transacted' => 'integer',
        'accessories_checklist_issue' => 'array',
        'accessories_checklist_return' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'quantity_transacted' => 1,
    ];

    protected static function newFactory(): LoanTransactionItemFactory
    {
        return LoanTransactionItemFactory::new();
    }

    // Relationships

    public function loanTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'loan_transaction_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function loanApplicationItem(): BelongsTo
    {
        return $this->belongsTo(LoanApplicationItem::class, 'loan_application_item_id');
    }

    // Accessors

    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusTranslatedAttribute();
    }

    public function getConditionOnReturnTranslatedAttribute(): ?string
    {
        if (!$this->condition_on_return) {
            return null;
        }
        return __(Equipment::$CONDITION_STATUSES_LABELS[$this->condition_on_return] ?? Str::title(str_replace('_', ' ', (string) $this->condition_on_return)));
    }

    // Static helper methods

    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }

    public static function getStatusesList(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    public static function getReturnApplicableStatuses(): array
    {
        return self::$RETURN_APPLICABLE_STATUSES;
    }

    /**
     * The "boot" method for model events.
     * Handles recalculation of quantities and updating parent status on save/delete.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $item): void {
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->saveQuietly();
                }
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });

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
