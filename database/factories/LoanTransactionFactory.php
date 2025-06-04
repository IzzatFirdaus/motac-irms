<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;

class LoanTransactionFactory extends EloquentFactory
{
  protected $model = LoanTransaction::class;

  public function definition(): array
  {
    $application = LoanApplication::inRandomOrder()->first() ?? LoanApplication::factory()->create();
    $transactionTypes = LoanTransaction::getTypesOptions();
    $transactionStatuses = LoanTransaction::getStatusesList();

    $transactionDate = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
    $chosenType = $this->faker->randomElement(array_keys($transactionTypes ?: [LoanTransaction::TYPE_ISSUE]));

    $dueDate = null;
    if ($chosenType == LoanTransaction::TYPE_ISSUE && $application->loan_end_date) {
      $dueDate = Carbon::parse($application->loan_end_date);
    }

    $issuingOfficerId = null;
    $receivingOfficerId = null;
    $accessoriesIssue = null;
    $issueTimestamp = null;
    $issueNotes = null;

    $returningOfficerId = null;
    $returnAcceptingOfficerId = null;
    $accessoriesReturn = null;
    $returnTimestamp = null;
    $returnNotes = null;
    $relatedTransactionId = null;

    if ($chosenType == LoanTransaction::TYPE_ISSUE) {
      $issuingOfficerId = User::inRandomOrder()->first()?->id;
      $receivingOfficerId = $application->user_id;
      $accessoriesIssue = $this->faker->randomElements(['Power Adapter', 'Beg Laptop', 'Mouse Wayarles'], $this->faker->numberBetween(0, 3));
      $issueTimestamp = $transactionDate;
      $issueNotes = $this->faker->optional(0.4)->sentence;
    } elseif ($chosenType == LoanTransaction::TYPE_RETURN) {
      $returningOfficerId = $application->user_id;
      $returnAcceptingOfficerId = User::inRandomOrder()->first()?->id;
      $accessoriesReturn = $this->faker->randomElements(['Power Adapter', 'Beg Laptop', 'Mouse Wayarles'], $this->faker->numberBetween(0, 3));
      $returnTimestamp = $transactionDate;
      $returnNotes = $this->faker->optional(0.5)->sentence;

      $relatedIssueTx = LoanTransaction::where('loan_application_id', $application->id)
        ->where('type', LoanTransaction::TYPE_ISSUE)
        ->latest('transaction_date')
        ->first();
      $relatedTransactionId = $relatedIssueTx?->id;
    }

    return [
      'loan_application_id' => $application->id,
      'type' => $chosenType,
      'transaction_date' => $transactionDate,
      'due_date' => $dueDate?->toDateString(),
      'issuing_officer_id' => $issuingOfficerId,
      'receiving_officer_id' => $receivingOfficerId,
      'accessories_checklist_on_issue' => $accessoriesIssue,
      'issue_notes' => $issueNotes,
      'issue_timestamp' => $issueTimestamp,
      'returning_officer_id' => $returningOfficerId,
      'return_accepting_officer_id' => $returnAcceptingOfficerId,
      'accessories_checklist_on_return' => $accessoriesReturn,
      'return_timestamp' => $returnTimestamp,
      'return_notes' => $returnNotes,
      'related_transaction_id' => $relatedTransactionId,
      'status' => $this->faker->randomElement(array_keys($transactionStatuses ?: [LoanTransaction::STATUS_COMPLETED])),
    ];
  }

  public function issue(): static
  {
    return $this->state(function (array $attributes) {
      $application = $this->getApplicationFromAttributes($attributes);
      $transactionDate = Carbon::parse($attributes['transaction_date'] ?? $this->faker->dateTimeBetween($application->approved_at ?? '-3 months', 'now'));
      $issueNotes = $this->faker->optional(0.3)->sentence;

      return [
        'type' => LoanTransaction::TYPE_ISSUE,
        'transaction_date' => $transactionDate,
        'issue_timestamp' => $transactionDate,
        'due_date' => $application->loan_end_date ? Carbon::parse($application->loan_end_date)->toDateString() : null,
        'status' => LoanTransaction::STATUS_ISSUED,
        'issuing_officer_id' => $attributes['issuing_officer_id'] ?? User::inRandomOrder()->first()?->id,
        'receiving_officer_id' => $attributes['receiving_officer_id'] ?? $application->user_id,
        'accessories_checklist_on_issue' => $attributes['accessories_checklist_on_issue'] ?? $this->faker->randomElements(['Power Adapter', 'Beg', 'Mouse'], $this->faker->numberBetween(0, 3)),
        'issue_notes' => $issueNotes,
        'loan_application_id' => $application->id,
      ];
    })->afterCreating(function (LoanTransaction $transaction) {
      if ($transaction->loanTransactionItems()->count() > 0) {
        return;
      }

      $loanApplication = $transaction->loanApplication()->first();

      // FIX APPLIED HERE: Changed applicationItems() to loanApplicationItems()
      if ($loanApplication && $loanApplication->loanApplicationItems()->exists()) {
        foreach ($loanApplication->loanApplicationItems as $appItem) { // Also ensure this uses the correct relationship
          $alreadyIssuedForAppItem = LoanTransactionItem::where('loan_application_item_id', $appItem->id)
            ->whereHas('loanTransaction', fn($q) => $q->where('type', LoanTransaction::TYPE_ISSUE))
            ->sum('quantity_transacted');
          $quantityToIssue = ($appItem->quantity_approved ?? 0) - $alreadyIssuedForAppItem;

          if ($quantityToIssue > 0) {
            $availableEquipment = Equipment::where('asset_type', $appItem->equipment_type)
              ->where('status', Equipment::STATUS_AVAILABLE)
              ->take($quantityToIssue)->get();

            foreach ($availableEquipment as $eq) {
              if ($quantityToIssue <= 0) break;
              LoanTransactionItem::factory()
                ->for($transaction)
                ->for($eq, 'equipment')
                ->for($appItem, 'loanApplicationItem')
                ->issued()
                ->create(['quantity_transacted' => 1]);
              $eq->update(['status' => Equipment::STATUS_ON_LOAN]);
              $quantityToIssue--;
            }
          }
        }
      }
    });
  }

  public function return(): static
  {
    return $this->state(function (array $attributes) {
      $application = $this->getApplicationFromAttributes($attributes);
      $issueDate = $application->issued_at ?? $application->approved_at ?? $application->created_at ?? '-1 month';
      $transactionDate = Carbon::parse($attributes['transaction_date'] ?? $this->faker->dateTimeBetween(Carbon::parse($issueDate)->addDay(), Carbon::parse($application->loan_end_date ?? 'now')->addDays(5)));
      $returnNotes = $this->faker->optional(0.4)->sentence;

      $relatedIssueTx = LoanTransaction::where('loan_application_id', $application->id)
        ->where('type', LoanTransaction::TYPE_ISSUE)
        ->latest('transaction_date')
        ->first();

      return [
        'type' => LoanTransaction::TYPE_RETURN,
        'transaction_date' => $transactionDate,
        'return_timestamp' => $transactionDate,
        'status' => $this->faker->randomElement([LoanTransaction::STATUS_RETURNED_GOOD, LoanTransaction::STATUS_RETURNED_DAMAGED]),
        'returning_officer_id' => $attributes['returning_officer_id'] ?? $application->user_id,
        'return_accepting_officer_id' => $attributes['return_accepting_officer_id'] ?? User::inRandomOrder()->first()?->id,
        'accessories_checklist_on_return' => $attributes['accessories_checklist_on_return'] ?? $this->faker->randomElements(['Power Adapter', 'Beg', 'Mouse'], $this->faker->numberBetween(0, 3)),
        'return_notes' => $returnNotes,
        'loan_application_id' => $application->id,
        'related_transaction_id' => $relatedIssueTx?->id,
      ];
    })->afterCreating(function (LoanTransaction $transaction) {
      if ($transaction->loanTransactionItems()->count() > 0) {
        return;
      }

      $relatedIssueTransactionId = $transaction->related_transaction_id ??
        LoanTransaction::where('loan_application_id', $transaction->loan_application_id)
        ->where('type', LoanTransaction::TYPE_ISSUE)
        ->latest('id')->value('id');

      if ($relatedIssueTransactionId) {
        $issuedItems = LoanTransactionItem::where('loan_transaction_id', $relatedIssueTransactionId)->get();
        foreach ($issuedItems as $issuedItem) {
          if ($issuedItem->equipment) {
            LoanTransactionItem::factory()
              ->for($transaction)
              ->for($issuedItem->equipment, 'equipment')
              ->for($issuedItem->loanApplicationItem, 'loanApplicationItem')
              // MODIFIED: Changed ->returned() to ->returnedGood()
              ->returnedGood()
              ->create([
                'quantity_transacted' => $issuedItem->quantity_transacted,
                'accessories_checklist_issue' => $issuedItem->accessories_checklist_issue,
              ]);
            $conditionOnReturn = $transaction->status === LoanTransaction::STATUS_RETURNED_DAMAGED ? Equipment::CONDITION_MINOR_DAMAGE : Equipment::CONDITION_GOOD;
            $issuedItem->equipment->update(['status' => Equipment::STATUS_AVAILABLE, 'condition_status' => $conditionOnReturn]);
          }
        }
      }
    });
  }

  public function deleted(): static
  {
    return $this->state(fn(array $attributes) => [
      'deleted_at' => now(),
    ]);
  }

  private function getApplicationFromAttributes(array $attributes): LoanApplication
  {
    $application = null;
    if (isset($attributes['loan_application_id'])) {
      $appId = $attributes['loan_application_id'];
      if (is_scalar($appId) && !empty($appId)) {
        $application = LoanApplication::find($appId);
      }
    }
    return $application ?? (LoanApplication::inRandomOrder()->first() ?? LoanApplication::factory()->create());
  }
}
