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
 * 
 * (PHPDoc from your provided file, confirmed and aligned with model)
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
 * @property-read string $statusTranslated Accessor: status_translated
 * @property-read string|null $conditionOnReturnTranslated Accessor: condition_on_return_translated
 * @property-read string|null $condition_on_return_translated
 * @property-read string $status_translated
 * @method static \Database\Factories\LoanTransactionItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereAccessoriesChecklistIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereAccessoriesChecklistReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereConditionOnReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereItemNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereLoanApplicationItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereLoanTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereQuantityTransacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withoutTrashed()
 * @mixin \Eloquent
 */
class LoanTransactionItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    // VAL_ prefix was used in factory, using direct constant names here for consistency with other models.
    public const STATUS_ITEM_ISSUED = 'issued';
    public const STATUS_ITEM_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_ITEM_RETURNED_GOOD = 'returned_good';
    public const STATUS_ITEM_RETURNED_MINOR_DAMAGE = 'returned_minor_damage';
    public const STATUS_ITEM_RETURNED_MAJOR_DAMAGE = 'returned_major_damage';
    public const STATUS_ITEM_REPORTED_LOST = 'reported_lost';
    public const STATUS_ITEM_UNSERVICEABLE_ON_RETURN = 'unserviceable_on_return'; // Clarified from 'unserviceable'

    public static array $STATUSES_LABELS = [
        self::STATUS_ITEM_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION => 'Pemulangan (Menunggu Semakan)',
        self::STATUS_ITEM_RETURNED_GOOD => 'Dipulangkan (Keadaan Baik)',
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE => 'Dipulangkan (Rosak Ringan)',
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE => 'Dipulangkan (Rosak Teruk)',
        self::STATUS_ITEM_REPORTED_LOST => 'Dilapor Hilang',
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN => 'Dipulangkan (Tidak Boleh Digunakan)',
    ];

    // Statuses applicable when an item is part of a RETURN transaction
    public static array $RETURN_APPLICABLE_STATUSES = [
        self::STATUS_ITEM_RETURNED_GOOD,
        self::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
        self::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
        self::STATUS_ITEM_REPORTED_LOST,
        self::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
        self::STATUS_ITEM_RETURNED_PENDING_INSPECTION,
    ];

    protected $table = 'loan_transaction_items';

    protected $fillable = [
        'loan_transaction_id',
        'equipment_id',
        'loan_application_item_id',
        'quantity_transacted',
        'status',
        'condition_on_return', // Stores key like Equipment::CONDITION_GOOD
        'accessories_checklist_issue',
        'accessories_checklist_return',
        'item_notes',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'quantity_transacted' => 'integer',
        'accessories_checklist_issue' => 'array',
        'accessories_checklist_return' => 'array',
    ];

    protected $attributes = [
        'quantity_transacted' => 1,
        // Default status should ideally be set based on the transaction type (issue/return)
        // For example, if linked to an 'issue' transaction, default status might be 'issued'.
        // This can be handled by the service/controller creating the item.
    ];

    // Static helper methods
    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }
    public static function getStatusesList(): array
    {
        return self::$STATUSES_LABELS;
    } // For factory
    public static function getConditionStatusesList(): array // Actually refers to Equipment conditions
    {
        return Equipment::$CONDITION_STATUSES_LABELS; // Used by factory, values should be keys of Equipment statuses
    }

    protected static function newFactory(): LoanTransactionItemFactory
    {
        return LoanTransactionItemFactory::new();
    }

    /**
     * After saving, update parent LoanApplicationItem quantities and LoanApplication status.
     * This is best handled by an observer (LoanTransactionItemObserver).
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $item) {
            // Update quantities on the related LoanApplicationItem
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities(); // Assumes method exists
                $item->loanApplicationItem->save(); // Save the application item after recalculating
            }
            // Trigger update on the main LoanApplication status
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });

        static::deleted(function (self $item) {
            // Also update quantities on delete
            if ($item->loanApplicationItem) {
                $item->loanApplicationItem->recalculateQuantities();
                $item->loanApplicationItem->save();
            }
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });
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

    // Blameable
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
    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getConditionOnReturnTranslatedAttribute(): ?string
    {
        // Uses Equipment's condition labels as condition_on_return stores keys from Equipment model
        return $this->condition_on_return
            ? (Equipment::$CONDITION_STATUSES_LABELS[$this->condition_on_return] ?? Str::title(str_replace('_', ' ', (string) $this->condition_on_return)))
            : null;
    }
}
