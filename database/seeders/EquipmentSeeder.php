<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\User;
// Required for ensuring foreign key data exists if not handled by factory robustly
// use Database\Seeders\DepartmentsSeeder;
// use Database\Seeders\LocationSeeder;
// use Database\Seeders\EquipmentCategorySeeder;
// use Database\Seeders\SubCategoriesSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * IMPORTANT: The EquipmentFactory associated with the Equipment model
     * MUST be updated to reflect the full schema of the 'equipment' table
     * as per 'MOTAC Integrated Resource Management System (Revision 3)',
     * section 4.3. This includes:
     * - asset_type (enum, use Equipment::ASSET_TYPE_*)
     * - brand, model, serial_number, tag_id
     * - purchase_date, warranty_expiry_date
     * - status (enum, use Equipment::STATUS_*)
     * - current_location (string, or ensure location_id is linked)
     * - notes, description
     * - condition_status (enum, use Equipment::CONDITION_*)
     * - department_id (link to departments)
     * - equipment_category_id (link to equipment_categories)
     * - sub_category_id (link to sub_categories)
     * - location_id (link to locations for structured location)
     * - item_code (unique internal identifier)
     * - purchase_price
     * - acquisition_type (enum, use Equipment::ACQUISITION_TYPE_*)
     * - classification (enum, use Equipment::CLASSIFICATION_*)
     * - funded_by, supplier_name
     * - created_by, updated_by (should be handled by BlameableObserver or set in factory)
     */
    public function run(int $numberOfEquipment = 50): void // Default to 50, can be overridden
    {
        Log::info('Starting EquipmentSeeder (Revision 3)...');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (! $auditUserId) {
            $this->call(UserSeeder::class); // Ensure UserSeeder creates at least one user, or a specific AdminUserSeeder
            $adminUserForAudit = User::orderBy('id')->first(); // Try again
            if (! $adminUserForAudit) {
                $adminUserForAudit = User::factory()->create(['name' => 'Audit User (EquipmentSeeder)']);
                Log::info('Created a default audit user for EquipmentSeeder as no users were found.');
            }

            $auditUserId = $adminUserForAudit->id;
        }

        Log::info(sprintf('Using User ID %s for created_by/updated_by overrides if needed.', $auditUserId));

        // It's good practice to ensure dependent master data seeders are run before this,
        // or that the EquipmentFactory can create them if they don't exist.
        // Uncomment if you want to explicitly run them.
        // $this->call([
        //     DepartmentsSeeder::class,
        //     LocationSeeder::class,
        //     EquipmentCategorySeeder::class,
        //     SubCategoriesSeeder::class,
        // ]);

        Log::info(sprintf('Attempting to create %d equipment items using factory (Revision 3)...', $numberOfEquipment));

        Equipment::factory()
            ->count($numberOfEquipment)
            ->create([
                // Override factory defaults here only if necessary for specific seeder logic.
                // The factory should ideally set created_by and updated_by using its own logic
                // or by utilizing the BlameableObserver if that's how it's designed.
                // Forcing it here ensures all seeded equipment is by this specific audit user.
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);

        Log::info(sprintf('EquipmentSeeder finished. Created/processed %d equipment items (Revision 3).', $numberOfEquipment));
    }
}
