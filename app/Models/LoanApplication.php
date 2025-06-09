<?php

declare(strict_types=1);

namespace App\Models;

// EDITED: Import the Blameable trait
use App\Traits\Blameable;
use Carbon\Carbon;
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

// ... (Your PHPDoc block can remain as is) ...

class LoanApplication extends Model
{
    use HasFactory;
    use SoftDeletes;
    // EDITED: Use the Blameable trait for creator/updater relationships
    use Blameable;

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_SUPPORT = 'pending_support';
    // CORRECTED: Use the constant name the controller is expecting
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

    public static array $STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PENDING_SUPPORT => 'Menunggu Sokongan Pegawai (Gred 41+)',
        self::STATUS_PENDING_APPROVER_REVIEW => 'Menunggu Kelulusan Pegawai Pelulus',
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

    // ... (Your $table, $fillable, $casts, $attributes properties remain the same) ...
    protected $table = 'loan_applications';
    protected $fillable = [ 'user_id', 'responsible_officer_id', 'supporting_officer_id', 'purpose', 'location', 'return_location', 'loan_start_date', 'loan_end_date', 'status', 'rejection_reason', 'applicant_confirmation_timestamp', 'submitted_at', 'approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'cancelled_by', 'cancelled_at', 'admin_notes', 'current_approval_officer_id', 'current_approval_stage', ];
    protected $casts = [ 'loan_start_date' => 'datetime', 'loan_end_date' => 'datetime', 'applicant_confirmation_timestamp' => 'datetime', 'submitted_at' => 'datetime', 'approved_at' => 'datetime', 'rejected_at' => 'datetime', 'cancelled_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime', ];
    protected $attributes = [ 'status' => self::STATUS_DRAFT, ];

    // --- Static Methods & Factory ---
    public static function getStatusOptions(): array { return self::$STATUS_OPTIONS; }
    public static function getStatusLabel(string $statusKey): string { return __(self::$STATUS_OPTIONS[$statusKey] ?? Str::title(str_replace('_', ' ', $statusKey))); }
    public static function getStatusKeys(): array { return array_keys(self::$STATUS_OPTIONS); }
    protected static function newFactory(): LoanApplicationFactory { return LoanApplicationFactory::new(); }

    // --- Relationships ---
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function responsibleOfficer(): BelongsTo { return $this->belongsTo(User::class, 'responsible_officer_id'); }
    public function supportingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'supporting_officer_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedBy(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function currentApprovalOfficer(): BelongsTo { return $this->belongsTo(User::class, 'current_approval_officer_id'); }
    public function loanApplicationItems(): HasMany { return $this->hasMany(LoanApplicationItem::class, 'loan_application_id'); }
    public function items(): HasMany { return $this->loanApplicationItems(); }
    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }

    // REMOVED: The creator(), updater(), and deleter() relationships are now provided by the Blameable trait.

    // ... (Your Accessors and Helper Methods can remain exactly the same) ...
    public function getStatusLabelAttribute(): string { return self::getStatusLabel($this->status); }
    public function getItemNameAttribute(): string { if (! empty($this->purpose)) { return Str::limit($this->purpose, 50); } if ($this->relationLoaded('loanApplicationItems') && $this->loanApplicationItems->isNotEmpty()) { $itemTypes = $this->loanApplicationItems->pluck('equipment_type')->unique()->implode(', '); return Str::limit($itemTypes ?: __('Pelbagai Peralatan'), 50); } return __('Tiada Butiran'); }
    public function getQuantityAttribute(): int { if ($this->relationLoaded('loanApplicationItems')) { return (int) $this->loanApplicationItems->sum('quantity_requested'); } return (int) $this->loanApplicationItems()->sum('quantity_requested'); }
    public function getExpectedReturnDateAttribute(): ?Carbon { return $this->loan_end_date; }
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
    public function canBeIssued(): bool { return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED]); }
    public function canBeReturned(): bool { return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE]); }
    public function getRelevantIssueTransactionForReturn(): ?LoanTransaction { return $this->loanTransactions()->where('type', LoanTransaction::TYPE_ISSUE)->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->first(); }
    public function isOverdue(): bool { if (! in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE, self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION])) { return false; } if ($this->status === self::STATUS_OVERDUE) { return true; } if ($this->loan_end_date && $this->loan_end_date->isPast()) { $unreturnedIssuedItems = $this->loanApplicationItems()->where('quantity_issued', '>', DB::raw('IFNULL(quantity_returned, 0)'))->exists(); return $unreturnedIssuedItems; } return false; }
    public function canBeCancelledByApplicant(): bool { return in_array($this->status, [ self::STATUS_DRAFT, self::STATUS_PENDING_SUPPORT, ]); }
    public function transitionToStatus(string $newStatus, ?string $reason = null, ?int $actingUserId = null): bool { Log::info("Transitioning LoanApplication ID {$this->id} from {$this->status} to {$newStatus}. Acting User ID: {$actingUserId}. Reason: {$reason}"); $this->status = $newStatus; if ($newStatus === self::STATUS_PENDING_SUPPORT && ! $this->submitted_at) { $this->submitted_at = now(); if ($this->applicant_confirmation_timestamp === null && $this->user_id === $actingUserId) { $this->applicant_confirmation_timestamp = now(); } } if ($newStatus === self::STATUS_CANCELLED && $actingUserId && ! $this->cancelled_by) { $this->cancelled_by = $actingUserId; $this->cancelled_at = now(); if ($reason) { $this->admin_notes = ($this->admin_notes ? $this->admin_notes."\n" : '').'Dibatalkan: '.$reason; } } if ($newStatus === self::STATUS_REJECTED && $reason) { $this->rejection_reason = $reason; if ($actingUserId) { $this->rejected_by = $actingUserId; } $this->rejected_at = now(); } if ($newStatus === self::STATUS_APPROVED && $actingUserId) { $this->approved_by = $actingUserId; $this->approved_at = now(); $this->rejection_reason = null; } return $this->save(); }
    public function updateOverallStatusAfterTransaction(): void { $this->loadMissing('loanApplicationItems'); $totalRequested = $this->loanApplicationItems->sum('quantity_requested'); $totalIssued = $this->loanApplicationItems->sum('quantity_issued'); $totalReturned = $this->loanApplicationItems->sum('quantity_returned'); $originalStatus = $this->status; if ($totalIssued == 0 && $totalReturned == 0) { if ($this->status === self::STATUS_ISSUED || $this->status === self::STATUS_PARTIALLY_ISSUED || $this->status === self::STATUS_RETURNED) { } } elseif ($totalIssued > 0 && $totalReturned >= $totalIssued) { $this->status = self::STATUS_RETURNED; } elseif ($totalIssued > 0 && $totalReturned < $totalIssued) { if ($totalIssued >= $totalRequested) { $this->status = self::STATUS_ISSUED; } else { $this->status = self::STATUS_PARTIALLY_ISSUED; } } if ( $this->loan_end_date && $this->loan_end_date->isPast() && in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED]) ) { $this->status = self::STATUS_OVERDUE; } if ($this->isDirty('status')) { $this->save(); Log::info("LoanApplication ID {$this->id} overall status changed from '{$originalStatus}' to '{$this->status}' based on transaction item sums. Requested: {$totalRequested}, Issued: {$totalIssued}, Returned: {$totalReturned}."); } else { Log::info("LoanApplication ID {$this->id} overall status remains '{$this->status}'. Requested: {$totalRequested}, Issued: {$totalIssued}, Returned: {$totalReturned}."); } }
}
