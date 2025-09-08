# Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS)
## Senarai & Rajah Gambaran Sistem (SRS)

Dokumen ini membekalkan kod mermaid untuk setiap rajah utama dalam SRS IRMS, merangkumi konteks sistem, hierarki modul, aktor, use case, aliran kerja, data, integrasi, dan pematuhan. Setiap rajah boleh diolah/dimasukkan ke dalam markdown, dokumentasi online, atau alat visual yang menyokong mermaid.

---

## Rajah SRS-1: System Context Diagram

```mermaid
flowchart LR
    IRMS["IRMS Sistem"]
    User["Pengguna (Staf MOTAC)"]
    PegawaiPenyokong["Pegawai Penyokong"]
    BPM["Staf BPM"]
    ITAdmin["Admin IT"]
    ITAgent["Agen IT"]
    ExtMail["Sistem E-mel"]
    Analytics["Sistem Analitik"]
    HRMS["HRMS (Integrasi API)"]

    User --> IRMS
    PegawaiPenyokong --> IRMS
    BPM --> IRMS
    ITAdmin --> IRMS
    ITAgent --> IRMS
    IRMS --> ExtMail
    IRMS --> Analytics
    IRMS --> HRMS
```
_Komen: Gambaran sistem IRMS dan hubungannya dengan aktor serta sistem luaran._

---

## Rajah SRS-2: Functional/Business Architecture Diagram

```mermaid
graph TD
    IRMS["IRMS Sistem"]
    IRMS --> PinjamanICT["Modul Pinjaman Peralatan ICT"]
    IRMS --> Helpdesk["Modul Helpdesk ICT"]
    IRMS --> Admin["Modul Pentadbiran Sistem"]
    IRMS --> Notifikasi["Modul Notifikasi & Komunikasi"]
    IRMS --> Laporan["Modul Laporan & Analitik"]

    PinjamanICT --> Permohonan
    PinjamanICT --> Kelulusan
    PinjamanICT --> Pengeluaran
    PinjamanICT --> Pemulangan

    Helpdesk --> TiketCipta
    Helpdesk --> TiketPenugasan
    Helpdesk --> TiketSelesai
    Helpdesk --> TiketTutup
```
_Komen: Struktur modul dan submodul utama IRMS._

---

## Rajah SRS-3: Actor Diagram

```mermaid
flowchart LR
    Pemohon["Pemohon"]
    PegawaiPenyokong["Pegawai Penyokong"]
    BPM["Staf BPM"]
    ITAdmin["Admin IT"]
    ITAgent["Agen IT"]
    Admin["Pentadbir Sistem"]

    Pemohon --- PinjamanICT
    Pemohon --- Helpdesk
    PegawaiPenyokong --- PinjamanICT
    BPM --- PinjamanICT
    ITAdmin --- Helpdesk
    ITAgent --- Helpdesk
    Admin --- Admin
```
_Komen: Senarai aktor dan pemetaan peranan kepada modul._

---

## Rajah SRS-4: Use Case Diagram – Pinjaman ICT

```mermaid
usecase
    actor Pemohon
    actor PegawaiPenyokong
    actor BPM
    Pemohon --> (Isi Permohonan)
    Pemohon --> (Semak Status)
    PegawaiPenyokong --> (Semak Permohonan)
    PegawaiPenyokong --> (Lulus/Tolak)
    BPM --> (Pengeluaran Peralatan)
    BPM --> (Terima Pemulangan)
    Pemohon --> (Pemulangan Peralatan)
```
_Komen: Use case utama modul pinjaman ICT._

---

## Rajah SRS-5: Use Case Diagram – Helpdesk

```mermaid
usecase
    actor StafMOTAC
    actor ITAdmin
    actor ITAgent
    StafMOTAC --> (Cipta Tiket)
    StafMOTAC --> (Semak Status Tiket)
    ITAdmin --> (Penugasan Tiket)
    ITAdmin --> (Pantau Statistik)
    ITAgent --> (Selesaikan Tiket)
    ITAgent --> (Tutup Tiket)
    StafMOTAC --> (Terima Notifikasi)
```
_Komen: Use case utama modul helpdesk._

---

## Rajah SRS-6: Use Case Diagram – Notifikasi & Laporan

```mermaid
usecase
    actor Pemohon
    actor PegawaiPenyokong
    actor BPM
    actor ITAdmin
    actor ITAgent
    actor Admin

    Pemohon --> (Terima Notifikasi)
    PegawaiPenyokong --> (Terima Notifikasi Masuk)
    BPM --> (Terima Notifikasi Pengeluaran/Pemulangan)
    ITAdmin --> (Terima Notifikasi Tiket Masuk)
    ITAgent --> (Terima Notifikasi Penugasan)
    Admin --> (Jana Laporan Analitik)
```
_Komen: Use case notifikasi dan laporan._

---

## Rajah SRS-7: ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ LOAN_APPLICATIONS : "menghantar"
    USERS ||--o{ HELP_DESK_TICKETS : "melapor"
    USERS ||--o{ APPROVALS : "meluluskan"
    USERS ||--o{ NOTIFICATIONS : "menerima"
    LOAN_APPLICATIONS ||--o{ LOAN_APPLICATION_ITEMS : "mengandungi"
    LOAN_APPLICATIONS ||--o{ LOAN_TRANSACTIONS : "berkaitan"
    LOAN_TRANSACTIONS ||--o{ LOAN_TRANSACTION_ITEMS : "mengandungi"
    EQUIPMENT ||--o{ LOAN_TRANSACTION_ITEMS : "digunakan"
    EQUIPMENT ||--o{ EQUIPMENT_CATEGORIES : "dikategori"
    EQUIPMENT_CATEGORIES ||--o{ SUB_CATEGORIES : "subkategori"
    EQUIPMENT ||--o{ LOCATIONS : "lokasi"
    HELP_DESK_TICKETS ||--o{ HELP_DESK_COMMENTS : "komen"
    HELP_DESK_TICKETS ||--o{ HELP_DESK_CATEGORIES : "kategori"
    APPROVALS }|--|| LOAN_APPLICATIONS : "untuk aplikasi"
    APPROVALS }|--|| HELP_DESK_TICKETS : "untuk tiket"
```
_Komen: Hubungan entiti utama dan data._

---

## Rajah SRS-8: Data Dictionary Table Reference

```mermaid
flowchart LR
    Users["users (id, name, ... )"]
    Equip["equipment (id, type, ... )"]
    LoanApp["loan_applications (id, user_id, ... )"]
    LoanItem["loan_application_items (id, loan_application_id, ... )"]
    LoanTrans["loan_transactions (id, loan_application_id, ... )"]
    LoanTransItem["loan_transaction_items (id, loan_transaction_id, ... )"]
    Helpdesk["helpdesk_tickets (id, user_id, ... )"]
    HelpCat["helpdesk_categories (id, name, ... )"]
    HelpCom["helpdesk_comments (id, ticket_id, ... )"]
    Approval["approvals (id, approvable_type, ... )"]
    Notif["notifications (id, notifiable_type, ... )"]
    Settings["settings (id, ... )"]

    Users --> LoanApp
    LoanApp --> LoanItem
    LoanApp --> LoanTrans
    LoanTrans --> LoanTransItem
    LoanTransItem --> Equip
    Helpdesk --> HelpCat
    Helpdesk --> HelpCom
    Approval --> LoanApp
    Approval --> Helpdesk
    Notif --> Users
    Settings --> Users
```
_Komen: Jadual utama dan hubungan (boleh diganti/ditambah jadual detail dalam markdown)._

---

## Rajah SRS-9: Workflow – Pinjaman ICT

```mermaid
flowchart TD
    Start("Mula")
    Start --> IsiPermohonan("Isi Borang Permohonan")
    IsiPermohonan --> HantarPermohonan("Hantar Permohonan")
    HantarPermohonan --> Validasi("Validasi Sistem")
    Validasi --> Kelulusan("Kelulusan Pegawai Penyokong")
    Kelulusan -->|Lulus| Pengeluaran("Pengeluaran Peralatan")
    Kelulusan -->|Tolak| Tamat("Tamat/Ralat")
    Pengeluaran --> Penggunaan("Penggunaan Peralatan")
    Penggunaan --> Pemulangan("Pemulangan Peralatan")
    Pemulangan --> Tamat
```
_Komen: Proses lengkap modul pinjaman ICT._

---

## Rajah SRS-10: Workflow – Helpdesk Ticket

```mermaid
flowchart TD
    Start("Mula")
    Start --> CiptaTiket("Cipta Tiket Aduan")
    CiptaTiket --> ValidasiTiket("Validasi Tiket")
    ValidasiTiket --> Penugasan("Penugasan Tiket oleh Admin")
    Penugasan -->|Assigned| Selesaikan("Selesaikan Tiket oleh Agen IT")
    Selesaikan --> Penutupan("Penutupan Tiket")
    Penutupan --> Tamat("Tamat Proses")
```
_Komen: Aliran proses tiket helpdesk._

---

## Rajah SRS-11: Approval Routing Diagram

```mermaid
flowchart TD
    Permohonan("Permohonan Dihantar")
    Permohonan --> ValidasiGred("Validasi Gred Penyokong")
    ValidasiGred -->|Gred Mencukupi| SemakPermohonan("Semak Permohonan")
    ValidasiGred -->|Gred Tidak Mencukupi| Eskalasi("Eskalasi ke Pegawai Lebih Tinggi")
    SemakPermohonan -->|Lulus| Lulus("Permohonan Diluluskan")
    SemakPermohonan -->|Tolak| Tolak("Permohonan Ditolak")
    Eskalasi --> SemakPermohonan
```
_Komen: Aliran kelulusan berhierarki dan eskalasi._

---

## Rajah SRS-12: DFD – Pinjaman ICT Module

```mermaid
flowchart TD
    Pemohon -->|Isi Borang| Borang
    Borang -->|Simpan| DB["Database"]
    DB -->|Validasi| Validasi
    Validasi -->|Keputusan| Kelulusan
    Kelulusan -->|Notifikasi| Notif
    Kelulusan -->|Pengeluaran| Pengeluaran
    Pengeluaran -->|Rekod| DB
    Pengeluaran -->|Notifikasi| Notif
    Pengeluaran -->|Penggunaan| Pemohon
    Pemohon -->|Pemulangan| Pemulangan
    Pemulangan -->|Rekod| DB
    Pemulangan -->|Notifikasi| Notif
```
_Komen: Aliran data utama modul pinjaman ICT._

---

## Rajah SRS-13: DFD – Helpdesk Module

```mermaid
flowchart TD
    StafMOTAC -->|Cipta Tiket| TiketForm
    TiketForm -->|Simpan| DBHelpdesk["DB Helpdesk"]
    DBHelpdesk -->|Validasi| ValidasiHelpdesk
    ValidasiHelpdesk -->|Penugasan| TiketAdmin
    TiketAdmin -->|Rekod| DBHelpdesk
    TiketAdmin -->|Notifikasi| NotifHelpdesk
    TiketAdmin -->|Selesaikan| ITAgent
    ITAgent -->|Kemas Kini| DBHelpdesk
    ITAgent -->|Notifikasi| NotifHelpdesk
    ITAgent -->|Tutup Tiket| DBHelpdesk
```
_Komen: Aliran data utama modul helpdesk._

---

## Rajah SRS-14: Integration Points Diagram

```mermaid
graph TD
    IRMS --> SMTP["Email Server (SMTP)"]
    IRMS --> SMS["SMS Gateway"]
    IRMS --> BI["Reporting/BI Tool"]
    IRMS --> HRMS["HRMS API (Integrasi)"]
```
_Komen: Titik integrasi IRMS ke sistem luaran._

---

## Rajah SRS-15: Notification Trigger Flow Diagram

```mermaid
sequenceDiagram
    participant User
    participant IRMS
    participant NotificationService
    participant EmailServer

    User->>IRMS: Lakukan Tindakan (Contoh: Hantar Permohonan)
    IRMS->>NotificationService: Trigger Notifikasi
    NotificationService->>User: Papar Notifikasi In-App
    NotificationService->>EmailServer: Hantar E-mel
    EmailServer->>User: Terima E-mel Notifikasi
```
_Komen: Pipeline proses notifikasi dari event hingga pengguna._

---

## Rajah SRS-16: MYDS Component Inventory Reference

```mermaid
graph TD
    UI["MYDS UI Component"]
    UI --> Button["Butang"]
    UI --> Form["Borang Input"]
    UI --> Card["Kad Maklumat"]
    UI --> Table["Jadual Data"]
    UI --> Badge["Lencana Status"]
    UI --> Navigation["Navigasi (Sidebar/Topbar)"]
    UI --> Alert["Dialog/Aduan"]
    UI --> Tabs["Tab Navigasi"]
    UI --> Pagination["Paginasi"]
```
_Komen: Senarai komponen MYDS yang digunakan dalam UI IRMS._

---

## Rajah SRS-17: MyGovEA Principles Compliance Matrix

```mermaid
flowchart LR
    subgraph PrinsipMyGovEA
        citizen["Berpaksikan Rakyat"]
        data["Berpacukan Data"]
        content["Kandungan Terancang"]
        tech["Teknologi Bersesuaian"]
        minimal["Antara Muka Minimalis"]
        seragam["Seragam"]
        menu["Paparan/Menu Jelas"]
        realistik["Realistik"]
        kognitif["Kognitif"]
        fleksibel["Fleksibel"]
        komunikasi["Komunikasi"]
        hierarki["Struktur Hierarki"]
        uiux["Komponen UI/UX"]
        tipografi["Tipografi"]
        default["Tetapan Lalai"]
        kawalan["Kawalan Pengguna"]
        ralat["Pencegahan Ralat"]
        panduan["Panduan & Dokumentasi"]
    end

    subgraph ModulIRMS
        pinjaman["Pinjaman ICT"]
        helpdesk["Helpdesk ICT"]
        admin["Pentadbiran"]
        notifikasi["Notifikasi"]
        laporan["Laporan"]
    end

    citizen --- pinjaman
    citizen --- helpdesk
    data --- pinjaman
    data --- laporan
    content --- pinjaman
    content --- helpdesk
    tech --- pinjaman
    tech --- helpdesk
    minimal --- pinjaman
    minimal --- helpdesk
    seragam --- pinjaman
    seragam --- helpdesk
    menu --- pinjaman
    menu --- helpdesk
    realistik --- pinjaman
    kognitif --- pinjaman
    fleksibel --- pinjaman
    fleksibel --- helpdesk
    komunikasi --- notifikasi
    hierarki --- admin
    uiux --- pinjaman
    tipografi --- pinjaman
    default --- admin
    kawalan --- admin
    ralat --- pinjaman
    panduan --- admin
```
_Komen: Matriks pemetaan prinsip MyGovEA ke modul IRMS._

---

> **Nota:**  
> Semua kod mermaid boleh terus dimasukkan ke dalam markdown, GitHub, Notion, atau mana-mana alat dokumentasi yang menyokong diagram berasaskan teks.  
> Rajah perlu dinomborkan dan dirujuk dalam seksyen berkaitan SRS.  
> Rajah boleh diolah/diperkaya mengikut keperluan semasa fasa design atau pelaksanaan.
