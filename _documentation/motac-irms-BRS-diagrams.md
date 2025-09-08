# Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS)  
## Senarai & Rajah Gambaran Sistem (BRS)

Dokumen ini menyenaraikan dan membekalkan kod mermaid untuk setiap rajah utama dalam BRS IRMS, merangkumi hierarki fungsi, use case, aliran kerja, entiti data, dan konteks sistem.  
Setiap rajah disediakan dalam format mermaid untuk mudah diolah/dimasukkan ke dalam markdown, dokumentasi online, atau alat seperti Draw.io/Mermaid Live Editor.

---

## Rajah 1: Hierarki Fungsi Sistem (Functional Hierarchy Diagram)

```mermaid
graph TD
    IRMS["IRMS Sistem Pengurusan Sumber Terintegrasi"]
    IRMS --> PinjamanICT["Modul Pinjaman Peralatan ICT"]
    IRMS --> Helpdesk["Modul Helpdesk & Sokongan ICT"]
    IRMS --> Admin["Modul Pentadbiran Sistem"]
    IRMS --> Notifikasi["Modul Notifikasi & Komunikasi"]
    IRMS --> Laporan["Modul Laporan & Analitik"]

    PinjamanICT --> Permohonan["Permohonan Pinjaman"]
    PinjamanICT --> Kelulusan["Kelulusan Permohonan"]
    PinjamanICT --> Pengeluaran["Pengeluaran Peralatan"]
    PinjamanICT --> Pemulangan["Pemulangan Peralatan"]

    Helpdesk --> TiketCipta["Cipta Tiket"]
    Helpdesk --> TiketPenugasan["Penugasan Tiket"]
    Helpdesk --> TiketPenyelesaian["Penyelesaian Tiket"]
    Helpdesk --> TiketPenutupan["Penutupan Tiket"]
```
_Komen: Struktur sistem IRMS, pecahan modul dan fungsi utama._

---

## Rajah 2: Arkitektur Bisnes (Business Architecture Overview)

```mermaid
flowchart LR
    User["Pengguna (Staf MOTAC)"] -->|Permohonan| IRMS
    IRMS -->|Kelulusan| PegawaiPenyokong["Pegawai Penyokong"]
    IRMS -->|Pengeluaran & Pemulangan| BPM["Staf BPM"]
    IRMS -->|Tiket Helpdesk| ITAdmin["Admin IT"]
    IRMS -->|Penugasan Tiket| ITAgent["Agen IT"]
    IRMS -->|Laporan/Analitik| Admin["Pentadbir Sistem"]
    IRMS -->|Notifikasi| User
    IRMS -->|Notifikasi| PegawaiPenyokong
    IRMS -->|Notifikasi| BPM
    IRMS -->|Notifikasi| ITAdmin
    IRMS -->|Notifikasi| ITAgent
```
_Komen: Hubungan antara entiti utama dan modul IRMS._

---

## Rajah 3: Use Case Diagram - Pinjaman ICT

```mermaid
usecase
    actor Pemohon
    actor PegawaiPenyokong
    actor BPM
    Pemohon --> (Isi Permohonan Pinjaman)
    Pemohon --> (Semak Status Permohonan)
    PegawaiPenyokong --> (Semak Permohonan)
    PegawaiPenyokong --> (Lulus/Tolak Permohonan)
    BPM --> (Pengeluaran Peralatan)
    BPM --> (Terima Pemulangan Peralatan)
    Pemohon --> (Pemulangan Peralatan)
```
_Komen: Use case utama untuk modul Pinjaman ICT._

---

## Rajah 4: Use Case Diagram - Helpdesk ICT

```mermaid
usecase
    actor StafMOTAC
    actor ITAdmin
    actor ITAgent
    StafMOTAC --> (Cipta Tiket Aduan ICT)
    StafMOTAC --> (Semak Status Tiket)
    ITAdmin --> (Penugasan Tiket)
    ITAdmin --> (Pantau Laporan)
    ITAgent --> (Selesaikan Tiket)
    ITAgent --> (Tutup Tiket)
    StafMOTAC --> (Terima Notifikasi Penyelesaian)
```
_Komen: Use case dan interaksi utama modul Helpdesk._

---

## Rajah 5: Use Case Diagram - Notifikasi & Laporan

```mermaid
usecase
    actor Pemohon
    actor PegawaiPenyokong
    actor BPM
    actor ITAdmin
    actor ITAgent
    actor PentadbirSistem

    Pemohon --> (Terima Notifikasi Permohonan)
    PegawaiPenyokong --> (Terima Notifikasi Permohonan Masuk)
    BPM --> (Terima Notifikasi Pengeluaran/Pemulangan)
    ITAdmin --> (Terima Notifikasi Tiket Masuk)
    ITAgent --> (Terima Notifikasi Penugasan Tiket)
    PentadbirSistem --> (Jana Laporan Analitik)
```
_Komen: Gambaran ringkas interaksi notifikasi dan laporan._

---

## Rajah 6: Workflow Pinjaman ICT

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
_Komen: Aliran penuh proses pinjaman ICT._

---

## Rajah 7: Workflow Helpdesk Ticket

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
_Komen: Aliran proses dari ciptaan hingga penutupan tiket._

---

## Rajah 8: Workflow Kelulusan (Approval Workflow)

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
_Komen: Aliran kelulusan berhierarki._

---

## Rajah 9: Entity Relationship Diagram (ERD)

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
_Komen: Hubungan entiti utama sistem IRMS._

---

## Rajah 10: Data Architecture Overview

```mermaid
graph LR
    Users["Users Table"]
    Equip["Equipment Table"]
    LoanApp["Loan Applications"]
    LoanItem["Loan Application Items"]
    LoanTrans["Loan Transactions"]
    LoanTransItem["Loan Transaction Items"]
    Helpdesk["Helpdesk Tickets"]
    HelpCat["Helpdesk Categories"]
    HelpCom["Helpdesk Comments"]
    Approval["Approvals"]
    Notif["Notifications"]
    Settings["Settings"]

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
_Komen: Gambaran aliran data dan hubungan jadual utama._

---

## Rajah 11: System Context Diagram

```mermaid
flowchart LR
    IRMS["IRMS Sistem"]
    User["Pengguna (Staf MOTAC)"]
    PegawaiPenyokong["Pegawai Penyokong"]
    BPM["Staf BPM"]
    ITAdmin["Admin IT"]
    ITAgent["Agen IT"]
    ExternalMail["Sistem E-mel"]
    Analytics["Sistem Analitik Luar"]

    User --> IRMS
    PegawaiPenyokong --> IRMS
    BPM --> IRMS
    ITAdmin --> IRMS
    ITAgent --> IRMS
    IRMS --> ExternalMail
    IRMS --> Analytics
```
_Komen: Batas sistem IRMS dan integrasi luaran._

---

## Rajah 12: Integration Points Diagram

```mermaid
graph TD
    IRMS --> Email["SMTP / Email Server"]
    IRMS --> SMS["SMS Gateway"]
    IRMS --> Reporting["Reporting/BI Tool"]
    IRMS --> ExternalAPI["API Luaran (contoh: HRMS)"]
```
_Komen: Integrasi utama IRMS dengan sistem luaran._

---

## Rajah 13: Notification Trigger Flow

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
_Komen: Pipeline notifikasi dari event hingga dashboard/email._

---

## Rajah 14: MYDS UI Component Reference

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
_Komen: Senarai komponen MYDS yang digunakan dalam IRMS._

---

## Rajah 15: MyGovEA Principles Mapping (Compliance Matrix)

```mermaid
flowchart LR
    subgraph PrinsipMyGovEA
        citizen["Berpaksikan Rakyat"]
        data["Berpacukan Data"]
        content["Kandungan Terancang"]
        tech["Teknologi Bersesuaian"]
        minimal["Antara Muka Minimalis & Mudah"]
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
> Semua kod mermaid boleh dimasukkan terus ke dalam markdown, GitHub, Notion, atau mana-mana alat dokumentasi yang menyokong diagram berasaskan teks.  
> Untuk dokumentasi rasmi, setiap rajah perlu dinomborkan dan dirujuk dalam seksyen berkaitan BRS.  
> Rajah boleh diolah/diperkaya mengikut keperluan semasa fasa rekaan atau pelaksanaan.
