<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory; // Assuming this model exists if equipment_category_id is used
use App\Models\Location; // Assuming this model exists if location_id is used
use App\Models\SubCategory; // Assuming this model exists if sub_category_id is used
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        // Fallback audit user for created_by/updated_by
        // $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Default Audit User (EqFactory)']);
        // $auditUserId = $auditUser->id; // BlameableObserver should handle this

        $departmentId = null;
        if (class_exists(Department::class)) {
            $departmentId = Department::inRandomOrder()->value('id')
                ?? Department::factory()->create()->id;
        }

        $purchaseDateRaw = $this->faker->optional(0.8)->dateTimeBetween('-5 years', '-3 months');
        $purchaseDate = $purchaseDateRaw ? Carbon::instance($purchaseDateRaw) : null;

        $warrantyExpiryDate = null;
        if ($purchaseDate) {
            $warrantyExpiryDate = $purchaseDate->copy()->addYears($this->faker->numberBetween(1, 3));
        }

        $createdAt = $purchaseDate ?? Carbon::parse($this->faker->dateTimeThisDecade('-2 years'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        // As per System Design (Section 4.3), equipment table has these fields:
        // asset_type (enum), brand, model, serial_number, tag_id, purchase_date, warranty_expiry_date,
        // status (enum), current_location (string, nullable), notes, condition_status (enum).
        // The factory below matches the provided file. If 'current_location' is truly removed from migration,
        // it should be removed from here too if not nullable or handled elsewhere.
        // Your provided factory already removed it from the return array.

        return [
            // 'equipment_category_id', 'sub_category_id', 'location_id' are not in the primary Equipment table design (section 4.3)
            // They might be custom additions or part of a more detailed specific design.
            // The provided factory had them. For alignment with the core system design, they would be omitted
            // unless they are indeed columns in your 'equipment' table.
            // Assuming they are NOT in the core 'equipment' table based on system design section 4.3.

            'asset_type' => $this->faker->randomElement(Equipment::getAssetTypesList()), // Uses model method
            'brand' => $this->faker->randomElement(['Dell', 'HP', 'Lenovo', 'Acer', 'Apple', 'Canon', 'Epson', 'Jabra', 'Polycom', 'Samsung', 'Logitech']),
            'model' => Str::title($this->faker->words(mt_rand(1, 2), true)) . ' ' . $this->faker->bothify('##??X'),
            'serial_number' => $this->faker->optional(0.95)->unique()->bothify('SN-########????'), // More varied serial
            'tag_id' => $this->faker->optional(0.9)->unique()->bothify('MOTAC/ICT/####/'.now()->year), // More relevant tag
            'purchase_date' => $purchaseDate ? $purchaseDate->format('Y-m-d') : null,
            'warranty_expiry_date' => $warrantyExpiryDate ? $warrantyExpiryDate->format('Y-m-d') : null,
            'status' => $this->faker->randomElement(Equipment::getOperationalStatusesList()), // Uses model method
            'current_location' => $this->faker->optional(0.6)->randomElement(['Aras 5, Blok A', 'Bilik Server BPM', 'Stor Utama ICT', $this->faker->company . ' Office']), // As per design: string, nullable
            'notes' => $this->faker->optional(0.4)->paragraph(1),
            'condition_status' => $this->faker->randomElement(Equipment::getConditionStatusesList()), // Uses model method

            // The following fields were in the provided factory but are not in the core design's equipment table (Section 4.3)
            // 'item_code' => $this->faker->optional(0.9)->unique()->bothify('ITEM-????-#####'),
            // 'description' => $this->faker->optional(0.7)->paragraph(1),
            // 'purchase_price' => $purchaseDate ? $this->faker->randomFloat(2, 100, 5000) : null,
            // 'classification' => $this->faker->optional(0.8)->randomElement(Equipment::getClassificationsList()),
            // 'acquisition_type' => $this->faker->optional(0.8)->randomElement(Equipment::getAcquisitionTypesList()),
            // 'funded_by' => $this->faker->optional(0.5)->bs,
            // 'supplier_name' => $this->faker->optional(0.7)->company,
            // 'department_id' => $departmentId, // department_id is NOT on the equipment table per design 4.3

            // Blameable fields will be handled by BlameableObserver
            // 'created_by' => $auditUserId,
            // 'updated_by' => $auditUserId,
            'deleted_by' => null,

            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

    // States remain as provided, they are good.
    public function available(): static
    {
        return $this->state(['status' => Equipment::STATUS_AVAILABLE]);
    }

    public function onLoan(): static
    {
        return $this->state(['status' => Equipment::STATUS_ON_LOAN]);
    }

    public function underMaintenance(): static
    {
        return $this->state(['status' => Equipment::STATUS_UNDER_MAINTENANCE]);
    }

    public function disposed(): static
    {
        return $this->state(['status' => Equipment::STATUS_DISPOSED]);
    }

    public function lost(): static
    {
        return $this->state(['status' => Equipment::STATUS_LOST]);
    }

    public function damagedNeedsRepair(): static
    {
        return $this->state(['status' => Equipment::STATUS_DAMAGED_NEEDS_REPAIR]);
    }

    public function conditionNew(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_NEW]);
    }

    public function conditionGood(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_GOOD]);
    }

    public function conditionFair(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_FAIR]);
    }

    public function conditionMinorDamage(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_MINOR_DAMAGE]);
    }

    public function conditionMajorDamage(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_MAJOR_DAMAGE]);
    }

    public function conditionUnserviceable(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_UNSERVICEABLE]);
    }

    // This state assumes equipment_category_id is a valid field on the Equipment model.
    // If not, this state or the main definition needs adjustment.
    // public function forCategory(EquipmentCategory|int $category): static
    // {
    //     $categoryId = $category instanceof EquipmentCategory ? $category->id : $category;
    //     $auditUserId = User::orderBy('id')->value('id') ?? User::factory()->create()->id;

    //     $subCategoryIdToSet = null;
    //     if (class_exists(SubCategory::class)) {
    //         $subCategoryIdToSet = SubCategory::where('equipment_category_id', $categoryId)->inRandomOrder()->value('id')
    //                             ?? SubCategory::factory()->create(['equipment_category_id' => $categoryId, 'created_by' => $auditUserId, 'updated_by' => $auditUserId])->id;
    //     }

    //     return $this->state(['equipment_category_id' => $categoryId, 'sub_category_id' => $subCategoryIdToSet]);
    // }

    public function deleted(): static
    {
        return $this->state([
            'deleted_at' => now(),
            // 'deleted_by' handled by BlameableObserver
            'status' => Equipment::STATUS_DISPOSED, // Often, deleted equipment are marked as disposed
        ]);
    }
}
