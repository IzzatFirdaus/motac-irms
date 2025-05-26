<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Loan Transaction Model.
 * (PHPDoc from your version in turn 30)
 * @property int $id
 * @property int $loan_application_id
 * @property string $type Enum: 'issue', 'return'
 * @property \Illuminate\Support\Carbon $transaction_date Default: now
 * @property int|null $issuing_officer_id Pegawai Pengeluar (BPM Staff)
 * @property int|null $receiving_officer_id Pegawai Penerima (Applicant/Delegate)
 * @property array|null $accessories_checklist_on_issue JSON from form
 * @property string|null $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp (Added from previous file if distinct from transaction_date)
 * @property \Illuminate\Support\Carbon|null $return_timestamp (transaction_date for return type)
 * @property int|null $returning_officer_id Pegawai Yang Memulangkan
 * @property int|null $return_accepting_officer_id Pegawai Terima Pulangan (BPM Staff)
 * @property array|null $accessories_checklist_on_return JSON from form
 * @property string|null $return_notes Catatan semasa pemulangan
 * @property int|null $related_transaction_id For linking return to issue
 * @property string $status Enum from STATUSES list
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \App\Models\User|null $issuingOfficer
 * @property-read \App\Models\User|null $receivingOfficer
 * @property-read \App\Models\User|null $returningOfficer
 * @property-read \App\Models\User|null $returnAcceptingOfficer
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property-read LoanTransaction|null $relatedIssueTransaction
 * @property-read string $type_translated
 * @property-read string $status_translated
 */
class LoanTransaction extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_ISSUE = 'issue';
    public const TYPE_RETURN = 'return';

    public static array $TYPES_LABELS = [ // Renamed from TYPES_LIST for consistency
        self::TYPE_ISSUE => 'Pengeluaran',
        self::TYPE_RETURN => 'Pemulangan',
    ];

    // Status Constants from System Design 4.3 & your model
    public const STATUS_PENDING_ISSUANCE = 'pending_issuance'; // If issuance isn't immediate
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED_PENDING_CHECK = 'returned_pending_check'; // If returns need verification
    public const STATUS_RETURNED_GOOD = 'returned_good';
    public const STATUS_RETURNED_DAMAGED = 'returned_damaged';
    // public const STATUS_PARTIALLY_RETURNED = 'partially_returned'; // This usually applies to LoanApplication overall status, not a single transaction.
    public const STATUS_REPORTED_LOST = 'reported_lost'; // If items in *this transaction* were noted as lost during return processing.
    public const STATUS_COMPLETED = 'completed'; // General completion for a transaction.
    public const STATUS_CANCELLED = 'cancelled';

    public static array $STATUSES_LABELS = [ // Renamed from STATUSES_LIST
        self::STATUS_PENDING_ISSUANCE => 'Menunggu Pengeluaran',
        self::STATUS_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_RETURNED_PENDING_CHECK => 'Menunggu Semakan Pemulangan',
        self::STATUS_RETURNED_GOOD => 'Dipulangkan (Baik)',
        self::STATUS_RETURNED_DAMAGED => 'Dipulangkan (Rosak)',
        // self::STATUS_PARTIALLY_RETURNED => 'Dipulangkan Sebahagian',
        self::STATUS_REPORTED_LOST => 'Dilapor Hilang (Semasa Transaksi Ini)',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'loan_transactions';

    protected $fillable = [
        'loan_application_id', 'type', 'transaction_date',
        'issuing_officer_id', 'receiving_officer_id',
        'accessories_checklist_on_issue', 'issue_notes', 'issue_timestamp', // Added issue_timestamp
        // return_timestamp is not distinct, transaction_date for 'return' type transactions will be the return time
        'returning_officer_id', 'return_accepting_officer_id',
        'accessories_checklist_on_return', 'return_notes',
        'related_transaction_id', 'status',
        // created_by, updated_by handled by BlameableObserver
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'accessories_checklist_on_issue' => 'array',
        'issue_timestamp' => 'datetime',
        // 'return_timestamp' => 'datetime', // Use transaction_date for return type
        'accessories_checklist_on_return' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_COMPLETED, // Default a transaction to completed once items are processed for it
    ];


    protected static function newFactory(): LoanTransactionFactory
    {
        return LoanTransactionFactory::new();
    }

    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'loan_transaction_id');
    }
    public function items(): HasMany // Alias
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

    public function relatedIssueTransaction(): BelongsTo // For return transactions
    {
        return $this->belongsTo(LoanTransaction::class, 'related_transaction_id');
    }

    public function creatorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updaterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleterInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getTypeTranslatedAttribute(): string
    {
        return self::$TYPES_LABELS[$this->type] ?? Str::title(str_replace('_', ' ', $this->type));
    }

    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    public static function getTypes(): array { return array_keys(self::$TYPES_LABELS); }
    public static function getStatuses(): array { return array_keys(self::$STATUSES_LABELS); }
}
