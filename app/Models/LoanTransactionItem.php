<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanTransactionItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // If you use soft deletes for this model
use Illuminate\Support\Str;

/**
 * Loan Transaction Item Model.
 * 
 * Represents a specific equipment item within a loan transaction (either an issue or a return).
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id
 * @property int|null $loan_application_item_id Link back to the requested item in application
 * @property int $quantity_transacted Typically 1 for serialized items
 * @property string $status Status of this item in this transaction
 * @property string|null $condition_on_return
 * @property array<array-key, mixed>|null $accessories_checklist_issue
 * @property array<array-key, mixed>|null $accessories_checklist_return
 * @property string|null $item_notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Equipment $equipment
 * @property-read string|null $condition_on_return_translated
 * @property-read string $status_label
 * @property-read string $status_translated
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read LoanTransactionItem|null $returnRecord
 * @property-read \App\Models\User|null $updater
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
    use SoftDeletes; // Include if you are using soft deletes for this model

    // Status constants for a loan transaction item
    public const STATUS_ITEM_ISSUED = 'issued';

    public const STATUS_ITEM_RETURNED = 'returned'; // General returned status, if needed. Specific statuses below are usually preferred.

    public const STATUS_ITEM_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';

    public const STATUS_ITEM_RETURNED_GOOD = 'returned_good';

    public const STATUS_ITEM_RETURNED_MINOR_DAMAGE = 'returned_minor_damage';

    public const STATUS_ITEM_RETURNED_MAJOR_DAMAGE = 'returned_major_damage';

    public const STATUS_ITEM_REPORTED_LOST = 'reported_lost';

    public const STATUS_ITEM_UNSERVICEABLE_ON_RETURN = 'unserviceable_on_return';

    // public const STATUS_ITEM_OVERDUE = 'overdue'; // Overdue is typically an application or loan transaction level status, not item level.

    // Labels for status constants for display purposes
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

    // Statuses that signify an item has been processed for return in some form
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
        'accessories_checklist_issue',   // Stored as JSON in DB
        'accessories_checklist_return',  // Stored as JSON in DB
        'item_notes',
        // created_by, updated_by, deleted_by are typically handled by observers or traits (e.g., Blameable)
    ];

    /**
     * The attributes that should be cast.
     * This is crucial for accessories_checklist fields.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_transacted' => 'integer',
        'accessories_checklist_issue' => 'array',   // Automatically converts JSON to array and vice-versa
        'accessories_checklist_return' => 'array',  // Automatically converts JSON to array and vice-versa
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // Only if using SoftDeletes
    ];

    /**
     * Default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'quantity_transacted' => 1, // Default for serialized items
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): LoanTransactionItemFactory
    {
        return LoanTransactionItemFactory::new();
    }

    /**
     * The "booted" method of the model.
     */
    // protected static function booted(): void // The `boot()` method is generally preferred for model event registration over `booted()`
    // {
    // If you still need $CONDITION_ON_RETURN_LIST, it's better to define it as a static method
    // or ensure Equipment::getConditionStatusOptions() is always available.
    // }
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

    /**
     * Attempt to find a return transaction item record associated with this issued item.
     * Note: This relationship's effectiveness depends on how returns are linked.
     * If a new LTI is created for return, linking them via a shared identifier (e.g., original_loan_transaction_item_id on the return LTI)
     * or checking via equipment_id and loan_application_item_id against 'return' type transactions for the same application is more robust.
     * The current implementation is a basic example.
     */
    public function returnRecord(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        // This assumes that a subsequent LoanTransactionItem exists for the same equipment
        // within a 'return' type LoanTransaction for the same LoanApplication.
        // This is a simplified example. A more robust link might be needed.
        return $this->hasOne(LoanTransactionItem::class, 'equipment_id', 'equipment_id')
            ->whereHas('loanTransaction', function ($query): void {
                $query->where('type', LoanTransaction::TYPE_RETURN)
                    ->where('loan_application_id', $this->loanTransaction?->loan_application_id);
            })
            ->whereIn('status', self::$RETURN_APPLICABLE_STATUSES); // And the status indicates it's a return processing
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
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    // Alias for easier access in some contexts
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusTranslatedAttribute();
    }

    public function getConditionOnReturnTranslatedAttribute(): ?string
    {
        if (! $this->condition_on_return) {
            return null;
        }

        // Assumes Equipment::$CONDITION_STATUSES_LABELS exists and is structured correctly [value => label]
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
     * Get list of valid condition options for when an item is returned.
     * Returns an associative array [value => label].
     */
    public static function getConditionOnReturnOptions(): array
    {
        return Equipment::getConditionStatusOptions(); // Directly use Equipment's method
    }

    /**
     * Get list of valid condition status KEYS for when an item is returned.
     * Returns an array of string values.
     */
    public static function getConditionStatusesList(): array
    {
        return Equipment::getConditionStatusesList(); // Directly use Equipment's method
    }

    /**
     * The "boot" method of the model.
     * Used for registering model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Listener for when a LoanTransactionItem is saved (created or updated)
        static::saved(function (self $item): void {
            if ($item->loanApplicationItem) { // Check if the relationship exists
                $item->loanApplicationItem->recalculateQuantities();
                // Save if dirty, but recalculateQuantities should ideally handle saving or return a flag
                if ($item->loanApplicationItem->isDirty()) {
                    $item->loanApplicationItem->saveQuietly(); // Use saveQuietly to avoid infinite loops if recalculateQuantities also triggers saves
                }
            }

            // Update parent LoanTransaction, which should then update LoanApplication
            $item->loanTransaction?->updateParentLoanApplicationStatus();
        });

        // Listener for when a LoanTransactionItem is deleted (soft or hard)
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
