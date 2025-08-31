<?php

namespace Tests\Feature\tmp;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DebugHelpdeskRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_helpdesk_redirect()
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('Admin');

        Sanctum::actingAs($admin);

        $response = $this->get(route('helpdesk.admin.tickets'));

        // Dump the full response for inspection
        $response->dump();
    }
}
