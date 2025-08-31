<?php

namespace Tests\Feature\tmp;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DebugHelpdeskAdminReproTest extends TestCase
{
    use RefreshDatabase;

    public function test_reproduce_admin_route_and_dump_response()
    {
        // mimic the AdminTicketManagementTest setUp
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'IT Admin']);
        Role::firstOrCreate(['name' => 'User']);

        HelpdeskCategory::factory()->create(['name' => 'Software']);
        HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('Admin');

        Sanctum::actingAs($admin);

        $response = $this->get(route('helpdesk.admin.tickets'));

        // Dump headers and status to help diagnose redirect
        fwrite(STDOUT, "Status: {$response->status()}\n");
        fwrite(STDOUT, "Headers:\n");
        foreach ($response->headers->allPreserveCase() as $k => $v) {
            fwrite(STDOUT, "  $k: ".implode(', ', $v)."\n");
        }

        $response->dump();
    }
}
