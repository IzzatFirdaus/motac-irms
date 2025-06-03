<?php

namespace Database\Factories;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EmailApplicationFactory extends EloquentFactory
{
    protected $model = EmailApplication::class;

    public function definition(): array
    {
        $applicantUser = User::inRandomOrder()->first() ?? User::factory()->create();

        $serviceStatusKeys = [];
        if (method_exists(User::class, 'getServiceStatusOptions')) {
            $serviceStatusKeys = array_keys(User::getServiceStatusOptions());
        }
        $defaultServiceStatus = defined(User::class.'::SERVICE_STATUS_TETAP') ? User::SERVICE_STATUS_TETAP : '1';
        $selectedServiceStatus = empty($serviceStatusKeys) ? $defaultServiceStatus : $this->faker->randomElement($serviceStatusKeys);

        $appointmentTypeKeys = [];
        if (method_exists(User::class, 'getAppointmentTypeOptions')) {
            $appointmentTypeKeys = array_keys(User::getAppointmentTypeOptions());
        }
        $defaultAppointmentType = defined(User::class.'::APPOINTMENT_TYPE_BAHARU') ? User::APPOINTMENT_TYPE_BAHARU : '1';
        $selectedAppointmentType = empty($appointmentTypeKeys) ? $defaultAppointmentType : $this->faker->randomElement($appointmentTypeKeys);

        $isCertified = $this->faker->boolean(80);

        return [
            'user_id' => $applicantUser->id,

            // Applicant Snapshot Fields (assuming these are in your EmailApplication model and migration)
            'applicant_title' => $applicantUser->title ?? $this->faker->randomElement(array_values(User::$TITLE_OPTIONS ?? ['Encik'])),
            'applicant_name' => $applicantUser->name,
            'applicant_identification_number' => $applicantUser->identification_number ?? $this->faker->numerify('######-##-####'),
            'applicant_passport_number' => $applicantUser->passport_number,
            'applicant_jawatan_gred' => ($applicantUser->position?->name ?? 'N/A') . ' / ' . ($applicantUser->grade?->name ?? 'N/A'),
            'applicant_bahagian_unit' => $applicantUser->department?->name ?? 'N/A',
            'applicant_level_aras' => $applicantUser->level ?? (string)$this->faker->numberBetween(1,18),
            'applicant_mobile_number' => $applicantUser->mobile_number ?? $this->faker->numerify('01#-#######'),
            'applicant_personal_email' => $applicantUser->personal_email ?? $applicantUser->email,

            // Core Application Fields
            'service_status' => $selectedServiceStatus,
            'appointment_type' => $selectedAppointmentType,

            'previous_department_name' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? $this->faker->company : null,
            'previous_department_email' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? $this->faker->companyEmail : null,

            'service_start_date' => (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI])) ? $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d') : null,
            'service_end_date' => function (array $attributes) use ($selectedServiceStatus) {
                return (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]) && isset($attributes['service_start_date']) && $attributes['service_start_date']) ?
                       Carbon::parse($attributes['service_start_date'])->addMonths($this->faker->numberBetween(3, 12))->format('Y-m-d') :
                       null;
            },

            'purpose' => $this->faker->sentence(10),
            'proposed_email' => $this->faker->optional(0.3)->passthrough(
                $this->faker->unique()->userName . '@' . config('mail.motac_domain', 'motac.gov.my')
            ),

            'group_email' => null,
            'group_admin_name' => null,
            'group_admin_email' => null,

            'supporting_officer_id' => User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first()?->id,
            'supporting_officer_name' => null,
            'supporting_officer_grade' => null,
            'supporting_officer_email' => null,

            'status' => $this->faker->randomElement([
                EmailApplication::STATUS_DRAFT,
                EmailApplication::STATUS_PENDING_SUPPORT,
            ]),

            'cert_info_is_true' => $isCertified,
            'cert_data_usage_agreed' => $isCertified,
            'cert_email_responsibility_agreed' => $isCertified,
            'certification_timestamp' => $isCertified ? $this->faker->dateTimeThisMonth() : null,
            'submitted_at' => ($isCertified && $this->faker->boolean(70)) ? $this->faker->dateTimeThisMonth() : null,

            'rejection_reason' => null,
            'final_assigned_email' => null,
            'final_assigned_user_id' => null,
            'processed_by' => null,
            'processed_at' => null,
            'created_at' => $createdAt = $this->faker->dateTimeBetween('-2 months', '-1 day'),
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    public function forGroupEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'group_email' => Str::slug($this->faker->words(2, true), '.') . '@' . config('mail.motac_domain', 'motac.gov.my'),
            'group_admin_name' => $this->faker->name(),
            'group_admin_email' => $this->faker->safeEmail(),
            'proposed_email' => null,
            'final_assigned_email' => $attributes['group_email'] ?? (Str::slug($this->faker->words(2, true), '.') . '@' . config('mail.motac_domain', 'motac.gov.my')),
            'purpose' => $attributes['purpose'] ?? 'Request for group email: ' . ($attributes['group_email'] ?? 'N/A'),
        ]);
    }

    public function certified(): static
    {
        return $this->state(fn (array $attributes) => [
            'cert_info_is_true' => true,
            'cert_data_usage_agreed' => true,
            'cert_email_responsibility_agreed' => true,
            'certification_timestamp' => $attributes['certification_timestamp'] ?? Carbon::now()->subMinutes($this->faker->numberBetween(5, 60)),
        ]);
    }

    public function pendingSupport(): static
    {
        return $this->certified()->state([
            'status' => EmailApplication::STATUS_PENDING_SUPPORT,
            'submitted_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }

    public function pendingAdmin(): static
    {
        return $this->pendingSupport()->state([
            'status' => EmailApplication::STATUS_PENDING_ADMIN
        ]);
    }

    public function approvedBySupport(): static
    {
        return $this->pendingSupport()->state([
            'status' => EmailApplication::STATUS_PENDING_ADMIN,
        ]);
    }

    public function fullyApproved(): static
    {
        return $this->pendingAdmin()->state([
            'status' => EmailApplication::STATUS_APPROVED,
        ]);
    }

    public function processing(): static
    {
        return $this->fullyApproved()->state([
            'status' => EmailApplication::STATUS_PROCESSING,
            'processed_by' => User::role('IT Admin')->inRandomOrder()->first()?->id,
            'processed_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }

    public function completed(): static
    {
        return $this->processing()->state(function (array $attributes) {
            $applicantUser = User::find($attributes['user_id']);
            // $nameForSlug = $applicantUser?->name ?? ($this->faker->firstName . '.' . $this->faker->lastName); // Not directly used for finalEmail with unique()
            // $baseEmailUser = Str::slug($nameForSlug, '.'); // Not directly used for finalEmail with unique()
            $baseUserId = Str::slug($applicantUser?->name ?? ($this->faker->firstName . $this->faker->lastName), '');

            $isUserIdOnly = isset($attributes['service_status']) &&
                            $attributes['service_status'] === User::SERVICE_STATUS_PELAJAR_INDUSTRI;

            $finalEmail = null;

            if (!$isUserIdOnly) {
                // Use proposed_email if available (it's already unique from definition method),
                // otherwise generate a new unique email using Faker's unique userName.
                $finalEmail = $attributes['proposed_email'] ??
                              $this->faker->unique()->userName . '@' . config('mail.motac_domain', 'motac.gov.my');
            }

            return [
                'status' => EmailApplication::STATUS_COMPLETED,
                'final_assigned_email' => $finalEmail,
                'final_assigned_user_id' => $baseUserId . $this->faker->unique()->randomNumber(4, true), // Ensure unique final_assigned_user_id
                'processed_at' => $attributes['processed_at'] ?? now(),
            ];
        });
    }

    public function rejected(): static
    {
        return $this->pendingSupport()->state([
            'status' => EmailApplication::STATUS_REJECTED,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function provisionFailed(): static
    {
        return $this->processing()->state(fn (array $attributes) => [
            'status' => EmailApplication::STATUS_PROVISION_FAILED,
            'rejection_reason' => 'Automated provisioning failed during factory creation.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmailApplication::STATUS_CANCELLED,
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
