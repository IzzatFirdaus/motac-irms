<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
// use Carbon\Carbon; // replaced with now() helper to satisfy static analysis types
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApprovalService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new approval record for a loan application.
     *
     * @param LoanApplication $loanApplication The loan application to be approved.
     * @param User            $approver        The user designated as the approver.
     * @param string          $level           The approval level (e.g., 'level_1', 'level_2').
     */
    public function createApproval(LoanApplication $loanApplication, User $approver, string $level): Approval
    {
        return DB::transaction(function () use ($loanApplication, $approver, $level) {
            $approval = new Approval([
                'loan_application_id' => $loanApplication->id,
                'approver_id'         => $approver->id,
                'level'               => $level,
                'status'              => 'pending', // Default status
            ]);
            $approval->save();

            // Notify the approver that there's a pending approval for them
            $this->notificationService->notifyApproverOfPendingApproval($approver, $approval);

            Log::info(sprintf('Approval record created for Loan Application ID %d, Level %s, Approver ID %d.', $loanApplication->id, $level, $approver->id));

            return $approval;
        });
    }

    /**
     * Record a decision for an approval task.
     *
     * @param Approval    $approval      The approval model instance.
     * @param string      $decision      The decision made (e.g., 'approved', 'rejected').
     * @param string|null $notes         Optional notes for the decision.
     * @param array       $approvalItems Additional approval items (if any, for loan applications).
     */
    public function recordApprovalDecision(Approval $approval, string $decision, ?string $notes = null, array $approvalItems = []): void
    {
        DB::transaction(function () use ($approval, $decision, $notes, $approvalItems) {
            // Set status and timestamps based on decision
            $approval->status = $decision;
            $approval->notes  = $notes;

            if ($decision === Approval::STATUS_APPROVED) {
                $approval->approved_at = now();
            } elseif ($decision === Approval::STATUS_REJECTED) {
                $approval->rejected_at = now();
            } elseif ($decision === Approval::STATUS_CANCELED) {
                $approval->canceled_at = now();
            }

            $approval->save();

            // If there are approvalItems, you can process them for child records (e.g., for loan applications)
            // This is left as a placeholder for extensibility
            // foreach ($approvalItems as $itemId => $itemDecision) { ... }

            Log::info('Approval decision recorded.', [
                'approval_id'    => $approval->id,
                'decision'       => $decision,
                'notes'          => $notes,
                'approval_items' => $approvalItems,
            ]);
        });
    }

    /**
     * Compatibility wrapper for older callers named `processApprovalDecision`.
     * Keeps existing behavior by delegating to recordApprovalDecision.
     *
     * @param Approval $approval
     * @param string $decision
     * @param string|null $notes
     * @param array $approvalItems
     */
    public function processApprovalDecision(Approval $approval, string $decision, ?User $actor = null, ?string $notes = null, ?array $approvalItems = null): void
    {
        // Allow older callers (without $actor) and newer callers (with $actor) seamlessly
        $this->recordApprovalDecision($approval, $decision, $notes, $approvalItems ?? []);
    }

    /**
     * Handles the logic after an approval decision is approved.
     *
     * @param Approval        $approval        The approval record that was approved.
     * @param LoanApplication $loanApplication The associated loan application.
     * @param string|null     $comments        Optional comments from the approver.
     *
     * @throws Throwable
     */
    public function handleApprovedDecision(Approval $approval, LoanApplication $loanApplication, ?string $comments): void
    {
        DB::transaction(function () use ($approval, $loanApplication, $comments): void {
            // Update the current approval record
            $approval->status      = Approval::STATUS_APPROVED;
            $approval->notes       = $comments;
            $approval->approved_at = now();
            $approval->save();

            Log::info(sprintf('Approval ID %d for Loan Application ID %d has been approved by officer ID %d.', $approval->id, $loanApplication->id, $approval->officer_id));

            // Check if there are further approval levels
            $currentLevel = $approval->getAttribute('level');
            $nextApproval = $loanApplication->approvals()->where('level', '>', $currentLevel)->orderBy('level')->first();

            if ($nextApproval) {
                // If there's a next approval level, update the loan application status
                // and notify the next approver.
                $loanApplication->status = $this->determineNextApprovalStatus($nextApproval->getAttribute('level'));
                $loanApplication->save();
                Log::info(sprintf('Loan Application ID %d status updated to %s. Next approval level is %s.', $loanApplication->id, $loanApplication->status, $nextApproval->getAttribute('level')));
                if ($nextApproval->officer instanceof User) {
                    $this->notificationService->notifyApproverOfPendingApproval($nextApproval->officer, $nextApproval);
                }
            } else {
                // If this was the final approval, mark the loan application as approved overall.
                $loanApplication->status      = LoanApplication::STATUS_APPROVED;
                $loanApplication->approved_at = now();
                $loanApplication->save();
                Log::info(sprintf('Loan Application ID %d status updated to APPROVED (final approval).', $loanApplication->id));

                // Notify the applicant that their application has been approved.
                $this->notificationService->notifyApplicationApproved($loanApplication->user, $loanApplication);

                // Optionally, notify relevant officers that the application is ready for issuance
                // (e.g., support officers or inventory managers)
                $issuingOfficers = User::whereHasRole('issuing_officer')->get(); // Example: Fetch users with 'issuing_officer' role
                foreach ($issuingOfficers as $officer) {
                    if ($officer instanceof User) {
                        $this->notificationService->notifyLoanApplicationReadyForIssuance($officer, $loanApplication);
                    }
                }
            }
        });
    }

    /**
     * Determine the loan application status based on the next approval level.
     */
    private function determineNextApprovalStatus(string $nextLevel): string
    {
        return match ($nextLevel) {
            'level_2' => LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            'level_3' => LoanApplication::STATUS_PENDING_BPM_REVIEW,
            default   => LoanApplication::STATUS_PROCESSING, // Fallback
        };
    }

    /**
     * Handles the logic after an approval decision is rejected.
     */
    public function handleRejectedDecision(Approval $approval, LoanApplication $loanApplication, ?string $reason): void
    {
        DB::transaction(function () use ($approval, $loanApplication, $reason): void {
            // Update the approval record
            $approval->status      = Approval::STATUS_REJECTED;
            $approval->notes       = $reason;
            $approval->rejected_at = now();
            $approval->save();

            Log::info(sprintf('Approval ID %d for Loan Application ID %d has been rejected by officer ID %d.', $approval->id, $loanApplication->id, $approval->officer_id));

            // Mark the loan application as rejected
            $loanApplication->status      = LoanApplication::STATUS_REJECTED;
            $loanApplication->rejected_at = now();
            $loanApplication->save();
            Log::info(sprintf('Loan Application ID %d status updated to REJECTED.', $loanApplication->id));

            // Notify the applicant that their application has been rejected
            $this->notificationService->notifyApplicationRejected($loanApplication->user, $loanApplication, $reason ?? 'No reason provided.');
        });
    }
}
