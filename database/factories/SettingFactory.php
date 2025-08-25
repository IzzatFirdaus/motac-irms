<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for the Setting model.
 *
 * - Uses static caches for User IDs to minimize repeated DB queries (for blameable columns).
 * - Never creates related models (users) in definition() -- expects at least one user exists.
 * - All foreign keys (created_by, updated_by, deleted_by) are randomly assigned from existing user IDs or can be set via state from the seeder.
 * - Use in seeders that ensure at least one user exists for audit fields.
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        // Cache user IDs for blameable columns (created_by, updated_by, deleted_by)
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $auditUserId = !empty($userIds) ? Arr::random($userIds) : null;

        // Use static Malaysian faker for performance and consistency
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Timestamps for created/updated/deleted
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted for variety
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $auditUserId : null;

        return [
            // General site/application settings
            'site_name' => $msFaker->company . ' RMS',
            'site_logo_path' => '/images/motac_default_logo.png',
            'application_name' => 'MOTAC Integrated Resource Management System',

            // Default email settings
            'default_notification_email_from' => $this->faker->safeEmail(),
            'default_notification_email_name' => 'MOTAC RMS Notification',
            'default_system_email' => $this->faker->safeEmail(),

            // Loan settings
            'default_loan_period_days' => $this->faker->randomElement([7, 14, 21, 30]),
            'max_loan_items_per_application' => $this->faker->numberBetween(1, 10),

            // Contact and maintenance
            'contact_us_email' => $this->faker->safeEmail(),
            'system_maintenance_mode' => $this->faker->boolean(10),
            'system_maintenance_message' => $msFaker->optional(0.5)->sentence(),

            // SMS API (optional fields)
            'sms_api_sender' => $this->faker->optional(0.7)->word(),
            'sms_api_username' => $this->faker->optional(0.5)->userName(),
            'sms_api_password' => $this->faker->optional(0.5)->password(8, 16),

            // Terms & conditions
            'terms_and_conditions_loan' => $msFaker->optional(0.7)->paragraphs(2, true),
            'terms_and_conditions_email' => $msFaker->optional(0.7)->paragraphs(2, true),

            // Blameable/audit columns
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => $deletedBy,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
        ];
    }

    /**
     * State for the default settings row (for use in SettingsSeeder).
     * These values are used to create the main configuration for the system.
     */
    public function defaultRow(): static
    {
        // Use static user cache for defaultRow as well
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $auditUserId = !empty($userIds) ? Arr::random($userIds) : null;

        return $this->state(function (array $attributes) use ($auditUserId): array {
            return [
                'site_name' => 'MOTAC Integrated Resource Management System',
                'site_logo_path' => '/images/motac_default_logo.png',
                'application_name' => 'Sistem Pengurusan Sumber Bersepadu MOTAC',
                'default_notification_email_from' => 'noreply@motac.gov.my',
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
     * State to mark the settings as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = !empty($userIds) ? Arr::random($userIds) : null;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
