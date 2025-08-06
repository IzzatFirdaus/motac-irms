<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class ApprovalSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Approval seeding (Revised Officer Logic)...');

        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Audit User Fallback (ApprovalSeeder)']);

        $officerIds = $this->getPotentialOfficerIds($auditUser->id);
        if ($officerIds->isEmpty()) {
            Log::warning('No potential approval officers found based on roles. Approval seeding will use a fallback officer.');
            $fallbackOfficer = User::factory()->create(['name' => 'Fallback Approval Officer (ApprovalSeeder)']);
            $officerIds = new EloquentCollection([$fallbackOfficer->id]);
        }

        $loanApplications = LoanApplication::query()->inRandomOrder()->limit(10)->get();
        if ($loanApplications->isEmpty() && LoanApplication::count() > 0) {
            $loanApplications = LoanApplication::inRandomOrder()->limit(10)->get();
        }

        // Ensure there are some applications to approve
        if ($loanApplications->isEmpty()) {
            Log::warning('No loan applications found to seed approvals for. Skipping approval seeding for Loan Applications.');
            return;
        }

        // Seed pending approvals
        foreach ($loanApplications as $application) {
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->pending()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_PENDING_HOD_REVIEW)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
            }
        }
        Log::info(sprintf('Created %d pending loan application approvals.', $loanApplications->count()));

        // Seed some approved/rejected approvals for existing applications
        $appsForApprovedRejected = $loanApplications->merge($loanApplications)->shuffle()->filter(function ($item) {
            return $item instanceof \Illuminate\Database\Eloquent\Model;
        })->values();
        $countApproved = 0;
        $countRejected = 0;

        foreach ($appsForApprovedRejected->take(8) as $application) { // Approve up to 8
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->approved()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_FINAL_APPROVAL)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
                $countApproved++;
            }
        }
        Log::info(sprintf('Created %d approved loan application approvals.', $countApproved));

        foreach ($appsForApprovedRejected->skip(8)->take(5) as $application) { // Reject up to 5
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->rejected()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_FINAL_APPROVAL)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
                $countRejected++;
            }
        }
        Log::info(sprintf('Created %d rejected loan application approvals.', $countRejected));

        // Seed some soft-deleted approvals if there are any approvables left
        if ($loanApplications->isNotEmpty()) {
            $randomApprovable = $loanApplications->random();
            if ($randomApprovable instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->count(3)
                    ->forApprovable($randomApprovable)
                    ->stage(Approval::STAGE_GENERAL_REVIEW)
                    ->deleted()
                    ->create([
                        'officer_id' => $officerIds->random(),
                    ]);
                Log::info('Created 3 soft-deleted approvals.');
            }
        }

        Log::info('Approval seeding complete.');
    }

    /**
     * Helper method to get User IDs for officers based on roles.
     */
    protected function getPotentialOfficerIds(?int $excludeUserId = null): EloquentCollection
    {
        $approverRoleNames = ['Admin', 'BPM Staff', 'IT Admin', 'HOD', 'Approver'];

        $query = User::query()->whereHas('roles', function (Builder $q) use ($approverRoleNames): void {
            $q->whereIn('name', $approverRoleNames);
        });

        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }

        $userIds = $query->pluck('id');

        if ($userIds->isEmpty()) {
            Log::warning('No specific officers found by roles, returning up to 5 random user IDs as fallback for seeder.');
            return User::inRandomOrder()->limit(5)->pluck('id');
        }

        return $userIds;
    }
}
