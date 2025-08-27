<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Seeds the departments table with MOTAC organizational structure.
 *
 * Creates a mix of headquarters and state branch departments with realistic
 * Malaysian government department names and structure.
 */
class DepartmentSeeder extends Seeder
{
    /**
     * Run the department seeder.
     *
     * Ensures at least one user exists for audit fields, seeds predefined
     * MOTAC departments, and adds additional random departments.
     */
    public function run(): void
    {
        Log::info('Starting Department seeding...');

        // Ensure at least one user exists for audit fields before creating departments
        $this->ensureAuditUserExists();

        // Disable foreign key checks before truncating
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all child tables referencing departments
        $childTables = [
            'equipment',
            'loan_applications',
            'loan_transactions',
            'helpdesk_tickets',
            // Add other tables with department_id FK as needed
        ];
        foreach ($childTables as $table) {
            if (\Schema::hasTable($table)) {
                \DB::table($table)->truncate();
            }
        }

        // Now truncate departments
        Department::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create predefined MOTAC departments
        $this->createMotacDepartments();

        // Create additional random departments for testing
        $this->createAdditionalDepartments();

        Log::info('Department seeding completed successfully.');
    }

    /**
     * Ensure at least one user exists for audit fields.
     * Creates a system audit user if no users exist in the database.
     */
    private function ensureAuditUserExists(): void
    {
        if (User::count() === 0) {
            Log::info('No users found. Creating audit user for department seeding...');

            // Create a simple audit user without complex relationships.
            // Use only valid enum value for 'status' (e.g. 'inactive').
            User::create([
                'name'                  => 'Audit User (DeptSeeder)',
                'email'                 => 'audit-deptseeder@motac.local',
                'email_verified_at'     => now(),
                'password'              => bcrypt('password'),
                'title'                 => 'tuan',
                'identification_number' => '999999999999',
                'passport_number'       => strtoupper(fake()->bothify('??########')),
                'status'                => 'inactive', // Use a valid ENUM value for status!
            ]);

            Log::info('Audit user created successfully.');
        }
    }

    /**
     * Create predefined MOTAC departments with realistic structure.
     */
    private function createMotacDepartments(): void
    {
        // Get a user ID for audit fields (first user is always present due to ensureAuditUserExists)
        $auditUserId = User::first()->id;

        // Define realistic MOTAC department structure
        $motacDepartments = [
            // Headquarters Departments
            ['name' => 'Bahagian Pentadbiran', 'code' => 'BP', 'branch_type' => 'headquarters', 'description' => 'Menguruskan hal-hal pentadbiran am dan sumber manusia'],
            ['name' => 'Bahagian Kewangan', 'code' => 'BK', 'branch_type' => 'headquarters', 'description' => 'Menguruskan kewangan dan belanjawan kementerian'],
            ['name' => 'Bahagian Kebudayaan', 'code' => 'BKB', 'branch_type' => 'headquarters', 'description' => 'Membangun dan mempromosikan kebudayaan Malaysia'],
            ['name' => 'Bahagian Kesenian', 'code' => 'BKS', 'branch_type' => 'headquarters', 'description' => 'Membangun industri kesenian tempatan'],
            ['name' => 'Bahagian Pelancongan', 'code' => 'BPL', 'branch_type' => 'headquarters', 'description' => 'Mempromosikan pelancongan Malaysia'],
            ['name' => 'Unit Teknologi Maklumat', 'code' => 'UTM', 'branch_type' => 'headquarters', 'description' => 'Menguruskan infrastruktur dan sistem ICT'],
            ['name' => 'Unit Komunikasi Korporat', 'code' => 'UKK', 'branch_type' => 'headquarters', 'description' => 'Menguruskan komunikasi dan perhubungan awam'],
            ['name' => 'Unit Perancangan Strategik', 'code' => 'UPS', 'branch_type' => 'headquarters', 'description' => 'Perancangan strategik dan dasar kementerian'],

            // State Branch Departments (examples)
            ['name' => 'Jabatan MOTAC Selangor', 'code' => 'JMSEL', 'branch_type' => 'state', 'description' => 'Pejabat negeri MOTAC di Selangor'],
            ['name' => 'Jabatan MOTAC Johor', 'code' => 'JMJOH', 'branch_type' => 'state', 'description' => 'Pejabat negeri MOTAC di Johor'],
            ['name' => 'Jabatan MOTAC Pulau Pinang', 'code' => 'JMPPG', 'branch_type' => 'state', 'description' => 'Pejabat negeri MOTAC di Pulau Pinang'],
            ['name' => 'Jabatan MOTAC Sabah', 'code' => 'JMSAB', 'branch_type' => 'state', 'description' => 'Pejabat negeri MOTAC di Sabah'],
            ['name' => 'Jabatan MOTAC Sarawak', 'code' => 'JMSRW', 'branch_type' => 'state', 'description' => 'Pejabat negeri MOTAC di Sarawak'],
        ];

        foreach ($motacDepartments as $dept) {
            Department::create([
                'name'                  => $dept['name'],
                'code'                  => $dept['code'],
                'branch_type'           => $dept['branch_type'],
                'description'           => $dept['description'],
                'is_active'             => true,
                'head_of_department_id' => null, // Can set later if needed
                'created_by'            => $auditUserId,
                'updated_by'            => $auditUserId,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }

        Log::info('Created ' . count($motacDepartments) . ' predefined MOTAC departments.');
    }

    /**
     * Create additional random departments for testing purposes.
     */
    private function createAdditionalDepartments(): void
    {
        // Create 10 additional random departments using the factory
        Department::factory()
            ->count(10)
            ->create();

        Log::info('Created 10 additional random departments.');
    }
}
