<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule a command to process and send any unsent bulk messages or queued notifications.
        // This supports the system's notification features for various application events.
        $schedule->command('messages:send-unsent-bulk-messages')
            ->everyFiveMinutes()
            ->withoutOverlapping(); // Prevents the command from overlapping if a previous instance is still running.

        // REFINEMENT: Add scheduled command for ICT Equipment Loan Return Reminders
        // This command would query for loans nearing their due date or overdue and send reminders.
        // System Design Reference: 5.2 (EquipmentReturnReminderNotification)
        // Ensure 'reminders:send-loan-return' command is created in App\Console\Commands.
        $schedule->command('reminders:send-loan-return')
            ->daily() // Example: Run daily. Adjust frequency as needed (e.g., dailyAt('08:00')).
            ->withoutOverlapping()
            ->onOneServer(); // Ensures the task runs on only one server in a multi-server setup.

        // Example of another potential scheduled task mentioned implicitly by system needs:
        // Pruning old soft-deleted records or logs if not handled by other means.
        // $schedule->command('model:prune', [
        //     '--model' => [\App\Models\WebhookCall::class, \App\Models\AuditLog::class], // Example models
        // ])->daily()->at('02:00');

        // Schedule for cleaning up old webhook calls (if using spatie/laravel-webhook-client default cleanup)
        // The spatie/laravel-webhook-client package's config ('webhook-client.php') has 'delete_after_days'.
        // The package itself often includes a command like 'webhook-client:clean' that should be scheduled.
        // Example, if you have such a command provided by the package:
        // $schedule->command('webhook-client:clean')->daily()->at('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
