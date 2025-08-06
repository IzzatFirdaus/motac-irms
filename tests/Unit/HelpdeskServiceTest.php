<?php

namespace Tests\Unit;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentAddedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use App\Services\HelpdeskService;
use App\Services\TicketNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Mockery;

class HelpdeskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;
    protected $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Mock the TicketNotificationService
        $this->notificationServiceMock = Mockery::mock(TicketNotificationService::class);
        $this->app->instance(TicketNotificationService::class, $this->notificationServiceMock);

        $this->helpdeskService = $this->app->make(HelpdeskService::class);

        // Create roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'IT Admin']);
        Role::firstOrCreate(['name' => 'User']);

        // Create base data
        HelpdeskCategory::factory()->create(['name' => 'General']);
        HelpdeskPriority::factory()->create(['name' => 'Low', 'level' => 1]);
        HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);
    }

    /** @test */
    public function it_can_create_a_ticket()
    {
        $user = User::factory()->create();
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $data = [
            'title' => 'Test Ticket Title',
            'description' => 'Test ticket description.',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ];

        // Expect notification service to be called
        $this->notificationServiceMock->shouldReceive('notifyTicketCreated')->once();

        $ticket = $this->helpdeskService->createTicket($data, $user);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'title' => 'Test Ticket Title',
            'user_id' => $user->id,
            'status' => 'open',
        ]);
        $this->assertNotNull($ticket->sla_due_at);
    }

    /** @test */
    public function it_can_add_a_comment_to_a_ticket()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $commentText = 'This is a test comment.';

        // Expect notification service to be called
        $this->notificationServiceMock->shouldReceive('notifyTicketCommentAdded')->once();

        $comment = $this->helpdeskService->addComment($ticket, $commentText, $user);

        $this->assertInstanceOf(HelpdeskComment::class, $comment);
        $this->assertDatabaseHas('helpdesk_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $commentText,
        ]);
        $this->assertFalse($comment->is_internal);
    }

    /** @test */
    public function it_can_add_an_internal_comment_to_a_ticket_by_it_admin()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $ticket = HelpdeskTicket::factory()->create(['user_id' => User::factory()->create()->id]);

        $commentText = 'This is an internal note.';

        // Expect notification service to be called
        $this->notificationServiceMock->shouldReceive('notifyTicketCommentAdded')->once();

        $comment = $this->helpdeskService->addComment($ticket, $commentText, $itAdmin, [], true);

        $this->assertTrue($comment->is_internal);
        $this->assertDatabaseHas('helpdesk_comments', [
            'id' => $comment->id,
            'is_internal' => true,
        ]);
    }


    /** @test */
    public function it_can_update_ticket_status()
    {
        $user = User::factory()->create();
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'status' => 'open']);

        // Expect notification service to be called
        $this->notificationServiceMock->shouldReceive('notifyTicketStatusUpdated')->once();

        $updatedTicket = $this->helpdeskService->updateTicketStatus($ticket, 'in_progress', null, null, $itAdmin);

        $this->assertEquals('in_progress', $updatedTicket->status);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_can_assign_a_ticket()
    {
        $user = User::factory()->create();
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $agent = User::factory()->create();
        $agent->assignRole('IT Admin');

        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'assigned_to_user_id' => null]);

        // Expect notification service to be called for status update and assignment
        $this->notificationServiceMock->shouldReceive('notifyTicketStatusUpdated')->once();
        $this->notificationServiceMock->shouldReceive('notifyTicketAssigned')->once();


        $updatedTicket = $this->helpdeskService->updateTicketStatus($ticket, 'open', $agent->id, null, $itAdmin);

        $this->assertEquals($agent->id, $updatedTicket->assigned_to_user_id);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'assigned_to_user_id' => $agent->id,
        ]);
    }

    /** @test */
    public function it_sets_closed_at_when_status_becomes_closed()
    {
        $user = User::factory()->create();
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'status' => 'open', 'closed_at' => null]);

        $this->notificationServiceMock->shouldReceive('notifyTicketStatusUpdated')->once();

        $updatedTicket = $this->helpdeskService->updateTicketStatus($ticket, 'closed', null, 'Issue resolved.', $itAdmin);

        $this->assertNotNull($updatedTicket->closed_at);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
            'resolution_notes' => 'Issue resolved.',
        ]);
    }

    /** @test */
    public function it_nullifies_closed_at_when_status_changes_from_closed()
    {
        $user = User::factory()->create();
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'status' => 'closed', 'closed_at' => now()]);

        $this->notificationServiceMock->shouldReceive('notifyTicketStatusUpdated')->once();

        $updatedTicket = $this->helpdeskService->updateTicketStatus($ticket, 'open', null, null, $itAdmin);

        $this->assertNull($updatedTicket->closed_at);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'status' => 'open',
            'closed_at' => null,
        ]);
    }
}
