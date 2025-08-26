<?php

namespace Database\Seeders;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeds the helpdesk_tickets table with sample tickets for testing and development purposes.
 * Ensures correct relationships and fields based on the current schema and model.
 */
class HelpdeskTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting HelpdeskTicket seeding...');

        // Ensure prerequisite data exists
        if (User::count() === 0 || HelpdeskCategory::count() === 0 || HelpdeskPriority::count() === 0) {
            Log::error('HelpdeskTicketSeeder requires at least one User, one HelpdeskCategory, and one HelpdeskPriority record. Aborting.');

            return;
        }

        // Truncate for a clean slate
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('helpdesk_tickets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated helpdesk_tickets table.');

        $auditUser   = User::orderBy('id')->first();
        $auditUserId = $auditUser?->id;

        if (! $auditUserId) {
            // Create a fallback audit user if none exists
            $auditUser   = User::factory()->create(['name' => 'Audit User (HelpdeskTicketSeeder)']);
            $auditUserId = $auditUser->id;
            Log::info(sprintf('Created a fallback audit user with ID %d for HelpdeskTicketSeeder.', $auditUserId));
        } else {
            Log::info(sprintf('Using existing audit user with ID %d for HelpdeskTicketSeeder.', $auditUserId));
        }

        // Gather IDs for foreign keys
        $userIds     = User::pluck('id');
        $categoryIds = HelpdeskCategory::pluck('id');
        $priorityIds = HelpdeskPriority::pluck('id');
        // Find users who could be helpdesk agents (could be filtered by role if needed)
        $assignedToUserIds = User::pluck('id');

        // Helper to get a random value from a collection
        $randomFrom = function ($collection) {
            return $collection->random();
        };

        // Create Open Tickets
        HelpdeskTicket::factory()->count(10)->create([
            'user_id'     => $randomFrom($userIds),
            'category_id' => $randomFrom($categoryIds),
            'priority_id' => $randomFrom($priorityIds),
            'status'      => HelpdeskTicket::STATUS_OPEN,
            'created_by'  => $auditUserId,
            'updated_by'  => $auditUserId,
        ]);
        Log::info('Created 10 "Open" helpdesk tickets.');

        // Create In Progress Tickets
        HelpdeskTicket::factory()->count(5)->create([
            'user_id'             => $randomFrom($userIds),
            'assigned_to_user_id' => $randomFrom($assignedToUserIds),
            'category_id'         => $randomFrom($categoryIds),
            'priority_id'         => $randomFrom($priorityIds),
            'status'              => HelpdeskTicket::STATUS_IN_PROGRESS,
            'created_by'          => $auditUserId,
            'updated_by'          => $auditUserId,
        ]);
        Log::info('Created 5 "In Progress" helpdesk tickets.');

        // Create Resolved Tickets
        HelpdeskTicket::factory()->count(3)->create([
            'user_id'             => $randomFrom($userIds),
            'assigned_to_user_id' => $randomFrom($assignedToUserIds),
            'category_id'         => $randomFrom($categoryIds),
            'priority_id'         => $randomFrom($priorityIds),
            'status'              => HelpdeskTicket::STATUS_RESOLVED,
            'resolution_notes'    => 'Issue resolved by technical support.',
            'created_by'          => $auditUserId,
            'updated_by'          => $auditUserId,
        ]);
        Log::info('Created 3 "Resolved" helpdesk tickets.');

        // Create Closed Tickets
        HelpdeskTicket::factory()->count(2)->create([
            'user_id'             => $randomFrom($userIds),
            'assigned_to_user_id' => $randomFrom($assignedToUserIds),
            'category_id'         => $randomFrom($categoryIds),
            'priority_id'         => $randomFrom($priorityIds),
            'status'              => HelpdeskTicket::STATUS_CLOSED,
            'resolution_notes'    => 'User confirmed resolution and ticket was closed.',
            'closed_at'           => now(),
            'closed_by_id'        => $randomFrom($assignedToUserIds),
            'created_by'          => $auditUserId,
            'updated_by'          => $auditUserId,
        ]);
        Log::info('Created 2 "Closed" helpdesk tickets.');

        Log::info('HelpdeskTicket seeding completed.');
    }
}
