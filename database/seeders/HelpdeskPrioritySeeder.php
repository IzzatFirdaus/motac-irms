<?php

namespace Database\Seeders;

use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeds the helpdesk_priorities table with the official priorities for helpdesk tickets.
 * Ensures color codes and ordering are consistent with the schema and model.
 */
class HelpdeskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting HelpdeskPriority seeding...');

        // Truncate the priorities table for a clean slate
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('helpdesk_priorities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated helpdesk_priorities table.');

        // Find or create an admin user for audit columns
        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId       = $adminUserForAudit?->id;

        if (! $auditUserId) {
            // If no user exists, create a fallback audit user
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (HelpdeskPrioritySeeder)']);
            $auditUserId       = $adminUserForAudit->id;
            Log::info(sprintf('Created a fallback audit user with ID %d for HelpdeskPrioritySeeder.', $auditUserId));
        } else {
            Log::info(sprintf('Using User ID %s for audit columns in HelpdeskPrioritySeeder.', $auditUserId));
        }

        // Define the official helpdesk priorities - name, level, color_code
        $priorities = [
            [
                'name'       => 'Low',
                'level'      => 10,
                'color_code' => '#28a745', // Green
            ],
            [
                'name'       => 'Medium',
                'level'      => 20,
                'color_code' => '#007bff', // Blue
            ],
            [
                'name'       => 'High',
                'level'      => 30,
                'color_code' => '#ffc107', // Yellow/Orange
            ],
            [
                'name'       => 'Critical',
                'level'      => 40,
                'color_code' => '#dc3545', // Red
            ],
        ];

        // Create or update each priority in the table
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
