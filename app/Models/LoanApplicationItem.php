<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanApplicationItemFactory; // Correct import for the specific factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\LoanTransaction; // Import if LoanTransaction constants are used directly
use App\Models\LoanTransactionItem as TransactionItemModel; // Alias if LoanTransactionItem is too verbose or conflicts

/**
 * Loan Application Item Model.
 * Represents a type of equipment and quantity requested in a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued
 * @property int $quantity_returned
 * @property string $status
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection|TransactionItemModel[] $loanTransactionItems
 * @property-read string $status_label
 */
class LoanApplicationItem extends Model
{
  use HasFactory;
  use SoftDeletes;

  public const STATUS_ITEM_REQUESTED = 'requested';
  public const STATUS_ITEM_APPROVED = 'approved';
  public const STATUS_ITEM_REJECTED = 'rejected';
  public const STATUS_ITEM_PARTIALLY_ALLOCATED = 'partially_allocated';
  public const STATUS_ITEM_ALLOCATED = 'allocated';
  public const STATUS_ITEM_UNAVAILABLE = 'unavailable';
  public const STATUS_ITEM_ISSUED = 'issued';
  public const STATUS_ITEM_RETURNED = 'returned';
  public const STATUS_ITEM_CANCELLED = 'cancelled';

  public const ITEM_STATUS_LABELS = [
    self::STATUS_ITEM_REQUESTED => 'Dimohon',
    self::STATUS_ITEM_APPROVED => 'Diluluskan (Item)',
    self::STATUS_ITEM_REJECTED => 'Ditolak (Item)',
    self::STATUS_ITEM_PARTIALLY_ALLOCATED => 'Diperuntukkan Sebahagian',
    self::STATUS_ITEM_ALLOCATED => 'Diperuntukkan',
    self::STATUS_ITEM_UNAVAILABLE => 'Tidak Tersedia',
    self::STATUS_ITEM_ISSUED => 'Telah Dikeluarkan',
    self::STATUS_ITEM_RETURNED => 'Telah Dipulangkan',
    self::STATUS_ITEM_CANCELLED => 'Dibatalkan (Item)',
  ];

  protected $fillable = [
    'loan_application_id',
    'equipment_type',
    'quantity_requested',
    'quantity_approved',
    'quantity_issued',
    'quantity_returned',
    'status',
    'notes',
  ];

  protected $casts = [
    'quantity_requested' => 'integer',
    'quantity_approved' => 'integer',
    'quantity_issued' => 'integer',
    'quantity_returned' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  protected static function booted(): void
  {
    static::creating(function ($item) {
      if (empty($item->status)) {
        $item->status = self::STATUS_ITEM_REQUESTED;
      }
      $item->quantity_issued = $item->quantity_issued ?? 0;
      $item->quantity_returned = $item->quantity_returned ?? 0;
    });

    static::deleting(function ($loanApplicationItem) {
      DB::transaction(function () use ($loanApplicationItem) {
        $loanApplicationItem->loanTransactionItems()->delete();
      });
    });
  }

  /**
   * Define the factory for the model.
   */
  protected static function newFactory(): LoanApplicationItemFactory // Corrected return type
  {
    return LoanApplicationItemFactory::new();
  }

  // Relationships

  public function loanApplication(): BelongsTo
  {
    return $this->belongsTo(LoanApplication::class);
  }

  public function loanTransactionItems(): HasMany
  {
    return $this->hasMany(TransactionItemModel::class, 'loan_application_item_id'); // Using alias
  }

  // Accessors

  public function getStatusLabelAttribute(): string
  {
    return self::ITEM_STATUS_LABELS[$this->status] ?? Str::title(str_replace('_', ' ', (string) $this->status));
  }

  public function equipment()
  {
    return $this->belongsTo(Equipment::class);
  }


  // Helper Methods

  public function recalculateQuantities(): void
  {
    $this->loadMissing('loanTransactionItems.loanTransaction');

    $issuedQty = 0;
    $returnedQty = 0;

    foreach ($this->loanTransactionItems as $transactionItem) {
      if ($transactionItem->loanTransaction) {
        if (
          $transactionItem->loanTransaction->type === LoanTransaction::TYPE_ISSUE &&
          // Assuming STATUS_ITEM_ISSUED is a constant in your LoanTransactionItem model
          $transactionItem->status === TransactionItemModel::STATUS_ITEM_ISSUED
        ) {
          $issuedQty += $transactionItem->quantity_transacted;
        } elseif (
          $transactionItem->loanTransaction->type === LoanTransaction::TYPE_RETURN &&
          // Assuming $RETURN_APPLICABLE_STATUSES is a static array in LoanTransactionItem
          is_array(TransactionItemModel::$RETURN_APPLICABLE_STATUSES) &&
          in_array($transactionItem->status, TransactionItemModel::$RETURN_APPLICABLE_STATUSES)
        ) {
          // Assuming STATUS_ITEM_REPORTED_LOST is a constant in LoanTransactionItem
          if (!in_array($transactionItem->status, [TransactionItemModel::STATUS_ITEM_REPORTED_LOST])) {
            $returnedQty += $transactionItem->quantity_transacted;
          }
        }
      }
    }

    $this->quantity_issued = $issuedQty;
    $this->quantity_returned = $returnedQty;

    if ($this->isDirty(['quantity_issued', 'quantity_returned'])) {
      $this->save();
      // if ($this->loanApplication) {
      //     $this->loanApplication->updateOverallStatus();
      // }
    }
  }
}
