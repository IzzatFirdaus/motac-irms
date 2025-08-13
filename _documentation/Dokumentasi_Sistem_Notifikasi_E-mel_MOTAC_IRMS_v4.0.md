# MOTAC IRMS: Sistem Notifikasi E-mel (v4.0)

Dokumen ini menerangkan pelaksanaan sistem notifikasi e-mel dalam Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS) v4.0. Dalam versi ini, "ciri e-mel" merujuk khusus kepada notifikasi automatik yang dihantar kepada pengguna untuk memaklumkan kejadian dan perubahan status dalam modul teras aplikasi. Dokumentasi ini telah diselaraskan dengan PRINSIP REKA BENTUK MYGOVEA (18 Prinsip).

---

## 1. Gambaran Umum

Sistem notifikasi e-mel berfungsi sebagai utiliti latar belakang untuk memastikan pengguna, pegawai penyokong, dan pentadbir sentiasa dimaklumkan mengenai perkembangan permohonan mereka. Ia bukan modul yang boleh diakses pengguna secara langsung, sebaliknya menjadi saluran komunikasi utama yang menyokong aliran kerja aplikasi.

Notifikasi dihantar secara automatik untuk kejadian penting dalam modul-modul berikut:

- **Pengurusan Pinjaman Peralatan ICT**
- **Pengurusan Helpdesk & Sokongan ICT**

Selaras dengan prinsip **Komunikasi** dan **Berpaksikan Rakyat**, sistem ini bertujuan untuk memastikan maklumat sampai kepada semua pihak berkepentingan dengan jelas dan tepat pada masanya.

---

## 2. Bagaimana Ia Berfungsi: Aliran Notifikasi

Proses notifikasi mengikut pola konsisten berasaskan kejadian di seluruh aplikasi, mematuhi prinsip **Paparan/Menu Jelas**, **Pencegahan Ralat**, dan **Kawalan Pengguna**.

1. **Tindakan Pengguna Berlaku:**  
   Pengguna atau pentadbir melakukan tindakan penting dalam sistem (cth: menghantar permohonan pinjaman, pentadbir menetapkan tiket helpdesk).

2. **Logik Lapisan Servis:**  
   Kelas servis berkaitan (cth: `LoanApplicationService`, `HelpdeskService`) memproses tindakan tersebut.

3. **Notifikasi Dihantar:**  
   Selepas tindakan berjaya dilaksanakan, servis memanggil `NotificationService` untuk menghantar notifikasi spesifik.

4. **Notifikasi Dihantar:**  
   Sistem menghantar notifikasi kepada pengguna melalui dua saluran:
   - **Pangkalan Data:** Notifikasi dalam aplikasi direkodkan dalam jadual notifikasi, boleh dilihat di dashboard pengguna.
   - **E-mel:** E-mel berbentuk rasmi dihantar ke alamat e-mel pengguna yang berdaftar menggunakan pemandu e-mel aplikasi (cth: SMTP).

```mermaid
graph TD
    A[Tindakan Pengguna (cth: Hantar Permohonan Pinjaman)] --> B{Lapisan Servis (cth: LoanApplicationService)}
    B --> C{NotificationService Dipanggil}
    C --> D[Bina Notifikasi Pangkalan Data]
    C --> E[Hantar Notifikasi E-mel]
    D --> F[Pengguna nampak amaran di Dashboard IRMS]
    E --> G[Pengguna terima e-mel di peti masuk]
```

---

## 3. Kejadian Utama Notifikasi Mengikut Modul

Jadual berikut menyenaraikan kejadian utama yang akan mencetuskan notifikasi e-mel, selaras dengan prinsip **Komunikasi**, **Pencegahan Ralat**, dan **Panduan & Dokumentasi**.

### 3.1 Modul Pinjaman Peralatan ICT

| Kelas Notifikasi                              | Kejadian Pencetus                              | Penerima              |
|----------------------------------------------- |----------------------------------------------- |-----------------------|
| `ApplicationSubmitted`                        | Pengguna menghantar permohonan pinjaman baru.  | Pemohon               |
| `ApplicationNeedsAction`                      | Permohonan sedia untuk kelulusan.              | Pegawai Penyokong     |
| `ApplicationApproved`                         | Permohonan pinjaman diluluskan.                | Pemohon               |
| `ApplicationRejected`                         | Permohonan pinjaman ditolak.                   | Pemohon               |
| `LoanApplicationReadyForIssuanceNotification` | Pinjaman diluluskan sedia untuk diproses.      | Staf BPM              |
| `EquipmentIssuedNotification`                 | Peralatan dikeluarkan kepada pemohon.          | Pemohon               |
| `EquipmentReturnedNotification`               | Peralatan yang dipinjam telah dipulangkan.     | Pemohon               |
| `EquipmentReturnReminderNotification`         | Tarikh akhir pemulangan hampir tiba.           | Pemohon               |
| `EquipmentOverdueNotification`                | Tarikh akhir pemulangan telah melepasi.        | Pemohon, Staf BPM     |
| `EquipmentIncidentNotification`               | Peralatan dilaporkan hilang/rosak.             | Staf BPM, pegawai berkaitan |

### 3.2 Modul Helpdesk & Sokongan ICT

| Kelas Notifikasi                  | Kejadian Pencetus                              | Penerima                |
|-----------------------------------|----------------------------------------------- |------------------------|
| `TicketCreatedNotification`       | Pengguna menghantar tiket helpdesk baru.       | Pengguna, Pasukan IT   |
| `TicketAssignedNotification`      | Tiket diberikan kepada agen IT.                | Agen IT                |
| `TicketStatusUpdatedNotification` | Status tiket berubah.                          | Pengguna               |
| `TicketCommentAddedNotification`  | Komen baru ditambah pada tiket.                | Pengguna, Agen IT      |
| `TicketClosedNotification`        | Tiket telah diselesaikan dan ditutup.          | Pengguna               |

---

## 4. Pelaksanaan Teknikal

Sistem notifikasi dibina menggunakan ciri standard Laravel dan kelas servis khusus, mematuhi prinsip **Teknologi Bersesuaian**, **Seragam**, dan **Panduan & Dokumentasi**.

| Komponen               | Laluan / Contoh                                    | Tujuan                                                                 |
|------------------------|----------------------------------------------------|------------------------------------------------------------------------|
| Notification Service   | `app/Services/NotificationService.php`             | Kelas servis pusat untuk penghantaran semua notifikasi.                |
| Notification Classes   | `app/Notifications/`                               | Setiap kejadian notifikasi ada kelas sendiri (cth: `App\Notifications\ApplicationApproved`) yang menentukan saluran penghantaran (e-mel, pangkalan data) dan format data. |
| Mailable Classes       | `app/Mail/`                                        | Untuk e-mel kompleks, kelas Mailable (cth: `App\Mail\EquipmentReturnReminder`) digunakan untuk membina kandungan e-mel dan lampiran. |
| Email Templates        | `resources/views/emails/`                          | Templat Blade yang menentukan struktur HTML dan kandungan e-mel keluar. |
| Configuration          | `config/mail.php`, `.env`                          | Fail `.env` dan `config/mail.php` digunakan untuk tetapan pemandu e-mel (SMTP, Mailgun) dan kelayakan. Untuk pembangunan, Mailtrap biasanya digunakan. |

---

## 5. Kesimpulan

Dalam versi 4.0 MOTAC IRMS, sistem e-mel adalah utiliti sokongan kritikal yang menumpukan kepada komunikasi. Ia memastikan semua pihak berkepentingan sentiasa dimaklumkan sepanjang aliran kerja pinjaman ICT dan helpdesk, memupuk ketelusan dan meningkatkan pengalaman pengguna tanpa menjadi modul aplikasi yang boleh diakses secara langsung.

Sistem ini selaras dengan PRINSIP REKA BENTUK MYGOVEA, mengutamakan komunikasi jelas, kawalan pengguna, pencegahan ralat serta dokumentasi lengkap untuk kelancaran operasi aplikasi kerajaan.

---
