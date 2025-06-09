<?php

namespace Database\Seeders;

use App\Models\Setting; // Your Setting model
use App\Models\User;    // For audit user
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Settings seeding (Revision 3 - Factory based)...');

        // The Setting model and factory assume a single-row settings table.
        // Truncate ensures we start fresh with one definitive row.
        DB::table('settings')->truncate();
        Log::info('Truncated settings table.');

        $adminUserForAudit = User::orderBy('id')->first(); // Get first user, likely an admin
        $auditUserId = $adminUserForAudit?->id;

        if (! $auditUserId) {
            // Create a fallback user if no users exist (e.g., running seeders in a very specific order or fresh db)
            // This relies on UserFactory being correctly set up.
            $fallbackUser = User::factory()->create(['name' => 'Audit User (SettingsSeeder)']);
            $auditUserId = $fallbackUser->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for SettingsSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in SettingsSeeder.");
        }

        // Define overrides for the factory's defaultRow state if needed.
        // The SettingFactory.php you provided already defines many of these.
        // These overrides ensure specific values for this MOTAC RMS instance.
        $settingsOverrides = [
            'site_name' => 'MOTAC Integrated Resource Management System - Seeded', // Specific site name for this seed
            'site_logo_path' => '/images/motac_logo_default.png', // Provide a default or ensure this path is valid
            'default_notification_email_from' => env('MAIL_FROM_ADDRESS', 'noreply.rms@motac.gov.my'),
            'default_notification_email_name' => env('MAIL_FROM_NAME', 'MOTAC Resource Management System'),

            // Terms and conditions are defined in the factory with Faker,
            // override here if you need specific static text for the seed.
            // 'terms_and_conditions_loan' => 'Ini adalah terma dan syarat pinjaman ICT MOTAC...',
            // 'terms_and_conditions_email' => 'Ini adalah terma dan syarat penggunaan emel MOTAC...',

            // SMS API credentials should ideally be null if not configured via .env
            // The factory sets them as optional or null.
            'sms_api_sender' => env('SMS_SENDER_ID', 'MOTACRMS'), // Your factory sets this to 'MOTAC_RMS' in defaultRow
            'sms_api_username' => env('SMS_API_USERNAME'), // Let factory handle or pull from env
            'sms_api_password' => env('SMS_API_PASSWORD'), // Let factory handle or pull from env

            // The factory and model boot method already handle these based on its logic.
            // Explicitly setting here ensures they are tied to the $auditUserId for seeding context.
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null, // Explicitly null for a new record
        ];

        Log::info('Creating default settings row using SettingFactory...');

        if (class_exists(Setting::class) && method_exists(Setting::class, 'factory')) {
            Setting::factory()
                ->defaultRow() // Apply the defaultRow state from SettingFactory.php
                ->create($settingsOverrides); // Apply our specific overrides
            Log::info('Default settings row created/updated successfully via factory.');
        } else {
            Log::error('App\Models\Setting model or its factory is not defined correctly.');
        }

        Log::info('Settings seeding complete.');
    }
}
