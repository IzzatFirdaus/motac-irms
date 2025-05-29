<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Import seeders that will be definitely called for clarity
// Many are commented out as per your original file, to be enabled as needed.
use Database\Seeders\RoleAndPermissionSeeder; // Called
use Database\Seeders\AdminUserSeeder;       // Called
use Database\Seeders\DepartmentSeeder;      // Called
use Database\Seeders\GradesSeeder;          // Called
use Database\Seeders\PositionSeeder;        // Called
use Database\Seeders\LocationSeeder;        // Called
use Database\Seeders\EquipmentCategorySeeder; // Called
use Database\Seeders\SubCategoriesSeeder;   // Called
use Database\Seeders\EquipmentSeeder;       // Called
use Database\Seeders\SettingsSeeder;        // Called
use Database\Seeders\UserSeeder;            // Optional, called if uncommented

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   * This method orchestrates the calling of other seeder classes.
   * The order is important to satisfy foreign key constraints.
   * Referenced Design: MOTAC Integrated Resource Management System (Revision 3)
   */
  public function run(): void
  {
    $logChannel = 'stderr'; // Use stderr for console visibility during seeding

    Log::channel($logChannel)->info('================================================================');
    Log::channel($logChannel)->info('🚀 STARTING DATABASE SEEDING PROCESS - Rev 3 🚀');
    Log::channel($logChannel)->info('================================================================');

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Log::channel($logChannel)->info('Foreign key checks DISABLED.');

    // SECTION 1: ROLES, PERMISSIONS & ADMIN USERS
    // Design Doc: 4.1 (Users table for created_by), 8.1 (RBAC)
    Log::channel($logChannel)->info('SECTION 1: Seeding Roles, Permissions & Admin Users...');
    $this->call([
      RoleAndPermissionSeeder::class, // Seeds roles (Admin, BPM Staff, IT Admin, User, Approver, HOD) & permissions
      AdminUserSeeder::class,         // Creates predefined admin/system users with specific roles (e.g., 'Admin', 'IT Admin')
    ]);
    Log::channel($logChannel)->info('✅ Roles, Permissions & Admin Users have been seeded.');

    // SECTION 2: ORGANIZATIONAL MASTER DATA
    // Design Doc: 4.1 (Departments, Grades, Positions)
    Log::channel($logChannel)->info('SECTION 2: Seeding Organizational Master Data...');
    $this->call([
      DepartmentSeeder::class,        // Seeds MOTAC Divisions/Units
      GradesSeeder::class,            // Seeds Staff Grades
      PositionSeeder::class,          // Seeds Staff Positions
      LocationSeeder::class,          // Seeds Physical Locations (for equipment placement, etc.)
      // CenterSeeder::class,         // Uncomment if 'Service Center' or similar entities are used
    ]);
    Log::channel($logChannel)->info('✅ Organizational Master Data has been seeded.');

    // SECTION 3: GENERAL USERS - Optional, but useful for development/testing
    // Depends on Roles, Departments, Grades, Positions
    Log::channel($logChannel)->info('SECTION 3: Seeding General Users (if UserSeeder is called)...');
    $this->call(UserSeeder::class); // Creates a batch of general users for testing with various roles
    Log::channel($logChannel)->info('✅ General Users have been seeded.');


    // SECTION 4: ICT EQUIPMENT MASTER DATA
    // Design Doc: 4.3 (EquipmentCategory, SubCategory)
    Log::channel($logChannel)->info('SECTION 4: Seeding ICT Equipment Master Data...');
    $this->call([
      EquipmentCategorySeeder::class, // Seeds Equipment Categories (e.g., Laptop, Projector)
      SubCategoriesSeeder::class,     // Seeds Equipment Sub-Categories (linked to EquipmentCategory)
    ]);
    Log::channel($logChannel)->info('✅ ICT Equipment Master Data has been seeded.');

    // SECTION 5: ICT EQUIPMENT ASSETS
    // Design Doc: 4.3 (Equipment table)
    // Depends on Users (for created_by), Departments, Locations, EquipmentCategories, SubCategories
    Log::channel($logChannel)->info('SECTION 5: Seeding ICT Equipment Assets...');
    $this->call([
      EquipmentSeeder::class,         // Seeds ICT equipment records (requires robust factory)
    ]);
    Log::channel($logChannel)->info('✅ ICT Equipment Assets have been seeded.');

    // SECTION 6: SAMPLE TRANSACTIONAL DATA - Optional, for testing workflows
    // Design Doc: 4.2 (EmailApplications), 4.3 (LoanApplications, etc.), 4.4 (Approvals)
    // These would depend heavily on Users and Equipment already being seeded.
    Log::channel($logChannel)->info('SECTION 6: Seeding Sample Transactional Data (Commented out by default)...');
    /*
        $this->call([
            EmailApplicationSeeder::class,  // Seeds sample email applications
            LoanApplicationSeeder::class,   // Seeds sample ICT loan applications
            // Further seeders for items within these applications, transactions, and approvals
            // LoanApplicationItemSeeder::class,
            // LoanTransactionSeeder::class, // (and LoanTransactionItemSeeder if separate)
            // ApprovalSeeder::class, // Polymorphic approvals for samples
        ]);
        Log::channel($logChannel)->info('✅ Sample Transactional Data has been seeded (if not commented out).');
        */

    // SECTION 7: SYSTEM SETTINGS & UTILITIES
    // Design Doc: 3.1 (Settings model), 4.4 (Notifications custom table)
    Log::channel($logChannel)->info('SECTION 7: Seeding System Settings & Utilities...');
    $this->call([
      SettingsSeeder::class,          // Seeds initial application settings
      // NotificationSeeder::class,   // Seeds sample notifications (custom table)
      // ImportSeeder::class,         // Seeds sample import records (if Import model is used)
      // ChangelogSeeder::class,      // If changelogs are maintained and seeded
    ]);
    Log::channel($logChannel)->info('✅ System Settings & Utilities have been seeded.');

    // HRMS SPECIFIC SEEDERS from your first DatabaseSeeder example (commented out as MOTAC scope is primary)
    // If these are needed, ensure their models and factories also align with any Revision 3 changes.
    /*
        Log::channel($logChannel)->info('SECTION HRMS: Seeding HRMS Specific Data (Commented out by default)...');
        $this->call([
            ContractsSeeder::class,
            LeavesSeeder::class,
            HolidaysSeeder::class,
            // EmployeesSeeder::class, // UserSeeder above might cover employees or be distinct
            // FingerprintSeeder::class,
            // AttendanceSeeder::class,
            // DiscountSeeder::class,
            // EmployeeLeaveSeeder::class,
            // TimelineSeeder::class,
            // CenterHolidaySeeder::class,
        ]);
        Log::channel($logChannel)->info('✅ HRMS Specific Data has been seeded (if not commented out).');
        */

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    Log::channel($logChannel)->info('Foreign key checks ENABLED.');

    Log::channel($logChannel)->info('================================================================');
    Log::channel($logChannel)->info('🎉 DATABASE SEEDING PROCESS COMPLETED! 🎉');
    Log::channel($logChannel)->info('================================================================');
  }
}
