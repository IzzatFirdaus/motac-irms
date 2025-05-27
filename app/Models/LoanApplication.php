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
 * @property int|null $approved_by User ID of final approver
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by User ID of rejector
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by User ID of canceller
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes Internal notes by admin/BPM
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
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $statusTranslated Accessor: status_translated
 * @property-read \Illuminate\Support\Carbon|null $issued_at Accessor for derived data
 * @property-read int|null $issued_by Accessor for derived data
 * @property-read \Illuminate\Support\Carbon|null $returned_at Accessor for derived data
 * @property-read int|null $returned_by Accessor for derived data
 */
class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants as defined in system design document and model
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    public const STATUS_PENDING_HOD_REVIEW = 'pending_hod_review';
    public const STATUS_PENDING_BPM_REVIEW = 'pending_bpm_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PARTIALLY_ISSUED = 'partially_issued';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED = 'returned';
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
        'approved_at', 'approved_by',
        'rejected_at', 'rejected_by',
        'cancelled_at', 'cancelled_by',
    ];

    protected $casts = [
        'loan_start_date' => 'datetime:Y-m-d H:i:s',
        'loan_end_date' => 'datetime:Y-m-d H:i:s',
        'applicant_confirmation_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
    public function items(): HasMany { return $this->applicationItems(); }
    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }
    public function approvedByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function cancelledByOfficer(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    public function getIssuedAtAttribute(): ?Carbon { /* ... as in provided model ... */ return null; } // Placeholder
    public function getIssuedByAttribute(): ?int { /* ... as in provided model ... */ return null; } // Placeholder
    public function getReturnedAtAttribute(): ?Carbon { /* ... as in provided model ... */ return null; } // Placeholder
    public function getReturnedByAttribute(): ?int { /* ... as in provided model ... */ return null; } // Placeholder


    // Static helper methods
    public static function getStatusOptions(): array { return self::$STATUSES_LABELS; }
    public static function getStatusesList(): array { return self::$STATUSES_LABELS; }
    public static function getStatusKeys(): array { return array_keys(self::$STATUSES_LABELS); }

    // Business Logic Methods
    /**
     * Checks if the application is in draft status.
     * Required by LoanApplicationPolicy.
     * @return bool
     */
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }

    /**
     * Checks if the application is fully approved and ready for issuance.
     * Required by LoanTransactionPolicy.
     * @return bool
     */
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }

    /**
     * Checks if the application has been partially issued.
     * Required by LoanTransactionPolicy.
     * @return bool
     */
    public function isPartiallyIssued(): bool { return $this->status === self::STATUS_PARTIALLY_ISSUED; }


    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        // ... implementation from provided model ...
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
        }
        $saved = $this->save();
        if ($saved) {
            Log::info("LoanApplication ID {$this->id} status transitioned from {$oldStatus} to {$newStatus}.", ['acting_user_id' => $actingUserId, 'reason' => $reason]);
        }
        return $saved;
    }

    public function updateOverallStatusAfterTransaction(): void
    {
        // ... implementation from provided model ...
        $this->loadMissing('applicationItems.equipment', 'loanTransactions.loanTransactionItems');

        $totalApprovedQty = 0;
        $totalIssuedQty = 0;
        $totalReturnedQty = 0;

        foreach ($this->applicationItems as $appItem) {
            $totalApprovedQty += $appItem->quantity_approved ?? $appItem->quantity_requested;
        }

        foreach($this->loanTransactions as $transaction) {
            if ($transaction->type === LoanTransaction::TYPE_ISSUE && in_array($transaction->status, [LoanTransaction::STATUS_ISSUED, LoanTransaction::STATUS_COMPLETED])) { // Assuming LoanTransaction constants
                $totalIssuedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            } elseif ($transaction->type === LoanTransaction::TYPE_RETURN && in_array($transaction->status, [LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED, LoanTransaction::STATUS_COMPLETED])) { // Assuming LoanTransaction constants
                $totalReturnedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            }
        }

        $currentStatus = $this->status;
        $newStatus = $currentStatus;

        if (in_array($currentStatus, [self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_DRAFT])) {
            return;
        }

        if ($totalApprovedQty > 0 && $totalIssuedQty > 0) {
            if ($totalReturnedQty >= $totalIssuedQty && $totalIssuedQty >= $totalApprovedQty) {
                $newStatus = self::STATUS_RETURNED;
            } elseif ($totalIssuedQty >= $totalApprovedQty) {
                $newStatus = self::STATUS_ISSUED;
            } else { // $totalIssuedQty < $totalApprovedQty
                $newStatus = self::STATUS_PARTIALLY_ISSUED;
            }
        } elseif ($currentStatus === self::STATUS_APPROVED && $totalIssuedQty === 0) {
            $newStatus = self::STATUS_APPROVED;
        }


        if ($newStatus !== self::STATUS_RETURNED && $newStatus !== self::STATUS_DRAFT && $this->loan_end_date && now()->gt($this->loan_end_date) && $totalIssuedQty > $totalReturnedQty) {
            $newStatus = self::STATUS_OVERDUE;
        }


        if ($newStatus !== $currentStatus) {
            $this->status = $newStatus;
            Log::info("LoanApplication ID {$this->id} status auto-updated after transaction.", ['old_status' => $currentStatus, 'new_status' => $newStatus, 'approved' => $totalApprovedQty, 'issued' => $totalIssuedQty, 'returned' => $totalReturnedQty]);
            $this->save();
        }
    }
}
