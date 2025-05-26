<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Loan Transaction Model.
 * (PHPDoc from your provided file, updated for timestamp fields)
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $items Alias
 * @property-read \App\Models\User|null $issuingOfficer
 * @property-read \App\Models\User|null $receivingOfficer
 * @property-read \App\Models\User|null $returningOfficer
 * @property-read \App\Models\User|null $returnAcceptingOfficer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read LoanTransaction|null $relatedIssueTransaction
 * @property-read string $typeTranslated Accessor: type_translated
 * @property-read string $statusTranslated Accessor: status_translated
 */
class LoanTransaction extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_ISSUE = 'issue';
    public const TYPE_RETURN = 'return';

    public static array $TYPES_LABELS = [
        self::TYPE_ISSUE => 'Pengeluaran',
        self::TYPE_RETURN => 'Pemulangan',
    ];

    // Statuses for a transaction itself
    public const STATUS_PENDING = 'pending'; // Transaction initiated but not completed (e.g. items being gathered for issue)
    public const STATUS_ISSUED = 'issued'; // Issue transaction completed, items handed over
    public const STATUS_RETURNED_PENDING_INSPECTION = 'returned_pending_inspection'; // Return transaction, items received but pending check
    public const STATUS_RETURNED_GOOD = 'returned_good'; // Return transaction completed, items good
    public const STATUS_RETURNED_DAMAGED = 'returned_damaged'; // Return transaction completed, items damaged
    public const STATUS_ITEMS_REPORTED_LOST = 'items_reported_lost'; // Return transaction where items were confirmed lost
    public const STATUS_COMPLETED = 'completed'; // Generic completion state, can be used if more specific states are not suitable
    public const STATUS_CANCELLED = 'cancelled'; // Transaction was voided/cancelled

    public static array $STATUSES_LABELS = [
        self::STATUS_PENDING => 'Menunggu Tindakan',
        self::STATUS_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_RETURNED_PENDING_INSPECTION => 'Pemulangan (Menunggu Semakan)',
        self::STATUS_RETURNED_GOOD => 'Dipulangkan (Keadaan Baik)',
        self::STATUS_RETURNED_DAMAGED => 'Dipulangkan (Ada Kerosakan)',
        self::STATUS_ITEMS_REPORTED_LOST => 'Item Dilaporkan Hilang',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'loan_transactions';

    protected $fillable = [
        'loan_application_id', 'type', 'transaction_date',
        'issuing_officer_id', 'receiving_officer_id',
        'accessories_checklist_on_issue', 'issue_notes', 'issue_timestamp', // Re-added issue_timestamp
        'returning_officer_id', 'return_accepting_officer_id',
        'accessories_checklist_on_return', 'return_notes', 'return_timestamp', // Re-added return_timestamp
        'related_transaction_id', 'status',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'issue_timestamp' => 'datetime', // Re-added cast
        'return_timestamp' => 'datetime', // Re-added cast
        'accessories_checklist_on_issue' => 'array',
        'accessories_checklist_on_return' => 'array',
    ];

    protected $attributes = [
        // Default status might be set by the service creating the transaction
    ];

    protected static function newFactory(): LoanTransactionFactory
    {
        return LoanTransactionFactory::new();
    }

    // Relationships
    public function loanApplication(): BelongsTo { return $this->belongsTo(LoanApplication::class, 'loan_application_id'); }
    public function loanTransactionItems(): HasMany { return $this->hasMany(LoanTransactionItem::class, 'loan_transaction_id'); }
    public function items(): HasMany { return $this->loanTransactionItems(); } // Alias

    public function issuingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'issuing_officer_id'); }
    public function receivingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'receiving_officer_id'); }
    public function returningOfficer(): BelongsTo { return $this->belongsTo(User::class, 'returning_officer_id'); }
    public function returnAcceptingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'return_accepting_officer_id'); }

    public function relatedIssueTransaction(): BelongsTo { return $this->belongsTo(LoanTransaction::class, 'related_transaction_id'); }

    // Blameable
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getTypeTranslatedAttribute(): string
    {
        return self::$TYPES_LABELS[$this->type] ?? Str::title(str_replace('_', ' ', (string) $this->type));
    }

    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    // Static helpers
    public static function getTypesOptions(): array { return self::$TYPES_LABELS; }
    public static function getTypesList(): array { return self::$TYPES_LABELS; } // For factory
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getStatusesList(): array { return self::$STATUSES_LABELS; } // For factory

    /**
     * Logic to update LoanApplication status after this transaction is saved/updated.
     * This should be called from an observer (e.g., LoanTransactionObserver).
     */
    public function updateParentLoanApplicationStatus(): void
    {
        if ($this->loanApplication) {
            $this->loanApplication->updateOverallStatusAfterTransaction();
        }
    }
}
