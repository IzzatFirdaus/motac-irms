<?php

// Assuming this file is located at:
// tests/Feature/LoanApplicationServiceTest.php
// or tests/Unit/LoanApplicationServiceTest.php

namespace Tests\Feature; // Or Tests\Unit if you move it

use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\LoanApplicationService;
use App\Services\LoanTransactionService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class LoanApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoanApplicationService $loanApplicationService;

    protected User $applicant;

    protected User $hod;

    protected User $supportingOfficer;

    protected Department $department;

    protected Grade $applicantGrade;

    protected Grade $supportingOfficerGrade;

    protected function setUp(): void
    {
        parent::setUp();

        // It's good practice to mock external services for true unit tests.
        // For this example, we'll resolve the actual service, making it more of an integration test.
        // If you want stricter unit tests, mock ApprovalService, LoanTransactionService, NotificationService.
        // $approvalServiceMock = $this->mock(ApprovalService::class);
        // $loanTransactionServiceMock = $this->mock(LoanTransactionService::class);
        // $notificationServiceMock = $this->mock(NotificationService::class);
        // $this->loanApplicationService = new LoanApplicationService($approvalServiceMock, $loanTransactionServiceMock, $notificationServiceMock);

        $this->loanApplicationService = $this->app->make(LoanApplicationService::class);

        $this->hod = User::factory()->create();
        $this->department = Department::factory()->create(['head_user_id' => $this->hod->id]);
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
        $equipment = Equipment::factory()->create(['status' => 'available']);
        $applicationData = [
            'purpose' => 'Unit Test Purpose - Draft',
            'location' => 'Unit Test Location',
            'return_location' => 'Unit Test Return Location',
            'loan_start_date' => now()->addDays(2)->toDateTimeString(),
            'loan_end_date' => now()->addDays(5)->toDateTimeString(),
            'responsible_officer_id' => $this->applicant->id,
            'items' => [
                ['equipment_type' => $equipment->asset_type, 'quantity_requested' => 1, 'notes' => 'Draft item note'],
            ],
            'applicant_confirmation' => false, // For draft, this can be false or not present
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
            $applicationData,
            $this->applicant,
            true // Save as draft only
        );

        $this->assertInstanceOf(LoanApplication::class, $loanApplication);
        $this->assertEquals($this->applicant->id, $loanApplication->user_id);
        $this->assertEquals('Unit Test Purpose - Draft', $loanApplication->purpose);
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $loanApplication->status);
        $this->assertNull($loanApplication->applicant_confirmation_timestamp);
        $this->assertCount(1, $loanApplication->loanApplicationItems); // Corrected relationship name
        $this->assertEquals(1, $loanApplication->loanApplicationItems->first()->quantity_requested);
    }

    /**
     * Test creating a loan application intended for immediate submission (though service saves as draft first).
     */
    public function test_create_application_for_submission(): void
    {
        $equipment = Equipment::factory()->create(['status' => 'available']);
        $applicationData = [
            'purpose' => 'Unit Test Purpose - For Submission',
            'location' => 'Unit Test Location',
            'return_location' => 'Unit Test Return Location',
            'loan_start_date' => now()->addDays(3)->toDateTimeString(),
            'loan_end_date' => now()->addDays(6)->toDateTimeString(),
            'responsible_officer_id' => $this->applicant->id,
            'items' => [
                ['equipment_type' => $equipment->asset_type, 'quantity_requested' => 2, 'notes' => 'Item for submission'],
            ],
            'applicant_confirmation' => true, // Crucial for submission intent
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $loanApplication = $this->loanApplicationService->createAndSubmitApplication(
            $applicationData,
            $this->applicant,
            false // Not saving as draft only, implies applicant_confirmation is set
        );

        $this->assertInstanceOf(LoanApplication::class, $loanApplication);
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $loanApplication->status); // Service creates as draft first
        $this->assertNotNull($loanApplication->applicant_confirmation_timestamp); // Timestamp set due to applicant_confirmation = true
        $this->assertCount(1, $loanApplication->loanApplicationItems);
    }

    public function test_create_application_fails_if_no_items(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('Permohonan mesti mempunyai sekurang-kurangnya satu item peralatan.'));

        $applicationData = [
            'purpose' => 'Test Purpose - No Items',
            'location' => 'Test Location',
            'return_location' => 'Test Return Location',
            'loan_start_date' => now()->addDays(2)->toDateTimeString(),
            'loan_end_date' => now()->addDays(5)->toDateTimeString(),
            'responsible_officer_id' => $this->applicant->id,
            'items' => [], // Empty items
            'applicant_confirmation' => true,
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $this->loanApplicationService->createAndSubmitApplication($applicationData, $this->applicant, false);
    }

    public function test_create_application_fails_if_no_confirmation_when_not_draft_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('Perakuan pemohon mesti diterima sebelum penghantaran.'));
        $equipment = Equipment::factory()->create(['status' => 'available']);
        $applicationData = [
            'purpose' => 'Test Purpose - No Confirmation',
            'location' => 'Test Location',
            'return_location' => 'Test Return Location',
            'loan_start_date' => now()->addDays(2)->toDateTimeString(),
            'loan_end_date' => now()->addDays(5)->toDateTimeString(),
            'responsible_officer_id' => $this->applicant->id,
            'items' => [
                ['equipment_type' => $equipment->asset_type, 'quantity_requested' => 1],
            ],
            'applicant_confirmation' => false, // Missing confirmation
            'supporting_officer_id' => $this->supportingOfficer->id,
        ];

        $this->loanApplicationService->createAndSubmitApplication($applicationData, $this->applicant, false);
    }

    /**
     * Test submitting a draft loan application for approval.
     */
    public function test_submit_draft_application_for_approval(): void
    {
        // Mock NotificationService to prevent actual notifications during test
        $notificationServiceMock = $this->mock(NotificationService::class);
        $notificationServiceMock->shouldReceive('notifyApplicantApplicationSubmitted')->once();
        $notificationServiceMock->shouldReceive('notifyApproverApplicationNeedsAction')->once();

        // Re-bind the service with the mock for this test if not already mocked globally
        $this->loanApplicationService = new LoanApplicationService(
            $this->app->make(ApprovalService::class), // Use real or mock ApprovalService as needed
            $this->app->make(LoanTransactionService::class),
            $notificationServiceMock
        );

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

        // Check if an approval task was created
        $this->assertDatabaseHas('approvals', [
            'approvable_id' => $submittedApplication->id,
            'approvable_type' => LoanApplication::class,
            'officer_id' => $this->supportingOfficer->id,
            'stage' => Approval::STAGE_LOAN_SUPPORT_REVIEW,
            'status' => Approval::STATUS_PENDING,
        ]);
    }

    public function test_submit_application_for_approval_fails_if_not_draft_or_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('Hanya draf permohonan atau permohonan yang ditolak boleh dihantar semula. Status semasa: Diluluskan'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_APPROVED, // Invalid status for submission
            'applicant_confirmation_timestamp' => now(),
            'supporting_officer_id' => $this->supportingOfficer->id,
        ]);

        $this->loanApplicationService->submitApplicationForApproval($application, $this->applicant);
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
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
            'purpose' => 'Initial Purpose',
        ]);
        $updateData = [
            'purpose' => 'Updated Purpose for Draft',
            'location' => 'Updated Location',
            'items' => [ // Ensure items sync logic is also tested if it's part of update
                // Potentially add item update data here
            ],
            'supporting_officer_id' => $this->supportingOfficer->id, // Example of updatable field
            // 'applicant_confirmation' => false, // Can remain false for draft
        ];

        $updatedApplication = $this->loanApplicationService->updateApplication(
            $loanApplication,
            $updateData,
            $this->applicant
        );

        $this->assertInstanceOf(LoanApplication::class, $updatedApplication);
        $this->assertEquals('Updated Purpose for Draft', $updatedApplication->purpose);
        $this->assertEquals('Updated Location', $updatedApplication->location);
        $this->assertEquals(LoanApplication::STATUS_DRAFT, $updatedApplication->status);
    }

    public function test_update_application_fails_if_not_draft_or_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('Hanya draf permohonan atau permohonan yang ditolak boleh dikemaskini. Status semasa: Dalam Kelulusan Sokongan'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_PENDING_SUPPORT, // Invalid status for update
        ]);
        $updateData = ['purpose' => 'Trying to update submitted app'];

        $this->loanApplicationService->updateApplication($application, $updateData, $this->applicant);
    }

    /**
     * Test deleting a draft loan application.
     */
    public function test_delete_draft_loan_application(): void
    {
        $draftApplication = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_DRAFT,
        ]);
        LoanApplicationItem::factory()->create(['loan_application_id' => $draftApplication->id]);

        $result = $this->loanApplicationService->deleteApplication($draftApplication, $this->applicant);

        $this->assertTrue($result);
        $this->assertSoftDeleted($draftApplication);
        // Also assert that related loanApplicationItems are deleted if cascade or manual delete is implemented
        $this->assertEquals(0, LoanApplicationItem::where('loan_application_id', $draftApplication->id)->count());
    }

    public function test_delete_non_draft_application_fails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('Hanya draf permohonan yang boleh dibuang.'));

        $application = LoanApplication::factory()->create([
            'user_id' => $this->applicant->id,
            'status' => LoanApplication::STATUS_PENDING_SUPPORT, // Not a draft
        ]);

        $this->loanApplicationService->deleteApplication($application, $this->applicant);
    }

    // Add more tests for:
    // - Logic for finding the Head of Department for an application (if handled by this service or ApprovalService)
    // - Application of config rules (e.g. min_loan_support_grade_level for supporting_officer_id on update/submit).
    // - createIssueTransaction and createReturnTransaction (these are complex and might need their own test class or extensive mocking of LoanTransactionService)
    // - getApplicationsForUser with various filters and user roles.
    // - getActiveLoansSummary.
    // - findLoanApplicationById.
    // - syncApplicationItems in detail (add, update, delete items).
}
