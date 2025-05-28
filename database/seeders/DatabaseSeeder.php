<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Import specific seeders that will be called
// System Design (Rev 3) - Implies need for various lookup and initial data
// Make sure all these seeder classes exist in Database\Seeders namespace

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
        Log::channel($logChannel)->info('🚀 MEMULAKAN PROSES PENYEMAIAN PANGKALAN DATA (SEEDING) - Rev 3 🚀');
        Log::channel($logChannel)->info('================================================================');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Log::channel($logChannel)->info('Pemeriksaan kunci asing DIMATIKAN.');

        // SECTION 1: PERANAN, KEBENARAN & PENGGUNA PENTADBIR (Roles, Permissions & Admin Users)
        // Design Doc: 4.1 (Users table for created_by), 8.1 (RBAC)
        Log::channel($logChannel)->info('SEKSYEN 1: Menyemai Peranan, Kebenaran & Pengguna Pentadbir...');
        $this->call([
            RoleAndPermissionSeeder::class, // Seeds roles (Admin, BPM Staff, IT Admin, User, Approver, HOD) & permissions
            AdminUserSeeder::class,         // Creates predefined admin/system users with specific roles (e.g., 'Admin', 'IT Admin')
        ]);
        Log::channel($logChannel)->info('✅ Peranan, Kebenaran & Pengguna Pentadbir telah disemai.');

        // SECTION 2: DATA INDUK ORGANISASI (Organizational Master Data)
        // Design Doc: 4.1 (Departments, Grades, Positions)
        Log::channel($logChannel)->info('SEKSYEN 2: Menyemai Data Induk Organisasi...');
        $this->call([
            DepartmentSeeder::class,        // Seeds Bahagian/Unit MOTAC
            GradesSeeder::class,            // Seeds Gred Kakitangan
            PositionSeeder::class,          // Seeds Jawatan Kakitangan
            LocationSeeder::class,          // Seeds Lokasi Fizikal (untuk penempatan peralatan dll.)
            // CenterSeeder::class,         // Uncomment if 'Pusat Khidmat' or similar entities are used
        ]);
        Log::channel($logChannel)->info('✅ Data Induk Organisasi telah disemai.');

        // SECTION 3: PENGGUNA AM (General Users) - Optional, but useful for development/testing
        // Depends on Roles, Departments, Grades, Positions
        Log::channel($logChannel)->info('SEKSYEN 3: Menyemai Pengguna Am (jika UserSeeder dipanggil)...');
        $this->call(UserSeeder::class); // Creates a batch of general users for testing with various roles
        Log::channel($logChannel)->info('✅ Pengguna Am telah disemai.');


        // SECTION 4: DATA INDUK PERALATAN ICT (ICT Equipment Master Data)
        // Design Doc: 4.3 (EquipmentCategory, SubCategory)
        Log::channel($logChannel)->info('SEKSYEN 4: Menyemai Data Induk Peralatan ICT...');
        $this->call([
            EquipmentCategorySeeder::class, // Seeds Kategori Peralatan (e.g., Komputer Riba, Projektor)
            SubCategoriesSeeder::class,     // Seeds Sub-Kategori Peralatan (linked to EquipmentCategory)
        ]);
        Log::channel($logChannel)->info('✅ Data Induk Peralatan ICT telah disemai.');

        // SECTION 5: ASET PERALATAN ICT (ICT Equipment Assets)
        // Design Doc: 4.3 (Equipment table)
        // Depends on Users (for created_by), Departments, Locations, EquipmentCategories, SubCategories
        Log::channel($logChannel)->info('SEKSYEN 5: Menyemai Aset Peralatan ICT...');
        $this->call([
            EquipmentSeeder::class,         // Seeds rekod peralatan ICT (requires robust factory)
        ]);
        Log::channel($logChannel)->info('✅ Aset Peralatan ICT telah disemai.');

        // SECTION 6: DATA CONTOH TRANSAKSI (Sample Transactional Data) - Optional, for testing workflows
        // Design Doc: 4.2 (EmailApplications), 4.3 (LoanApplications, etc.), 4.4 (Approvals)
        // These would depend heavily on Users and Equipment already being seeded.
        Log::channel($logChannel)->info('SEKSYEN 6: Menyemai Data Contoh Transaksi (Komen keluar secara lalai)...');
        /*
        $this->call([
            EmailApplicationSeeder::class,  // Seeds contoh permohonan emel
            LoanApplicationSeeder::class,   // Seeds contoh permohonan pinjaman ICT
            // Further seeders for items within these applications, transactions, and approvals
            // LoanApplicationItemSeeder::class,
            // LoanTransactionSeeder::class, // (and LoanTransactionItemSeeder if separate)
            // ApprovalSeeder::class, // Polymorphic approvals for samples
        ]);
        Log::channel($logChannel)->info('✅ Data Contoh Transaksi telah disemai (jika tidak dikomen keluar).');
        */

        // SECTION 7: TETAPAN SISTEM & UTILITI (System Settings & Utilities)
        // Design Doc: 3.1 (Settings model), 4.4 (Notifications custom table)
        Log::channel($logChannel)->info('SEKSYEN 7: Menyemai Tetapan Sistem & Utiliti...');
        $this->call([
            SettingsSeeder::class,          // Seeds tetapan aplikasi awal
            // NotificationSeeder::class,   // Seeds contoh notifikasi (custom table)
            // ImportSeeder::class,         // Seeds contoh rekod import (if Import model is used)
            // ChangelogSeeder::class,      // If changelogs are maintained and seeded
        ]);
        Log::channel($logChannel)->info('✅ Tetapan Sistem & Utiliti telah disemai.');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel($logChannel)->info('Pemeriksaan kunci asing DIAKTIFKAN SEMULA.');

        Log::channel($logChannel)->info('================================================================');
        Log::channel($logChannel)->info('🎉 PROSES PENYEMAIAN PANGKALAN DATA (SEEDING) TELAH SELESAI! 🎉');
        Log::channel($logChannel)->info('================================================================');
    }
}
