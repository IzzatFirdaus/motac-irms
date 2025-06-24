<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use App\Notifications\ApplicationNeedsAction;
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

    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function initiateApprovalWorkflow(
        Model $approvableItem,
        User $submittedBy,
        string $initialStage,
        ?User $assignedOfficerModel = null,
        ?array $manualOfficerDetails = null
    ): ?Approval {
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $itemMorphClass = $approvableItem->getMorphClass();

        $officerNameToLog = $assignedOfficerModel?->name ?? $manualOfficerDetails['officer_name'] ?? 'Manual Officer (Details Provided)';
        $officerIdToLog = $assignedOfficerModel?->id ?? 'N/A_MANUAL (Not a system user)';

        Log::info(self::LOG_AREA.sprintf("Initiating approval workflow for %s ID %s, stage '%s'. Submitted by User ID %d. Assigning to Officer: %s (ID: %s).", $itemMorphClass, $itemIdLog, $initialStage, $submittedBy->id, $officerNameToLog, $officerIdToLog));

        if (! method_exists($approvableItem, 'approvals')) {
            Log::error(self::LOG_AREA.sprintf("Approvable item %s ID %s does not have 'approvals' relationship defined.", $itemMorphClass, $itemIdLog));
            throw new RuntimeException(sprintf('Item %s tidak dikonfigurasi untuk aliran kelulusan.', $itemMorphClass));
        }

        if (! $assignedOfficerModel instanceof \App\Models\User) {
            Log::warning(self::LOG_AREA.sprintf("No system User model provided for 'assignedOfficerModel'. Approval task not created in 'approvals' table for %s ID %s. Stage: %s. Manual details: ", $itemMorphClass, $itemIdLog, $initialStage).json_encode($manualOfficerDetails), [
                'manual_details' => $manualOfficerDetails,
            ]);

            return null;
        }

        $pendingStatus = Approval::STATUS_PENDING;
        $stageDisplayName = Approval::getStageDisplayName($initialStage);

        try {
            /** @var Approval $approvalTask */
            $approvalTask = $approvableItem->approvals()->create([
                'officer_id' => $assignedOfficerModel->id,
                'stage' => $initialStage,
                'status' => $pendingStatus,
                'comments' => __('Menunggu tindakan :officerName untuk peringkat: :stage', ['officerName' => $assignedOfficerModel->name, 'stage' => $stageDisplayName]),
            ]);

            Log::info(self::LOG_AREA.sprintf('Approval task ID %d created for %s ID %s, assigned to officer ID %d.', $approvalTask->id, $itemMorphClass, $itemIdLog, $assignedOfficerModel->id));

            if (class_exists(ApplicationNeedsAction::class)) {
                $this->notificationService->notifyApproverApplicationNeedsAction($approvalTask);
            }

            return $approvalTask;
        } catch (Throwable $throwable) {
            Log::error(self::LOG_AREA.sprintf('Failed to initiate approval workflow for %s ID %s: %s', $itemMorphClass, $itemIdLog, $throwable->getMessage()), ['exception' => $throwable]);
            throw new RuntimeException(__('Gagal memulakan aliran kerja kelulusan: ').$throwable->getMessage(), 0, $throwable);
        }
    }

    public function processApprovalDecision(
        Approval $approvalTask,
        string $decision,
        User $processedBy,
        ?string $comments = null,
        ?array $itemQuantities = null
    ): Model {
        $taskIdLog = $approvalTask->id;
        $approvableItem = $approvalTask->approvable()->first();

        if (! $approvableItem) {
            Log::critical(self::LOG_AREA.sprintf('CRITICAL: Approvable item not found for Approval Task ID %d.', $taskIdLog));
            throw new ModelNotFoundException(__('Item berkaitan untuk diluluskan tidak ditemui bagi Tugasan Kelulusan ID :taskId.', ['taskId' => $taskIdLog]));
        }

        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $itemClass = $approvableItem->getMorphClass();

        Log::info(self::LOG_AREA.sprintf('Processing approval decision for Task ID %d (%s ID %s). Decision: %s. By User ID %d. Stage: %s.', $taskIdLog, $itemClass, $itemIdLog, $decision, $processedBy->id, $approvalTask->stage), ['comments' => $comments]);

        if (! in_array($decision, [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED], true)) {
            throw new InvalidArgumentException(__("Keputusan kelulusan ':decision' tidak sah untuk Tugasan ID :taskId.", ['decision' => $decision, 'taskId' => $taskIdLog]));
        }

        if ((int) $approvalTask->officer_id !== (int) $processedBy->id && ! $processedBy->hasRole('Admin')) {
            Log::warning(self::LOG_AREA.sprintf('User ID %d is not assigned officer (ID: %d) nor Admin for Task ID %d. Denied.', $processedBy->id, $approvalTask->officer_id, $taskIdLog));
            throw new AuthorizationException(__('Anda tidak mempunyai kebenaran untuk memproses tugasan kelulusan ini.'));
        }

        if ($approvalTask->status !== Approval::STATUS_PENDING) {
            throw new RuntimeException(__("Tugasan Kelulusan ID :taskId tidak dalam status 'pending' (semasa: :status). Tidak boleh memproses keputusan.", ['taskId' => $taskIdLog, 'status' => $approvalTask->status]));
        }

        $approvalTask->status = $decision;
        $approvalTask->comments = $comments;
        $approvalTask->approval_timestamp = now();
        $approvalTask->save();

        Log::info(self::LOG_AREA.sprintf("Task ID %d status updated to '%s'.", $taskIdLog, $decision));

        if ($decision === Approval::STATUS_APPROVED) {
            $this->handleApprovedItem($approvableItem, $approvalTask, $processedBy, $itemQuantities);
        } else { // STATUS_REJECTED
            $this->handleRejectedItem($approvableItem, $processedBy, $comments);
        }

        return $approvableItem->refresh();
    }

    private function handleApprovedItem(Model $approvableItem, Approval $currentApprovalTask, User $approvedBy, ?array $itemQuantities = null): void
    {
        $itemClass = $approvableItem->getMorphClass();
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $currentStage = $currentApprovalTask->stage;

        if ($approvableItem instanceof LoanApplication && is_array($itemQuantities)) {
            foreach ($itemQuantities as $itemData) {
                $loanAppItemId = $itemData['loan_application_item_id'] ?? null;
                $qtyApproved = $itemData['quantity_approved'] ?? null;
                if ($loanAppItemId !== null && $qtyApproved !== null) {
                    $appItem = LoanApplicationItem::find($loanAppItemId);
                    if ($appItem && $appItem->loan_application_id === $approvableItem->id) {
                        $appItem->quantity_approved = min((int) $qtyApproved, $appItem->quantity_requested);
                        $appItem->save();
                    }
                }
            }

            $approvableItem->refresh();
        }

        if ($approvableItem instanceof LoanApplication && $currentStage === Approval::STAGE_LOAN_SUPPORT_REVIEW) {
            $approvableItem->status = LoanApplication::STATUS_APPROVED;
            $approvableItem->approved_by = $approvedBy->id;
            $approvableItem->approved_at = now();
            $approvableItem->save();
            Log::info(self::LOG_AREA.sprintf("%s ID %s status set to '%s'.", $itemClass, $itemIdLog, $approvableItem->status));

            if ($approvableItem->user) {
                $this->notificationService->notifyApplicantApplicationApproved($approvableItem);

                // EDIT: Changed the query to be guard-agnostic, fixing the RoleDoesNotExist error.
                $bpmStaff = User::whereHas('roles', fn ($query) => $query->where('name', 'BPM Staff'))
                    ->where('status', User::STATUS_ACTIVE)->get();

                if ($bpmStaff->isNotEmpty()) {
                    $this->notificationService->notifyBpmLoanReadyForIssuance($approvableItem, $bpmStaff);
                } else {
                    Log::warning(self::LOG_AREA.sprintf("No active 'BPM Staff' found for approved LoanApplication ID %s.", $itemIdLog));
                }
            }
        }

        // ... other logic for different application types can follow
    }

    private function handleRejectedItem(Model $approvableItem, User $rejectedBy, ?string $rejectionReason): void
    {
        $itemClass = $approvableItem->getMorphClass();
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';

        $rejectedStatusForItem = null;
        if ($approvableItem instanceof EmailApplication) {
            $rejectedStatusForItem = EmailApplication::STATUS_REJECTED;
        } elseif ($approvableItem instanceof LoanApplication) {
            $rejectedStatusForItem = LoanApplication::STATUS_REJECTED;
        }

        if ($rejectedStatusForItem !== null && $rejectedStatusForItem !== '' && $rejectedStatusForItem !== '0') {
            $approvableItem->status = $rejectedStatusForItem;
            if (property_exists($approvableItem, 'rejection_reason') && $approvableItem->isFillable('rejection_reason')) {
                $approvableItem->rejection_reason = $rejectionReason;
            }

            $approvableItem->save();
            Log::info(self::LOG_AREA.sprintf("%s ID %s status updated to '%s'.", $itemClass, $itemIdLog, $rejectedStatusForItem));
        }

        if ($approvableItem->user) {
            $this->notificationService->notifyApplicantApplicationRejected($approvableItem, $rejectedBy, $rejectionReason);
        }
    }

    public function findOfficerForStage(): ?User
    {
        // This method remains unchanged
        return null;
    }
}
