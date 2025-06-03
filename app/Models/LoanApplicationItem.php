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
 * * Represents a type of equipment and quantity requested in a loan application.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 * Migration context: 2025_04_22_105519_create_loan_application_items_table.php includes 'status' column.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type Type of equipment requested (e.g., 'laptop', 'projector')
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued Default: 0
 * @property int $quantity_returned Default: 0
 * @property string $status Status of this specific requested item (enum from migration)
 * @property string|null $notes Notes for this specific item request
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $equipmentTypeLabel Accessor for equipment_type label
 * @property-read string $equipment_type_label
 * @property-read int|null $loan_transaction_items_count
 * @method static \Database\Factories\LoanApplicationItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereEquipmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityIssued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withoutTrashed()
 * @mixin \Eloquent
 */
class LoanApplicationItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Note: Actual enum values for 'status' are defined in the migration
    // 2025_04_22_105519_create_loan_application_items_table.php
    // e.g., 'pending_approval', 'item_approved', etc.
    // This model should ideally have constants for these if they are referenced in code.

    protected $table = 'loan_application_items';

    protected $fillable = [
        'loan_application_id',
        'equipment_type',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'quantity_returned',
        'status', // Added to fillable
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
        // 'status' => 'pending_approval', // Default status if applicable, should match migration default
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

    // Accessor for status label (assuming you might add status constants/labels here or use a helper)
    // public function getStatusLabelAttribute(): string
    // {
    //     // Example: return self::$ITEM_STATUS_LABELS[$this->status] ?? Str::title($this->status);
    //     return Str::title(str_replace('_', ' ', (string) $this->status));
    // }


    public function recalculateQuantities(): void
    {
        $this->loadMissing('loanTransactionItems.loanTransaction');

        $issuedQty = 0;
        $returnedQty = 0;

        foreach ($this->loanTransactionItems as $item) {
            if ($item->loanTransaction?->type === LoanTransaction::TYPE_ISSUE && $item->status === LoanTransactionItem::STATUS_ITEM_ISSUED) {
                $issuedQty += $item->quantity_transacted;
            } elseif ($item->loanTransaction?->type === LoanTransaction::TYPE_RETURN &&
                      is_array(LoanTransactionItem::$RETURN_APPLICABLE_STATUSES) && // Ensure it's an array
                      in_array($item->status, LoanTransactionItem::$RETURN_APPLICABLE_STATUSES)) {
                if (!in_array($item->status, [LoanTransactionItem::STATUS_ITEM_REPORTED_LOST])) {
                    $returnedQty += $item->quantity_transacted;
                }
            }
        }
        $this->quantity_issued = $issuedQty;
        $this->quantity_returned = $returnedQty;

        // If this method modifies attributes, it should save.
        if ($this->isDirty(['quantity_issued', 'quantity_returned'])) {
            $this->save();
        }
    }
}
