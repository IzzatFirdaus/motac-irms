# **DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)**

**NAMA SISTEM:** `[Sertakan Nama Sistem Di Sini]`

**NAMA AGENSI:** `[Nama Agensi Di Sini]`

**NAMA AGENSI INDUK:** `[Nama Agensi Induk Di Sini]`

**TARIKH DOKUMEN:** `[Tarikh Dokumen]`

**VERSI DOKUMEN:** `[Versi Dokumen, cth: 1.0]`

---

## **KETERANGAN DOKUMEN**

*Seksyen ini adalah ruangan untuk menyatakan secara ringkas keterangan berkenaan dokumen yang disediakan. Dokumen ini menerangkan keperluan sistem yang akan dirujuk semasa fasa pembangunan sistem. Kandungan dokumen ini merangkumi senarai aktor sistem, hierarki fungsi sistem, rajah use case, model proses sistem dan model maklumat.*

### **SEMAKAN DAN PENGESAHAN DOKUMEN**

*Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.*

**Disemak Oleh:**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh |
| :--- | :--- | :--- | :--- |
| `[Nama Penyemak]` | `[Jawatan Penyemak]` | | `[Tarikh]` |
| `[Nama Penyemak]` | `[Jawatan Penyemak]` | | `[Tarikh]` |

**Disahkan Oleh:**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh |
| :--- | :--- | :--- | :--- |
| `[Nama Pengesah]` | `[Jawatan Pengesah]` | | `[Tarikh]` |
| `[Nama Pengesah]` | `[Jawatan Pengesah]` | | `[Tarikh]` |

### **KAWALAN DOKUMEN**

*Seksyen ini adalah ruangan untuk mencatatkan maklumat-maklumat pindaan yang telah dilakukan ke atas dokumen ini.*

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| `1.0` | `[Tarikh]` | `Draf Awal` | `[Nama Penyedia]` |
| `1.1` | `[Tarikh]` | `[Ringkasan Pindaan]` | `[Nama Penyedia]` |

---

### **KANDUNGAN**

## Jadual Kandungan

- [KETERANGAN DOKUMEN](#keterangan-dokumen)
- [AKRONIM DAN DEFINISI](#akronim-dan-definisi)
- [SUMBER RUJUKAN](#sumber-rujukan)
- [1. PENGENALAN](#1-pengenalan)
- [2. PEMODELAN FUNGSI SISTEM](#2-pemodelan-fungsi-sistem)
- [3. PEMODELAN USE CASE](#3-pemodelan-use-case)
- [4. PEMODELAN MAKLUMAT](#4-pemodelan-maklumat)
- [5. PEMODELAN PROSES SISTEM](#5-pemodelan-proses-sistem)
- [6. PENENTUAN KEPERLUAN BUKAN FUNGSIAN](#6-penentuan-keperluan-bukan-fungsian)
- [7. PENENTUAN SAIZ SISTEM APLIKASI](#7-penentuan-saiz-sistem-aplikasi)
- [8. LAMPIRAN](#8-lampiran)

---

### **AKRONIM DAN DEFINISI**

**a) Akronim**
*Sub seksyen ini adalah ruangan untuk menerangkan akronim-akronim yang digunakan di dalam dokumen.*

| Akronim | Keterangan |
| :--- | :--- |
| SRS | Spesifikasi Keperluan Sistem |
| ERD | Rajah Hubungan Entiti |
| DFD | Rajah Aliran Data |

**b) Definisi**
*Sub seksyen ini adalah ruangan untuk menerangkan definisi bagi terma atau istilah yang digunakan di dalam dokumen.*

| Terma/Istilah | Definisi |
| :--- | :--- |
| Aktor | Peranan yang dimainkan oleh entiti luar yang berinteraksi dengan sistem. |
| Entiti | Objek signifikan di mana maklumat mengenainya perlu disimpan. |

### **SUMBER RUJUKAN**

*Seksyen ini adalah ruangan untuk menyenaraikan semua sumber-sumber rujukan yang digunakan di dalam penyediaan dokumen ini.*

a) `[Nama Dokumen Rujukan 1]`
b) `[Nama Dokumen Rujukan 2]`

---

## **1. PENGENALAN**

### **1.1. Tujuan Sistem**

*Seksyen ini menerangkan tujuan, objektif dan matlamat sistem aplikasi ini dibangunkan selaras dengan objektif bisnes yang ingin dicapai.*
*Contoh: Tujuan pembangunan sistem adalah untuk... Sistem yang baru perlulah dapat memenuhi keperluan berikut:*

- *a) Keperluan 1*
- *b) Keperluan 2*

### **1.2. Skop Sistem**

*Seksyen ini menjelaskan penentuan skop sistem aplikasi yang ingin dibangunkan.*
*Contoh: Skop sistem yang akan dibangunkan merangkumi perkara berikut:*

- *a) Skop 1*
- *b) Skop 2*

### **1.3. Senarai Aktor Sistem**

*Seksyen ini menyenaraikan aktor-aktor sistem yang terlibat serta keterangan fungsinya di dalam sistem aplikasi yang akan dibangunkan.*

| AKTOR | KETERANGAN |
| :--- | :--- |
| `[Nama Aktor 1]` | `[Keterangan peranan Aktor 1]` |
| `[Nama Aktor 2]` | `[Keterangan peranan Aktor 2]` |

---

## **2. PEMODELAN FUNGSI SISTEM**

### **2.1. Penggunaan Notasi**

*Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Fungsi Sistem.*

### **2.2. Rajah Hierarki Fungsian Sistem**

*Seksyen ini menyediakan Rajah Hierarki Fungsian Sistem yang merangkumi komponen-komponen seperti sistem, subsistem, fungsi, modul, submodul dan transaksi.*

`[Rajah 1: Hierarki Fungsi Sistem]`

### **2.3. Jadual Pemadanan Aktor Dengan Fungsi Sistem**

*Seksyen ini menyediakan Jadual Pemadanan Aktor bagi setiap modul.*

**Nama Modul: `[Nama Modul 1]`**

| Bil. | ID Fungsi Sistem | Nama Transaksi | Aktor Sistem |
| :--- | :--- | :--- | :--- |
| 1. | `[ID Fungsi]` | `[Nama Transaksi]` | `[Nama Aktor]` |
| 2. | `[ID Fungsi]` | `[Nama Transaksi]` | `[Nama Aktor]` |

---

## **3. PEMODELAN USE CASE**

### **3.1. Penggunaan Notasi**

*Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Use Case.*

### **3.2. Model Use Case**

*Seksyen ini menyediakan Model Use Case yang terdiri daripada Rajah Use Case serta keterangan bagi use case yang terlibat.*

#### **3.2.1. `[Nama Modul 1]`**

**a) Rajah Use Case**
`[Rajah 2: Rajah Use Case Modul 1]`

#### b) Keterangan Use Case

| LABEL | NAMA USE CASE | KETERANGAN |
| :--- | :--- | :--- |
| `[UC-ID-01]` | `[Nama Use Case]` | `[Keterangan proses untuk Use Case ini]` |
| `[UC-ID-02]` | `[Nama Use Case]` | `[Keterangan proses untuk Use Case ini]` |

---

## **4. PEMODELAN MAKLUMAT**

### **4.1. Penggunaan Notasi**

*Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Maklumat.*

### **4.2. Model Maklumat**

*Seksyen ini menyediakan Model Maklumat yang terdiri daripada Rajah Hubungan Entiti (ERD).*

`[Rajah X: Rajah Hubungan Entiti (ERD)]`

### **4.3. Definisi Kamus Data**

*Seksyen ini menyediakan Definisi Kamus Data bagi setiap entiti yang telah disediakan di dalam ERD.*

**a) Entiti `[NAMA_ENTITI_1]`**
**Keterangan Entiti:** `[Keterangan ringkas mengenai entiti]`

**Atribut:**

| Nama | Pilihan (Y/T) | Format | Saiz | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `#* id_entiti` | T | numerik | 12 | Pengenal unik |
| `* nama_atribut` | T | alfanumerik | 150 | Nama |

**Peraturan Bisnes:**

1. Setiap `[NAMA_ENTITI_1]` mesti `[peraturan]` satu atau lebih `[NAMA_ENTITI_2]`.
2. Setiap `[NAMA_ENTITI_1]` mungkin `[peraturan]` satu atau lebih `[NAMA_ENTITI_3]`.

---

## **5. PEMODELAN PROSES SISTEM**

### **5.1. Penggunaan Notasi**

*Seksyen ini menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Proses Sistem.*

### **5.2. Model Proses Sistem**

*Seksyen ini menyediakan Model Proses Sistem yang terdiri daripada Rajah Konteks dan Rajah Aliran Data (DFD).*

#### **5.2.1. Rajah Konteks**

`[Rajah X: Rajah Konteks Sistem]`

#### **5.2.2. Aliran Data `[Nama Modul 1]`**

`[Rajah X: Aliran Data Modul 1]`

### **5.3. Definisi Aliran Data**

*Seksyen ini menyediakan dan melengkapkan Definisi Aliran Data.*

| Nama Aliran Data | Sumber | Destinasi | Atribut |
| :--- | :--- | :--- | :--- |
| `[Nama Aliran]` | `[Fungsi/Entiti]` | `[Fungsi/Storan]` | `[atribut1, atribut2]` |

---

## **6. PENENTUAN KEPERLUAN BUKAN FUNGSIAN**

### **6.1. Jadual Ciri-ciri Kualiti Sistem**

*Seksyen ini menyediakan jadual keperluan bukan fungsian yang merangkumi aspek sistem, dalaman dan luaran.*

| ID | Ciri-ciri Kualiti | Catatan |
| :--- | :--- | :--- |
| NF-AS-01 | Interoperability | `[Keperluan Interoperability]` |
| NF-AS-02 | Scalability | `[Keperluan Skalabiliti]` |
| NF-AS-03 | Response Time | `[Keperluan Masa Tindak Balas]` |

---

## **7. PENENTUAN SAIZ SISTEM APLIKASI**

*Seksyen ini menyediakan Pengiraan Saiz Sistem Aplikasi dengan menggunakan kaedah Function Points Analysis.*
*<-- Masukkan jadual dan pengiraan Function Point di sini -->*

---

## **8. LAMPIRAN**

*Seksyen ini merupakan ruangan untuk menyertakan dokumen-dokumen sokongan yang perlu dirujuk seperti format borang fizikal, format laporan dan lain-lain.*

### **Diagrams (Rajah)**

The SRS includes the following diagrams to visually model the system's functions, user interactions, data structure, and processes:

- **Rajah 1:** Hierarki Fungsi Sistem Mengurus Penggunaan Bilik Mesyuarat (Functional Hierarchy Diagram)
- **Rajah 2:** Rajah Use Case Mengurus Pengguna (Use Case Diagram for User Management)
- **Rajah 3:** Rajah Use Case Sub Modul Pendaftaran Bilik (Use Case Diagram for Room Registration Sub-Module)
- **Rajah 4:** Rajah Use Case Sub Modul Aduan Kerosakan (Use Case Diagram for Damage Report Sub-Module)
- **Rajah 5:** Rajah Use Case Sub Modul Permohonan Tempahan (Use Case Diagram for Booking Application Sub-Module)
- **Rajah 6:** Rajah Use Case Sub Modul Kelulusan Tempahan (Use Case Diagram for Booking Approval Sub-Module)
- **Rajah 7:** Rajah Use Case Sub Modul Maklumbalas Tempahan (Use Case Diagram for Booking Feedback Sub-Module)
- **Rajah 8:** Rajah Use Case Modul Dashboard & Laporan (Use Case Diagram for Dashboard & Report Module)
- **Rajah 9:** Rajah Use Case Modul Pentadbiran Sistem (Use Case Diagram for System Administration Module)
- **Rajah 10:** Rajah Hubungan Entiti (ERD) (Entity-Relationship Diagram)
- **Rajah 11:** Rajah Konteks Sistem Mengurus Penggunaan Bilik Mesyuarat (System Context Diagram)
- **Rajah 12:** Aliran Data Modul Pengurusan Pengguna (Data Flow Diagram for User Management Module)
- **Rajah 13:** Aliran Data SubModul Pendaftaran Bilik (Data Flow Diagram for Room Registration Sub-Module)
- **Rajah 14:** Aliran Data SubModul Aduan Kerosakan (Data Flow Diagram for Damage Report Sub-Module)
- **Rajah 15:** Aliran Data SubModul Permohonan Tempahan (Data Flow Diagram for Booking Application Sub-Module)
- **Rajah 16:** Aliran Data SubModul Kelulusan Tempahan (Data Flow Diagram for Booking Approval Sub-Module)
- **Rajah 17:** Aliran Data SubModul Maklumbalas Tempahan (Data Flow Diagram for Booking Feedback Sub-Module)
- **Rajah 18:** Aliran Data Modul Dashboard dan Laporan (Data Flow Diagram for Dashboard and Report Module)
- **Rajah 19:** Aliran Data Modul Pentadbiran Sistem (Data Flow Diagram for System Administration Module)

### **Tables (Jadual)**

The document uses tables to provide detailed, structured information for notation, actor roles, use case descriptions, data definitions, and non-functional requirements.

- **Jadual 1:** Senarai Aktor Sistem
- **Jadual 2:** Notasi Rajah Fungsi Sistem
- **Jadual 3-7:** Pemadanan Aktor Dengan Fungsi (for all modules)
- **Jadual 8:** Notasi Rajah Use Case
- **Jadual 9-16:** Keterangan Use Case (for all modules)
- **Jadual 17:** Notasi Rajah Hubungan Data
- **Jadual 18-31:** Definisi Entiti (Data Dictionaries for all entities, e.g., PENGGUNA, BILIK MESYUARAT, TEMPAHAN)
- **Jadual 32:** Notasi Rajah Aliran Data
- **Jadual 33-40:** Definisi Aliran Data (for all modules)
- **Jadual 41-42:** Templat Ciri-ciri Sistem Aplikasi (Non-Functional Requirements)

### **Other Modeling Components**

Beyond specific diagrams and tables, the KRISA SRS is structured around several key modeling activities:

- **Pemodelan Fungsi Sistem (System Function Modeling):** Defines what the system does through functional hierarchies and actor mapping.
- **Pemodelan Use Case (Use Case Modeling):** Describes system behavior from a user's perspective through use case diagrams and detailed descriptions.
- **Pemodelan Maklumat (Information Modeling):** Outlines the system's data structure using Entity-Relationship Diagrams (ERD) and detailed Data Dictionaries.
- **Pemodelan Proses Sistem (System Process Modeling):** Illustrates how data moves through the system using a Context Diagram and Data Flow Diagrams (DFDs).
- **Penentuan Keperluan Bukan Fungsian (Non-Functional Requirements Determination):** Specifies system quality attributes like performance, scalability, and security.
