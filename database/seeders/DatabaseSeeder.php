<?php

namespace Database\Seeders;

use App\Models\Equipment as AppEquipment;
use App\Models\User as AppUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
// Import all required seeders
// REMOVED: use Database\Seeders\EmailApplicationSeeder; // No longer needed
use Database\Seeders\HelpdeskCategorySeeder; // NEW
use Database\Seeders\HelpdeskPrioritySeeder; // NEW
use Database\Seeders\HelpdeskTicketSeeder;   // NEW
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The order of execution is critical to satisfy foreign key constraints.
     * 1. Master/lookup data (Roles, Departments, Positions, Grades, Helpdesk Categories/Priorities) is seeded first.
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
            RoleAndPermissionSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            GradesSeeder::class,
            LocationSeeder::class,
            EquipmentCategorySeeder::class,
            SubCategoriesSeeder::class,
            // NEW: Helpdesk Master Data
            HelpdeskCategorySeeder::class,
            HelpdeskPrioritySeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Core Master Data has been seeded.');


        // SECTION 2: USERS (Dependent on roles, departments, positions, grades)
        Log::channel($logChannel)->info('SECTION 2: Seeding Users...');
        $this->call([
            AdminUserSeeder::class, // Creates primary admin and core users
            UserSeeder::class,      // Creates additional general users
        ]);
        Log::channel($logChannel)->info('✅ Users have been seeded.');

        // SECTION 3: CORE ASSETS & HR STRUCTURES (Dependent on locations, categories, users)
        Log::channel($logChannel)->info('SECTION 3: Seeding Core Assets & HR Structures...');
        $this->call([
            EquipmentSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Core Assets & HR Structures have been seeded.');


        // SECTION 4: Seeding Sample Transactional Data (Loan Applications, Loan Transactions, Approvals, and NEW Helpdesk Tickets)
        Log::channel($logChannel)->info('SECTION 4: Seeding Sample Transactional Data...');
        if (AppUser::count() > 0 && AppEquipment::count() > 0) {
            $this->call([
                // REMOVED: EmailApplicationSeeder::class,
                LoanApplicationSeeder::class,
                LoanTransactionSeeder::class,
                ApprovalSeeder::class,
                // NEW: Helpdesk Tickets
                HelpdeskTicketSeeder::class,
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
