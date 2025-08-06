<?php
// File: database/seeders/HelpdeskPrioritySeeder.php

namespace Database\Seeders;

use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpdeskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting HelpdeskPriority seeding...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('helpdesk_priorities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated helpdesk_priorities table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (!$auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (HelpdeskPrioritySeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info(sprintf('Created a fallback audit user with ID %d for HelpdeskPrioritySeeder.', $auditUserId));
        } else {
            Log::info(sprintf('Using User ID %s for audit columns in HelpdeskPrioritySeeder.', $auditUserId));
        }

        $priorities = [
            ['name' => 'Low', 'level' => 10, 'color_code' => '#28a745'], // Green
            ['name' => 'Medium', 'level' => 20, 'color_code' => '#007bff'], // Blue
            ['name' => 'High', 'level' => 30, 'color_code' => '#ffc107'], // Yellow/Orange
            ['name' => 'Critical', 'level' => 40, 'color_code' => '#dc3545'], // Red
        ];

        foreach ($priorities as $priorityData) {
            HelpdeskPriority::firstOrCreate(
                ['name' => $priorityData['name']],
                array_merge($priorityData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }

        Log::info('HelpdeskPriority seeding complete. Created/verified ' . count($priorities) . ' priorities.');
    }
}
