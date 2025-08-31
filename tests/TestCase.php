<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @property \Illuminate\Foundation\Application   $app
 * @property \Illuminate\Contracts\Console\Kernel $artisan
 * @property string                               $baseUrl
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure at least one user exists for factories
        \App\Models\User::factory()->create();
        // Create a helpdesk category and priority only if none exist to avoid unique constraint collisions
        if (! \App\Models\HelpdeskCategory::query()->exists()) {
            \App\Models\HelpdeskCategory::factory()->create();
        }
        if (! \App\Models\HelpdeskPriority::query()->exists()) {
            \App\Models\HelpdeskPriority::factory()->create();
        }

        // Ensure required roles and permissions exist for the 'web' guard
        $roles = ['BPM', 'BPM Staff', 'IT Admin', 'Admin', 'Approver', 'Regular User'];
        foreach ($roles as $role) {
            foreach (['web', 'sanctum'] as $guard) {
                if (! \Spatie\Permission\Models\Role::where('name', $role)->where('guard_name', $guard)->exists()) {
                    \Spatie\Permission\Models\Role::create(['name' => $role, 'guard_name' => $guard]);
                }
            }
        }

        $permissions = ['viewAny', 'view helpdesk tickets', 'view settings', 'view reports', 'view loan management', 'App\\Models\\HelpdeskTicket'];
        foreach ($permissions as $permission) {
            foreach (['web', 'sanctum'] as $guard) {
                if (! \Spatie\Permission\Models\Permission::where('name', $permission)->where('guard_name', $guard)->exists()) {
                    \Spatie\Permission\Models\Permission::create(['name' => $permission, 'guard_name' => $guard]);
                }
            }
        }
    }
}
