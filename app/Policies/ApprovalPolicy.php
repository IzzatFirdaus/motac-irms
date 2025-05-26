<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User; // User model is provided and uses HasRoles
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log; // Added from suggestion

class ApprovalPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): Response|bool
    {
        return $user->hasPermissionTo('view_all_approvals') || $user->hasAnyRole(['BPMStaff', 'IT Admin', 'SupportingOfficer', 'HOD'])
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat rekod kelulusan.'));
    }

    public function view(User $user, Approval $approval): Response|bool
    {
        if ($user->id === $approval->officer_id) {
            return Response::allow();
        }
        /** @var \App\Models\EmailApplication|\App\Models\LoanApplication $approvable */
        $approvable = $approval->approvable;
        if ($approvable && $approvable->user_id === $user->id) { // Check if the approvable item belongs to the user
            return Response::allow();
        }
        if ($user->hasPermissionTo('view_all_approvals') || $user->hasAnyRole(['BPMStaff', 'IT Admin'])) {
            return Response::allow();
        }
        return Response::deny(__('Anda tidak mempunyai kebenaran untuk melihat rekod kelulusan ini.'));
    }

    public function create(User $user): Response|bool
    {
        return $user->hasRole('Admin') && $user->hasPermissionTo('manage_approvals')
            ? Response::allow()
            : Response::deny(__('Rekod kelulusan dicipta secara automatik oleh sistem.'));
    }

    public function update(User $user, Approval $approval): Response|bool
    {
        return $user->id === $approval->officer_id &&
               $approval->status === Approval::STATUS_PENDING  &&
               ($user->hasPermissionTo('act_on_approval_tasks') || $user->hasAnyRole(['SupportingOfficer','HOD','BPMStaff','IT Admin']))
            ? Response::allow()
            : Response::deny(__('Anda tidak mempunyai kebenaran untuk mengambil tindakan ke atas kelulusan ini atau ia tidak lagi menunggu keputusan.'));
    }

    public function delete(User $user, Approval $approval): Response|bool
    {
        return $user->hasRole('Admin') && $user->hasPermissionTo('manage_approvals')
            ? Response::allow()
            : Response::deny(__('Rekod kelulusan tidak boleh dipadam.'));
    }
}
