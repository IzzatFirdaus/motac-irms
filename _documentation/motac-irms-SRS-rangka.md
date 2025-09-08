# **DOKUMEN SPESIFIKASI KEPERLUAN SISTEM (SRS)**

**NAMA SISTEM:** Sistem Pengurusan Tempahan Bilik Mesyuarat MOTAC (IRMS)

**NAMA AGENSI:** Kementerian Pelancongan, Seni dan Budaya (MOTAC)

**NAMA AGENSI INDUK:** [Isi jika ada]

**TARIKH DOKUMEN:** 2025-08-14

**VERSI DOKUMEN:** 1.0

---

## **KETERANGAN DOKUMEN**

Dokumen ini menerangkan keperluan sistem yang akan dirujuk semasa fasa pembangunan IRMS. Kandungan dokumen merangkumi senarai aktor sistem, hierarki fungsi sistem, rajah use case, model proses sistem dan model maklumat.

### **SEMAKAN DAN PENGESAHAN DOKUMEN**

**Disemak Oleh:**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh |
| :--- | :--- | :--- | :--- |
| [Nama Penyemak] | [Jawatan Penyemak] | | [Tarikh] |

**Disahkan Oleh:**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh |
| :--- | :--- | :--- | :--- |
| [Nama Pengesah] | [Jawatan Pengesah] | | [Tarikh] |

### **KAWALAN DOKUMEN**

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0 | 2025-08-14 | Draf Awal | [Nama Penyedia] |

---

## Jadual Kandungan

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

#### a) Akronim

| Akronim | Keterangan |
| :--- | :--- |
| SRS | Spesifikasi Keperluan Sistem |
| IRMS | Integrated Room Management System |
| MOTAC | Ministry of Tourism, Arts and Culture |
| ERD | Entity Relationship Diagram |
| DFD | Data Flow Diagram |

#### b) Definisi

| Terma/Istilah | Definisi |
| :--- | :--- |
| Aktor | Peranan yang dimainkan oleh entiti luar yang berinteraksi dengan sistem. |
| Entiti | Objek signifikan di mana maklumat mengenainya perlu disimpan. |

---

### **SUMBER RUJUKAN**

a) Dokumentasi BRS IRMS  
b) Manual Prosedur Kerja Pengurusan Tempahan Bilik  
c) Panduan MYDS & MyGovEA

---

## **1. PENGENALAN**

### **1.1. Tujuan Sistem**

Terangkan tujuan, objektif dan matlamat sistem aplikasi IRMS.

### **1.2. Skop Sistem**

Jelaskan skop sistem aplikasi yang ingin dibangunkan.

### **1.3. Senarai Aktor Sistem**

| AKTOR | KETERANGAN |
| :--- | :--- |
| [Nama Aktor 1] | [Peranan Aktor 1] |
| [Nama Aktor 2] | [Peranan Aktor 2] |

---

## **2. PEMODELAN FUNGSI SISTEM**

### **2.1. Penggunaan Notasi**

#### Senaraikan notasi yang digunakan untuk model fungsi sistem

### **2.2. Rajah Hierarki Fungsian Sistem**

_[Sisipkan Rajah 1: Hierarki Fungsi Sistem IRMS]_  
_(Rajah akan menunjukkan subsistem, modul, submodul dan transaksi utama)_

### **2.3. Jadual Pemadanan Aktor Dengan Fungsi Sistem**

**Nama Modul: [Nama Modul]**

| Bil. | ID Fungsi Sistem | Nama Transaksi | Aktor Sistem |
| :--- | :--- | :--- | :--- |
| 1. | [ID Fungsi] | [Nama Transaksi] | [Nama Aktor] |
| 2. | [ID Fungsi] | [Nama Transaksi] | [Nama Aktor] |

---

## **3. PEMODELAN USE CASE**

### **3.1. Penggunaan Notasi**

#### Senaraikan notasi yang digunakan untuk model use case

### **3.2. Model Use Case**

#### **3.2.1. [Nama Modul]**

**a) Rajah Use Case**  
_[Sisipkan Rajah Use Case Modul]_  

#### b) Keterangan Use Case

| LABEL | NAMA USE CASE | KETERANGAN |
| :--- | :--- | :--- |
| [UC-ID-01] | [Nama Use Case] | [Keterangan proses untuk Use Case ini] |
| [UC-ID-02] | [Nama Use Case] | [Keterangan proses untuk Use Case ini] |

---

## **4. PEMODELAN MAKLUMAT**

### **4.1. Penggunaan Notasi**

Senaraikan notasi yang digunakan untuk model maklumat

### **4.2. Model Maklumat**

_[Sisipkan Rajah ERD Sistem IRMS]_  

### **4.3. Definisi Kamus Data**

**a) Entiti [NAMA_ENTITI]**  
Keterangan Entiti: [Keterangan ringkas mengenai entiti]

**Atribut:**

| Nama | Pilihan (Y/T) | Format | Saiz | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| id_entiti | T | numerik | 12 | Pengenal unik |
| nama_atribut | T | alfanumerik | 150 | Nama |

**Peraturan Bisnes:**

1. Setiap [NAMA_ENTITI] mesti [peraturan] satu atau lebih [NAMA_ENTITI lain].
2. ...

---

## **5. PEMODELAN PROSES SISTEM**

### **5.1. Penggunaan Notasi**

#### Senaraikan Notasi yang Digunakan untuk Model Proses Sistem

(Senaraikan notasi yang digunakan untuk model proses sistem)

### **5.2. Model Proses Sistem**

#### **5.2.1. Rajah Konteks**

_[Sisipkan Rajah Konteks Sistem]_  

#### **5.2.2. Aliran Data [Nama Modul]**

_[Sisipkan Rajah Aliran Data Modul]_  

### **5.3. Definisi Aliran Data**

| Nama Aliran Data | Sumber | Destinasi | Atribut |
| :--- | :--- | :--- | :--- |
| [Nama Aliran] | [Fungsi/Entiti] | [Fungsi/Storan] | [atribut1, atribut2] |

---

## **6. PENENTUAN KEPERLUAN BUKAN FUNGSIAN**

### **6.1. Jadual Ciri-ciri Kualiti Sistem**

| ID | Ciri-ciri Kualiti | Catatan |
| :--- | :--- | :--- |
| NF-AS-01 | Interoperability | [Keperluan Interoperability] |
| NF-AS-02 | Scalability | [Keperluan Skalabiliti] |
| NF-AS-03 | Response Time | [Keperluan Masa Tindak Balas] |

---

## **7. PENENTUAN SAIZ SISTEM APLIKASI**

### Jadual Pengiraan Function Point dan Rumusan Saiz Sistem Aplikasi

---

## **8. LAMPIRAN**

### Dokumen sokongan, format borang, format laporan dan lain-lain

---

> **Nota:**  
> Dokumen ini disusun mengikut templat rasmi KRISA dan mematuhi prinsip reka bentuk MyGovEA serta panduan MYDS untuk pembangunan sistem kerajaan.
