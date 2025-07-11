<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\LoanApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoanApplication extends Model
{
  use Blameable;
  use HasFactory;
  use SoftDeletes;

  // --- STATUS CONSTANTS ---
  public const STATUS_DRAFT = 'draft';
  public const STATUS_PENDING_SUPPORT = 'pending_support';
  public const STATUS_PENDING_APPROVER_REVIEW = 'pending_approver_review';
  public const STATUS_PENDING_BPM_REVIEW = 'pending_bpm_review';
  public const STATUS_APPROVED = 'approved';
  public const STATUS_REJECTED = 'rejected';
  public const STATUS_PARTIALLY_ISSUED = 'partially_issued';
  public const STATUS_ISSUED = 'issued';
  public const STATUS_RETURNED = 'returned';
  public const STATUS_OVERDUE = 'overdue';
  public const STATUS_CANCELLED = 'cancelled';
  public const STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION = 'partially_returned_pending_inspection';
  public const STATUS_COMPLETED = 'completed';

  // --- PROPERTIES ---
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
    'current_approval_stage'
  ];

  protected $casts = [
    'loan_start_date' => 'datetime',
    'loan_end_date' => 'datetime',
    'applicant_confirmation_timestamp' => 'datetime',
    'submitted_at' => 'datetime',
    'approved_at' => 'datetime',
    'rejected_at' => 'datetime',
    'cancelled_at' => 'datetime'
  ];

  protected $attributes = ['status' => self::STATUS_DRAFT];

  protected static function newFactory(): LoanApplicationFactory
  {
    return LoanApplicationFactory::new();
  }

  // --- RELATIONSHIPS ---
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

  // --- ACCESSORS & STATIC METHODS ---
  public function getStatusLabelAttribute(): string
  {
    return self::getStatusOptions()[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
  }

  /**
   * UPDATED: This method now dynamically fetches status labels from the language files.
   */
  public static function getStatusOptions(): array
  {
    return [
      // CORRECTED: All calls now use 'loan-applications' (hyphen) to match Laravel conventions
      self::STATUS_DRAFT => __('loan-applications.statuses.draft'),
      self::STATUS_PENDING_SUPPORT => __('loan-applications.statuses.pending_support'),
      self::STATUS_PENDING_APPROVER_REVIEW => __('loan-applications.statuses.pending_approver_review'),
      self::STATUS_PENDING_BPM_REVIEW => __('loan-applications.statuses.pending_bpm_review'),
      self::STATUS_APPROVED => __('loan-applications.statuses.approved'),
      self::STATUS_REJECTED => __('loan-applications.statuses.rejected'),
      self::STATUS_PARTIALLY_ISSUED => __('loan-applications.statuses.partially_issued'),
      self::STATUS_ISSUED => __('loan-applications.statuses.issued'),
      self::STATUS_RETURNED => __('loan-applications.statuses.returned'),
      self::STATUS_OVERDUE => __('loan-applications.statuses.overdue'),
      self::STATUS_CANCELLED => __('loan-applications.statuses.cancelled'),
      self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => __('loan-applications.statuses.partially_returned_pending_inspection'),
      self::STATUS_COMPLETED => __('loan-applications.statuses.completed'),
    ];
  }

  public function getStatusColorClassAttribute(): string
  {
    return match ($this->status) {
      self::STATUS_DRAFT => 'badge bg-secondary-subtle text-secondary-emphasis',
      self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_APPROVER_REVIEW, self::STATUS_PENDING_BPM_REVIEW => 'badge bg-warning-subtle text-warning-emphasis',
      self::STATUS_APPROVED => 'badge bg-info-subtle text-info-emphasis',
      self::STATUS_PARTIALLY_ISSUED, self::STATUS_ISSUED => 'badge bg-primary-subtle text-primary-emphasis',
      self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'badge bg-primary-subtle text-primary-emphasis',
      self::STATUS_RETURNED, self::STATUS_COMPLETED => 'badge bg-success-subtle text-success-emphasis',
      self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_OVERDUE => 'badge bg-danger-subtle text-danger-emphasis',
      default => 'badge bg-dark-subtle text-dark-emphasis',
    };
  }

  // --- HELPER METHODS ---
  public function isDraft(): bool
  {
    return $this->status === self::STATUS_DRAFT;
  }

  public function isRejected(): bool
  {
    return $this->status === self::STATUS_REJECTED;
  }

  public function isClosed(): bool
  {
    return in_array($this->status, [
      self::STATUS_RETURNED,
      self::STATUS_COMPLETED,
      self::STATUS_CANCELLED,
      self::STATUS_REJECTED,
    ]);
  }

  public function canBeReturned(): bool
  {
    return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE]);
  }

  public function isOverdue(): bool
  {
    if (!in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED])) {
      return false;
    }
    if ($this->loan_end_date && $this->loan_end_date->isPast()) {
      return $this->loanApplicationItems()->where('quantity_issued', '>', DB::raw('IFNULL(quantity_returned, 0)'))->exists();
    }
    return false;
  }

  public function updateOverallStatusAfterTransaction(): void
  {
    $this->load('loanApplicationItems');
    $totalApproved = (int) $this->loanApplicationItems->sum('quantity_approved');
    $totalIssued = (int) $this->loanApplicationItems->sum('quantity_issued');
    $totalReturned = (int) $this->loanApplicationItems->sum('quantity_returned');
    $originalStatus = $this->status;
    $newStatus = $originalStatus;

    if ($totalIssued > 0) {
      if ($totalReturned >= $totalIssued) {
        $newStatus = self::STATUS_RETURNED;
      } elseif ($totalReturned > 0) {
        $newStatus = self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION;
      } elseif ($totalIssued >= $totalApproved) {
        $newStatus = self::STATUS_ISSUED;
      } else {
        $newStatus = self::STATUS_PARTIALLY_ISSUED;
      }
    } elseif ($this->status !== self::STATUS_REJECTED && $this->status !== self::STATUS_CANCELLED) {
      $newStatus = self::STATUS_APPROVED;
    }

    if ($newStatus !== self::STATUS_RETURNED && $this->isOverdue()) {
      $newStatus = self::STATUS_OVERDUE;
    }

    if ($newStatus !== $originalStatus) {
      $this->update(['status' => $newStatus]);
      Log::info(sprintf("LoanApplication ID %d overall status changed from '%s' to '%s'.", $this->id, $originalStatus, $newStatus));
    }
  }

  // --- NEW ACCESSORS ---

  /**
   * Get the effective return location, falling back to the usage location.
   * This simplifies the logic in the Blade view.
   *
   * @return string|null
   */
  public function getEffectiveReturnLocationAttribute(): ?string
  {
    return $this->return_location ?? $this->location;
  }

  /**
   * Get the latest loan transaction of type 'issue'.
   * This moves the query from the Blade view into the model.
   *
   * @return \App\Models\LoanTransaction|null
   */
  public function getLatestIssueTransactionAttribute(): ?LoanTransaction
  {
    return $this->loanTransactions()->where('type', 'issue')->latest('transaction_date')->first();
  }

  // --- QUERY SCOPES ---

  /**
   * Scope for active loans (issued but not fully returned or completed).
   */
  public function scopeActive($query)
  {
    return $query->whereIn('status', [
      self::STATUS_ISSUED,
      self::STATUS_PARTIALLY_ISSUED,
      self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION,
      self::STATUS_OVERDUE
    ]);
  }

  /**
   * Scope for overdue loans.
   */
  public function scopeOverdue($query)
  {
    return $query->whereIn('status', [
      self::STATUS_ISSUED,
      self::STATUS_PARTIALLY_ISSUED
    ])
      ->whereDate('loan_end_date', '<', now());
  }

  /**
   * Scope for loans due in a given number of days.
   */
  public function scopeDueInDays($query, int $days)
  {
    return $query
      ->whereIn('status', [
        self::STATUS_ISSUED,
        self::STATUS_PARTIALLY_ISSUED
      ])
      ->whereDate('loan_end_date', '=', now()->addDays($days)->toDateString());
  }
}
