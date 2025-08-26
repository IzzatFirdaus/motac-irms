<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTicketManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'IT Admin']);
        Role::firstOrCreate(['name' => 'User']);

        // Create necessary categories and priorities
        HelpdeskCategory::factory()->create(['name' => 'Software']);
        HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);
    }

    /** @test */
    public function only_it_admins_or_admins_can_view_ticket_management_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');

        $user = User::factory()->create();
        $user->assignRole('User');

        $this->actingAs($admin)->get(route('helpdesk.admin.index'))->assertOk();
        $this->actingAs($itAdmin)->get(route('helpdesk.admin.index'))->assertOk();
        $this->actingAs($user)->get(route('helpdesk.admin.index'))->assertForbidden(); // Users cannot access
    }

    /** @test */
    public function it_admin_can_update_ticket_status_and_assignment()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');

        $applicant = User::factory()->create();
        $agent     = User::factory()->create();
        $agent->assignRole('IT Admin');

        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $ticket = HelpdeskTicket::create([
            'title'       => 'Test Ticket',
            'description' => 'Test description',
            'category_id' => $category->id,
            'status'      => 'open',
            'priority_id' => $priority->id,
            'user_id'     => $applicant->id,
        ]);

        Livewire::actingAs($itAdmin)
            ->test(\App\Livewire\Helpdesk\Admin\TicketManagement::class)
            ->call('openEditModal', $ticket)
            ->set('editStatus', 'in_progress')
            ->set('editAssignedTo', $agent->id)
            ->call('updateTicket');

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertEquals($agent->id, $ticket->assigned_to_user_id);
    }

    /** @test */
    public function assigned_agent_can_update_their_assigned_ticket_status()
    {
        $applicant = User::factory()->create();
        $agent     = User::factory()->create();
        $agent->assignRole('IT Admin');

        $category = HelpdeskCategory::first();
        $priority = HelpdeskPriority::first();

        $ticket = HelpdeskTicket::create([
            'title'               => 'Test Ticket for Agent',
            'description'         => 'Test description',
            'category_id'         => $category->id,
            'status'              => 'open',
            'priority_id'         => $priority->id,
            'user_id'             => $applicant->id,
            'assigned_to_user_id' => $agent->id,
        ]);

        Livewire::actingAs($agent)
            ->test(\App\Livewire\Helpdesk\Admin\TicketManagement::class)
            ->call('openEditModal', $ticket)
            ->set('editStatus', 'resolved')
            ->call('updateTicket');

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->closed_at); // Should be closed when resolved
    }

    /** @test */
    public function it_admin_can_filter_tickets()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');

        $category1 = HelpdeskCategory::firstOrCreate(['name' => 'Software']);
        $category2 = HelpdeskCategory::firstOrCreate(['name' => 'Network']);

        $priority1 = HelpdeskPriority::firstOrCreate(['name' => 'Low', 'level' => 1]);
        $priority2 = HelpdeskPriority::firstOrCreate(['name' => 'High', 'level' => 3]);

        HelpdeskTicket::factory()->create([
            'title' => 'Ticket A', 'status' => 'open', 'category_id' => $category1->id, 'priority_id' => $priority1->id,
        ]);
        HelpdeskTicket::factory()->create([
            'title' => 'Ticket B', 'status' => 'in_progress', 'category_id' => $category2->id, 'priority_id' => $priority2->id,
        ]);
        HelpdeskTicket::factory()->create([
            'title' => 'Ticket C', 'status' => 'resolved', 'category_id' => $category1->id, 'priority_id' => $priority2->id,
        ]);

        Livewire::actingAs($itAdmin)
            ->test(\App\Livewire\Helpdesk\Admin\TicketManagement::class)
            ->set('statusFilter', 'open')
            ->assertSee('Ticket A')
            ->assertDontSee('Ticket B')
            ->assertDontSee('Ticket C');

        Livewire::actingAs($itAdmin)
            ->test(\App\Livewire\Helpdesk\Admin\TicketManagement::class)
            ->set('categoryFilter', $category2->id)
            ->assertDontSee('Ticket A')
            ->assertSee('Ticket B')
            ->assertDontSee('Ticket C');

        Livewire::actingAs($itAdmin)
            ->test(\App\Livewire\Helpdesk\Admin\TicketManagement::class)
            ->set('search', 'Ticket C')
            ->assertDontSee('Ticket A')
            ->assertDontSee('Ticket B')
            ->assertSee('Ticket C');
    }
}
