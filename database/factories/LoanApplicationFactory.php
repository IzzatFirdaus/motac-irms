<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // For logging potential issues

class LoanApplicationFactory extends EloquentFactory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        $applicantUser = User::inRandomOrder()->first() ?? User::factory()->create();
        $responsibleOfficerUser = $this->faker->boolean(70)
            ? $applicantUser
            : (User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first() ?? User::factory()->create());

        $applicationDate = Carbon::instance($this->faker->dateTimeBetween('-3 months', '-1 week'));
        $loanStartDate = Carbon::instance($this->faker->dateTimeBetween($applicationDate->copy()->addDays(1), $applicationDate->copy()->addDays(15)));
        $loanEndDate = Carbon::instance($this->faker->dateTimeBetween($loanStartDate->copy()->addDays(1), $loanStartDate->copy()->addDays(60)));

        $allStatuses = [];
        if (method_exists(LoanApplication::class, 'getStatusKeys')) {
            $allStatuses = LoanApplication::getStatusKeys();
        } elseif (isset(LoanApplication::$STATUS_OPTIONS)) {
            $allStatuses = array_keys(LoanApplication::$STATUS_OPTIONS);
        } else {
            Log::warning('LoanApplicationFactory: Status keys not found on LoanApplication model. Using minimal fallback.');
            $allStatuses = ['draft', 'pending_support'];
        }

        $defaultStatus = defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft';
        $selectableStatuses = array_filter($allStatuses, fn ($statusKey) => $statusKey !== $defaultStatus);
        $chosenStatusKey = !empty($selectableStatuses)
            ? $this->faker->randomElement($selectableStatuses)
            : (defined(LoanApplication::class.'::STATUS_PENDING_SUPPORT') ? LoanApplication::STATUS_PENDING_SUPPORT : $defaultStatus);

        $timestamps = $this->generateTimestampsForStatus($chosenStatusKey, $applicationDate, $loanStartDate, $loanEndDate);

        return [
            'user_id' => $applicantUser->id,
            'responsible_officer_id' => $responsibleOfficerUser->id,
            'supporting_officer_id' => $this->faker->boolean(50) ? (User::whereNotIn('id', [$applicantUser->id, $responsibleOfficerUser->id])->inRandomOrder()->first()?->id) : null,

            // Revised purpose generation to remove dependency on 'catchPhrase' and 'bs' formatters
            'purpose' => $this->faker->sentence(mt_rand(6, 15)),
            'location' => $this->faker->city . ', ' . $this->faker->streetName,
            'return_location' => $this->faker->optional(0.3)->city . ', ' . $this->faker->streetName,
            'loan_start_date' => $loanStartDate->format('Y-m-d H:i:s'),
            'loan_end_date' => $loanEndDate->format('Y-m-d H:i:s'),
            'status' => $chosenStatusKey,
            'rejection_reason' => $chosenStatusKey === (defined(LoanApplication::class.'::STATUS_REJECTED') ? LoanApplication::STATUS_REJECTED : 'rejected') ? $this->faker->sentence() : null,
            'applicant_confirmation_timestamp' => $timestamps['applicant_confirmation_timestamp'],
            'admin_notes' => $this->faker->optional(0.1)->paragraph,

            'submitted_at' => $timestamps['submitted_at'],
            'approved_at' => $timestamps['approved_at'],
            'approved_by' => $timestamps['approved_by'],
            'rejected_at' => $timestamps['rejected_at'],
            'rejected_by' => $timestamps['rejected_by'],
            'cancelled_at' => $timestamps['cancelled_at'],
            'cancelled_by' => $timestamps['cancelled_by'],

            'created_at' => $applicationDate,
            'updated_at' => $applicationDate->copy()->addMinutes($this->faker->numberBetween(10, 1000)),
            'deleted_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (LoanApplication $loanApplication) {
            if ($loanApplication->status !== (defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft') && $loanApplication->applicationItems()->count() === 0) {
                LoanApplicationItem::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->for($loanApplication)
                    ->create();
            }

            $pendingApprovalStatuses = [
                defined(LoanApplication::class.'::STATUS_PENDING_SUPPORT') ? LoanApplication::STATUS_PENDING_SUPPORT : 'pending_support',
                defined(LoanApplication::class.'::STATUS_PENDING_HOD_REVIEW') ? LoanApplication::STATUS_PENDING_HOD_REVIEW : 'pending_hod_review',
                defined(LoanApplication::class.'::STATUS_PENDING_BPM_REVIEW') ? LoanApplication::STATUS_PENDING_BPM_REVIEW : 'pending_bpm_review',
            ];

            if (in_array($loanApplication->status, $pendingApprovalStatuses) && $loanApplication->approvals()->count() === 0 && class_exists(Approval::class)) {
                $currentApprovalStage = null;
                if ($loanApplication->status === (defined(LoanApplication::class.'::STATUS_PENDING_SUPPORT') ? LoanApplication::STATUS_PENDING_SUPPORT : 'pending_support')) {
                    $currentApprovalStage = defined(Approval::class.'::STAGE_LOAN_SUPPORT_REVIEW') ? Approval::STAGE_LOAN_SUPPORT_REVIEW : 'loan_support_review';
                } elseif ($loanApplication->status === (defined(LoanApplication::class.'::STATUS_PENDING_HOD_REVIEW') ? LoanApplication::STATUS_PENDING_HOD_REVIEW : 'pending_hod_review')) {
                    $currentApprovalStage = defined(Approval::class.'::STAGE_LOAN_HOD_REVIEW') ? Approval::STAGE_LOAN_HOD_REVIEW : 'loan_hod_review';
                } elseif ($loanApplication->status === (defined(LoanApplication::class.'::STATUS_PENDING_BPM_REVIEW') ? LoanApplication::STATUS_PENDING_BPM_REVIEW : 'pending_bpm_review')) {
                    $currentApprovalStage = defined(Approval::class.'::STAGE_LOAN_BPM_REVIEW') ? Approval::STAGE_LOAN_BPM_REVIEW : 'loan_bpm_review';
                }

                if ($currentApprovalStage) {
                    $officerForApproval = $loanApplication->supporting_officer_id ? User::find($loanApplication->supporting_officer_id) : null;

                    if (!$officerForApproval) {
                        $roleForStage = null;
                        if ($currentApprovalStage === (defined(Approval::class.'::STAGE_LOAN_SUPPORT_REVIEW') ? Approval::STAGE_LOAN_SUPPORT_REVIEW : 'loan_support_review')) {
                             $roleForStage = 'Approver';
                        } elseif ($currentApprovalStage === (defined(Approval::class.'::STAGE_LOAN_HOD_REVIEW') ? Approval::STAGE_LOAN_HOD_REVIEW : 'loan_hod_review')) {
                            $roleForStage = 'HOD';
                        } elseif ($currentApprovalStage === (defined(Approval::class.'::STAGE_LOAN_BPM_REVIEW') ? Approval::STAGE_LOAN_BPM_REVIEW : 'loan_bpm_review')) {
                            $roleForStage = 'BPM Staff';
                        }

                        if ($roleForStage) {
                             $officerForApproval = User::role($roleForStage)->inRandomOrder()->first();
                        }
                        $officerForApproval = $officerForApproval ?? User::inRandomOrder()->first() ?? User::factory()->create();
                    }

                    Approval::factory()
                        ->for($loanApplication, 'approvable')
                        ->status(defined(Approval::class.'::STATUS_PENDING') ? Approval::STATUS_PENDING : 'pending')
                        ->stage($currentApprovalStage)
                        ->create([
                            'officer_id' => $officerForApproval->id,
                        ]);
                }
            }
        });
    }

    // State methods
    public function draft(): static { return $this->state(['status' => defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft']); }
    public function pendingSupport(): static {
        return $this->state([
            'status' => defined(LoanApplication::class.'::STATUS_PENDING_SUPPORT') ? LoanApplication::STATUS_PENDING_SUPPORT : 'pending_support',
            'applicant_confirmation_timestamp' => now()->subMinutes(15),
            'submitted_at' => now()->subMinutes(10)
        ]);
    }
    public function pendingHodReview(): static { return $this->pendingSupport()->state(['status' => defined(LoanApplication::class.'::STATUS_PENDING_HOD_REVIEW') ? LoanApplication::STATUS_PENDING_HOD_REVIEW : 'pending_hod_review']); }
    public function pendingBpmReview(): static { return $this->pendingHodReview()->state(['status' => defined(LoanApplication::class.'::STATUS_PENDING_BPM_REVIEW') ? LoanApplication::STATUS_PENDING_BPM_REVIEW : 'pending_bpm_review']); }
    public function approved(): static {
        return $this->pendingBpmReview()->state([
            'status' => defined(LoanApplication::class.'::STATUS_APPROVED') ? LoanApplication::STATUS_APPROVED : 'approved',
            'approved_at' => now(),
            'approved_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id
        ]);
    }
    public function rejected(): static {
        return $this->pendingSupport()->state([
            'status' => defined(LoanApplication::class.'::STATUS_REJECTED') ? LoanApplication::STATUS_REJECTED : 'rejected',
            'rejection_reason' => $this->faker->sentence,
            'rejected_at' => now(),
            'rejected_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id
        ]);
    }
    public function issued(): static { return $this->approved()->state(['status' => defined(LoanApplication::class.'::STATUS_ISSUED') ? LoanApplication::STATUS_ISSUED : 'issued']); }
    public function partiallyIssued(): static { return $this->approved()->state(['status' => defined(LoanApplication::class.'::STATUS_PARTIALLY_ISSUED') ? LoanApplication::STATUS_PARTIALLY_ISSUED : 'partially_issued']); }
    public function returned(): static { return $this->issued()->state(['status' => defined(LoanApplication::class.'::STATUS_RETURNED') ? LoanApplication::STATUS_RETURNED : 'returned']); }
    public function overdue(): static {
        return $this->issued()->state([
            'status' => defined(LoanApplication::class.'::STATUS_OVERDUE') ? LoanApplication::STATUS_OVERDUE : 'overdue',
            'loan_end_date' => now()->subDays(3)
        ]);
    }
    public function cancelled(): static {
        return $this->state([
            'status' => defined(LoanApplication::class.'::STATUS_CANCELLED') ? LoanApplication::STATUS_CANCELLED : 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id
        ]);
    }
    public function certified(): static { return $this->state(['applicant_confirmation_timestamp' => now()->subMinutes(10)]); }
    public function withItems(int $count = 2): static { return $this->has(LoanApplicationItem::factory()->count($count), 'applicationItems'); }
    public function deleted(): static { return $this->state(['deleted_at' => now()]); }

    private function generateTimestampsForStatus(string $status, Carbon $applicationDate, Carbon $loanStartDate, Carbon $loanEndDate): array
    {
        $timestamps = [
            'applicant_confirmation_timestamp' => null, 'submitted_at' => null,
            'approved_at' => null, 'approved_by' => null,
            'rejected_at' => null, 'rejected_by' => null,
            'cancelled_at' => null, 'cancelled_by' => null,
        ];

        $statusDraft = defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft';
        $statusApproved = defined(LoanApplication::class.'::STATUS_APPROVED') ? LoanApplication::STATUS_APPROVED : 'approved';
        $statusIssued = defined(LoanApplication::class.'::STATUS_ISSUED') ? LoanApplication::STATUS_ISSUED : 'issued';
        $statusPartiallyIssued = defined(LoanApplication::class.'::STATUS_PARTIALLY_ISSUED') ? LoanApplication::STATUS_PARTIALLY_ISSUED : 'partially_issued';
        $statusReturned = defined(LoanApplication::class.'::STATUS_RETURNED') ? LoanApplication::STATUS_RETURNED : 'returned';
        $statusOverdue = defined(LoanApplication::class.'::STATUS_OVERDUE') ? LoanApplication::STATUS_OVERDUE : 'overdue';
        $statusRejected = defined(LoanApplication::class.'::STATUS_REJECTED') ? LoanApplication::STATUS_REJECTED : 'rejected';
        $statusCancelled = defined(LoanApplication::class.'::STATUS_CANCELLED') ? LoanApplication::STATUS_CANCELLED : 'cancelled';

        if (!in_array($status, [$statusDraft])) {
            $timestamps['submitted_at'] = $applicationDate->copy()->addMinutes($this->faker->numberBetween(1, 60));
            $timestamps['applicant_confirmation_timestamp'] = $timestamps['submitted_at'];
        }

        $lastEventDate = $timestamps['submitted_at'] ? Carbon::parse($timestamps['submitted_at']) : $applicationDate;

        if ($status === $statusApproved ||
            in_array($status, [$statusIssued, $statusPartiallyIssued, $statusReturned, $statusOverdue])) {
            $timestamps['approved_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['approved_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === $statusRejected) {
            $timestamps['rejected_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['rejected_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === $statusCancelled) {
            $timestamps['cancelled_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(0, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['cancelled_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }
        return $timestamps;
    }
}
