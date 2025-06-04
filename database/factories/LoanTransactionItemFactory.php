<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Arr; // Added for Arr::random

class LoanTransactionItemFactory extends EloquentFactory
{
    protected $model = LoanTransactionItem::class;

    public function definition(): array
    {
        $loanTransaction = LoanTransaction::inRandomOrder()->first() ?? LoanTransaction::factory()->create();
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->inRandomOrder()->first() ?? Equipment::factory()->available()->create();

        $loanApplicationItemId = null;
        if ($loanTransaction->loanApplication) {
            $appItem = $loanTransaction->loanApplication
                ->loanApplicationItems()
                ->where('equipment_type', $equipment->asset_type)
                ->inRandomOrder()
                ->first();
            $loanApplicationItemId = $appItem?->id;

            if (!$loanApplicationItemId) {
                $loanApplicationItemId = $loanTransaction->loanApplication->loanApplicationItems()->inRandomOrder()->first()?->id;
            }
        }

        $itemStatuses = LoanTransactionItem::getStatusesList();
        $itemConditionStatusesKeys = LoanTransactionItem::getConditionStatusesList(); // Get keys for random selection

        // Use correct constant from Model as fallback
        $chosenStatusKey = $this->faker->randomElement($itemStatuses ?: [LoanTransactionItem::STATUS_ITEM_ISSUED]);
        $conditionOnReturnKey = null;

        // Use correct constants from Model
        if (in_array($chosenStatusKey, [
            LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_REPORTED_LOST,
            LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN, // Corrected Constant
        ])) {
            if ($chosenStatusKey === LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD) {
                $conditionOnReturnKey = Equipment::CONDITION_GOOD;
            } elseif ($chosenStatusKey === LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE) {
                $conditionOnReturnKey = Equipment::CONDITION_MINOR_DAMAGE;
            } elseif ($chosenStatusKey === LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE) {
                $conditionOnReturnKey = Equipment::CONDITION_MAJOR_DAMAGE;
            } elseif ($chosenStatusKey === LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN) { // Corrected Constant
                $conditionOnReturnKey = Equipment::CONDITION_UNSERVICEABLE;
            } elseif ($chosenStatusKey === LoanTransactionItem::STATUS_ITEM_REPORTED_LOST) {
                $conditionOnReturnKey = Equipment::CONDITION_LOST; // Explicitly map lost status
            } else {
                // Fallback condition for other returned statuses if any; ensure $itemConditionStatusesKeys is not empty
                $conditionOnReturnKey = !empty($itemConditionStatusesKeys)
                    ? Arr::random($itemConditionStatusesKeys)
                    : Equipment::CONDITION_FAIR; // Default fallback
            }
        }

        return [
            'loan_transaction_id' => $loanTransaction->id,
            'equipment_id' => $equipment->id,
            'loan_application_item_id' => $loanApplicationItemId,
            'quantity_transacted' => 1,
            'status' => $chosenStatusKey,
            'condition_on_return' => $conditionOnReturnKey,
            'accessories_checklist_issue' => $loanTransaction->type == LoanTransaction::TYPE_ISSUE ? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 3))) : null,
            'accessories_checklist_return' => $loanTransaction->type == LoanTransaction::TYPE_RETURN ? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 3))) : null,
            'item_notes' => $this->faker->optional(0.2)->sentence,
        ];
    }

    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_ISSUED, // Corrected Constant
            'condition_on_return' => null,
            'accessories_checklist_issue' => json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(1, 3))),
            'accessories_checklist_return' => null,
        ]);
    }

    public function returnedGood(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD, // Corrected Constant
            'condition_on_return' => Equipment::CONDITION_GOOD,
            'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(1, 3))),
        ]);
    }

    public function returnedDamaged(): static
    {
        $damageConditions = [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE];
        // Use a defined damage status from the model
        $itemDamageStatuses = [
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE
        ];
        return $this->state(fn (array $attributes) => [
            'status' => Arr::random($itemDamageStatuses), // Pick a specific damage status
            'condition_on_return' => Arr::random($damageConditions),
            'item_notes' => $attributes['item_notes'] ?? $this->faker->sentence,
            'accessories_checklist_return' => json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 2))),
        ]);
    }

    public function forTransaction(LoanTransaction|int $loanTransaction): static
    {
        return $this->state(['loan_transaction_id' => $loanTransaction instanceof LoanTransaction ? $loanTransaction->id : $loanTransaction]);
    }

    public function forEquipment(Equipment|int $equipment): static
    {
        return $this->state(['equipment_id' => $equipment instanceof Equipment ? $equipment->id : $equipment]);
    }

    public function forLoanApplicationItem(LoanApplicationItem|int|null $loanAppItem): static
    {
        return $this->state(['loan_application_item_id' => $loanAppItem instanceof LoanApplicationItem ? $loanAppItem->id : $loanAppItem]);
    }
}
