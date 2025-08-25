# Spesifikasi Keperluan Bisnes (BRS)

## Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS)

### Modul: [Nama Modul] _(Isi jika BRS untuk modul tertentu)_

|                      |                                      |
|----------------------|--------------------------------------|
| **NAMA AGENSI**      | Kementerian Pelancongan, Seni dan Budaya (MOTAC) |
| **NAMA AGENSI INDUK**| [Isi jika ada]                       |
| **TARIKH DOKUMEN**   | 2025-08-14                           |
| **VERSI DOKUMEN**    | 1.0                                  |

---

## KETERANGAN DOKUMEN

Dokumen ini menerangkan keperluan bisnes dan pengguna bagi pembangunan Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS). Ia merangkumi skop bisnes, gambaran keseluruhan sistem, pemegang taruh, keperluan pengurusan bisnes, keperluan pengoperasian bisnes, pematuhan kepada MYDS & Prinsip MyGovEA, serta input untuk SRS.

---

## SEMAKAN DAN PENGESAHAN DOKUMEN

### Semakan Dokumen

| Disemak Oleh            | Jawatan      | Tandatangan | Tarikh Semakan |
|-------------------------|--------------|-------------|----------------|
| [Nama Pengurus Projek]  | [Jawatan]    |             | [Tarikh]       |
| [Nama SME]              | [Jawatan]    |             | [Tarikh]       |

### Pengesahan Dokumen

| Disahkan Oleh           | Jawatan      | Tandatangan | Tarikh Semakan |
|------------------------|--------------|-------------|----------------|
| [Nama Penasihat Projek]| [Jawatan]    |             | [Tarikh]       |
| [Nama Pemilik Projek]  | [Jawatan]    |             | [Tarikh]       |

---

## KAWALAN DOKUMEN

| No. Versi | Tarikh     | Ringkasan Pindaan                | Penyedia         |
|-----------|------------|----------------------------------|------------------|
| 1.0       | 2025-08-14 | Dokumen versi pertama            | [Nama Penyedia]  |

---

## KANDUNGAN

1. PENGENALAN
2. KEPERLUAN PENGURUSAN BISNES
3. PEMATUHAN MYDS & PRINSIP REKA BENTUK MYGOVEA
4. KEPERLUAN PENGOPERASIAN BISNES
5. KEPERLUAN BUKAN FUNGSI (NON-FUNCTIONAL)
6. SENIBINA DATA & ARKITEKTUR
7. DIAGRAM & JADUAL UTAMA
8. ALIRAN NOTIFIKASI & KOMUNIKASI
9. KUALITI & PEMATUHAN
10. LAMPIRAN

---

## SENARAI GAMBARAJAH

<!-- Placeholder for workflow diagrams, ERD, business architecture, process flows -->

---

## SENARAI JADUAL

<!-- Placeholder for tables: stakeholders, requirements mapping, data entities, process mapping -->

---

## AKRONIM

| Akronim | Keterangan                              |
|---------|-----------------------------------------|
| BRS     | Business Requirement Specification      |
| SME     | Subject Matter Expert                   |
| IRMS    | Integrated Resource Management System   |
| MOTAC   | Ministry of Tourism, Arts and Culture   |
| MYDS    | Malaysia Government Design System       |
| BPM     | Bahagian Pengurusan Maklumat            |

---

## SUMBER RUJUKAN

- Pekeliling Am Bilangan 2 Tahun 2012  
- Manual Prosedur Kerja Pengurusan Sumber ICT  
- Dokumentasi MYDS & MyGovEA  
- Dokumentasi Sistem MOTAC IRMS v4.0 dan v5.0  
- Borang Pinjaman Peralatan ICT MOTAC  
- Dokumentasi Flow Pinjaman & Helpdesk

---

# 1. PENGENALAN

## 1.1 Tujuan Bisnes

Sistem IRMS menyediakan platform digital untuk pengurusan pinjaman peralatan ICT dan sokongan helpdesk ICT, menggantikan proses manual dan modul legasi.

## 1.2 Skop Bisnes

- Pinjaman peralatan ICT: permohonan, kelulusan, pengeluaran, pemulangan.
- Pengurusan tiket helpdesk ICT: aduan, penugasan, penyelesaian, penutupan.
- Pengurusan data pengguna, inventori aset, kelulusan, notifikasi, audit.

## 1.3 Gambaran Keseluruhan Bisnes

Platform Laravel modular, berasaskan workflow automatik, dashboard berdasarkan peranan pengguna, notifikasi masa sebenar, pematuhan audit & privasi.

## 1.4 Senarai Pemegang Taruh

| Pemegang Taruh           | Keterangan                                              |
|--------------------------|--------------------------------------------------------|
| Pemohon                  | Staf yang memohon pinjaman/perkhidmatan ICT            |
| Pegawai Penyokong        | Pegawai kelulusan mengikut gred organisasi             |
| Staf BPM                 | Pengurusan pengeluaran/pemulangan aset                 |
| Agen IT                  | Pengurusan tiket & sokongan ICT                        |
| Pentadbir Sistem         | Pengurusan tetapan, audit, dan laporan                 |

---

# 2. KEPERLUAN PENGURUSAN BISNES

## 2.1 Matlamat dan Objektif

- Menyatukan pengurusan sumber ICT dan sokongan dalam satu sistem digital
- Memastikan kelulusan berasaskan hierarki gred
- Menyediakan audit trail dan privasi data
- Menyokong notifikasi automatik, laporan masa sebenar, dashboard peranan

## 2.2 Arkitektur Bisnes

<!-- Placeholder for business architecture diagrams and descriptions -->

## 2.3 Arkitektur Maklumat

<!-- Placeholder for data architecture, core entities, and relationships -->

---

# 3. PEMATUHAN MYDS & PRINSIP REKA BENTUK MYGOVEA

## 3.1 Senarai Pematuhan MYDS

- Komponen UI: Butang, borang, kad, navigasi, lencana status, panel
- Palet warna & tipografi: MYDS Blue, Inter, Poppins, kontras minimum 4.5:1
- Grid 12-8-4: Layout responsif untuk desktop, tablet, mobile
- Kebolehcapaian WCAG 2.1 AA: label, focus ring, skip link, ARIA

## 3.2 Pematuhan 18 Prinsip MyGovEA

- Berpaksikan Rakyat, Berpacukan Data, Kandungan Terancang, Teknologi Bersesuaian, dll.
- Lampiran: Senarai semak pemetaan setiap keperluan kepada prinsip

---

# 4. KEPERLUAN PENGOPERASIAN BISNES

## 4.1 Fungsi Bisnes Teras

### 4.1.1 Pinjaman Peralatan ICT

- Permohonan (borang interaktif, auto-isi profil)
- Kelulusan (rantai pegawai penyokong, validasi gred)
- Pengeluaran (senarai semak aksesori, audit status)
- Pemulangan (validasi keadaan, audit log, notifikasi peringatan)

### 4.1.2 Helpdesk ICT

- Cipta tiket (kategori, keutamaan)
- Penugasan & eskalasi (admin IT, agen IT)
- Penyelesaian & penutupan tiket (komen, status, notifikasi)

## 4.2 Model Proses Bisnes

<!-- Placeholder for workflow diagrams (mermaid, flowchart), proses kelulusan, aliran pengeluaran/pemulangan, aliran tiket helpdesk -->

---

# 5. KEPERLUAN BUKAN FUNGSI (NON-FUNCTIONAL)

## 5.1 Kebolehcapaian

- Pematuhan WCAG 2.1 AA
- Komponen UI dengan ARIA, skip link, tab order

## 5.2 Responsif & Mobile

- Menggunakan grid MYDS 12-8-4
- Touch target minimum 48px

## 5.3 Keselamatan

- Autentikasi (Fortify/Jetstream)
- Kebenaran berasaskan peranan/gred (Spatie Permission)
- Audit log penuh (BlameableObserver)
- Kawalan akses data, validasi input

## 5.4 Prestasi & Skalabiliti

- Caching, query optimisasi, Docker, CI/CD

## 5.5 Sokongan Peranti & Browser

- Chrome 90+, Firefox 88+, Safari 14+, Edge 90+, Mobile Safari iOS 14+

---

# 6. SENIBINA DATA & ARKITEKTUR

## 6.1 Entiti Utama

- Pengguna (users), jabatan (departments), jawatan (positions), gred (grades)
- Peralatan ICT (equipment, equipment_categories, sub_categories, locations)
- Pinjaman ICT (loan_applications, loan_application_items, loan_transactions, loan_transaction_items)
- Helpdesk (helpdesk_tickets, helpdesk_categories, helpdesk_comments)
- Kelulusan (approvals), notifikasi (notifications), tetapan (settings)

## 6.2 Hubungan & Mapping

- Approval polimorfik kepada permohonan/tiket
- Notifikasi kepada pengguna/events
- Mapping peranan, gred, kategori, dan lokasi

---

# 7. DIAGRAM & JADUAL UTAMA

- Placeholder: Workflow pinjaman ICT, kelulusan, pengeluaran, pemulangan, helpdesk
- Placeholder: Jadual pemegang taruh, jadual pemetaan peranan kepada fungsi, jadual mapping data

---

# 8. ALIRAN NOTIFIKASI & KOMUNIKASI

## 8.1 Notifikasi Sistem

- Trigger setiap tindakan utama: permohonan, kelulusan, pengeluaran, pemulangan, tiket, penyelesaian
- Saluran: pangkalan data (dashboard), e-mel rasmi (template MYDS)
- Jadual mapping trigger, penerima, kelas notifikasi

---

# 9. KUALITI & PEMATUHAN

## 9.1 Senarai Semak Kualiti

- Pematuhan MYDS: warna, tipografi, komponen, grid, kebolehcapaian
- Ujian browser/mobile: Chrome, Firefox, Safari, Edge, Mobile Safari
- Prestasi: masa loading < 3s

## 9.2 Senarai Semak 18 Prinsip MyGovEA

- Lampiran: pemetaan keperluan sistem kepada setiap prinsip

---

# 10. LAMPIRAN

- Borang rasmi pinjaman ICT
- Flowchart, ERD, dokumen audit, manual pengguna, QA checklist, template e-mel

---

> **Nota:**  
> Dokumen ini disusun mengikut templat rasmi KRISA, mematuhi prinsip reka bentuk MyGovEA dan panduan MYDS untuk pembangunan sistem kerajaan digital MOTAC.

<!--
Setiap seksyen skeleton ini boleh diisi/diupdate dengan maklumat, rajah, jadual dan rujukan yang diambil daripada file dokumentasi sistem, reka bentuk, aliran kerja, jadual data, dan prinsip MyGovEA yang telah dikongsi.
-->
