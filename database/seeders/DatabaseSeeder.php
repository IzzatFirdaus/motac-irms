<?php

namespace Database\Seeders;

use App\Models\Equipment as AppEquipment;
use App\Models\User as AppUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Master seeder for the MOTAC ICT Loan HRMS system.
 * Seeds all essential lookup, user, asset, transaction, and system tables with
 * realistic and inter-related data in the correct dependency order.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The order of execution is critical to satisfy foreign key constraints.
     * 1. Master/lookup data (Roles, Departments, Positions, Grades, Helpdesk Categories/Priorities) is seeded first.
     * 2. Users that depend on this master data are seeded next.
     * 3. Core assets/equipment (depend on categories, locations, users).
     * 4. Transactional data (depends on users/equipment).
     * 5. Settings and notifications.
     */
    public function run(): void
    {
        // Temporarily allow lazy loading to avoid errors in seeders
        $originalPreventLazyLoading = Model::preventsLazyLoading();
        Model::preventLazyLoading(false);

        $logChannel = 'stderr';

        // Visual log banner for seeding start
        Log::channel($logChannel)->info(str_repeat('=', 66));
        Log::channel($logChannel)->info('🚀 STARTING DATABASE SEEDING PROCESS - MOTAC ICT LOAN HRMS 🚀');
        Log::channel($logChannel)->info(str_repeat('=', 66));

        // Disable foreign key checks to avoid FK constraint issues during truncation/insertions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Log::channel($logChannel)->info('Foreign key checks DISABLED.');

        // SECTION 1: CORE MASTER DATA (Roles, Permissions, and Organizational Lookups)
        Log::channel($logChannel)->info('SECTION 1: Seeding Core Master Data...');
        $this->call([
            // Roles & permissions (if using Spatie or similar)
            RoleAndPermissionSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            GradesSeeder::class,
            LocationSeeder::class,
            EquipmentCategorySeeder::class,
            SubCategoriesSeeder::class,
            // Helpdesk master data
            HelpdeskCategorySeeder::class,
            HelpdeskPrioritySeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Core Master Data has been seeded.');

        // SECTION 2: USERS
        Log::channel($logChannel)->info('SECTION 2: Seeding Users...');
        $this->call([
            AdminUserSeeder::class,
            UserSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Users have been seeded.');

        // SECTION 3: ASSETS & HR STRUCTURES
        Log::channel($logChannel)->info('SECTION 3: Seeding Core Assets & HR Structures...');
        $this->call([
            EquipmentSeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ Core Assets & HR Structures have been seeded.');

        // SECTION 4: TRANSACTIONAL DATA (Loan Applications, Transactions, Approvals, Helpdesk Tickets)
        Log::channel($logChannel)->info('SECTION 4: Seeding Sample Transactional Data...');
        // Only seed transactions if user and equipment data exist
        if (AppUser::count() > 0 && AppEquipment::count() > 0) {
            $this->call([
                LoanApplicationSeeder::class,
                LoanTransactionSeeder::class,
                ApprovalSeeder::class,
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

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel($logChannel)->info('Foreign key checks ENABLED.');

        // Visual log banner for seeding end
        Log::channel($logChannel)->info(str_repeat('=', 66));
        Log::channel($logChannel)->info('🎉 DATABASE SEEDING PROCESS COMPLETED! (MOTAC ICT LOAN HRMS) 🎉');
        Log::channel($logChannel)->info(str_repeat('=', 66));

        // Restore previous lazy loading setting
        Model::preventLazyLoading($originalPreventLazyLoading);
    }
}
