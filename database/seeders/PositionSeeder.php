<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder populates the positions table based on the supplementary document.
     * It should be run before the GradesSeeder.
     */
    public function run(): void
    {
        Log::info('Starting Position seeding (Revision 3.5)...');

        // To ensure clean seeding, we truncate the table first.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Position::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::info('Truncated positions table.');

        $adminUserForAudit = User::orderBy('id')->first();
        $auditUserId = $adminUserForAudit?->id;
        if ($auditUserId) {
            Log::info(sprintf('Using User ID %s for audit columns in PositionSeeder.', $auditUserId));
        }

        // Complete list of 65 positions from the MyMail form specification
        // The 'id' here corresponds to the legacy 'value' from the form, which is used
        // by the Grade's `class` attribute to establish a link.
        $positions = [
            ['id' => 1, 'name' => 'Menteri', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 2, 'name' => 'Timbalan Menteri', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 3, 'name' => 'Ketua Setiausaha', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 4, 'name' => 'Timbalan Ketua Setiausaha', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 5, 'name' => 'Setiausaha Bahagian', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 6, 'name' => 'Setiausaha Akhbar', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 7, 'name' => 'Setiausaha Sulit Kanan', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 8, 'name' => 'Setiausaha Sulit', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 9, 'name' => 'Pegawai Tugas-Tugas Khas', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 10, 'name' => 'Timbalan Setiausaha Bahagian', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 11, 'name' => 'Ketua Unit', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 12, 'name' => 'Pegawai Khas', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 13, 'name' => 'Pegawai Media', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 14, 'name' => 'Pengarah', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 15, 'name' => 'Timbalan Pengarah', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 16, 'name' => 'Penolong Pengarah', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 17, 'name' => 'Ketua Penolong Setiausaha Kanan (M)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 18, 'name' => 'Ketua Penolong Setiausaha (M)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 19, 'name' => 'Penolong Setiausaha Kanan (M)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 20, 'name' => 'Penolong Setiausaha (M)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 21, 'name' => 'Pegawai Teknologi Maklumat (F)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 22, 'name' => 'Pegawai Kebudayaan (B)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 23, 'name' => 'Penasihat Undang-Undang (L)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 24, 'name' => 'Pegawai Psikologi (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 25, 'name' => 'Akauntan (WA)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 26, 'name' => 'Pegawai Hal Ehwal Islam (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 27, 'name' => 'Pegawai Penerangan (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 28, 'name' => 'Jurutera (J)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 29, 'name' => 'Kurator (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 30, 'name' => 'Jurukur Bahan (J)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 31, 'name' => 'Arkitek (J)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 32, 'name' => 'Pegawai Arkib (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 33, 'name' => 'Juruaudit (W)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 34, 'name' => 'Perangkawan (E)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 35, 'name' => 'Pegawai Siasatan (P)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 36, 'name' => 'Penguasa Imigresen (KP)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 37, 'name' => 'Pereka (B)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 38, 'name' => 'Peguam Persekutuan (L)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 39, 'name' => 'Penolong Pegawai Teknologi Maklumat', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 40, 'name' => 'Penolong Pegawai hal Ehwal Islam (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 41, 'name' => 'Penolong Pegawai Undang-Undang (L)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 42, 'name' => 'Penolong Pegawai Teknologi Maklumat_2', 'created_by' => $auditUserId, 'updated_by' => $auditUserId], // Renamed to ensure uniqueness
            ['id' => 43, 'name' => 'Penolong Juruaudit', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 44, 'name' => 'Penolong Jurutera', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 45, 'name' => 'Penolong Pegawai Tadbir', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 46, 'name' => 'Penolong Pegawai Penerangan (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 47, 'name' => 'Penolong Pegawai Psikologi (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 48, 'name' => 'Penolong Pegawai Siasatan (P)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 49, 'name' => 'Penolong Pegawai Arkib (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 50, 'name' => 'Jurufotografi', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 51, 'name' => 'Penolong Penguasa Imigresen (KP)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 52, 'name' => 'Penolong Pustakawan (S)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 53, 'name' => 'Setiausaha Pejabat (N)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 54, 'name' => 'Pembantu Setiausaha Pejabat (N)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 55, 'name' => 'Pembantu Tadbir (Perkeranian/Operasi) (N)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 56, 'name' => 'Penolong Akauntan (W)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 57, 'name' => 'Pembantu Tadbir (Kewangan) (W)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 58, 'name' => 'Pembantu Operasi (N)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 59, 'name' => 'Pembantu Keselamatan (KP)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 60, 'name' => 'Juruteknik Komputer (FT)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 61, 'name' => 'Pemandu Kenderaan (H)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 62, 'name' => 'Pembantu Khidmat (H)', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 63, 'name' => 'MySTEP', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 64, 'name' => 'Pelajar Latihan Industri', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
            ['id' => 65, 'name' => 'Pegawai Imigresen', 'created_by' => $auditUserId, 'updated_by' => $auditUserId],
        ];

        // Use insert for better performance with a large array.
        Position::insert($positions);
        Log::info('Finished seeding '.count($positions).' positions.');
    }
}
