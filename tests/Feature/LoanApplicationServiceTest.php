<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use App\Notifications\ApplicationNeedsAction;
use App\Notifications\ApplicationSubmitted;
use App\Services\LoanApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoanApplicationService $loanApplicationService;

    protected User $applicant;

    protected User $hod;

    protected User $supportingOfficer;

    protected User $admin;

    protected Department $department;

    protected Grade $applicantGrade;

    protected Grade $supportingOfficerGrade;

    protected function setUp(): void
    {
        parent::setUp();

        // Add all necessary roles to this test's setup
        Role::findOrCreate('Applicant', 'web');
        Role::findOrCreate('Supporting Officer', 'web');
        Role::findOrCreate('HOD', 'web');
        Role::findOrCreate('BPM Staff', 'web');
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Approver', 'web'); // Add this role

        // Using the real service container to resolve the service, making this an integration test.
        $this->loanApplicationService = $this->app->make(LoanApplicationService::class);

        // Mock Notifications to prevent actual sending
        Notification::fake();

        // Setup Users and Roles
        $this->admin = User::factory()->create(); // Assumes a BlameableObserver is setting created_by
        $this->actingAs($this->admin); // Set a default user for blameable fields

        $this->hod = User::factory()->create();
        $this->department = Department::factory()->create(['head_of_department_id' => $this->hod->id]);
        $this->applicantGrade = Grade::factory()->create(['name' => 'N19', 'level' => 19]);
        $this->applicant = User::factory()->create([
            'department_id' => $this->department->id,
            'grade_id' => $this->applicantGrade->id,
        ]);

        $this->supportingOfficerGrade = Grade::factory()->create(['name' => '41', 'level' => 41, 'is_approver_grade' => true]);
        $this->supportingOfficer = User::factory()->create(['grade_id' => $this->supportingOfficerGrade->id]);

        // Set mock config for minimum grade level for supporting officer in loans
        Config::set('motac.approval.min_loan_support_grade_level', 41);
    }

    /**
     * Test creating a new loan application and saving it as a draft.
     */
    public function test_create_application_as_draft(): void
    {
        $this->actingAs($this->applicant); // The applicant is performing the action

        $equipment = Equipment::factory()->create(['status' => 'available']);
        $applicationData = [
            'purpose' => 'Unit Test Purpose - Draft',
            'location' => 'Unit Test Location',
            'loan_start_date' => now()->addDays(2)->toDateTimeString(),
            'loan_end_date' => now()->addDays(5)->toDateTimeString(),
            'items' => [
                ['equipment_type' => $equipment->asset_type, 'quantity_requested' => 1, 'notes' => 'Draft item note'],
            ],
            'applicant_confirmation' => false, // For draft, this is false
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
            $applicationData,
            $this->applicant,
            true // Save as draft only
        );

        $this->assertInstanceOf(LoanApplication::class, $loanApplication);
        $this->assertEquals($this->applicant->id, $loanApplication->user_id);
        $this->assertEquals($this->applicant->id, $loanApplication->created_by); // Blameable check
        $this->assertEquals('Unit Test Purpose - Draft', $loanApplication->purpose);
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $loanApplication->status);
        $this->assertNull($loanApplication->applicant_confirmation_timestamp);
        $this->assertCount(1, $loanApplication->loanApplicationItems);
        $this->assertEquals(1, $loanApplication->loanApplicationItems->first()->quantity_requested);

        // Ensure no notifications are sent for drafts
        Notification::assertNothingSent();
    }

    /**
     * Test submitting a draft loan application for approval.
     */
    public function test_submit_draft_application_for_approval(): void
    {
        $this->actingAs($this->applicant);

        $draftApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
            'applicant_confirmation_timestamp' => now(), // Crucial for submission
            'supporting_officer_id' => $this->supportingOfficer->id,
        ]);

        $submittedApplication = $this->loanApplicationService->submitApplicationForApproval(
            $draftApplication,
            $this->applicant // The user submitting
        );

        $this->assertEquals(LoanApplication::STATUS_PENDING_SUPPORT, $submittedApplication->status);
        $this->assertNotNull($submittedApplication->submitted_at);
        $this->assertEquals($this->applicant->id, $submittedApplication->updated_by); // Blameable check

        // Check if an approval task was created
        $this->assertDatabaseHas('approvals', [
            'approvable_id' => $submittedApplication->id,
            'approvable_type' => LoanApplication::class,
            'officer_id' => $this->supportingOfficer->id,
            'stage' => Approval::STAGE_LOAN_SUPPORT_REVIEW,
            'status' => Approval::STATUS_PENDING,
        ]);

        // Assert correct notifications were sent
        Notification::assertSentTo($this->applicant, ApplicationSubmitted::class);
        Notification::assertSentTo($this->supportingOfficer, ApplicationNeedsAction::class);
    }

    public function test_submit_application_for_approval_fails_if_no_confirmation_timestamp(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('Perakuan pemohon mesti diterima sebelum penghantaran. Sila kemaskini draf dan sahkan perakuan.'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
            'applicant_confirmation_timestamp' => null, // Missing timestamp
            'supporting_officer_id' => $this->supportingOfficer->id,
        ]);

        $this->loanApplicationService->submitApplicationForApproval($application, $this->applicant);
    }

    /**
     * Test updating an existing draft loan application.
     */
    public function test_update_loan_application_as_draft(): void
    {
        $this->actingAs($this->applicant);

        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
            'purpose' => 'Initial Purpose',
        ]);
        $updateData = [
            'purpose' => 'Updated Purpose for Draft',
            'location' => 'Updated Location',
            'items' => [], // Assuming items are handled separately or synced
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $updatedApplication = $this->loanApplicationService->updateApplication(
            $loanApplication,
            $updateData,
            $this->applicant
        );

        $this->assertInstanceOf(LoanApplication::class, $updatedApplication);
        $this->assertEquals('Updated Purpose for Draft', $updatedApplication->purpose);
        $this->assertEquals($this->applicant->id, $updatedApplication->updated_by); // Blameable check
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $updatedApplication->status);
    }

    public function test_update_rejected_application(): void
    {
        $this->actingAs($this->applicant);

        $rejectedApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_REJECTED,
            'purpose' => 'Purpose that was rejected',
        ]);
        $updateData = [
            'purpose' => 'Corrected purpose for resubmission',
            'location' => 'New location',
            'items' => [],
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $updatedApplication = $this->loanApplicationService->updateApplication(
            $rejectedApplication,
            $updateData,
            $this->applicant
        );

        // When a rejected application is updated, it should go back to draft status
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $updatedApplication->status);
        $this->assertEquals('Corrected purpose for resubmission', $updatedApplication->purpose);
        $this->assertNull($updatedApplication->rejection_reason); // Check if rejection reason is cleared on update
    }

    public function test_update_application_fails_if_not_draft_or_rejected(): void
    {
        $this->expectExceptionMessage(__('Hanya draf permohonan atau permohonan yang ditolak boleh dikemaskini. Status semasa: Menunggu Sokongan Pegawai'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_PENDING_SUPPORT,
        ]);
        $updateData = ['purpose' => 'Trying to update submitted app'];

        $this->loanApplicationService->updateApplication($application, $updateData, $this->applicant);
    }

    /**
     * Test deleting a draft loan application.
     */
    public function test_delete_draft_loan_application(): void
    {
        $this->actingAs($this->applicant);

        $draftApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
        ]);
        LoanApplicationItem::factory()->create(['loan_application_id' => $draftApplication->id]);

        $result = $this->loanApplicationService->deleteApplication($draftApplication, $this->applicant);

        $this->assertTrue($result);
        $this->assertSoftDeleted($draftApplication);
        $this->assertEquals($this->applicant->id, $draftApplication->fresh()->deleted_by); // Blameable check
        $this->assertEquals(0, LoanApplicationItem::where('loan_application_id', $draftApplication->id)->count());
    }

    public function test_delete_non_draft_application_fails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('Hanya draf permohonan yang boleh dibuang.'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_PENDING_SUPPORT,
        ]);

        $this->loanApplicationService->deleteApplication($application, $this->applicant);
    }
}
