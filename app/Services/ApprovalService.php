<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use App\Notifications\ApplicationApproved;
use App\Notifications\ApplicationNeedsAction;
use App\Notifications\ApplicationRejected;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class ApprovalService
{
    private const LOG_AREA = 'ApprovalService: ';

    public function initiateApprovalWorkflow(
        Model $approvableItem, // EmailApplication or LoanApplication
        User $submittedBy,    // User who submitted the application
        string $initialStage,   // e.g., Approval::STAGE_EMAIL_SUPPORT_REVIEW
        User $assignedOfficer // User to whom this first approval task is assigned
    ): Approval {
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $itemMorphClass = $approvableItem->getMorphClass();
        Log::info(self::LOG_AREA . "Initiating approval workflow for {$itemMorphClass} ID {$itemIdLog}, stage '{$initialStage}'. Submitted by User ID {$submittedBy->id}. Assigning to Officer ID {$assignedOfficer->id}.");

        if (!method_exists($approvableItem, 'approvals')) {
            Log::error(self::LOG_AREA . "Approvable item {$itemMorphClass} ID {$itemIdLog} does not have 'approvals' relationship.");
            throw new RuntimeException("Item {$itemMorphClass} tidak dikonfigurasi untuk kelulusan.");
        }

        $pendingStatus = Approval::STATUS_PENDING;
        $stageDisplayName = Approval::getStageDisplayName($initialStage);

        try {
            /** @var Approval $approvalTask */
            $approvalTask = $approvableItem->approvals()->create([ // Using relationship to create
                'officer_id' => $assignedOfficer->id,
                'stage' => $initialStage,
                'status' => $pendingStatus,
                'comments' => __("Menunggu tindakan :officerName untuk peringkat: :stage", ['officerName' => $assignedOfficer->name, 'stage' => $stageDisplayName]),
                // 'created_by' and 'updated_by' will be handled by BlameableObserver
                // if the Approval model uses it and submittedBy is the Auth::user()
            ]);

            Log::info(self::LOG_AREA . "Approval task ID {$approvalTask->id} created for {$itemMorphClass} ID {$itemIdLog}, assigned to officer ID {$assignedOfficer->id}.");

            if (class_exists(ApplicationNeedsAction::class)) {
                $assignedOfficer->notify(new ApplicationNeedsAction($approvalTask, $approvableItem));
            }

            return $approvalTask;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA . "Failed to initiate approval workflow for {$itemMorphClass} ID {$itemIdLog}: {$e->getMessage()}", ['exception' => $e]);
            throw new RuntimeException(__('Gagal memulakan aliran kerja kelulusan: ') . $e->getMessage(), 0, $e);
        }
    }

    public function processApprovalDecision(
        Approval $approvalTask,
        string $decision, // Approval::STATUS_APPROVED or Approval::STATUS_REJECTED
        User $processedBy,
        ?string $comments = null
    ): Model {
        $taskIdLog = $approvalTask->id;
        $approvableItem = $approvalTask->approvable()->first(); // Ensure it's fetched

        if (!$approvableItem) {
            Log::critical(self::LOG_AREA . "CRITICAL: Approvable item not found for Approval Task ID {$taskIdLog}.");
            throw new ModelNotFoundException(__("Item berkaitan untuk diluluskan tidak ditemui bagi Tugasan Kelulusan ID :taskId.", ['taskId' => $taskIdLog]));
        }
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $itemClass = $approvableItem->getMorphClass();

        Log::info(self::LOG_AREA . "Processing approval decision for Task ID {$taskIdLog} ({$itemClass} ID {$itemIdLog}). Decision: {$decision}. By User ID {$processedBy->id}. Stage: {$approvalTask->stage}.", compact('comments'));

        if (!in_array($decision, [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED], true)) {
            throw new InvalidArgumentException(__("Keputusan kelulusan ':decision' tidak sah untuk Tugasan ID :taskId.", ['decision' => $decision, 'taskId' => $taskIdLog]));
        }

        // Authorization: Ensure $processedBy is the assigned officer or an admin
        if ((int) $approvalTask->officer_id !== (int) $processedBy->id && !$processedBy->hasRole('Admin')) {
            Log::warning(self::LOG_AREA . "User ID {$processedBy->id} is not assigned officer (ID: {$approvalTask->officer_id}) nor Admin for Task ID {$taskIdLog}. Denied.");
            throw new AuthorizationException(__('Anda tidak mempunyai kebenaran untuk memproses tugasan kelulusan ini.'));
        }

        if ($approvalTask->status !== Approval::STATUS_PENDING) {
            throw new RuntimeException(__("Tugasan Kelulusan ID :taskId tidak dalam status 'pending' (semasa: :status). Tidak boleh memproses keputusan.", ['taskId' => $taskIdLog, 'status' => $approvalTask->status]));
        }

        $approvalTask->status = $decision;
        $approvalTask->comments = $comments;
        $approvalTask->approval_timestamp = now();
        $approvalTask->save(); // BlameableObserver handles updated_by

        Log::info(self::LOG_AREA . "Task ID {$taskIdLog} status updated to '{$decision}'.");

        if ($decision === Approval::STATUS_APPROVED) {
            $this->handleApprovedItem($approvableItem, $approvalTask, $processedBy);
        } else { // STATUS_REJECTED
            $this->handleRejectedItem($approvableItem, $approvalTask, $processedBy, $comments);
        }

        return $approvableItem->refresh();
    }

    protected function handleApprovedItem(Model $approvableItem, Approval $currentApprovalTask, User $approvedBy): void
    {
        $itemClass = $approvableItem->getMorphClass();
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $currentStage = $currentApprovalTask->stage;
        $currentStageDisplay = Approval::getStageDisplayName($currentStage);

        Log::info(self::LOG_AREA . "Handling approved item: {$itemClass} ID {$itemIdLog}, approved at stage '{$currentStageDisplay}' by User ID {$approvedBy->id}.");

        $nextStageKey = null;
        $finalApprovalStatusForItem = null;
        $itemStatusBeforeNextStage = null;

        if ($approvableItem instanceof EmailApplication) {
            if ($currentStage === Approval::STAGE_EMAIL_SUPPORT_REVIEW) {
                $nextStageKey = Approval::STAGE_EMAIL_ADMIN_REVIEW; // Next is IT Admin review
                $itemStatusBeforeNextStage = EmailApplication::STATUS_PENDING_ADMIN;
            } elseif ($currentStage === Approval::STAGE_EMAIL_ADMIN_REVIEW) {
                $finalApprovalStatusForItem = EmailApplication::STATUS_APPROVED; // Final approval for email app
            }
        } elseif ($approvableItem instanceof LoanApplication) {
            if ($currentStage === Approval::STAGE_LOAN_SUPPORT_REVIEW) {
                $nextStageKey = Approval::STAGE_LOAN_HOD_REVIEW;
                $itemStatusBeforeNextStage = LoanApplication::STATUS_PENDING_HOD_REVIEW;
            } elseif ($currentStage === Approval::STAGE_LOAN_HOD_REVIEW) {
                $nextStageKey = Approval::STAGE_LOAN_BPM_REVIEW;
                $itemStatusBeforeNextStage = LoanApplication::STATUS_PENDING_BPM_REVIEW;
            } elseif ($currentStage === Approval::STAGE_LOAN_BPM_REVIEW) {
                $finalApprovalStatusForItem = LoanApplication::STATUS_APPROVED; // Final approval for loan app
            }
        }

        if ($finalApprovalStatusForItem && method_exists($approvableItem, 'transitionToStatus')) {
            $approvableItem->transitionToStatus($finalApprovalStatusForItem, __("Diluluskan sepenuhnya pada peringkat ':stage'.", ['stage' => $currentStageDisplay]), $approvedBy->id);
            Log::info(self::LOG_AREA . "{$itemClass} ID {$itemIdLog} reached final approval status: '{$finalApprovalStatusForItem}'.");

            if ($approvableItem->user instanceof User && class_exists(ApplicationApproved::class)) {
                $approvableItem->user->notify(new ApplicationApproved($approvableItem));
            }
        } elseif ($nextStageKey && $itemStatusBeforeNextStage && method_exists($approvableItem, 'transitionToStatus')) {
            $nextStageDisplay = Approval::getStageDisplayName($nextStageKey);
            $approvableItem->transitionToStatus($itemStatusBeforeNextStage, __("Diluluskan pada peringkat ':currentStage', menunggu peringkat ':nextStage'.", ['currentStage' => $currentStageDisplay, 'nextStage' => $nextStageDisplay]), $approvedBy->id);

            $nextOfficer = $this->findOfficerForStage($approvableItem, $nextStageKey, $approvedBy);
            if ($nextOfficer) {
                $this->initiateApprovalWorkflow($approvableItem, $approvedBy, $nextStageKey, $nextOfficer);
                Log::info(self::LOG_AREA . "Initiated next approval stage '{$nextStageDisplay}' for {$itemClass} ID {$itemIdLog}, assigned to Officer ID {$nextOfficer->id}.");
            } else {
                Log::error(self::LOG_AREA . "Could not find officer for next stage '{$nextStageDisplay}' for {$itemClass} ID {$itemIdLog}. Item status is '{$approvableItem->status}'. Further action required.");
                // Notify super admin or specific group about stalled workflow
            }
        } else {
            Log::warning(self::LOG_AREA . "{$itemClass} ID {$itemIdLog} approved at stage '{$currentStageDisplay}', but no defined next step or final status. Item status: '{$approvableItem->status}'. Workflow may be incomplete.");
        }
    }

    protected function handleRejectedItem(Model $approvableItem, Approval $currentApprovalTask, User $rejectedBy, ?string $rejectionReason): void
    {
        $itemClass = $approvableItem->getMorphClass();
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $currentStageDisplay = Approval::getStageDisplayName($currentApprovalTask->stage);
        Log::info(self::LOG_AREA . "Handling rejected item: {$itemClass} ID {$itemIdLog}, stage '{$currentStageDisplay}'. Reason: " . ($rejectionReason ?? 'N/A'));

        $rejectedStatusForItem = null;
        if ($approvableItem instanceof EmailApplication) $rejectedStatusForItem = EmailApplication::STATUS_REJECTED;
        elseif ($approvableItem instanceof LoanApplication) $rejectedStatusForItem = LoanApplication::STATUS_REJECTED;

        if ($rejectedStatusForItem && method_exists($approvableItem, 'transitionToStatus')) {
            if (property_exists($approvableItem, 'rejection_reason') && $approvableItem->isFillable('rejection_reason')) {
                 $approvableItem->rejection_reason = $rejectionReason;
            }
            $approvableItem->transitionToStatus($rejectedStatusForItem, __("Ditolak pada peringkat ':stage'. Alasan: :reason", ['stage' => $currentStageDisplay, 'reason' => ($rejectionReason ?: __('Tiada alasan diberikan'))]), $rejectedBy->id);
            Log::info(self::LOG_AREA . "{$itemClass} ID {$itemIdLog} status updated to '{$rejectedStatusForItem}'.");
        } else {
             Log::warning(self::LOG_AREA . "Could not set rejected status for {$itemClass} ID {$itemIdLog}. Model issues or status missing.");
             // Fallback attempt
            if (property_exists($approvableItem, 'status') && $rejectedStatusForItem) {
                $approvableItem->status = $rejectedStatusForItem;
                if (property_exists($approvableItem, 'rejection_reason') && $approvableItem->isFillable('rejection_reason')) {
                    $approvableItem->rejection_reason = $rejectionReason;
                }
                $approvableItem->save();
                Log::info(self::LOG_AREA . "{$itemClass} ID {$itemIdLog} status directly updated to '{$rejectedStatusForItem}'.");
            }
        }

        if ($approvableItem->user instanceof User && class_exists(ApplicationRejected::class)) {
            $approvableItem->user->notify(new ApplicationRejected($approvableItem, $rejectedBy, $rejectionReason));
        }
    }

    protected function findOfficerForStage(Model $approvableItem, string $stageKey, User $previousActor): ?User
    {
        $logIdentifier = "{$approvableItem->getMorphClass()} ID " . ($approvableItem->id ?? 'N/A_ITEM_ID');
        Log::debug(self::LOG_AREA . "Finding officer for stage '{$stageKey}' for {$logIdentifier}.");

        $applicant = $approvableItem->user ?? null;
        $officer = null;
        $activeUserStatus = User::STATUS_ACTIVE;

        // Logic to find the next officer based on stage and application type
        // This is highly dependent on your organization's structure and approval matrix.
        // Example:
        if ($approvableItem instanceof EmailApplication) {
            if ($stageKey === Approval::STAGE_EMAIL_ADMIN_REVIEW) {
                // Assign to any user with 'IT Admin' or 'BPM Staff' role.
                // For better distribution, consider round-robin, load balancing, or specific department logic.
                $officer = User::role(['IT Admin', 'BPMStaff'])->where('status', $activeUserStatus)->inRandomOrder()->first();
            }
        } elseif ($approvableItem instanceof LoanApplication) {
            if ($stageKey === Approval::STAGE_LOAN_HOD_REVIEW && $applicant?->department_id) {
                // Find HOD of the applicant's department.
                // This assumes HODs have a specific 'HOD' role and are in the same department.
                $officer = User::where('department_id', $applicant->department_id)
                    ->role('HOD')
                    ->where('status', $activeUserStatus)
                    ->first();
                if (!$officer) Log::warning(self::LOG_AREA."No HOD found for department ID: {$applicant->department_id} for {$logIdentifier}.");
            } elseif ($stageKey === Approval::STAGE_LOAN_BPM_REVIEW) {
                // Assign to any user with 'BPM Staff' role.
                $officer = User::role('BPMStaff')->where('status', $activeUserStatus)->inRandomOrder()->first();
            }
        }

        if ($officer) {
            Log::info(self::LOG_AREA . "Found officer ID {$officer->id} ({$officer->name}) for stage '{$stageKey}' for {$logIdentifier}.");
            return $officer;
        }

        Log::error(self::LOG_AREA . "No officer found for stage '{$stageKey}' for {$logIdentifier}. Workflow cannot proceed automatically.");
        // CRITICAL: Notify super admins or a default fallback group about the inability to find the next approver.
        // E.g., NotificationFacade::send(User::role('SuperAdmin')->get(), new WorkflowStalledNotification($approvableItem, $stageKey));
        return null;
    }
}
