<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User; // Not directly used here but good practice for factories
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Arr;

class LoanTransactionItemFactory extends EloquentFactory
{
    protected $model = LoanTransactionItem::class;

    public function definition(): array
    {
        $loanTransaction = LoanTransaction::inRandomOrder()->first() ?? LoanTransaction::factory()->create();
        // Ensure equipment is actually available if this factory is used to create an 'issued' item's initial record
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->inRandomOrder()->first();

        // If no available equipment, create one that is available.
        if (!$equipment) {
            $equipment = Equipment::factory()->available()->create();
        }

        $loanApplicationItemId = null;
        if ($loanTransaction->loanApplication) {
            // Try to find a matching app item first
            $appItem = $loanTransaction->loanApplication
                ->loanApplicationItems() // Ensure this relationship exists and is correct
                ->where('equipment_type', $equipment->asset_type) // Match by equipment type
                ->inRandomOrder()
                ->first();
            $loanApplicationItemId = $appItem?->id;

            // Fallback: if no matching type, just pick any app item from the application
            if (!$loanApplicationItemId && $loanTransaction->loanApplication->loanApplicationItems()->count() > 0) {
                $loanApplicationItemId = $loanTransaction->loanApplication->loanApplicationItems()->inRandomOrder()->first()?->id;
            }
        }

        $itemStatuses = LoanTransactionItem::getStatusesList(); // e.g., ['issued', 'returned_good', ...]
        $chosenStatus = $this->faker->randomElement(
            !empty($itemStatuses) ? $itemStatuses : [LoanTransactionItem::STATUS_ITEM_ISSUED]
        );

        $conditionOnReturn = null;
        $accessoriesIssue = null;
        $accessoriesReturn = null;

        if ($loanTransaction->type == LoanTransaction::TYPE_ISSUE) {
            $chosenStatus = LoanTransactionItem::STATUS_ITEM_ISSUED; // Force status for issue transaction
            $accessoriesIssue = $this->faker->randomElements(config('motac.loan_accessories_list', ['Power Adapter', 'Mouse', 'Bag']), $this->faker->numberBetween(0, 3));
        } elseif ($loanTransaction->type == LoanTransaction::TYPE_RETURN) {
            // For a return transaction, determine the condition based on the item's status
            $accessoriesReturn = $this->faker->randomElements(config('motac.loan_accessories_list', ['Power Adapter', 'Mouse', 'Bag']), $this->faker->numberBetween(0, 3));

            // If status isn't explicitly set by a state, derive condition
            switch ($chosenStatus) {
                case LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD:
                    $conditionOnReturn = Equipment::CONDITION_GOOD;
                    break;
                case LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE:
                    $conditionOnReturn = Equipment::CONDITION_MINOR_DAMAGE;
                    break;
                case LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE:
                    $conditionOnReturn = Equipment::CONDITION_MAJOR_DAMAGE;
                    break;
                case LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN:
                    $conditionOnReturn = Equipment::CONDITION_UNSERVICEABLE;
                    break;
                case LoanTransactionItem::STATUS_ITEM_REPORTED_LOST:
                    $conditionOnReturn = Equipment::CONDITION_LOST;
                    break;
                case LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION:
                case LoanTransactionItem::STATUS_ITEM_RETURNED: // General returned
                    // For pending or general returned, pick a plausible condition or leave null if inspection sets it
                    $validConditionKeys = LoanTransactionItem::getConditionStatusesList(); // Method exists in LTI model
                    $conditionOnReturn = !empty($validConditionKeys) ? Arr::random($validConditionKeys) : Equipment::CONDITION_FAIR;
                    break;
                default: // For 'issued' or other non-return statuses, condition_on_return should be null
                    $conditionOnReturn = null;
                    break;
            }
        }


        return [
            'loan_transaction_id' => $loanTransaction->id,
            'equipment_id' => $equipment->id,
            'loan_application_item_id' => $loanApplicationItemId,
            'quantity_transacted' => 1,
            'status' => $chosenStatus,
            'condition_on_return' => $conditionOnReturn,
            // Casts in model handle JSON encoding/decoding. Factory should provide arrays.
            'accessories_checklist_issue' => $accessoriesIssue,
            'accessories_checklist_return' => $accessoriesReturn,
            'item_notes' => $this->faker->optional(0.2)->sentence,
        ];
    }

    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_ISSUED,
            'condition_on_return' => null,
            'accessories_checklist_issue' => $this->faker->randomElements(config('motac.loan_accessories_list', ['Power Adapter', 'Mouse', 'Bag']), $this->faker->numberBetween(1, 3)),
            'accessories_checklist_return' => null,
        ]);
    }

    public function returnedGood(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
            'condition_on_return' => Equipment::CONDITION_GOOD,
            // If accessories_checklist_issue was set in attributes, use it, else generate new
            'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? $this->faker->randomElements(config('motac.loan_accessories_list', ['Power Adapter', 'Mouse', 'Bag']), $this->faker->numberBetween(1, 3)),
        ]);
    }

    public function returnedDamaged(): static
    {
        $damageConditions = [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE];
        $itemDamageStatuses = [
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE
        ];
        return $this->state(fn (array $attributes) => [
            'status' => Arr::random($itemDamageStatuses),
            'condition_on_return' => Arr::random($damageConditions),
            'item_notes' => $attributes['item_notes'] ?? $this->faker->sentence,
            'accessories_checklist_return' => $this->faker->randomElements(config('motac.loan_accessories_list', ['Power Adapter', 'Mouse', 'Bag']), $this->faker->numberBetween(0, 2)),
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
