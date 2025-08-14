<?php

namespace Database\Seeders;

use App\Models\Equipment as AppEquipment;
use App\Models\User as AppUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
// Import all required seeders

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The order of execution is critical to satisfy foreign key constraints.
     * 1. Master/lookup data (Roles, Departments, Positions, Grades) is seeded first.
     * 2. Users that depend on this master data are seeded next.
     * 3. Transactional data that depends on users and equipment is seeded last.
     */
    public function run(): void
    {
        $originalPreventLazyLoading = Model::preventsLazyLoading();
        Model::preventLazyLoading(false);
        $logChannel = 'stderr';

        Log::channel($logChannel)->info('================================================================');
        Log::channel($logChannel)->info('🚀 STARTING DATABASE SEEDING PROCESS - MOTAC RMS (Rev 3.5) 🚀');
        Log::channel($logChannel)->info('================================================================');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Log::channel($logChannel)->info('Foreign key checks DISABLED.');

        // SECTION 1: CORE MASTER DATA (Roles, Permissions, and Organizational Lookups)
        // These must run first as all other data depends on them.
        Log::channel($logChannel)->info('SECTION 1: Seeding Core Master Data...');
        $this->call([
            RoleAndPermissionSeeder::class, // Creates Roles (e.g., 'Admin', 'Approver')
            DepartmentSeeder::class,         // Creates Departments
            // MODIFIED: Corrected the order. Positions must exist before Grades can reference them.
            PositionSeeder::class,           // Creates Positions
            GradesSeeder::class,             // Creates Grades (e.g., 'F44', 'N19') which depend on Positions
            LocationSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Core Master Data has been seeded.');

        // SECTION 2: USERS (Administrative and General)
        // This runs after master data is available to ensure correct assignment of roles, grades, etc.
        Log::channel($logChannel)->info('SECTION 2: Seeding Users...');
        $this->call([
            AdminUserSeeder::class, // Creates key users with specific roles and high-level grades.
            UserSeeder::class,      // Creates general, randomized users for testing.
        ]);
        Log::channel($logChannel)->info('✅ Administrative and General Users have been seeded.');

        // SECTION 3: ICT EQUIPMENT MASTER DATA & ASSETS
        Log::channel($logChannel)->info('SECTION 3: Seeding ICT Equipment Data...');
        $this->call([
            EquipmentCategorySeeder::class,
            SubCategoriesSeeder::class,
            EquipmentSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ ICT Equipment Data has been seeded.');

        // SECTION 4: SAMPLE TRANSACTIONAL DATA (Optional, for testing workflows)
        Log::channel($logChannel)->info('SECTION 4: Seeding Sample Transactional Data...');
        if (AppUser::count() > 0 && AppEquipment::count() > 0) {
            $this->call([
                EmailApplicationSeeder::class,
                LoanApplicationSeeder::class,
                LoanTransactionSeeder::class,
                ApprovalSeeder::class,
            ]);
            Log::channel($logChannel)->info('✅ Sample Transactional Data has been seeded.');
        } else {
            Log::channel($logChannel)->warning('⚠️ Skipping Sample Transactional Data: Prerequisite User or Equipment data missing.');
        }

        // SECTION 5: SYSTEM SETTINGS & UTILITIES
        Log::channel($logChannel)->info('SECTION 5: Seeding System Settings & Utilities...');
        $this->call([
            SettingsSeeder::class,
            NotificationSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ System Settings & Utilities have been seeded.');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel($logChannel)->info('Foreign key checks ENABLED.');

        Log::channel($logChannel)->info('================================================================');
        Log::channel($logChannel)->info('🎉 DATABASE SEEDING PROCESS COMPLETED! (MOTAC RMS - Rev 3.5) 🎉');
        Log::channel($logChannel)->info('================================================================');

        Model::preventLazyLoading($originalPreventLazyLoading);
    }
}
