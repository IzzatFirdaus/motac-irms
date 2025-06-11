<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicantUser, $supportingOfficerUser, $hodUser, $bpmStaffUser, $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // This line is CRUCIAL and must be present to fix 403 Forbidden errors.
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('Applicant', 'web');
        Role::findOrCreate('Supporting Officer', 'web');
        Role::findOrCreate('HOD', 'web');
        Role::findOrCreate('BPM Staff', 'web');
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Approver', 'web');

        $this->applicantUser = User::factory()->create()->assignRole('Applicant');
        $this->supportingOfficerUser = User::factory()->create()->assignRole('Supporting Officer');
        $this->hodUser = User::factory()->create()->assignRole('HOD');
        $this->bpmStaffUser = User::factory()->create()->assignRole('BPM Staff');
        $this->adminUser = User::factory()->create()->assignRole('Admin');

        $grade41 = Grade::factory()->create(['name' => '41', 'level' => 41, 'is_approver_grade' => true]);
        $this->supportingOfficerUser->grade_id = $grade41->id;
        $this->supportingOfficerUser->save();
        Config::set('motac.approval.min_loan_support_grade_level', 41);

        $department = Department::factory()->create(['head_of_department_id' => $this->hodUser->id]);
        $this->applicantUser->department_id = $department->id;
        $this->applicantUser->save();
    }

    public function test_applicant_can_submit_new_loan_application(): void
    {
        $applicationData = [
            'purpose' => 'Required for official business trip.',
            'location' => 'Test Location',
            'loan_start_date' => now()->format('Y-m-d H:i:s'),
            'loan_end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'supporting_officer_id' => $this->supportingOfficerUser->id,
            'applicant_confirmation' => true,
            'items' => [['equipment_type' => 'laptop', 'quantity_requested' => 1]],
        ];

        $response = $this->actingAs($this->applicantUser)->post(route('loan-applications.store'), $applicationData);
        $response->assertRedirect()->assertSessionHas('success');
    }

    public function test_approving_a_loan_application_succeeds(): void
    {
        $loanApplication = LoanApplication::factory()->create(['status' => LoanApplication::STATUS_PENDING_SUPPORT]);
        $item = $loanApplication->loanApplicationItems()->create(['equipment_type' => 'laptop', 'quantity_requested' => 1]);
        $approval = $loanApplication->approvals()->create(['officer_id' => $this->supportingOfficerUser->id, 'stage' => 'loan_support_review']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('approvals.recordDecision', $approval->id), [
                'decision' => 'approved',
                'comments' => 'Approved by Admin for testing.',
                'items_approved' => [
                    $item->id => ['quantity_approved' => '1']
                ]
            ]);

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('loan_applications', ['id' => $loanApplication->id, 'status' => LoanApplication::STATUS_APPROVED]);
    }

    public function test_bpm_staff_can_issue_equipment(): void
    {
        $equipment = Equipment::factory()->create(['status' => Equipment::STATUS_AVAILABLE]);
        $loanApplication = LoanApplication::factory()->create(['status' => LoanApplication::STATUS_APPROVED]);
        $item = $loanApplication->loanApplicationItems()->create(['equipment_type' => 'laptop', 'quantity_requested' => 1, 'quantity_approved' => 1]);

        $issueData = [
            'receiving_officer_id' => $this->applicantUser->id,
            'transaction_date' => now()->format('Y-m-d H:i:s'),
            'items' => [[
                'loan_application_item_id' => $item->id,
                'equipment_id' => $equipment->id,
                'quantity_issued' => 1
            ]],
        ];

        $response = $this->actingAs($this->bpmStaffUser)->post(route('loan-applications.issue.store', $loanApplication->id), $issueData);

        $response->assertRedirect()->assertSessionHas('success');
    }

    public function test_bpm_staff_can_process_equipment_return(): void
    {
        $equipment = Equipment::factory()->create(['status' => Equipment::STATUS_ON_LOAN]);

        $loanApplication = LoanApplication::factory()->create([
            'status' => LoanApplication::STATUS_ISSUED,
            'loan_end_date' => now()->addDays(5),
        ]);

        $issueTransaction = LoanTransaction::factory()->create(['loan_application_id' => $loanApplication->id, 'type' => 'issue']);
        $issuedItem = $issueTransaction->loanTransactionItems()->create(['equipment_id' => $equipment->id, 'quantity_transacted' => 1]);

        $returnData = [
            'returning_officer_id' => $this->applicantUser->id,
            'transaction_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                $issuedItem->id => [
                    'loan_application_item_id' => $issuedItem->id,
                    'quantity_returned' => 1,
                    'condition_on_return' => 'good'
                ]
            ],
        ];

        $response = $this->actingAs($this->bpmStaffUser)->post(route('loan-transactions.return.store', $issueTransaction->id), $returnData);

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('loan_applications', ['id' => $loanApplication->id, 'status' => LoanApplication::STATUS_RETURNED]);
    }
}
