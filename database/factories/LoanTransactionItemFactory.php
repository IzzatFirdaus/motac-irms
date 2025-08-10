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
 * Factory for the LoanTransactionItem model.
 *
 * Generates records that represent the linkage of a specific equipment
 * to a loan transaction (issue or return). Handles all foreign keys,
 * audit fields, and soft-delete fields as per migration and model.
 */
class LoanTransactionItemFactory extends Factory
{
    protected $model = LoanTransactionItem::class;

    public function definition(): array
    {
        // Use Malaysian locale for sample data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Find or create a transaction
        $loanTransaction = LoanTransaction::inRandomOrder()->first() ?? LoanTransaction::factory()->create();

        // Find or create a suitable equipment (preferably available)
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->inRandomOrder()->first() ?? Equipment::factory()->available()->create();

        // Find or create a loan application item linked to this transaction/equipment if possible
        $loanApplicationItemId = null;
        if ($loanTransaction->loanApplication) {
            $appItem = $loanTransaction->loanApplication
                ->loanApplicationItems()
                ->where('equipment_type', $equipment->asset_type)
                ->inRandomOrder()
                ->first();
            $loanApplicationItemId = $appItem?->id;

            if (!$loanApplicationItemId && $loanTransaction->loanApplication->loanApplicationItems()->count() > 0) {
                $loanApplicationItemId = $loanTransaction->loanApplication->loanApplicationItems()->inRandomOrder()->first()?->id;
            }
        }

        // Get all possible statuses from the model, or sensible defaults
        $itemStatuses = method_exists(LoanTransactionItem::class, 'getStatusesList')
            ? LoanTransactionItem::getStatusesList()
            : [
                LoanTransactionItem::STATUS_ITEM_ISSUED,
                LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
                LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
                LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            ];
        $chosenStatus = $this->faker->randomElement($itemStatuses);

        // Accessories for issue/return
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $conditionOnReturn = null;
        $accessoriesIssue = null;
        $accessoriesReturn = null;

        // Set fields depending on transaction type (issue or return)
        if ($loanTransaction->type === LoanTransaction::TYPE_ISSUE) {
            $chosenStatus = LoanTransactionItem::STATUS_ITEM_ISSUED;
            $accessoriesIssue = $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 3));
        } elseif ($loanTransaction->type === LoanTransaction::TYPE_RETURN) {
            $accessoriesReturn = $this->faker->randomElements($accessories, $this->faker->numberBetween(0, 3));
            // Set a reasonable return condition if status indicates a return
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
                default:
                    $conditionOnReturn = null;
            }
        }

        // Find or create a user for blameable columns
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (LoanTransactionItemFactory)'])->id;

        return [
            'loan_transaction_id'         => $loanTransaction->id,
            'equipment_id'                => $equipment->id,
            'loan_application_item_id'    => $loanApplicationItemId,
            'quantity_transacted'         => 1,
            'status'                      => $chosenStatus,
            'condition_on_return'         => $conditionOnReturn,
            'accessories_checklist_issue' => $accessoriesIssue,
            'accessories_checklist_return'=> $accessoriesReturn,
            'item_notes'                  => $msFaker->optional(0.2)->sentence,
            'created_by'                  => $auditUserId,
            'updated_by'                  => $auditUserId,
            'deleted_by'                  => null,
            'created_at'                  => now(),
            'updated_at'                  => now(),
            'deleted_at'                  => null,
        ];
    }

    /**
     * State for items marked as issued.
     */
    public function issued(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        return $this->state([
            'status' => LoanTransactionItem::STATUS_ITEM_ISSUED,
            'condition_on_return' => null,
            'accessories_checklist_issue' => $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            'accessories_checklist_return' => null,
        ]);
    }

    /**
     * State for items returned in good condition.
     */
    public function returnedGood(): static
    {
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        return $this->state(function (array $attributes) use ($accessories) {
            return [
                'status' => LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD,
                'condition_on_return' => Equipment::CONDITION_GOOD,
                'accessories_checklist_return' => $attributes['accessories_checklist_issue'] ?? $this->faker->randomElements($accessories, $this->faker->numberBetween(1, 3)),
            ];
        });
    }

    /**
     * State for items returned with damage.
     */
    public function returnedDamaged(): static
    {
        $msFaker = \Faker\Factory::create('ms_MY');
        $accessories = config('motac.loan_accessories_list', ['Adapter Kuasa', 'Tetikus', 'Beg Komputer Riba']);
        $damageConditions = [Equipment::CONDITION_MINOR_DAMAGE, Equipment::CONDITION_MAJOR_DAMAGE];
        $itemDamageStatuses = [
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE,
            LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN,
        ];
        return $this->state(function (array $attributes) use ($msFaker, $accessories, $damageConditions, $itemDamageStatuses) {
            return [
                'status' => Arr::random($itemDamageStatuses),
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
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (LoanTransactionItemFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
