<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\LoanTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Loan Transaction Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 */
class LoanTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Blameable; // Assuming a custom trait for blameable fields

    // --- CONSTANTS ---
    public const TYPE_ISSUE = 'issue';
    public const TYPE_RETURN = 'return';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_RETURNED_GOOD = 'returned_good';
    public const STATUS_RETURNED_DAMAGED = 'returned_damaged';
    public const STATUS_ITEMS_REPORTED_LOST = 'items_reported_lost';
    public const STATUS_RETURNED_WITH_LOSS = 'returned_with_loss';
    public const STATUS_RETURNED_WITH_DAMAGE_AND_LOSS = 'returned_with_damage_and_loss';
    public const STATUS_PARTIALLY_RETURNED = 'partially_returned';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_OVERDUE = 'overdue';

    public static array $TYPES_LABELS = [
        self::TYPE_ISSUE => 'Pengeluaran',
        self::TYPE_RETURN => 'Pemulangan',
    ];

    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING => 'Menunggu Tindakan',
        self::STATUS_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_RETURNED => 'Telah Dipulangkan (Umum)',
        self::STATUS_RETURNED_PENDING_INSPECTION => 'Pemulangan (Menunggu Semakan)',
        self::STATUS_RETURNED_GOOD => 'Dipulangkan (Keadaan Baik)',
        self::STATUS_RETURNED_DAMAGED => 'Dipulangkan (Ada Kerosakan)',
        self::STATUS_ITEMS_REPORTED_LOST => 'Item Dilaporkan Hilang',
        self::STATUS_RETURNED_WITH_LOSS => 'Dipulangkan (Dengan Kehilangan)',
        self::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'Dipulangkan (Dengan Kerosakan & Kehilangan)',
        self::STATUS_PARTIALLY_RETURNED => 'Dipulangkan Sebahagian',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
        self::STATUS_OVERDUE => 'Tertunggak (Transaksi)',
    ];

    protected $table = 'loan_transactions';
    protected $fillable = [ 'loan_application_id', 'type', 'transaction_date', 'issuing_officer_id', 'receiving_officer_id', 'accessories_checklist_on_issue', 'issue_notes', 'issue_timestamp', 'returning_officer_id', 'return_accepting_officer_id', 'accessories_checklist_on_return', 'return_notes', 'return_timestamp', 'related_transaction_id', 'status' ];
    protected $casts = [ 'transaction_date' => 'datetime', 'issue_timestamp' => 'datetime', 'return_timestamp' => 'datetime', 'accessories_checklist_on_issue' => 'array', 'accessories_checklist_on_return' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime' ];
    protected $attributes = [ 'status' => self::STATUS_PENDING ];

    protected static function newFactory(): LoanTransactionFactory { return LoanTransactionFactory::new(); }

    // --- RELATIONSHIPS ---
    public function loanApplication(): BelongsTo { return $this->belongsTo(LoanApplication::class, 'loan_application_id'); }
    public function loanTransactionItems(): HasMany { return $this->hasMany(LoanTransactionItem::class, 'loan_transaction_id'); }
    public function items(): HasMany { return $this->loanTransactionItems(); }
    public function issuingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'issuing_officer_id'); }
    public function receivingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'receiving_officer_id'); }
    public function returningOfficer(): BelongsTo { return $this->belongsTo(User::class, 'returning_officer_id'); }
    public function returnAcceptingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'return_accepting_officer_id'); }
    public function relatedIssueTransaction(): BelongsTo { return $this->belongsTo(LoanTransaction::class, 'related_transaction_id'); }

    // --- ACCESSORS ---
    public function getTypeLabelAttribute(): string { return __(self::$TYPES_LABELS[$this->type] ?? Str::title(str_replace('_', ' ', $this->type))); }
    public function getStatusLabelAttribute(): string { return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status))); }

    public function getTypeColorClassAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ISSUE => 'text-bg-info',
            self::TYPE_RETURN => 'text-bg-primary',
            default => 'text-bg-secondary',
        };
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ISSUED => 'text-bg-info',
            self::STATUS_RETURNED, self::STATUS_RETURNED_GOOD, self::STATUS_COMPLETED => 'text-bg-success',
            self::STATUS_RETURNED_PENDING_INSPECTION, self::STATUS_PARTIALLY_RETURNED => 'text-bg-primary',
            self::STATUS_RETURNED_DAMAGED, self::STATUS_OVERDUE => 'text-bg-warning',
            self::STATUS_ITEMS_REPORTED_LOST, self::STATUS_CANCELLED => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    /**
     * Accessor for a representative name of the items in the transaction.
     * Note: This accessor will trigger database queries if 'loanTransactionItems.equipment' is not already eager-loaded.
     * @return string
     */
    public function getItemNameAttribute(): string
    {
        if (!$this->relationLoaded('loanTransactionItems')) {
            $this->load('loanTransactionItems.equipment:id,brand,model');
        }
        if ($this->loanTransactionItems->isNotEmpty()) {
            $firstItem = $this->loanTransactionItems->first();
            if ($firstItem?->equipment) {
                return trim(($firstItem->equipment->brand ?? '') . ' ' . ($firstItem->equipment->model ?? __('Item Peralatan')));
            }
            return __('Item Tidak Diketahui');
        }
        return __('Tiada Item');
    }

    /**
     * Accessor for the total quantity of items in this transaction.
     * Note: This accessor will trigger a database query if 'loanTransactionItems' is not already eager-loaded.
     * @return int
     */
    public function getQuantityAttribute(): int
    {
        if ($this->relationLoaded('loanTransactionItems')) {
            return (int) $this->loanTransactionItems->sum('quantity_transacted');
        }
        // If not loaded, performs a separate query.
        return (int) $this->loanTransactionItems()->sum('quantity_transacted');
    }

    // --- STATIC HELPERS ---
    public static function getDefinedDefaultRelationsStatic(): array
    {
        return [
            'loanApplication.user:id,name',
            'loanTransactionItems.equipment:id,brand,model,tag_id',
            'issuingOfficer:id,name',
            'receivingOfficer:id,name',
            'returningOfficer:id,name',
            'returnAcceptingOfficer:id,name',
            'relatedIssueTransaction',
        ];
    }
    public static function getTypesOptions(): array { return self::$TYPES_LABELS; }
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }

    // --- HELPER METHODS ---
    public function isIssue(): bool { return $this->type === self::TYPE_ISSUE; }
    public function isReturn(): bool { return $this->type === self::TYPE_RETURN; }
    public function updateParentLoanApplicationStatus(): void
    {
        if ($this->loanApplication) {
            if (method_exists($this->loanApplication, 'updateOverallStatusAfterTransaction')) {
                $this->loanApplication->updateOverallStatusAfterTransaction();
            }
        }
    }
}
