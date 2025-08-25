<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Optimized ApprovalSeeder for batch creation.
 * - Batches inserts for better performance.
 * - Minimizes user and application queries.
 * - Only creates approvals if there are loan applications and eligible officers.
 * - Uses model constants and avoids per-record Eloquent creation where possible.
 */
class ApprovalSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Approval seeding (Optimized)...');

        // Find or create a user to use for audit columns (created_by/updated_by).
        $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Audit User Fallback (ApprovalSeeder)']);

        // Get all eligible officers (by role) once, as a flat array of IDs.
        $officerIds = User::whereHas('roles', function ($q) {
            $q->whereIn('name', [
                'Admin', 'BPM Staff', 'IT Admin', 'HOD', 'Approver'
            ]);
        })->pluck('id')->all();

        if (empty($officerIds)) {
            Log::warning('No potential approval officers found. Using fallback audit user.');
            $officerIds[] = $auditUser->id;
        }

        // Fetch a selection of loan applications to attach approvals to.
        $loanApplications = LoanApplication::inRandomOrder()->limit(20)->get();
        if ($loanApplications->isEmpty()) {
            Log::warning('No loan applications found to seed approvals for. Skipping.');
            return;
        }

        $now = Carbon::now();
        $batch = [];

        // Helper: returns a full approval array with all columns, filling unused with null
        $approvalArray = function ($overrides = []) use ($auditUser, $now) {
            return array_merge([
                'approvable_type' => $overrides['approvable_type'] ?? null,
                'approvable_id' => $overrides['approvable_id'] ?? null,
                'officer_id' => $overrides['officer_id'] ?? null,
                'stage' => $overrides['stage'] ?? null,
                'status' => $overrides['status'] ?? null,
                'notes' => $overrides['notes'] ?? null,
                'approved_at' => $overrides['approved_at'] ?? null,
                'rejected_at' => $overrides['rejected_at'] ?? null,
                'canceled_at' => $overrides['canceled_at'] ?? null,
                'resubmitted_at' => $overrides['resubmitted_at'] ?? null,
                'created_by' => $auditUser->id,
                'updated_by' => $auditUser->id,
                'deleted_by' => $overrides['deleted_by'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => $overrides['deleted_at'] ?? null,
            ], $overrides);
        };

        // --- 1. Pending approvals (support_review) ---
        foreach ($loanApplications as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_SUPPORT_REVIEW,
                'status' => Approval::STATUS_PENDING,
                'notes' => 'Menunggu semakan sokongan.',
            ]);
        }

        // --- 2. Approved & Rejected at final_approval ---
        $approvedApps = $loanApplications->shuffle()->take(8);
        foreach ($approvedApps as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_FINAL_APPROVAL,
                'status' => Approval::STATUS_APPROVED,
                'approved_at' => $now->copy()->addMinutes(rand(1, 30)),
                'notes' => 'Diluluskan di peringkat akhir.',
            ]);
        }

        $rejectedApps = $loanApplications->shuffle()->take(5);
        foreach ($rejectedApps as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_FINAL_APPROVAL,
                'status' => Approval::STATUS_REJECTED,
                'rejected_at' => $now->copy()->addMinutes(rand(1, 30)),
                'notes' => 'Ditolak di peringkat akhir.',
            ]);
        }

        // --- 3. Canceled and Forwarded (workflow variety) ---
        foreach ($loanApplications->shuffle()->take(2) as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_GENERAL_REVIEW,
                'status' => Approval::STATUS_CANCELED,
                'canceled_at' => $now->copy()->addMinutes(rand(1, 30)),
                'notes' => 'Dibatalkan oleh pemohon.',
            ]);
        }
        foreach ($loanApplications->shuffle()->take(2) as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_LOAN_SUPPORT_REVIEW,
                'status' => Approval::STATUS_FORWARDED,
                'resubmitted_at' => $now->copy()->addMinutes(rand(1, 30)),
                'notes' => 'Permohonan dimajukan kepada pegawai lain.',
            ]);
        }

        // --- 4. Soft deleted approvals for variety ---
        foreach ($loanApplications->shuffle()->take(3) as $application) {
            $batch[] = $approvalArray([
                'approvable_type' => get_class($application),
                'approvable_id' => $application->id,
                'officer_id' => $officerIds[array_rand($officerIds)],
                'stage' => Approval::STAGE_GENERAL_REVIEW,
                'status' => Approval::STATUS_PENDING,
                'notes' => 'Dihapus untuk ujian soft delete.',
                'deleted_by' => $auditUser->id,
                'deleted_at' => $now->copy()->addMinutes(rand(31, 60)),
            ]);
        }

        // --- Perform bulk insert in chunks for memory/DB efficiency ---
        $chunkSize = 100;
        foreach (array_chunk($batch, $chunkSize) as $chunk) {
            Approval::insert($chunk);
        }

        Log::info('ApprovalSeeder: Inserted ' . count($batch) . ' approval tasks in batch.');
    }
}
