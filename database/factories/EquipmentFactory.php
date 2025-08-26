<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Optimized Factory for Equipment model.
 *
 * - Uses static caches for related model IDs (User, Department, EquipmentCategory, SubCategory, Location).
 * - Does NOT create related models in definition() (ensures performant batch seeding).
 * - All foreign keys can be passed via state; otherwise, chosen randomly from existing records.
 * - Use with seeder that ensures all referenced data exists before seeding equipment.
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        // Cache related IDs for performance (static array persists across calls)
        static $userIds, $departmentIds, $equipmentCategoryIds, $subCategoryIdsByCat, $locationIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        if (! isset($departmentIds)) {
            $departmentIds = Department::pluck('id')->all();
        }
        if (! isset($equipmentCategoryIds)) {
            $equipmentCategoryIds = EquipmentCategory::pluck('id')->all();
        }
        if (! isset($locationIds)) {
            $locationIds = Location::pluck('id')->all();
        }
        if (! isset($subCategoryIdsByCat)) {
            // Map EquipmentCategory ID => array of SubCategory IDs
            $subCategoryIdsByCat = [];
            foreach (SubCategory::all() as $subCat) {
                $subCategoryIdsByCat[$subCat->equipment_category_id][] = $subCat->id;
            }
        }

        // Choose random IDs from cached arrays (or null if not available)
        $auditUserId         = ! empty($userIds) ? Arr::random($userIds) : null;
        $departmentId        = ! empty($departmentIds) ? Arr::random($departmentIds) : null;
        $equipmentCategoryId = ! empty($equipmentCategoryIds) ? Arr::random($equipmentCategoryIds) : null;
        $locationId          = ! empty($locationIds) ? Arr::random($locationIds) : null;

        // Pick a subcategory belonging to the selected equipment category
        $subCategoryId = null;
        if ($equipmentCategoryId && ! empty($subCategoryIdsByCat[$equipmentCategoryId])) {
            $subCategoryId = Arr::random($subCategoryIdsByCat[$equipmentCategoryId]);
        }

        // Use static Faker for ms_MY locale
        static $msFaker;
        if (! $msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Generate dates
        $purchaseDateRaw    = $this->faker->optional(0.8)->dateTimeBetween('-5 years', '-3 months');
        $purchaseDate       = $purchaseDateRaw ? Carbon::instance($purchaseDateRaw) : null;
        $warrantyExpiryDate = $purchaseDate ? $purchaseDate->copy()->addYears($this->faker->numberBetween(1, 3)) : null;

        $createdAt = $purchaseDate ?? Carbon::parse($this->faker->dateTimeThisDecade('-2 years'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt->toDateTimeString(), 'now'));

        // Generate unique asset identifiers
        // Use a fresh local faker instance for identifier generation to avoid any null/bootstrapping issues
        $localFaker = \Faker\Factory::create('ms_MY');
        $itemCode   = $localFaker->unique()->bothify('ITEM-????-#####');
        $tagIdRaw   = $localFaker->optional(0.9)->unique();
        $tagId      = $tagIdRaw
            ? $tagIdRaw->numerify('MOTAC/ICT/'.now()->year.'######')
            : 'MOTAC/ICT/'.now()->year.mt_rand(100000, 999999);
        $serialNumberRaw = $localFaker->optional(0.95)->unique();
        $serialNumber    = $serialNumberRaw
            ? $serialNumberRaw->bothify('SN-########????')
            : 'SN-'.mt_rand(10000000, 99999999).strtoupper(Str::random(4));

        // Enumerate asset type, status, and condition options from Equipment model
        $assetType = $this->faker->randomElement([
            Equipment::ASSET_TYPE_LAPTOP,
            Equipment::ASSET_TYPE_PROJECTOR,
            Equipment::ASSET_TYPE_PRINTER,
            Equipment::ASSET_TYPE_DESKTOP,
            Equipment::ASSET_TYPE_MONITOR,
            Equipment::ASSET_TYPE_OTHER_ICT,
        ]);

        $status = $this->faker->randomElement([
            Equipment::STATUS_AVAILABLE,
            Equipment::STATUS_ON_LOAN,
            Equipment::STATUS_UNDER_MAINTENANCE,
            Equipment::STATUS_DISPOSED,
            Equipment::STATUS_LOST,
            Equipment::STATUS_DAMAGED_NEEDS_REPAIR,
        ]);

        $conditionStatus = $this->faker->randomElement([
            Equipment::CONDITION_NEW,
            Equipment::CONDITION_GOOD,
            Equipment::CONDITION_FAIR,
            Equipment::CONDITION_MINOR_DAMAGE,
            Equipment::CONDITION_MAJOR_DAMAGE,
            Equipment::CONDITION_UNSERVICEABLE,
            Equipment::CONDITION_LOST,
        ]);

        // Acquisition type and classification (as string, not enum constants here)
        $acquisitionType = $this->faker->optional(0.8)->randomElement([
            'purchase', 'lease', 'donation', 'transfer', 'other',
        ]);
        $classification = $this->faker->optional(0.8)->randomElement([
            'asset', 'inventory', 'consumable', 'other',
        ]);

        return [
            'equipment_category_id' => $equipmentCategoryId,
            'sub_category_id'       => $subCategoryId,
            'item_code'             => $itemCode,
            'tag_id'                => $tagId,
            'serial_number'         => $serialNumber,
            'asset_type'            => $assetType,
            'brand'                 => $this->faker->randomElement(['Dell', 'HP', 'Lenovo', 'Acer', 'Apple', 'Canon', 'Epson', 'Samsung']),
            'model'                 => Str::title($this->faker->words(mt_rand(1, 2), true)).' '.$this->faker->bothify('##??X'),
            'description'           => $msFaker->optional(0.7)->paragraph(2),
            'purchase_price'        => $purchaseDate ? $this->faker->randomFloat(2, 100, 5000) : null,
            'purchase_date'         => $purchaseDate ? $purchaseDate->format('Y-m-d') : null,
            'warranty_expiry_date'  => $warrantyExpiryDate ? $warrantyExpiryDate->format('Y-m-d') : null,
            'status'                => $status,
            'condition_status'      => $conditionStatus,
            'location_id'           => $locationId,
            'current_location'      => $msFaker->optional(0.7)->address(),
            'notes'                 => $msFaker->optional(0.4)->paragraph(1),
            'classification'        => $classification,
            'acquisition_type'      => $acquisitionType,
            'funded_by'             => $msFaker->optional(0.5)->company(),
            'supplier_name'         => $msFaker->optional(0.7)->company(),
            'department_id'         => $departmentId,
            'created_by'            => $auditUserId,
            'updated_by'            => $auditUserId,
            'deleted_by'            => null,
            'created_at'            => $createdAt,
            'updated_at'            => $updatedAt,
            'deleted_at'            => null,
        ];
    }

    /**
     * State for available equipment.
     */
    public function available(): static
    {
        return $this->state(['status' => Equipment::STATUS_AVAILABLE]);
    }

    /**
     * State for equipment on loan.
     */
    public function onLoan(): static
    {
        return $this->state(['status' => Equipment::STATUS_ON_LOAN]);
    }

    /**
     * State for equipment under maintenance.
     */
    public function underMaintenance(): static
    {
        return $this->state(['status' => Equipment::STATUS_UNDER_MAINTENANCE]);
    }

    /**
     * State for disposed equipment.
     */
    public function disposed(): static
    {
        return $this->state(['status' => Equipment::STATUS_DISPOSED]);
    }

    /**
     * State for lost equipment.
     */
    public function lost(): static
    {
        return $this->state(['status' => Equipment::STATUS_LOST]);
    }

    /**
     * State for equipment needing repair.
     */
    public function damagedNeedsRepair(): static
    {
        return $this->state(['status' => Equipment::STATUS_DAMAGED_NEEDS_REPAIR]);
    }

    /**
     * State for new condition.
     */
    public function conditionNew(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_NEW]);
    }

    /**
     * State for good condition.
     */
    public function conditionGood(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_GOOD]);
    }

    /**
     * State for fair condition.
     */
    public function conditionFair(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_FAIR]);
    }

    /**
     * State for minor damage.
     */
    public function conditionMinorDamage(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_MINOR_DAMAGE]);
    }

    /**
     * State for major damage.
     */
    public function conditionMajorDamage(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_MAJOR_DAMAGE]);
    }

    /**
     * State for unserviceable equipment.
     */
    public function conditionUnserviceable(): static
    {
        return $this->state(['condition_status' => Equipment::CONDITION_UNSERVICEABLE]);
    }

    /**
     * State for soft deleted equipment.
     */
    public function deleted(): static
    {
        static $userIds;
        if (! isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = ! empty($userIds) ? Arr::random($userIds) : null;

        return $this->state([
            'deleted_at' => now(),
            'status'     => Equipment::STATUS_DISPOSED,
            'deleted_by' => $deleterId,
        ]);
    }
}
