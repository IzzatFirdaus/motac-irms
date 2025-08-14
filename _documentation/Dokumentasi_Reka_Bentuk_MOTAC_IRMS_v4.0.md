# Sistem Pengurusan Sumber Terintegrasi MOTAC

Dokumentasi Bahasa Reka Bentuk Versi 5.0 | Untuk Bahagian Pengurusan Maklumat (BPM)
<!-- Dokumen ini mematuhi Malaysia Government Design System (MYDS) dan 18 Prinsip MyGOVEA -->

---

## 1. Prinsip Teras Reka Bentuk

### 1.1 Pematuhan MYDS (Malaysia Government Design System)

- **Standard Kerajaan:** Aplikasi ini menggunakan MYDS sebagai asas reka bentuk untuk memastikan konsistensi dengan platform kerajaan lain.
- **Komponen Standard:** Menggunakan komponen UI MYDS untuk butang, borang, navigasi dan elemen antara muka.
- **Kebolehcapaian:** Mematuhi WCAG 2.1 AA untuk memastikan akses kepada semua pengguna.
- **Responsif:** Menggunakan sistem grid 12-8-4 MYDS untuk desktop, tablet dan mobile.

### 1.2 Berpaksikan Rakyat (Mesra Pengguna & Jelas)

- **Akses Rakyat:** Reka bentuk aplikasi menempatkan keperluan dan kehendak pengguna sebagai fokus utama.
- **Bahasa Melayu Utama:** Bahasa utama antara muka ialah Bahasa Melayu, dengan pilihan dwibahasa mengikut keperluan.
- **Penglibatan Pengguna:** Pengguna dilibatkan dalam setiap fasa pembangunan untuk memastikan aplikasi memenuhi keperluan.
- **Kemudahan Navigasi:** Maksimum 3 klik untuk mencapai fungsi utama, medan wajib ditandakan dengan asterisk merah (*).

### 1.3 Berpacukan Data

- **Pengurusan Data Selamat:** Data diurus secara selamat dan mematuhi PDPA Malaysia.
- **Struktur Data Terancang:** Pemodelan data mengikut standard kerajaan untuk kemudahan integrasi.
- **Perkongsian Data:** Data dikongsi mengikut protokol keselamatan dan keperluan agensi.

### 1.4 Kandungan Terancang

- **Hierarki Maklumat:** Kandungan disusun mengikut kepentingan dan aliran kerja pengguna.
- **Bahasa Formal:** Penggunaan bahasa rasmi yang mudah difahami untuk semua peringkat pengguna.
- **Konsistensi Terminologi:** Istilah teknikal dan pentadbiran digunakan secara konsisten.

---

## 2. Sistem Warna MYDS untuk MOTAC

### 2.1 Palet Warna Utama (Mengikut MYDS)

| Peranan          | Token MYDS           | Nilai Hex    | Penggunaan MOTAC                                    |
|------------------|---------------------|--------------|-----------------------------------------------------|
| **Primary**      | `primary-600`       | #2563EB      | Butang utama, pautan aktif, navigasi utama         |
| **Primary Light**| `primary-300`       | #96B7FF      | Hover states, latar belakang butang sekunder       |
| **Primary Dark** | `primary-700`       | #1D4ED8      | Butang tekan, keadaan fokus                        |
| **MOTAC Brand**  | Custom              | #0055A4      | Logo, header jenama (custom overlay pada primary)  |
| **BPM Accent**   | Custom              | #E60000      | Ikon BPM, notifikasi kritikal                      |

### 2.2 Warna Semantik (MYDS Standard)

| Status           | Token MYDS           | Nilai Hex    | Penggunaan                                          |
|------------------|---------------------|--------------|-----------------------------------------------------|
| **Success**      | `success-600`       | #16A34A      | Mesej kejayaan, status diluluskan                  |
| **Warning**      | `warning-600`       | #CA8A04      | Amaran, status menunggu                            |
| **Danger**       | `danger-600`        | #DC2626      | Ralat, tindakan hapus, status ditolak              |
| **Info**         | `primary-500`       | #3A75F6      | Maklumat tambahan, petua                           |

### 2.3 Warna Neutral (MYDS Standard)

| Elemen           | Token MYDS (Light)   | Token MYDS (Dark)    | Penggunaan                    |
|------------------|---------------------|---------------------|-------------------------------|
| **Background**   | `bg-white`          | `bg-gray-900`       | Latar utama aplikasi          |
| **Surface**      | `bg-gray-50`        | `bg-gray-850`       | Kad, panel, modal             |
| **Border**       | `otl-gray-200`      | `otl-gray-800`      | Sempadan elemen               |
| **Text Primary** | `txt-black-900`     | `txt-white`         | Teks utama                    |
| **Text Secondary**| `txt-black-500`    | `txt-black-500`     | Teks sokongan                 |

---

## 3. Tipografi Mengikut MYDS

### 3.1 Hierarki Teks

| Elemen           | Saiz MYDS            | Font Weight  | Penggunaan MOTAC                          |
|------------------|---------------------|--------------|-------------------------------------------|
| **Heading 1**    | 36px (2.25rem)      | 600          | Tajuk halaman utama                       |
| **Heading 2**    | 30px (1.875rem)     | 600          | Tajuk seksyen                             |
| **Heading 3**    | 24px (1.5rem)       | 600          | Tajuk sub-seksyen                         |
| **Heading 4**    | 20px (1.25rem)      | 600          | Tajuk kad, panel                          |
| **Body Large**   | 18px (1.125rem)     | 400          | Teks pengenalan, kandungan penting        |
| **Body Medium**  | 16px (1rem)         | 400          | Teks badan standard                       |
| **Body Small**   | 14px (0.875rem)     | 400          | Label, teks bantuan                       |
| **Caption**      | 12px (0.75rem)      | 400          | Metadata, cap waktu                       |

### 3.2 Keluarga Font

- **Font Utama:** Inter (MYDS standard) untuk keterbacaan optimum
- **Font Fallback:** Noto Sans untuk sokongan Unicode Bahasa Melayu
- **Line Height:** 1.5 untuk teks badan (mengikut MYDS)
- **Letter Spacing:** Default browser untuk keterbacaan

---

## 4. Komponen UI Mengikut MYDS

### 4.1 Butang (Button Component)

```html
<!-- Butang Utama -->
<button class="myds-button myds-button--primary myds-button--medium">
  Hantar Permohonan
</button>

<!-- Butang Sekunder -->
<button class="myds-button myds-button--secondary myds-button--medium">
  <i class="bi-arrow-left"></i>
  Kembali
</button>

<!-- Butang Bahaya -->
<button class="myds-button myds-button--danger myds-button--medium">
  <i class="bi-trash"></i>
  Hapus
</button>
```

### 4.2 Input Borang (Form Components)

```html
<!-- Input Text dengan Label -->
<div class="myds-form-group">
  <label for="nama" class="myds-label">
    Nama Penuh <span class="text-danger">*</span>
  </label>
  <input type="text" id="nama" class="myds-input" 
         placeholder="Masukkan nama penuh anda" required>
  <div class="myds-hint-text">
    Seperti dalam kad pengenalan
  </div>
</div>

<!-- Select Dropdown -->
<div class="myds-form-group">
  <label for="jabatan" class="myds-label">
    Jabatan <span class="text-danger">*</span>
  </label>
  <select id="jabatan" class="myds-select" required>
    <option value="">Pilih jabatan</option>
    <option value="bpm">Bahagian Pengurusan Maklumat</option>
    <option value="bpp">Bahagian Perancangan dan Penyelidikan</option>
  </select>
</div>
```

### 4.3 Kad dan Panel (Card Components)

```html
<!-- Kad Statistik Dashboard -->
<div class="myds-card">
  <div class="myds-card-header">
    <h3 class="myds-card-title">Permohonan Pinjaman</h3>
  </div>
  <div class="myds-card-content">
    <div class="stat-value">24</div>
    <div class="stat-label">Menunggu Kelulusan</div>
  </div>
</div>

<!-- Panel Status Tiket -->
<div class="myds-panel myds-panel--warning">
  <div class="myds-panel-title">
    <i class="bi-exclamation-triangle"></i>
    Tiket Dalam Tindakan
  </div>
  <div class="myds-panel-content">
    Tiket #HD2024-001 sedang diproses oleh Unit IT
  </div>
</div>
```

### 4.4 Navigasi (Navigation Components)

```html
<!-- Navigasi Sisi Menggunakan MYDS -->
<nav class="myds-sidebar" aria-label="Navigasi utama">
  <div class="myds-sidebar-header">
    <img src="/assets/logo-motac.svg" alt="MOTAC" height="40">
  </div>
  <ul class="myds-sidebar-menu">
    <li class="myds-sidebar-item">
      <a href="/dashboard" class="myds-sidebar-link myds-sidebar-link--active">
        <i class="bi-house-door"></i>
        <span>Papan Pemuka</span>
      </a>
    </li>
    <li class="myds-sidebar-item">
      <a href="/pinjaman" class="myds-sidebar-link">
        <i class="bi-laptop"></i>
        <span>Pinjaman ICT</span>
      </a>
    </li>
    <li class="myds-sidebar-item">
      <a href="/helpdesk" class="myds-sidebar-link">
        <i class="bi-headset"></i>
        <span>Helpdesk</span>
      </a>
    </li>
  </ul>
</nav>
```

---

## 5. Layout dan Grid System

### 5.1 Grid System MYDS (12-8-4)

```html
<!-- Layout Desktop (12 Column) -->
<div class="myds-container">
  <div class="myds-row">
    <!-- Sidebar Navigation (3 columns) -->
    <div class="myds-col-12 myds-col-md-3">
      <nav class="myds-sidebar">
        <!-- Navigasi content -->
      </nav>
    </div>
    
    <!-- Main Content (9 columns) -->
    <div class="myds-col-12 myds-col-md-9">
      <main class="myds-main-content">
        <!-- Kandungan utama -->
      </main>
    </div>
  </div>
</div>

<!-- Grid untuk Borang (8 Column pada Tablet) -->
<div class="myds-row">
  <div class="myds-col-12 myds-col-md-6 myds-col-lg-4">
    <!-- Input field 1 -->
  </div>
  <div class="myds-col-12 myds-col-md-6 myds-col-lg-4">
    <!-- Input field 2 -->
  </div>
</div>
```

### 5.2 Breakpoints MYDS

| Peranti    | Saiz Skrin     | Grid Columns | Gap    | Max Width  |
|------------|---------------|--------------|--------|------------|
| **Mobile** | ≤ 767px       | 4            | 18px   | 100%       |
| **Tablet** | 768px-1023px  | 8            | 24px   | 100%       |
| **Desktop**| ≥ 1024px      | 12           | 24px   | 1280px     |

---

## 6. Komponen Khusus MOTAC

### 6.1 Status Badge untuk Workflow

```html
<!-- Status Permohonan -->
<span class="myds-badge myds-badge--warning">
  <i class="bi-clock"></i>
  Menunggu Kelulusan
</span>

<span class="myds-badge myds-badge--success">
  <i class="bi-check-circle"></i>
  Diluluskan
</span>

<span class="myds-badge myds-badge--danger">
  <i class="bi-x-circle"></i>
  Ditolak
</span>

<!-- Status Tiket Helpdesk -->
<span class="myds-badge myds-badge--primary">
  <i class="bi-envelope-open"></i>
  Buka
</span>

<span class="myds-badge myds-badge--warning">
  <i class="bi-arrow-repeat"></i>
  Dalam Tindakan
</span>
```

### 6.2 Aliran Kerja Visual

```html
<!-- Stepper untuk Proses Pinjaman ICT -->
<div class="myds-stepper">
  <div class="myds-stepper-item myds-stepper-item--completed">
    <div class="myds-stepper-icon">
      <i class="bi-check"></i>
    </div>
    <div class="myds-stepper-content">
      <h4>Permohonan Dihantar</h4>
      <p>12 Ogos 2025, 2:30 PM</p>
    </div>
  </div>
  
  <div class="myds-stepper-item myds-stepper-item--active">
    <div class="myds-stepper-icon">
      <i class="bi-person-check"></i>
    </div>
    <div class="myds-stepper-content">
      <h4>Menunggu Kelulusan Penyelia</h4>
      <p>Status semasa</p>
    </div>
  </div>
  
  <div class="myds-stepper-item">
    <div class="myds-stepper-icon">
      <i class="bi-laptop"></i>
    </div>
    <div class="myds-stepper-content">
      <h4>Keluaran Peralatan</h4>
      <p>Tertangguh</p>
    </div>
  </div>
</div>
```

### 6.3 Templat Borang MOTAC

```html
<!-- Borang Pinjaman ICT (PK.(S).MOTAC.07.(L3)) -->
<form class="myds-form">
  <!-- Header Borang -->
  <div class="myds-form-header">
    <h1>Borang Permohonan Pinjaman Peralatan ICT</h1>
    <p class="myds-form-reference">Rujukan: PK.(S).MOTAC.07.(L3)</p>
  </div>
  
  <!-- Bahagian 1: Maklumat Pemohon -->
  <div class="myds-form-section">
    <h2>BAHAGIAN 1: MAKLUMAT PEMOHON</h2>
    
    <div class="myds-row">
      <div class="myds-col-12 myds-col-md-6">
        <div class="myds-form-group">
          <label for="nama_penuh" class="myds-label">
            Nama Penuh <span class="text-danger">*</span>
          </label>
          <input type="text" id="nama_penuh" class="myds-input" required>
        </div>
      </div>
      
      <div class="myds-col-12 myds-col-md-6">
        <div class="myds-form-group">
          <label for="no_pekerja" class="myds-label">
            No. Pekerja <span class="text-danger">*</span>
          </label>
          <input type="text" id="no_pekerja" class="myds-input" required>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Bahagian 2: Peralatan Diperlukan -->
  <div class="myds-form-section">
    <h2>BAHAGIAN 2: PERALATAN DIPERLUKAN</h2>
    
    <div class="myds-form-group">
      <label for="jenis_peralatan" class="myds-label">
        Jenis Peralatan <span class="text-danger">*</span>
      </label>
      <select id="jenis_peralatan" class="myds-select" required>
        <option value="">Pilih jenis peralatan</option>
        <option value="laptop">Laptop</option>
        <option value="tablet">Tablet</option>
        <option value="projektor">Projektor</option>
      </select>
    </div>
  </div>
  
  <!-- Butang Tindakan -->
  <div class="myds-form-actions">
    <button type="button" class="myds-button myds-button--secondary">
      Simpan Draf
    </button>
    <button type="submit" class="myds-button myds-button--primary">
      Hantar Permohonan
    </button>
  </div>
</form>
```

---

## 7. Kebolehcapaian (Accessibility)

### 7.1 Pematuhan WCAG 2.1 AA

```html
<!-- Contoh implementasi kebolehcapaian -->

<!-- Skip Link untuk navigasi papan kekunci -->
<a href="#main-content" class="myds-skip-link">
  Langkau ke kandungan utama
</a>

<!-- Navigation dengan ARIA labels -->
<nav class="myds-sidebar" aria-label="Navigasi utama" role="navigation">
  <ul class="myds-sidebar-menu">
    <li>
      <a href="/dashboard" 
         class="myds-sidebar-link" 
         aria-current="page"
         aria-describedby="dashboard-desc">
        <i class="bi-house-door" aria-hidden="true"></i>
        <span>Papan Pemuka</span>
      </a>
      <span id="dashboard-desc" class="sr-only">
        Halaman utama dengan ringkasan aktiviti
      </span>
    </li>
  </ul>
</nav>

<!-- Form dengan label dan error handling -->
<div class="myds-form-group">
  <label for="email" class="myds-label">
    Alamat E-mel <span class="text-danger">*</span>
  </label>
  <input type="email" 
         id="email" 
         class="myds-input"
         aria-describedby="email-help email-error"
         aria-invalid="false"
         required>
  <div id="email-help" class="myds-hint-text">
    Gunakan e-mel rasmi organisasi
  </div>
  <div id="email-error" class="myds-error-text" role="alert" aria-live="polite">
    <!-- Error message akan dipaparkan di sini -->
  </div>
</div>
```

### 7.2 Focus Management

```css
/* Focus indicators mengikut MYDS */
.myds-button:focus,
.myds-input:focus,
.myds-select:focus {
  outline: 3px solid var(--myds-focus-ring-primary);
  outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .myds-button {
    border: 2px solid currentColor;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .myds-transition {
    transition: none;
  }
}
```

---

## 8. Responsif dan Mobile-First

### 8.1 Mobile Navigation

```html
<!-- Hamburger menu untuk mobile -->
<header class="myds-header">
  <button class="myds-menu-toggle myds-button--ghost" 
          aria-expanded="false"
          aria-controls="main-nav"
          aria-label="Buka menu navigasi">
    <i class="bi-list"></i>
  </button>
  
  <div class="myds-header-brand">
    <img src="/assets/logo-motac-mobile.svg" alt="MOTAC" height="32">
  </div>
  
  <div class="myds-header-actions">
    <button class="myds-button--ghost" aria-label="Notifikasi">
      <i class="bi-bell"></i>
      <span class="myds-badge myds-badge--danger myds-badge--sm">3</span>
    </button>
  </div>
</header>

<!-- Mobile-friendly form layout -->
<div class="myds-form-mobile">
  <div class="myds-form-group">
    <label for="tarikh_pinjaman" class="myds-label">
      Tarikh Pinjaman <span class="text-danger">*</span>
    </label>
    <!-- Date picker optimized for mobile -->
    <input type="date" 
           id="tarikh_pinjaman" 
           class="myds-input myds-input--mobile"
           required>
  </div>
</div>
```

### 8.2 Touch-Friendly Interactions

```css
/* Touch target sizes mengikut MYDS */
.myds-button,
.myds-input,
.myds-select {
  min-height: 44px; /* Minimum touch target */
  min-width: 44px;
}

.myds-button--large {
  min-height: 56px;
  padding: 16px 24px;
}

/* Mobile-specific spacing */
@media (max-width: 767px) {
  .myds-form-group {
    margin-bottom: 24px;
  }
  
  .myds-card {
    margin-bottom: 16px;
  }
  
  .myds-button {
    width: 100%;
    margin-bottom: 12px;
  }
}
```

---

## 9. Templat E-mel

### 9.1 Struktur E-mel MOTAC

```html
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi MOTAC IRMS</title>
</head>
<body style="margin: 0; padding: 0; font-family: Inter, sans-serif;">
  <!-- Header dengan logo MOTAC -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #2563EB;">
    <tr>
      <td style="padding: 20px; text-align: center;">
        <img src="https://irms.motac.gov.my/assets/logo-motac-white.png" 
             alt="MOTAC" 
             width="120" 
             style="height: auto;">
      </td>
    </tr>
  </table>
  
  <!-- Kandungan utama -->
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto;">
    <tr>
      <td style="padding: 40px 20px;">
        <h1 style="color: #18181B; font-size: 24px; margin-bottom: 16px;">
          Permohonan Pinjaman ICT Diluluskan
        </h1>
        
        <p style="color: #3F3F46; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
          Yang dihormati En./Pn. [NAMA_PEMOHON],
        </p>
        
        <p style="color: #3F3F46; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
          Permohonan pinjaman peralatan ICT anda telah diluluskan. 
          Sila ambil peralatan di Bahagian Pengurusan Maklumat dalam masa 3 hari bekerja.
        </p>
        
        <!-- Call-to-action button -->
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td style="background-color: #2563EB; border-radius: 6px;">
              <a href="[LINK_SISTEM]" 
                 style="display: inline-block; padding: 12px 24px; 
                        color: white; text-decoration: none; font-weight: 500;">
                Lihat Butiran Permohonan
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  <!-- Footer -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F8F9FA; margin-top: 40px;">
    <tr>
      <td style="padding: 20px; text-align: center;">
        <img src="https://irms.motac.gov.my/assets/logo-bpm.png" 
             alt="Bahagian Pengurusan Maklumat" 
             width="80" 
             style="height: auto; margin-bottom: 8px;">
        <p style="color: #71717A; font-size: 12px; margin: 0;">
          Bahagian Pengurusan Maklumat<br>
          Kementerian Pelancongan, Seni dan Budaya Malaysia
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
```

---

## 10. Testing dan Quality Assurance

### 10.1 Senarai Semak Pematuhan MYDS

- [ ] **Warna:** Menggunakan token warna MYDS yang betul
- [ ] **Tipografi:** Font Inter dengan hierarki saiz MYDS
- [ ] **Komponen:** Menggunakan komponen MYDS standard
- [ ] **Grid:** Layout mengikut sistem grid 12-8-4
- [ ] **Accessibility:** Lulus audit WCAG 2.1 AA
- [ ] **Responsif:** Berfungsi pada mobile, tablet, desktop
- [ ] **Performance:** Loading time < 3 saat
- [ ] **Jenama:** Logo dan warna MOTAC/BPM digunakan dengan betul

### 10.2 Browser Support

| Browser        | Versi Minimum | Status Support |
|----------------|---------------|----------------|
| Chrome         | 90+           | ✅ Penuh      |
| Firefox        | 88+           | ✅ Penuh      |
| Safari         | 14+           | ✅ Penuh      |
| Edge           | 90+           | ✅ Penuh      |
| Mobile Safari  | iOS 14+       | ✅ Penuh      |
| Chrome Mobile  | 90+           | ✅ Penuh      |

---

## 11. Pematuhan 18 Prinsip MyGOVEA

Semua keputusan reka bentuk dalam dokumen ini mematuhi 18 Prinsip Reka Bentuk MyGOVEA:

1. **Berpaksikan Rakyat** - Navigasi mudah, bahasa jelas
2. **Berpacukan Data** - Struktur data terancang, keselamatan terjamin
3. **Kandungan Terancang** - Hierarki maklumat yang logik
4. **Teknologi Bersesuaian** - Menggunakan MYDS dan standard web
5. **Antara Muka Minimalis** - Komponen MYDS yang bersih
6. **Seragam** - Konsisten dengan platform kerajaan lain
7. **Paparan Jelas** - Typography dan warna yang mudah dibaca
8. **Realistik** - Mengikut keupayaan teknikal dan keperluan pengguna
9. **Kognitif** - Mengurangkan beban mental pengguna
10. **Fleksibel** - Responsive dan mudah dikembang
11. **Komunikasi** - Dokumentasi dan feedback yang jelas
12. **Struktur Hierarki** - Navigasi dan kandungan yang teratur
13. **Komponen UI/UX** - Menggunakan MYDS component library
14. **Tipografi** - Font Inter dengan hierarki yang betul
15. **Tetapan Lalai** - Configuration yang selamat dan mudah
16. **Kawalan Pengguna** - Interface yang boleh dikawal pengguna
17. **Pencegahan Ralat** - Validation dan error handling
18. **Panduan Dokumentasi** - Manual yang lengkap dan terkini

---

*Dokumen diselenggara oleh Pejabat Reka Bentuk BPM*  
*Kemaskini terakhir: 14 Ogos 2025*  
*Versi: 5.0 (MYDS Compliant)*

## Key Changes Made

1. **MYDS Compliance**: Updated color palette, typography, and components to follow MYDS standards
2. **Color Tokens**: Replaced custom colors with MYDS token system while preserving MOTAC brand colors as custom overlays
3. **Typography**: Updated to use Inter font and MYDS text hierarchy
4. **Components**: Added proper MYDS component usage examples with HTML/CSS
5. **Grid System**: Implemented MYDS 12-8-4 responsive grid system
6. **Accessibility**: Enhanced WCAG 2.1 AA compliance examples
7. **Mobile-First**: Added mobile-specific patterns and touch-friendly interactions
8. **Email Templates**: Updated with MYDS-compliant styling
9. **Quality Assurance**: Added MYDS compliance checklist
10. **Documentation**: Better structure following MYDS documentation patterns

The documentation now properly aligns with Malaysia's official design system while maintaining MOTAC's specific branding and workflow requirements.
