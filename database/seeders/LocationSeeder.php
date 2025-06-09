<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Locations seeding (Revision 3)...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        Log::info('Truncated locations table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;

        if (! $auditUserId) {
            $adminUserForAudit = User::factory()->create(['name' => 'Audit User (LocationSeeder)']);
            $auditUserId = $adminUserForAudit->id;
            Log::info("Created a fallback audit user with ID {$auditUserId} for LocationSeeder.");
        } else {
            Log::info("Using User ID {$auditUserId} for audit columns in LocationSeeder.");
        }

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
                'name' => 'MOTAC HQ - Aras 18, Bahagian Pengurusan Maklumat', // Example, Aras from MyMail
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

        Log::info('Creating specific MOTAC locations (Revision 3)...');
        foreach ($locations as $locationData) {
            Location::firstOrCreate(
                ['name' => $locationData['name']], // Unique by name
                array_merge($locationData, [
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                ])
            );
        }
        Log::info('Ensured specific MOTAC locations exist.');

        $targetCount = 10; // Reduced target for more focused default locations
        if (Location::count() >= $targetCount) {
            Log::info('Locations seeding complete (Revision 3).');

            return;
        }
        $needed = $targetCount - Location::count();
        if ($needed > 0) {
            Location::factory()
                ->count($needed)
                ->create([
                    'created_by' => $auditUserId,
                    'updated_by' => $auditUserId,
                    'is_active' => true, // Ensure factory created locations are active
                ]);
            Log::info("Created {$needed} additional random locations using factory.");
        }

        Log::info('Locations seeding complete (Revision 3).');
    }
}
