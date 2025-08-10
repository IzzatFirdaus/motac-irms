<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the Setting model.
 *
 * Generates application-wide configuration settings.
 * Ensures all fields are present and match the settings table structure (see migration and model).
 * By default, produces a single row for the application, but can be used to create test variants.
 * Sets blameable/audit columns if users exist.
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        // Use a Malaysian locale for more realistic data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Get or create a user for blameable fields
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (SettingFactory)'])->id;

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;

        return [
            'site_name' => $msFaker->optional(0.7)->company . ' RMS',
            'site_logo_path' => '/images/motac_default_logo.png',
            'application_name' => 'MOTAC Integrated Resource Management System',
            'default_notification_email_from' => $this->faker->safeEmail(),
            'default_notification_email_name' => 'MOTAC RMS Notification',
            'default_system_email' => $this->faker->safeEmail(),
            'default_loan_period_days' => $this->faker->randomElement([7, 14, 21, 30]),
            'max_loan_items_per_application' => $this->faker->numberBetween(1, 10),
            'contact_us_email' => $this->faker->safeEmail(),
            'system_maintenance_mode' => $this->faker->boolean(10),
            'system_maintenance_message' => $msFaker->optional(0.5)->sentence(),
            'sms_api_sender' => $this->faker->optional(0.7)->word(),
            'sms_api_username' => $this->faker->optional(0.5)->userName(),
            'sms_api_password' => $this->faker->optional(0.5)->password(8, 16),
            'terms_and_conditions_loan' => $msFaker->optional(0.7)->paragraphs(2, true),
            'terms_and_conditions_email' => $msFaker->optional(0.7)->paragraphs(2, true),
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => $isDeleted ? $auditUserId : null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * State for the default settings row (for use in SettingsSeeder).
     */
    public function defaultRow(): static
    {
        // These values are consistent and suitable for seeding a default configuration.
        return $this->state(function (array $attributes): array {
            $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Audit User (SettingFactory)'])->id;
            return [
                'site_name' => 'MOTAC Integrated Resource Management System',
                'site_logo_path' => '/images/motac_default_logo.png',
                'application_name' => 'Sistem Pengurusan Sumber Bersepadu MOTAC',
                'default_notification_email_from' => 'noreply.rms@motac.gov.my',
                'default_notification_email_name' => 'MOTAC Resource Management System',
                'default_system_email' => 'system.rms@motac.gov.my',
                'default_loan_period_days' => 7,
                'max_loan_items_per_application' => 5,
                'contact_us_email' => 'aduan.rms@motac.gov.my',
                'system_maintenance_mode' => false,
                'system_maintenance_message' => 'Sistem kini dalam mod penyelenggaraan. Sila cuba lagi dalam beberapa minit.',
                'sms_api_sender' => 'MOTACGov',
                'sms_api_username' => null,
                'sms_api_password' => null,
                'terms_and_conditions_loan' => 'Sila patuhi semua terma dan syarat peminjaman peralatan ICT MOTAC.',
                'terms_and_conditions_email' => 'Penggunaan alamat e-mel rasmi MOTAC tertakluk pada polisi keselamatan ICT dan tatakelola data MOTAC.',
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        });
    }

    /**
     * Mark settings as soft deleted.
     */
    public function deleted(): static
    {
        $auditUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (SettingFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $auditUserId,
        ]);
    }
}
