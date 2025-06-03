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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Auth facade not directly used in this model

/**
 * Loan Application Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3 [cite: 368]
 *
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property int|null $responsible_officer_id
 * @property int|null $supporting_officer_id
 * @property string $purpose
 * @property string $location Usage location
 * @property string|null $return_location
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $approved_by User ID of approver
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by User ID of rejector
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by User ID of canceller
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes
 * @property int|null $current_approval_officer_id User ID of current approver
 * @property string|null $current_approval_stage
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $rejectedBy
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $applicationItems
 * @property-read int|null $application_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items alias for applicationItems
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 * @property-read bool $is_draft
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApplicantConfirmationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCurrentApprovalOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCurrentApprovalStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereResponsibleOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereReturnLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereSupportingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication withoutTrashed()
 * @mixin \Eloquent
 */
class LoanApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Status constants as defined in "Revision 3" (Section 4.3, loan_applications table) [cite: 371]
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

    /**
     * Options for status dropdowns and human-readable display.
     * Consider using translation keys for labels if supporting multiple languages.
     */
    public static array $STATUS_OPTIONS = [
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
        // created_by, updated_by are typically handled by BlameableObserver or similar mechanism [cite: 352]
    ];

    protected $casts = [
        'loan_start_date' => 'datetime',
        'loan_end_date' => 'datetime',
        'applicant_confirmation_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime', // Eloquent handles these by default
        'updated_at' => 'datetime', // Eloquent handles these by default
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT, // Default status for new applications
    ];

    /**
     * Returns status options for dropdowns or display.
     */
    public static function getStatusOptions(): array
    {
        return self::$STATUS_OPTIONS;
    }

    /**
     * Returns a specific status label.
     */
    public static function getStatusLabel(string $statusKey): string
    {
        return __(self::$STATUS_OPTIONS[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)));
    }


    // Helper to get all status keys
    public static function getStatusKeys(): array
    {
        return array_keys(self::$STATUS_OPTIONS);
    }

    protected static function newFactory(): LoanApplicationFactory
    {
        return LoanApplicationFactory::new();
    }

    // Relationships
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); } // Applicant
    public function responsibleOfficer(): BelongsTo { return $this->belongsTo(User::class, 'responsible_officer_id'); }
    public function supportingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'supporting_officer_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedBy(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function currentApprovalOfficer(): BelongsTo { return $this->belongsTo(User::class, 'current_approval_officer_id');}

    public function applicationItems(): HasMany { return $this->hasMany(LoanApplicationItem::class, 'loan_application_id'); }
    public function items(): HasMany { return $this->applicationItems(); } // Alias for applicationItems

    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }

    // Blameable relationships [cite: 352]
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    // Accessors
    public function getStatusTranslatedAttribute(): string
    {
        return self::getStatusLabel($this->status);
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBeIssued(): bool
    {
        // Add logic based on status, e.g., approved or partially issued but not fully returned.
        // This is a placeholder, actual logic might be more complex.
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED]);
    }

    public function canBeReturned(): bool
    {
        // Add logic based on status, e.g., issued or partially issued.
        return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE]);
    }


    /**
     * Gets the most recent relevant "issue" transaction for initiating a return.
     * This logic might be better in a service if it grows more complex.
     */
    public function getRelevantIssueTransactionForReturn(): ?LoanTransaction
    {
        return $this->loanTransactions()
            ->where('type', LoanTransaction::TYPE_ISSUE)
            // ->where('status', LoanTransaction::STATUS_ISSUED) // Or other statuses that permit return
            ->orderBy('transaction_date', 'desc')
            ->first();
    }


    /**
     * Basic status transition.
     * NOTE: Complex business logic for status transitions, notifications, and approval workflow
     * initiation should ideally be handled within a dedicated service class (e.g., LoanApplicationService, ApprovalService)
     * to keep the model focused on data representation and relationships. [cite: 324]
     */
    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        Log::info("Transitioning LoanApplication ID {$this->id} from {$this->status} to {$newStatus}. Acting User ID: {$actingUserId}. Reason: {$reason}");

        $this->status = $newStatus;

        if ($newStatus === self::STATUS_PENDING_SUPPORT && !$this->submitted_at) {
            $this->submitted_at = now();
            $this->applicant_confirmation_timestamp = now(); // Also set confirmation if submitting
        }

        if ($newStatus === self::STATUS_CANCELLED && $actingUserId && !$this->cancelled_by) {
            $this->cancelled_by = $actingUserId;
            $this->cancelled_at = now();
            if($reason) $this->admin_notes = ($this->admin_notes ? $this->admin_notes . "\n" : '') . "Dibatalkan: " . $reason;
        }

        // If rejected, store the reason
        if ($newStatus === self::STATUS_REJECTED && $reason) {
            $this->rejection_reason = $reason;
            if($actingUserId) $this->rejected_by = $actingUserId;
            $this->rejected_at = now();
        }
         if ($newStatus === self::STATUS_APPROVED && $actingUserId) {
            $this->approved_by = $actingUserId;
            $this->approved_at = now();
            $this->rejection_reason = null; // Clear rejection reason if it's now approved
        }


        return $this->save();
    }

    /**
     * Placeholder for updating overall status after a transaction.
     * Logic should be in LoanApplicationService. [cite: 326]
     */
    public function updateOverallStatusAfterTransaction(): void
    {
        Log::debug("Placeholder: updateOverallStatusAfterTransaction called for LoanApplication ID {$this->id}. Actual logic should be in a service.");
        // Example:
        // $this->load('loanTransactions.loanTransactionItems', 'applicationItems');
        // // Logic to determine if status should be 'issued', 'partially_issued', 'returned' based on items and transactions.
        // // This is complex and belongs in the LoanApplicationService.
    }
}
