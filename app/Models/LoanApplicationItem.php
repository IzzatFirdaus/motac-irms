<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * LoanApplicationItem Model.
 *
 * Represents a requested equipment type/quantity in a loan application.
 * Each item records the type of equipment, amount requested/approved/issued/returned,
 * and status within the application's approval and issuance workflow.
 *
 * @property int                             $id
 * @property int                             $loan_application_id
 * @property int|null                        $equipment_id
 * @property string                          $equipment_type
 * @property int                             $quantity_requested
 * @property int|null                        $quantity_approved
 * @property int                             $quantity_issued
 * @property int                             $quantity_returned
 * @property string                          $status
 * @property string|null                     $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class LoanApplicationItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Status constants for item approval and issuance/return workflow
    public const STATUS_PENDING_APPROVAL = 'pending_approval';

    public const STATUS_ITEM_APPROVED = 'item_approved';

    public const STATUS_ITEM_REJECTED = 'item_rejected';

    public const STATUS_AWAITING_ISSUANCE = 'awaiting_issuance';

    public const STATUS_FULLY_ISSUED = 'fully_issued';

    public const STATUS_PARTIALLY_ISSUED = 'partially_issued';

    public const STATUS_FULLY_RETURNED = 'fully_returned';

    public const STATUS_ITEM_CANCELLED = 'item_cancelled';

    // Labels for each status (used for UI/presentation)
    public const ITEM_STATUS_LABELS = [
        self::STATUS_PENDING_APPROVAL  => 'Menunggu Kelulusan (Item)',
        self::STATUS_ITEM_APPROVED     => 'Diluluskan (Item)',
        self::STATUS_ITEM_REJECTED     => 'Ditolak (Item)',
        self::STATUS_AWAITING_ISSUANCE => 'Menunggu Pengeluaran',
        self::STATUS_FULLY_ISSUED      => 'Telah Dikeluarkan Sepenuhnya',
        self::STATUS_PARTIALLY_ISSUED  => 'Telah Dikeluarkan Sebahagian',
        self::STATUS_FULLY_RETURNED    => 'Telah Dipulangkan Sepenuhnya',
        self::STATUS_ITEM_CANCELLED    => 'Dibatalkan (Item)',
    ];

    /**
     * Mass-assignable fields, must match migration and factory.
     */
    protected $fillable = [
        'loan_application_id',
        'equipment_id',
        'equipment_type',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'quantity_returned',
        'status',
        'notes',
    ];

    /**
     * Attribute casts for correct data types.
     */
    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved'  => 'integer',
        'quantity_issued'    => 'integer',
        'quantity_returned'  => 'integer',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    /**
     * Booted model hook.
     * - Sets default status and quantity values on creation.
     * - Cascade deletes related loan transaction items when deleted.
     */
    protected static function booted(): void
    {
        static::creating(function ($item): void {
            if (empty($item->status)) {
                $item->status = self::STATUS_PENDING_APPROVAL;
            }
            $item->quantity_issued   = $item->quantity_issued   ?? 0;
            $item->quantity_returned = $item->quantity_returned ?? 0;
        });

        static::deleting(function ($loanApplicationItem): void {
            DB::transaction(function () use ($loanApplicationItem): void {
                $loanApplicationItem->loanTransactionItems()->delete();
            });
        });
    }

    /**
     * Factory definition for this model.
     */
    protected static function newFactory(): LoanApplicationItemFactory
    {
        return LoanApplicationItemFactory::new();
    }

    // ---------------------
    // Relationships
    // ---------------------

    /**
     * The parent LoanApplication.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Related LoanTransactionItems (issue/return records for this request).
     */
    public function loanTransactionItems(): HasMany
    {
        return $this->hasMany(LoanTransactionItem::class, 'loan_application_item_id');
    }

    /**
     * Optionally, the specific Equipment assigned to this request (may be null).
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    // ---------------------
    // Accessors & Helpers
    // ---------------------

    /**
     * Get a human-readable label for the status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::ITEM_STATUS_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * Get a human-readable label for the equipment type.
     */
    public function getEquipmentTypeLabelAttribute(): string
    {
        return Equipment::getAssetTypeOptions()[$this->equipment_type] ?? Str::title(str_replace('_', ' ', (string) $this->equipment_type));
    }

    /**
     * Recalculate the issued and returned quantities based on related transaction items.
     * This ensures that the summary fields remain in sync with the actual transactions.
     */
    public function recalculateQuantities(): void
    {
        $this->loadMissing('loanTransactionItems.loanTransaction');

        // Sum quantities for 'issue' transactions with "issued" status
        $issuedQty = $this->loanTransactionItems
            ->where('loanTransaction.type', LoanTransaction::TYPE_ISSUE)
            ->where('status', LoanTransactionItem::STATUS_ITEM_ISSUED)
            ->sum('quantity_transacted');

        // Sum quantities for 'return' transactions, excluding those marked as lost
        $returnedQty = $this->loanTransactionItems
            ->where('loanTransaction.type', LoanTransaction::TYPE_RETURN)
            ->whereNotIn('status', [LoanTransactionItem::STATUS_ITEM_REPORTED_LOST])
            ->sum('quantity_transacted');

        $this->quantity_issued   = $issuedQty;
        $this->quantity_returned = $returnedQty;

        if ($this->isDirty(['quantity_issued', 'quantity_returned'])) {
            $this->save();
        }
    }
}
