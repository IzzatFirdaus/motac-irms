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

/**
 * LoanTransaction Model.
 *
 * Represents an equipment issue or return record for a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property array|string|null $accessories_checklist_on_issue
 * @property int|null $issuing_officer_id
 * @property int|null $receiving_officer_id
 * @property array|null $accessories_checklist_on_issue
 * @property string|null $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp
 * @property int|null $returning_officer_id
 * @property int|null $return_accepting_officer_id
 * @property array|null $accessories_checklist_on_return
 * @property string|null $return_notes
 * @property \Illuminate\Support\Carbon|null $return_timestamp
 * @property int|null $related_transaction_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \App\Models\LoanApplication|null $loanApplication
 */
class LoanTransaction extends Model
{
    use Blameable, HasFactory, SoftDeletes;

    // Transaction types
    public const TYPE_ISSUE = 'issue';
    public const TYPE_RETURN = 'return';

    // Status constants (must match migration enum)
    public const STATUS_PENDING = 'pending';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection';
    public const STATUS_RETURNED_GOOD = 'returned_good';
    public const STATUS_RETURNED_DAMAGED = 'returned_damaged';
    public const STATUS_ITEMS_REPORTED_LOST = 'items_reported_lost';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_PARTIALLY_RETURNED = 'partially_returned';
    public const STATUS_RETURNED_WITH_LOSS = 'returned_with_loss';
    public const STATUS_RETURNED_WITH_DAMAGE_AND_LOSS = 'returned_with_damage_and_loss';

    protected $table = 'loan_transactions';

    protected $fillable = [
        'loan_application_id',
        'type',
        'transaction_date',
        'issuing_officer_id',
        'receiving_officer_id',
        'accessories_checklist_on_issue',
        'issue_notes',
        'issue_timestamp',
        'returning_officer_id',
        'return_accepting_officer_id',
        'accessories_checklist_on_return',
        'return_notes',
        'return_timestamp',
        'related_transaction_id',
        'due_date',
        'status'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'issue_timestamp' => 'datetime',
        'return_timestamp' => 'datetime',
        'due_date' => 'date',
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

    // Relationships

    /**
     * The parent loan application for this transaction.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * The items (equipment) transacted in this transaction.
     */
    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'loan_transaction_id');
    }

    /**
     * Alias for loanTransactionItems relationship.
     */
    public function items(): HasMany
    {
        return $this->loanTransactionItems();
    }

    /**
     * The officer who issued the equipment (if applicable).
     */
    public function issuingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuing_officer_id');
    }

    /**
     * The officer (user) who received the equipment (applicant).
     */
    public function receivingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiving_officer_id');
    }

    /**
     * The officer who returned the equipment (applicant).
     */
    public function returningOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returning_officer_id');
    }

    /**
     * The officer who accepted the returned equipment.
     */
    public function returnAcceptingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_accepting_officer_id');
    }

    /**
     * The related issue transaction for which this is a return.
     */
    public function relatedIssueTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'related_transaction_id');
    }

    // Accessors

    /**
     * Get a human-readable label for the transaction type.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? Str::title(str_replace('_', ' ', $this->type));
    }

    /**
     * Get a human-readable label for the transaction status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * Get a Bootstrap color class for the transaction type (for badge display).
     */
    public function getTypeColorClassAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ISSUE => 'text-bg-info',
            self::TYPE_RETURN => 'text-bg-primary',
            default => 'text-bg-secondary',
        };
    }

    /**
     * Get a Bootstrap color class for the transaction status (for badge display).
     */
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ISSUED => 'text-bg-info',
            self::STATUS_RETURNED, self::STATUS_RETURNED_GOOD, self::STATUS_COMPLETED => 'text-bg-success',
            self::STATUS_RETURNED_PENDING_INSPECTION, self::STATUS_PARTIALLY_RETURNED => 'text-bg-primary',
            self::STATUS_RETURNED_DAMAGED, self::STATUS_OVERDUE => 'text-bg-warning',
            self::STATUS_ITEMS_REPORTED_LOST, self::STATUS_CANCELLED => 'text-bg-danger',
            self::STATUS_RETURNED_WITH_LOSS, self::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    /**
     * Get the primary item name for this transaction (for display purposes).
     */
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

    /**
     * Get total quantity transacted in this transaction.
     */
    public function getQuantityAttribute(): int
    {
        if ($this->relationLoaded('loanTransactionItems')) {
            return (int) $this->loanTransactionItems->sum('quantity_transacted');
        }
        return (int) $this->loanTransactionItems()->sum('quantity_transacted');
    }

    // Static helper methods

    /**
     * Returns the allowed transaction types for use in factories/seeders. (RAW values for enum)
     * Use this in factories/seeder logic where you need the list of allowed 'type' values.
     */
    public static function getTypesOptions(): array
    {
        return [
            self::TYPE_ISSUE,
            self::TYPE_RETURN
        ];
    }

    /**
     * Transaction type options for dropdowns.
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_ISSUE => __('reports.filters.type_issue'),
            self::TYPE_RETURN => __('reports.filters.type_return'),
        ];
    }

    /**
     * Transaction status options for dropdowns.
     * This covers all statuses from the migration's enum.
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
            self::STATUS_RETURNED => __('common.statuses.returned'),
            self::STATUS_PARTIALLY_RETURNED => __('common.statuses.partially_returned'),
            self::STATUS_RETURNED_WITH_LOSS => __('common.statuses.returned_with_loss'),
            self::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => __('common.statuses.returned_with_damage_and_loss'),
        ];
    }

    /**
     * List of all valid statuses as per migration enum (for validation/filtering).
     */
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
            self::STATUS_OVERDUE,
            self::STATUS_RETURNED,
            self::STATUS_PARTIALLY_RETURNED,
            self::STATUS_RETURNED_WITH_LOSS,
            self::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS,
        ];
    }

    /**
     * Returns the default relations to be loaded for this model.
     */
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

    // Helper methods

    /**
     * Returns true if this transaction is an issue transaction.
     */
    public function isIssue(): bool
    {
        return $this->type === self::TYPE_ISSUE;
    }

    /**
     * Returns true if this transaction is a return transaction.
     */
    public function isReturn(): bool
    {
        return $this->type === self::TYPE_RETURN;
    }

    /**
     * Updates the parent LoanApplication's status after this transaction.
     * Used to keep application status in sync with transactions.
     */
    public function updateParentLoanApplicationStatus(): void
    {
        // Call the update method on the parent loan application if available.
        if ($this->loanApplication && method_exists($this->loanApplication, 'updateOverallStatusAfterTransaction')) {
            $this->loanApplication->updateOverallStatusAfterTransaction();
        }
    }

    /**
     * Returns true if this transaction is fully closed/returned.
     * Used for reporting and workflow checks.
     */
    public function isFullyClosedOrReturned(): bool
    {
        // Checks if this transaction (issue type) has been fully returned/closed
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
