<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting SubCategories seeding (for Equipment - Revision 3)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('sub_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated sub_categories table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (SubCatSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for SubCategoriesSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in SubCategoriesSeeder.");
        }

        $equipmentCategories = EquipmentCategory::all()->keyBy('name');

        if ($equipmentCategories->isEmpty()) {
            Log::error('No Equipment Categories found. Cannot seed SubCategories. Please run EquipmentCategorySeeder first.');
            return;
        }

        $subCategoriesData = [
            // Linked to 'Komputer Riba'
            ['equipment_category_name' => 'Komputer Riba', 'name' => 'Ultrabook', 'description' => 'Komputer riba nipis dan ringan.', 'is_active' => true],
            ['equipment_category_name' => 'Komputer Riba', 'name' => 'Laptop Standard Pejabat', 'description' => 'Laptop untuk kegunaan pejabat am.', 'is_active' => true],
            ['equipment_category_name' => 'Komputer Riba', 'name' => 'Workstation Mudah Alih', 'description' => 'Laptop berprestasi tinggi untuk tugas berat.', 'is_active' => true],

            // Linked to 'Projektor LCD'
            ['equipment_category_name' => 'Projektor LCD', 'name' => 'Projektor Jarak Dekat (Short Throw)', 'description' => 'Projektor untuk ruang kecil.', 'is_active' => true],
            ['equipment_category_name' => 'Projektor LCD', 'name' => 'Projektor Mudah Alih', 'description' => 'Projektor kompak untuk perjalanan.', 'is_active' => true],
            ['equipment_category_name' => 'Projektor LCD', 'name' => 'Projektor Dewan Besar', 'description' => 'Projektor lumen tinggi untuk dewan.', 'is_active' => true],

            // Linked to 'Pencetak'
            ['equipment_category_name' => 'Pencetak', 'name' => 'Pencetak Laser (Monokrom)', 'description' => 'Pencetak laser hitam putih.', 'is_active' => true],
            ['equipment_category_name' => 'Pencetak', 'name' => 'Pencetak Laser (Warna)', 'description' => 'Pencetak laser berwarna.', 'is_active' => true],
            ['equipment_category_name' => 'Pencetak', 'name' => 'Pencetak Pelbagai Fungsi (MFP)', 'description' => 'Pencetak dengan fungsi imbas, salin, faks.', 'is_active' => true],

            // Linked to 'Peranti Input/Output'
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Papan Kekunci Wayarles', 'description' => 'Papan kekunci tanpa wayar.', 'is_active' => true],
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Tetikus Ergonomik', 'description' => 'Tetikus dengan reka bentuk ergonomik.', 'is_active' => true],
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Kamera Web HD', 'description' => 'Kamera web resolusi tinggi.', 'is_active' => true],
            ['equipment_category_name' => 'Peranti Input/Output', 'name' => 'Monitor LED 24-inci', 'description' => 'Monitor LED saiz 24 inci.', 'is_active' => true],
        ];

        Log::info('Creating specific subcategories for equipment categories (Revision 3)...');
        $createdCount = 0;
        foreach ($subCategoriesData as $subCategoryDef) {
            $parentCategory = $equipmentCategories->get($subCategoryDef['equipment_category_name']);

            if ($parentCategory) {
                SubCategory::firstOrCreate(
                    [
                        'name' => $subCategoryDef['name'],
                        'equipment_category_id' => $parentCategory->id,
                    ],
                    array_merge(
                        [
                            'description' => $subCategoryDef['description'] ?? null,
                            'is_active' => $subCategoryDef['is_active'] ?? true,
                        ],
                        [
                            'created_by' => $auditUserId,
                            'updated_by' => $auditUserId,
                        ]
                    )
                );
                $createdCount++;
            } else {
                Log::warning("EquipmentCategory '{$subCategoryDef['equipment_category_name']}' not found for subcategory '{$subCategoryDef['name']}'. Skipping.");
            }
        }
        Log::info("Ensured {$createdCount} specific subcategories exist.");

        $targetCount = 20; // Adjust as needed
        if (SubCategory::count() >= $targetCount || $equipmentCategories->isEmpty()) {
            Log::info('SubCategories seeding complete (Revision 3).');
            return;
        }
        $needed = $targetCount - SubCategory::count();
        if ($needed > 0) {
            SubCategory::factory()
                ->count($needed)
                ->create([
                    'equipment_category_id' => $equipmentCategories->random()->id,
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                    'is_active' => true,
                ]);
            Log::info("Created {$needed} additional subcategories using factory.");
        }
        Log::info('SubCategories seeding complete (Revision 3).');
    }
}
