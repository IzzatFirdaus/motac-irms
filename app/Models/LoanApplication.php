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
use Illuminate\Support\Facades\DB; // Added for DB::raw if needed in complex queries
use Carbon\Carbon; // Ensure Carbon is imported

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
  } // Changed from approver
  public function rejectedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'rejected_by');
  } // Changed from rejector
  public function cancelledBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'cancelled_by');
  } // Changed from canceller
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
  public function getStatusLabelAttribute(): string // Renamed for consistency
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
    // Actual overdue logic might be more complex, potentially checking items vs loan_end_date
    if ($this->status === self::STATUS_OVERDUE) return true; // If already marked overdue

    if ($this->loan_end_date && Carbon::parse($this->loan_end_date)->isPast()) {
      $unreturnedIssuedItems = $this->applicationItems()
        ->where('quantity_issued', '>', DB::raw('IFNULL(quantity_returned, 0)')) // Check items not fully returned
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
      if ($this->applicant_confirmation_timestamp === null && $this->user_id === $actingUserId) { // Only set if applicant is submitting
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
    Log::debug("Placeholder: updateOverallStatusAfterTransaction called for LoanApplication ID {$this->id}. Actual logic should be in a service.");
  }
}
