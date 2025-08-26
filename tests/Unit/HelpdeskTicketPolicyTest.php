<?php

namespace Tests\Unit;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Policies\HelpdeskTicketPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HelpdeskTicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'IT Admin']);
        Role::firstOrCreate(['name' => 'User']);
    }

    /** @test */
    public function admin_can_view_any_tickets()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->viewAny($admin));
    }

    /** @test */
    public function it_admin_can_view_any_tickets()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->viewAny($itAdmin));
    }

    /** @test */
    public function regular_user_cannot_view_any_tickets_globally()
    {
        $user = User::factory()->create();
        $user->assignRole('User');
        $policy = new HelpdeskTicketPolicy;
        $this->assertFalse($policy->viewAny($user));
    }

    /** @test */
    public function applicant_can_view_their_own_ticket()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->view($user, $ticket));
    }

    /** @test */
    public function user_cannot_view_other_users_ticket()
    {
        $user1  = User::factory()->create();
        $user2  = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user1->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertFalse($policy->view($user2, $ticket));
    }

    /** @test */
    public function admin_can_view_any_ticket()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->view($admin, $ticket));
    }

    /** @test */
    public function it_admin_can_view_any_ticket()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->view($itAdmin, $ticket));
    }

    /** @test */
    public function any_user_can_create_a_ticket()
    {
        $user   = User::factory()->create();
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->create($user));
    }

    /** @test */
    public function it_admin_can_update_a_ticket()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->update($itAdmin, $ticket));
    }

    /** @test */
    public function assigned_agent_can_update_their_assigned_ticket_if_not_closed()
    {
        $agent = User::factory()->create();
        $agent->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'assigned_to_user_id' => $agent->id, 'status' => 'open']);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->update($agent, $ticket));
    }

    /** @test */
    public function assigned_agent_cannot_update_a_closed_ticket()
    {
        $agent = User::factory()->create();
        $agent->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'assigned_to_user_id' => $agent->id, 'status' => 'closed']);
        $policy = new HelpdeskTicketPolicy;
        $this->assertFalse($policy->update($agent, $ticket));
    }

    /** @test */
    public function applicant_cannot_update_their_own_ticket()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertFalse($policy->update($user, $ticket));
    }

    /** @test */
    public function admin_can_delete_a_ticket()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $ticket = HelpdeskTicket::factory()->create();
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->delete($admin, $ticket));
    }

    /** @test */
    public function it_admin_cannot_delete_a_ticket()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $ticket = HelpdeskTicket::factory()->create();
        $policy = new HelpdeskTicketPolicy;
        $this->assertFalse($policy->delete($itAdmin, $ticket));
    }

    /** @test */
    public function applicant_can_add_comment_to_their_own_ticket()
    {
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->addComment($user, $ticket));
    }

    /** @test */
    public function assigned_agent_can_add_comment_to_ticket()
    {
        $agent = User::factory()->create();
        $agent->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id, 'assigned_to_user_id' => $agent->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->addComment($agent, $ticket));
    }

    /** @test */
    public function it_admin_can_add_comment_to_any_ticket()
    {
        $itAdmin = User::factory()->create();
        $itAdmin->assignRole('IT Admin');
        $user   = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        $policy = new HelpdeskTicketPolicy;
        $this->assertTrue($policy->addComment($itAdmin, $ticket));
    }
}
