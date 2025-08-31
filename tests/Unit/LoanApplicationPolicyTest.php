<?php

namespace Tests\Unit;

use App\Models\Approval;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\User;
use App\Policies\LoanApplicationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanApplicationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicant;

    protected User $otherUser;

    protected User $supportingOfficer;

    protected User $bpmStaff;

    protected User $adminUser;

    protected LoanApplicationPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary roles
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('BPM Staff', 'web');
        Role::findOrCreate('Approver', 'web'); // <-- ADD THIS LINE
        Role::findOrCreate('HOD', 'web');      // <-- ADD THIS LINE
        $this->policy = new LoanApplicationPolicy;

        // Create Users
        $this->applicant = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->bpmStaff  = User::factory()->create()->assignRole('BPM Staff');
        $this->adminUser = User::factory()->create()->assignRole('Admin');

        // Setup supporting officer with the required grade level
        $grade41                 = Grade::factory()->create(['name' => '41', 'level' => 41]);
        $this->supportingOfficer = User::factory()->create(['grade_id' => $grade41->id]);
        config(['motac.approval.min_loan_support_grade_level' => 41]);
    }

    public function test_any_user_can_view_any_loan_application_list(): void
    {
        $this->assertTrue($this->policy->viewAny($this->applicant)->allowed());
    }

    public function test_applicant_can_view_own_loan_application(): void
    {
        $application = LoanApplication::factory()->make(['user_id' => $this->applicant->id]);
        $this->assertTrue($this->policy->view($this->applicant, $application)->allowed());
    }

    public function test_any_authenticated_user_can_create_loan_application(): void
    {
        $this->assertTrue($this->policy->create($this->applicant)->allowed());
    }

    public function test_applicant_can_update_own_draft_loan_application(): void
    {
        $application = LoanApplication::factory()->make([
            'user_id' => $this->applicant->id,
            'status'  => LoanApplication::STATUS_DRAFT,
        ]);
        $this->assertTrue($this->policy->update($this->applicant, $application)->allowed());
    }

    public function test_applicant_cannot_update_own_submitted_loan_application(): void
    {
        $application = LoanApplication::factory()->make([
            'user_id' => $this->applicant->id,
            'status'  => LoanApplication::STATUS_PENDING_SUPPORT,
        ]);
        $this->assertFalse($this->policy->update($this->applicant, $application)->allowed());
    }

    public function test_applicant_can_submit_their_own_draft_application(): void
    {
        $application = LoanApplication::factory()->make([
            'user_id' => $this->applicant->id,
            'status'  => LoanApplication::STATUS_DRAFT,
        ]);
        $this->assertTrue($this->policy->submit($this->applicant, $application)->allowed());
    }

    public function test_user_cannot_submit_another_users_application(): void
    {
        $application = LoanApplication::factory()->make(['user_id' => $this->otherUser->id]);
        $this->assertFalse($this->policy->submit($this->applicant, $application)->allowed());
    }

    public function test_supporting_officer_with_correct_grade_can_record_decision(): void
    {
        $application = LoanApplication::factory()->create([
            'status'                 => LoanApplication::STATUS_PENDING_SUPPORT,
            'current_approval_stage' => Approval::STAGE_LOAN_SUPPORT_REVIEW,
        ]);
        $application->approvals()->create([
            'officer_id' => $this->supportingOfficer->id,
            'stage'      => Approval::STAGE_LOAN_SUPPORT_REVIEW,
            'status'     => Approval::STATUS_PENDING,
        ]);

        $this->assertTrue($this->policy->recordDecision($this->supportingOfficer, $application)->allowed());
    }

    public function test_bpm_staff_can_process_issuance(): void
    {
        $application = LoanApplication::factory()->make(['status' => LoanApplication::STATUS_APPROVED]);
        $this->assertTrue($this->policy->processIssuance($this->bpmStaff, $application)->allowed());
    }

    public function test_applicant_cannot_process_issuance(): void
    {
        $application = LoanApplication::factory()->make(['status' => LoanApplication::STATUS_APPROVED]);
        $this->assertFalse($this->policy->processIssuance($this->applicant, $application)->allowed());
    }

    public function test_bpm_staff_can_process_return(): void
    {
        $application = LoanApplication::factory()->make(['status' => LoanApplication::STATUS_ISSUED]);
        $this->assertTrue($this->policy->processReturn($this->bpmStaff, $application)->allowed());
    }
}
