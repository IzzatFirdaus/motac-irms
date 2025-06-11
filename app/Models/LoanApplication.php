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
    use HasFactory;
    use SoftDeletes;
    use Blameable;

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
        self::STATUS_COMPLETED => 'Selesai (Lengkap & Dipulangkan)',
    ];

    // --- PROPERTIES ---
    protected $table = 'loan_applications';
    protected $fillable = ['user_id', 'responsible_officer_id', 'supporting_officer_id', 'purpose', 'location', 'return_location', 'loan_start_date', 'loan_end_date', 'status', 'rejection_reason', 'applicant_confirmation_timestamp', 'submitted_at', 'approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'cancelled_by', 'cancelled_at', 'admin_notes', 'current_approval_officer_id', 'current_approval_stage'];
    protected $casts = ['loan_start_date' => 'datetime', 'loan_end_date' => 'datetime', 'applicant_confirmation_timestamp' => 'datetime', 'submitted_at' => 'datetime', 'approved_at' => 'datetime', 'rejected_at' => 'datetime', 'cancelled_at' => 'datetime'];
    protected $attributes = ['status' => self::STATUS_DRAFT];

    protected static function newFactory(): LoanApplicationFactory
    {
        return LoanApplicationFactory::new();
    }

    // --- RELATIONSHIPS ---
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function responsibleOfficer(): BelongsTo { return $this->belongsTo(User::class, 'responsible_officer_id'); }
    public function supportingOfficer(): BelongsTo { return $this->belongsTo(User::class, 'supporting_officer_id'); }
    public function currentApprovalOfficer(): BelongsTo { return $this->belongsTo(User::class, 'current_approval_officer_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedBy(): BelongsTo { return $this->belongsTo(User::class, 'rejected_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }
    public function loanApplicationItems(): HasMany { return $this->hasMany(LoanApplicationItem::class, 'loan_application_id'); }
    public function items(): HasMany { return $this->loanApplicationItems(); }
    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }

    // --- ACCESSORS & STATIC METHODS ---
    public function getStatusLabelAttribute(): string
    {
        return self::$STATUS_OPTIONS[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    public static function getStatusOptions(): array
    {
        return self::$STATUS_OPTIONS;
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'text-bg-secondary',
            self::STATUS_PENDING_SUPPORT, self::STATUS_PENDING_APPROVER_REVIEW, self::STATUS_PENDING_BPM_REVIEW => 'text-bg-warning',
            self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_ISSUED => 'text-bg-info',
            self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_OVERDUE => 'text-bg-danger',
            self::STATUS_RETURNED, self::STATUS_COMPLETED => 'text-bg-success',
            self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'text-bg-primary',
            default => 'text-bg-dark',
        };
    }

    // --- HELPER METHODS ---
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }

    /**
     * Check if the application status is rejected.
     * This method was added to resolve the 'BadMethodCallException'.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBeReturned(): bool { return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED, self::STATUS_OVERDUE]); }

    public function isOverdue(): bool {
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
            // Logic for when items have been issued.
            if ($totalReturned >= $totalIssued) {
                // ALL issued items have been returned.
                $newStatus = self::STATUS_RETURNED;
            } elseif ($totalReturned > 0) {
                // SOME, but not all, issued items have been returned.
                $newStatus = self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION;
            } elseif ($totalIssued >= $totalApproved) {
                // ALL approved items have been issued.
                $newStatus = self::STATUS_ISSUED;
            } else {
                // SOME, but not all, approved items have been issued.
                $newStatus = self::STATUS_PARTIALLY_ISSUED;
            }
        } else {
            // Logic for when NO items have been issued yet.
            if ($this->status !== self::STATUS_REJECTED && $this->status !== self::STATUS_CANCELLED) {
                 $newStatus = self::STATUS_APPROVED;
            }
        }

        if ($newStatus !== self::STATUS_RETURNED && $this->isOverdue()) {
            $newStatus = self::STATUS_OVERDUE;
        }

        if ($newStatus !== $originalStatus) {
            $this->update(['status' => $newStatus]);
            Log::info("LoanApplication ID {$this->id} overall status changed from '{$originalStatus}' to '{$newStatus}'.");
        }
    }
}
