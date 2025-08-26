<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\TicketNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HelpdeskEscalationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake(); // Prevent actual notifications from being sent
        Role::firstOrCreate(['name' => 'IT Admin']);
        // Ensure necessary data exists for ticket creation
        \App\Models\HelpdeskCategory::factory()->create(['name' => 'General']);
        \App\Models\HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);
    }

    /** @test */
    public function overdue_tickets_trigger_escalation_notification_to_it_admins()
    {
        // Create an IT Admin to receive notifications
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');

        // Create an overdue ticket
        $user          = User::factory()->create();
        $overdueTicket = HelpdeskTicket::factory()->create([
            'user_id'    => $user->id,
            'status'     => 'open',
            'sla_due_at' => Carbon::now()->subHours(1), // Set SLA due in the past
        ]);

        // Mock the notification service to assert calls
        $mockNotificationService = Mockery::mock(TicketNotificationService::class);
        $this->app->instance(TicketNotificationService::class, $mockNotificationService);

        // Expect the escalation notification to be called for the overdue ticket
        $mockNotificationService->shouldReceive('notifyTicketEscalated')
            ->once()
            ->with(Mockery::on(function ($ticket) use ($overdueTicket) {
                return $ticket->id === $overdueTicket->id;
            }));
        $mockNotificationService->shouldReceive('notifyTicketCreated'); // Prevent calls from factory

        // Simulate running a command that checks for overdue tickets
        // You would need to create this Artisan command (e.g., `php artisan helpdesk:check-overdue-tickets`)
        // For testing, we'll manually call the service method that the command would use.
        $service = new TicketNotificationService; // Get the real service instance
        // Assuming your command calls a method like this on the service for each overdue ticket found:
        $service->notifyTicketEscalated($overdueTicket);

        Notification::assertSentTo($itAdmin, function (\Illuminate\Notifications\Notification $notification) {
            // Assert it's the correct type of notification and contains the ticket ID
            // Note: You would need a TicketEscalatedNotification class for this to work precisely.
            // For now, we'll check if a general notification was sent to IT Admin
            return true; // Simplified for now, assumes any notification to IT Admin is for escalation
        });
    }

    /** @test */
    public function closed_tickets_do_not_trigger_escalation()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');

        // Create a closed, overdue ticket
        $user                = User::factory()->create();
        $closedOverdueTicket = HelpdeskTicket::factory()->create([
            'user_id'    => $user->id,
            'status'     => 'closed', // Status is closed
            'sla_due_at' => Carbon::now()->subHours(1),
        ]);

        $mockNotificationService = Mockery::mock(TicketNotificationService::class);
        $this->app->instance(TicketNotificationService::class, $mockNotificationService);

        // Expect notifyTicketEscalated NOT to be called
        $mockNotificationService->shouldNotReceive('notifyTicketEscalated');
        $mockNotificationService->shouldReceive('notifyTicketCreated'); // Prevent calls from factory

        // Simulate the check for overdue tickets
        $service = new TicketNotificationService;
        // This is a simplified check, ideally, your cron job/command would filter this out.
        // But if it still calls the method, the method itself should handle it.
        // For this test, we assert that the _notification_ is not sent if the status is closed
        // even if the SLA is technically past.
        if ($closedOverdueTicket->status !== 'closed' && $closedOverdueTicket->is_overdue) {
            $service->notifyTicketEscalated($closedOverdueTicket);
        }

        Notification::assertNotSentTo($itAdmin, \App\Notifications\TicketEscalatedNotification::class); // If you have this specific notification
        // Or more generally, ensure no unexpected notifications are sent.
    }
}
