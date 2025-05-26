<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\Auth; // Not directly used, fallback User::factory() is better for seeders

class LoanApplicationFactory extends EloquentFactory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        $applicantUser = User::inRandomOrder()->first() ?? User::factory()->create();
        $responsibleOfficerUser = $this->faker->boolean(70)
            ? $applicantUser
            : (User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first() ?? User::factory()->create());

        // $auditUserId = Auth::id() ?? (User::orderBy('id')->value('id') ?? User::factory()->create()->id); // Blameable

        $applicationDate = Carbon::instance($this->faker->dateTimeBetween('-3 months', '-1 week'));
        $loanStartDate = Carbon::instance($this->faker->dateTimeBetween($applicationDate->copy()->addDays(1), $applicationDate->copy()->addDays(15)));
        $loanEndDate = Carbon::instance($this->faker->dateTimeBetween($loanStartDate->copy()->addDays(1), $loanStartDate->copy()->addDays(60)));

        $allStatuses = LoanApplication::getStatusesList(); // Assuming method like getStatusesList() or STATUS_OPTIONS array
        $selectableStatuses = array_filter($allStatuses, fn ($key) => $key !== LoanApplication::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
        $chosenStatusKey = $this->faker->randomElement(empty($selectableStatuses) ? [LoanApplication::STATUS_PENDING_SUPPORT] : array_keys($selectableStatuses));

        // Timestamps and _by fields based on status
        $timestamps = $this->generateTimestampsForStatus($chosenStatusKey, $applicationDate, $loanStartDate, $loanEndDate);

        return [
            'user_id' => $applicantUser->id,
            'responsible_officer_id' => $responsibleOfficerUser->id,
            // 'supporting_officer_id' needs to be added based on design (section 4.3)
            'supporting_officer_id' => $this->faker->boolean(50) ? (User::whereNotIn('id', [$applicantUser->id, $responsibleOfficerUser->id])->inRandomOrder()->first()?->id) : null,

            'purpose' => $this->faker->catchPhrase() . ' - ' . $this->faker->bs(),
            'location' => $this->faker->city . ', ' . $this->faker->streetName,
            'return_location' => $this->faker->optional(0.3)->city . ', ' . $this->faker->streetName, // As per design (section 4.3)
            'loan_start_date' => $loanStartDate->format('Y-m-d H:i:s'),
            'loan_end_date' => $loanEndDate->format('Y-m-d H:i:s'),
            'status' => $chosenStatusKey,
            'rejection_reason' => $chosenStatusKey === LoanApplication::STATUS_REJECTED ? $this->faker->sentence() : null,
            'applicant_confirmation_timestamp' => $timestamps['applicant_confirmation_timestamp'],
            'admin_notes' => $this->faker->optional(0.1)->paragraph,

            // Timestamps and _by fields from helper
            'submitted_at' => $timestamps['submitted_at'],
            'approved_at' => $timestamps['approved_at'],
            'approved_by' => $timestamps['approved_by'],
            'rejected_at' => $timestamps['rejected_at'],
            'rejected_by' => $timestamps['rejected_by'],
            'cancelled_at' => $timestamps['cancelled_at'],
            'cancelled_by' => $timestamps['cancelled_by'],
            'issued_at' => $timestamps['issued_at'],
            'issued_by' => $timestamps['issued_by'],
            'returned_at' => $timestamps['returned_at'],
            'returned_by' => $timestamps['returned_by'],

            // 'created_by', 'updated_by' handled by BlameableObserver
            'created_at' => $applicationDate,
            'updated_at' => $applicationDate->copy()->addMinutes($this->faker->numberBetween(10, 1000)),
            'deleted_at' => null,
            // 'deleted_by' handled by BlameableObserver
        ];
    }

    private function generateTimestampsForStatus(string $status, Carbon $applicationDate, Carbon $loanStartDate, Carbon $loanEndDate): array
    {
        $timestamps = [
            'applicant_confirmation_timestamp' => null, 'submitted_at' => null,
            'approved_at' => null, 'approved_by' => null,
            'rejected_at' => null, 'rejected_by' => null,
            'cancelled_at' => null, 'cancelled_by' => null,
            'issued_at' => null, 'issued_by' => null,
            'returned_at' => null, 'returned_by' => null,
        ];

        if ($status !== LoanApplication::STATUS_DRAFT) {
            $timestamps['submitted_at'] = $applicationDate->copy()->subDays($this->faker->numberBetween(0, 2));
            $timestamps['applicant_confirmation_timestamp'] = $timestamps['submitted_at'];
        }

        $lastEventDate = $timestamps['submitted_at'] ?? $applicationDate;

        if (in_array($status, [
            LoanApplication::STATUS_PENDING_HOD_REVIEW, LoanApplication::STATUS_PENDING_BPM_REVIEW,
            LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_PARTIALLY_ISSUED, LoanApplication::STATUS_RETURNED, LoanApplication::STATUS_OVERDUE
        ])) { // Support approved
            $lastEventDate = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 2));
        }

        if (in_array($status, [
            LoanApplication::STATUS_PENDING_BPM_REVIEW, LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED,
            LoanApplication::STATUS_RETURNED, LoanApplication::STATUS_OVERDUE
        ])) { // HOD approved
            $lastEventDate = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 2));
        }

        if (in_array($status, [
            LoanApplication::STATUS_APPROVED, LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_PARTIALLY_ISSUED, LoanApplication::STATUS_RETURNED, LoanApplication::STATUS_OVERDUE
        ])) { // Final approval / BPM approval
            $timestamps['approved_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, 2));
            $timestamps['approved_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
            $lastEventDate = $timestamps['approved_at'];
        }

        if (in_array($status, [
            LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED,
            LoanApplication::STATUS_RETURNED, LoanApplication::STATUS_OVERDUE
        ])) {
            $timestamps['issued_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(0, 1));
            $timestamps['issued_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
            $lastEventDate = $timestamps['issued_at'];
        }

        if ($status === LoanApplication::STATUS_RETURNED) {
            $maxReturnDays = $loanEndDate->diffInDays($lastEventDate);
            $timestamps['returned_at'] = $lastEventDate->copy()->addDays($this->faker->numberBetween(1, $maxReturnDays > 0 ? $maxReturnDays : 1));
            $timestamps['returned_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === LoanApplication::STATUS_REJECTED) {
            $timestamps['rejected_at'] = ($timestamps['submitted_at'] ?? $applicationDate)->copy()->addDays($this->faker->numberBetween(1, 3));
            $timestamps['rejected_by'] = User::inRandomOrder()->first()?->id ?? User::factory()->create()->id;
        }

        if ($status === LoanApplication::STATUS_CANCELLED) {
            $timestamps['cancelled_at'] = ($timestamps['submitted_at'] ?? $applicationDate)->copy()->addDays($this->faker->numberBetween(1, 3));
            $timestamps['cancelled_by'] = $this->faker->boolean(70) ? ($timestamps['user_id'] ?? User::inRandomOrder()->first()?->id) : (User::inRandomOrder()->first()?->id ?? User::factory()->create()->id);
        }
        return $timestamps;
    }


    public function configure(): static
    {
        return $this->afterCreating(function (LoanApplication $loanApplication) {
            // Create LoanApplicationItems if not in draft status
            if ($loanApplication->status !== LoanApplication::STATUS_DRAFT && $loanApplication->applicationItems()->count() === 0) {
                LoanApplicationItem::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->for($loanApplication)
                    ->create();
            }

            // Create pending Approval task based on status
            $pendingApprovalStatuses = [
                LoanApplication::STATUS_PENDING_SUPPORT,
                LoanApplication::STATUS_PENDING_HOD_REVIEW,
                LoanApplication::STATUS_PENDING_BPM_REVIEW,
            ];

            if (in_array($loanApplication->status, $pendingApprovalStatuses) && $loanApplication->approvals()->count() === 0) {
                $currentApprovalStage = null;
                if ($loanApplication->status === LoanApplication::STATUS_PENDING_SUPPORT) {
                    $currentApprovalStage = Approval::STAGE_SUPPORT_REVIEW;
                } elseif ($loanApplication->status === LoanApplication::STATUS_PENDING_HOD_REVIEW) {
                    $currentApprovalStage = Approval::STAGE_HOD_REVIEW;
                } elseif ($loanApplication->status === LoanApplication::STATUS_PENDING_BPM_REVIEW) {
                    $currentApprovalStage = Approval::STAGE_BPM_REVIEW;
                }

                if ($currentApprovalStage && class_exists(Approval::class)) {
                    $officerForApproval = $loanApplication->supporting_officer_id ? User::find($loanApplication->supporting_officer_id) : null;
                    if (!$officerForApproval) { // Fallback if supporting_officer_id is not set or user not found
                         // Logic to find a suitable approver based on grade, department, or role
                        $officerForApproval = User::whereHas('roles', function($q) use ($currentApprovalStage) {
                            // This is a simplified example, real logic might be more complex
                            if($currentApprovalStage === Approval::STAGE_SUPPORT_REVIEW) $q->where('name', 'SupportingOfficer');
                            elseif($currentApprovalStage === Approval::STAGE_HOD_REVIEW) $q->where('name', 'HOD');
                            elseif($currentApprovalStage === Approval::STAGE_BPM_REVIEW) $q->where('name', 'BPMStaff');
                        })->inRandomOrder()->first() ?? User::factory()->create();
                    }

                    Approval::factory()
                        ->for($loanApplication, 'approvable')
                        ->status(Approval::STATUS_PENDING)
                        ->stage($currentApprovalStage)
                        ->create([
                            'officer_id' => $officerForApproval->id,
                            // 'created_by' & 'updated_by' handled by BlameableObserver or can be set here if needed
                        ]);
                }
            }
        });
    }

    // State methods
    public function draft(): static { return $this->state(['status' => LoanApplication::STATUS_DRAFT]); }
    public function pendingSupport(): static { return $this->state(['status' => LoanApplication::STATUS_PENDING_SUPPORT, 'applicant_confirmation_timestamp' => now()->subMinutes(5), 'submitted_at' => now()->subMinutes(4)]); }
    public function pendingHodReview(): static { return $this->pendingSupport()->state(['status' => LoanApplication::STATUS_PENDING_HOD_REVIEW]); }
    public function pendingBpmReview(): static { return $this->pendingHodReview()->state(['status' => LoanApplication::STATUS_PENDING_BPM_REVIEW]); }
    public function approved(): static { return $this->pendingBpmReview()->state(['status' => LoanApplication::STATUS_APPROVED, 'approved_at' => now(), 'approved_by' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id]); }
    public function rejected(): static { return $this->pendingSupport()->state(['status' => LoanApplication::STATUS_REJECTED, 'rejection_reason' => $this->faker->sentence, 'rejected_at' => now(), 'rejected_by' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id]); }
    public function issued(): static { return $this->approved()->state(['status' => LoanApplication::STATUS_ISSUED, 'issued_at' => now(), 'issued_by' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id]); }
    public function partiallyIssued(): static { return $this->approved()->state(['status' => LoanApplication::STATUS_PARTIALLY_ISSUED, 'issued_at' => now(), 'issued_by' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id]); }
    public function returned(): static { return $this->issued()->state(['status' => LoanApplication::STATUS_RETURNED, 'returned_at' => now(), 'returned_by' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id]); }
    public function overdue(): static { return $this->issued()->state(['status' => LoanApplication::STATUS_OVERDUE, 'loan_end_date' => now()->subDays(3)]); }
    public function cancelled(): static { return $this->state(['status' => LoanApplication::STATUS_CANCELLED, 'cancelled_at' => now()]); }
    public function certified(): static { return $this->state(['applicant_confirmation_timestamp' => now()->subMinutes(10)]); }
    public function withItems(int $count = 2): static { return $this->has(LoanApplicationItem::factory()->count($count), 'applicationItems'); }
    public function deleted(): static { return $this->state(['deleted_at' => now()]); } // deleted_by handled by observer
}
