<?php

namespace Database\Seeders;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpdeskTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting HelpdeskTicket seeding...');

        if (User::count() === 0 || HelpdeskCategory::count() === 0 || HelpdeskPriority::count() === 0) {
            Log::error('HelpdeskTicketSeeder requires at least one User, one HelpdeskCategory, and one HelpdeskPriority record. Aborting.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('helpdesk_tickets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated helpdesk_tickets table.');

        $auditUser = User::orderBy('id')->first();
        $auditUserId = $auditUser?->id;

        if (! $auditUserId) {
            $auditUser = User::factory()->create(['name' => 'Audit User (HelpdeskTicketSeeder)']);
            $auditUserId = $auditUser->id;
            Log::info(sprintf('Created a fallback audit user with ID %d for HelpdeskTicketSeeder.', $auditUserId));
        } else {
            Log::info(sprintf('Using existing audit user with ID %d for HelpdeskTicketSeeder.', $auditUserId));
        }

        $userIds = User::pluck('id');
        $categoryIds = HelpdeskCategory::pluck('id');
        $priorityIds = HelpdeskPriority::pluck('id');
        $assignedToUserIds = User::whereHas('roles', fn ($q) => $q->where('name', 'Helpdesk Support'))->pluck('id');

        if ($assignedToUserIds->isEmpty()) {
            Log::warning('No "Helpdesk Support" users found. Creating one for seeding purposes.');
            $supportUser = User::factory()->create(['name' => 'Helpdesk Support (Seeder)']);
            // Attach a role if your application uses a roles system, e.g., $supportUser->assignRole('Helpdesk Support');
            $assignedToUserIds = collect([$supportUser->id]);
        }

        // Open Tickets
        HelpdeskTicket::factory()->count(10)->create([
            'user_id' => $userIds->random(),
            'category_id' => $categoryIds->random(),
            'priority_id' => $priorityIds->random(),
            'status' => HelpdeskTicket::STATUS_OPEN, // Changed
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
        ]);
        Log::info('Created 10 "Open" tickets.');

        // In Progress
        HelpdeskTicket::factory()->count(5)->create([
            'user_id' => $userIds->random(),
            'assigned_to_user_id' => $assignedToUserIds->random(),
            'category_id' => $categoryIds->random(),
            'priority_id' => $priorityIds->random(),
            'status' => HelpdeskTicket::STATUS_IN_PROGRESS, // Changed
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
        ]);
        Log::info('Created 5 "In Progress" tickets.');

        // Resolved
        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $userIds->random(),
            'assigned_to_user_id' => $assignedToUserIds->random(),
            'category_id' => $categoryIds->random(),
            'priority_id' => $priorityIds->random(),
            'status' => HelpdeskTicket::STATUS_RESOLVED, // Changed
            'resolution_notes' => 'Issue resolved by technical support.',
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
        ]);
        Log::info('Created 3 "Resolved" tickets.');

        // Closed
        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $userIds->random(),
            'assigned_to_user_id' => $assignedToUserIds->random(),
            'category_id' => $categoryIds->random(),
            'priority_id' => $priorityIds->random(),
            'status' => HelpdeskTicket::STATUS_CLOSED, // Changed
            'resolution_notes' => 'User confirmed resolution and ticket was closed.',
            'closed_at' => now(),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
        ]);
        Log::info('Created 2 "Closed" tickets.');

        Log::info('HelpdeskTicket seeding completed.');
    }
}
