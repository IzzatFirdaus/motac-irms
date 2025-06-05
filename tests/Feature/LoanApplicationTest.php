<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoanApplication;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\Department;
use App\Models\Position;
use Spatie\Permission\Models\Role;

class LoanApplicationTest extends TestCase
{
  use RefreshDatabase;

  protected User $applicantUser;
  protected User $supportingOfficerUser;
  protected User $hodUser;
  protected User $bpmStaffUser;
  protected User $adminUser;

  protected function setUp(): void
  {
    parent::setUp();

    // Seed basic roles
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']); // Assuming you have a RoleSeeder

    $this->applicantUser = User::factory()->create()->assignRole('Applicant'); // Adjust role name
    $this->supportingOfficerUser = User::factory()->create()->assignRole('Supporting Officer'); // Adjust role name
    $this->hodUser = User::factory()->create()->assignRole('HOD'); // Adjust role name
    $this->bpmStaffUser = User::factory()->create()->assignRole('BPM Staff'); // Adjust role name
    $this->adminUser = User::factory()->create()->assignRole('Admin'); // Adjust role name

    // Ensure supporting officers and HODs have appropriate grades for approval logic
    // Example: Grade 41 for supporting officer for loans
    $grade41 = Grade::factory()->create(['name' => '41', 'level' => 41, 'is_approver_grade' => true]); //
    $this->supportingOfficerUser->grade_id = $grade41->id;
    $this->supportingOfficerUser->save();

    $hodGrade = Grade::factory()->create(['name' => 'JUSA C', 'level' => 50, 'is_approver_grade' => true]); // Example HOD grade
    $this->hodUser->grade_id = $hodGrade->id;
    $this->hodUser->save();

    // Setup applicant's department with HOD for routing tests
    $department = Department::factory()->create(['head_user_id' => $this->hodUser->id]); //
    $this->applicantUser->department_id = $department->id;
    $this->applicantUser->save();

    // Mock config values if needed
    // Config::set('motac.approval.min_loan_support_grade_level', 41);
  }

  /**
   * Test if an authenticated applicant can view the loan application form.
   */
  public function test_applicant_can_view_loan_application_form(): void
  {
    $response = $this->actingAs($this->applicantUser)
      ->get(route('loan-applications.create')); // Adjust route name

    $response->assertStatus(200);
    $response->assertViewIs('loan_applications.create'); // Adjust view name
  }

  /**
   * Test if an applicant can successfully submit a new loan application.
   * This covers part of the workflow: 5.2.1 Loan Application Initiation
   * and 5.2.2 Confirmation & Certification
   */
  public function test_applicant_can_submit_new_loan_application(): void
  {
    $equipment = Equipment::factory()->create(['status' => 'available']); //
    $applicationData = [
      'purpose' => 'Official duty travel to Sabah.', //
      'location' => 'Kota Kinabalu Office', //
      'loan_start_date' => now()->addDays(5)->format('Y-m-d H:i:s'), //
      'loan_end_date' => now()->addDays(10)->format('Y-m-d H:i:s'), //
      'applicant_confirmation_timestamp' => now()->format('Y-m-d H:i:s'), //
      'items' => [
        [
          'equipment_type' => $equipment->asset_type, //
          'quantity_requested' => 1, //
          'notes' => 'Need a reliable laptop.' //
        ]
      ],
      // Add other required fields from LoanApplication and LoanApplicationItem
    ];

    $response = $this->actingAs($this->applicantUser)
      ->post(route('loan-applications.store'), $applicationData); // Adjust route name

    $response->assertRedirect(); // Or assertCreated, depending on your controller
    $response->assertSessionHas('success'); // Or similar feedback

    $this->assertDatabaseHas('loan_applications', [
      'user_id' => $this->applicantUser->id,
      'purpose' => 'Official duty travel to Sabah.',
      'status' => LoanApplication::STATUS_PENDING_SUPPORT // (or relevant initial status)
    ]);
    $this->assertDatabaseHas('loan_application_items', [
      'quantity_requested' => 1
    ]);
  }

  /**
   * Test validation when submitting an incomplete loan application.
   */
  public function test_loan_application_submission_fails_with_invalid_data(): void
  {
    $response = $this->actingAs($this->applicantUser)
      ->post(route('loan-applications.store'), ['purpose' => '']); // Adjust route name

    $response->assertStatus(302); // Usually redirects back on validation error
    $response->assertSessionHasErrors(['purpose']); //
  }

  /**
   * Test if a supporting officer can approve a pending loan application.
   * This covers part of the workflow: 5.2.3 Supporting Officer Approval
   */
  public function test_supporting_officer_can_approve_loan_application(): void
  {
    $loanApplication = LoanApplication::factory()->create([
      'user_id' => $this->applicantUser->id,
      'status' => LoanApplication::STATUS_PENDING_SUPPORT, //
      // 'current_approval_officer_id' => $this->supportingOfficerUser->id, // Assuming routing assigns it
    ]);
    // Ensure the application is routed to this supporting officer via ApprovalService logic
    // This might involve setting up the 'approvals' table polymorphic relation

    $response = $this->actingAs($this->supportingOfficerUser)
      ->post(route('approvals.recordDecision', $loanApplication->id), [ // Adjust route & params
        'approvable_type' => get_class($loanApplication),
        'approvable_id' => $loanApplication->id,
        'status' => 'approved', //
        'comments' => 'Approved by supporting officer.'
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('loan_applications', [
      'id' => $loanApplication->id,
      // Status should now be pending_hod_review or pending_bpm_review or approved
      // 'status' => LoanApplication::STATUS_PENDING_HOD_REVIEW, // Or next appropriate status
    ]);
    $this->assertDatabaseHas('approvals', [
      'approvable_id' => $loanApplication->id,
      'officer_id' => $this->supportingOfficerUser->id,
      'status' => 'approved'
    ]);
  }

  /**
   * Test if HOD can approve a loan application pending HOD review.
   */
  public function test_hod_can_approve_loan_application(): void
  {
    $loanApplication = LoanApplication::factory()->create([
      'user_id' => $this->applicantUser->id,
      'department_id' => $this->applicantUser->department_id,
      'status' => LoanApplication::STATUS_PENDING_HOD_REVIEW, //
      // 'current_approval_officer_id' => $this->hodUser->id, // Assuming routing assigns it
    ]);

    $response = $this->actingAs($this->hodUser)
      ->post(route('approvals.recordDecision', $loanApplication->id), [ // Adjust route & params
        'approvable_type' => get_class($loanApplication),
        'approvable_id' => $loanApplication->id,
        'status' => 'approved',
        'comments' => 'Approved by HOD.'
      ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('loan_applications', [
      'id' => $loanApplication->id,
      'status' => LoanApplication::STATUS_PENDING_BPM_REVIEW, // Or next appropriate status
    ]);
  }


  /**
   * Test if BPM staff can issue equipment for an approved loan application.
   * This covers part of the workflow: 5.2.5 Equipment Issuance
   */
  public function test_bpm_staff_can_issue_equipment(): void
  {
    $equipment = Equipment::factory()->create(['status' => Equipment::STATUS_AVAILABLE]); //
    $loanApplication = LoanApplication::factory()->create([
      'user_id' => $this->applicantUser->id,
      'status' => LoanApplication::STATUS_APPROVED // Or 'pending_bpm_review' if BPM finalizes approval
    ]);
    $loanApplicationItem = $loanApplication->items()->create([
      'equipment_type' => $equipment->asset_type,
      'quantity_requested' => 1,
      'quantity_approved' => 1, //
    ]);

    $issueData = [
      'loan_application_id' => $loanApplication->id,
      'issuing_officer_id' => $this->bpmStaffUser->id, //
      'receiving_officer_id' => $this->applicantUser->id, //
      'accessories_checklist_on_issue' => json_encode(['Power Cable', 'Bag']), //
      'issue_notes' => 'Equipment issued in good condition.', //
      'items_to_issue' => [
        [
          'loan_application_item_id' => $loanApplicationItem->id,
          'equipment_id' => $equipment->id, //
          'quantity_transacted' => 1 //
        ]
      ]
    ];

    // This might be a Livewire component action or a controller action
    // For example: $this->actingAs($this->bpmStaffUser)->livewire(ProcessIssuance::class) ...
    // Or:
    $response = $this->actingAs($this->bpmStaffUser)
      ->post(route('loan-transactions.issue.store'), $issueData); // Adjust route name

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('loan_applications', [
      'id' => $loanApplication->id,
      'status' => LoanApplication::STATUS_ISSUED //
    ]);
    $this->assertDatabaseHas('equipment', [
      'id' => $equipment->id,
      'status' => Equipment::STATUS_ON_LOAN //
    ]);
    $this->assertDatabaseHas('loan_transactions', [
      'loan_application_id' => $loanApplication->id,
      'type' => 'issue', //
      'issuing_officer_id' => $this->bpmStaffUser->id,
    ]);
    $this->assertDatabaseHas('loan_transaction_items', [
      'equipment_id' => $equipment->id,
      'quantity_transacted' => 1
    ]);
  }

  /**
   * Test if an applicant cannot approve their own loan application.
   */
  public function test_applicant_cannot_approve_own_loan_application(): void
  {
    $loanApplication = LoanApplication::factory()->create([
      'user_id' => $this->applicantUser->id,
      'status' => LoanApplication::STATUS_PENDING_SUPPORT,
    ]);

    $response = $this->actingAs($this->applicantUser)
      ->post(route('approvals.recordDecision', $loanApplication->id), [ // Adjust route
        'approvable_type' => get_class($loanApplication),
        'approvable_id' => $loanApplication->id,
        'status' => 'approved',
        'comments' => 'Self-approved.'
      ]);

    $response->assertStatus(403); // Forbidden, due to policy
    $this->assertDatabaseHas('loan_applications', [
      'id' => $loanApplication->id,
      'status' => LoanApplication::STATUS_PENDING_SUPPORT, // Status should not change
    ]);
  }

  // Add more tests:
  // - Rejection by supporting officer/HOD
  // - BPM staff returning equipment
  // - Overdue loan reminders
  // - Access control for different roles viewing/editing applications
  // - Tests for specific validation rules (date logic, quantity checks etc.)
  // - Test for `responsible_officer_id`
  // - Test for grade level restrictions for approvers using custom middleware
}
