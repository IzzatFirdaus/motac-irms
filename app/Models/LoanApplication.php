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
    public function loanApplicationItems(): HasMany { return $this->hasMany(LoanApplicationItem::class, 'loan_application_id'); }
    public function items(): HasMany { return $this->loanApplicationItems(); }
    public function loanTransactions(): HasMany { return $this->hasMany(LoanTransaction::class, 'loan_application_id'); }
    public function approvals(): MorphMany { return $this->morphMany(Approval::class, 'approvable'); }

    // --- ACCESSORS ---
    public function getStatusLabelAttribute(): string
    {
        return self::$STATUS_OPTIONS[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    // --- HELPER METHODS ---
    public function isOverdue(): bool {
        // A loan is only considered overdue if it's currently active (issued).
        if (!in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_ISSUED])) {
            return false;
        }
        // If the end date has passed and there are still items not returned.
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

        if ($totalIssued <= 0) {
            // No items have been issued yet.
            $newStatus = self::STATUS_APPROVED;
        } elseif ($totalReturned >= $totalIssued && $totalIssued > 0) {
            // All issued items have been returned.
            $newStatus = self::STATUS_RETURNED;
        } elseif ($totalReturned > 0 && $totalReturned < $totalIssued) {
            $newStatus = self::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION;
        } elseif ($totalIssued >= $totalApproved && $totalApproved > 0) {
            $newStatus = self::STATUS_ISSUED;
        } elseif ($totalIssued > 0 && $totalIssued < $totalApproved) {
            $newStatus = self::STATUS_PARTIALLY_ISSUED;
        }

        // **THE FIX**: This logic ensures a returned loan cannot be marked as overdue.
        // The overdue check now only runs if the loan is still active.
        if ($newStatus !== self::STATUS_RETURNED && $this->isOverdue()) {
            $newStatus = self::STATUS_OVERDUE;
        }

        if ($newStatus !== $originalStatus) {
            $this->update(['status' => $newStatus]);
            Log::info("LoanApplication ID {$this->id} overall status changed from '{$originalStatus}' to '{$newStatus}'.");
        }
    }
}
