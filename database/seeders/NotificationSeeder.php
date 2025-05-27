<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Notification seeding...');

        // Consider if truncate is always desired, or if adding to existing is sometimes needed.
        // For a clean seed, truncate is fine.
        if (config('app.env') !== 'production') { // Safety check
            DB::table('notifications')->truncate();
            Log::info('Truncated notifications table (non-production environment).');
        }


        if (User::count() === 0) {
            Log::error('No Users found. Cannot seed Notifications. Please run UserSeeder first.');
            return;
        }
        Log::info('Found '.User::count().' Users for potential notification assignment.');

        $totalNotificationsToCreate = 50; // Reduced for quicker seeding, adjust as needed
        if ($totalNotificationsToCreate === 0) {
            Log::info('NotificationSeeder: totalNotificationsToCreate is 0, no new notifications will be created by factory.');
        } else {
            Log::info("Creating {$totalNotificationsToCreate} random notifications using factory...");
            Notification::factory()->count($totalNotificationsToCreate)->create();
            Log::info("Created {$totalNotificationsToCreate} notifications via factory.");
        }


        // Mark some as read (correctly updating existing notifications)
        $unreadNotificationsCount = Notification::whereNull('read_at')->count();
        if ($unreadNotificationsCount > 0) {
            $numToMarkRead = (int) min($unreadNotificationsCount, ceil($totalNotificationsToCreate * 0.3)); // Mark up to 30% of created or available unread

            if ($numToMarkRead > 0) {
                $notificationsToMarkRead = Notification::whereNull('read_at')
                    ->inRandomOrder()
                    ->limit($numToMarkRead)
                    ->get();

                foreach ($notificationsToMarkRead as $notification) {
                    $notification->markAsRead(); // Uses the method from App\Models\Notification
                }
                Log::info("Marked {$notificationsToMarkRead->count()} notifications as read.");
            } else {
                Log::info('No notifications to mark as read based on calculation.');
            }
        } else {
            Log::info('No unread notifications found to mark as read.');
        }

        Log::info('Notification seeding complete.');
    }
}
