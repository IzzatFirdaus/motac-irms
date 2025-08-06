<?php
// File: database/seeders/HelpdeskCategorySeeder.php

namespace Database\Seeders;

use App\Models\HelpdeskCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpdeskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting HelpdeskCategory seeding...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('helpdesk_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated helpdesk_categories table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (HelpdeskCategorySeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info(sprintf('Created a fallback audit user with ID %d for HelpdeskCategorySeeder.', $auditUserId));
        } else {
            Log::info(sprintf('Using User ID %s for audit columns in HelpdeskCategorySeeder.', $auditUserId));
        }

        $categories = [
            ['name' => 'Hardware', 'description' => 'Issues related to physical computer components, peripherals, etc.', 'is_active' => true],
            ['name' => 'Software', 'description' => 'Problems with operating systems, applications, or specialized software.', 'is_active' => true],
            ['name' => 'Network', 'description' => 'Connectivity issues, Wi-Fi problems, VPN access, etc.', 'is_active' => true],
            ['name' => 'Account & Access', 'description' => 'Password resets, account lockouts, access permissions.', 'is_active' => true],
            ['name' => 'Printer', 'description' => 'Printer setup, toner replacement, paper jams, and other printing issues.', 'is_active' => true],
            ['name' => 'Email', 'description' => 'Email client configuration, sending/receiving issues.', 'is_active' => true],
            ['name' => 'System Performance', 'description' => 'Slow computer, application crashes, freezing.', 'is_active' => true],
            ['name' => 'Other', 'description' => 'Miscellaneous IT support requests not covered by other categories.', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            HelpdeskCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                array_merge($categoryData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }

        Log::info('HelpdeskCategory seeding complete. Created/verified ' . count($categories) . ' categories.');
    }
}
