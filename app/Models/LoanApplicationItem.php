<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Sum quantities for 'issue' transactions
        $issuedQty = $this->loanTransactionItems
            ->where('loanTransaction.type', LoanTransaction::TYPE_ISSUE)
            ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
            ->sum('quantity_transacted');

        // Sum quantities for 'return' transactions, excluding 'lost' items
        $returnedQty = $this->loanTransactionItems
            ->where('loanTransaction.type', LoanTransaction::TYPE_RETURN)
            ->whereNotIn('status', [LoanTransactionItem::STATUS_ITEM_REPORTED_LOST])
            ->sum('quantity_transacted');

        $this->quantity_issued = $issuedQty;
        $this->quantity_returned = $returnedQty;

        // **THE FIX**: This block checks if the quantities have changed and, if so,
        // saves the updated model to the database. This is the crucial final step.
        if ($this->isDirty(['quantity_issued', 'quantity_returned'])) {
            $this->save();
        }
    }
}
