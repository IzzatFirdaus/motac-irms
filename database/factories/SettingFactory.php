<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory // Correct class declaration
{
    protected $model = Setting::class;

    public function definition(): array
    {
        // Ensure at least one user exists for audit purposes, or create one.
        $auditUserId = User::inRandomOrder()->first()?->id;
        if (!$auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $auditUserId = User::factory()->create()->id;
        }

        return [
            // Fields from your Setting.php model and factory
            'site_name' => 'MOTAC Integrated Resource System - Factory Default',
            'site_logo_path' => null,
            'default_notification_email_from' => $this->faker->unique()->safeEmail,
            'default_notification_email_name' => 'MOTAC RMS Notifications (Factory)',
            'terms_and_conditions_loan' => $this->faker->paragraphs(3, true),
            'terms_and_conditions_email' => $this->faker->paragraphs(2, true),

            'sms_api_sender' => $this->faker->optional(0.7)->company,
            'sms_api_username' => $this->faker->optional(0.7)->userName,
            'sms_api_password' => $this->faker->optional(0.7)->asciify('**********'), // Avoid real passwords

            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            // created_at and updated_at will be handled by Eloquent timestamps
        ];
    }

    /**
     * For a single-row settings table, this state ensures specific defaults.
     */
    public function defaultRow(): static
    {
        // Ensure at least one user exists for audit purposes for the default row state as well.
        $auditUserId = User::inRandomOrder()->first()?->id;
        if (!$auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
            $auditUserId = User::factory()->create()->id;
        }

        return $this->state(
            fn (array $attributes) => [
                'site_name' => 'MOTAC Integrated Resource Management System - Default Row',
                'site_logo_path' => '/images/logo_default.png', // A more specific default path
                'default_notification_email_from' => 'noreply.system@motac.gov.my',
                'default_notification_email_name' => 'MOTAC RMS (System)',
                'sms_api_sender' => 'MOTAC_System',
                'sms_api_username' => null, // Typically set via .env or specific seeder override
                'sms_api_password' => null, // Typically set via .env or specific seeder override
                'terms_and_conditions_loan' => 'Sila patuhi semua terma dan syarat peminjaman peralatan ICT yang ditetapkan oleh MOTAC.',
                'terms_and_conditions_email' => 'Penggunaan emel rasmi MOTAC adalah tertakluk kepada polisi keselamatan ICT MOTAC.',
                'created_by' => $attributes['created_by'] ?? $auditUserId, // Use existing or fallback
                'updated_by' => $attributes['updated_by'] ?? $auditUserId, // Use existing or fallback
            ]
        );
    }
}
