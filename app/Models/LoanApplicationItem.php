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
use Illuminate\Support\Facades\Log;

/**
 * Loan Application Model.
 * (PHPDoc from your version in turn 24)
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property int|null $responsible_officer_id User ID of the officer responsible, if not applicant
 * @property int|null $supporting_officer_id User ID of the officer supporting the application (e.g., HOD Gred 41+)
 * @property string $purpose
 * @property string $location Location where equipment will be used
 * @property string|null $return_location Location where equipment will be returned (from system design)
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date Tarikh Dijangka Pulang
 * @property string $status From STATUSES_LIST
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
 * @property int|null $current_approval_officer_id (Consider if this is better tracked on Approval model)
 * @property string|null $current_approval_stage (Consider if this is better tracked on Approval model)
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
 * @property-read \App\Models\User|null $approvedByOfficer
 * @property-read \App\Models\User|null $rejectedByOfficer
 * @property-read \App\Models\User|null $cancelledByOfficer
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property-read string $status_translated
 */
class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

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

    // For validation Rule::in
    public static array $STATUSES_LIST = [
        self::STATUS_DRAFT, self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_HOD_REVIEW,
        self::STATUS_PENDING_BPM_REVIEW, self::STATUS_APPROVED, self::STATUS_REJECTED,
        self::STATUS_PARTIALLY_ISSUED, self::STATUS_ISSUED, self::STATUS_RETURNED,
        self::STATUS_OVERDUE, self::STATUS_CANCELLED,
    ];

    // For display
    public static array $STATUSES_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan',
        self::STATUS_PENDING_HOD_REVIEW => 'Menunggu Kelulusan Ketua Jabatan',
        self::STATUS_PENDING_BPM_REVIEW => 'Semakan Ketersediaan (BPM)',
        self::STATUS_APPROVED => 'Diluluskan (Sedia Pengeluaran)',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_PARTIALLY_ISSUED => 'Dikeluarkan Sebahagian',
        self::STATUS_ISSUED => 'Telah Dikeluarkan',
        self::STATUS_RETURNED => 'Telah Dipulangkan',
        self::STATUS_OVERDUE => 'Tertunggak Pemulangan',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'loan_applications';

    protected $fillable = [
        'user_id', 'responsible_officer_id', 'supporting_officer_id', 'purpose', 'location', 'return_location',
        'loan_start_date', 'loan_end_date', 'status', 'rejection_reason', 'applicant_confirmation_timestamp',
        'admin_notes', 'submitted_at', 'approved_at', 'approved_by', 'rejected_at', 'rejected_by',
        'cancelled_at', 'cancelled_by',
        'current_approval_officer_id', 'current_approval_stage', // These might be better derived or on Approval model
        // created_by, updated_by are handled by BlameableObserver
    ];

    protected $casts = [
        'loan_start_date' => 'datetime',
        'loan_end_date' => 'datetime',
        'applicant_confirmation_timestamp' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    protected static function newFactory(): LoanApplicationFactory
    {
        return LoanApplicationFactory::new();
    }

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

    public function applicationItems(): HasMany
    {
        return $this->hasMany(LoanApplicationItem::class, 'loan_application_id');
    }

    public function getItemsAttribute() // Alias
    {
        return $this->applicationItems; // Access as loaded collection or query if not loaded
    }

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

    public function getStatusTranslatedAttribute(): string
    {
        return self::$STATUSES_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    public static function getStatusOptions(): array
    {
        return self::$STATUSES_LABELS;
    }
    public static function getStatuses(): array // For Rule::in
    {
        return self::$STATUSES_LIST;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
    {
        if (!in_array($newStatus, self::$STATUSES_LIST, true)) {
            Log::warning("LoanApplication ID {$this->id}: Invalid status transition attempt to '{$newStatus}'.", ['acting_user_id' => $actingUserId, 'current_status' => $this->status]);
            return false;
        }
        $oldStatus = $this->status;
        $this->status = $newStatus;

        switch ($newStatus) {
            case self::STATUS_PENDING_SUPPORT:
                $this->submitted_at = $this->submitted_at ?? now();
                $this->applicant_confirmation_timestamp = $this->applicant_confirmation_timestamp ?? now();
                break;
            case self::STATUS_APPROVED:
                $this->approved_at = now();
                $this->approved_by = $actingUserId;
                break;
            case self::STATUS_REJECTED:
                $this->rejected_at = now();
                $this->rejected_by = $actingUserId;
                if ($reason) $this->rejection_reason = $reason;
                break;
            case self::STATUS_CANCELLED:
                $this->cancelled_at = now();
                $this->cancelled_by = $actingUserId;
                break;
        }
        Log::info("LoanApplication ID {$this->id} status transitioned from {$oldStatus} to {$newStatus}.", ['acting_user_id' => $actingUserId, 'reason' => $reason]);
        return $this->save();
    }

    public function updateOverallStatusAfterTransaction(): void
    {
        // This logic should be robust and reflect the current state of items
        $this->refresh()->load('applicationItems'); // Ensure fresh data

        $totalApproved = $this->applicationItems->sum(function($item) {
            return $item->quantity_approved ?? $item->quantity_requested;
        });
        $totalIssued = $this->applicationItems->sum('quantity_issued');
        $totalReturned = $this->applicationItems->sum('quantity_returned');

        $currentStatus = $this->status;
        $newStatus = $currentStatus;

        if (in_array($currentStatus, [self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_DRAFT])) {
            // Do not change status if already in a final or draft state by this method
            return;
        }

        if ($totalIssued == 0 && $currentStatus == self::STATUS_APPROVED) {
            $newStatus = self::STATUS_APPROVED; // Stays approved
        } elseif ($totalIssued > 0 && $totalIssued < $totalApproved) {
            $newStatus = self::STATUS_PARTIALLY_ISSUED;
        } elseif ($totalIssued > 0 && $totalIssued >= $totalApproved) {
            // All approved items have been issued
            if ($totalReturned < $totalIssued) {
                if (now()->gt($this->loan_end_date)) {
                    $newStatus = self::STATUS_OVERDUE;
                } else {
                    $newStatus = self::STATUS_ISSUED;
                }
            } elseif ($totalReturned >= $totalIssued) {
                $newStatus = self::STATUS_RETURNED;
            }
        }

        if ($newStatus !== $currentStatus) {
            Log::info("LoanApplication ID {$this->id} status updated after transaction from {$currentStatus} to {$newStatus}.");
            $this->status = $newStatus;
            $this->saveQuietly(); // Use saveQuietly to avoid triggering observers if this is an internal update
        }
    }
}
