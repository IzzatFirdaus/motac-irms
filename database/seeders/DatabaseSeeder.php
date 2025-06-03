<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

// Explicitly import seeders that are actively called
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\GradesSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\EquipmentCategorySeeder;
use Database\Seeders\SubCategoriesSeeder;
use Database\Seeders\EquipmentSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\UserSeeder;

// Import seeders that are now uncommented
use Database\Seeders\EmailApplicationSeeder;
use Database\Seeders\LoanApplicationSeeder;
use Database\Seeders\LoanTransactionSeeder; // Ensure this file exists
use Database\Seeders\ApprovalSeeder;        // Ensure this file exists
use Database\Seeders\NotificationSeeder;

// Import others if/when uncommented below:
// use Database\Seeders\CenterSeeder;
// use Database\Seeders\LoanApplicationItemSeeder; // Usually handled by LoanApplicationSeeder's factory
// use Database\Seeders\ImportSeeder;
// use Database\Seeders\ChangelogSeeder;
// use Database\Seeders\ContractsSeeder;
// use Database\Seeders\LeavesSeeder;
// use Database\Seeders\HolidaysSeeder;

// For checking User/Equipment counts if uncommenting parts of transactional data
use App\Models\User as AppUser;
use App\Models\Equipment as AppEquipment;


class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   * This method orchestrates the calling of other seeder classes.
   * The order is important to satisfy foreign key constraints,
   * aligned with MOTAC Integrated Resource Management System (Revision 3).
   */
  public function run(): void
  {
    $originalPreventLazyLoading = Model::preventsLazyLoading();
    Model::preventLazyLoading(false);

    $logChannel = 'stderr';

    Log::channel($logChannel)->info('================================================================');
    Log::channel($logChannel)->info('🚀 STARTING DATABASE SEEDING PROCESS - MOTAC RMS (Rev 3) 🚀');
    Log::channel($logChannel)->info('================================================================');

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Log::channel($logChannel)->info('Foreign key checks DISABLED.');

    // SECTION 1: ROLES, PERMISSIONS & ADMINISTRATIVE USERS
    Log::channel($logChannel)->info('SECTION 1: Seeding Roles, Permissions & Admin Users...');
    $this->call([
      RoleAndPermissionSeeder::class,
      AdminUserSeeder::class,
    ]);
    Log::channel($logChannel)->info('✅ Roles, Permissions & Admin Users have been seeded.');

    // SECTION 2: ORGANIZATIONAL MASTER DATA
    Log::channel($logChannel)->info('SECTION 2: Seeding Organizational Master Data...');
    $this->call([
      DepartmentSeeder::class,
      GradesSeeder::class,
      PositionSeeder::class,
      LocationSeeder::class,
      // CenterSeeder::class,
    ]);
    Log::channel($logChannel)->info('✅ Organizational Master Data has been seeded.');

    // SECTION 3: GENERAL USERS
    Log::channel($logChannel)->info('SECTION 3: Seeding General Users...');
    $this->call(UserSeeder::class);
    Log::channel($logChannel)->info('✅ General Users have been seeded.');


    // SECTION 4: ICT EQUIPMENT MASTER DATA (LOOKUPS)
    Log::channel($logChannel)->info('SECTION 4: Seeding ICT Equipment Master Data (Lookups)...');
    $this->call([
      EquipmentCategorySeeder::class,
      SubCategoriesSeeder::class,
    ]);
    Log::channel($logChannel)->info('✅ ICT Equipment Master Data (Lookups) have been seeded.');

    // SECTION 5: ICT EQUIPMENT ASSETS (MAIN DATA)
    Log::channel($logChannel)->info('SECTION 5: Seeding ICT Equipment Assets...');
    $this->call([
      EquipmentSeeder::class,
    ]);
    Log::channel($logChannel)->info('✅ ICT Equipment Assets have been seeded.');

    // SECTION 6: SAMPLE TRANSACTIONAL DATA (Optional, for testing workflows)
    Log::channel($logChannel)->info('SECTION 6: Seeding Sample Transactional Data...');
    // It's good practice to ensure prerequisite data exists before seeding transactions
    if (AppUser::count() > 0 && AppEquipment::count() > 0) {
        $this->call([
            EmailApplicationSeeder::class,
            LoanApplicationSeeder::class,
            // LoanApplicationItemSeeder::class, // Usually handled by LoanApplicationFactory's afterCreating hook
            LoanTransactionSeeder::class,     // Ensure LoanTransactionSeeder.php exists and uses its factory
            ApprovalSeeder::class,            // Ensure ApprovalSeeder.php exists and uses its factory
        ]);
        Log::channel($logChannel)->info('✅ Sample Transactional Data has been seeded.');
    } else {
       Log::channel($logChannel)->warning('⚠️ Skipping Sample Transactional Data: Prerequisite User or Equipment data missing.');
    }


    // SECTION 7: SYSTEM SETTINGS & UTILITIES
    Log::channel($logChannel)->info('SECTION 7: Seeding System Settings & Utilities...');
    $this->call([
      SettingsSeeder::class,
      NotificationSeeder::class,   // Factory exists
      // ImportSeeder::class,
      // ChangelogSeeder::class,
    ]);
    Log::channel($logChannel)->info('✅ System Settings & Utilities have been seeded.');

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    Log::channel($logChannel)->info('Foreign key checks ENABLED.');

    Log::channel($logChannel)->info('================================================================');
    Log::channel($logChannel)->info('🎉 DATABASE SEEDING PROCESS COMPLETED! (MOTAC RMS - Rev 3) 🎉');
    Log::channel($logChannel)->info('================================================================');

    Model::preventLazyLoading($originalPreventLazyLoading);
  }
}
