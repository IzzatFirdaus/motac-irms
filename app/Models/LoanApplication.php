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
 * @property string $status (enum: 'draft', 'pending_support', 'pending_approver_review', 'pending_bpm_review', 'approved', 'rejected', 'partially_issued', 'issued', 'returned', 'overdue', 'cancelled', default: 'draft') // Updated enum description
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
 * @property-read string $item_name (Accessor)
 * @property-read int $quantity (Accessor)
 * @property-read \Illuminate\Support\Carbon|null $expected_return_date (Accessor)
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
  // public const STATUS_PENDING_HOD_REVIEW = 'pending_hod_review'; // Old constant
  public const STATUS_PENDING_APPROVER_REVIEW = 'pending_approver_review'; // New constant
  public const STATUS_PENDING_BPM_REVIEW = 'pending_bpm_review';
  public const STATUS_APPROVED = 'approved';
  public const STATUS_REJECTED = 'rejected';
  public const STATUS_PARTIALLY_ISSUED = 'partially_issued';
  public const STATUS_ISSUED = 'issued';
  public const STATUS_RETURNED = 'returned';
  public const STATUS_OVERDUE = 'overdue';
  public const STATUS_CANCELLED = 'cancelled';
  public const STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION = 'partially_returned_pending_inspection';


  public static array $STATUS_OPTIONS = [
    self::STATUS_DRAFT => 'Draf',
    self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai (Gred 41+)',
    // self::STATUS_PENDING_HOD_REVIEW => 'Menunggu Kelulusan Ketua Jabatan/Unit', // Old option
    self::STATUS_PENDING_APPROVER_REVIEW => 'Menunggu Kelulusan Pegawai Pelulus', // New option
    self::STATUS_PENDING_BPM_REVIEW => 'Menunggu Semakan & Kelulusan BPM',
    self::STATUS_APPROVED => 'Diluluskan (Sedia Untuk Pengeluaran)',
    self::STATUS_REJECTED => 'Ditolak',
    self::STATUS_PARTIALLY_ISSUED => 'Sebahagian Peralatan Telah Dikeluarkan',
    self::STATUS_ISSUED => 'Semua Peralatan Telah Dikeluarkan',
    self::STATUS_RETURNED => 'Semua Peralatan Telah Dipulangkan',
    self::STATUS_OVERDUE => 'Tertunggak Pemulangan Peralatan',
    self::STATUS_CANCELLED => 'Dibatalkan',
    self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'Dipulangkan Sebahagian (Menunggu Semakan)',
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

  // ADDED: Append new accessors to array/JSON forms if they should always be included.
  // Optional, depending on usage. For dashboard display, direct access is usually fine.
  // protected $appends = ['status_label', 'item_name', 'quantity', 'expected_return_date'];


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
  public function items(): HasMany // Alias
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

  /**
   * ADDED: Accessor for item_name.
   * For the dashboard's "Perkara" (Subject/Matter) column,
   * this will use the loan's purpose.
   */
  public function getItemNameAttribute(): string
  {
    if (!empty($this->purpose)) {
        return Str::limit($this->purpose, 50); // Limit length for display
    }

    // Fallback if purpose is empty but items exist (more complex, requires eager loading 'loanApplicationItems')
    if ($this->relationLoaded('loanApplicationItems') && $this->loanApplicationItems->isNotEmpty()) {
        $itemTypes = $this->loanApplicationItems->pluck('equipment_type')->unique()->implode(', ');
        return Str::limit($itemTypes ?: __('Pelbagai Peralatan'), 50);
    }

    return __('Tiada Butiran');
  }

  /**
   * ADDED: Accessor for overall quantity of requested items in the application.
   */
  public function getQuantityAttribute(): int
  {
      // Ensure loanApplicationItems relationship is loaded to avoid N+1 if called in a loop
      if ($this->relationLoaded('loanApplicationItems')) {
          return (int) $this->loanApplicationItems->sum('quantity_requested');
      }
      // Fallback if not eager loaded for some reason, though less efficient.
      // It's better to ensure eager loading in the component.
      return (int) $this->loanApplicationItems()->sum('quantity_requested');
  }

  /**
   * ADDED: Accessor for expected_return_date.
   * This assumes loan_end_date is the expected return date.
   */
  public function getExpectedReturnDateAttribute(): ?Carbon
  {
      return $this->loan_end_date;
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

  // Duplicated method name, using the one from HasMany relationship definition
  // public function applicationItems()
  // {
  //   return $this->hasMany(LoanApplicationItem::class);
  // }


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
    if (!in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE, self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION])) { // Added partially_returned_pending_inspection
      return false;
    }
    if ($this->status === self::STATUS_OVERDUE) return true;

    if ($this->loan_end_date && Carbon::parse($this->loan_end_date)->isPast()) {
      $unreturnedIssuedItems = $this->loanApplicationItems()
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
    // Ensure relationships are loaded if not already
    $this->loadMissing('loanApplicationItems');

    $totalRequested = $this->loanApplicationItems->sum('quantity_requested');
    $totalIssued = $this->loanApplicationItems->sum('quantity_issued');
    $totalReturned = $this->loanApplicationItems->sum('quantity_returned');

    $originalStatus = $this->status; // Keep track of original status for logging

    if ($totalIssued == 0 && $totalReturned == 0) {
        // If no items ever issued, status should remain based on approval flow (e.g., approved, pending)
        // No change here unless it was 'issued' or 'returned' incorrectly before.
        if ($this->status === self::STATUS_ISSUED || $this->status === self::STATUS_PARTIALLY_ISSUED || $this->status === self::STATUS_RETURNED) {
            // This state is unusual if nothing was issued. Revert to approved if it was.
            // Or, this could be a cancellation case after approval but before issue.
            // For now, we assume if it got to 'issued', it implies items were meant to be issued.
            // This condition implies something went wrong if items were not actually recorded as issued.
        }
    } elseif ($totalIssued > 0 && $totalReturned >= $totalIssued) {
        // All items that were issued have been returned (or more, which is an anomaly)
        $this->status = self::STATUS_RETURNED;
    } elseif ($totalIssued > 0 && $totalReturned < $totalIssued) {
        // Some items are still out (not fully returned)
        // Check if all *requested* items were issued initially
        if ($totalIssued >= $totalRequested) { // All requested (or more, anomaly) were issued
            $this->status = self::STATUS_ISSUED; // Still considered 'Issued' as not everything is back
        } else { // Not all requested items were issued, implies partially issued
            $this->status = self::STATUS_PARTIALLY_ISSUED;
        }

        // If any returned items are pending inspection, it could influence the overall status
        // For simplicity in this method, we'll primarily base it on quantities.
        // A more granular status like 'partially_returned_pending_inspection' might be set by LoanTransactionService
        // if needed, and this method would respect it if it's already set, or refine it.
    }
    // else, no items were ever issued, so status remains as per approval workflow (e.g., draft, approved)

    // Overdue Check: This should apply if items are still out (issued or partially issued) and past due date
    if (
      $this->loan_end_date &&
      $this->loan_end_date->isPast() &&
      in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED])
    ) {
      $this->status = self::STATUS_OVERDUE;
    }

    if ($this->isDirty('status')) {
        $this->save();
        Log::info("LoanApplication ID {$this->id} overall status changed from '{$originalStatus}' to '{$this->status}' based on transaction item sums. Requested: {$totalRequested}, Issued: {$totalIssued}, Returned: {$totalReturned}.");
    } else {
        Log::info("LoanApplication ID {$this->id} overall status remains '{$this->status}'. Requested: {$totalRequested}, Issued: {$totalIssued}, Returned: {$totalReturned}.");
    }
  }
}
