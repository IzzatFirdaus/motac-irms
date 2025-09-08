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
        // Ensure base tables exist before attempting to seed (in-memory sqlite runs migrations lazily)
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            return; // Migrations not yet run for this test; skip global seeding
        }
            // Ensure at least one user exists for factories
            if (! \App\Models\User::query()->exists()) {
                \App\Models\User::factory()->create();
            }
        

        // Do not globally pre-seed helpdesk categories/priorities here to avoid
        // unique constraint conflicts with tests which explicitly create them.
        // Individual tests will create the categories/priorities they require
        // via factory() or firstOrCreate().

        // Ensure required roles and permissions exist for the 'web' guard
        $roles = ['BPM', 'BPM Staff', 'IT Admin', 'Admin', 'Approver', 'Regular User'];
        foreach ($roles as $role) {
            foreach (['web', 'sanctum'] as $guard) {
                if (! \Spatie\Permission\Models\Role::where('name', $role)->where('guard_name', $guard)->exists()) {
                    \Spatie\Permission\Models\Role::create(['name' => $role, 'guard_name' => $guard]);
                }
            }
        }

        $permissions = ['viewAny', 'view helpdesk tickets', 'view settings', 'view reports', 'view loan management', \App\Models\HelpdeskTicket::class];
        foreach ($permissions as $permission) {
            foreach (['web', 'sanctum'] as $guard) {
                if (! \Spatie\Permission\Models\Permission::where('name', $permission)->where('guard_name', $guard)->exists()) {
                    \Spatie\Permission\Models\Permission::create(['name' => $permission, 'guard_name' => $guard]);
                }
            }
        }
    }
}
