<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $bpmStaffUser;

    protected User $itAdminUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles for both guards
        $guards = ['web', 'sanctum'];
        foreach ($guards as $guard) {
            Role::findOrCreate('Admin', $guard);
            Role::findOrCreate('BPM Staff', $guard);
            Role::findOrCreate('IT Admin', $guard);
            Role::findOrCreate('User', $guard);
        }

        // Create users and assign roles for both guards
        $this->adminUser = User::factory()->create();
        $this->bpmStaffUser = User::factory()->create();
        $this->itAdminUser = User::factory()->create();
        $this->regularUser = User::factory()->create();

        foreach ($guards as $guard) {
            $this->adminUser->assignRole(Role::findByName('Admin', $guard));
            $this->bpmStaffUser->assignRole(Role::findByName('BPM Staff', $guard));
            $this->itAdminUser->assignRole(Role::findByName('IT Admin', $guard));
            $this->regularUser->assignRole(Role::findByName('User', $guard));
        }

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** @test */
    public function admin_can_see_settings_reports_and_all_admin_sections_in_menu(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/dashboard');

        $response->assertOk();
        // Assert based on the visible text from the menu config's 'name' key
        $response->assertSeeText(__('menu.settings.title'));
        $response->assertSeeText(__('menu.reports.title'));
        $response->assertSeeText(__('menu.administration.equipment_management'));
        $response->assertSeeText(__('menu.administration.helpdesk_management')); // New Helpdesk Admin link
        $response->assertSeeText(__('menu.helpdesk.title')); // New Helpdesk link for all users
    }

    /** @test */
    public function bpm_staff_can_see_loan_management_and_reports(): void
    {
        $response = $this->actingAs($this->bpmStaffUser)->get('/dashboard');

        $response->assertOk();
        $response->assertSeeText(__('menu.administration.equipment_management'));
        $response->assertSeeText(__('menu.reports.title'));
        $response->assertSeeText(__('menu.helpdesk.title')); // BPM Staff can also see Helpdesk
        // Cannot see links for other roles or higher privileges
        $response->assertDontSeeText(__('menu.settings.title'));
        $response->assertDontSeeText(__('menu.administration.helpdesk_management')); // Not IT Admin helpdesk management
    }

    /** @test */
    public function it_admin_can_see_helpdesk_and_its_management_sections(): void
    {
        $response = $this->actingAs($this->itAdminUser)->get('/dashboard');

        $response->assertOk();
        // Can see their specific admin link for Helpdesk
        $response->assertSeeText(__('menu.administration.helpdesk_management'));
        $response->assertSeeText(__('menu.helpdesk.title')); // IT Admin can also see general Helpdesk

        // Cannot see links for other roles
        $response->assertDontSeeText(__('menu.administration.equipment_management'));
        $response->assertDontSeeText(__('menu.settings.title'));
    }

    /** @test */
    public function regular_user_sees_basic_menu_and_helpdesk_only(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/dashboard');

        $response->assertOk();
        // Can see links to create loan applications
        $response->assertSeeText(__('menu.apply_for_resources.loan'));
        $response->assertSeeText(__('menu.helpdesk.title')); // Regular user can see Helpdesk

        // Cannot see any admin, settings, or removed email links
        $response->assertDontSeeText(__('menu.administration.title'));
        $response->assertDontSeeText(__('menu.reports.title'));
        $response->assertDontSeeText(__('menu.settings.title'));
        // Ensure email application is no longer visible
        $response->assertDontSeeText(__('menu.apply_for_resources.email'));
    }
}
