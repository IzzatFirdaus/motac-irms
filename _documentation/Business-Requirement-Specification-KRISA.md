# **Spesifikasi Keperluan Bisnes (BRS)**
# **< NAMA SISTEM >**
*(Sertakan nama modul di bawah nama sistem sekiranya dokumen disediakan secara berasingan bagi setiap modul di bawah sistem yang sama)*

| | |
| --- | --- |
| **NAMA AGENSI** | < Nama Agensi > |
| **NAMA AGENSI INDUK** | < Nama Agensi Induk > |
| **TARIKH DOKUMEN** | < Tarikh Dokumen > |
| **VERSI DOKUMEN** | < Versi Dokumen > |

---
### **KETERANGAN DOKUMEN**
*Seksyen ini adalah ruangan untuk menyatakan secara ringkas keterangan ringkas berkenaan dokumen yang disediakan.*

> **Contoh:** Dokumen ini menerangkan keperluan bisnes dan pengguna bagi pembangunan Sistem Mengurus Penggunaan Bilik Mesyuarat. Kandungannya merangkumi maklumat terperinci skop bisnes, gambaran keseluruhan sistem, pemegang taruh yang terlibat, keperluan pengurusan bisnes, keperluan pengoperasian bisnes dan keperluan proses bisnes. Dokumen ini akan menjadi input kepada penyediaan Spesifikasi Keperluan Sistem yang akan dibangunkan.

### **SEMAKAN DAN PENGESAHAN DOKUMEN**
*Seksyen ini adalah ruangan bagi pegawai-pegawai yang bertanggungjawab untuk melakukan semakan dan pengesahan kepada maklumat-maklumat yang terkandung di dalam dokumen ini.*

**Semakan Dokumen**
| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| < Nama Pengurus Projek/Pembangunan Sistem > | < Jawatan > | | < Tarikh > |
| < Nama Subject Matter Expert (SME) > | < Jawatan > | | < Tarikh > |

**Pengesahan Dokumen**
| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| < Nama Penasihat Projek > | < Jawatan > | | < Tarikh > |
| < Nama Pemilik Projek > | < Jawatan > | | < Tarikh > |

### **KAWALAN DOKUMEN**
*Seksyen ini adalah ruangan untuk mencatatkan maklumat-maklumat penyediaan dokumen termasuk maklumat pindaan yang telah dilakukan ke atas dokumen ini.*

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 1.0 | < Tarikh > | Dokumen versi pertama selesai disediakan | < Nama Penyedia > |
| 1.1 | < Tarikh > | < Contoh: Perubahan dalam rujukan Rajah Aliran Proses Kerja > | < Nama Penyedia > |

---
### **KANDUNGAN**
*Seksyen ini merupakan ruangan untuk memasukkan maklumat kandungan dokumen berserta nombor muka surat yang terlibat.*

### **SENARAI GAMBARAJAH**
*Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi gambarajah-gambarajah yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.*

### **SENARAI JADUAL**
*Seksyen ini merupakan ruangan untuk memasukkan senarai nombor rujukan bagi jadual-jadual yang terkandung di dalam dokumen berserta nombor muka surat yang terlibat.*

### **AKRONIM**
*Sub seksyen ini adalah ruangan untuk menerangkan akronim-akronim yang digunakan di dalam dokumen.*

| Akronim | Keterangan |
| :--- | :--- |
| BRS | Bisness Requirement Spesification |
| SME | Subject Matter Expert |
| ICT | Information and Communications Technology |

### **SUMBER RUJUKAN**
*Seksyen ini adalah ruangan untuk menyenaraikan semua sumber-sumber rujukan yang digunakan di dalam penyediaan dokumen ini.*
> **Contoh:**
> a) Pekeliling Am Bilangan 2 Tahun 2012, Tatacara Pengurusan Aset Tak Alih Kerajaan
> b) Manual Prosedur Kerja Pengurusan Penggunaan Bilik Mesyuarat
> c) Manual Pengguna Sistem Tempahan Bilik Mesyuarat (STMB)

---
## **1. PENGENALAN**

### **1.1 Tujuan Bisnes**
*Seksyen ini adalah ruangan untuk menerangkan latarbelakang, sebab-sebab dan bagaimana sistem yang akan dibangunkan dapat membantu dan menyumbang untuk mencapai objektif bisnes.*
> **Contoh:** Bahagian Khidmat Pengurusan (pemilik sistem) telahpun mempunyai Sistem Tempahan Bilik Mesyuarat dalam menyokong pentadbiran bilik mesyuarat sejak 5 tahun yang lalu. Sistem tersebut terdapat kekurangan dari segi fungsi dan kemudahan yang dibangunkan dalam menyokong cara kerja baru. Oleh itu pemilik sistem telah meminta satu sistem baru yang teratur dan efisyen perlu dibangunkan bagi menangani isu-isu semasa dalam pentadbiran dan pengurusan bilik mesyuarat.

### **1.2 Skop Bisnes**
*Seksyen ini adalah ruangan untuk menjelaskan penentuan skop bagi domain bisnes organisasi yang terlibat.*
> **Contoh:**
> a) Meliputi semua urusan berkaitan dengan pengurusan bilik mesyuarat termasuk permohonan penggunaan bilik, kelulusan permohonan, pembatalan penggunaan, maklumbalas penggunaan bilik, pelaporan kerosakan dan penjanaan laporan yang diperlukan.
> b) Berinteraksi dengan Unit Selenggara Aset dalam melapor kerosakan dan kemaskini status pembaikan.

### **1.3 Gambaran Keseluruhan Bisnes**
*Seksyen ini adalah ruangan untuk menerangkan struktur organisasi yang berkaitan dengan domain bisnes serta hubungannya dengan entiti luar. Penggunaan rajah adalah digalakkan untuk menerang struktur organisasi berkenaan.*
> **Contoh:** Pengurusan Penggunaan Bilik Mesyuarat adalah salah satu fungsi dalam mengurus aset agensi di bawah fungsi mengurus Aset Tak Alih (Bilik Mesyuarat dan Bilik Perbincangan). Rajah 1 di bawah menggambarkan fungsi bisnes Bahagian Khidmat Pengurusan dan hubungkait dengan fungsi-fungsi lain di bahagian yang sama.
> *(Sertakan rajah di sini)*

### **1.4 Senarai Pemegang Taruh**
*Seksyen ini adalah ruangan untuk menyenarai dan menerangkan pemegang-pemegang taruh yang terlibat dengan domain bisnes berkenaan.*
> **Contoh:**
> | Pemegang Taruh | Keterangan |
> | :--- | :--- |
> | Pengurusan Tertinggi | Pegawai-pegawai yang terdiri daripada Ketua Pengarah, Timbalan-timbalan Pengarah serta Pengarah-pengarah Bahagian Agensi |
> | Bahagian Khidmat Pengurusan | Pemilik Proses kepada pengurusan aset tak alih agensi, juga kepada fungsi bisnes Mengurus Penggunaan bilik Mesyuarat. |
> | Warga Agensi | Pengguna-pengguna yang melaksanakan dan menerima perkhidmatan penggunaan bilik mesyuarat dan bilik perbincangan |

---
## **2. KEPERLUAN PENGURUSAN BISNES**

### **2.1 Matlamat dan Objektif**
*Seksyen ini adalah ruangan untuk menyenarai dan menerangkan matlamat, objektif dan hasil bisnes yang ingin dicapai melalui pelaksanaan sistem yang akan dibangunkan.*
> **Contoh:**
> a) Semua rekod bilik mesyuarat dan penggunaannya dapat disimpan secara tepat dan terkini supaya agensi dapat membuat perancangan halatuju seterusnya.
> b) Permintaan dan penggunaan bilik mesyuarat dapat diuruskan secara teratur bagi memenuhi kehendak pelanggan (warga agensi) yang diukur dari maklumbalas penggunaan oleh pelanggan.
> c) Pemantauan kesediaan bilik Mesyuarat bagi kegunaan pelanggan dapat dilakukan secara berkesan melalui komunikasi secara dalam talian dengan unit selenggara.
> d) Penjanaan laporan bilik serta penggunaan dapat disediakan dengan segera dan bila-bila masa oleh pengurusan bagi tujuan membuat keputusan (decision making).

### **2.2 Arkitektur Bisnes**
*Seksyen ini adalah ruangan untuk menjelas dan menyediakan rajah Arkitektur Bisnes yang berkaitan dengan sistem yang akan dibangunkan.*
> **Contoh:** Rajah 2 merupakan Arkitektur Bisnes agensi MAMPU dan hubungkait komponen Perkhidmatan, Sistem Aplikasi, Data dan Teknologi dalam pembangunan Sistem Mengurus Penggunaan Bilik Mesyuarat.
> *(Sertakan rajah di sini)*

### **2.3 Arkitektur Maklumat**
*Seksyen ini adalah ruangan untuk menerangkan Arkitektur Maklumat bagi sistem aplikasi yang akan dibangunkan.*
> **Contoh:** Rajah 3 merupakan Arkitektur Maklumat sistem yang akan dibangunkan, terdiri dari hubungkait antara pengguna, proses bisnes dan maklumat yang diperlu dalam menyokong pelaksanaan bisnes.
> *(Sertakan rajah di sini)*

---
## **3. KEPERLUAN PENGOPERASIAN BISNES**

### **3.1 Keperluan Fungsi Bisnes**
#### **3.1.1 Penggunaan Notasi**
*Seksyen ini adalah ruangan untuk menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Fungsi Bisnes.*
*(Sertakan jadual notasi di sini seperti Jadual 2 dalam dokumen sampel).*

#### **3.1.2 Model Fungsi Bisnes**
*Seksyen ini adalah ruangan untuk menyediakan Model Fungsi Bisnes yang terdiri daripada Rajah Hirarki Fungsi serta keterangan bagi fungsi-fungsi berkenaan.*
*a) Struktur Hierarki fungsi bisnes*
*(Sertakan rajah hierarki di sini seperti Rajah 4 dalam dokumen sampel).*
*b) Keterangan Fungsi Bisnes*
*(Sertakan jadual keterangan fungsi di sini seperti Jadual 3 dalam dokumen sampel).*

#### **3.1.3 Senarai Pengguna**
*Seksyen ini adalah ruangan untuk menyenaraikan senarai pengguna-pengguna yang terlibat secara langsung dengan fungsi bisnes.*
*(Sertakan jadual senarai pengguna di sini seperti Jadual 4 dalam dokumen sampel).*

### **3.2 Keperluan Proses Bisnes**
#### **3.2.1 Penggunaan Notasi**
*Seksyen ini adalah ruangan untuk menyenaraikan notasi-notasi yang akan digunakan untuk menyediakan Model Proses.*
*(Sertakan jadual notasi di sini seperti Jadual 5 dalam dokumen sampel).*

#### **3.2.2 Model dan Definisi Proses Bisnes**
*Seksyen ini adalah ruangan untuk menyediakan Model Proses Bisnes yang merangkumi Aliran Proses Bisnes dan Definisi Fungsi Bisnes.*
*a) PFD-BM-MP-PR Selenggara Profil Pengguna*
***Rajah Aliran Proses***
*(Sertakan rajah aliran proses di sini seperti Rajah 5 dalam dokumen sampel).*
***Definisi Aktiviti Fungsi Bisnes***
*(Sertakan jadual definisi aktiviti di sini seperti Jadual 6 & 7 dalam dokumen sampel).*

*(Ulangi corak di atas untuk setiap proses bisnes yang lain seperti PFD-BM-MP-SB, PFD-BM-MT-TB, dll.)*

---
## **4. PENGIRAAN SAIZ APLIKASI**
*Seksyen ini adalah ruangan untuk menyediakan Pengiraan Saiz Sistem Aplikasi dengan menggunakan kaedah Function Points Analysis.*
> **Contoh:** Jadual 19 adalah rumusan kepada pengiraan saiz aplikasi menggunakan kaedah Function Point Analysis. Pengiraan teperinci boleh dirujuk di Lampiran 1 - Pengiraan Saiz Aplikasi.
*(Sertakan jadual ringkasan di sini seperti Jadual 19 dalam dokumen sampel).*

---
## **LAMPIRAN**
*Seksyen ini merupakan ruangan untuk menyertakan dokumen-dokumen sokongan yang perlu dirujuk seperti pekeliling, minit mesyuarat, borang-borang fizikal, surat-surat dan sebagainya.*
> **Contoh:** LAMPIRAN 1 - PENGIRAAN SAIZ APLIKASI
> *(Sertakan lampiran pengiraan saiz aplikasi seperti di muka surat 43-63 dalam dokumen sampel).*

### **Senarai Gambarajah (List of Diagrams)**

* **Rajah 1:** Gambaran Bisnes Pengurusan Bilik Mesyuarat
* **Rajah 2:** Arkitektur Bisnes MAMPU
* **Rajah 3:** Arkitektur Maklumat Sistem
* **Rajah 4:** Hierarki Fungsi Bisnes Mengurus Penggunaan Bilik Mesyuarat
* **Rajah 5:** Aliran Proses PFD-BM-MP-PR Selenggara Profil Pengguna
* **Rajah 6:** Aliran Proses PFD-BM-MP-SB Selenggara Bilik Mesyuarat
* **Rajah 7:** Aliran Proses PFD-BM-MT-TB Tempah Bilik Mesyuarat
* **Rajah 8:** Aliran Proses PFD-BM-JL Papar Dashboard dan Jana Laporan

### **Senarai Jadual (List of Tables)**

* **Jadual 1:** Senarai Pemegang Taruh
* **Jadual 2:** Notasi Hierarki Fungsi Bisnes
* **Jadual 3:** Keteranan Fungsi Bisnes
* **Jadual 4:** Senarai Pengguna
* **Jadual 5:** Notasi Aliran Proses Bisnes
* **Jadual 6:** Definisi Aktiviti PFD-BM-MP-PR-01 Daftar Profil Pengguna Baru
* **Jadual 7:** Definisi Aktiviti PFD-BM-MP-PR-02 Kemaskini Profil Pengguna
* **Jadual 8:** Definisi Aktiviti PFD-BM-MP-SB-01 Kemaskini Maklumat Bilik Mesyuarat
* **Jadual 9:** Definisi Aktiviti PFD-BM-MP-SB-02 Semak Maklum Balas Pengguna
* **Jadual 10:** Definisi Aktiviti PFD-BM-MP-SB-03 Hantar Aduan Kerosakan
* **Jadual 11:** Definisi Aktiviti PFD-BM-MP-SB-04 Kemaskini Status Bilik Mesyuarat
* **Jadual 12:** Definisi Aktiviti PFD-BM-MT-TB-01 Mohon Tempahan Bilik Mesyuarat
* **Jadual 13:** Definisi Aktiviti PFD-BM-MT-TB-02 Lulus Permohonan Tempahan
* **Jadual 14:** Definisi Aktiviti PFD-BM-MT-TB-03 Semak Status Tempahan
* **Jadual 15:** Definisi Aktiviti PFD-BM-MT-TB-04 Batal Tempahan
* **Jadual 16:** Definisi Aktiviti PFD-BM-MT-TB-05 Beri Maklum Balas Penggunaan Bilik
* **Jadual 17:** Definisi Aktiviti PFD-BM-JL-01 Papar Dashboard dan Statistik
* **Jadual 18:** Definisi Aktiviti PFD-BM-JL-02 Jana Laporan Statistik Bilik Mesyuarat
* **Jadual 19:** Pengiraan Saiz Aplikasi Menggunakan Kaedah Function Point
