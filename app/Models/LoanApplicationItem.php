<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // For transaction items related to this app item
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Loan Application Item Model.
 * Represents a type of equipment and quantity requested in a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type Type of equipment requested (e.g., 'laptop', 'projector')
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued Default: 0
 * @property int $quantity_returned Default: 0
 * @property string|null $notes Notes for this specific item request
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $equipmentTypeLabel Accessor for equipment_type label
 */
class LoanApplicationItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'loan_application_items';

    protected $fillable = [
        'loan_application_id',
        'equipment_type', // String identifier like 'laptop', 'projector'
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'quantity_returned',
        'notes',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved' => 'integer',
        'quantity_issued' => 'integer',
        'quantity_returned' => 'integer',
    ];

    protected $attributes = [
        'quantity_issued' => 0,
        'quantity_returned' => 0,
    ];

    protected static function newFactory(): LoanApplicationItemFactory
    {
        return LoanApplicationItemFactory::new();
    }

    // Relationships
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get all loan transaction items associated with this application item.
     * This links the requested item to its actual issuance/return transactions.
     */
    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'loan_application_item_id');
    }


    // Blameable relationships
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

    // Accessor for equipment_type label
    public function getEquipmentTypeLabelAttribute(): string
    {
        return Equipment::$ASSET_TYPES_LABELS[$this->equipment_type] ?? Str::title(str_replace('_', ' ', (string) $this->equipment_type));
    }

    /**
     * Update quantities based on a transaction item.
     * Typically called when a LoanTransactionItem is created or its status changes.
     */
    public function updateQuantitiesFromTransactionItem(LoanTransactionItem $transactionItem): void
    {
        if ($transactionItem->loanTransaction?->type === LoanTransaction::TYPE_ISSUE &&
            $transactionItem->status === LoanTransactionItem::STATUS_ITEM_ISSUED) {
            // This logic might be too simple if items can be un-issued or issue transactions cancelled.
            // Recalculating sums from all related transaction items is more robust.
            // $this->increment('quantity_issued', $transactionItem->quantity_transacted);
        } elseif ($transactionItem->loanTransaction?->type === LoanTransaction::TYPE_RETURN &&
            in_array($transactionItem->status, LoanTransactionItem::$RETURN_APPLICABLE_STATUSES)) {
            // $this->increment('quantity_returned', $transactionItem->quantity_transacted);
        }
        // For more robust quantity updates, sum from related LoanTransactionItems
        $this->recalculateQuantities();
        $this->save();
    }

    /**
     * Recalculates issued and returned quantities based on associated transaction items.
     */
    public function recalculateQuantities(): void
    {
        $this->loadMissing('loanTransactionItems.loanTransaction');

        $issuedQty = 0;
        $returnedQty = 0;

        foreach ($this->loanTransactionItems as $item) {
            if ($item->loanTransaction?->type === LoanTransaction::TYPE_ISSUE && $item->status === LoanTransactionItem::STATUS_ITEM_ISSUED) {
                $issuedQty += $item->quantity_transacted;
            } elseif ($item->loanTransaction?->type === LoanTransaction::TYPE_RETURN && in_array($item->status, LoanTransactionItem::$RETURN_APPLICABLE_STATUSES)) {
                // Only count actual returns, not "reported_lost" as "returned quantity" unless business logic dictates
                if (!in_array($item->status, [LoanTransactionItem::STATUS_ITEM_REPORTED_LOST])) {
                    $returnedQty += $item->quantity_transacted;
                }
            }
        }
        $this->quantity_issued = $issuedQty;
        $this->quantity_returned = $returnedQty;
    }

}
