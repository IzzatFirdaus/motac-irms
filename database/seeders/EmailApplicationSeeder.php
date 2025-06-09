<?php

namespace Database\Seeders;

use App\Models\EmailApplication;
use App\Models\User; // Ensure User is imported if factory needs it for audit fallback
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // If you were using DB facade for truncate
use Illuminate\Support\Facades\Log;

class EmailApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Email Applications seeding...');

        // Assuming you might want to truncate for a clean seed run for this table
        // DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // EmailApplication::truncate(); // Or DB::table('email_applications')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        // Log::info('Truncated email_applications table (if uncommented).');

        // Ensure an audit user exists, or the factory can create one.
        // $auditUserId = User::first()?->id ?? User::factory()->create()->id;

        if (User::count() === 0) {
            Log::error(
                'No Users found. EmailApplicationFactory might fail or create users. Run UserSeeder first.'
            );
            // return; // Optionally stop if no users
        }

        Log::info('Creating Email Application records with various statuses...');

        EmailApplication::factory()
            ->count(10)
            ->create(); // Defaults to states like 'draft' or 'pending_support' from factory definition
        Log::info('Created 10 draft/default email applications.');

        EmailApplication::factory()
            ->count(15)
            ->certified() // Assuming 'certified' state exists and sets appropriate fields
            ->create();
        Log::info('Created 15 certified email applications.');

        EmailApplication::factory()
            ->count(10)
            ->pendingSupport() // This state should exist
            ->create();
        Log::info('Created 10 pending support review email applications.');

        EmailApplication::factory()
            ->count(8)
            ->pendingAdmin() // This state should exist
            ->create();
        Log::info('Created 8 pending IT Admin review email applications.');

        // This is where the error was, line 65 or around here:
        EmailApplication::factory()
            ->count(12)
          // ->approved() // OLD LINE - 'approved' method does not exist
            ->approvedBySupport() // MODIFIED: Changed to use existing 'approvedBySupport' state
            ->create();
        Log::info('Created 12 approved (by support) email applications.');

        EmailApplication::factory()
            ->count(7)
            ->processing() // This state should exist
            ->create();
        Log::info('Created 7 processing email applications.');

        EmailApplication::factory()
            ->count(15)
            ->completed() // This state should exist
            ->create();
        Log::info('Created 15 completed email applications.');

        EmailApplication::factory()
            ->count(5)
            ->rejected() // This state should exist
            ->create();
        Log::info('Created 5 rejected email applications.');

        EmailApplication::factory()
            ->count(3)
            ->provisionFailed() // This state should exist
            ->create();
        Log::info('Created 3 email applications with provisioning failed status.');

        EmailApplication::factory()
            ->count(3)
            ->cancelled() // This state should exist
            ->create();
        Log::info('Created 3 cancelled email applications.');

        // Assuming a 'deleted' state exists in your factory for soft deletes
        // If not, you can create them and then delete:
        // $deletedApps = EmailApplication::factory()->count(2)->create();
        // foreach ($deletedApps as $app) { $app->delete(); }
        // Or if a state ->deleted() exists:
        // EmailApplication::factory()->count(2)->deleted()->create();
        // Log::info('Created 2 deleted email applications.');

        EmailApplication::factory()
            ->count(5)
            ->forGroupEmail() // This state should exist
            ->create();
        Log::info('Created 5 email applications with group details.');

        Log::info('Email Applications seeding complete.');
    }
}
