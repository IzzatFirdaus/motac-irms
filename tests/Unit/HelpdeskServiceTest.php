<?php

namespace Tests\Unit;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\HelpdeskService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

/**
 * Unit tests for HelpdeskService.
 * Ensures business logic for ticket creation, updating, commenting, closing, and attachments works as expected,
 * and verifies NotificationService integration.
 */
class HelpdeskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $helpdeskService;

    protected $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Mock NotificationService and bind it in the container.
        $this->notificationServiceMock = Mockery::mock([NotificationService::class]);
        $this->app->instance(NotificationService::class, $this->notificationServiceMock);

        $this->helpdeskService = $this->app->make(HelpdeskService::class);

        // Seed base categories and priorities
        HelpdeskCategory::factory()->create(['name' => 'General']);
        HelpdeskPriority::factory()->create(['name' => 'Low', 'level' => 1]);
        HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);
    }

    /** @test */
    public function it_can_create_a_ticket_and_send_notification()
    {
        $user     = User::factory()->create();
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $data = [
            'title'       => 'Printer not working',
            'description' => 'Cannot print from any application.',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ];
        $attachments = [
            UploadedFile::fake()->create('error.txt', 10, 'text/plain'),
        ];

        // NotificationService should be called for ticket creation (to applicant)
        $this->notificationServiceMock
            ->shouldReceive('notifyTicketCreated')
            ->with($user, Mockery::type(HelpdeskTicket::class), 'applicant')
            ->once();

        $ticket = $this->helpdeskService->createTicket($data, $user, $attachments);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id'      => $ticket->id,
            'title'   => $data['title'],
            'user_id' => $user->id,
            'status'  => HelpdeskTicket::STATUS_OPEN,
        ]);
        $this->assertNotNull($ticket->sla_due_at);
        $this->assertCount(1, $ticket->attachments);
        Storage::disk('public')->assertExists($ticket->attachments->first()->file_path);
    }

    /** @test */
    public function it_can_add_a_comment_and_send_notifications()
    {
        $user        = User::factory()->create();
        $ticket      = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $commentText = 'Here is a screenshot.';
        $attachments = [
            UploadedFile::fake()->image('screenshot.png'),
        ];

        // Notification to applicant (ticket owner)
        $this->notificationServiceMock
            ->shouldReceive('notifyTicketCommentAdded')
            ->with($ticket->user, Mockery::type(HelpdeskComment::class), $user, 'applicant')
            ->once();

        // No assignee, so no second notification

        $comment = $this->helpdeskService->addComment($ticket, $commentText, $user, $attachments);

        $this->assertInstanceOf(HelpdeskComment::class, $comment);
        $this->assertDatabaseHas('helpdesk_comments', [
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'comment'     => $commentText,
            'is_internal' => false,
        ]);
        $this->assertCount(1, $comment->attachments);
        Storage::disk('public')->assertExists($comment->attachments->first()->file_path);
    }

    /** @test */
    public function it_can_add_an_internal_comment_and_send_notifications()
    {
        $user        = User::factory()->create();
        $ticket      = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $commentText = 'Internal note.';

        $this->notificationServiceMock
            ->shouldReceive('notifyTicketCommentAdded')
            ->with($ticket->user, Mockery::type(HelpdeskComment::class), $user, 'applicant')
            ->once();

        $comment = $this->helpdeskService->addComment($ticket, $commentText, $user, [], true);

        $this->assertTrue($comment->is_internal);
        $this->assertDatabaseHas('helpdesk_comments', [
            'id'          => $comment->id,
            'is_internal' => true,
        ]);
    }

    /** @test */
    public function it_can_update_a_ticket_and_send_notifications_on_status_and_assignment_change()
    {
        $user     = User::factory()->create();
        $assignee = User::factory()->create();
        $ticket   = HelpdeskTicket::factory()->create([
            'user_id'             => $user->id,
            'assigned_to_user_id' => null,
            'status'              => HelpdeskTicket::STATUS_OPEN,
        ]);
        $data = [
            'status'              => HelpdeskTicket::STATUS_IN_PROGRESS,
            'assigned_to_user_id' => $assignee->id,
            'title'               => 'Updated title',
        ];

        // Should notify status update to applicant and assignee, and assignment to assignee
        $this->notificationServiceMock
            ->shouldReceive('notifyTicketStatusUpdated')
            ->with($user, Mockery::type(HelpdeskTicket::class), $assignee, 'applicant')
            ->once();
        $this->notificationServiceMock
            ->shouldReceive('notifyTicketStatusUpdated')
            ->with($assignee, Mockery::type(HelpdeskTicket::class), $assignee, 'assignee')
            ->once();
        $this->notificationServiceMock
            ->shouldReceive('notifyTicketAssigned')
            ->with($assignee, Mockery::type(HelpdeskTicket::class), $assignee)
            ->once();

        $updated = $this->helpdeskService->updateTicket($ticket, $data, $assignee);

        $this->assertEquals(HelpdeskTicket::STATUS_IN_PROGRESS, $updated->status);
        $this->assertEquals($assignee->id, $updated->assigned_to_user_id);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id'                  => $ticket->id,
            'status'              => HelpdeskTicket::STATUS_IN_PROGRESS,
            'assigned_to_user_id' => $assignee->id,
            'title'               => 'Updated title',
        ]);
    }

    /** @test */
    public function it_sets_closed_at_when_status_becomes_closed_and_notifies()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id'   => $user->id,
            'status'    => HelpdeskTicket::STATUS_OPEN,
            'closed_at' => null,
        ]);
        $closer = User::factory()->create();

        $data = [
            'status'           => HelpdeskTicket::STATUS_CLOSED,
            'resolution_notes' => 'Fixed!',
        ];

        $this->notificationServiceMock
            ->shouldReceive('notifyTicketStatusUpdated')
            ->with($user, Mockery::type(HelpdeskTicket::class), $closer, 'applicant')
            ->once();

        $updated = $this->helpdeskService->updateTicket($ticket, $data, $closer);

        $this->assertEquals(HelpdeskTicket::STATUS_CLOSED, $updated->status);
        $this->assertNotNull($updated->closed_at);
        $this->assertEquals('Fixed!', $updated->resolution_notes);
    }

    /** @test */
    public function it_nullifies_closed_at_when_status_changes_from_closed_and_notifies()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id'   => $user->id,
            'status'    => HelpdeskTicket::STATUS_CLOSED,
            'closed_at' => now(),
        ]);
        $updater = User::factory()->create();

        $data = [
            'status' => HelpdeskTicket::STATUS_OPEN,
        ];

        $this->notificationServiceMock
            ->shouldReceive('notifyTicketStatusUpdated')
            ->with($user, Mockery::type(HelpdeskTicket::class), $updater, 'applicant')
            ->once();

        $updated = $this->helpdeskService->updateTicket($ticket, $data, $updater);

        $this->assertEquals(HelpdeskTicket::STATUS_OPEN, $updated->status);
        $this->assertNull($updated->closed_at);
    }

    /** @test */
    public function it_can_close_a_ticket_and_send_notification()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id'          => $user->id,
            'status'           => HelpdeskTicket::STATUS_IN_PROGRESS,
            'closed_at'        => null,
            'resolution_notes' => null,
        ]);
        $closer = User::factory()->create();

        $this->notificationServiceMock
            ->shouldReceive('notifyTicketStatusUpdated')
            ->with($user, Mockery::type(HelpdeskTicket::class), $closer, 'applicant')
            ->once();

        $data = [
            'resolution_notes' => 'Replaced the cable.',
        ];

        $closedTicket = $this->helpdeskService->closeTicket($ticket, $data, $closer);

        $this->assertEquals(HelpdeskTicket::STATUS_CLOSED, $closedTicket->status);
        $this->assertNotNull($closedTicket->closed_at);
        $this->assertEquals('Replaced the cable.', $closedTicket->resolution_notes);
        $this->assertEquals($closer->id, $closedTicket->closed_by_id);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
