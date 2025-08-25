<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HelpdeskSLAStatusTest extends TestCase
{
    use RefreshDatabase;

    protected $category;
    protected $priority;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure necessary data exists for ticket creation
    $this->category = \App\Models\HelpdeskCategory::factory()->create(['name' => 'General']);
    $this->priority = \App\Models\HelpdeskPriority::factory()->create(['name' => 'Medium', 'level' => 2]);
    }

    /** @test */
    public function ticket_is_not_overdue_if_sla_due_date_is_in_future()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'priority_id' => $this->priority->id,
            'assigned_to_user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'sla_due_at' => Carbon::now()->addDay(),
            'status' => 'open'
        ]);

        $this->assertFalse($ticket->is_overdue);
    }

    /** @test */
    public function ticket_is_overdue_if_sla_due_date_has_passed_and_status_is_not_closed()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'priority_id' => $this->priority->id,
            'assigned_to_user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'sla_due_at' => Carbon::now()->subDay(),
            'status' => 'open' // Not closed
        ]);

        $this->assertTrue($ticket->is_overdue);

        $ticket->update(['status' => 'in_progress']);
        $this->assertTrue($ticket->is_overdue);
    }

    /** @test */
    public function ticket_is_not_overdue_if_status_is_closed_even_if_sla_due_date_passed()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'priority_id' => $this->priority->id,
            'assigned_to_user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'sla_due_at' => Carbon::now()->subDay(),
            'status' => 'closed'
        ]);

        $this->assertFalse($ticket->is_overdue);
    }

    /** @test */
    public function ticket_is_not_overdue_if_sla_due_at_is_null()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'priority_id' => $this->priority->id,
            'assigned_to_user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'sla_due_at' => null,
            'status' => 'open'
        ]);

        $this->assertFalse($ticket->is_overdue);
    }

    /** @test */
    public function sla_due_at_is_set_on_ticket_creation()
    {
        $user = User::factory()->create();
        $category = \App\Models\HelpdeskCategory::first();
        $priority = \App\Models\HelpdeskPriority::first();

    // Use the general NotificationService expected by HelpdeskService
    $ticket = (new \App\Services\HelpdeskService(new \App\Services\NotificationService()))->createTicket([
            'title' => 'SLA Test',
            'description' => 'SLA description',
            'category_id' => $category->id,
            'priority_id' => $priority->id,
        ], $user);

        $this->assertNotNull($ticket->sla_due_at);
        // Assuming default 48 hours, ensure it's around now + 48 hours
        $this->assertTrue(Carbon::now()->addHours(47)->lessThan($ticket->sla_due_at));
        $this->assertTrue(Carbon::now()->addHours(49)->greaterThan($ticket->sla_due_at));
    }
}
