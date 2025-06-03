<?php

namespace Database\Factories;

use App\Models\Setting;
// Removed: use App\Models\User; // Not strictly needed if BlameableObserver handles audit fields
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'site_name' => 'MOTAC Integrated Resource System - Factory Base',
            'site_logo_path' => $this->faker->optional(0.5)->imageUrl(200, 100, 'logo'),
            'application_name' => 'MOTAC RMS (Base Factory Default)',
            'default_notification_email_from' => $this->faker->unique()->safeEmail,
            'default_notification_email_name' => 'MOTAC RMS Notifications (Base)',
            'default_system_email' => $this->faker->optional(0.8)->safeEmail,
            'default_loan_period_days' => $this->faker->randomElement([7, 14, 30]),
            'max_loan_items_per_application' => $this->faker->numberBetween(3, 10),
            'contact_us_email' => $this->faker->optional(0.9)->safeEmail,
            'system_maintenance_mode' => $this->faker->boolean(10),
            'system_maintenance_message' => $this->faker->optional(0.5)->sentence(15),
            'terms_and_conditions_loan' => $this->faker->paragraphs(2, true),
            'terms_and_conditions_email' => $this->faker->paragraphs(2, true),
            'sms_api_sender' => $this->faker->optional(0.7)->company,
            'sms_api_username' => $this->faker->optional(0.7)->userName,
            'sms_api_password' => $this->faker->optional(0.7)->password(8, 16),
            // created_by, updated_by handled by BlameableObserver or seeder override
            'deleted_by' => null,
        ];
    }

    public function defaultRow(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'site_name' => 'MOTAC Integrated Resource Management System',
                'site_logo_path' => '/images/motac_default_logo.png',
                'application_name' => 'Sistem Pengurusan Sumber Bersepadu MOTAC',
                'default_notification_email_from' => 'noreply.spsb@motac.gov.my',
                'default_notification_email_name' => 'MOTAC SPSB Notifikasi',
                'default_system_email' => 'system.spsb@motac.gov.my',
                'default_loan_period_days' => 7,
                'max_loan_items_per_application' => 5,
                'contact_us_email' => 'aduan.spsb@motac.gov.my',
                'system_maintenance_mode' => false,
                'system_maintenance_message' => 'Sistem sedang dalam mod penyelenggaraan. Sila cuba sebentar lagi.',
                'sms_api_sender' => 'MOTACGov',
                'sms_api_username' => null,
                'sms_api_password' => null,
                'terms_and_conditions_loan' => 'Sila patuhi semua terma dan syarat peminjaman peralatan ICT yang ditetapkan oleh pihak MOTAC. Sebarang kerosakan atau kehilangan adalah di bawah tanggungjawab peminjam.',
                'terms_and_conditions_email' => 'Penggunaan alamat e-mel rasmi MOTAC adalah tertakluk sepenuhnya kepada polisi keselamatan ICT dan tatakelola data MOTAC.',
                // created_by, updated_by handled by BlameableObserver or seeder override
            ]
        );
    }
}
