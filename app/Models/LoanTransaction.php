<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Loan Transaction Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $type Enum: 'issue', 'return'
 * @property \Illuminate\Support\Carbon $transaction_date General date of the transaction event
 * @property int|null $issuing_officer_id Pegawai Pengeluar (BPM Staff)
 * @property int|null $receiving_officer_id Pegawai Penerima (Applicant/Delegate)
 * @property array|null $accessories_checklist_on_issue JSON
 * @property string|null $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp Actual moment of physical issuance
 * @property int|null $returning_officer_id Pegawai Yang Memulangkan
 * @property int|null $return_accepting_officer_id Pegawai Terima Pulangan (BPM Staff)
 * @property array|null $accessories_checklist_on_return JSON
 * @property string|null $return_notes Catatan semasa pemulangan
 * @property \Illuminate\Support\Carbon|null $return_timestamp Actual moment of physical return
 * @property int|null $related_transaction_id For linking return to issue
 * @property string $status Enum from STATUSES_LABELS keys
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $items Alias for loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $issuingOfficer
 * @property-read \App\Models\User|null $receivingOfficer
 * @property-read \App\Models\User|null $returningOfficer
 * @property-read \App\Models\User|null $returnAcceptingOfficer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read LoanTransaction|null $relatedIssueTransaction If this is a return transaction, this points to the original issue transaction
 * @property-read string $type_label Translated type
 * @property-read string $status_label Translated status
 * @property-read string $item_name Representative item name for the transaction (accessor)
 * @property-read int $quantity Total quantity of items in the transaction (accessor)
 *
 * @method static LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction withoutTrashed()
 *
 * @mixin \Eloquent
 */
class LoanTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_ISSUE = 'issue';

    public const TYPE_RETURN = 'return';

    // Statuses for a transaction itself as per System Design (Rev 3)
    public const STATUS_PENDING = 'pending';

    public const STATUS_ISSUED = 'issued';

    public const STATUS_RETURNED = 'returned'; // Added general returned status

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
        self::STATUS_RETURNED => 'Telah Dipulangkan (Umum)', // Added label
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

    protected $fillable = [
        'loan_application_id', 'type', 'transaction_date',
        'issuing_officer_id', 'receiving_officer_id',
        'accessories_checklist_on_issue', 'issue_notes', 'issue_timestamp',
        'returning_officer_id', 'return_accepting_officer_id',
        'accessories_checklist_on_return', 'return_notes', 'return_timestamp',
        'related_transaction_id', 'status',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'issue_timestamp' => 'datetime',
        'return_timestamp' => 'datetime',
        'accessories_checklist_on_issue' => 'array',
        'accessories_checklist_on_return' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    // ADDED: Append new accessors to array/JSON forms if needed, optional.
    // protected $appends = ['item_name', 'quantity', 'status_label', 'type_label'];

    protected static function newFactory(): LoanTransactionFactory
    {
        return LoanTransactionFactory::new();
    }

    // Relationships
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
    public function getTypeLabelAttribute(): string
    {
        return self::getTypeLabel($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabel($this->status);
    }

    /**
     * Get a representative item name for the transaction.
     * For simplicity, returns the name of the first equipment item.
     * If multiple distinct items, it might show "Multiple Items" or similar.
     */
    public function getItemNameAttribute(): string
    {
        if ($this->relationLoaded('loanTransactionItems') && $this->loanTransactionItems->isNotEmpty()) {
            $firstItem = $this->loanTransactionItems->first();
            if ($firstItem && $firstItem->relationLoaded('equipment') && $firstItem->equipment) {
                // Assuming Equipment model has a 'name' or 'model' attribute
                return $firstItem->equipment->name ?? $firstItem->equipment->model ?? __('Item Peralatan');
            }

            return __('Item Tidak Diketahui');
        } elseif ($this->loanTransactionItems()->exists()) { // Fallback if not eager loaded, but less efficient
            $firstItem = $this->loanTransactionItems()->with('equipment')->first();
            if ($firstItem && $firstItem->equipment) {
                return $firstItem->equipment->name ?? $firstItem->equipment->model ?? __('Item Peralatan');
            }

            return __('Item Tidak Diketahui');
        }

        return __('Tiada Item');
    }

    /**
     * Get the total quantity of items transacted in this loan transaction.
     */
    public function getQuantityAttribute(): int
    {
        // Ensure loanTransactionItems relationship is loaded to avoid N+1 if called in a loop
        if ($this->relationLoaded('loanTransactionItems')) {
            return (int) $this->loanTransactionItems->sum('quantity_transacted');
        }

        // Fallback if not eager loaded, less efficient
        return (int) $this->loanTransactionItems()->sum('quantity_transacted');
    }

    // Static helpers for labels and options
    public static function getTypeLabel(string $typeKey): string
    {
        return __(self::$TYPES_LABELS[$typeKey] ?? Str::title(str_replace('_', ' ', $typeKey)));
    }

    public static function getStatusLabel(string $statusKey): string
    {
        return __(self::$STATUSES_LABELS[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)));
    }

    public static function getTypesOptions(): array
    {
        return self::$TYPES_LABELS;
    }

    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }

    public static function getStatusesList(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    public function isIssue(): bool
    {
        return $this->type === self::TYPE_ISSUE;
    }

    public function isReturn(): bool
    {
        return $this->type === self::TYPE_RETURN;
    }

    public function isFullyClosedOrReturned(): bool
    {
        return in_array($this->status, [
            self::STATUS_RETURNED, // Added general returned
            self::STATUS_RETURNED_GOOD,
            self::STATUS_RETURNED_DAMAGED,
            self::STATUS_ITEMS_REPORTED_LOST,
            self::STATUS_RETURNED_WITH_LOSS,
            self::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ]);
    }

    public static function getDefinedDefaultRelationsStatic(): array
    {
        return [
            'loanApplication.user', 'loanTransactionItems.equipment', 'issuingOfficer',
            'receivingOfficer', 'returningOfficer', 'returnAcceptingOfficer', 'relatedIssueTransaction',
        ];
    }

    public function updateParentLoanApplicationStatus(): void
    {
        if ($this->loanApplication) {
            Log::info("Triggering updateOverallStatusAfterTransaction for LoanApplication ID {$this->loanApplication->id} from LoanTransaction ID {$this->id}");
            if (method_exists($this->loanApplication, 'updateOverallStatusAfterTransaction')) {
                $this->loanApplication->updateOverallStatusAfterTransaction();
            } else {
                Log::warning('Method updateOverallStatusAfterTransaction not found on LoanApplication model.');
            }
        }
    }
}
