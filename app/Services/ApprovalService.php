<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
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

        Log::info(self::LOG_AREA."Initiating approval workflow for {$itemMorphClass} ID {$itemIdLog}, stage '{$initialStage}'. Submitted by User ID {$submittedBy->id}. Assigning to Officer: {$officerNameToLog} (ID: {$officerIdToLog}).");

        if (! method_exists($approvableItem, 'approvals')) {
            Log::error(self::LOG_AREA."Approvable item {$itemMorphClass} ID {$itemIdLog} does not have 'approvals' relationship defined.");
            throw new RuntimeException("Item {$itemMorphClass} tidak dikonfigurasi untuk aliran kelulusan.");
        }

        if (! $assignedOfficerModel) {
            Log::warning(self::LOG_AREA."No system User model provided for 'assignedOfficerModel'. Approval task not created in 'approvals' table for {$itemMorphClass} ID {$itemIdLog}. Stage: {$initialStage}. Manual details: ".json_encode($manualOfficerDetails), [
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

            Log::info(self::LOG_AREA."Approval task ID {$approvalTask->id} created for {$itemMorphClass} ID {$itemIdLog}, assigned to officer ID {$assignedOfficerModel->id}.");

            if (class_exists(ApplicationNeedsAction::class)) {
                $this->notificationService->notifyApproverApplicationNeedsAction($approvalTask);
            }

            return $approvalTask;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA."Failed to initiate approval workflow for {$itemMorphClass} ID {$itemIdLog}: {$e->getMessage()}", ['exception' => $e]);
            throw new RuntimeException(__('Gagal memulakan aliran kerja kelulusan: ').$e->getMessage(), 0, $e);
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
            Log::critical(self::LOG_AREA."CRITICAL: Approvable item not found for Approval Task ID {$taskIdLog}.");
            throw new ModelNotFoundException(__('Item berkaitan untuk diluluskan tidak ditemui bagi Tugasan Kelulusan ID :taskId.', ['taskId' => $taskIdLog]));
        }
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';
        $itemClass = $approvableItem->getMorphClass();

        Log::info(self::LOG_AREA."Processing approval decision for Task ID {$taskIdLog} ({$itemClass} ID {$itemIdLog}). Decision: {$decision}. By User ID {$processedBy->id}. Stage: {$approvalTask->stage}.", compact('comments'));

        if (! in_array($decision, [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED], true)) {
            throw new InvalidArgumentException(__("Keputusan kelulusan ':decision' tidak sah untuk Tugasan ID :taskId.", ['decision' => $decision, 'taskId' => $taskIdLog]));
        }

        if ((int) $approvalTask->officer_id !== (int) $processedBy->id && ! $processedBy->hasRole('Admin')) {
            Log::warning(self::LOG_AREA."User ID {$processedBy->id} is not assigned officer (ID: {$approvalTask->officer_id}) nor Admin for Task ID {$taskIdLog}. Denied.");
            throw new AuthorizationException(__('Anda tidak mempunyai kebenaran untuk memproses tugasan kelulusan ini.'));
        }

        if ($approvalTask->status !== Approval::STATUS_PENDING) {
            throw new RuntimeException(__("Tugasan Kelulusan ID :taskId tidak dalam status 'pending' (semasa: :status). Tidak boleh memproses keputusan.", ['taskId' => $taskIdLog, 'status' => $approvalTask->status]));
        }

        $approvalTask->status = $decision;
        $approvalTask->comments = $comments;
        $approvalTask->approval_timestamp = now();
        $approvalTask->save();

        Log::info(self::LOG_AREA."Task ID {$taskIdLog} status updated to '{$decision}'.");

        if ($decision === Approval::STATUS_APPROVED) {
            $this->handleApprovedItem($approvableItem, $approvalTask, $processedBy, $itemQuantities);
        } else { // STATUS_REJECTED
            $this->handleRejectedItem($approvableItem, $approvalTask, $processedBy, $comments);
        }

        return $approvableItem->refresh();
    }

    protected function handleApprovedItem(Model $approvableItem, Approval $currentApprovalTask, User $approvedBy, ?array $itemQuantities = null): void
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
            Log::info(self::LOG_AREA."{$itemClass} ID {$itemIdLog} status set to '{$approvableItem->status}'.");

            if ($approvableItem->user) {
                $this->notificationService->notifyApplicantApplicationApproved($approvableItem);

                // EDIT: Changed the query to be guard-agnostic, fixing the RoleDoesNotExist error.
                $bpmStaff = User::whereHas('roles', fn($query) => $query->where('name', 'BPM Staff'))
                                 ->where('status', User::STATUS_ACTIVE)->get();

                if ($bpmStaff->isNotEmpty()) {
                    $this->notificationService->notifyBpmLoanReadyForIssuance($approvableItem, $bpmStaff);
                } else {
                    Log::warning(self::LOG_AREA."No active 'BPM Staff' found for approved LoanApplication ID {$itemIdLog}.");
                }
            }
        }
        // ... other logic for different application types can follow
    }

    protected function handleRejectedItem(Model $approvableItem, Approval $currentApprovalTask, User $rejectedBy, ?string $rejectionReason): void
    {
        $itemClass = $approvableItem->getMorphClass();
        $itemIdLog = $approvableItem->id ?? 'N/A_ITEM_ID';

        $rejectedStatusForItem = null;
        if ($approvableItem instanceof EmailApplication) {
            $rejectedStatusForItem = EmailApplication::STATUS_REJECTED;
        } elseif ($approvableItem instanceof LoanApplication) {
            $rejectedStatusForItem = LoanApplication::STATUS_REJECTED;
        }

        if ($rejectedStatusForItem) {
            $approvableItem->status = $rejectedStatusForItem;
            if (property_exists($approvableItem, 'rejection_reason') && $approvableItem->isFillable('rejection_reason')) {
                $approvableItem->rejection_reason = $rejectionReason;
            }
            $approvableItem->save();
            Log::info(self::LOG_AREA."{$itemClass} ID {$itemIdLog} status updated to '{$rejectedStatusForItem}'.");
        }

        if ($approvableItem->user) {
            $this->notificationService->notifyApplicantApplicationRejected($approvableItem, $rejectedBy, $rejectionReason);
        }
    }

    public function findOfficerForStage(Model $approvableItem, string $stageKey, User $previousActor): ?User
    {
        // This method remains unchanged
        return null;
    }
}
