<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Arr;

class LoanTransactionItemFactory extends EloquentFactory
{
    protected $model = LoanTransactionItem::class;

    public function definition(): array
    {
        $msFaker = \Faker\Factory::create('ms_MY');

        $loanTransaction = LoanTransaction::inRandomOrder()->first() ?? LoanTransaction::factory()->create();
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->inRandomOrder()->first();

        if (! $equipment) {
            $equipment = Equipment::factory()->available()->create();
        }

        $loanApplicationItemId = null;
        if ($loanTransaction->loanApplication) {
            $appItem = $loanTransaction->loanApplication
                ->loanApplicationItems()
                ->where('equipment_type', $equipment->asset_type)
                ->inRandomOrder()
                ->first();
            $loanApplicationItemId = $appItem?->id;

            if (! $loanApplicationItemId && $loanTransaction->loanApplication->loanApplicationItems()->count() > 0) {
                $loanApplicationItemId = $loanTransaction->loanApplication->loanApplicationItems()->inRandomOrder()->first()?->id;
            }
        }

        $itemStatuses = LoanTransactionItem::getStatusesList();
        $chosenStatus = $this->faker->randomElement(
            ! empty($itemStatuses) ? $itemStatuses : [LoanTransactionItem::STATUS_ITEM_ISSUED]
        );
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $conditionOnReturn = null;
        $accessoriesIssue = null;
        $accessoriesReturn = null;

        if ($loanTransaction->type == LoanTransaction::TYPE_ISSUE) {
            $chosenStatus = LoanTransactionItem::STATUS_ITEM_ISSUED;
            $accessoriesIssue = $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 3));
        } elseif ($loanTransaction->type == LoanTransaction::TYPE_RETURN) {
            $accessoriesReturn = $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 3));
            switch ($chosenStatus) {
                case LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD:
                    $conditionOnReturn = Equipment::CONDITION_GOOD;
                    break;
                    // ... other cases
                default: $conditionOnReturn = null;
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
            'accessories_checklist_issue' => $accessoriesIssue,
            'accessories_checklist_return' => $accessoriesReturn,
            'item_notes' => $msFaker->optional(0.2)->sentence,
        ];
    }

    public function issued(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_ISSUED,
            'condition_on_return' => null,
            'accessories_checklist_issue' => $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            'accessories_checklist_return' => null,
        ]);
    }

    public function returnedGood(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);

        return $this->state(fn (array $attributes) => [
            'status' => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
            'condition_on_return' => Equipment::CONDITION_GOOD,
            'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
        ]);
    }

    public function returnedDamaged(): static
    {
        $msFaker = \Faker\Factory::create('ms_MY');
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $damageConditions = [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE];
        $itemDamageStatuses = [
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
        ];

        return $this->state(fn (array $attributes) => [
            'status' => Arr::random($itemDamageStatuses),
            'condition_on_return' => Arr::random($damageConditions),
            'item_notes' => $attributes['item_notes'] ?? $msFaker->sentence,
            'accessories_checklist_return' => $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 2)),
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
