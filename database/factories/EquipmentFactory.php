<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $auditUserId = User::inRandomOrder()->value('id');
        if (! $auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
            try {
                $auditUser = User::factory()->create(['name' => 'Audit User (EqFactory)']);
                $auditUserId = $auditUser->id;
            } catch (\Exception $e) {
                Log::error('EquipmentFactory: Could not create fallback audit user: '.$e->getMessage());
            }
        }

        $equipmentCategoryId = EquipmentCategory::inRandomOrder()->value('id');
        if (! $equipmentCategoryId && class_exists(EquipmentCategory::class) && method_exists(EquipmentCategory::class, 'factory')) {
            try {
                $equipmentCategory = EquipmentCategory::factory()->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
                $equipmentCategoryId = $equipmentCategory?->id;
            } catch (\Exception $e) {
                Log::error('EquipmentFactory: Could not create fallback EquipmentCategory: '.$e->getMessage());
            }
        }

        $subCategoryId = null;
        if ($equipmentCategoryId && class_exists(SubCategory::class) && method_exists(SubCategory::class, 'factory')) {
            try {
                $subCategory = SubCategory::where('equipment_category_id', $equipmentCategoryId)->inRandomOrder()->first();
                if (! $subCategory) {
                    $subCategory = SubCategory::factory()->create([
                        'equipment_category_id' => $equipmentCategoryId,
                        'created_by' => $auditUserId,
                        'updated_by' => $auditUserId,
                    ]);
                }

                $subCategoryId = $subCategory?->id;
            } catch (\Exception $e) {
                Log::error('EquipmentFactory: Could not create/find fallback SubCategory: '.$e->getMessage());
            }
        }

        $locationId = Location::inRandomOrder()->value('id');
        if (! $locationId && class_exists(Location::class) && method_exists(Location::class, 'factory')) {
            try {
                $location = Location::factory()->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
                $locationId = $location?->id;
            } catch (\Exception $e) {
                Log::error('EquipmentFactory: Could not create fallback Location: '.$e->getMessage());
            }
        }

        $departmentId = Department::inRandomOrder()->value('id');
        if (! $departmentId && class_exists(Department::class) && method_exists(Department::class, 'factory')) {
            try {
                $department = Department::factory()->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
                $departmentId = $department?->id;
            } catch (\Exception $e) {
                Log::error('EquipmentFactory: Could not create fallback Department: '.$e->getMessage());
            }
        }

        $purchaseDateRaw = $this->faker->optional(0.8)->dateTimeBetween('-5 years', '-3 months');
        $purchaseDate = $purchaseDateRaw ? Carbon::instance($purchaseDateRaw) : null;

        $warrantyExpiryDate = null;
        if ($purchaseDate instanceof Carbon) {
            $warrantyExpiryDate = $purchaseDate->copy()->addYears($this->faker->numberBetween(1, 3));
        }

        $createdAt = $purchaseDate ?? Carbon::parse($this->faker->dateTimeThisDecade('-2 years'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt->toDateTimeString(), 'now'));

        return [
            'asset_type' => $this->faker->randomElement([
                Equipment::ASSET_TYPE_LAPTOP,
                Equipment::ASSET_TYPE_PROJECTOR,
                Equipment::ASSET_TYPE_PRINTER,
                Equipment::ASSET_TYPE_DESKTOP_PC,
                Equipment::ASSET_TYPE_MONITOR,
                Equipment::ASSET_TYPE_OTHER_ICT,
            ]),
            'brand' => $this->faker->randomElement(['Dell', 'HP', 'Lenovo', 'Acer', 'Apple', 'Canon', 'Epson', 'Samsung']),
            'model' => Str::title($this->faker->words(mt_rand(1, 2), true)).' '.$this->faker->bothify('##??X'),
            'serial_number' => $this->faker->optional(0.95)->passthrough(
                $this->faker->unique()->bothify('SN-########????')
            ),
            'tag_id' => $this->faker->optional(0.9)->passthrough(
                'MOTAC/ICT/'.now()->year.'/'.$this->faker->unique()->numerify('######')
            ),
            'purchase_date' => $purchaseDate ? $purchaseDate->format('Y-m-d') : null,
            'warranty_expiry_date' => $warrantyExpiryDate ? $warrantyExpiryDate->format('Y-m-d') : null,
            'status' => $this->faker->randomElement([
                Equipment::STATUS_AVAILABLE,
                Equipment::STATUS_ON_LOAN,
                Equipment::STATUS_UNDER_MAINTENANCE,
                Equipment::STATUS_DISPOSED,
                Equipment::STATUS_LOST,
                Equipment::STATUS_DAMAGED_NEEDS_REPAIR,
            ]),
            'current_location' => $msFaker->optional(0.7)->address(),
            'notes' => $msFaker->optional(0.4)->paragraph(1),
            'condition_status' => $this->faker->randomElement([
                Equipment::CONDITION_NEW,
                Equipment::CONDITION_GOOD,
                Equipment::CONDITION_FAIR,
                Equipment::CONDITION_MINOR_DAMAGE,
                Equipment::CONDITION_MAJOR_DAMAGE,
                Equipment::CONDITION_UNSERVICEABLE,
                Equipment::CONDITION_LOST,
            ]),
            'department_id' => $departmentId,
            'equipment_category_id' => $equipmentCategoryId,
            'sub_category_id' => $subCategoryId,
            'location_id' => $locationId,
            'item_code' => $this->faker->optional(0.9)->passthrough(
                $this->faker->unique()->bothify('ITEM-????-#####')
            ),
            'description' => $msFaker->optional(0.7)->paragraph(2),
            'purchase_price' => $purchaseDate ? $this->faker->randomFloat(2, 100, 5000) : null,
            'acquisition_type' => $this->faker->optional(0.8)->randomElement([
                Equipment::ACQUISITION_TYPE_PURCHASE,
                Equipment::ACQUISITION_TYPE_LEASE,
                Equipment::ACQUISITION_TYPE_DONATION,
                Equipment::ACQUISITION_TYPE_TRANSFER,
                Equipment::ACQUISITION_TYPE_OTHER,
            ]),
            'classification' => $this->faker->optional(0.8)->randomElement([
                Equipment::CLASSIFICATION_ASSET,
                Equipment::CLASSIFICATION_INVENTORY,
                Equipment::CLASSIFICATION_CONSUMABLE,
                Equipment::CLASSIFICATION_OTHER,
            ]),
            'funded_by' => $msFaker->optional(0.5)->company(),
            'supplier_name' => $msFaker->optional(0.7)->company,
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

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

    public function deleted(): static
    {
        return $this->state([
            'deleted_at' => now(),
            'status' => Equipment::STATUS_DISPOSED,
        ]);
    }
}
