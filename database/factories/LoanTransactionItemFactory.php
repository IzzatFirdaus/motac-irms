<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Optimized Factory for LoanTransactionItem model.
 *
 * - Uses static caches for related model IDs to minimize repeated DB queries.
 * - Does NOT create related models in definition() (assumes dependencies seeded first).
 * - Foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with seeders that ensure all referenced models exist before creating transaction items.
 */
class LoanTransactionItemFactory extends Factory
{
    protected $model = LoanTransactionItem::class;

    public function definition(): array
    {
        // Static caches for related model IDs to reduce DB queries
        static $transactionIds, $equipmentIds, $loanAppItemIds, $userIds;

        if (!isset($transactionIds)) {
            $transactionIds = LoanTransaction::pluck('id')->all();
        }
        if (!isset($equipmentIds)) {
            $equipmentIds = Equipment::pluck('id')->all();
        }
        if (!isset($loanAppItemIds)) {
            $loanAppItemIds = LoanApplicationItem::pluck('id')->all();
        }
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick random related IDs or null if none exist
        $loanTransactionId = !empty($transactionIds) ? Arr::random($transactionIds) : null;
        $equipmentId = !empty($equipmentIds) ? Arr::random($equipmentIds) : null;
        $loanAppItemId = !empty($loanAppItemIds) ? Arr::random($loanAppItemIds) : null;
        $auditUserId = !empty($userIds) ? Arr::random($userIds) : null;

        // Status options from model constants (fallback if not defined)
        $itemStatuses = [
            LoanTransactionItem::STATUS_ITEM_ISSUED ?? 'issued',
            LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD ?? 'returned_good',
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE ?? 'returned_minor_damage',
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE ?? 'returned_major_damage',
            LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN ?? 'unserviceable_on_return',
        ];
        $chosenStatus = $this->faker->randomElement($itemStatuses);

        // Accessories config (can be set via config or fallback)
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        // Determine condition on return if status is a return-type
        $conditionOnReturn = null;
        if ($chosenStatus === LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD ?? 'returned_good') {
            $conditionOnReturn = Equipment::CONDITION_GOOD ?? 'good';
        } elseif ($chosenStatus === LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE ?? 'returned_minor_damage') {
            $conditionOnReturn = Equipment::CONDITION_MINOR_DAMAGE ?? 'minor_damage';
        } elseif ($chosenStatus === LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE ?? 'returned_major_damage') {
            $conditionOnReturn = Equipment::CONDITION_MAJOR_DAMAGE ?? 'major_damage';
        } elseif ($chosenStatus === LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN ?? 'unserviceable_on_return') {
            $conditionOnReturn = Equipment::CONDITION_UNSERVICEABLE ?? 'unserviceable';
        }

        // Use Malaysian faker for notes
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Timestamps
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? $this->faker->dateTimeBetween($updatedAt, 'now') : null;

        return [
            'loan_transaction_id'         => $loanTransactionId,
            'equipment_id'                => $equipmentId,
            'loan_application_item_id'    => $loanAppItemId,
            'quantity_transacted'         => 1,
            'status'                      => $chosenStatus,
            'condition_on_return'         => $conditionOnReturn,
            'accessories_checklist_issue' => $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            'accessories_checklist_return'=> $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 3)),
            'item_notes'                  => $msFaker->optional(0.2)->sentence,
            'created_by'                  => $auditUserId,
            'updated_by'                  => $auditUserId,
            'deleted_by'                  => $isDeleted ? $auditUserId : null,
            'created_at'                  => $createdAt,
            'updated_at'                  => $updatedAt,
            'deleted_at'                  => $deletedAt,
        ];
    }

    /**
     * State for items marked as issued (for issue transactions).
     */
    public function issued(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        return $this->state([
            'status' => LoanTransactionItem::STATUS_ITEM_ISSUED ?? 'issued',
            'condition_on_return' => null,
            'accessories_checklist_issue' => $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            'accessories_checklist_return' => null,
        ]);
    }

    /**
     * State for items returned in good condition (for return transactions).
     */
    public function returnedGood(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        return $this->state(function (array $attributes) use ($accessories) {
            return [
                'status' => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD ?? 'returned_good',
                'condition_on_return' => Equipment::CONDITION_GOOD ?? 'good',
                'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            ];
        });
    }

    /**
     * State for items returned with damage (for return transactions).
     */
    public function returnedDamaged(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $damageStatuses = [
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE ?? 'returned_minor_damage',
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE ?? 'returned_major_damage',
            LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN ?? 'unserviceable_on_return',
        ];
        $damageConditions = [
            Equipment::CONDITION_MINOR_DAMAGE ?? 'minor_damage',
            Equipment::CONDITION_MAJOR_DAMAGE ?? 'major_damage',
            Equipment::CONDITION_UNSERVICEABLE ?? 'unserviceable',
        ];
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }
        return $this->state(function (array $attributes) use ($msFaker, $accessories, $damageStatuses, $damageConditions) {
            return [
                'status' => Arr::random($damageStatuses),
                'condition_on_return' => Arr::random($damageConditions),
                'item_notes' => $attributes['item_notes'] ?? $msFaker->sentence,
                'accessories_checklist_return' => $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 2)),
            ];
        });
    }

    /**
     * Link to a specific transaction.
     */
    public function forTransaction(LoanTransaction|int $loanTransaction): static
    {
        return $this->state([
            'loan_transaction_id' => $loanTransaction instanceof LoanTransaction ? $loanTransaction->id : $loanTransaction,
        ]);
    }

    /**
     * Link to a specific equipment.
     */
    public function forEquipment(Equipment|int $equipment): static
    {
        return $this->state([
            'equipment_id' => $equipment instanceof Equipment ? $equipment->id : $equipment,
        ]);
    }

    /**
     * Link to a specific loan application item.
     */
    public function forLoanApplicationItem(LoanApplicationItem|int|null $loanAppItem): static
    {
        return $this->state([
            'loan_application_item_id' => $loanAppItem instanceof LoanApplicationItem ? $loanAppItem->id : $loanAppItem,
        ]);
    }

    /**
     * Mark the item as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = !empty($userIds) ? Arr::random($userIds) : null;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
