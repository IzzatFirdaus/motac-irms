<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Fake the storage for file uploads
        // Create necessary categories and priorities for tests
        HelpdeskCategory::factory()->create(['name' => 'Hardware']);
        HelpdeskPriority::factory()->create(['name' => 'Medium', 'level' => 2]);
    }

    /** @test */
    public function a_user_can_create_a_helpdesk_ticket()
    {
        $user = User::factory()->create();
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketForm::class)
            ->set('title', 'My Printer is Not Working')
            ->set('description', 'The printer in my office is not printing anything.')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->call('createTicket')
            ->assertRedirect(route('helpdesk.view', HelpdeskTicket::first()->id)); // Assert redirect to new ticket

        $this->assertDatabaseHas('helpdesk_tickets', [
            'user_id' => $user->id,
            'title' => 'My Printer is Not Working',
            'description' => 'The printer in my office is not printing anything.',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
            'status' => 'open',
        ]);

        // Assert that an SLA due date is set
        $this->assertNotNull(HelpdeskTicket::first()->sla_due_at);
    }

    /** @test */
    public function a_user_can_upload_attachments_when_creating_a_ticket()
    {
        $user = User::factory()->create();
        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $file = UploadedFile::fake()->image('document.jpg', 100, 100)->size(500); // 500KB

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketForm::class)
            ->set('title', 'Need Software Installation')
            ->set('description', 'Requesting installation of Adobe Photoshop.')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->set('attachments', [$file])
            ->call('createTicket')
            ->assertRedirect(route('helpdesk.view', HelpdeskTicket::first()->id));

        $ticket = HelpdeskTicket::first();
        $this->assertCount(1, $ticket->attachments);
        Storage::disk('public')->assertExists($ticket->attachments->first()->file_path);
    }

    /** @test */
    public function guests_cannot_create_a_helpdesk_ticket()
    {
        $this->get(route('helpdesk.create'))->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_title_description_category_and_priority()
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketForm::class)
            ->set('title', '')
            ->set('description', '')
            ->set('category_id', '')
            ->set('priority_id', '')
            ->call('createTicket')
            ->assertHasErrors(['title', 'description', 'category_id', 'priority_id']);
    }
}
