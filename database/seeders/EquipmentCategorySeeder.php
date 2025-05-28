<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting EquipmentCategory seeding (Revision 3)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('equipment_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated equipment_categories table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (EqCategorySeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for EquipmentCategorySeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in EquipmentCategorySeeder.");
        }

        $categories = [
            [
                'name' => 'Komputer Riba',
                'description' => 'Komputer mudah alih untuk kegunaan pejabat dan lapangan.',
                'is_active' => true,
            ],
            [
                'name' => 'Projektor LCD',
                'description' => 'Alat untuk paparan visual mesyuarat dan pembentangan.',
                'is_active' => true,
            ],
            [
                'name' => 'Pencetak', // More general
                'description' => 'Pencetak untuk dokumen pejabat (Laser, Inkjet).',
                'is_active' => true,
            ],
            [
                'name' => 'Peralatan Rangkaian',
                'description' => 'Penghala, suis, dan perkakasan rangkaian lain.',
                'is_active' => true,
            ],
            [
                'name' => 'Peranti Input/Output', // Peripherals
                'description' => 'Papan kekunci, tetikus, kamera web, monitor, dll.',
                'is_active' => true,
            ],
            [
                'name' => 'Storan Mudah Alih',
                'description' => 'Pemacu keras luaran dan pemacu kilat USB.',
                'is_active' => true,
            ],
            [
                'name' => 'Komputer Meja (Desktop PC)',
                'description' => 'Komputer stesen kerja tetap.',
                'is_active' => true,
            ],
            [
                'name' => 'Peralatan ICT Lain',
                'description' => 'Peralatan ICT lain yang tidak dikategorikan secara spesifik.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            EquipmentCategory::firstOrCreate(
                ['name' => $categoryData['name']], // Unique by name
                array_merge($categoryData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }
        Log::info('Created/verified specific equipment categories (Revision 3).');

        $targetCount = 10; // Adjust if more variety is needed from factory
        if (EquipmentCategory::count() >= $targetCount) {
            Log::info('EquipmentCategory seeding complete (Revision 3).');
            return;
        }
        $needed = $targetCount - EquipmentCategory::count();
        if ($needed > 0) {
            EquipmentCategory::factory()
                ->count($needed)
                ->create([
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                    'is_active' => true,
                ]);
            Log::info("Created {$needed} additional equipment categories using factory.");
        }

        Log::info('EquipmentCategory seeding complete (Revision 3).');
    }
}
