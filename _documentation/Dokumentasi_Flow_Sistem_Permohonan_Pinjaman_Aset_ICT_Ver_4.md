# Aliran Sistem Permohonan Pinjaman ICT (v4.0)

Dokumen ini menerangkan keseluruhan aliran kerja untuk modul Pinjaman Peralatan ICT, salah satu daripada dua komponen utama dalam Sistem Pengurusan Sumber Terintegrasi MOTAC (v4.0). Setiap peringkat, pelaku utama dan komponen kod dinyatakan berdasarkan seni bina akhir sistem. Dokumentasi ini telah dikemaskini selaras dengan PRINSIP REKA BENTUK MYGOVEA (18 Prinsip).

---

## 1. Permulaan Permohonan & Pengesahan

Proses bermula apabila pemohon membuat permohonan baru. Permohonan boleh disimpan sebagai draf atau dihantar untuk kelulusan dengan pengesahan maklumat dan persetujuan terma.

- **Pelaku Utama:** Pemohon

- **Antara Muka Pengguna (UI):**
    - `App\Livewire\ResourceManagement\LoanApplication\ApplicationForm`: Komponen Livewire utama untuk borang permohonan dinamik.
    - `App\Livewire\ResourceManagement\MyApplications\Loan\Index`: Memaparkan senarai permohonan pemohon berserta status semasa.
    - `resources/views/livewire/resource-management/loan-application/application-form.blade.php`: Templat Blade untuk paparan borang permohonan.

- **Logik Teras:**
    - `app\Services\LoanApplicationService`: Logik perniagaan utama bagi penciptaan dan pengurusan permohonan.
    - `App\Http\Controllers\LoanApplicationController`: Mengurus logik backend untuk aliran bukan Livewire, terutamanya paparan perincian permohonan.
    - `app\Http\Requests\StoreLoanApplicationRequest` & `app\Http\Requests\UpdateLoanApplicationRequest`: Mengesahkan data yang masuk supaya semua medan diformatkan dengan betul.

- **Data & Perubahan Status:**
    - `App\Models\LoanApplication`: Rekod baru dicipta dengan status `draft`. Setelah dihantar, status ditukar kepada `pending_support` dan masa pengesahan pemohon direkodkan.
    - `App\Models\LoanApplicationItem`: Rekod dicipta untuk setiap jenis peralatan yang dimohon.

- **Kebenaran:**
    - `app\Policies\LoanApplicationPolicy`: Kaedah `create`, `update`, dan `submit` memastikan pengguna sah dan dibenarkan melakukan tindakan.

- **Notifikasi & Komunikasi:**
    - `app\Services\NotificationService`: Menghantar notifikasi kepada pemohon selepas penghantaran berjaya.
    - `App\Notifications\ApplicationSubmitted`: Notifikasi e-mel dan pangkalan data dihantar kepada pemohon.

### Penyesuaian Prinsip MYGOVEA:
- **Berpaksikan Rakyat:** Setiap peringkat mengambil kira keperluan dan pengalaman pengguna.
- **Antara Muka Minimalis dan Mudah:** Borang dan langkah mudah difahami.
- **Tetapan Lalai:** Medan diisi awal berdasarkan profil pengguna untuk kelancaran proses.
- **Tipografi:** Label, arahan dan status jelas untuk kebolehbacaan optimum.

---

## 2. Aliran Kelulusan Permohonan

Setelah dihantar, permohonan dirujuk kepada pegawai penyokong untuk semakan dan keputusan. Sistem menguatkuasakan syarat gred minimum untuk pegawai penyokong.

- **Pelaku Utama:** Pegawai Penyokong (Gred 41 ke atas mengikut konfigurasi)

- **Antara Muka Pengguna (UI):**
    - `App\Livewire\ResourceManagement\Approval\Dashboard`: UI pusat untuk pegawai membuat semakan dan tindakan kelulusan.
    - `resources/views/livewire/resource-management/approval/dashboard.blade.php`: Paparan Blade untuk dashboard pegawai.

- **Logik Teras:**
    - `app\Services\ApprovalService`: Mengurus logik utama bagi pencarian pegawai, penciptaan/kemas kini rekod kelulusan, dan pemprosesan keputusan.
    - `App\Http\Controllers\ApprovalController`: Kaedah `recordDecision()` memproses tindakan kelulusan/penolakan.

- **Data & Perubahan Status:**
    - `App\Models\Approval`: Rekod kelulusan dicipta/kemas kini dengan keputusan, komen dan masa.
    - `App\Models\LoanApplication`: Status permohonan ditukar kepada `approved` atau `rejected`.
    - `App\Models\LoanApplicationItem`: Medan `quantity_approved` boleh dikemas kini oleh pegawai.

- **Kebenaran:**
    - `app\Policies\LoanApplicationPolicy`: Kaedah `recordDecision` memastikan pengguna ialah pegawai yang sah.
    - `app\Policies\ApprovalPolicy`: Kaedah `update` mengawal tindakan ke atas rekod Approval.

- **Notifikasi & Komunikasi:**
    - `App\Notifications\ApplicationNeedsAction`: Notifikasi kepada pegawai bertugas.
    - `App\Notifications\ApplicationApproved` / `App\Notifications\ApplicationRejected`: Notifikasi keputusan kepada pemohon.
    - `App\Notifications\LoanApplicationReadyForIssuanceNotification`: Notifikasi kepada staf BPM bahawa permohonan telah diluluskan.

### Penyesuaian Prinsip MYGOVEA:
- **Struktur Hierarki:** Proses kelulusan bertingkat dan jelas.
- **Komunikasi:** Notifikasi dan status dihantar kepada pihak berkaitan.
- **Seragam:** Proses kelulusan dan paparan konsisten di semua modul.

---

## 3. Proses Pengeluaran Peralatan

Permohonan yang diluluskan diproses oleh pasukan BPM, memilih aset spesifik dari inventori dan mengeluarkan peralatan kepada pemohon.

- **Pelaku Utama:** Staf BPM

- **Antara Muka Pengguna (UI) & Logik Teras:**
    - `App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance`: Komponen Livewire utama untuk proses pengeluaran.
    - `app\Services\LoanTransactionService`: Kaedah `processNewIssue()` mengurus penciptaan transaksi pengeluaran, kemas kini status peralatan dan pengaitan aset.
    - `resources/views/loan_transactions/issue.blade.php`: Paparan utama yang memuatkan komponen Livewire.

- **Data & Perubahan Status:**
    - `App\Models\LoanTransaction`: Rekod baru dengan `type = 'issue'`, menyimpan butiran pegawai dan senarai semak aksesori.
    - `App\Models\LoanTransactionItem`: Rekod pengaitan nombor siri peralatan dengan transaksi.
    - `App\Models\Equipment`: Status aset dikemas kini kepada `on_loan`.
    - `App\Models\LoanApplication`: Status ditukar kepada `issued` atau `partially_issued`.

- **Kebenaran:**
    - `app\Policies\LoanTransactionPolicy`: Kaedah `createIssue` memastikan hanya staf BPM yang sah boleh melaksanakan tindakan.

- **Notifikasi & Komunikasi:**
    - `App\Notifications\EquipmentIssuedNotification`: Notifikasi kepada pemohon bahawa peralatan telah dikeluarkan.

### Penyesuaian Prinsip MYGOVEA:
- **Kandungan Terancang:** Senarai semak dan data pengeluaran dikendalikan secara teratur.
- **Fleksibel:** Proses boleh disesuaikan mengikut keperluan inventori.
- **Pencegahan Ralat:** Validasi dan senarai semak elak kesilapan pengeluaran.

---

## 4. Proses Pemulangan Peralatan

Pemohon atau pegawai yang ditetapkan memulangkan peralatan. Staf BPM akan memeriksa peralatan berdasarkan senarai semak asal, merekod keadaan dan status pemulangan.

- **Pelaku Utama:** Pemohon/Pegawai Pemulangan, Staf BPM

- **Antara Muka Pengguna (UI) & Logik Teras:**
    - `App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn`: Komponen Livewire utama untuk urusan pemulangan.
    - `App\Http\Controllers\LoanTransactionController`: Memproses penghantaran dari komponen Livewire dan memaparkan perincian transaksi.
    - `app\Services\LoanTransactionService`: Kaedah `processExistingReturn()` untuk penciptaan transaksi pemulangan dan kemas kini status peralatan.
    - `resources/views/loan_transactions/return.blade.php`: Paparan utama untuk proses pemulangan.
    - `resources/views/livewire/resource-management/admin/bpm/process-return.blade.php`: Paparan spesifik untuk komponen Livewire.

- **Data & Perubahan Status:**
    - `App\Models\LoanTransaction`: Rekod baru dengan `type = 'return'`, menyimpan pegawai, senarai semak, nota, dan masa.
    - `App\Models\LoanTransactionItem`: Kemaskini status pemulangan dan keadaan setiap item.
    - `App\Models\Equipment`: Status aset dikemas kini kepada `available`, `under_maintenance` dan lain-lain.
    - `App\Models\LoanApplication`: Status ditukar kepada `returned` selepas semua item diproses.

- **Kebenaran:**
    - `app\Policies\LoanTransactionPolicy`: Kaedah `processReturn` memastikan hanya staf BPM boleh merekod pemulangan.

- **Notifikasi & Komunikasi:**
    - `app\Services\NotificationService`: Menghantar semua notifikasi berkaitan proses pemulangan.
    - `App\Notifications\EquipmentReturnedNotification`: Notifikasi kepada pemohon bahawa pemulangan telah diproses.
    - `App\Notifications\EquipmentReturnReminderNotification`: Notifikasi peringatan sebelum tarikh akhir pemulangan.
    - `App\Notifications\EquipmentOverdueNotification`: Notifikasi jika lewat pemulangan.
    - `App\Notifications\EquipmentIncidentNotification`, `EquipmentLostNotification.php`, `EquipmentDamagedNotification.php`: Pemberitahuan kepada pihak berkaitan jika item rosak/hilang.

- **Mailables & Email Views:**
    - Kelas Mailables (cth: `App\Mail\EquipmentReturnedNotification`) membina kandungan e-mel.
    - Templat e-mel disimpan dalam `resources/views/emails/`.

### Penyesuaian Prinsip MYGOVEA:
- **Komponen UI/UX:** Borang dan paparan pemulangan konsisten, mudah difahami.
- **Pencegahan Ralat:** Senarai semak dan pengesahan keadaan peralatan.
- **Kandungan Terancang:** Nota keadaan dan rekod pemulangan jelas.

---

## 5. Komponen Berkongsi & Infrastruktur

Komponen berikut digunakan sepanjang aliran pinjaman untuk memastikan fungsi teras dan seni bina konsisten.

- **Model Teras:**  
  `User.php`, `Department.php`, `Position.php`, dan `Grade.php` menyediakan konteks pengguna untuk pemohon, penyokong dan staf.

- **Pemerhati (Observers):**
  `app\Observers\BlameableObserver.php` mengisi medan `created_by`, `updated_by`, dan `deleted_by` pada model auditable seperti `LoanApplication` dan `LoanTransaction`.

- **Middleware:**
    - **Autentikasi:** `auth:sanctum` dan `config('jetstream.auth_session')`.
    - **Kebenaran:** `can:` untuk semakan polisi, peranan/kebenaran Spatie dan middleware khusus seperti `check.gradelevel`.
    - **Umum:** Middleware Laravel seperti `EncryptCookies`, `StartSession`, dan `VerifyCsrfToken`.

- **Routing:**
  Semua permintaan web diurus dalam `routes/web.php`.

- **Service Provider:**
    - `AppServiceProvider`: Mendaftar servis teras seperti `LoanApplicationService`, `LoanTransactionService`, dan `NotificationService`.
    - `AuthServiceProvider`: Mendaftar semua polisi model.
    - `EventServiceProvider`: Mendaftar pemerhati model.

- **Konfigurasi:**
  Fail `config/motac.php` menyimpan tetapan sistem seperti gred minimum penyokong dan senarai aksesori pinjaman.

- **Paparan & Pembantu Berkongsi:**
    - Komponen Blade boleh guna semula di `resources/views/components/` (cth: untuk lencana status).
    - `app/Helpers/Helpers.php` mengandungi fungsi utiliti yang digunakan merentasi modul.

### Penyesuaian Prinsip MYGOVEA:
- **Seragam & Fleksibel:** Seni bina modular, komponen boleh digunakan semula.
- **Panduan & Dokumentasi:** Kod, polisi dan tetapan didokumentasi bagi memudahkan pemeliharaan serta rujukan.
- **Teknologi Bersesuaian:** Penggunaan struktur dan servis Laravel yang optimum.

---

# Penutup

Dokumentasi aliran sistem pinjaman ICT telah diselaraskan sepenuhnya dengan 18 Prinsip Reka Bentuk MyGOVEA, memastikan aplikasi mesra pengguna, selamat, konsisten, mudah diselenggara dan mendukung keberkesanan operasi kerajaan digital MOTAC.
