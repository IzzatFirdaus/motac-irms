<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Log::info('Starting Locations seeding (Revision 4 - Patched)...');

        // Disable foreign key checks to truncate the table
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated locations table.');

        // Find an admin user to associate with the created records
        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        // If no user exists, create one for auditing purposes
        if (! $auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (LocationSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for LocationSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in LocationSeeder.");
        }

        // A static list of essential locations to ensure they always exist
        $locations = [
            [
                'name' => 'MOTAC HQ - Aras G, Stor Utama ICT',
                'description' => 'Stor utama penyimpanan peralatan ICT di Ibu Pejabat MOTAC, Aras G.',
                'address' => 'Kementerian Pelancongan, Seni dan Budaya, Aras G, Presint 5',
                'city' => 'Putrajaya',
                'state' => 'WP Putrajaya',
                'country' => 'Malaysia',
                'postal_code' => '62200',
                'is_active' => true,
            ],
            [
                'name' => 'MOTAC HQ - Aras 10, Bilik Server Utama',
                'description' => 'Lokasi selamat untuk server utama dan peralatan rangkaian di Ibu Pejabat.',
                'address' => 'Kementerian Pelancongan, Seni dan Budaya, Aras 10, Presint 5',
                'city' => 'Putrajaya',
                'state' => 'WP Putrajaya',
                'country' => 'Malaysia',
                'postal_code' => '62200',
                'is_active' => true,
            ],
            [
                'name' => 'MOTAC HQ - Aras 18, Bahagian Pengurusan Maklumat',
                'description' => 'Ruang pejabat Bahagian Pengurusan Maklumat di Aras 18.',
                'address' => 'Kementerian Pelancongan, Seni dan Budaya, Aras 18, Presint 5',
                'city' => 'Putrajaya',
                'state' => 'WP Putrajaya',
                'country' => 'Malaysia',
                'postal_code' => '62200',
                'is_active' => true,
            ],
            [
                'name' => 'Pejabat MOTAC Negeri Perak - Pejabat Am',
                'description' => 'Pejabat pentadbiran utama di MOTAC Negeri Perak, Ipoh.',
                'address' => 'Jalan Panglima Bukit Gantang Wahab',
                'city' => 'Ipoh',
                'state' => 'Perak',
                'country' => 'Malaysia',
                'postal_code' => '30000',
                'is_active' => true,
            ],
            [
                'name' => 'Auditorium Kementerian',
                'description' => 'Auditorium utama untuk acara rasmi dan taklimat.',
                'address' => 'Kementerian Pelancongan, Seni dan Budaya, Aras 2, Presint 5',
                'city' => 'Putrajaya',
                'state' => 'WP Putrajaya',
                'country' => 'Malaysia',
                'postal_code' => '62200',
                'is_active' => true,
            ],
        ];

        Log::info('Creating specific MOTAC locations...');
        // Use firstOrCreate to prevent errors if the seeder is run multiple times
        foreach ($locations as $locationData) {
            Location::firstOrCreate(
                ['name' => $locationData['name']], // Unique key to check
                array_merge($locationData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }
        Log::info('Ensured specific MOTAC locations exist.');

        // Define a target for how many total locations you want
        $targetCount = 15; // Increased target slightly
        $currentCount = Location::count();

        // If we have fewer than the target, create more using a factory
        if ($currentCount < $targetCount) {
            $needed = $targetCount - $currentCount;
            Log::info("Attempting to create {$needed} additional random locations using a factory...");

            $createdCount = 0;
            // Loop to create the exact number of needed locations
            for ($i = 0; $i < $needed; $i++) {
                try {
                    // Use the factory to generate a new, unique location
                    // The unique() method on the faker instance in your factory is key here
                    Location::factory()->create([
                        'created_by' => $auditUserId,
                        'updated_by' => $auditUserId,
                        'is_active' => true,
                    ]);
                    $createdCount++;
                } catch (UniqueConstraintViolationException $e) {
                    // If the factory generates a duplicate name despite our efforts, log it and try again.
                    Log::warning('Location factory generated a duplicate name. Retrying with a new entry.');
                    $i--; // Decrement the counter to ensure we still create the target number of locations.
                }
            }
            Log::info("Successfully created {$createdCount} new random locations.");
        }

        Log::info('Locations seeding complete (Revision 4 - Patched).');
    }
}
