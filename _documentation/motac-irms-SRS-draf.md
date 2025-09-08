# DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)

**NAMA SISTEM:** Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS)

**NAMA AGENSI:** Kementerian Pelancongan, Seni dan Budaya (MOTAC)

**NAMA AGENSI INDUK:** [Isi jika ada]

**TARIKH DOKUMEN:** 2025-08-14

**VERSI DOKUMEN:** 1.0

---

## KETERANGAN DOKUMEN

Dokumen ini menerangkan spesifikasi keperluan sistem IRMS secara lengkap, merangkumi keperluan fungsi dan bukan fungsi, model data, pemodelan proses, pematuhan MYDS & MyGovEA, serta input untuk fasa pembangunan dan pelaksanaan.

---

## SEMAKAN DAN PENGESAHAN DOKUMEN

### Disemak Oleh

| Disemak Oleh     | Jawatan           | Tandatangan | Tarikh |
|------------------|-------------------|-------------|--------|
| [Nama Penyemak]  | [Jawatan Penyemak]|             | [Tarikh] |

### Disahkan Oleh

| Disahkan Oleh    | Jawatan           | Tandatangan | Tarikh |
|------------------|-------------------|-------------|--------|
| [Nama Pengesah]  | [Jawatan Pengesah]|             | [Tarikh] |

---

## KAWALAN DOKUMEN

| No. Versi | Tarikh     | Ringkasan Pindaan | Penyedia        |
|-----------|------------|-------------------|-----------------|
| 1.0       | 2025-08-14 | Draf Awal         | [Nama Penyedia] |

---

## KANDUNGAN

1. AKRONIM & DEFINISI
2. SUMBER RUJUKAN
3. PENGENALAN SISTEM
4. AKTOR & PERANAN
5. KEHENDAK FUNGSI SISTEM
6. KEHENDAK BUKAN FUNGSI (NON-FUNCTIONAL)
7. MODEL DATA & ARKITEKTUR
8. PEMODELAN PROSES SISTEM
9. PEMODELAN USE CASE
10. UI/UX & PEMATUHAN MYDS
11. LOGIK NOTIFIKASI & KOMUNIKASI
12. KUALITI & UJIAN KEPATUHAN
13. LAMPIRAN

---

## 1. AKRONIM & DEFINISI

| Akronim | Keterangan                              |
|---------|-----------------------------------------|
| SRS     | Spesifikasi Keperluan Sistem            |
| IRMS    | Integrated Resource Management System   |
| MYDS    | Malaysia Government Design System       |
| BPM     | Bahagian Pengurusan Maklumat            |
| DFD     | Data Flow Diagram                      |
| ERD     | Entity Relationship Diagram             |
| API     | Application Programming Interface       |

| Terma/Istilah  | Definisi                          |
|----------------|-----------------------------------|
| Aktor          | Peranan yang berinteraksi dengan sistem |
| Entiti         | Objek data utama dalam sistem     |
| Tiket Helpdesk | Permohonan sokongan ICT           |
| Kelulusan      | Proses pengesahan permohonan      |
| Notifikasi     | Komunikasi automatik sistem       |

---

## 2. SUMBER RUJUKAN

- Manual Prosedur Kerja Pengurusan Sumber ICT
- Pekeliling Am Bil. 2 Tahun 2012
- Dokumentasi MYDS (Design & Develop Overview)
- Prinsip Reka Bentuk MyGovEA
- Borang Pinjaman Peralatan ICT MOTAC
- Dokumentasi Flow, Data, Reka Bentuk, Notifikasi IRMS

---

## 3. PENGENALAN SISTEM

### 3.1 Tujuan Sistem

Sistem IRMS membolehkan pengurusan digital untuk pinjaman peralatan ICT dan permohonan sokongan IT, menggantikan proses manual dan menyatukan data serta workflow dalam satu platform selamat dan modular.

### 3.2 Skop Sistem

- Modul Pinjaman Peralatan ICT: permohonan, kelulusan, pengeluaran, pemulangan.
- Modul Helpdesk ICT: tiket, penugasan, penyelesaian, penutupan.
- Infrastruktur berkongsi: pengurusan pengguna, kelulusan, notifikasi, audit.

---

## 4. AKTOR & PERANAN

| Aktor               | Peranan / Keterangan                                      |
|---------------------|----------------------------------------------------------|
| Pemohon             | Staf memohon pinjaman peralatan/tiket ICT                |
| Pegawai Penyokong   | Pegawai kelulusan, logik gred organisasi                 |
| Staf BPM            | Pengurusan pengeluaran/pemulangan aset                   |
| Agen IT             | Pengurusan tiket & sokongan ICT                          |
| Pentadbir Sistem    | Pengurusan tetapan, audit, laporan                       |

<!--
Setiap aktor dihubungkan kepada fungsi dan proses dalam sistem. Rujuk jadual pemadanan aktor dengan fungsi sistem di seksyen proses.
-->

---

## 5. KEHENDAK FUNGSI SISTEM

### 5.1 Modul Pinjaman Peralatan ICT

- **Permohonan:** Borang interaktif, auto-isi, simpan draf, hantar untuk kelulusan.
- **Kelulusan:** Rantai kelulusan mengikut gred, pelarasan kuantiti, komen kelulusan.
- **Pengeluaran:** Ketersediaan inventori, senarai semak aksesori, audit status, notifikasi.
- **Pemulangan:** Validasi keadaan, audit log, notifikasi peringatan/overdue.

### 5.2 Modul Helpdesk ICT

- **Cipta Tiket:** Borang kategori, keutamaan, lampiran fail.
- **Penugasan & Eskalasi:** Penilaian, penugasan agen, penetapan SLA.
- **Penyelesaian & Penutupan:** Komen, status, notifikasi, penutupan automatik/manual.

### 5.3 Infrastruktur Berkongsi

- **Kelulusan Polimorfik:** Digunakan pada pinjaman dan tiket.
- **Notifikasi:** E-mel rasmi dan dashboard in-app.
- **Audit Log:** Jejak tindakan pengguna dan perubahan data.

---

## 6. KEHENDAK BUKAN FUNGSI (NON-FUNCTIONAL)

- **Kebolehcapaian:** Pematuhan WCAG 2.1 AA, ARIA, skip link, tab order.
- **Keselamatan:** Autentikasi Fortify/Jetstream, kebenaran Spatie Permission, audit log, validasi input, perlindungan CSRF.
- **Prestasi:** Masa loading < 3s, caching, optimisasi database, Docker, CI/CD.
- **Responsif:** MYDS grid 12-8-4, mobile-first, touch target 48px min.
- **Browser & Peranti:** Chrome 90+, Firefox 88+, Safari 14+, Edge 90+, Mobile Safari iOS 14+.
- **Privasi & Pematuhan:** PDPA, log audit, kawalan akses peranan, pematuhan 18 Prinsip MyGovEA.

---

## 7. MODEL DATA & ARKITEKTUR

### 7.1 Entiti Utama & Struktur

- **Pengguna:** users, roles, permissions, departments, positions, grades.
- **Peralatan ICT:** equipment, equipment_categories, sub_categories, locations.
- **Pinjaman ICT:** loan_applications, loan_application_items, loan_transactions, loan_transaction_items.
- **Helpdesk:** helpdesk_tickets, helpdesk_categories, helpdesk_comments.
- **Kelulusan & Notifikasi:** approvals, notifications, settings.

<!--
Sisipkan ERD dan jadual data, rujuk kepada seksyen jadual data dalam dokumentasi.
-->

### 7.2 Data Dictionary

<!--
Placeholder: Jadual definisi atribut setiap entiti, peraturan bisnes, hubungan foreign key, constraint.
-->

---

## 8. PEMODELAN PROSES SISTEM

### 8.1 Context & Data Flow Diagrams

<!--
Placeholder: Rajah konteks sistem, DFD aliran permohonan pinjaman, kelulusan, pengeluaran, pemulangan, helpdesk.
-->

### 8.2 Proses Modul Pinjaman ICT

- Permohonan → Kelulusan → Pengeluaran → Pemulangan → Selesai

### 8.3 Proses Modul Helpdesk

- Cipta tiket → Penugasan → Penyelesaian → Penutupan → Arkib

---

## 9. PEMODELAN USE CASE

### 9.1 Modul Pinjaman Peralatan ICT

| Use Case ID | Nama Use Case             | Aktor           | Keterangan/Proses                |
|-------------|--------------------------|-----------------|----------------------------------|
| UC-01       | Permohonan Pinjaman      | Pemohon         | Isi borang, simpan draf, hantar  |
| UC-02       | Kelulusan Permohonan     | Pegawai Penyokong| Semak, lulus/tolak, komen        |
| UC-03       | Pengeluaran Peralatan    | Staf BPM        | Pilih item, audit, serah         |
| UC-04       | Pemulangan Peralatan     | Pemohon, BPM    | Hantar balik, semak, audit       |

### 9.2 Modul Helpdesk ICT

| Use Case ID | Nama Use Case        | Aktor        | Keterangan/Proses         |
|-------------|---------------------|--------------|---------------------------|
| UC-05       | Cipta Tiket         | Pemohon      | Borang, kategori, lampiran|
| UC-06       | Penugasan Tiket     | Admin IT     | Triage, assign, SLA       |
| UC-07       | Penyelesaian Tiket  | Agen IT      | Komen, status, notifikasi |
| UC-08       | Penutupan Tiket     | Agen IT      | Tutup, arkib, laporan     |

<!--
Sisipkan rajah use case untuk setiap modul.
-->

---

## 10. UI/UX & PEMATUHAN MYDS

### 10.1 Komponen MYDS Digunakan

- Butang: Primary, secondary, danger, icon only
- Borang: Input, select, textarea, date picker, file upload
- Kad: Statistik, panel status, summary list
- Navigasi: Masthead, sidebar, breadcrumb
- Status badge/tag: Menunggu, lulus, tolak, info
- Layout: Grid 12-8-4, responsive, spacing standard
- Alert dialog, callout, toast, tooltip, skip link

### 10.2 Branding dan Customization MOTAC

- Warna: MYDS blue (#2563EB), custom MOTAC brand
- Tipografi: Inter (body), Poppins (heading)
- Logo: MOTAC & BPM, header/footer

### 10.3 Kebolehcapaian

- Semua komponen, warna, dan navigasi mematuhi WCAG 2.1 AA & MYDS accessibility.

---

## 11. LOGIK NOTIFIKASI & KOMUNIKASI

### 11.1 Notifikasi Sistem

- Trigger: permohonan, kelulusan, pengeluaran, pemulangan, tiket, penyelesaian, penutupan.
- Penerima: pemohon, pegawai penyokong, staf BPM, agen IT.
- Saluran: dashboard in-app, e-mel rasmi (template MYDS), notifikasi pangkalan data.
- Jadual mapping kejadian, kelas notifikasi, template e-mel.

<!--
Sisipkan diagram aliran notifikasi untuk setiap proses utama.
-->

---

## 12. KUALITI & UJIAN KEPATUHAN

### 12.1 Senarai Semak Kualiti

- Warna, tipografi, komponen, grid, kebolehcapaian MYDS
- Ujian pelayar & peranti: Chrome, Firefox, Safari, Edge, Mobile Safari
- Ujian prestasi: masa loading, caching, optimisasi
- Ujian keselamatan: autentikasi, audit, validasi input
- Ujian kebolehcapaian: tab order, skip link, ARIA, contrast ratio

### 12.2 Kepatuhan Prinsip MyGovEA

- Lampiran: pemetaan keperluan sistem kepada semua 18 Prinsip MyGovEA

---

## 13. LAMPIRAN

- Borang rasmi permohonan pinjaman
- Flowchart, ERD, data dictionary, template e-mel, QA checklist, manual pengguna, compliance mapping

---

> **Nota:**  
> Dokumen ini disusun mengikut templat rasmi KRISA, mematuhi prinsip reka bentuk MyGovEA dan panduan MYDS untuk pembangunan sistem kerajaan digital MOTAC.

<!--
Setiap seksyen skeleton ini boleh diisi/diupdate dengan maklumat, rajah, jadual dan rujukan yang diambil daripada file dokumentasi sistem, reka bentuk, aliran kerja, jadual data, dan prinsip MyGovEA yang telah dikongsi.
-->
