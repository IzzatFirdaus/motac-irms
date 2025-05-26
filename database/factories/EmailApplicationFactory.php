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
        // $auditUserId = User::orderBy('id')->first()?->id ?? $applicantUser->id; // For created_by, let BlameableObserver handle

        $applicantDepartment = $applicantUser->department?->name ?? $this->faker->company . ' Department';
        $applicantPosition = $applicantUser->position?->name ?? $this->faker->jobTitle;
        $applicantGrade = $applicantUser->grade?->name ?? 'N' . $this->faker->numberBetween(19, 54);

        $serviceStatusKeys = array_keys(User::getServiceStatusOptions()); // Using static method from User model
        $selectedServiceStatus = empty($serviceStatusKeys) ? 'tetap' : $this->faker->randomElement($serviceStatusKeys);

        $appointmentTypeKeys = array_keys(User::getAppointmentTypeOptions()); // Using static method
        $selectedAppointmentType = empty($appointmentTypeKeys) ? 'baharu' : $this->faker->randomElement($appointmentTypeKeys);

        $isCertified = $this->faker->boolean(80);

        // Fields that might be part of the EmailApplication model itself (not denormalized from User)
        // If these are indeed denormalized, fetching from $applicantUser is correct.
        // Otherwise, they should be faked independently or set via specific states.
        // Based on System Design (Section 4.2), these fields are on email_applications table.

        return [
            'user_id' => $applicantUser->id, // Applicant
            // Applicant details snapshot (denormalized as per original factory)
            'applicant_title' => $applicantUser->title ?? $this->faker->title(),
            'applicant_name' => $applicantUser->name,
            'applicant_identification_number' => $applicantUser->identification_number ?? $this->faker->numerify('######-##-####'),
            'applicant_passport_number' => $applicantUser->passport_number,
            'applicant_jawatan_gred' => ($applicantUser->position?->name ?? $this->faker->jobTitle) . ' / ' . ($applicantUser->grade?->name ?? 'N/A'),
            'applicant_bahagian_unit' => $applicantUser->department?->name ?? $this->faker->company,
            'applicant_level_aras' => $applicantUser->level ?? (string) $this->faker->numberBetween(1, 18),
            'applicant_mobile_number' => $applicantUser->mobile_number ?? $this->faker->phoneNumber,
            'applicant_personal_email' => $applicantUser->personal_email ?? $applicantUser->email,


            // Application specific details based on design (Section 4.2)
            // 'service_status' and 'appointment_type' on email_applications seem redundant if they are primary on User
            // Assuming they are for the state of the user *at the time of application* or if the applicant is not yet a User
            'service_status' => $selectedServiceStatus, // As per User model's enum values
            'appointment_type' => $selectedAppointmentType, // As per User model's enum values

            'previous_department_name' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? $this->faker->company : null,
            'previous_department_email' => ($selectedAppointmentType === User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN) ? $this->faker->companyEmail : null,

            'service_start_date' => (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI])) ? $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d') : null,
            'service_end_date' => function (array $attributes) use ($selectedServiceStatus) {
                return (in_array($selectedServiceStatus, [User::SERVICE_STATUS_KONTRAK_MYSTEP, User::SERVICE_STATUS_PELAJAR_INDUSTRI]) && $attributes['service_start_date']) ?
                       Carbon::parse($attributes['service_start_date'])->addMonths($this->faker->numberBetween(3, 12))->format('Y-m-d') :
                       null;
            },

            'application_reason_notes' => $this->faker->sentence(10), // "Cadangan E-mel ID/ Tujuan/ Catatan"
            'proposed_email' => $this->faker->optional(0.7)->unique()->userName . '@' . config('mail.motac_domain', 'motac.gov.my'),
            'group_email' => null, // For group email requests
            'contact_person_name' => null, // "Nama Admin/EO/CC"
            'contact_person_email' => null, // "E-mel Admin/EO/CC"

            'supporting_officer_id' => User::where('id', '!=', $applicantUser->id)->inRandomOrder()->first()?->id, // Links to users table
            // The following fields are for storing details if supporting officer is not a system user or for historical data.
            // System design (4.2) keeps these as string.
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

            'rejection_reason' => null,
            'final_assigned_email' => null,
            'final_assigned_user_id' => null,
            'created_at' => $createdAt = $this->faker->dateTimeBetween('-2 months', '-1 day'),
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
            // 'created_by', 'updated_by' handled by BlameableObserver
        ];
    }

    public function forGroupEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'group_email' => Str::slug($this->faker->words(2, true), '.') . '@' . config('mail.motac_domain', 'motac.gov.my'),
            'contact_person_name' => $this->faker->name(),
            'contact_person_email' => $this->faker->safeEmail(),
            'proposed_email' => null, // Typically null for group email requests
            'final_assigned_email' => $attributes['group_email'], // Group email becomes the final assigned email
            'application_reason_notes' => $attributes['application_reason_notes'] ?? 'Request for group email: ' . ($attributes['group_email'] ?? 'N/A'),
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
        return $this->certified()->state(['status' => EmailApplication::STATUS_PENDING_SUPPORT]);
    }

    public function pendingAdmin(): static
    {
        // Assumes it went through support approval
        return $this->pendingSupport()->state(['status' => EmailApplication::STATUS_PENDING_ADMIN]);
    }

    // 'approvedBySupport' might be an internal state before 'pendingAdmin' or 'approved'
    // For simplicity, let's make it lead to pending admin action.
    public function approvedBySupport(): static
    {
        return $this->pendingSupport()->state(['status' => EmailApplication::STATUS_PENDING_ADMIN]);
    }

    public function fullyApproved(): static // Application is approved, ready for provisioning
    {
        return $this->pendingAdmin()->state(['status' => EmailApplication::STATUS_APPROVED]);
    }

    public function processing(): static // IT Admin is provisioning
    {
        return $this->fullyApproved()->state(['status' => EmailApplication::STATUS_PROCESSING]);
    }

    public function completed(): static // Provisioning done, email/ID assigned
    {
        return $this->processing()->state(function (array $attributes) {
            $applicantUser = User::find($attributes['user_id']);
            $nameForSlug = $applicantUser?->name ?? ($this->faker->firstName . '.' . $this->faker->lastName);
            $baseEmail = Str::slug($nameForSlug, '.');

            // Determine if it's an email or just user ID request based on service_status
            // Pelajar Latihan Industri only gets User ID
            $isUserIdOnly = $attributes['service_status'] === User::SERVICE_STATUS_PELAJAR_INDUSTRI;

            $finalEmail = null;
            $finalUserId = null;

            if (!$isUserIdOnly) {
                 // Use proposed_email if available, else generate one
                $finalEmail = $attributes['proposed_email'] ?? ($baseEmail . '@' . config('mail.motac_domain', 'motac.gov.my'));
            }

            // All users might get a system user_id, even if they primarily use an external email.
            // For Latihan Industri, this is their primary identifier.
            // The `user_id_assigned` field on the users table seems to be the target for this.
            $finalUserId = Str::slug($nameForSlug, ''); // Generate a simple user ID

            return [
                'status' => EmailApplication::STATUS_COMPLETED,
                'final_assigned_email' => $finalEmail,
                'final_assigned_user_id' => $finalUserId,
            ];
        });
    }

    public function rejected(): static
    {
        // Can be rejected at any stage, assume pending_support for this state
        return $this->pendingSupport()->state([
            'status' => EmailApplication::STATUS_REJECTED,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function provisionFailed(): static
    {
        return $this->processing()->state(fn (array $attributes) => [
            'status' => EmailApplication::STATUS_PROVISION_FAILED,
            // Add a reason if your model supports it
            // 'provisioning_failure_reason' => $this->faker->bs(),
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
            // 'deleted_by' will be handled by BlameableObserver if user is authenticated
        ]);
    }
}
