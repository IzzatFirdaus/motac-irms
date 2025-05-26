<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Loan Application Model.
 * (PHPDoc from your provided file, confirmed and aligned with model properties)
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property int|null $responsible_officer_id User ID of the officer responsible, if not applicant
 * @property int|null $supporting_officer_id User ID of the officer supporting (e.g., HOD Gred 41+)
 * @property string $purpose
 * @property string $location Location where equipment will be used
 * @property string|null $return_location Location where equipment will be returned
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date Tarikh Dijangka Pulang
 * @property string $status From STATUSES_LABELS keys
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp For BAHAGIAN 4
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $approved_by User ID of final approver (final stage that makes it ready for issuance)
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by User ID of rejector
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by User ID of canceller
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property int|null $issued_by User ID of officer who issued items (from LoanTransaction) - This should be on Transaction.
 * @property \Illuminate\Support\Carbon|null $issued_at Timestamp of first issuance (from LoanTransaction) - This should be on Transaction.
 * @property int|null $returned_by User ID of officer who accepted final return (from LoanTransaction) - This should be on Transaction.
 * @property \Illuminate\Support\Carbon|null $returned_at Timestamp of final return (from LoanTransaction) - This should be on Transaction.
 * @property string|null $admin_notes Internal notes by admin/BPM
 * @property int|null $current_approval_officer_id For tracking current pending approver (if simple workflow)
 * @property string|null $current_approval_stage For tracking current pending approval stage (if simple workflow)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user Applicant
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $applicationItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items Alias for applicationItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read \App\Models\User|null $approvedByOfficer User who gave final approval stage leading to 'approved' status
 * @property-read \App\Models\User|null $rejectedByOfficer User who set 'rejected' status
 * @property-read \App\Models\User|null $cancelledByOfficer User who set 'cancelled' status
 * @property-read \App\Models\User|null $issuedByOfficer User who performed first issuance (via transactions)
 * @property-read \App\Models\User|null $returnedByOfficer User who processed final return (via transactions)
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $statusTranslated Accessor: status_translated
 */
class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_HOD_REVIEW = 'pending_hod_review';
    public const STATUS_PENDING_BPM_REVIEW = 'pending_bpm_review';
    public const STATUS_APPROVED = 'approved'; // Approved by all necessary parties, ready for issuance
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PARTIALLY_ISSUED = 'partially_issued';
    public const STATUS_ISSUED = 'issued'; // All approved items issued
    public const STATUS_RETURNED = 'returned'; // All issued items returned
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai',
        self::STATUS_PENDING_HOD_REVIEW => 'Menunggu Kelulusan Ketua Jabatan',
        self::STATUS_PENDING_BPM_REVIEW => 'Menunggu Semakan & Kelulusan BPM',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Pengeluaran)',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PARTIALLY_ISSUED => 'Sebahagian Peralatan Dikeluarkan',
        self::STATUS_ISSUED => 'Semua Peralatan Telah Dikeluarkan',
        self::STATUS_RETURNED => 'Semua Peralatan Telah Dipulangkan',
        self::STATUS_OVERDUE => 'Tertunggak Pemulangan',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'loan_applications';

    protected $fillable = [
        'user_id', 'responsible_officer_id', 'supporting_officer_id', 'purpose', 'location', 'return_location',
        'loan_start_date', 'loan_end_date', 'status', 'rejection_reason', 'applicant_confirmation_timestamp',
        'admin_notes', 'submitted_at',
        'approved_at', 'approved_by', // Officer who gave final approval to make it 'approved'
        'rejected_at', 'rejected_by',
        'cancelled_at', 'cancelled_by',
        // 'current_approval_officer_id', 'current_approval_stage', // These might be better managed via the Approval model relations
        // The fields issued_at, issued_by, returned_at, returned_by are derived from LoanTransactions, not direct columns here.
    ];

    protected $casts = [
        'loan_start_date' => 'datetime:Y-m-d H:i:s',
        'loan_end_date' => 'datetime:Y-m-d H:i:s',
        'applicant_confirmation_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        // 'issued_at' => 'datetime', // Derived
        // 'returned_at' => 'datetime', // Derived
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    protected static function newFactory(): LoanApplicationFactory
    {
        return LoanApplicationFactory::new();
    }

    // Relationships
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function responsibleOfficer(): BelongsTo { return $this->belongsTo(User::class, 'responsible_officer_id'); }
    public function supportingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'supporting_officer_id'); }
    public function applicationItems(): HasMany { return $this->hasMany(LoanApplicationItem::class, 'loan_application_id'); }
    public function items(): HasMany { return $this->applicationItems(); } // Alias
    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }

    // For specific status event officers
    public function approvedByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function cancelledByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }

    // Blameable
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    // Accessors to derive issued_at, issued_by, returned_at, returned_by from transactions
    public function getIssuedAtAttribute(): ?Carbon
    {
        return $this->loanTransactions()
            ->where('type', LoanTransaction::TYPE_ISSUE)
            ->where('status', LoanTransaction::STATUS_ISSUED) // or relevant completed status for issue
            ->min('transaction_date'); // Get the earliest issue transaction date
    }

    public function getIssuedByAttribute(): ?int // Returns User ID
    {
        $firstIssueTransaction = $this->loanTransactions()
            ->where('type', LoanTransaction::TYPE_ISSUE)
            ->where('status', LoanTransaction::STATUS_ISSUED)
            ->orderBy('transaction_date', 'asc')
            ->first();
        return $firstIssueTransaction?->issuing_officer_id;
    }
     public function issuedByOfficer(): BelongsTo // Relationship for issued_by logic
    {
        // This is more complex as issued_by is not a direct column.
        // You might define this to fetch the user based on the getIssuedByAttribute if needed frequently.
        // For now, direct attribute access or specific queries are simpler.
        // Placeholder:
        return $this->belongsTo(User::class, 'dummy_issued_by_id_should_be_dynamic'); // This is not a real FK
    }


    public function getReturnedAtAttribute(): ?Carbon
    {
        // Determine if all items are returned, then get latest return date
        if ($this->status === self::STATUS_RETURNED) {
            return $this->loanTransactions()
                ->where('type', LoanTransaction::TYPE_RETURN)
                ->whereIn('status', [LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED, LoanTransaction::STATUS_COMPLETED])
                ->max('transaction_date');
        }
        return null;
    }

    public function getReturnedByAttribute(): ?int // Returns User ID
    {
         if ($this->status === self::STATUS_RETURNED) {
            $lastReturnTransaction = $this->loanTransactions()
                ->where('type', LoanTransaction::TYPE_RETURN)
                ->whereIn('status', [LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED, LoanTransaction::STATUS_COMPLETED])
                ->orderBy('transaction_date', 'desc')
                ->first();
            return $lastReturnTransaction?->return_accepting_officer_id;
        }
        return null;
    }
     public function returnedByOfficer(): BelongsTo
    {
        // Placeholder for dynamic relation if needed
        return $this->belongsTo(User::class, 'dummy_returned_by_id_should_be_dynamic');
    }


    // Static helper methods
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getStatusesList(): array { return self::$STATUSES_LABELS; } // Alias for factory
    public static function getStatusKeys(): array { return array_keys(self::$STATUSES_LABELS); }

    // Business Logic Methods
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }

    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning("LoanApplication ID {$this->id}: Invalid status transition '{$newStatus}'.", ['acting_user_id' => $actingUserId]);
            return false;
        }
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $actingUserIdToSet = $actingUserId ?? Auth::id();

        switch ($newStatus) {
            case self::STATUS_PENDING_SUPPORT:
                if (!$this->submitted_at) $this->submitted_at = now();
                if (!$this->applicant_confirmation_timestamp) $this->applicant_confirmation_timestamp = $this->submitted_at ?? now();
                break;
            case self::STATUS_APPROVED:
                if (!$this->approved_at) $this->approved_at = now();
                if ($actingUserIdToSet) $this->approved_by = $actingUserIdToSet;
                break;
            case self::STATUS_REJECTED:
                if (!$this->rejected_at) $this->rejected_at = now();
                if ($actingUserIdToSet) $this->rejected_by = $actingUserIdToSet;
                if ($reason) $this->rejection_reason = $reason;
                break;
            case self::STATUS_CANCELLED:
                if (!$this->cancelled_at) $this->cancelled_at = now();
                if ($actingUserIdToSet) $this->cancelled_by = $actingUserIdToSet;
                break;
            // ISSUED, RETURNED statuses are typically set by updateOverallStatusAfterTransaction
        }
        $saved = $this->save();
        if ($saved) {
            Log::info("LoanApplication ID {$this->id} status transitioned from {$oldStatus} to {$newStatus}.", ['acting_user_id' => $actingUserId, 'reason' => $reason]);
        }
        return $saved;
    }

    public function updateOverallStatusAfterTransaction(): void
    {
        $this->loadMissing('applicationItems.equipment', 'loanTransactions.loanTransactionItems');

        $totalApprovedQty = 0;
        $totalIssuedQty = 0;
        $totalReturnedQty = 0;

        foreach ($this->applicationItems as $appItem) {
            $totalApprovedQty += $appItem->quantity_approved ?? $appItem->quantity_requested; // Fallback to requested if not explicitly approved
        }

        foreach($this->loanTransactions as $transaction) {
            if ($transaction->type === LoanTransaction::TYPE_ISSUE && in_array($transaction->status, [LoanTransaction::STATUS_ISSUED, LoanTransaction::STATUS_COMPLETED])) {
                $totalIssuedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            } elseif ($transaction->type === LoanTransaction::TYPE_RETURN && in_array($transaction->status, [LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED, LoanTransaction::STATUS_COMPLETED])) {
                $totalReturnedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            }
        }

        $currentStatus = $this->status;
        $newStatus = $currentStatus;

        // Do not change status if it's already in a final state like rejected or cancelled
        if (in_array($currentStatus, [self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_DRAFT])) {
            return;
        }

        if ($totalApprovedQty === 0 && $currentStatus !== self::STATUS_DRAFT) { // No items approved, but submitted
            // This case might imply an issue or lead to cancellation/rejection.
            // For now, keep status or let manual transition handle.
        } elseif ($totalIssuedQty > 0) {
            if ($totalReturnedQty >= $totalIssuedQty && $totalIssuedQty >= $totalApprovedQty) {
                $newStatus = self::STATUS_RETURNED; // All issued items (which were all approved items) are returned
            } elseif ($totalIssuedQty >= $totalApprovedQty) {
                $newStatus = self::STATUS_ISSUED; // All approved items are issued
            } elseif ($totalIssuedQty < $totalApprovedQty) {
                $newStatus = self::STATUS_PARTIALLY_ISSUED; // Some, but not all, approved items are issued
            }
        } elseif ($currentStatus === self::STATUS_APPROVED && $totalIssuedQty === 0) {
            // Stays approved if nothing is issued yet
            $newStatus = self::STATUS_APPROVED;
        }

        // Overdue check (applies if not yet fully returned)
        if ($newStatus !== self::STATUS_RETURNED && $newStatus !== self::STATUS_DRAFT && now()->gt($this->loan_end_date) && $totalIssuedQty > $totalReturnedQty) {
            $newStatus = self::STATUS_OVERDUE;
        }


        if ($newStatus !== $currentStatus) {
            $this->status = $newStatus;
            Log::info("LoanApplication ID {$this->id} status auto-updated after transaction.", ['old_status' => $currentStatus, 'new_status' => $newStatus, 'approved' => $totalApprovedQty, 'issued' => $totalIssuedQty, 'returned' => $totalReturnedQty]);
            $this->save();
        }
    }
}
