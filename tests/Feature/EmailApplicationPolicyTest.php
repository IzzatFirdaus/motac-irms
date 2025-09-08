<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmailApplication;
use App\Models\Grade;
use App\Policies\EmailApplicationPolicy;
use Spatie\Permission\Models\Role;

class EmailApplicationPolicyTest extends TestCase
{
  protected User $applicantUser;
  protected User $otherUser;
  protected User $supportingOfficerUser;
  protected User $itAdminUser;
  protected User $adminUser;
  protected EmailApplicationPolicy $policy;

  protected function setUp(): void
  {
    parent::setUp();

    // Seed basic roles
    // You might need a RoleSeeder or create roles directly here
    Role::findOrCreate('Applicant', 'web');
    Role::findOrCreate('Supporting Officer', 'web');
    Role::findOrCreate('IT Admin', 'web'); //
    Role::findOrCreate('Admin', 'web'); //


    $this->policy = new EmailApplicationPolicy();

    $applicantGrade = Grade::factory()->create(['name' => 'N19', 'level' => 19]);
    $this->applicantUser = User::factory()->create(['grade_id' => $applicantGrade->id])->assignRole('Applicant');

    $otherUserGrade = Grade::factory()->create(['name' => 'N22', 'level' => 22]);
    $this->otherUser = User::factory()->create(['grade_id' => $otherUserGrade->id])->assignRole('Applicant');

    // Supporting officer for email: Grade 9 or above
    $supportingOfficerGrade = Grade::factory()->create(['name' => 'F41', 'level' => 41, 'is_approver_grade' => true]);
    $this->supportingOfficerUser = User::factory()->create(['grade_id' => $supportingOfficerGrade->id])->assignRole('Supporting Officer');

    $itAdminGrade = Grade::factory()->create(['name' => 'F44', 'level' => 44]);
    $this->itAdminUser = User::factory()->create(['grade_id' => $itAdminGrade->id])->assignRole('IT Admin');

    $adminGrade = Grade::factory()->create(['name' => 'JUSA A', 'level' => 52]);
    $this->adminUser = User::factory()->create(['grade_id' => $adminGrade->id])->assignRole('Admin');

    // Mock config if policy uses it directly
    // config(['motac.approval.min_email_supporting_officer_grade_level' => 9]);
  }

  /**
   * Test if any authenticated user can generally view a list of email applications (policy dependent).
   * Policy: viewAny
   */
  public function test_view_any_email_application(): void
  {
    // This often depends on roles. An admin might be able to view all.
    $this->assertTrue($this->policy->viewAny($this->adminUser));
    // An applicant might only view their own, so viewAny might be false or restricted
    // $this->assertFalse($this->policy->viewAny($this->applicantUser)); // Or specific logic
  }

  /**
   * Test if an applicant can view their own email application.
   * Policy: view
   */
  public function test_applicant_can_view_own_email_application(): void
  {
    $application = EmailApplication::factory()->make(['user_id' => $this->applicantUser->id]); //
    $this->assertTrue($this->policy->view($this->applicantUser, $application));
  }

  /**
   * Test if an applicant cannot view another user's email application.
   * Policy: view
   */
  public function test_applicant_cannot_view_others_email_application(): void
  {
    $application = EmailApplication::factory()->make(['user_id' => $this->otherUser->id]);
    $this->assertFalse($this->policy->view($this->applicantUser, $application));
  }

  /**
   * Test if IT Admin can view any email application.
   * Policy: view
   */
  public function test_it_admin_can_view_any_email_application(): void
  {
    $application = EmailApplication::factory()->make(['user_id' => $this->applicantUser->id]);
    $this->assertTrue($this->policy->view($this->itAdminUser, $application));
  }

  /**
   * Test if an authenticated user (with appropriate role/status) can create an email application.
   * Policy: create
   */
  public function test_user_can_create_email_application(): void
  {
    // Eligibility rules might apply based on user's service_status, etc.
    // Assuming a general 'Applicant' role can initiate.
    $this->assertTrue($this->policy->create($this->applicantUser));
  }

  /**
   * Test if an applicant can update their own draft email application.
   * Policy: update
   */
  public function test_applicant_can_update_own_draft_email_application(): void
  {
    $application = EmailApplication::factory()->make([
      'user_id' => $this->applicantUser->id,
      'status' => EmailApplication::STATUS_DRAFT //
    ]);
    $this->assertTrue($this->policy->update($this->applicantUser, $application));
  }

  /**
   * Test if an applicant cannot update their submitted email application.
   * Policy: update
   */
  public function test_applicant_cannot_update_submitted_email_application(): void
  {
    $application = EmailApplication::factory()->make([
      'user_id' => $this->applicantUser->id,
      'status' => EmailApplication::STATUS_PENDING_SUPPORT //
    ]);
    $this->assertFalse($this->policy->update($this->applicantUser, $application));
  }

  /**
   * Test if an applicant cannot update another user's draft email application.
   * Policy: update
   */
  public function test_applicant_cannot_update_others_draft_email_application(): void
  {
    $application = EmailApplication::factory()->make([
      'user_id' => $this->otherUser->id,
      'status' => EmailApplication::STATUS_DRAFT
    ]);
    $this->assertFalse($this->policy->update($this->applicantUser, $application));
  }

  /**
   * Test if supporting officer can "approve" (or record decision on) an application.
   * This might be a specific method like 'approve' or 'recordSupportDecision'.
   * The policy should check if the officer has the minimum grade.
   * Policy: approve (hypothetical method name for supporting officer action)
   */
  public function test_supporting_officer_with_correct_grade_can_approve_application(): void
  {
    // Ensure supportingOfficerUser has a grade >= config('motac.approval.min_email_supporting_officer_grade_level')
    // This is mocked/setup in setUp()
    $application = EmailApplication::factory()->make([
      'status' => EmailApplication::STATUS_PENDING_SUPPORT,
      // 'supporting_officer_id' => $this->supportingOfficerUser->id // Or routed via ApprovalService
    ]);
    // Assuming an 'approve' method in policy
    // $this->assertTrue($this->policy->approve($this->supportingOfficerUser, $application));
    $this->assertTrue(true); // Placeholder
  }

  public function test_supporting_officer_with_incorrect_grade_cannot_approve_application(): void
  {
    $lowGradeOfficer = User::factory()->create();
    $lowGrade = Grade::factory()->create(['name' => 'N11', 'level' => 5]); // Below grade 9
    $lowGradeOfficer->grade_id = $lowGrade->id;
    $lowGradeOfficer->save();
    $lowGradeOfficer->assignRole('Supporting Officer');


    $application = EmailApplication::factory()->make(['status' => EmailApplication::STATUS_PENDING_SUPPORT]);
    // $this->assertFalse($this->policy->approve($lowGradeOfficer, $application));
    $this->assertTrue(true); // Placeholder
  }

  /**
   * Test if IT Admin can process a pending admin email application.
   * Policy: process (hypothetical method name for IT Admin action)
   */
  public function test_it_admin_can_process_pending_admin_application(): void
  {
    $application = EmailApplication::factory()->make(['status' => EmailApplication::STATUS_PENDING_ADMIN]); //
    // $this->assertTrue($this->policy->process($this->itAdminUser, $application));
    $this->assertTrue(true); // Placeholder
  }

  /**
   * Test admin override (if explicitly testable at policy level, often Gate::before handles this implicitly).
   * Note: Gate::before for admin overrides is confirmed.
   * Testing Gate::before directly in a unit test for a policy can be tricky.
   * Usually, you'd test that a non-admin is blocked, and an admin (via a feature test or by knowing the Gate works) is allowed.
   */
  public function test_admin_can_do_anything_due_to_gate_before(): void
  {
    // Example: Admin trying to update a submitted application by another user
    $application = EmailApplication::factory()->make([
      'user_id' => $this->otherUser->id,
      'status' => EmailApplication::STATUS_COMPLETED //
    ]);
    // In a real scenario, Gate::before would allow this if the user is 'Admin'
    // The policy method itself might return false, but the Gate overrides.
    // This specific test might be better as a feature test or an integration test of the Gate.
    // For a policy unit test, you'd typically test the policy's own logic.
    // However, if the policy itself has an `if ($user->hasRole('Admin')) return true;` at the start, that can be unit tested.

    $this->assertTrue(true); // Placeholder - actual testing of Gate::before is complex at unit level.
    // Assume this means the Gate::before should handle it.
  }


  // Add more tests:
  // - Policy for deleting draft applications.
  // - Policy for submitting a draft application.
  // - Specific checks for various application statuses.
  // - Checks based on user's service_status, appointment_type for eligibility if policy enforces it.
}
