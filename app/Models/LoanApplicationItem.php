<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationItemFactory; // Correct import for the specific factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Removed direct import of LoanTransaction as it's used via $transactionItem->loanTransaction
// Ensure LoanTransactionItem is correctly aliased or used if its constants are directly accessed here.
// use App\Models\LoanTransactionItem as TransactionItemModel; // Already aliased in original

/**
 * Loan Application Item Model.
 * Represents a type of equipment and quantity requested in a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued
 * @property int $quantity_returned
 * @property string $status
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection|LoanTransactionItem[] $loanTransactionItems
 * @property-read string $status_label
 */
class LoanApplicationItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Constants based on the ENUM values in the migration
    public const STATUS_PENDING_APPROVAL = 'pending_approval';

    public const STATUS_ITEM_APPROVED = 'item_approved';

    public const STATUS_ITEM_REJECTED = 'item_rejected';

    public const STATUS_AWAITING_ISSUANCE = 'awaiting_issuance';

    public const STATUS_FULLY_ISSUED = 'fully_issued';

    public const STATUS_PARTIALLY_ISSUED = 'partially_issued';

    public const STATUS_FULLY_RETURNED = 'fully_returned';

    public const STATUS_ITEM_CANCELLED = 'item_cancelled';

    public const ITEM_STATUS_LABELS = [
        self::STATUS_PENDING_APPROVAL => 'Menunggu Kelulusan (Item)',
        self::STATUS_ITEM_APPROVED => 'Diluluskan (Item)',
        self::STATUS_ITEM_REJECTED => 'Ditolak (Item)',
        self::STATUS_AWAITING_ISSUANCE => 'Menunggu Pengeluaran',
        self::STATUS_FULLY_ISSUED => 'Telah Dikeluarkan Sepenuhnya',
        self::STATUS_PARTIALLY_ISSUED => 'Telah Dikeluarkan Sebahagian',
        self::STATUS_FULLY_RETURNED => 'Telah Dipulangkan Sepenuhnya',
        self::STATUS_ITEM_CANCELLED => 'Dibatalkan (Item)',
    ];

    protected $fillable = [
        'loan_application_id',
        'equipment_id',
        'equipment_type',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'quantity_returned',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved' => 'integer',
        'quantity_issued' => 'integer',
        'quantity_returned' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($item) {
            if (empty($item->status)) {
                $item->status = self::STATUS_PENDING_APPROVAL;
            }
            $item->quantity_issued = $item->quantity_issued ?? 0;
            $item->quantity_returned = $item->quantity_returned ?? 0;
        });

        static::deleting(function ($loanApplicationItem) {
            DB::transaction(function () use ($loanApplicationItem) {
                // Assuming LoanTransactionItem model is App\Models\LoanTransactionItem
                $loanApplicationItem->loanTransactionItems()->delete();
            });
        });
    }

    protected static function newFactory(): LoanApplicationItemFactory
    {
        return LoanApplicationItemFactory::new();
    }

    // Relationships
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function loanTransactionItems(): HasMany
    {
        // Assuming LoanTransactionItem model is App\Models\LoanTransactionItem
        return $this->hasMany(LoanTransactionItem::class, 'loan_application_item_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::ITEM_STATUS_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    // Helper Methods
    public function recalculateQuantities(): void
    {
        $this->loadMissing('loanTransactionItems.loanTransaction');

        $issuedQty = 0;
        $returnedQty = 0;

        foreach ($this->loanTransactionItems as $transactionItem) {
            // Ensure $transactionItem is an instance of App\Models\LoanTransactionItem
            // and $transactionItem->loanTransaction is an instance of App\Models\LoanTransaction
            if ($transactionItem instanceof LoanTransactionItem && $transactionItem->loanTransaction instanceof LoanTransaction) {
                if (
                    $transactionItem->loanTransaction->type === LoanTransaction::TYPE_ISSUE && //
                    $transactionItem->status === LoanTransactionItem::STATUS_ITEM_ISSUED //
                ) {
                    $issuedQty += $transactionItem->quantity_transacted;
                } elseif (
                    $transactionItem->loanTransaction->type === LoanTransaction::TYPE_RETURN && //
                    is_array(LoanTransactionItem::$RETURN_APPLICABLE_STATUSES) && //
                    in_array($transactionItem->status, LoanTransactionItem::$RETURN_APPLICABLE_STATUSES) //
                ) {
                    if (! in_array($transactionItem->status, [LoanTransactionItem::STATUS_ITEM_REPORTED_LOST])) { //
                        $returnedQty += $transactionItem->quantity_transacted;
                    }
                }
            }
        }

        $this->quantity_issued = $issuedQty;
        $this->quantity_returned = $returnedQty;

        // No save() or updateOverallStatus() here, the calling context (e.g., LTI observer) should handle saving this model
        // and then triggering the parent LoanApplication update if necessary.
        // This method purely recalculates and sets properties on the current instance.
    }
}
