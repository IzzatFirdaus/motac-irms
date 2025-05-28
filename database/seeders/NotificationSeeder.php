<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
// Example: If notifications are linked to specific application types polymorphically
// use App\Models\LoanApplication;
// use App\Models\EmailApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // Your custom Notification model as per Revision 3

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Notification seeding (Revision 3)...');

        // Assuming your custom Notification model and its table migration exist as per Revision 3
        // The table should have id (UUID), type, notifiable_type, notifiable_id, data, read_at, and blameable/timestamps.
        if (!Schema::hasTable('notifications')) {
            Log::error('Custom notifications table does not exist. Skipping NotificationSeeder.');
            return;
        }

        DB::table('notifications')->truncate();
        Log::info('Truncated custom notifications table.');

        if (User::count() === 0) {
            Log::error('No Users found. Cannot seed Notifications as they require a notifiable user. Please run UserSeeder first.');
            return;
        }
        Log::info('Found ' . User::count() . ' Users for potential notification assignment.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;
        if (!$auditUserId) {
            // This case should ideally not be hit if UserSeeder runs first.
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (NotifSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for NotificationSeeder blameable fields.");
        }


        $totalNotificationsToCreate = 50; // Create fewer for more targeted examples initially
        Log::info("Creating {$totalNotificationsToCreate} random notifications using factory (Revision 3)...");

        // The NotificationFactory needs to be robust:
        // - Pick a random existing User as 'notifiable'.
        // - Set 'type' to a valid Notification class string (e.g., App\Notifications\ApplicationSubmitted::class).
        // - Populate 'data' with realistic JSON for that notification type.
        // - Set 'created_by' and 'updated_by' to $auditUserId (or handle via BlameableObserver).
        if (class_exists(Notification::class) && method_exists(Notification::class, 'factory')) {
            Notification::factory()
              ->count($totalNotificationsToCreate)
              ->create([
                // Override created_by/updated_by if your factory doesn't handle it or if you want specific audit user for all.
                // 'created_by' => $auditUserId, // The blameable observer should handle this if auth user is set.
                // 'updated_by' => $auditUserId, // For seeders, it's often fine if these are null or set by factory to a random user.
                // Revision 3 notification table has these as nullable.
              ]);
            Log::info("Created {$totalNotificationsToCreate} notifications.");

            // Mark some as read
            $numReadNotifications = (int) ($totalNotificationsToCreate * 0.3);
            if ($numReadNotifications > 0) {
                $notificationsToMarkRead = Notification::whereNull('read_at')
                  ->inRandomOrder()
                  ->limit($numReadNotifications)
                  ->get();

                foreach ($notificationsToMarkRead as $notification) {
                    $notification->update(['read_at' => now(), 'updated_by' => $auditUserId]); // Also update 'updated_by'
                }
                Log::info("Marked up to {$notificationsToMarkRead->count()} notifications as read.");
            }
        } else {
            Log::error('App\Models\Notification model or its factory not found. Cannot seed notifications.');
        }
        Log::info('Notification seeding complete (Revision 3).');
    }
}
