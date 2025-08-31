<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $bpmStaffUser;

    protected User $itAdminUser;

    protected User $approverUser;

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
            Role::findOrCreate('Approver', $guard);
            Role::findOrCreate('User', $guard);
        }

        // Create users and assign roles for both guards
        $this->adminUser    = User::factory()->create();
        $this->bpmStaffUser = User::factory()->create();
        $this->itAdminUser  = User::factory()->create();
        $this->regularUser  = User::factory()->create();
        $this->approverUser = User::factory()->create();

        foreach ($guards as $guard) {
            $this->adminUser->assignRole(Role::findByName('Admin', $guard));
            $this->bpmStaffUser->assignRole(Role::findByName('BPM', $guard));
            $this->itAdminUser->assignRole(Role::findByName('IT Admin', $guard));
            $this->regularUser->assignRole(Role::findByName('User', $guard));
            $this->approverUser->assignRole(Role::findByName('Approver', $guard));
        }

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_sees_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser, 'web')->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.admin');
        $response->assertSeeText('Admin Dashboard');
    }

    public function test_bpm_staff_sees_bpm_dashboard(): void
    {
        $response = $this->actingAs($this->bpmStaffUser, 'web')->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.bpm-staff');
        $response->assertSeeText('BPM Staff Dashboard');
    }

    public function test_it_admin_sees_it_admin_dashboard(): void
    {
        $response = $this->actingAs($this->itAdminUser, 'web')->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.it-admin');
        $response->assertSeeText('IT Admin Dashboard');
    }

    public function test_approver_sees_approver_dashboard(): void
    {
        // Create a pending loan application for the approver to see
        $loanApplication = LoanApplication::factory()->create(['status' => 'pending_support']);
        Approval::factory()->create([
            'approvable_id'   => $loanApplication->id,
            'approvable_type' => LoanApplication::class,
            'officer_id'      => $this->approverUser->id,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->approverUser, 'web')->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.approver');
        $response->assertSeeText('Your Approval Tasks');
    }

    public function test_regular_user_sees_user_dashboard(): void
    {
        $response = $this->actingAs($this->regularUser, 'web')->get('/dashboard');
        $response->assertOk();
        $response->assertViewIs('dashboard.user');
        $response->assertSeeText(sprintf('Welcome, %s!', $this->regularUser->name));
    }

    public function test_regular_user_cannot_access_admin_equipment_page(): void
    {
        $response = $this->actingAs($this->regularUser, 'web')->get(route('admin.equipment.index'));
        $response->assertStatus(403);
    }

    public function test_bpm_staff_can_access_admin_equipment_page(): void
    {
        $response = $this->actingAs($this->bpmStaffUser, 'web')->get(route('admin.equipment.index'));
        $response->assertOk();
    }

    public function test_regular_user_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->regularUser, 'web')->get(route('settings.index'));
        $response->assertStatus(403);
    }
}
