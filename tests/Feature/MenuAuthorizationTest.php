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

        // Create roles
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('BPM Staff', 'web');
        Role::findOrCreate('IT Admin', 'web');
        Role::findOrCreate('User', 'web');

        // Create users and assign roles
        $this->adminUser = User::factory()->create()->assignRole('Admin');
        $this->bpmStaffUser = User::factory()->create()->assignRole('BPM Staff');
        $this->itAdminUser = User::factory()->create()->assignRole('IT Admin');
        $this->regularUser = User::factory()->create()->assignRole('User');
    }

    public function test_admin_can_see_settings_and_reports_in_menu(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/dashboard');

        $response->assertOk();
        // Assert based on the visible text from the menu config's 'name' key
        $response->assertSeeText(__('menu.settings.title'));
        $response->assertSeeText(__('menu.reports.title'));
        $response->assertSeeText(__('menu.system_logs'));
    }

    public function test_bpm_staff_can_see_equipment_management_but_not_settings(): void
    {
        $response = $this->actingAs($this->bpmStaffUser)->get('/dashboard');

        $response->assertOk();
        // Can see their specific admin link
        $response->assertSeeText(__('menu.administration.equipment_management'));
        $response->assertSeeText(__('menu.reports.title'));

        // Cannot see links for other roles or higher privileges
        $response->assertDontSeeText(__('menu.settings.title'));
        $response->assertDontSeeText(__('menu.administration.email_applications'));
    }

    public function test_it_admin_can_see_email_processing_but_not_equipment_management(): void
    {
        $response = $this->actingAs($this->itAdminUser)->get('/dashboard');

        $response->assertOk();
        // Can see their specific admin link
        $response->assertSeeText(__('menu.administration.email_applications'));

        // Cannot see links for other roles
        $response->assertDontSeeText(__('menu.administration.equipment_management'));
        $response->assertDontSeeText(__('menu.settings.title'));
    }

    public function test_regular_user_sees_basic_menu_only(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/dashboard');

        $response->assertOk();
        // Can see links to create applications
        $response->assertSeeText(__('menu.apply_for_resources.loan'));
        $response->assertSeeText(__('menu.apply_for_resources.email'));

        // Cannot see any admin or settings links
        $response->assertDontSeeText(__('menu.administration.title'));
        $response->assertDontSeeText(__('menu.reports.title'));
        $response->assertDontSeeText(__('menu.settings.title'));
    }
}
