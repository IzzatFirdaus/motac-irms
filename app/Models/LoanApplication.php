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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Loan Application Model.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 *
 * @property int $id
 * @property int $user_id (applicant)
 * @property int|null $responsible_officer_id
 * @property int|null $supporting_officer_id
 * @property string $purpose
 * @property string $location (usage location)
 * @property string|null $return_location
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date
 * @property string $status (enum: 'draft', 'pending_support', 'pending_hod_review', 'pending_bpm_review', 'approved', 'rejected', 'partially_issued', 'issued', 'returned', 'overdue', 'cancelled', default: 'draft')
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes
 * @property int|null $current_approval_officer_id
 * @property string|null $current_approval_stage
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \App\Models\User $user The applicant
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $rejectedBy
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $loanApplicationItems
 * @property-read int|null $loan_application_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_label
 * @property-read string $full_name (Assuming this accessor might exist on User or LoanApplication if needed)
 *
 * @method static LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withoutTrashed()
 * @mixin \Eloquent
 */
class LoanApplication extends Model
{
  use HasFactory;
  use SoftDeletes;

  // Status Constants
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
  public const STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION = 'partially_returned_pending_inspection'; // ADDED: Missing constant


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
    self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'Dipulangkan Sebahagian (Menunggu Semakan)', // ADDED: Label for new status
  ];

  protected $table = 'loan_applications';

  protected $fillable = [
    'user_id',
    'responsible_officer_id',
    'supporting_officer_id',
    'purpose',
    'location',
    'return_location',
    'loan_start_date',
    'loan_end_date',
    'status',
    'rejection_reason',
    'applicant_confirmation_timestamp',
    'submitted_at',
    'approved_by',
    'approved_at',
    'rejected_by',
    'rejected_at',
    'cancelled_by',
    'cancelled_at',
    'admin_notes',
    'current_approval_officer_id',
    'current_approval_stage',
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

  public static function getStatusOptions(): array
  {
    return self::$STATUS_OPTIONS;
  }

  public static function getStatusLabel(string $statusKey): string
  {
    return __(self::$STATUS_OPTIONS[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey)));
  }

  public static function getStatusKeys(): array
  {
    return array_keys(self::$STATUS_OPTIONS);
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
  public function approvedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'approved_by');
  }
  public function rejectedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'rejected_by');
  }
  public function cancelledBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'cancelled_by');
  }
  public function currentApprovalOfficer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'current_approval_officer_id');
  }

  public function loanApplicationItems(): HasMany
  {
    return $this->hasMany(LoanApplicationItem::class, 'loan_application_id');
  }
  public function items(): HasMany
  {
    return $this->loanApplicationItems();
  }

  public function loanTransactions(): HasMany
  {
    return $this->hasMany(LoanTransaction::class, 'loan_application_id');
  }
  public function approvals(): MorphMany
  {
    return $this->morphMany(Approval::class, 'approvable');
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
  public function getStatusLabelAttribute(): string
  {
    return self::getStatusLabel($this->status);
  }

  // Helper Methods for status checks
  public function isDraft(): bool
  {
    return $this->status === self::STATUS_DRAFT;
  }

  public function isRejected(): bool
  {
    return $this->status === self::STATUS_REJECTED;
  }

  public function canBeIssued(): bool
  {
    return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED]);
  }

  public function canBeReturned(): bool
  {
    return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE]);
  }

  public function getRelevantIssueTransactionForReturn(): ?LoanTransaction
  {
    return $this->loanTransactions()
      ->where('type', LoanTransaction::TYPE_ISSUE)
      ->orderBy('transaction_date', 'desc')
      ->orderBy('id', 'desc')
      ->first();
  }

  public function isOverdue(): bool
  {
    if (!in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE])) {
      return false;
    }
    if ($this->status === self::STATUS_OVERDUE) return true;

    if ($this->loan_end_date && Carbon::parse($this->loan_end_date)->isPast()) {
      $unreturnedIssuedItems = $this->loanApplicationItems() // Changed from applicationItems to loanApplicationItems
        ->where('quantity_issued', '>', DB::raw('IFNULL(quantity_returned, 0)'))
        ->exists();
      return $unreturnedIssuedItems;
    }
    return false;
  }

  public function canBeCancelledByApplicant(): bool
  {
    return in_array($this->status, [
      self::STATUS_DRAFT,
      self::STATUS_PENDING_SUPPORT,
    ]);
  }

  public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool
  {
    Log::info("Transitioning LoanApplication ID {$this->id} from {$this->status} to {$newStatus}. Acting User ID: {$actingUserId}. Reason: {$reason}");

    $this->status = $newStatus;

    if ($newStatus === self::STATUS_PENDING_SUPPORT && !$this->submitted_at) {
      $this->submitted_at = now();
      if ($this->applicant_confirmation_timestamp === null && $this->user_id === $actingUserId) {
        $this->applicant_confirmation_timestamp = now();
      }
    }

    if ($newStatus === self::STATUS_CANCELLED && $actingUserId && !$this->cancelled_by) {
      $this->cancelled_by = $actingUserId;
      $this->cancelled_at = now();
      if ($reason) $this->admin_notes = ($this->admin_notes ? $this->admin_notes . "\n" : '') . "Dibatalkan: " . $reason;
    }

    if ($newStatus === self::STATUS_REJECTED && $reason) {
      $this->rejection_reason = $reason;
      if ($actingUserId) $this->rejected_by = $actingUserId;
      $this->rejected_at = now();
    }
    if ($newStatus === self::STATUS_APPROVED && $actingUserId) {
      $this->approved_by = $actingUserId;
      $this->approved_at = now();
      $this->rejection_reason = null;
    }
    return $this->save();
  }

  public function updateOverallStatusAfterTransaction(): void
  {
    $totalRequested = $this->loanApplicationItems->sum('quantity_requested');
    $totalIssued = $this->loanApplicationItems->sum('quantity_issued'); // Changed from applicationItems
    $totalReturned = $this->loanApplicationItems->sum('quantity_returned'); // Changed from applicationItems

    if ($totalIssued == 0 && $totalReturned == 0) {
      // No items issued yet. If status is approved, keep it. Otherwise, no change if still pending.
      // No explicit status change needed here, as the initial state is already set based on approval flow.
    } elseif ($totalIssued > 0 && $totalReturned === $totalIssued) {
      // All issued items have been returned
      $this->status = self::STATUS_RETURNED;
    } elseif ($totalIssued > 0 && $totalReturned < $totalIssued) {
      // Some items are still out or partially returned
      if ($totalIssued === $totalRequested) {
        // All requested items were issued, but not all returned
        $this->status = self::STATUS_ISSUED; // Keep issued, as some are still out
      } else {
        // Some requested were issued, and some are still out
        $this->status = self::STATUS_PARTIALLY_ISSUED;
      }

      // Check if there are any items currently awaiting inspection from a return
      $hasItemsPendingInspection = $this->loanTransactions()
        ->where('type', LoanTransaction::TYPE_RETURN)
        ->whereIn('status', [
          LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION,
          LoanTransaction::STATUS_RETURNED_GOOD, // Could be in this state after a quick return
          LoanTransaction::STATUS_RETURNED_DAMAGED,
          LoanTransaction::STATUS_RETURNED_WITH_LOSS,
          LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS,
          LoanTransaction::STATUS_PARTIALLY_RETURNED // General partially returned
        ])
        ->exists();

      // If there are partial returns but some items are still out (not fully returned),
      // we might set it to a "partially returned pending inspection" state if relevant.
      // This logic needs to be carefully considered based on your desired application lifecycle.
      if ($hasItemsPendingInspection && $totalReturned > 0 && $totalReturned < $totalIssued) {
        $this->status = self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION;
      }
    } elseif ($totalIssued > 0 && $totalIssued === $totalRequested && $totalReturned < $totalIssued) {
      // All requested items were issued, but not all returned yet
      $this->status = self::STATUS_ISSUED;
    }

    // Handle overdue logic: Check if loan_end_date is in the past and status is issued/partially_issued
    if (
      $this->loan_end_date && $this->loan_end_date->isPast() &&
      ($this->status === self::STATUS_ISSUED ||
        $this->status === self::STATUS_PARTIALLY_ISSUED ||
        $this->status === self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION) // Also check if partially returned and overdue
    ) {
      $this->status = self::STATUS_OVERDUE;
    }


    if ($this->isDirty('status')) {
      $this->save();
      Log::info("LoanApplication ID {$this->id} overall status updated to '{$this->status}' based on transaction item changes.");
    }
  }
}
