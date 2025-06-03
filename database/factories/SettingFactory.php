<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        // $auditUserId = User::inRandomOrder()->first()?->id; // No longer explicitly needed here for blameable
        // if (!$auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
        //     $auditUserId = User::factory()->create()->id;
        // }

        return [
            'site_name' => 'MOTAC Integrated Resource System - Factory Default',
            'site_logo_path' => null,
            'default_notification_email_from' => $this->faker->unique()->safeEmail,
            'default_notification_email_name' => 'MOTAC RMS Notifications (Factory)',
            'terms_and_conditions_loan' => $this->faker->paragraphs(3, true),
            'terms_and_conditions_email' => $this->faker->paragraphs(2, true),
            'sms_api_sender' => $this->faker->optional(0.7)->company,
            'sms_api_username' => $this->faker->optional(0.7)->userName,
            'sms_api_password' => $this->faker->optional(0.7)->asciify('**********'),
            // 'created_by', 'updated_by' are handled by BlameableObserver
            'deleted_by' => null,
        ];
    }

    public function defaultRow(): static
    {
        // $auditUserId = User::inRandomOrder()->first()?->id; // No longer explicitly needed here for blameable
        // if (!$auditUserId && class_exists(User::class) && method_exists(User::class, 'factory')) {
        //     $auditUserId = User::factory()->create()->id;
        // }

        return $this->state(
            fn (array $attributes) => [
                'site_name' => 'MOTAC Integrated Resource Management System - Default Row',
                'site_logo_path' => '/images/logo_default.png',
                'default_notification_email_from' => 'noreply.system@motac.gov.my',
                'default_notification_email_name' => 'MOTAC RMS (System)',
                'sms_api_sender' => 'MOTAC_System',
                'sms_api_username' => null,
                'sms_api_password' => null,
                'terms_and_conditions_loan' => 'Sila patuhi semua terma dan syarat peminjaman peralatan ICT yang ditetapkan oleh MOTAC.',
                'terms_and_conditions_email' => 'Penggunaan emel rasmi MOTAC adalah tertakluk kepada polisi keselamatan ICT MOTAC.',
                // 'created_by' => $attributes['created_by'] ?? $auditUserId, // Handled by BlameableObserver
                // 'updated_by' => $attributes['updated_by'] ?? $auditUserId, // Handled by BlameableObserver
            ]
        );
    }
}
