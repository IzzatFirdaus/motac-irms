<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanApplicationPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) { // Standardized role 'Admin'
            return true;
        }
        return null;
    }

    public function viewAny(User $user): Response|bool
    {
        // Adjusted to allow any authenticated user to view their own,
        // plus specific roles for broader access.
        return $user->id !== null // Any authenticated user can potentially see a list (filtered to their own by controller/service)
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat senarai permohonan pinjaman ini.'));
    }

    public function view(User $user, LoanApplication $loanApplication): Response|bool
    {
        return $user->id === $loanApplication->user_id || // Applicant
            $user->id === $loanApplication->responsible_officer_id || // Responsible officer
            $user->id === $loanApplication->supporting_officer_id || // Supporting officer
            ($loanApplication->current_approval_officer_id && $user->id === $loanApplication->current_approval_officer_id) || // Current approver
            $user->hasRole(['BPM Staff', 'Approver', 'HOD']) || // Added HOD for viewing
            $user->can('view_loan_applications') // General permission
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat permohonan pinjaman ini.'));
    }

    public function create(User $user): Response|bool
    {
        // Any authenticated user can attempt to create
        return $user->id !== null
            ? Response::allow()
            : Response::deny(__('Anda mesti log masuk untuk membuat permohonan pinjaman.'));
    }

    public function update(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Applicant can update their own draft or rejected application
        return $user->id === $loanApplication->user_id &&
               ($loanApplication->isDraft() || $loanApplication->status === LoanApplication::STATUS_REJECTED) //
            ? Response::allow()
            : Response::deny(__('Anda hanya boleh mengemaskini draf permohonan anda atau permohonan yang telah ditolak.'));
    }

    public function delete(User $user, LoanApplication $loanApplication): Response|bool
    {
         // Applicant can delete their own draft application
        return $user->id === $loanApplication->user_id && $loanApplication->isDraft()
            ? Response::allow()
            : Response::deny(__('Anda hanya boleh memadam draf permohonan anda sahaja.'));
    }

    public function restore(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Only users with specific permission (typically Admins)
        return $user->can('restore_loan_applications')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memulihkan permohonan pinjaman ini.'));
    }

    public function forceDelete(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Only users with specific permission (typically Admins)
        return $user->can('force_delete_loan_applications')
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk memadam permohonan pinjaman ini secara kekal.'));
    }

    public function submit(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canSubmit = ($user->id === $loanApplication->user_id && // Applicant
            ($loanApplication->isDraft() || $loanApplication->status === LoanApplication::STATUS_REJECTED)); // Can submit draft or rejected

        return $canSubmit
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk menghantar permohonan ini atau statusnya tidak membenarkan penghantaran.'));
    }

    public function recordDecision(User $user, LoanApplication $loanApplication): Response|bool
    {
        // Admin override handled by before() method.

        $actionableStatuses = [
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
        ];
        if (!in_array($loanApplication->status, $actionableStatuses)) {
            return Response::deny(__('Permohonan ini tidak dalam status yang boleh diproses untuk kelulusan/penolakan.'));
        }

        // Determine current stage key based on application status if not directly set on application
        $currentStageKey = $loanApplication->current_approval_stage;
        if (!$currentStageKey) {
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
            // Stage-specific grade checks
            if ($currentStageKey === Approval::STAGE_LOAN_SUPPORT_REVIEW) {
                $minSupportGradeLevel = (int) config('motac.approval.min_loan_support_grade_level', 41);
                if (!$user->grade || (int) $user->grade->level < $minSupportGradeLevel) {
                    return Response::deny(__('Gred anda (:userGrade) tidak memenuhi syarat minima (Gred :minGrade) untuk menyokong permohonan ini.', ['userGrade' => $user->grade?->name ?? 'N/A', 'minGrade' => $minSupportGradeLevel]));
                }
            } elseif ($currentStageKey === Approval::STAGE_LOAN_APPROVER_REVIEW) {
                // Use the new config key for general approver stage
                $minGeneralApproverGradeLevel = (int) config('motac.approval.min_loan_general_approver_grade_level', config('motac.approval.min_loan_support_grade_level', 41)); // Fallback to support grade if new key not set
                if (!$user->grade || (int) $user->grade->level < $minGeneralApproverGradeLevel) {
                    return Response::deny(__('Gred anda (:userGrade) tidak memenuhi syarat minima (Gred :minGrade) untuk peringkat kelulusan ini.', ['userGrade' => $user->grade?->name ?? 'N/A', 'minGrade' => $minGeneralApproverGradeLevel]));
                }
            }
            // Add other stage-specific role/grade checks here if necessary for STAGE_LOAN_BPM_REVIEW etc.

            return Response::allow();
        }

        return Response::deny(__('Anda tidak ditetapkan sebagai pegawai pelulus untuk permohonan ini pada peringkat semasa atau tiada tugasan kelulusan aktif untuk anda.'));
    }

    public function approve(User $user, LoanApplication $loanApplication): Response|bool
    {
        // This is a more general check. `recordDecision` is more specific for the actual action.
        // This might be used for UI elements like "Can this user generally approve applications?"
        $isPending = in_array($loanApplication->status, [
            LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW,
        ]);

        return ($user->hasRole(['Approver', 'HOD', 'BPM Staff'])) && $isPending
            ? Response::allow()
            : Response::deny(__('Permohonan ini tidak dalam status menunggu kelulusan atau anda tiada kebenaran umum untuk meluluskan.'));
    }

    public function processIssuance(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') &&
            in_array($loanApplication->status, [
                LoanApplication::STATUS_APPROVED,
                LoanApplication::STATUS_PARTIALLY_ISSUED,
            ]);

        return $canProcess
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengeluarkan peralatan bagi permohonan ini atau statusnya tidak membenarkan (mesti Diluluskan atau Sebahagian Dikeluarkan).'));
    }

    public function processReturn(User $user, LoanApplication $loanApplication): Response|bool
    {
        $canProcess = $user->hasRole('BPM Staff') &&
            in_array($loanApplication->status, [
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
