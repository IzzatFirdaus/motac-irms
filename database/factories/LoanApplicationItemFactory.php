<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Arr;

class LoanApplicationItemFactory extends EloquentFactory
{
    protected $model = LoanApplicationItem::class;

    public function definition(): array
    {
        $loanApplication = LoanApplication::inRandomOrder()->first() ?? LoanApplication::factory()->create();
        // $auditUserId = User::orderBy('id')->first()?->id ?? User::factory()->create()->id; // Blameable

        $assetTypes = Equipment::getAssetTypesList(); // Ensure this method exists on Equipment model [cite: 5]
        $requestedQuantity = $this->faker->numberBetween(1, 3);
        $approvedQuantity = $this->faker->optional(0.7)->numberBetween(1, $requestedQuantity); // Approved <= Requested
        $issuedQuantity = ($approvedQuantity !== null && $this->faker->boolean(80)) ? $this->faker->numberBetween(0, $approvedQuantity) : 0;
        $returnedQuantity = ($issuedQuantity > 0 && $this->faker->boolean(70)) ? $this->faker->numberBetween(0, $issuedQuantity) : 0;

        // The 'status' field for LoanApplicationItem is not in the system design (Section 4.3).
        // If it exists in your migration, ensure these values are valid.
        // For this factory, we'll use a simple placeholder list or derive status from quantities.
        // $itemStatuses = ['pending_approval', 'item_approved', 'item_rejected', 'awaiting_issuance', 'fully_issued', 'partially_issued', 'fully_returned', 'item_cancelled'];
        // $derivedStatus = 'pending_approval'; // Default or derive based on quantities below.
        // Example derivation:
        $derivedStatus = 'unknown';
        if ($approvedQuantity === null) $derivedStatus = 'pending_approval';
        elseif ($approvedQuantity > 0 && $issuedQuantity == 0) $derivedStatus = 'awaiting_issuance';
        elseif ($issuedQuantity > 0 && $issuedQuantity < $approvedQuantity) $derivedStatus = 'partially_issued';
        elseif ($issuedQuantity > 0 && $issuedQuantity == $approvedQuantity && $returnedQuantity < $issuedQuantity) $derivedStatus = 'fully_issued';
        elseif ($returnedQuantity > 0 && $returnedQuantity == $issuedQuantity) $derivedStatus = 'fully_returned';


        return [
            'loan_application_id' => $loanApplication->id,
            'equipment_type' => Arr::random($assetTypes ?: ['laptop']), // Fallback if list is empty [cite: 5]
            'quantity_requested' => $requestedQuantity,
            'quantity_approved' => $approvedQuantity,
            'quantity_issued' => $issuedQuantity,
            'quantity_returned' => $returnedQuantity, // Added from system design [cite: 5]
            // 'status' => $derivedStatus, // If you have a status column
            'notes' => $this->faker->optional(0.3)->sentence,
            // 'created_by', 'updated_by' handled by BlameableObserver
            'deleted_by' => null,
        ];
    }

    public function quantityRequested(int $quantity): static
    {
        return $this->state(fn (array $attributes) => ['quantity_requested' => $quantity]);
    }

    public function quantityApproved(?int $quantity): static // Allow null
    {
        return $this->state(fn (array $attributes) => ['quantity_approved' => $quantity]);
    }

    public function quantityIssued(int $quantity): static
    {
        return $this->state(fn (array $attributes) => ['quantity_issued' => $quantity]);
    }

    public function quantityReturned(int $quantity): static
    {
        return $this->state(fn (array $attributes) => ['quantity_returned' => $quantity]);
    }

    public function type(string $type): static
    {
        $assetTypes = Equipment::getAssetTypesList();
        if (!in_array($type, $assetTypes) && !empty($assetTypes)) {
            $type = Arr::random($assetTypes); // Default to a random valid type if provided type is invalid
        } elseif (empty($assetTypes)) {
            $type = 'laptop'; // Absolute fallback
        }
        return $this->state(fn (array $attributes) => ['equipment_type' => $type]);
    }

    // Example state for a fully processed item
    public function fullyProcessed(): static
    {
        return $this->state(function (array $attributes) {
            $requested = $attributes['quantity_requested'] ?? $this->faker->numberBetween(1,2);
            return [
                'quantity_requested' => $requested,
                'quantity_approved' => $requested,
                'quantity_issued' => $requested,
                'quantity_returned' => $requested,
            ];
        });
    }
}
