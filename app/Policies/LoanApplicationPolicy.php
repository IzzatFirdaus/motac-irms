<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoanApplicationPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->id !== null
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai permohonan pinjaman ini.'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->id === $loanApplication->user_id || $user->id === $loanApplication->responsible_officer_id || $user->id === $loanApplication->supporting_officer_id || ($loanApplication->current_approval_officer_id && $user->id === $loanApplication->current_approval_officer_id) || $user->hasRole(['BPM Staff', 'Approver', 'HOD']) || $user->can('view_loan_applications')
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat permohonan pinjaman ini.'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response|bool
    {
        return $user->id !== null
          ? Response::allow()
          : Response::deny(__('Anda mesti log masuk untuk membuat permohonan pinjaman.'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LoanApplication $loanApplication): Response|bool
    {
        $isOwner          = $user->id                                               === $loanApplication->user_id;
        $isEditableStatus = $loanApplication->isDraft() || $loanApplication->status === LoanApplication::STATUS_REJECTED;

        return $isOwner && $isEditableStatus
          ? Response::allow()
          : Response::deny(__('Anda hanya boleh mengemaskini draf permohonan anda atau permohonan yang telah ditolak.'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->id === $loanApplication->user_id && $loanApplication->isDraft()
          ? Response::allow()
          : Response::deny(__('Anda hanya boleh memadam draf permohonan anda sahaja.'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->can('restore_loan_applications')
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan permohonan pinjaman ini.'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->can('force_delete_loan_applications')
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam permohonan pinjaman ini secara kekal.'));
    }

    /**
     * Determine whether the user can submit the model for approval.
     */
    public function submit(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canSubmit = ($user->id === $loanApplication->user_id && ($loanApplication->isDraft() || $loanApplication->status === LoanApplication::STATUS_REJECTED));

        return $canSubmit
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk menghantar permohonan ini atau statusnya tidak membenarkan penghantaran.'));
    }

    /**
     * Determine whether the user can record a decision on the application.
     */
    public function recordDecision(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Add an explicit check for Admin role for clarity, though `before` handles it.
        if ($user->hasRole('Admin')) {
            return Response::allow();
        }

        $actionableStatuses = [
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
        ];
        if (! in_array($loanApplication->status, $actionableStatuses)) {
            return Response::deny(__('Permohonan ini tidak dalam status yang boleh diproses untuk kelulusan/penolakan.'));
        }

        $currentStageKey = $loanApplication->current_approval_stage;
        if (! $currentStageKey) {
            switch ($loanApplication->status) {
                case LoanApplication::STATUS_PENDING_SUPPORT:
                    $currentStageKey = Approval::STAGE_LOAN_SUPPORT_REVIEW;
                    break;
                case LoanApplication::STATUS_PENDING_APPROVER_REVIEW:
                    $currentStageKey = Approval::STAGE_LOAN_APPROVER_REVIEW;
                    break;
                case LoanApplication::STATUS_PENDING_BPM_REVIEW:
                    $currentStageKey = Approval::STAGE_LOAN_BPM_REVIEW;
                    break;
                default:
                    return Response::deny(__('Peringkat kelulusan semasa untuk permohonan ini tidak dapat dikenal pasti.'));
            }
        }

        $pendingApprovalTask = $loanApplication->approvals()
            ->where('status', Approval::STATUS_PENDING)
            ->where('stage', $currentStageKey)
            ->where('officer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($pendingApprovalTask) {
            if ($currentStageKey === Approval::STAGE_LOAN_SUPPORT_REVIEW) {
                $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
                if (! $user->grade || (int) $user->grade->level < $minSupportGradeLevel) {
                    return Response::deny(__('Gred anda (:userGrade) tidak memenuhi syarat minima (Gred :minGrade) untuk menyokong permohonan ini.', ['userGrade' => $user->grade?->name ?? 'N/A', 'minGrade' => $minSupportGradeLevel]));
                }
            } elseif ($currentStageKey === Approval::STAGE_LOAN_APPROVER_REVIEW) {
                $minGeneralApproverGradeLevel = (int) config('motac.approval.min_loan_general_approver_grade_level', 41);
                if (! $user->grade || (int) $user->grade->level < $minGeneralApproverGradeLevel) {
                    return Response::deny(__('Gred anda (:userGrade) tidak memenuhi syarat minima (Gred :minGrade) untuk peringkat kelulusan ini.', ['userGrade' => $user->grade?->name ?? 'N/A', 'minGrade' => $minGeneralApproverGradeLevel]));
                }
            }

            return Response::allow();
        }

        return Response::deny(__('Anda tidak ditetapkan sebagai pegawai pelulus untuk permohonan ini pada peringkat semasa atau tiada tugasan kelulusan aktif untuk anda.'));
    }

    /**
     * Determine whether the user can generally approve applications.
     */
    public function approve(User $user, LoanApplication $loanApplication): Response|bool
    {
        $isPending = in_array($loanApplication->status, [
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
        ]);

        return $user->hasRole(['Approver', 'HOD', 'BPM Staff']) && $isPending
          ? Response::allow()
          : Response::deny(__('Permohonan ini tidak dalam status menunggu kelulusan atau anda tiada kebenaran umum untuk meluluskan.'));
    }

    /**
     * Determine whether the user can process the issuance of items for the loan.
     */
    public function processIssuance(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') && in_array($loanApplication->status, [
              LoanApplication::STATUS_APPROVED,
              LoanApplication::STATUS_PARTIALLY_ISSUED,
          ]);

        return $canProcess
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan ini atau statusnya tidak membenarkan (mesti Diluluskan atau Sebahagian Dikeluarkan).'));
    }

    /**
     * Determine whether the user can process the return of items for the loan.
     */
    public function processReturn(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') && in_array($loanApplication->status, [
              LoanApplication::STATUS_ISSUED,
              LoanApplication::STATUS_PARTIALLY_ISSUED,
              LoanApplication::STATUS_OVERDUE,
              LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION,
          ]);

        return $canProcess
          ? Response::allow()
          : Response::deny(__('Anda tidak mempunyai kebenaran untuk memproses pemulangan bagi permohonan ini atau statusnya tidak membenarkan.'));
    }
}
