<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Handles scheduling of console commands for MOTAC IRMS.
 * v4.0: Includes Helpdesk and loan-related scheduled tasks.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process unsent bulk messages or queued notifications every 5 minutes
        $schedule->command('messages:send-unsent-bulk-messages')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // Send ICT loan return reminders daily
        $schedule->command('reminders:send-loan-return')
            ->daily()
            ->withoutOverlapping()
            ->onOneServer();

        // Escalate overdue ICT loan returns and notify stakeholders every morning
        $schedule->command('loan:check-overdue-returns')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->onOneServer();

        // (Optional) Clean up old webhook calls if using Spatie package
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
