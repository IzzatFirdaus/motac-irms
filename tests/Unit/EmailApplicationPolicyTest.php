<?php

namespace Tests\Unit;

use App\Models\EmailApplication;
use App\Models\User;
use App\Policies\EmailApplicationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailApplicationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicantUser, $otherUser, $itAdminUser, $adminUser;
    protected EmailApplicationPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Applicant', 'web');
        Role::findOrCreate('IT Admin', 'web');
        Role::findOrCreate('Admin', 'web');

        $this->policy = new EmailApplicationPolicy();

        $this->applicantUser = User::factory()->create()->assignRole('Applicant');
        $this->otherUser = User::factory()->create()->assignRole('Applicant');
        $this->itAdminUser = User::factory()->create()->assignRole('IT Admin');
        $this->adminUser = User::factory()->create()->assignRole('Admin');
    }

    public function test_admin_can_view_any_application(): void
    {
        $application = EmailApplication::factory()->make(['user_id' => $this->otherUser->id]);
        // EDIT: Changed assertion to use ->allowed() on the Response object.
        $this->assertTrue($this->policy->view($this->adminUser, $application)->allowed());
    }

    public function test_it_admin_can_view_any_email_application(): void
    {
        $application = EmailApplication::factory()->make(['user_id' => $this->applicantUser->id]);
        // EDIT: Changed assertion to use ->allowed() on the Response object.
        $this->assertTrue($this->policy->view($this->itAdminUser, $application)->allowed());
    }

    public function test_user_can_create_email_application(): void
    {
        // EDIT: Changed assertion to use ->allowed() on the Response object.
        $this->assertTrue($this->policy->create($this->applicantUser)->allowed());
    }
}
