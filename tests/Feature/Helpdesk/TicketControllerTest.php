<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests for the Helpdesk TicketController (web routes).
 * Covers creating, viewing, updating, and deleting tickets via the web interface.
 */
class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Notification::fake();

        // Set up categories and priorities
        HelpdeskCategory::factory()->create(['name' => 'Hardware']);
        HelpdeskPriority::factory()->create(['name' => 'Normal', 'level' => 2]);
    }

    /** @test */
    public function guest_cannot_access_ticket_routes()
    {
        $response = $this->get(route('helpdesk.tickets.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_view_ticket_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('helpdesk.tickets.index'));
        $response->assertStatus(200)
            ->assertViewIs('helpdesk.index');
    }

    /** @test */
    public function user_can_view_ticket_create_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('helpdesk.tickets.create'));
        $response->assertStatus(200)
            ->assertViewIs('helpdesk.create');
    }

    /** @test */
    public function user_can_create_a_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $data = [
            'title'       => 'Cannot print to printer',
            'description' => 'The office printer is not responding.',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
            'attachments' => [
                UploadedFile::fake()->create('log.txt', 10, 'text/plain'),
            ],
        ];

        $response = $this->post(route('helpdesk.tickets.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('helpdesk_tickets', [
            'title'   => 'Cannot print to printer',
            'user_id' => $user->id,
        ]);
        Storage::disk('public')->assertExists(
            HelpdeskTicket::first()->attachments()->first()->file_path
        );
    }

    /** @test */
    public function user_can_view_ticket_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('helpdesk.tickets.show', $ticket));
        $response->assertStatus(200)
            ->assertViewIs('helpdesk.show')
            ->assertViewHas('ticket', $ticket);
    }

    /** @test */
    public function user_can_update_own_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Old title',
        ]);
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $response = $this->put(route('helpdesk.tickets.update', $ticket), [
            'title'       => 'New title',
            'description' => 'Updated description',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ]);
        $response->assertRedirect(route('helpdesk.tickets.show', $ticket));

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id'          => $ticket->id,
            'title'       => 'New title',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function user_can_delete_own_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('helpdesk.tickets.destroy', $ticket));
        $response->assertRedirect(route('helpdesk.tickets.index'));

        $this->assertSoftDeleted('helpdesk_tickets', [
            'id' => $ticket->id,
        ]);
    }

    /** @test */
    public function user_cannot_update_others_ticket()
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->put(route('helpdesk.tickets.update', $ticket), [
            'title' => 'Hacked!',
        ]);
        $response->assertForbidden();
    }

    /** @test */
    public function user_cannot_delete_others_ticket()
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->delete(route('helpdesk.tickets.destroy', $ticket));
        $response->assertForbidden();
    }
}
