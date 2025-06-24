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

class LoanTransaction extends Model
{
    use Blameable;
    use HasFactory;
    use SoftDeletes;

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

    protected $table = 'loan_transactions';

    protected $fillable = [
        'loan_application_id', 'type', 'transaction_date', 'issuing_officer_id',
        'receiving_officer_id', 'accessories_checklist_on_issue', 'issue_notes',
        'issue_timestamp', 'returning_officer_id', 'return_accepting_officer_id',
        'accessories_checklist_on_return', 'return_notes', 'return_timestamp',
        'related_transaction_id', 'status'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'issue_timestamp' => 'datetime',
        'return_timestamp' => 'datetime',
        'accessories_checklist_on_issue' => 'array',
        'accessories_checklist_on_return' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING
    ];

    protected static function newFactory(): LoanTransactionFactory
    {
        return LoanTransactionFactory::new();
    }

    // --- RELATIONSHIPS ---
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'loan_transaction_id');
    }

    public function items(): HasMany
    {
        return $this->loanTransactionItems();
    }

    public function issuingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuing_officer_id');
    }

    public function receivingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiving_officer_id');
    }

    public function returningOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returning_officer_id');
    }

    public function returnAcceptingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_accepting_officer_id');
    }

    public function relatedIssueTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'related_transaction_id');
    }

    // --- ACCESSORS ---
    public function getTypeLabelAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? Str::title(str_replace('_', ' ', $this->type));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

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

    public function getItemNameAttribute(): string
    {
        if (! $this->relationLoaded('loanTransactionItems')) {
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

    public function getQuantityAttribute(): int
    {
        if ($this->relationLoaded('loanTransactionItems')) {
            return (int) $this->loanTransactionItems->sum('quantity_transacted');
        }
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

    /**
     * UPDATED: Now returns a translatable array of transaction types.
     * This makes the model's output dynamic based on the app's locale.
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_ISSUE => __('reports.filters.type_issue'),
            self::TYPE_RETURN => __('reports.filters.type_return'),
        ];
    }

    /**
     * UPDATED: Returns a translatable array of transaction statuses.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => __('common.statuses.pending'),
            self::STATUS_ISSUED => __('common.statuses.issued'),
            self::STATUS_RETURNED_PENDING_INSPECTION => __('common.statuses.returned_pending_inspection'),
            self::STATUS_RETURNED_GOOD => __('common.statuses.returned_good'),
            self::STATUS_RETURNED_DAMAGED => __('common.statuses.returned_damaged'),
            self::STATUS_ITEMS_REPORTED_LOST => __('common.statuses.items_reported_lost'),
            self::STATUS_COMPLETED => __('common.statuses.completed'),
            self::STATUS_CANCELLED => __('common.statuses.cancelled'),
            self::STATUS_OVERDUE => __('common.statuses.overdue'),
        ];
    }

    public static function getStatusesList(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ISSUED,
            self::STATUS_RETURNED_PENDING_INSPECTION,
            self::STATUS_RETURNED_GOOD,
            self::STATUS_RETURNED_DAMAGED,
            self::STATUS_ITEMS_REPORTED_LOST,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    // --- HELPER METHODS ---
    public function isIssue(): bool
    {
        return $this->type === self::TYPE_ISSUE;
    }

    public function isReturn(): bool
    {
        return $this->type === self::TYPE_RETURN;
    }

    public function updateParentLoanApplicationStatus(): void
    {
        if ($this->loanApplication && method_exists($this->loanApplication, 'updateOverallStatusAfterTransaction')) {
            $this->loanApplication->updateOverallStatusAfterTransaction();
        }
    }

    public function isFullyClosedOrReturned(): bool
    {
        if (!$this->isIssue()) {
            return true;
        }

        $totalQuantityIssued = $this->loanTransactionItems()->sum('quantity_transacted');

        if ($totalQuantityIssued <= 0) {
            return true;
        }

        $returnTransactionIds = self::where('related_transaction_id', $this->id)
            ->where('type', self::TYPE_RETURN)
            ->pluck('id');

        $totalQuantityReturned = LoanTransactionItem::whereIn('loan_transaction_id', $returnTransactionIds)->sum('quantity_transacted');

        return $totalQuantityReturned >= $totalQuantityIssued;
    }
}
