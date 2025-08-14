<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');

        return [
            'site_name' => 'Sistem Pengurusan Sumber Bersepadu MOTAC',
            'site_logo_path' => '/images/motac_default_logo.png',
            'application_name' => 'MOTAC IRMS',
            'default_notification_email_from' => 'noreply.spsb@motac.gov.my',
            'default_notification_email_name' => 'Notifikasi MOTAC IRMS',
            'default_system_email' => 'sistem.spsb@motac.gov.my',
            'default_loan_period_days' => $this->faker->randomElement([7, 14, 30]),
            'max_loan_items_per_application' => $this->faker->numberBetween(3, 10),
            'contact_us_email' => 'aduan.spsb@motac.gov.my',
            'system_maintenance_mode' => $this->faker->boolean(10),
            'system_maintenance_message' => 'Sistem kini dalam mod penyelenggaraan. Sila cuba lagi sebentar nanti.',
            'terms_and_conditions_loan' => $msFaker->paragraphs(2, true),
            'terms_and_conditions_email' => $msFaker->paragraphs(2, true),
            'sms_api_sender' => 'MOTACGov',
            'sms_api_username' => $this->faker->optional(0.7)->userName,
            'sms_api_password' => $this->faker->optional(0.7)->password(8, 16),
            'deleted_by' => null,
        ];
    }

    public function defaultRow(): static
    {
        return $this->state(
            fn (array $attributes): array => [
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
            ]
        );
    }
}
