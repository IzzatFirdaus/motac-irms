[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<p align="center">
  <a href="https://github.com/IzzatFirdaus/motac-irms">
    <h1 align="center">MOTAC IRMS</h1>
  </a>
  <h2 align="center">Sistem Pengurusan Sumber Terintegrasi MOTAC</h2>
  <p align="center">
    Sistem berpusat untuk mengurus Pinjaman Peralatan ICT dan Sistem Helpdesk/Tiket di Kementerian Pelancongan, Seni dan Budaya Malaysia.<br />
    Berdasarkan struktur templat amralsaleeh/HRMS dan ditambah baik untuk keperluan operasi MOTAC.<br /><br />
    <a href="https://github.com/IzzatFirdaus/motac-irms/issues">Lapor Isu</a>
    ·
    <a href="https://github.com/IzzatFirdaus/motac-irms/issues">Mohon Penambahbaikan</a>
  </p>
</p>
<br />

---

## Pengenalan

**Sistem Pengurusan Sumber Terintegrasi MOTAC (MOTAC IRMS)** ialah aplikasi web berasaskan Laravel yang direka untuk memusatkan, mengautomasikan, dan memperkemas proses operasi utama untuk Kementerian Pelancongan, Seni dan Budaya Malaysia.

Versi 4.0 memberi fokus kepada dua modul utama:
- **Pengurusan Pinjaman Peralatan ICT**
- **Pengurusan Helpdesk & Sokongan ICT**

MOTAC IRMS menyediakan peraturan perniagaan yang mantap, aliran kerja bersatu, serta pengalaman pengguna moden untuk meningkatkan kecekapan, keselamatan dan akauntabiliti.

---

## Ciri-ciri

- **Pengurusan Pinjaman Peralatan ICT** Mengurus permohonan, kelulusan, pengeluaran, penjejakan dan pemulangan peralatan ICT (laptop, projektor, dsb.) untuk kegunaan rasmi.

- **Sistem Helpdesk & Tiket** Mengurus tiket sokongan IT dari penciptaan dan penugasan hingga penyelesaian dan pelaporan, mempercepat proses sokongan untuk semua isu berkaitan ICT.

- **Pengurusan Data Bersatu** Menggabungkan data pengguna, permohonan, tiket sokongan, kelulusan, inventori peralatan dan notifikasi dalam satu pangkalan data selamat.

- **Aliran Kerja Automatik & Standard** Memperkemas proses permohonan/kelulusan, meminimumkan langkah manual dan beban pentadbiran untuk pinjaman serta tiket sokongan.

- **Kawalan Akses Berdasarkan Peranan (RBAC) & Keselamatan** Kebenaran terperinci untuk pengguna, penyokong, staf BPM dan pentadbir IT. Termasuk logik kelulusan berasaskan gred dan peranan standard.

- **Borang Dinamik dengan Livewire** Menyokong borang kompleks dan bersyarat untuk permohonan pinjaman serta tiket helpdesk.

- **Laporan & Notifikasi Masa Nyata** Menyediakan maklumat penggunaan sumber, status pinjaman, prestasi helpdesk serta menghantar notifikasi e-mel dan dalam aplikasi untuk kejadian penting.

- **Pengurusan Inventori Peralatan ICT** Menyimpan inventori terperinci dengan kategori, subkategori, lokasi fizikal dan status (sedia, sedang dipinjam, dalam penyelenggaraan).

- **Jejak Audit & Akauntabiliti** Merekod tindakan utama (created_by, updated_by) untuk ketelusan dan pematuhan di semua modul.

- **Penyetempatan & Sokongan Bahasa Melayu** Antara muka utama dalam Bahasa Melayu, dengan sokongan penukaran bahasa dan tarikh tempatan.

---

## Dibangunkan Dengan

- [Laravel](https://laravel.com) - Rangka kerja PHP moden
- [Livewire](https://livewire.laravel.com) - Antara muka dinamik untuk Laravel
- [Jetstream](https://jetstream.laravel.com/) - Autentikasi & pengurusan pasukan
- [Vuexy](https://pixinvent.com/demo/vuexy-laravel-admin-dashboard-template/landing/) - Templat dashboard pentadbir (integrasi melalui Jetstream)
- [Spatie Activitylog & Permissions](https://spatie.be/open-source) - Logging dan RBAC
- [Maatwebsite Excel](https://laravel-excel.com/) - Import/eksport data
- [Dompdf](https://github.com/barryvdh/laravel-dompdf) - Penjanaan PDF
- [Log Viewer](https://github.com/opcodesio/log-viewer) - Pengurusan log
- Dan banyak lagi (rujuk `composer.json`)

---

## Cara Memulakan

### Keperluan

- PHP 8.2 ke atas
- Composer
- MySQL

### Pemasangan

1. **Klon repositori:**
    ```bash
    git clone https://github.com/IzzatFirdaus/motac-irms
    ```
2. **Masuk ke folder projek:**
    ```bash
    cd motac-irms
    ```
3. **Pasang kebergantungan:**
    ```bash
    composer install
    ```
4. **Konfigurasi persekitaran:**
    - Salin `.env.example` ke `.env`
    - Edit `.env` untuk tetapkan:
        - Pangkalan data (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
        - `APP_URL` (URL aplikasi anda)
        - `APP_TIMEZONE` (`Asia/Kuala_Lumpur`)
        - Tetapan e-mel (`MAIL_MAILER`, dsb. mengikut `config/mail.php`)
5. **Jana kunci aplikasi:**
    ```bash
    php artisan key:generate
    ```
6. **Pautan storan:**
    ```bash
    php artisan storage:link
    ```
7. **Jalankan migrasi dan (opsyenal) semai data khusus MOTAC:**
    ```bash
    php artisan migrate --seed
    ```
    *Gunakan `--seed` jika anda mempunyai seeder khusus MOTAC untuk jabatan, gred, peranan, dsb.*
8. **Mulakan pelayan pembangunan:**
    ```bash
    php artisan serve
    ```
9. **Akses aplikasi:**
    - Layari `http://localhost:8000` atau `APP_URL` yang telah dikonfigurasi

### Akaun Pentadbir Asas (Contoh Pembangunan)

Pengguna pentadbir awal boleh dicipta melalui seeder atau pendaftaran khas.  
Jika menggunakan templat asas HRMS, kelayakan demo pembangunan mungkin:
```text
email: admin@demo.com
password: admin
```

*Ubah untuk penggunaan produksi! Kelayakan pentadbir sebenar harus diurus dengan selamat.*

-----

## Sumbangan

Sumbangan dialu-alukan daripada pembangun dan pengguna MOTAC yang sah.

  - Gunakan [penjejak isu](https://github.com/IzzatFirdaus/motac-irms/issues) untuk permintaan ciri dan laporan pepijat.
  - Untuk kerentanan keselamatan, rujuk [`SECURITY.md`](SECURITY.md).

-----

## Hubungi

**Bahagian Pengurusan Maklumat (BPM)**  
Kementerian Pelancongan, Seni dan Budaya Malaysia  
Pautan Projek: [https://github.com/IzzatFirdaus/motac-irms]  
*E-mel rasmi akan disediakan oleh MOTAC/BPM.*

-----

## Lesen

Projek ini menggunakan Lesen MIT jika diadopsi daripada templat asas.  
Lihat [`LICENSE.md`](https://github.com/IzzatFirdaus/motac-irms/blob/master/LICENSE.md) untuk maklumat lanjut.  
MOTAC berhak menetapkan terma lesen khusus.
