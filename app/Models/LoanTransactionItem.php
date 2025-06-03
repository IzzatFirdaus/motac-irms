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
 * Loan Transaction Item Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id Specific physical equipment item
 * @property int|null $loan_application_item_id Link to original request line item
 * @property int $quantity_transacted Typically 1 for serialized items
 * @property string $status Status of this item within THIS transaction (e.g., 'issued', 'returned_good')
 * @property string|null $condition_on_return Matches Equipment model's condition_status enum keys (e.g., Equipment::CONDITION_GOOD)
 * @property array|null $accessories_checklist_issue Item-specific accessories issued (JSON)
 * @property array|null $accessories_checklist_return Item-specific accessories returned (JSON)
 * @property string|null $item_notes Notes specific to this item in this transaction
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read \App\Models\Equipment $equipment
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 * @property-read string|null $condition_on_return_translated
 */
class LoanTransactionItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_ITEM_ISSUED = 'issued';
    public const STATUS_ITEM_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_ITEM_RETURNED_GOOD = 'returned_good';
    public const STATUS_ITEM_RETURNED_MINOR_DAMAGE = 'returned_minor_damage';
    public const STATUS_ITEM_RETURNED_MAJOR_DAMAGE = 'returned_major_damage';
    public const STATUS_ITEM_REPORTED_LOST = 'reported_lost';
    public const STATUS_ITEM_UNSERVICEABLE_ON_RETURN = 'unserviceable_on_return'; // Aligned with Design Doc Sec 4.3 enum

    public static array $STATUSES_LABELS = [
        self::STATUS_ITEM_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION => 'Pemulangan (Menunggu Semakan)',
        self::STATUS_ITEM_RETURNED_GOOD => 'Dipulangkan (Keadaan Baik)',
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE => 'Dipulangkan (Rosak Ringan)',
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE => 'Dipulangkan (Rosak Teruk)',
        self::STATUS_ITEM_REPORTED_LOST => 'Dilapor Hilang',
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN => 'Dipulangkan (Tidak Boleh Digunakan)',
    ];

    public static array $RETURN_APPLICABLE_STATUSES = [
        self::STATUS_ITEM_RETURNED_GOOD,
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
        self::STATUS_ITEM_REPORTED_LOST,
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION,
    ];

    public static array $CONDITION_ON_RETURN_LIST;


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

    protected static function booted(): void
    {
        // parent::booted(); // Call if Laravel's base Model ever implements a booted method.
        if (empty(self::$CONDITION_ON_RETURN_LIST)) {
            // Ensure Equipment::getConditionStatusOptions() returns an array of ['value' => 'Label']
            self::$CONDITION_ON_RETURN_LIST = Equipment::getConditionStatusOptions();
        }
    }

    // Relationships
    public function loanTransaction(): BelongsTo { return $this->belongsTo(LoanTransaction::class, 'loan_transaction_id'); }
    public function equipment(): BelongsTo { return $this->belongsTo(Equipment::class, 'equipment_id'); }
    public function loanApplicationItem(): BelongsTo { return $this->belongsTo(LoanApplicationItem::class, 'loan_application_item_id'); }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }
    public function getStatusLabelAttribute(): string { return $this->getStatusTranslatedAttribute(); }

    public function getConditionOnReturnTranslatedAttribute(): ?string
    {
        return $this->condition_on_return
            ? (__(Equipment::$CONDITION_STATUSES_LABELS[$this->condition_on_return] ?? Str::title(str_replace('_', ' ', (string) $this->condition_on_return))))
            : null;
    }

    // Static helper methods
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }

    public static function getStatusesList(): array { return array_keys(self::$STATUSES_LABELS); }

    public static function getReturnApplicableStatuses(): array { return self::$RETURN_APPLICABLE_STATUSES; }

    public static function getConditionStatusesList(): array
    {
         return array_keys(Equipment::getConditionStatusOptions());
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $item) {
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->save();
                }
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });

        static::deleted(function (self $item) {
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->save();
                }
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });
    }
}
