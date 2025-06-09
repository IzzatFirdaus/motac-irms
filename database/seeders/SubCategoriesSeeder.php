<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory; // Your EquipmentCategory model
use App\Models\SubCategory;      // Your SubCategory model
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting SubCategories seeding (for Equipment - Revision 3 - Factory based)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('sub_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated sub_categories table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (! $auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (SubCatSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for SubCategoriesSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in SubCategoriesSeeder.");
        }

        $equipmentCategories = EquipmentCategory::all()->keyBy('name');

        if ($equipmentCategories->isEmpty()) {
            Log::warning('No Equipment Categories found. Calling EquipmentCategorySeeder to ensure categories exist.');
            // Ensure EquipmentCategorySeeder uses the same audit user logic if called here.
            // For simplicity, it's better to ensure it's run via DatabaseSeeder.
            // $this->call(EquipmentCategorySeeder::class);
            // $equipmentCategories = EquipmentCategory::all()->keyBy('name'); // Re-fetch

            if (EquipmentCategory::count() === 0) { // Check again after potential call
                Log::error('Failed to ensure EquipmentCategories exist. Cannot effectively seed SubCategories. Please run EquipmentCategorySeeder first.');

                return;
            }
            $equipmentCategories = EquipmentCategory::all()->keyBy('name'); // Re-fetch if seeder was called
        }

        // Define specific subcategories.
        // Ensure 'equipment_category_name' matches names from your EquipmentCategorySeeder
        // (e.g., "Komputer Riba", "Projektor LCD", "Peranti Input/Output")
        $subCategoriesData = [
            ['equipment_category_name' => 'Komputer Riba', 'name' => 'Ultrabook MOTAC', 'description' => 'Komputer riba nipis dan ringan.', 'is_active' => true],
            ['equipment_category_name' => 'Komputer Riba', 'name' => 'Laptop Pejabat Standard', 'description' => 'Laptop untuk kegunaan pejabat am.', 'is_active' => true],

            ['equipment_category_name' => 'Projektor LCD', 'name' => 'Projektor Jarak Dekat (Bilik Mesyuarat)', 'description' => 'Projektor untuk ruang kecil.', 'is_active' => true],
            ['equipment_category_name' => 'Projektor LCD', 'name' => 'Projektor Mudah Alih (Acara Luar)', 'description' => 'Projektor kompak untuk perjalanan dan acara luar.', 'is_active' => true],

            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Papan Kekunci (Wayarles)', 'description' => 'Papan kekunci tanpa wayar.', 'is_active' => true],
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Tetikus (Ergonomik)', 'description' => 'Tetikus dengan reka bentuk ergonomik.', 'is_active' => true],
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Monitor LED 24 inci', 'description' => 'Monitor LED bersaiz 24 inci.', 'is_active' => true],
            // Add more specific subcategories as needed
        ];

        Log::info('Creating specific subcategories for equipment categories (Revision 3)...');
        $createdSpecificCount = 0;
        foreach ($subCategoriesData as $subCategoryDef) {
            $parentCategory = $equipmentCategories->get($subCategoryDef['equipment_category_name']);

            if ($parentCategory) {
                // Fields align with SubCategory.php model's fillable array
                SubCategory::firstOrCreate(
                    [
                        'name' => $subCategoryDef['name'],
                        'equipment_category_id' => $parentCategory->id,
                    ],
                    [
                        'description' => $subCategoryDef['description'] ?? null,
                        'is_active' => $subCategoryDef['is_active'] ?? true,
                        'created_by' => $auditUserId,
                        'updated_by' => $auditUserId,
                    ]
                );
                $createdSpecificCount++;
            } else {
                Log::warning("Parent EquipmentCategory '{$subCategoryDef['equipment_category_name']}' not found for subcategory '{$subCategoryDef['name']}'. Skipping this specific subcategory.");
            }
        }
        Log::info("Ensured/Created {$createdSpecificCount} specific subcategories.");

        // Use the SubCategoryFactory.php to create additional random subcategories
        $targetFactoryCount = 15; // Number of additional random subcategories

        if ($equipmentCategories->isNotEmpty() && class_exists(SubCategory::class) && method_exists(SubCategory::class, 'factory')) {
            $currentSubCategoryCount = SubCategory::count();
            $needed = $targetFactoryCount - ($currentSubCategoryCount - $createdSpecificCount); // Aim for total, considering already created specifics. More simply, just add a fixed number of factory ones.
            $needed = max(0, $targetFactoryCount); // Create at least $targetFactoryCount new ones via factory if possible.

            if ($needed > 0) {
                Log::info("Creating {$needed} additional random subcategories using factory...");
                SubCategory::factory()
                    ->count($needed)
                    // The SubCategoryFactory.php handles linking to a random EquipmentCategory and audit stamps
                    ->create(); // Factory will handle 'equipment_category_id' and 'created_by', 'updated_by'
                Log::info("Created {$needed} additional subcategories using factory.");
            }
        } elseif ($equipmentCategories->isEmpty()) {
            Log::warning('Skipping factory creation of SubCategories as no Equipment Categories exist to link to.');
        } else {
            Log::error('App\Models\SubCategory model or its factory not found. Cannot seed additional subcategories via factory.');
        }
        Log::info('SubCategories seeding complete (Revision 3 - Factory based).');
    }
}
