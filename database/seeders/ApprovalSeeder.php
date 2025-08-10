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

/**
 * Seeds the approvals table for the system.
 * Ensures a mix of pending, approved, rejected, and soft-deleted approval tasks for LoanApplications.
 * Uses the extended approval workflow (status, notes, decision timestamps) as per updated schema/model.
 */
class ApprovalSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Approval seeding (Revised Officer Logic)...');

        // Find or create a user to use for audit columns (created_by/updated_by).
        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Audit User Fallback (ApprovalSeeder)']);

        // Get IDs of officers who are eligible to act as approvers (by role).
        $officerIds = $this->getPotentialOfficerIds($auditUser->id);
        if ($officerIds->isEmpty()) {
            Log::warning('No potential approval officers found based on roles. Approval seeding will use a fallback officer.');
            $fallbackOfficer = User::factory()->create(['name' => 'Fallback Approval Officer (ApprovalSeeder)']);
            $officerIds = new EloquentCollection([$fallbackOfficer->id]);
        }

        // Fetch a selection of loan applications to attach approvals to.
        $loanApplications = LoanApplication::query()->inRandomOrder()->limit(10)->get();
        if ($loanApplications->isEmpty() && LoanApplication::count() > 0) {
            $loanApplications = LoanApplication::inRandomOrder()->limit(10)->get();
        }

        if ($loanApplications->isEmpty()) {
            Log::warning('No loan applications found to seed approvals for. Skipping approval seeding for Loan Applications.');
            return;
        }

        // Seed pending approvals for each application (at support stage).
        foreach ($loanApplications as $application) {
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->pending()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_SUPPORT_REVIEW)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
            }
        }
        Log::info(sprintf('Created %d pending loan application approvals.', $loanApplications->count()));

        // Seed approvals at final approval stage (some approved, some rejected)
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

        // Optionally, seed forwarded or canceled approvals if desired for workflow variety:
        // Forwarded
        foreach ($loanApplications->shuffle()->take(2) as $application) {
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->status(Approval::STATUS_FORWARDED)
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_LOAN_SUPPORT_REVIEW)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
            }
        }
        // Canceled
        foreach ($loanApplications->shuffle()->take(2) as $application) {
            if ($application instanceof \Illuminate\Database\Eloquent\Model) {
                Approval::factory()
                    ->status(Approval::STATUS_CANCELED)
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_GENERAL_REVIEW)
                    ->create([
                        'officer_id' => $officerIds->random(),
                        'created_by' => $auditUser->id,
                        'updated_by' => $auditUser->id,
                    ]);
            }
        }

        Log::info('Approval seeding complete.');
    }

    /**
     * Helper method to get User IDs for officers based on roles.
     * Filters out the given user ID from results (so we don't assign the audit user).
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
