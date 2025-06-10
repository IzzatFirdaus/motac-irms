<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class LoanApplicationFactory extends EloquentFactory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $applicantUser = User::inRandomOrder()->first() ?? User::factory()->create();
        $responsibleOfficerUser = $this->faker->boolean(70)
            ? $applicantUser
            : (User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first() ?? User::factory()->create());

        $applicationDate = Carbon::instance($this->faker->dateTimeBetween('-3 months', '-1 week'));
        $loanStartDate = Carbon::instance($this->faker->dateTimeBetween($applicationDate->copy()->addDays(1), $applicationDate->copy()->addDays(15)));
        $loanEndDate = Carbon::instance($this->faker->dateTimeBetween($loanStartDate->copy()->addDays(1), $loanStartDate->copy()->addDays(60)));

        $allStatuses = method_exists(LoanApplication::class, 'getStatusKeys') ? LoanApplication::getStatusKeys() : array_keys(LoanApplication::$STATUS_OPTIONS ?? [LoanApplication::STATUS_DRAFT => 'Draft']);
        $defaultStatus = defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft';
        $selectableStatuses = array_filter($allStatuses, fn ($statusKey) => $statusKey !== $defaultStatus);
        $chosenStatusKey = !empty($selectableStatuses)
            ? $this->faker->randomElement($selectableStatuses)
            : (defined(LoanApplication::class.'::STATUS_PENDING_SUPPORT') ? LoanApplication::STATUS_PENDING_SUPPORT : $defaultStatus);

        $timestamps = $this->generateTimestampsForStatus($chosenStatusKey, $applicationDate);

        return [
            'user_id' => $applicantUser->id,
            'responsible_officer_id' => $responsibleOfficerUser->id,
            'supporting_officer_id' => $this->faker->boolean(50) ? (User::whereNotIn('id', [$applicantUser->id, $responsibleOfficerUser->id])->inRandomOrder()->first()?->id) : null,
            'purpose' => $msFaker->sentence(mt_rand(6, 15)),
            'location' => $msFaker->city.', '.$msFaker->streetName,
            'return_location' => $this->faker->optional(0.3)->passthrough($msFaker->city.', '.$msFaker->streetName),
            'loan_start_date' => $loanStartDate->format('Y-m-d H:i:s'),
            'loan_end_date' => $loanEndDate->format('Y-m-d H:i:s'),
            'status' => $chosenStatusKey,
            'rejection_reason' => $chosenStatusKey === (defined(LoanApplication::class.'::STATUS_REJECTED') ? LoanApplication::STATUS_REJECTED : 'rejected') ? $msFaker->sentence() : null,
            'applicant_confirmation_timestamp' => $timestamps['applicant_confirmation_timestamp'],
            'admin_notes' => $msFaker->optional(0.1)->paragraph,
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
            // Corrected relationship name
            if ($loanApplication->status !== (defined(LoanApplication::class.'::STATUS_DRAFT') ? LoanApplication::STATUS_DRAFT : 'draft') && $loanApplication->loanApplicationItems()->count() === 0) {
                LoanApplicationItem::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->for($loanApplication)
                    ->create();
            }

            $pendingApprovalStatuses = [
                LoanApplication::STATUS_PENDING_SUPPORT,
                LoanApplication::STATUS_PENDING_APPROVER_REVIEW, // CORRECTED
                LoanApplication::STATUS_PENDING_BPM_REVIEW,
            ];

            if (in_array($loanApplication->status, $pendingApprovalStatuses) && $loanApplication->approvals()->count() === 0 && class_exists(Approval::class)) {
                $currentApprovalStage = null;
                if ($loanApplication->status === LoanApplication::STATUS_PENDING_SUPPORT) {
                    $currentApprovalStage = Approval::STAGE_LOAN_SUPPORT_REVIEW;
                } elseif ($loanApplication->status === LoanApplication::STATUS_PENDING_APPROVER_REVIEW) { // CORRECTED
                    $currentApprovalStage = Approval::STAGE_LOAN_APPROVER_REVIEW; // CORRECTED
                } elseif ($loanApplication->status === LoanApplication::STATUS_PENDING_BPM_REVIEW) {
                    $currentApprovalStage = Approval::STAGE_LOAN_BPM_REVIEW;
                }

                if ($currentApprovalStage) {
                    $officerForApproval = $loanApplication->supporting_officer_id ? User::find($loanApplication->supporting_officer_id) : null;

                    if (! $officerForApproval) {
                        $roleForStage = null;
                        if ($currentApprovalStage === Approval::STAGE_LOAN_SUPPORT_REVIEW) {
                             $roleForStage = 'Approver';
                        } elseif ($currentApprovalStage === Approval::STAGE_LOAN_APPROVER_REVIEW) { // CORRECTED
                            $roleForStage = 'HOD';
                        } elseif ($currentApprovalStage === Approval::STAGE_LOAN_BPM_REVIEW) {
                            $roleForStage = 'BPM Staff';
                        }

                        if ($roleForStage) {
                             $officerForApproval = User::role($roleForStage)->where('id', '!=', $loanApplication->user_id)->inRandomOrder()->first();
                        }
                        $officerForApproval = $officerForApproval ?? User::where('id', '!=', $loanApplication->user_id)->inRandomOrder()->first() ?? User::factory()->create();
                    }

                    Approval::factory()
                        ->for($loanApplication, 'approvable')
                        ->status(Approval::STATUS_PENDING)
                        ->stage($currentApprovalStage)
                        ->create(['officer_id' => $officerForApproval->id]);
                }
            }
        });
    }

    // --- State Methods ---
    public function draft(): static { return $this->state(['status' => LoanApplication::STATUS_DRAFT]); }
    public function pendingSupport(): static {
        return $this->state([
            'status' => LoanApplication::STATUS_PENDING_SUPPORT,
            'applicant_confirmation_timestamp' => now()->subMinutes(15),
            'submitted_at' => now()->subMinutes(10),
        ]);
    }
    // CORRECTED: Renamed method and updated status constant
    public function pendingApproverReview(): static { return $this->pendingSupport()->state(['status' => LoanApplication::STATUS_PENDING_APPROVER_REVIEW]); }
    public function pendingBpmReview(): static { return $this->pendingApproverReview()->state(['status' => LoanApplication::STATUS_PENDING_BPM_REVIEW]); }
    public function approved(): static {
        return $this->pendingBpmReview()->state([
            'status' => LoanApplication::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
        ]);
    }
    public function rejected(): static {
        return $this->pendingSupport()->state([
            'status' => LoanApplication::STATUS_REJECTED,
            'rejection_reason' => $this->faker->sentence,
            'rejected_at' => now(),
            'rejected_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
        ]);
    }
    public function issued(): static { return $this->approved()->state(['status' => LoanApplication::STATUS_ISSUED]); }
    public function partiallyIssued(): static { return $this->approved()->state(['status' => LoanApplication::STATUS_PARTIALLY_ISSUED]); }
    public function returned(): static { return $this->issued()->state(['status' => LoanApplication::STATUS_RETURNED]); }
    public function overdue(): static {
        return $this->issued()->state([
            'status' => LoanApplication::STATUS_OVERDUE,
            'loan_end_date' => now()->subDays(3),
        ]);
    }
    public function cancelled(): static {
        return $this->state([
            'status' => LoanApplication::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
        ]);
    }
    public function certified(): static { return $this->state(['applicant_confirmation_timestamp' => now()->subMinutes(10)]); }
    // Corrected relationship name to 'loanApplicationItems'
    public function withItems(int $count = 2): static { return $this->has(LoanApplicationItem::factory()->count($count), 'loanApplicationItems'); }
    public function deleted(): static { return $this->state(['deleted_at' => now()]); }

    private function generateTimestampsForStatus(string $status, Carbon $applicationDate): array
    {
        $timestamps = [
            'applicant_confirmation_timestamp' => null, 'submitted_at' => null,
            'approved_at' => null, 'approved_by' => null,
            'rejected_at' => null, 'rejected_by' => null,
            'cancelled_at' => null, 'cancelled_by' => null,
        ];

        if (!in_array($status, [LoanApplication::STATUS_DRAFT])) {
            $timestamps['submitted_at'] = $applicationDate->copy()->addMinutes($this->faker->numberBetween(1, 60));
            $timestamps['applicant_confirmation_timestamp'] = $timestamps['submitted_at'];
        }

        $lastEventDate = $timestamps['submitted_at'] ? Carbon::parse($timestamps['submitted_at']) : $applicationDate;

        $approvedStatuses = [
            LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_PARTIALLY_ISSUED, LoanApplication::STATUS_RETURNED,
            LoanApplication::STATUS_OVERDUE,
        ];

        if (in_array($status, $approvedStatuses)) {
            $timestamps['approved_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['approved_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === LoanApplication::STATUS_REJECTED) {
            $timestamps['rejected_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['rejected_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === LoanApplication::STATUS_CANCELLED) {
            $timestamps['cancelled_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(0, 5))->addHours($this->faker->numberBetween(1,12));
            $timestamps['cancelled_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }
        return $timestamps;
    }
}
