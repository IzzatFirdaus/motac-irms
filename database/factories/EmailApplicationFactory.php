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
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        $applicantUser = User::inRandomOrder()->first() ?? User::factory()->create();

        $serviceStatusKeys = method_exists(User::class, 'getServiceStatusOptions') ? array_keys(User::getServiceStatusOptions()) : [];
        $defaultServiceStatus = defined(User::class.'::SERVICE_STATUS_TETAP') ? User::SERVICE_STATUS_TETAP : '1';
        $selectedServiceStatus = $serviceStatusKeys === [] ? $defaultServiceStatus : $this->faker->randomElement($serviceStatusKeys);

        $appointmentTypeKeys = method_exists(User::class, 'getAppointmentTypeOptions') ? array_keys(User::getAppointmentTypeOptions()) : [];
        $defaultAppointmentType = defined(User::class.'::APPOINTMENT_TYPE_BAHARU') ? User::APPOINTMENT_TYPE_BAHARU : '1';
        $selectedAppointmentType = $appointmentTypeKeys === [] ? $defaultAppointmentType : $this->faker->randomElement($appointmentTypeKeys);

        $isCertified = $this->faker->boolean(80);

        return [
            'user_id' => $applicantUser->id,
            'applicant_title' => $applicantUser->title ?? $this->faker->randomElement(array_values(User::$TITLE_OPTIONS ?? ['Encik', 'Puan', 'Cik'])),
            'applicant_name' => $applicantUser->name,
            'applicant_identification_number' => $applicantUser->identification_number ?? $msFaker->myKadNumber(null, true),
            'applicant_passport_number' => $applicantUser->passport_number,
            'applicant_jawatan_gred' => ($applicantUser->position?->name ?? 'N/A').' / '.($applicantUser->grade?->name ?? 'N/A'),
            'applicant_bahagian_unit' => $applicantUser->department?->name ?? 'N/A',
            'applicant_level_aras' => $applicantUser->level ?? (string) $this->faker->numberBetween(1, 18),
            'applicant_mobile_number' => $applicantUser->mobile_number ?? $msFaker->mobileNumber(true, true),
            'applicant_personal_email' => $applicantUser->personal_email ?? $applicantUser->email,

            'service_status' => $selectedServiceStatus,
            'appointment_type' => $selectedAppointmentType,

            'previous_department_name' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? 'Jabatan '.$msFaker->company : null,
            'previous_department_email' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? $msFaker->userName.'@'.$this->faker->randomElement(['jpa.gov.my', 'customs.gov.my', 'treasury.gov.my']) : null,

            'service_start_date' => (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI])) ? $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d') : null,
            'service_end_date' => function (array $attributes) use ($selectedServiceStatus) {
                return (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]) && isset($attributes['service_start_date']) && $attributes['service_start_date']) ?
                       Carbon::parse($attributes['service_start_date'])->addMonths($this->faker->numberBetween(3, 12))->format('Y-m-d') :
                       null;
            },

            'purpose' => $msFaker->sentence(10),
            'proposed_email' => $this->faker->optional(0.3)->passthrough(
                $this->faker->unique()->userName.'@'.config('mail.motac_domain', 'motac.gov.my')
            ),

            'group_email' => null, 'group_admin_name' => null, 'group_admin_email' => null,

            'supporting_officer_id' => User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first()?->id,
            'supporting_officer_name' => null, 'supporting_officer_grade' => null, 'supporting_officer_email' => null,

            'status' => $this->faker->randomElement([EmailApplication::STATUS_DRAFT, EmailApplication::STATUS_PENDING_SUPPORT]),

            'cert_info_is_true' => $isCertified, 'cert_data_usage_agreed' => $isCertified, 'cert_email_responsibility_agreed' => $isCertified,
            'certification_timestamp' => $isCertified ? $this->faker->dateTimeThisMonth() : null,
            'submitted_at' => ($isCertified && $this->faker->boolean(70)) ? $this->faker->dateTimeThisMonth() : null,

            'rejection_reason' => null, 'final_assigned_email' => null, 'final_assigned_user_id' => null,
            'processed_by' => null, 'processed_at' => null,
            'created_at' => $createdAt = $this->faker->dateTimeBetween('-2 months', '-1 day'),
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    public function forGroupEmail(): static
    {
        return $this->state(function (array $attributes): array {
            $msFaker = \Faker\Factory::create('ms_MY');

            return [
                'group_email' => Str::slug($msFaker->words(2, true), '.').'@'.config('mail.motac_domain', 'motac.gov.my'),
                'group_admin_name' => $msFaker->name(),
                'group_admin_email' => $msFaker->safeEmail(),
                'proposed_email' => null,
                'final_assigned_email' => $attributes['group_email'] ?? (Str::slug($msFaker->words(2, true), '.').'@'.config('mail.motac_domain', 'motac.gov.my')),
                'purpose' => $attributes['purpose'] ?? 'Permohonan untuk e-mel kumpulan: '.($attributes['group_email'] ?? 'N/A'),
            ];
        });
    }

    public function certified(): static
    {
        return $this->state(fn (array $attributes): array => [
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
            'status' => EmailApplication::STATUS_PENDING_ADMIN,
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
        return $this->processing()->state(function (array $attributes): array {
            $applicantUser = User::find($attributes['user_id']);
            $baseUserId = Str::slug($applicantUser?->name ?? ($this->faker->firstName.$this->faker->lastName), '');

            $isUserIdOnly = isset($attributes['service_status']) &&
                            $attributes['service_status'] === User::SERVICE_STATUS_PELAJAR_INDUSTRI;

            $finalEmail = null;

            if (! $isUserIdOnly) {
                $finalEmail = $attributes['proposed_email'] ??
                              $this->faker->unique()->userName.'@'.config('mail.motac_domain', 'motac.gov.my');
            }

            return [
                'status' => EmailApplication::STATUS_COMPLETED,
                'final_assigned_email' => $finalEmail,
                'final_assigned_user_id' => $baseUserId.$this->faker->unique()->randomNumber(4, true),
                'processed_at' => $attributes['processed_at'] ?? now(),
            ];
        });
    }

    public function rejected(): static
    {
        return $this->pendingSupport()->state(function (array $attributes): array {
            $msFaker = \Faker\Factory::create('ms_MY');

            return [
                'status' => EmailApplication::STATUS_REJECTED,
                'rejection_reason' => $msFaker->sentence(),
            ];
        });
    }

    public function provisionFailed(): static
    {
        return $this->processing()->state(fn (array $attributes): array => [
            'status' => EmailApplication::STATUS_PROVISION_FAILED,
            'rejection_reason' => 'Automated provisioning failed during factory creation.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => EmailApplication::STATUS_CANCELLED,
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
        ]);
    }
}
