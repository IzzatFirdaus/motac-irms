<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
// Removed: use App\Models\User; // Not directly used for audit here
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Arr;

class LoanApplicationItemFactory extends EloquentFactory
{
  protected $model = LoanApplicationItem::class;

  public function definition(): array
  {
    $loanApplication = LoanApplication::inRandomOrder()->first() ?? LoanApplication::factory()->create();

    $assetTypes = method_exists(Equipment::class, 'getAssetTypesList') ? Equipment::getAssetTypesList() : ['laptop', 'projector', 'printer']; // Added fallback
    $requestedQuantity = $this->faker->numberBetween(1, 3);
    $approvedQuantity = $this->faker->optional(0.7)->numberBetween(1, $requestedQuantity);
    $issuedQuantity = ($approvedQuantity !== null && $this->faker->boolean(80)) ? $this->faker->numberBetween(0, $approvedQuantity) : 0;
    $returnedQuantity = ($issuedQuantity > 0 && $this->faker->boolean(70)) ? $this->faker->numberBetween(0, $issuedQuantity) : 0;

    // Statuses defined in the migration for loan_application_items
    // Example: ['pending_approval', 'item_approved', 'item_rejected', 'awaiting_issuance', 'fully_issued', 'partially_issued', 'fully_returned', 'item_cancelled']
    // For simplicity, the factory can set a common initial status or derive it.
    // The migration default is 'pending_approval'.
    $itemStatus = 'pending_approval'; // Default, matching migration
    if ($approvedQuantity !== null && $approvedQuantity > 0) {
      $itemStatus = 'item_approved';
      if ($issuedQuantity == $approvedQuantity && $approvedQuantity > 0) {
        $itemStatus = 'fully_issued';
        if ($returnedQuantity == $issuedQuantity) {
          $itemStatus = 'fully_returned';
        }
      } elseif ($issuedQuantity > 0) {
        $itemStatus = 'partially_issued';
      } elseif ($issuedQuantity == 0) {
        $itemStatus = 'awaiting_issuance';
      }
    } elseif ($approvedQuantity === 0) {
      $itemStatus = 'item_rejected';
    }


    return [
      'loan_application_id' => $loanApplication->id,
      'equipment_type' => Arr::random($assetTypes ?: ['laptop']),
      'quantity_requested' => $requestedQuantity,
      'quantity_approved' => $approvedQuantity,
      'quantity_issued' => $issuedQuantity,
      'quantity_returned' => $returnedQuantity,
      'status' => $itemStatus, // Now setting the status field
      'notes' => $this->faker->optional(0.3)->sentence,
      // 'created_by', 'updated_by' assumed to be handled by BlameableObserver
      'deleted_by' => null,
    ];
  }

  public function quantityRequested(int $quantity): static
  {
    return $this->state(fn(array $attributes) => ['quantity_requested' => $quantity]);
  }

  public function quantityApproved(?int $quantity): static
  {
    return $this->state(fn(array $attributes) => ['quantity_approved' => $quantity]);
  }

  public function quantityIssued(int $quantity): static
  {
    return $this->state(fn(array $attributes) => ['quantity_issued' => $quantity]);
  }

  public function quantityReturned(int $quantity): static
  {
    return $this->state(fn(array $attributes) => ['quantity_returned' => $quantity]);
  }

  public function type(string $type): static
  {
    $assetTypes = method_exists(Equipment::class, 'getAssetTypesList') ? Equipment::getAssetTypesList() : [];
    if (!in_array($type, $assetTypes) && !empty($assetTypes)) {
      $type = Arr::random($assetTypes);
    } elseif (empty($assetTypes)) {
      $type = 'laptop'; // Absolute fallback
    }
    return $this->state(fn(array $attributes) => ['equipment_type' => $type]);
  }

  public function fullyProcessed(): static
  {
    return $this->state(function (array $attributes) {
      $requested = $attributes['quantity_requested'] ?? $this->faker->numberBetween(1, 2);
      return [
        'quantity_requested' => $requested,
        'quantity_approved' => $requested,
        'quantity_issued' => $requested,
        'quantity_returned' => $requested,
        'status' => 'fully_returned', // Assuming this is a valid status from migration
      ];
    });
  }
}
