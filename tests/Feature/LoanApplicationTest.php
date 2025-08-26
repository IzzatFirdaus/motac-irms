<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Feature tests for ICT Loan Application workflow.
 * This covers: submission, approval, equipment issuance, and equipment return.
 * Updated for v4.0: Email/User ID provisioning features removed, Helpdesk integration is out of scope for this file.
 */
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

        // Fix permission cache issues that cause 403 errors in Spatie Permission
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Roles for ICT Loan workflow
        Role::findOrCreate('Applicant', 'web');
        Role::findOrCreate('Supporting Officer', 'web');
        Role::findOrCreate('HOD', 'web');
        Role::findOrCreate('BPM Staff', 'web');
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Approver', 'web');

        // Permission for approval workflow
        Permission::findOrCreate('act_on_approval_tasks', 'web');

        // Create users and assign roles
        $this->applicantUser         = User::factory()->create()->assignRole('Applicant');
        $this->supportingOfficerUser = User::factory()->create()->assignRole(['Supporting Officer', 'Approver']);
        $this->hodUser               = User::factory()->create()->assignRole('HOD');
        $this->bpmStaffUser          = User::factory()->create()->assignRole('BPM Staff');
        $this->adminUser             = User::factory()->create()->assignRole('Admin');

        // Supporting Officer needs grade 41 for approval
        $grade41                               = Grade::factory()->create(['name' => '41', 'level' => 41, 'is_approver_grade' => true]);
        $this->supportingOfficerUser->grade_id = $grade41->id;
        $this->supportingOfficerUser->givePermissionTo('act_on_approval_tasks');
        $this->supportingOfficerUser->save();

        // Set correct config value for min loan support grade
        Config::set('motac.approval.min_loan_support_grade_level', 41);

        // Set up department for applicant (needed for routing approvals)
        $department                         = Department::factory()->create(['head_of_department_id' => $this->hodUser->id]);
        $this->applicantUser->department_id = $department->id;
        $this->applicantUser->save();
    }

    /**
     * Test that applicant can submit a new loan application.
     * This covers the basic submission workflow.
     */
    public function test_applicant_can_submit_new_loan_application(): void
    {
        $applicationData = [
            'purpose'                => 'Required for official business trip.',
            'location'               => 'Test Location',
            'loan_start_date'        => now()->format('Y-m-d H:i:s'),
            'loan_end_date'          => now()->addDays(1)->format('Y-m-d H:i:s'),
            'supporting_officer_id'  => $this->supportingOfficerUser->id,
            'applicant_confirmation' => true,
            'items'                  => [['equipment_type' => 'laptop', 'quantity_requested' => 1]],
        ];

        $response = $this->actingAs($this->applicantUser)->post(route('loan-applications.store'), $applicationData);
        $response->assertRedirect()->assertSessionHas('success');
    }

    /**
     * Test that an approver (Supporting Officer) can approve the application.
     * Covers the approval decision logic and status update.
     */
    public function test_approving_a_loan_application_succeeds(): void
    {
        $loanApplication = LoanApplication::factory()->create(['status' => LoanApplication::STATUS_PENDING_SUPPORT]);
        $item            = $loanApplication->loanApplicationItems()->create(['equipment_type' => 'laptop', 'quantity_requested' => 1]);
        $approval        = $loanApplication->approvals()->create([
            'officer_id' => $this->supportingOfficerUser->id,
            'stage'      => 'loan_support_review',
        ]);

        // Only designated approver should be able to act
        $response = $this->actingAs($this->supportingOfficerUser)
            ->post(route('approvals.recordDecision', $approval->id), [
                'decision'       => 'approved',
                'comments'       => 'Approved by Admin for testing.',
                'items_approved' => [
                    $item->id => ['quantity_approved' => '1'],
                ],
            ]);

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('loan_applications', [
            'id'     => $loanApplication->id,
            'status' => LoanApplication::STATUS_APPROVED,
        ]);
    }

    /**
     * Test that BPM Staff can issue equipment for an approved loan application.
     * Covers the equipment issuance process.
     */
    public function test_bpm_staff_can_issue_equipment(): void
    {
        $equipment       = Equipment::factory()->create(['status' => Equipment::STATUS_AVAILABLE]);
        $loanApplication = LoanApplication::factory()->create(['status' => LoanApplication::STATUS_APPROVED]);
        $item            = $loanApplication->loanApplicationItems()->create([
            'equipment_type'     => 'laptop',
            'quantity_requested' => 1,
            'quantity_approved'  => 1,
        ]);

        $issueData = [
            'receiving_officer_id' => $this->applicantUser->id,
            'transaction_date'     => now()->format('Y-m-d H:i:s'),
            'items'                => [[
                'loan_application_item_id' => $item->id,
                'equipment_id'             => $equipment->id,
                'quantity_issued'          => 1,
            ]],
        ];

        $response = $this->actingAs($this->bpmStaffUser)->post(
            route('loan-applications.issue.store', $loanApplication->id),
            $issueData
        );
        $response->assertRedirect()->assertSessionHas('success');
    }

    /**
     * Test that BPM Staff can process equipment return for an issued application.
     * Covers the return workflow and status update.
     */
    public function test_bpm_staff_can_process_equipment_return(): void
    {
        $equipment       = Equipment::factory()->create(['status' => Equipment::STATUS_ON_LOAN]);
        $loanApplication = LoanApplication::factory()->create(['status' => LoanApplication::STATUS_ISSUED]);
        $applicationItem = $loanApplication->loanApplicationItems()->create([
            'equipment_type'     => $equipment->asset_type,
            'quantity_requested' => 1,
            'quantity_approved'  => 1,
            'quantity_issued'    => 1,
        ]);
        $issueTransaction = LoanTransaction::factory()->create([
            'loan_application_id' => $loanApplication->id,
            'type'                => 'issue',
            'status'              => LoanTransaction::STATUS_ISSUED,
        ]);
        $issuedItem = $issueTransaction->loanTransactionItems()->create([
            'equipment_id'             => $equipment->id,
            'quantity_transacted'      => 1,
            'loan_application_item_id' => $applicationItem->id,
        ]);

        $returnData = [
            'returning_officer_id' => $this->applicantUser->id,
            'transaction_date'     => now()->format('Y-m-d H:i:s'),
            'items'                => [[
                'loan_transaction_item_id' => $issuedItem->id,
                'equipment_id'             => $equipment->id,
                'quantity_returned'        => 1,
                'condition_on_return'      => 'good',
                'item_status_on_return'    => 'returned_good',
            ]],
        ];

        $response = $this->actingAs($this->bpmStaffUser)->post(
            route('loan-transactions.return.store', $issueTransaction->id),
            $returnData
        );

        $response->assertRedirect()->assertSessionHas('success');
        $loanApplication->refresh();
        $this->assertEquals(LoanApplication::STATUS_RETURNED, $loanApplication->status);
    }
}
