<?php

namespace Tests\Feature\tmp;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DebugHelpdeskAdminMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_matrix_access_levels()
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'IT Admin']);
        Role::firstOrCreate(['name' => 'User']);

        HelpdeskCategory::factory()->create(['name' => 'Software']);
        HelpdeskPriority::factory()->create(['name' => 'High', 'level' => 3]);

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('Admin');

        $itAdmin = User::factory()->create(['email_verified_at' => now()]);
        $itAdmin->assignRole('IT Admin');

        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('User');

        Sanctum::actingAs($admin);
        $r1 = $this->get(route('helpdesk.admin.tickets'));
        fwrite(STDOUT, "admin status: {$r1->status()}\n");

        Sanctum::actingAs($itAdmin);
        $r2 = $this->get(route('helpdesk.admin.tickets'));
        fwrite(STDOUT, "itadmin status: {$r2->status()}\n");

        Sanctum::actingAs($user);
        $r3 = $this->get(route('helpdesk.admin.tickets'));
        fwrite(STDOUT, "user status: {$r3->status()}\n");

        $r1->dump();
    }
}
