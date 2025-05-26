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
        // $auditUserId = User::orderBy('id')->first()?->id ?? User::factory()->create()->id; // Blameable

        $loanTransaction = LoanTransaction::inRandomOrder()->first() ?? LoanTransaction::factory()->create();
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE) // Prefer available equipment
            ->inRandomOrder()->first() ?? Equipment::factory()->available()->create();


        $loanApplicationItemId = null;
        if ($loanTransaction->loanApplication) {
            // Try to find a matching application item type if possible, or a random one
            $appItem = $loanTransaction->loanApplication
                ->applicationItems()
                ->where('equipment_type', $equipment->asset_type) // Match asset type
                ->inRandomOrder()
                ->first();
            $loanApplicationItemId = $appItem?->id;

            if (!$loanApplicationItemId) { // Fallback to any item from the application
                 $loanApplicationItemId = $loanTransaction->loanApplication->applicationItems()->inRandomOrder()->first()?->id;
            }
        }


        $itemStatuses = LoanTransactionItem::getStatusesList(); // Uses getter [cite: 7]
        $itemConditionStatuses = LoanTransactionItem::getConditionStatusesList(); // Uses getter [cite: 7]

        $chosenStatusKey = $this->faker->randomElement(array_keys($itemStatuses ?: [LoanTransactionItem::VAL_STATUS_ISSUED]));
        $conditionOnReturnKey = null;

        if (in_array($chosenStatusKey, [ // Check against actual VAL_ constants [cite: 7]
            LoanTransactionItem::VAL_STATUS_RETURNED_GOOD,
            LoanTransactionItem::VAL_STATUS_RETURNED_MINOR_DAMAGE, // Assuming this constant exists
            LoanTransactionItem::VAL_STATUS_RETURNED_MAJOR_DAMAGE, // Assuming this constant exists
            LoanTransactionItem::VAL_STATUS_REPORTED_LOST,       // Assuming this constant exists
            LoanTransactionItem::VAL_STATUS_UNSERVICEABLE,       // Assuming this constant exists
        ])) {
            // Only set condition_on_return if the status implies a return event
            if ($chosenStatusKey === LoanTransactionItem::VAL_STATUS_RETURNED_GOOD) {
                $conditionOnReturnKey = Equipment::CONDITION_GOOD; // Map to Equipment condition
            } elseif ($chosenStatusKey === LoanTransactionItem::VAL_STATUS_RETURNED_MINOR_DAMAGE) {
                $conditionOnReturnKey = Equipment::CONDITION_MINOR_DAMAGE;
            } elseif ($chosenStatusKey === LoanTransactionItem::VAL_STATUS_RETURNED_MAJOR_DAMAGE) {
                $conditionOnReturnKey = Equipment::CONDITION_MAJOR_DAMAGE;
            } elseif ($chosenStatusKey === LoanTransactionItem::VAL_STATUS_UNSERVICEABLE) {
                $conditionOnReturnKey = Equipment::CONDITION_UNSERVICEABLE;
            } else { // For reported_lost or other general returned statuses
                 $conditionOnReturnKey = Arr::random(array_keys($itemConditionStatuses ?: [Equipment::CONDITION_FAIR])); // Fallback condition
            }
        }


        return [
            'loan_transaction_id' => $loanTransaction->id,
            'equipment_id' => $equipment->id,
            'loan_application_item_id' => $loanApplicationItemId, // As per design (Section 4.3)
            'quantity_transacted' => 1, // Typically 1 for specific serialized equipment
            'status' => $chosenStatusKey, // As per design (Section 4.3)
            'condition_on_return' => $conditionOnReturnKey, // As per design (Section 4.3), should match Equipment model's condition enum values
            'accessories_checklist_issue' => $loanTransaction->type == LoanTransaction::TYPE_ISSUE ? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 3))) : null, // As per design (Section 4.3)
            'accessories_checklist_return' => $loanTransaction->type == LoanTransaction::TYPE_RETURN ? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 3))) : null, // As per design (Section 4.3)
            'item_notes' => $this->faker->optional(0.2)->sentence, // As per design (Section 4.3)
            // 'created_by', 'updated_by' handled by BlameableObserver
        ];
    }

    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::VAL_STATUS_ISSUED,
            'condition_on_return' => null,
            'accessories_checklist_issue' => json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(1, 3))), // Ensure some accessories on issue
            'accessories_checklist_return' => null,
        ]);
    }

    public function returnedGood(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::VAL_STATUS_RETURNED_GOOD, 
            'condition_on_return' => Equipment::CONDITION_GOOD, // Matches Equipment condition
            'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(1, 3))),
        ]);
    }
     public function returnedDamaged(): static
    {
        $damageConditions = [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE, Equipment::CONDITION_UNSERVICEABLE];
        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::VAL_STATUS_RETURNED_DAMAGED, // Assuming this VAL_ constant exists
            'condition_on_return' => Arr::random($damageConditions),
            'item_notes' => $attributes['item_notes'] ?? $this->faker->sentence, // Add a note for damage
            'accessories_checklist_return' => json_encode($this->faker->randomElements(['Power Adapter', 'Mouse', 'Bag'], $this->faker->numberBetween(0, 2))), // Maybe some accessories missing/damaged
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

    public function forLoanApplicationItem(LoanApplicationItem|int|null $loanAppItem): static // Allow null
    {
        return $this->state(['loan_application_item_id' => $loanAppItem instanceof LoanApplicationItem ? $loanAppItem->id : $loanAppItem]);
    }
}
