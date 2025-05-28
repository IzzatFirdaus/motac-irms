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
 * System Design Reference: Section 4.3 ICT Equipment Loan Modules
 * @property int $id
 * @property int $user_id (Applicant User ID)
 * @property int|null $responsible_officer_id (User ID of the officer responsible, if not applicant)
 * @property int|null $supporting_officer_id (User ID of the officer supporting - e.g., HOD Gred 41+)
 * @property string $purpose (Tujuan Permohonan from form)
 * @property string $location (Lokasi* (Usage) from form)
 * @property string|null $return_location (Optional return location if different)
 * @property \Illuminate\Support\Carbon $loan_start_date (Tarikh Pinjaman* from form)
 * @property \Illuminate\Support\Carbon $loan_end_date (Tarikh Dijangka Pulang* from form)
 * @property string $status (From STATUS_CONSTANTS)
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp (For BAHAGIAN 4 - Applicant Confirmation)
 * @property \Illuminate\Support\Carbon|null $submitted_at (Timestamp when user formally submits the draft)
 * @property int|null $approved_by (User ID of final approver)
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by (User ID of rejector)
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by (User ID of canceller)
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes (Internal notes by admin/BPM staff)
 * @property string|null $current_approval_officer_id (Tracks who the approval task is currently with)
 * @property string|null $current_approval_stage (Tracks current approval stage name)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User $user (Applicant)
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $applicationItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items (Alias for applicationItems)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read \App\Models\User|null $approvedByOfficer (User who gave final approval)
 * @property-read \App\Models\User|null $rejectedByOfficer
 * @property-read \App\Models\User|null $cancelledByOfficer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated (Accessor)
 */
class LoanApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Status constants as defined in System Design (Section 4.3)
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support'; // After user submits draft, awaiting Supporting Officer (Gred 41+)
    public const STATUS_PENDING_HOD_REVIEW = 'pending_hod_review'; // If HOD review is a distinct step
    public const STATUS_PENDING_BPM_REVIEW = 'pending_bpm_review'; // After support/HOD, awaiting BPM (IT Equipment staff)
    public const STATUS_APPROVED = 'approved'; // Approved by BPM, ready for issuance
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PARTIALLY_ISSUED = 'partially_issued';
    public const STATUS_ISSUED = 'issued'; // All approved items issued
    public const STATUS_RETURNED = 'returned'; // All issued items returned
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled'; // User cancels draft, or admin cancels approved but unissued

    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai (Gred 41+)',
        self::STATUS_PENDING_HOD_REVIEW => 'Menunggu Kelulusan Ketua Jabatan/Unit',
        self::STATUS_PENDING_BPM_REVIEW => 'Menunggu Semakan & Kelulusan BPM',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Pengeluaran)',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PARTIALLY_ISSUED => 'Sebahagian Peralatan Telah Dikeluarkan',
        self::STATUS_ISSUED => 'Semua Peralatan Telah Dikeluarkan',
        self::STATUS_RETURNED => 'Semua Peralatan Telah Dipulangkan',
        self::STATUS_OVERDUE => 'Tertunggak Pemulangan Peralatan',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'loan_applications';

    protected $fillable = [
        'user_id', 'responsible_officer_id', 'supporting_officer_id',
        'purpose', 'location', 'return_location',
        'loan_start_date', 'loan_end_date',
        'status', 'rejection_reason',
        'applicant_confirmation_timestamp', 'submitted_at',
        'approved_by', 'approved_at',
        'rejected_by', 'rejected_at',
        'cancelled_by', 'cancelled_at',
        'admin_notes',
        'current_approval_officer_id', 'current_approval_stage',
        // created_by, updated_by, deleted_by are handled by BlameableObserver
    ];

    protected $casts = [
        'loan_start_date' => 'datetime:Y-m-d H:i:s',
        'loan_end_date' => 'datetime:Y-m-d H:i:s',
        'applicant_confirmation_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [ // Default values for new instances
        'status' => self::STATUS_DRAFT,
    ];

    // Placeholder accessors for derived data based on transactions (implement if needed)
    // public function getIssuedAtAttribute(): ?Carbon { /* Logic based on first 'issue' transaction */ return null; }
    // public function getIssuedByAttribute(): ?int { /* Logic based on first 'issue' transaction */ return null; }
    // public function getReturnedAtAttribute(): ?Carbon { /* Logic based on last 'return' transaction completing all items */ return null; }
    // public function getReturnedByAttribute(): ?int { /* Logic based on last 'return' transaction */ return null; }

    // Static helper methods
    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }
    public static function getStatusKeys(): array
    {
        return array_keys(self::$STATUSES_LABELS);
    }

    protected static function newFactory(): LoanApplicationFactory
    {
        return LoanApplicationFactory::new();
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function responsibleOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_officer_id');
    }
    public function supportingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supporting_officer_id');
    }
    public function currentApprovalOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approval_officer_id');
    }
    public function applicationItems(): HasMany
    {
        return $this->hasMany(LoanApplicationItem::class, 'loan_application_id');
    }
    public function items(): HasMany
    {
        return $this->applicationItems();
    } // Alias
    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class, 'loan_application_id');
    }
    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
    public function approvedByOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function rejectedByOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    public function cancelledByOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
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
    public function getStatusTranslatedAttribute(): string
    {
        return __(self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status)));
    }

    // Business Logic Methods
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    } // Approved and ready for issuance
    public function isPartiallyIssued(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_ISSUED;
    }
    public function isFullyIssued(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }
    public function isPendingApproval(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_SUPPORT,
            self::STATUS_PENDING_HOD_REVIEW,
            self::STATUS_PENDING_BPM_REVIEW
        ]);
    }


    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!array_key_exists($newStatus, self::$STATUSES_LABELS)) {
            Log::warning("LoanApplication ID {$this->id}: Invalid status transition '{$newStatus}'.", [
                'acting_user_id' => $actingUserId ?? Auth::id()
            ]);
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $actingUserIdToSet = $actingUserId ?? Auth::id(); // Get acting user if not provided

        switch ($newStatus) {
            case self::STATUS_PENDING_SUPPORT:
                if (!$this->submitted_at) {
                    $this->submitted_at = now();
                }
                // Applicant confirmation timestamp should be set when user submits from draft
                if (!$this->applicant_confirmation_timestamp) {
                    $this->applicant_confirmation_timestamp = $this->submitted_at ?? now();
                }
                break;
            case self::STATUS_APPROVED:
                if (!$this->approved_at) {
                    $this->approved_at = now();
                }
                if ($actingUserIdToSet) {
                    $this->approved_by = $actingUserIdToSet;
                }
                break;
            case self::STATUS_REJECTED:
                if (!$this->rejected_at) {
                    $this->rejected_at = now();
                }
                if ($actingUserIdToSet) {
                    $this->rejected_by = $actingUserIdToSet;
                }
                if ($reason) {
                    $this->rejection_reason = $reason;
                }
                break;
            case self::STATUS_CANCELLED:
                if (!$this->cancelled_at) {
                    $this->cancelled_at = now();
                }
                if ($actingUserIdToSet) {
                    $this->cancelled_by = $actingUserIdToSet;
                }
                break;
        }
        $saved = $this->save();
        if ($saved) {
            Log::info("LoanApplication ID {$this->id} status transitioned from '{$oldStatus}' to '{$newStatus}'.", [
                'acting_user_id' => $actingUserIdToSet, 'reason' => $reason
            ]);
            // Dispatch an event for notifications
            // event(new \App\Events\LoanApplicationStatusChanged($this, $oldStatus, $actingUserIdToSet));
        } else {
            Log::error("LoanApplication ID {$this->id}: Failed to save status transition from '{$oldStatus}' to '{$newStatus}'.", [
               'acting_user_id' => $actingUserIdToSet,
            ]);
        }
        return $saved;
    }

    public function updateOverallStatusAfterTransaction(): void
    {
        // Load necessary relationships if not already loaded
        $this->loadMissing(['applicationItems', 'loanTransactions.loanTransactionItems']);

        $totalApprovedQty = 0;
        $totalIssuedQty = 0;
        $totalReturnedQty = 0;

        foreach ($this->applicationItems as $appItem) {
            $totalApprovedQty += $appItem->quantity_approved ?? $appItem->quantity_requested; // Fallback to requested if not explicitly approved quantity
        }

        foreach ($this->loanTransactions as $transaction) {
            if ($transaction->type === LoanTransaction::TYPE_ISSUE &&
                in_array($transaction->status, [LoanTransaction::STATUS_ISSUED, LoanTransaction::STATUS_COMPLETED])) {
                $totalIssuedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            } elseif ($transaction->type === LoanTransaction::TYPE_RETURN &&
                      in_array($transaction->status, [LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED, LoanTransaction::STATUS_COMPLETED])) {
                $totalReturnedQty += $transaction->loanTransactionItems()->sum('quantity_transacted');
            }
        }

        $currentStatus = $this->status;
        $newStatus = $currentStatus; // Default to no change

        // Do not change status if already rejected, cancelled, or still a draft
        if (in_array($currentStatus, [self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_DRAFT])) {
            return;
        }

        if ($totalApprovedQty > 0) { // Only if there's something to issue
            if ($totalIssuedQty > 0) { // If at least some items are issued
                if ($totalReturnedQty >= $totalIssuedQty) { // All issued items have been returned
                    $newStatus = self::STATUS_RETURNED;
                } elseif ($totalIssuedQty >= $totalApprovedQty) { // All approved items have been issued
                    $newStatus = self::STATUS_ISSUED;
                } else { // Some approved items issued, but not all
                    $newStatus = self::STATUS_PARTIALLY_ISSUED;
                }
            } elseif ($currentStatus === self::STATUS_APPROVED && $totalIssuedQty === 0) {
                // Still approved, nothing issued yet
                $newStatus = self::STATUS_APPROVED;
            }
        }


        // Check for overdue status only if not already fully returned or draft/cancelled/rejected
        if (!in_array($newStatus, [self::STATUS_RETURNED, self::STATUS_DRAFT, self::STATUS_CANCELLED, self::STATUS_REJECTED]) &&
            $this->loan_end_date && now()->gt($this->loan_end_date) && $totalIssuedQty > $totalReturnedQty) {
            $newStatus = self::STATUS_OVERDUE;
        }

        if ($newStatus !== $currentStatus) {
            // Use transitionToStatus to ensure logging and potential events are handled,
            // though this is an internal update, so direct status set and save is also an option.
            // For simplicity here, direct save. Add acting user if this can be system-triggered.
            Log::info("LoanApplication ID {$this->id} status auto-updated after transaction.", [
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'approved_qty' => $totalApprovedQty,
                'issued_qty' => $totalIssuedQty,
                'returned_qty' => $totalReturnedQty
            ]);
            $this->status = $newStatus;
            $this->save();
        }
    }
}
