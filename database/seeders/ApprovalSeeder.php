<?php

// File: database/seeders/ApprovalSeeder.php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\EmailApplication;
// Removed: use App\Models\Grade; // Not directly used in getOfficerIds anymore
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder; // Keep for type hinting if complex queries remain
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role; // Import Role model

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

        $emailApplications = EmailApplication::query()->inRandomOrder()->limit(10)->get();
        if ($emailApplications->isEmpty() && EmailApplication::count() > 0) {
            $emailApplications = EmailApplication::take(5)->get();
        } elseif (EmailApplication::count() === 0) {
            Log::info('No EmailApplications found. Creating some for ApprovalSeeder.');
            EmailApplication::factory(5)->create([
                'user_id' => $auditUser->id,
                'supporting_officer_id' => $officerIds->isNotEmpty() ? $officerIds->random() : null
            ]);
            $emailApplications = EmailApplication::all();
        }

        $loanApplications = LoanApplication::query()->inRandomOrder()->limit(15)->get();
        if ($loanApplications->isEmpty() && LoanApplication::count() > 0) {
            $loanApplications = LoanApplication::take(5)->get();
        } elseif (LoanApplication::count() === 0) {
            Log::info('No LoanApplications found. Creating some for ApprovalSeeder.');
            LoanApplication::factory(5)->create([
                'user_id' => $auditUser->id,
                'responsible_officer_id' => $officerIds->isNotEmpty() ? $officerIds->random() : null
            ]);
            $loanApplications = LoanApplication::all();
        }

        if ($emailApplications->isEmpty() && $loanApplications->isEmpty()) {
            Log::warning('Still no Email or Loan Applications found. Skipping Approval seeding for applications.');
        }

        $approvalStatuses = [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED, Approval::STATUS_PENDING];

        Log::info('Seeding Approvals for Email Applications...');
        foreach ($emailApplications as $application) {
            if ($officerIds->isEmpty()) continue; // Skip if no officers
            $officerId = $officerIds->random();
            $chosenStatus = Arr::random($approvalStatuses);

            Approval::factory()
                ->forApprovable($application)
                ->stage(Approval::STAGE_EMAIL_SUPPORT_REVIEW) // Changed from generic STAGE_SUPPORT_REVIEW
                ->status($chosenStatus)
                ->create([
                    'officer_id' => $officerId,
                    // created_by, updated_by handled by BlameableObserver or factory
                ]);

            $firstApproval = $application->approvals()->where('stage', Approval::STAGE_EMAIL_SUPPORT_REVIEW)->latest()->first();
            if ($firstApproval && $firstApproval->status === Approval::STATUS_APPROVED) {
                if ($officerIds->isEmpty()) continue;
                $nextOfficerId = $officerIds->random();
                $nextChosenStatus = Arr::random($approvalStatuses);
                Approval::factory()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_EMAIL_ADMIN_REVIEW)
                    ->status($nextChosenStatus)
                    ->create([
                        'officer_id' => $nextOfficerId,
                        // created_by, updated_by handled by BlameableObserver or factory
                    ]);
            }
        }
        Log::info('Seeded Approvals for Email Applications.');

        Log::info('Seeding Approvals for Loan Applications...');
        foreach ($loanApplications as $application) {
            if ($officerIds->isEmpty()) continue;
            $officerId = $officerIds->random();
            $chosenStatus = Arr::random($approvalStatuses);

            Approval::factory()
                ->forApprovable($application)
                ->stage(Approval::STAGE_LOAN_SUPPORT_REVIEW) // Changed from generic STAGE_SUPPORT_REVIEW
                ->status($chosenStatus)
                ->create([
                    'officer_id' => $officerId,
                    // created_by, updated_by handled by BlameableObserver or factory
                ]);

            $firstApproval = $application->approvals()->where('stage', Approval::STAGE_LOAN_SUPPORT_REVIEW)->latest()->first();
            if ($firstApproval && $firstApproval->status === Approval::STATUS_APPROVED) {
                if ($officerIds->isEmpty()) continue;
                $nextOfficerId = $officerIds->random();
                $nextChosenStatus = Arr::random($approvalStatuses);
                Approval::factory()
                    ->forApprovable($application)
                    ->stage(Approval::STAGE_LOAN_HOD_REVIEW)
                    ->status($nextChosenStatus)
                    ->create([
                        'officer_id' => $nextOfficerId,
                        // created_by, updated_by handled by BlameableObserver or factory
                    ]);
                // Optionally add BPM review stage if HOD approved
                $hodApproval = $application->approvals()->where('stage', Approval::STAGE_LOAN_HOD_REVIEW)->latest()->first();
                if ($hodApproval && $hodApproval->status === Approval::STATUS_APPROVED) {
                    if ($officerIds->isEmpty()) continue;
                    $bpmOfficerId = $officerIds->random();
                    $bpmChosenStatus = Arr::random($approvalStatuses);
                    Approval::factory()
                        ->forApprovable($application)
                        ->stage(Approval::STAGE_LOAN_BPM_REVIEW)
                        ->status($bpmChosenStatus)
                        ->create([
                            'officer_id' => $bpmOfficerId,
                        ]);
                }
            }
        }
        Log::info('Seeded Approvals for Loan Applications.');

        if ($officerIds->isNotEmpty() && ($emailApplications->isNotEmpty() || $loanApplications->isNotEmpty())) {
            $randomApprovable = $emailApplications->isNotEmpty() ? $emailApplications->random() : $loanApplications->random();
            Approval::factory()
                ->count(3)
                ->forApprovable($randomApprovable)
                ->stage(Approval::STAGE_GENERAL_REVIEW) // Using a more generic stage
                ->deleted() // This state in ApprovalFactory should handle deleted_at and ideally deleted_by
                ->create([
                    'officer_id' => $officerIds->random(),
                    // created_by, updated_by handled by BlameableObserver or factory
                ]);
            Log::info('Created 3 soft-deleted approvals.');
        }

        Log::info('Approval seeding complete.');
    }

    /**
     * Helper method to get User IDs for officers based on roles.
     */
    protected function getPotentialOfficerIds(?int $excludeUserId = null): EloquentCollection
    {
        // Define roles that typically handle approvals
        $approverRoleNames = ['Admin', 'BPM Staff', 'IT Admin', 'HOD', 'Approver']; // Adjust as per your RoleAndPermissionSeeder

        $query = User::query()->whereHas('roles', function (Builder $q) use ($approverRoleNames) {
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
