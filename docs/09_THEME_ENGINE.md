# 🌌 09 — Theme Engine

> **Spesifikasi resmi Theme Engine** — sistem manajemen tema visual CosmicLib yang menangani seluruh aspek tampilan tanpa menyentuh Business Logic.
>
> Theme Engine memungkinkan setiap sekolah mengubah tampilan CosmicLib Library sesuai identitas visual mereka secara dinamis dan aman.
>
> Baca setelah [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md), [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md), dan [`16_SETTING_ENGINE.md`](16_SETTING_ENGINE.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/09_THEME_ENGINE.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Final Blueprint` — spesifikasi resmi Theme Engine |
| **Engine** | CosmicLib Engine |
| **Framework** | Laravel 12 · PHP 8.3+ · MySQL 8+ |
| **Frontend** | Blade · Bootstrap 5.3 · Vite |
| **Arsitektur** | Modular Presentation Layer |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi Theme Engine](#2-filosofi-theme-engine)
3. [Theme Lifecycle](#3-theme-lifecycle)
4. [Theme Structure](#4-theme-structure)
5. [theme.json](#5-themejson)
6. [Theme Loader](#6-theme-loader)
7. [Theme Manager](#7-theme-manager)
8. [Theme Customizer](#8-theme-customizer)
9. [Layout System](#9-layout-system)
10. [Component System](#10-component-system)
11. [Color System](#11-color-system)
12. [Typography](#12-typography)
13. [Dark Mode](#13-dark-mode)
14. [Responsive Design](#14-responsive-design)
15. [Theme Assets](#15-theme-assets)
16. [Theme Configuration](#16-theme-configuration)
17. [Theme Compatibility](#17-theme-compatibility)
18. [Theme Security](#18-theme-security)
19. [Theme Marketplace (Future)](#19-theme-marketplace-future)
20. [Default Themes](#20-default-themes)
21. [AI Rules](#21-ai-rules)
22. [Best Practice](#22-best-practice)
23. [Architecture Diagram](#23-architecture-diagram)
24. [Theme Checklist](#24-theme-checklist)

---

## 1. Pendahuluan

### 1.1 Apa Itu Theme Engine?

**Theme Engine** adalah subsistem CosmicLib yang bertanggung jawab mengelola seluruh aspek presentasi visual aplikasi. Theme Engine menangani pemuatan tema, pengaturan layout, komponen UI, warna, tipografi, aset (CSS/JS/gambar), serta kustomisasi tampilan — semuanya tanpa mengubah satu baris pun Business Logic.

Theme Engine memastikan bahwa:

- **Tampilan dapat diganti kapan saja** — sekolah dapat berganti tema tanpa memengaruhi data atau modul.
- **Kustomisasi tanpa coding** — admin dapat mengubah warna, font, logo melalui panel tanpa menyentuh kode.
- **Konsistensi visual** — seluruh halaman menggunakan sistem desain yang sama melalui CSS Variables dan Blade Components.
- **Performa optimal** — aset tema dimuat secara efisien melalui Vite dan lazy loading.
- **Aksesibilitas terjaga** — semua tema dibangun dengan standar WCAG.

### 1.2 Perbedaan Theme Engine dan Module Engine

| Aspek | Theme Engine | Module Engine |
| :--- | :--- | :--- |
| **Fokus** | Presentasi visual & UI | Fitur bisnis & fungsionalitas |
| **Tanggung Jawab** | Layout, warna, font, aset, komponen UI | Route, controller, service, repository, migration |
| **Dampak** | Hanya mengubah tampilan | Menambah/mengubah fungsionalitas |
| **Business Logic** | **Tidak boleh** mengandung Business Logic | Berisi Business Logic sesuai fitur |
| **Database** | Tidak boleh membuat tabel sendiri (kecuali theme customizer settings) | Memiliki migration & tabel sendiri |
| **Dependency** | Bergantung pada Module Engine untuk komponen yang perlu data | Mandiri |
| **Aktif** | Satu tema aktif dalam satu waktu | Banyak modul aktif bersamaan |

### 1.3 Theme Hanya Mengatur Tampilan

Prinsip fundamental Theme Engine:

> **Theme Engine adalah lapisan presentasi murni.**
>
> - Theme **tidak boleh** menjalankan query database.
> - Theme **tidak boleh** mengandung logika bisnis.
> - Theme **tidak boleh** mengubah data.
> - Theme **tidak boleh** membuat route sendiri (kecuali route preview).
> - Theme **hanya** mengatur bagaimana data ditampilkan.

---

## 2. Filosofi Theme Engine

### 2.1 Theme Sebagai Lapisan Presentasi

Theme dalam CosmicLib adalah **lapisan presentasi** yang terpisah sepenuhnya dari logika aplikasi. Ini mengikuti arsitektur分层:

```text
┌──────────────────────────────────────┐
│         Business Logic               │   ← Modules, Services, Repositories
│         (Tidak berubah)              │
├──────────────────────────────────────┤
│         Theme Layer                  │   ← Layout, Komponen, CSS, Asset
│         (Dapat diganti)              │
├──────────────────────────────────────┤
│         Framework (Laravel)          │   ← Routing, Controller dasar
└──────────────────────────────────────┘
```

### 2.2 Theme Dapat Diganti Tanpa Efek Samping

Sekolah dapat mengganti tema kapan saja tanpa:

- Kehilangan data pengguna, buku, atau transaksi.
- Mengubah konfigurasi modul.
- Memengaruhi permission atau role.
- Mengganggu workflow bisnis.

### 2.3 Theme Mendukung Branding Sekolah

Setiap sekolah memiliki identitas visual unik. Theme Engine memungkinkan:

| Kebutuhan Branding | Dukungan Theme Engine |
| :--- | :--- |
| Logo sekolah | Upload logo via Theme Customizer |
| Warna korporat | Ubah primary, secondary, accent warna |
| Font khas | Pilih font sesuai branding sekolah |
| Banner kustom | Hero banner dengan konten sekolah |
| Nama sekolah | Tampil di header, login page, footer |
| Alamat & kontak | Konfigurasi footer content |

### 2.4 Prinsip Desain Theme Engine

| Prinsip | Implementasi |
| :--- | :--- |
| **Modular** | Setiap tema adalah paket mandiri dengan struktur terstandarisasi |
| **Dynamic** | Tema dapat diganti runtime tanpa restart aplikasi |
| **Configurable** | Semua aspek visual dapat dikonfigurasi via database |
| **Responsive** | Semua tema wajib mendukung desktop, tablet, mobile |
| **Dark Mode Ready** | Setiap tema wajib menyediakan palet dark dan light |
| **AI Friendly** | Struktur konsisten, dokumentasi lengkap, kontrak eksplisit |
| **Enterprise Ready** | Mendukung multi-sekolah, branding kustom, audit trail perubahan tema |
| **Shared Hosting Friendly** | Aset tema dioptimasi untuk hosting dengan resource terbatas |

---

## 3. Theme Lifecycle

Setiap tema dalam CosmicLib melalui siklus hidup lengkap sebagai berikut:

```text
                    ┌──────────┐
                    │   DRAFT   │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │  INSTALL  │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │  PREVIEW  │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │ ACTIVATE  │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │CUSTOMIZE  │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │  UPDATE   │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │DEACTIVATE │
                    └────┬─────┘
                         │
                         ▼
                    ┌──────────┐
                    │  REMOVE   │
                    └──────────┘
```

### 3.1 Detail Setiap Fase

| Fase | Deskripsi | Siapa yang Melakukan | Efek pada Sistem |
| :--- | :--- | :--- | :--- |
| **Draft** | Tema dalam pengembangan, belum siap digunakan | Developer | Tidak terlihat di sistem |
| **Install** | Tema dipasang ke dalam sistem | Admin / Developer | File di-copy ke `themes/`, registrasi di database |
| **Preview** | Admin melihat pratinjau tema sebelum diaktifkan | Admin | Tampil di halaman preview, tidak memengaruhi pengguna lain |
| **Activate** | Tema diterapkan ke seluruh sistem | Admin | Semua halaman menggunakan tema ini |
| **Customize** | Admin mengubah pengaturan visual tema | Admin | Perubahan disimpan di Setting Engine |
| **Update** | Tema diperbarui ke versi lebih baru | Admin / Developer | File tema diperbarui, migrasi konfigurasi |
| **Deactivate** | Tema dinonaktifkan, fallback ke tema default | Admin | Sistem kembali ke tema default |
| **Remove** | Tema dihapus dari sistem permanen | Admin | File dan data tema dihapus |

### 3.2 Aturan Lifecycle

- Tema **tidak bisa** diaktifkan tanpa melalui fase **Install**.
- Tema **tidak bisa** langsung dihapus jika masih **Active** — harus **Deactivate** terlebih dahulu.
- Fase **Preview** bersifat opsional tetapi sangat direkomendasikan sebelum **Activate**.
- **Update** dapat dilakukan kapan saja tanpa harus menonaktifkan tema.
- Saat **Remove**, konfigurasi kustom tema **harus** dibackup terlebih dahulu.

---

## 4. Theme Structure

Setiap tema dalam CosmicLib memiliki struktur direktori standar sebagai berikut:

### 4.1 Struktur Folder Tema

```text
themes/
└── {theme-name}/
    ├── theme.json                    ← Metadata & konfigurasi tema (WAJIB)
    ├── README.md                     ← Dokumentasi tema (WAJIB)
    ├── preview.png                   ← Gambar preview tema (WAJIB)
    │
    ├── Layouts/                      ← Template layout Blade
    │   ├── master.blade.php          ← Layout master utama
    │   ├── admin.blade.php           ← Layout halaman admin
    │   ├── guest.blade.php           ← Layout halaman publik
    │   ├── auth.blade.php            ← Layout halaman login/register
    │   ├── landing.blade.php         ← Layout halaman beranda/landing
    │   ├── error.blade.php           ← Layout halaman error (403, 404, 500)
    │   └── print.blade.php           ← Layout cetak
    │
    ├── Pages/                        ← Template halaman spesifik
    │   ├── dashboard.blade.php       ← Halaman dashboard
    │   ├── profile.blade.php         ← Halaman profil
    │   ├── login.blade.php           ← Halaman login
    │   └── register.blade.php        ← Halaman registrasi
    │
    ├── Components/                   ← Blade Components kustom tema
    │   ├── navbar.blade.php          ← Komponen navbar
    │   ├── sidebar.blade.php         ← Komponen sidebar
    │   ├── footer.blade.php          ← Komponen footer
    │   ├── card.blade.php            ← Komponen card
    │   ├── modal.blade.php           ← Komponen modal
    │   ├── alert.blade.php           ← Komponen alert
    │   ├── breadcrumb.blade.php      ← Komponen breadcrumb
    │   ├── pagination.blade.php      ← Komponen pagination
    │   ├── table.blade.php           ← Komponen table
    │   ├── button.blade.php          ← Komponen button
    │   └── widget.blade.php          ← Komponen widget
    │
    ├── Partials/                     ← Potongan view parsial
    │   ├── header.blade.php          ← Header partial
    │   ├── navbar-top.blade.php      ← Navbar atas
    │   ├── navbar-side.blade.php     ← Sidebar navigasi
    │   ├── footer-main.blade.php     ← Footer utama
    │   ├── scripts.blade.php         ← Script JS
    │   ├── styles.blade.php          ← Style CSS
    │   └── analytics.blade.php       ← Tracking/analytics
    │
    ├── Assets/                       ← Aset tema (public)
    │   ├── CSS/
    │   │   ├── theme.css              ← CSS utama tema
    │   │   ├── theme-dark.css         ← CSS dark mode
    │   │   ├── theme-rtl.css          ← CSS RTL (jika didukung)
    │   │   └── custom.css             ← CSS kustom (dari customizer)
    │   │
    │   ├── JS/
    │   │   ├── theme.js               ← JavaScript utama tema
    │   │   ├── theme-init.js          ← Inisialisasi tema
    │   │   └── customizer.js          ← JS untuk theme customizer
    │   │
    │   ├── Images/
    │   │   ├── logo.svg               ← Logo default tema
    │   │   ├── favicon.ico            ← Favicon default tema
    │   │   ├── login-bg.jpg           ← Background halaman login
    │   │   ├── hero-banner.jpg        ← Banner hero landing
    │   │   └── default-avatar.png     ← Avatar default
    │   │
    │   ├── Fonts/                     ← Font kustom tema
    │   │   └── {font-name}/
    │   │       ├── font.woff2
    │   │       └── font.woff
    │   │
    │   └── SVG/                       ← Icon SVG kustom tema
    │       ├── icons.svg
    │       └── logo-icon.svg
    │
    ├── Lang/                          ← Terjemahan spesifik tema
    │   ├── id/
    │   │   └── theme.php
    │   └── en/
    │       └── theme.php
    │
    ├── Config/                        ← Konfigurasi tambahan tema
    │   ├── widgets.php               ← Widget yang tersedia di tema
    │   └── menus.php                 ← Menu default tema
    │
    └── Screenshots/                   ← Screenshot untuk dokumentasi
        ├── dashboard.png
        ├── login.png
        ├── landing.png
        └── mobile.png
```

### 4.2 Penjelasan Setiap Direktori

| Direktori | Wajib | Fungsi |
| :--- | :--- | :--- |
| `Layouts/` | Ya | Template layout utama yang digunakan oleh seluruh halaman |
| `Pages/` | Tidak | Template untuk halaman spesifik yang ingin di-override oleh tema |
| `Components/` | Tidak | Blade Components kustom dengan gaya tema |
| `Partials/` | Tidak | Potongan view kecil yang digunakan di layout |
| `Assets/` | Ya | Aset publik tema (CSS, JS, gambar, font) |
| `Lang/` | Tidak | File terjemahan spesifik tema |
| `Config/` | Tidak | Konfigurasi tambahan seperti widget dan menu default tema |
| `Screenshots/` | Tidak | Screenshot untuk dokumentasi dan marketplace |

### 4.3 Aturan Struktur

- Setiap tema **WAJIB** memiliki `theme.json`, `README.md`, dan `preview.png`.
- Direktori `Layouts/` dan `Assets/` **WAJIB** ada meskipun hanya berisi file minimal.
- File yang tidak ada akan di-fallback ke tema **Cosmic Default** (tema bawaan).
- Nama direktori tema menggunakan **kebab-case** (contoh: `cosmic-modern`, `school-blue`).

---

## 5. theme.json

File `theme.json` adalah **manifest utama** setiap tema. File ini berisi seluruh metadata, konfigurasi, dan deklarasi kemampuan tema.

### 5.1 Struktur theme.json

```json
{
  "name": "Cosmic Default",
  "slug": "cosmic-default",
  "description": "Tema default CosmicLib dengan desain modern dan responsif.",
  "version": "1.0.0",
  "author": {
    "name": "Yusuf Wardana",
    "email": "dev@cosmiclib.id",
    "url": "https://cosmiclib.id"
  },
  "homepage": "https://cosmiclib.id/themes/cosmic-default",
  "license": "MIT",
  "provider": "Themes\\CosmicDefault\\Providers\\ThemeServiceProvider",

  "minimum_core": "1.0.0",
  "minimum_php": "8.2",
  "minimum_laravel": "12.0",

  "supported_modules": ["*"],
  "excluded_modules": [],

  "color_scheme": {
    "type": "dynamic",
    "default_mode": "light",
    "dark_mode_supported": true
  },

  "dark_mode": true,
  "rtl": false,
  "responsive": true,

  "preview": "preview.png",
  "screenshots": [
    "screenshots/dashboard.png",
    "screenshots/login.png",
    "screenshots/landing.png"
  ],

  "layouts": {
    "master": "Layouts.master",
    "admin": "Layouts.admin",
    "guest": "Layouts.guest",
    "auth": "Layouts.auth",
    "landing": "Layouts.landing",
    "error": "Layouts.error",
    "print": "Layouts.print"
  },

  "assets": {
    "css": [
      "Assets/CSS/theme.css",
      "Assets/CSS/theme-dark.css"
    ],
    "js": [
      "Assets/JS/theme.js",
      "Assets/JS/theme-init.js"
    ],
    "fonts": [
      "Assets/Fonts/inter/inter.woff2",
      "Assets/Fonts/jetbrains-mono/jetbrains-mono.woff2"
    ]
  },

  "dependencies": {
    "modules": [],
    "plugins": [],
    "themes": []
  },

  "settings": {
    "default": {
      "primary_color": "#4F46E5",
      "secondary_color": "#7C3AED",
      "accent_color": "#06B6D4",
      "font_family": "Inter",
      "heading_font": "Outfit",
      "sidebar_style": "default",
      "navbar_style": "default",
      "layout_width": "full"
    }
  }
}
```

### 5.2 Penjelasan Property

#### Metadata Dasar

| Property | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `name` | `string` | Ya | Nama tema (human-readable, maksimal 100 karakter) |
| `slug` | `string` | Ya | Identitas unik tema (kebab-case, hanya huruf kecil dan tanda hubung) |
| `description` | `string` | Ya | Deskripsi tema (maksimal 500 karakter) |
| `version` | `string` | Ya | Versi tema menggunakan **Semantic Versioning** (`x.y.z`) |
| `author` | `object` | Ya | Informasi pembuat tema |
| `homepage` | `string` | Tidak | URL website/dokumentasi tema |
| `license` | `string` | Ya | Lisensi tema (contoh: `MIT`, `GPL-3.0`) |
| `provider` | `string` | Tidak | Service Provider tema (jika tema memiliki boot logic) |

#### Persyaratan Sistem

| Property | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `minimum_core` | `string` | Ya | Versi minimum CosmicLib Core yang dibutuhkan |
| `minimum_php` | `string` | Ya | Versi minimum PHP yang dibutuhkan |
| `minimum_laravel` | `string` | Ya | Versi minimum Laravel yang dibutuhkan |

#### Kompatibilitas Modul

| Property | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `supported_modules` | `array` | Tidak | Daftar modul yang didukung (`["*"]` untuk semua modul) |
| `excluded_modules` | `array` | Tidak | Daftar modul yang tidak didukung oleh tema |

#### Kemampuan Tema

| Property | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `color_scheme` | `object` | Ya | Konfigurasi skema warna tema |
| `dark_mode` | `boolean` | Ya | Apakah tema mendukung dark mode |
| `rtl` | `boolean` | Ya | Apakah tema mendukung arah kanan-ke-kiri |
| `responsive` | `boolean` | Ya | Apakah tema responsif |
| `preview` | `string` | Ya | Path ke gambar preview tema (relatif terhadap root tema) |

#### Layout & Asset

| Property | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `layouts` | `object` | Ya | Mapping nama layout ke path Blade view |
| `assets` | `object` | Ya | Daftar aset CSS, JS, dan font yang akan dimuat |
| `dependencies` | `object` | Tidak | Dependensi tema terhadap modul/plugin/tema lain |
| `settings` | `object` | Ya | Nilai default konfigurasi tema |

---

## 6. Theme Loader

### 6.1 Tanggung Jawab Theme Loader

Theme Loader adalah subsistem Core Engine yang bertanggung jawab atas:

| Kemampuan | Deskripsi |
| :--- | :--- |
| **Scan Folder Themes** | Mendeteksi semua tema yang tersedia di direktori `themes/` |
| **Membaca theme.json** | Mem-parsing dan memvalidasi file manifest setiap tema |
| **Compatibility Check** | Memverifikasi kompatibilitas tema dengan versi Core, PHP, dan Laravel |
| **Register Theme** | Mendaftarkan tema ke dalam registry sistem |
| **Load Layout** | Mengarahkan Blade view path ke folder layout tema aktif |
| **Load Components** | Meregistrasi namespace Blade Components tema |
| **Load Assets** | Memuat CSS, JS, dan font tema melalui Vite |
| **Load Configuration** | Menggabungkan konfigurasi default tema dengan kustomisasi database |

### 6.2 Alur Loading Tema

```text
Theme Loader
    │
    ├── 1. INISIALISASI
    │       ├── Baca Setting Engine: active_theme
    │       ├── Jika tidak ada → gunakan tema default "cosmic-default"
    │       └── Jika tema tidak ditemukan → fallback ke "cosmic-default"
    │
    ├── 2. VALIDASI TEMA
    │       ├── Cek direktori themes/{slug}/ ada
    │       ├── Parse theme.json
    │       ├── Validasi format JSON
    │       ├── Cek minimum_core ≥ versi CosmicLib
    │       ├── Cek minimum_php ≥ PHP versi server
    │       ├── Cek minimum_laravel ≥ Laravel versi server
    │       └── Jika validasi gagal → fallback + log error
    │
    ├── 3. REGISTRASI VIEW
    │       ├── Registrasi view namespace: "theme" → themes/{slug}/
    │       ├── Set view finder paths (prioritas tema aktif)
    │       └── Load layout mapping dari theme.json
    │
    ├── 4. REGISTRASI ASSET
    │       ├── Daftarkan CSS files ke Vite
    │       ├── Daftarkan JS files ke Vite
    │       ├── Daftarkan font untuk preload
    │       └── Set asset URL base path
    │
    ├── 5. LOAD CONFIGURATION
    │       ├── Load default config dari theme.json
    │       ├── Merge dengan kustomisasi dari Setting Engine
    │       ├── Generate CSS Variables dari konfigurasi
    │       └── Cache hasil konfigurasi
    │
    ├── 6. REGISTRASI COMPONENTS
    │       ├── Scan Components/ directory
    │       ├── Register Blade Components
    │       └── Set component namespace
    │
    ├── 7. TERAPKAN MIDDLEWARE
    │       ├── Register middleware LoadTheme
    │       ├── Set locale & direction (LTR/RTL)
    │       ├── Set dark mode preference
    │       └── Inject CSS variables ke view
    │
    └── 8. DISPATCH EVENT
            └── ThemeActivated { theme: {name}, config: {merged} }
```

### 6.3 Fallback Theme

Jika tema aktif bermasalah, Core Engine wajib melakukan fallback:

```text
Load Theme: {slug}
    │
    ├── ✅ Sukses
    │       └── Gunakan tema {slug}
    │
    └── ❌ Gagal (tidak ditemukan / corrupt / tidak kompatibel)
            │
            ├── 1. Log error: "Theme {slug} gagal dimuat"
            ├── 2. Cek tema default "cosmic-default"
            │       ├── ✅ Ada → Gunakan cosmic-default
            │       └── ❌ Tidak ada → Fatal Error
            │
            └── 3. Jika cosmic-default juga gagal
                    └── Fatal Error + Tampilkan halaman error
```

### 6.4 Theme View Resolver

Theme Loader menggunakan **View Resolver** untuk menentukan file view mana yang akan digunakan:

```text
View: "admin.dashboard"
    │
    ├── 1. Cek di themes/{active}/Layouts/admin/dashboard.blade.php
    │       ├── Ada → Gunakan file ini
    │       └── Tidak ada → Turun
    │
    ├── 2. Cek di themes/{active}/Pages/dashboard.blade.php
    │       ├── Ada → Gunakan file ini
    │       └── Tidak ada → Turun
    │
    ├── 3. Cek di themes/{active}/Layouts/admin.blade.php
    │       ├── Ada → Gunakan layout ini
    │       └── Tidak ada → Turun
    │
    └── 4. Gunakan view default dari module
```

---

## 7. Theme Manager

### 7.1 Dashboard Theme Manager

Theme Manager adalah antarmuka admin untuk mengelola tema. Berikut fitur yang tersedia:

| Fitur | Deskripsi | Hak Akses |
| :--- | :--- | :--- |
| **Daftar Theme** | Menampilkan semua tema yang terinstal | Super Admin, Admin |
| **Preview** | Pratinjau tema sebelum diaktifkan | Super Admin, Admin |
| **Activate** | Mengaktifkan tema untuk seluruh sistem | Super Admin |
| **Deactivate** | Menonaktifkan tema, fallback ke default | Super Admin |
| **Delete** | Menghapus tema dari sistem | Super Admin |
| **Import ZIP** | Menginstal tema baru dari file ZIP | Super Admin |
| **Export Theme** | Mengekspor tema beserta kustomisasi | Super Admin |

### 7.2 Tampilan Daftar Theme

Halaman daftar tema menampilkan:

```text
┌─────────────────────────────────────────────────────────────────┐
│  💠 Manajemen Theme                              [Import Theme] │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
│  │  [Preview]   │  │  [Preview]   │  │  [Preview]   │              │
│  │  Cosmic      │  │  Cosmic      │  │  School      │              │
│  │  Default     │  │  Modern      │  │  Blue        │              │
│  │              │  │              │  │              │              │
│  │  ● Active    │  │  ○ Inactive  │  │  ○ Inactive  │              │
│  │  v1.0.0      │  │  v1.0.0      │  │  v1.0.0      │              │
│  │  [Preview]   │  │  [Preview]   │  │  [Preview]   │              │
│  │  [Deactivate]│  │  [Activate]  │  │  [Activate]  │              │
│  │  [Customize] │  │  [Customize] │  │  [Customize] │              │
│  │  [Export]    │  │  [Export]    │  │  [Export]    │              │
│  │  [Delete]    │  │  [Delete]    │  │  [Delete]    │              │
│  └─────────────┘  └─────────────┘  └─────────────┘              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 7.3 Preview Theme

Saat preview, tema diterapkan hanya untuk admin yang sedang preview:

```text
Preview Mode
    │
    ├── 1. Simpan theme_preview di session
    ├── 2. Load tema preview seperti tema aktif
    ├── 3. Tampilkan banner "Preview Mode: {theme_name}"
    │       └── [Aktifkan] [Batalkan]
    └── 4. Jika diaktifkan → simpan ke Setting Engine & clear session
```

### 7.4 Import Theme ZIP

Alur instalasi tema melalui upload ZIP:

```text
Import Theme ZIP
    │
    ├── 1. Upload file ZIP
    ├── 2. Validasi struktur ZIP
    │       ├── Cek ada theme.json di root
    │       ├── Cek format theme.json valid
    │       └── Cek slug tema unik (tidak ada duplikat)
    │
    ├── 3. Ekstrak ke storage/temp/{slug}/
    │
    ├── 4. Compatibility Check
    │       ├── Cek minimum_core
    │       ├── Cek minimum_php
    │       ├── Cek minimum_laravel
    │       └── Cek dependensi modul
    │
    ├── 5. Jika lolos → Copy ke themes/{slug}/
    ├── 6. Hapus folder temp
    ├── 7. Registrasi di database
    └── 8. Tampilkan notifikasi sukses
```

### 7.5 Export Theme

Ekspor tema beserta kustomisasi:

```text
Export Theme: {slug}
    │
    ├── 1. Copy seluruh folder themes/{slug}/ ke temp
    ├── 2. Merge kustomisasi database ke theme.json (override settings)
    ├── 3. Buat ZIP dari folder temp
    ├── 4. Beri nama: {slug}-v{version}-custom.zip
    └── 5. Download file ZIP
```

---

## 8. Theme Customizer

### 8.1 Filosofi Customizer

**Theme Customizer** adalah antarmuka visual yang memungkinkan admin mengubah tampilan tema **tanpa coding**. Semua perubahan disimpan di Setting Engine dan langsung diterapkan.

### 8.2 Panel Customizer

```text
┌──────────────────────────────────────────────────────┐
│  🎨 Customize Theme: Cosmic Default                  │
├──────────────────────┬───────────────────────────────┤
│  Panel Pengaturan    │  Pratinjau Langsung           │
│                      │                               │
│  [Identitas Sekolah] │  ┌─────────────────────────┐  │
│  ○ Logo              │  │                         │  │
│  ○ Favicon           │  │   [LIVE PREVIEW]        │  │
│                      │  │                         │  │
│  [Warna]             │  │   Dashboard dengan      │  │
│  ○ Primary Color     │  │   warna & font baru     │  │
│  ○ Secondary Color   │  │                         │  │
│  ○ Accent Color      │  └─────────────────────────┘  │
│                      │                               │
│  [Tipografi]         │                               │
│  ○ Font Family       │                               │
│  ○ Heading Font      │                               │
│                      │                               │
│  [Layout]            │                               │
│  ○ Sidebar Style     │                               │
│  ○ Navbar Style      │                               │
│                      │                               │
│  [Simpan] [Reset]    │                               │
└──────────────────────┴───────────────────────────────┘
```

### 8.3 Daftar Pengaturan Customizer

#### Identitas Sekolah

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Logo Sekolah | `upload` | logo.svg | Upload logo sekolah (format: PNG, SVG, WEBP) |
| Favicon | `upload` | favicon.ico | Icon tab browser (format: ICO, PNG, SVG) |
| Nama Sekolah | `text` | — | Nama institusi sekolah |
| Alamat Sekolah | `textarea` | — | Alamat lengkap sekolah |
| No. Telepon | `text` | — | Nomor telepon sekolah |
| Email | `email` | — | Email resmi sekolah |

#### Warna

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Primary Color | `color` | `#4F46E5` | Warna utama tema (navbar, button primary, link) |
| Secondary Color | `color` | `#7C3AED` | Warna sekunder (aksen kedua) |
| Accent Color | `color` | `#06B6D4` | Warna aksen (highlight, badge) |
| Success Color | `color` | `#10B981` | Warna sukses (alert sukses, status aktif) |
| Danger Color | `color` | `#EF4444` | Warna bahaya (alert error, hapus) |
| Warning Color | `color` | `#F59E0B` | Warna peringatan (alert warning) |
| Info Color | `color` | `#3B82F6` | Warna informasi (alert info) |
| Background Color | `color` | `#F8FAFC` | Warna latar belakang halaman |
| Surface Color | `color` | `#FFFFFF` | Warna permukaan card/section |
| Text Color | `color` | `#1E293B` | Warna teks utama |
| Border Color | `color` | `#E2E8F0` | Warna border elemen |

#### Tipografi

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Font Default | `select` | Inter | Font untuk teks body |
| Font Heading | `select` | Outfit | Font untuk heading (judul) |
| Font Code | `select` | JetBrains Mono | Font untuk kode/monospace |
| Font Size Base | `select` | 16px | Ukuran font dasar |
| Line Height | `select` | 1.5 | Tinggi baris default |

#### Layout

| Pengaturan | Tipe | Default | Opsi |
| :--- | :--- | :--- | :--- |
| Sidebar Style | `select` | `default` | `default`, `collapsed`, `mini`, `hidden` |
| Navbar Style | `select` | `default` | `default`, `sticky`, `static`, `floating` |
| Sidebar Position | `select` | `left` | `left`, `right` |
| Layout Width | `select` | `full` | `full`, `boxed`, `fluid` |
| Footer Style | `select` | `default` | `default`, `sticky`, `hidden` |

#### Dashboard

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Dashboard Layout | `select` | `grid` | `grid`, `list`, `columns` |
| Widget Columns | `select` | 3 | Jumlah kolom widget (1-4) |
| Show Welcome Card | `switch` | true | Tampilkan kartu selamat datang |

#### Halaman Login

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Login Background | `upload` | login-bg.jpg | Gambar background halaman login |
| Login Logo | `upload` | logo.svg | Logo di halaman login |
| Login Layout | `select` | `centered` | `centered`, `split`, `cover` |

#### Landing / Beranda

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Hero Banner | `upload` | hero-banner.jpg | Banner hero section landing page |
| Hero Title | `text` | — | Judul hero section |
| Hero Subtitle | `textarea` | — | Subjudul hero section |
| Tampilkan Carousel | `switch` | false | Tampilkan carousel di landing |
| Tampilkan Announcement | `switch` | true | Tampilkan section pengumuman |
| Footer Content | `textarea` | — | Konten footer landing (HTML aman) |

#### Kustom Lanjutan

| Pengaturan | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| Custom CSS | `code` | — | CSS kustom yang ditambahkan ke tema |
| Custom JS | `code` | — | JavaScript kustom yang ditambahkan ke tema |
| Custom Head | `code` | — | Meta tag / script di `<head>` |

### 8.4 Alur Customizer

```text
User mengubah pengaturan
    │
    ├── 1. Input → Validasi (tipe, format, ukuran)
    │       ├── ✅ Valid → Lanjut
    │       └── ❌ Invalid → Tampilkan error
    │
    ├── 2. Simpan ke Setting Engine
    │       ├── Key: "theme.{slug}.{setting_name}"
    │       └── Value: sesuai tipe (string, json, file path)
    │
    ├── 3. Update CSS Variables
    │       ├── Generate ulang CSS Variables
    │       └── Simpan ke file cache / inline
    │
    ├── 4. Clear cache tema
    │
    ├── 5. Tampilkan notifikasi sukses
    │
    └── 6. Dispatch event ThemeConfigChanged
```

---

## 9. Layout System

### 9.1 Jenis Layout

Theme Engine menyediakan tujuh layout standar yang dapat digunakan oleh seluruh halaman:

```text
Layout System
    │
    ├── 🏛️ Master Layout         → Layout dasar yang digunakan semua layout lain
    ├── 👤 Admin Layout          → Layout halaman admin (sidebar + navbar)
    ├── 🚪 Guest Layout          → Layout halaman publik (tanpa sidebar)
    ├── 🔐 Auth Layout           → Layout halaman login/register
    ├── 🏠 Landing Layout        → Layout halaman beranda/landing page
    ├── ⚠️ Error Layout          → Layout halaman error (403, 404, 500, 503)
    └── 🖨️ Print Layout          → Layout untuk cetak (stripped, no sidebar)
```

### 9.2 Struktur Layout

#### Master Layout

Layout master adalah **induk** dari semua layout lain. Berisi struktur HTML dasar:

```text
master.blade.php
    │
    ├── <!DOCTYPE html>
    ├── <html>
    │   ├── <head>
    │   │   ├── Meta tags (charset, viewport, CSRF)
    │   │   ├── Title (dinamis dari halaman)
    │   │   ├── Favicon (dari customizer)
    │   │   ├── CSS Variables (inline dari konfigurasi)
    │   │   ├── Theme CSS (compiled by Vite)
    │   │   ├── Custom CSS (dari customizer)
    │   │   └── @stack('styles')
    │   │
    │   └── <body>
    │       ├── @yield('body')
    │       ├── Theme JS (compiled by Vite)
    │       ├── Custom JS (dari customizer)
    │       └── @stack('scripts')
    │
    └── </html>
```

#### Admin Layout

```text
admin.blade.php (extends master)
    │
    ├── @section('body')
    │   ├── <div id="app">
    │   │   ├── Navbar (top navigation)
    │   │   ├── Sidebar (main navigation)
    │   │   ├── Main Content
    │   │   │   ├── Breadcrumb
    │   │   │   ├── Page Header
    │   │   │   ├── Alert / Notification
    │   │   │   ├── @yield('content')
    │   │   │   └── Footer
    │   │   └── </div>
    │   └── @endsection
    │
    ├── @stack('styles')
    └── @stack('scripts')
```

#### Auth Layout

```text
auth.blade.php (extends master)
    │
    ├── @section('body')
    │   ├── <div class="auth-wrapper">
    │   │   ├── Background Image (dari customizer)
    │   │   ├── <div class="auth-card">
    │   │   │   ├── Logo (dari customizer)
    │   │   │   ├── @yield('auth-content')
    │   │   │   └── </div>
    │   │   └── </div>
    │   └── @endsection
    │
    └── @stack('styles')
```

### 9.3 Layout Mapping

Theme Engine menggunakan mapping layout dari `theme.json` untuk menentukan layout mana yang digunakan oleh halaman tertentu:

| Layout | Prefix Route | Digunakan Oleh |
| :--- | :--- | :--- |
| `master` | — | Layout dasar (tidak digunakan langsung) |
| `admin` | `admin.*` | Semua halaman dashboard admin |
| `guest` | `guest.*`, `web.*` | Halaman publik (katalog buku, dll) |
| `auth` | `auth.*` | Halaman login, register, forgot password |
| `landing` | `home`, `/` | Halaman beranda/landing page |
| `error` | — | Halaman error (403, 404, 500, 503) |
| `print` | `*.print` | Halaman cetak (laporan, kartu anggota) |

---

## 10. Component System

### 10.1 Komponen UI Standar

Theme Engine menyediakan sistem komponen UI yang konsisten di seluruh tema. Setiap tema dapat meng-override komponen sesuai gaya visualnya.

| Komponen | Fungsi | Variasi |
| :--- | :--- | :--- |
| **Navbar** | Navigasi atas | `default`, `sticky`, `static`, `floating`, `transparent` |
| **Sidebar** | Navigasi samping | `default`, `collapsed`, `mini`, `hidden`, `overlay` |
| **Footer** | Footer halaman | `default`, `sticky`, `hidden`, `minimal` |
| **Header** | Judul halaman | `default`, `with-breadcrumb`, `with-actions` |
| **Card** | Kontainer konten | `default`, `bordered`, `shadow`, `flat`, `gradient` |
| **Widget** | Widget dashboard | `stat`, `chart`, `list`, `table`, `custom` |
| **Modal** | Dialog popup | `default`, `centered`, `fullscreen`, `scrollable` |
| **Alert** | Notifikasi | `success`, `danger`, `warning`, `info`, `primary` |
| **Breadcrumb** | Navigasi lokasi | `default`, `arrow`, `slash`, `dot` |
| **Pagination** | Navigasi halaman | `default`, `simple`, `rounded`, `icon` |
| **Table** | Tabel data | `default`, `striped`, `bordered`, `hover`, `responsive` |
| **Form** | Form input | `default`, `floating`, `outlined`, `filled` |
| **Button** | Tombol aksi | `primary`, `secondary`, `success`, `danger`, `outline`, `ghost`, `gradient` |
| **Badge** | Label status | `default`, `pill`, `dot` |
| **Progress** | Progress bar | `default`, `animated`, `striped` |
| **Tabs** | Tab navigasi | `default`, `pills`, `underline`, `vertical` |
| **Avatar** | Foto profil | `default`, `rounded`, `bordered`, `group` |
| **Dropdown** | Menu dropdown | `default`, `hover`, `click` |

### 10.2 Struktur Blade Components

Setiap komponent tema mengikuti pola Blade Component Laravel:

```text
Components/
├── navbar.blade.php           ← <x-theme-navbar />
├── sidebar.blade.php          ← <x-theme-sidebar />
├── footer.blade.php           ← <x-theme-footer />
├── card.blade.php             ← <x-theme-card />
├── modal.blade.php            ← <x-theme-modal />
├── alert.blade.php            ← <x-theme-alert type="success" />
├── breadcrumb.blade.php       ← <x-theme-breadcrumb />
├── pagination.blade.php       ← <x-theme-pagination />
├── table.blade.php            ← <x-theme-table />
├── button.blade.php           ← <x-theme-button variant="primary" />
└── widget.blade.php           ← <x-theme-widget />
```

### 10.3 Component Inheritance

Tema dapat meng-override komponen dengan aturan:

```text
Render <x-theme-card />
    │
    ├── 1. Cek di themes/{active}/Components/card.blade.php
    │       ├── Ada → Gunakan komponen tema aktif
    │       └── Tidak → Turun
    │
    ├── 2. Cek di themes/{parent}/Components/card.blade.php
    │       ├── Ada → Gunakan komponen tema induk
    │       └── Tidak → Turun
    │
    └── 3. Gunakan komponen default dari Core
```

---

## 11. Color System

### 11.1 CSS Variables

Semua warna dalam CosmicLib **WAJIB** menggunakan CSS Variables. Tidak boleh ada warna hardcode.

```css
:root {
  /* Primary */
  --cos-primary: #4F46E5;
  --cos-primary-rgb: 79, 70, 229;
  --cos-primary-hover: #4338CA;
  --cos-primary-light: #EEF2FF;
  --cos-primary-dark: #3730A3;

  /* Secondary */
  --cos-secondary: #7C3AED;
  --cos-secondary-rgb: 124, 58, 237;
  --cos-secondary-hover: #6D28D9;
  --cos-secondary-light: #F5F3FF;
  --cos-secondary-dark: #5B21B6;

  /* Accent */
  --cos-accent: #06B6D4;
  --cos-accent-rgb: 6, 182, 212;
  --cos-accent-hover: #0891B2;
  --cos-accent-light: #ECFEFF;
  --cos-accent-dark: #0E7490;

  /* Status */
  --cos-success: #10B981;
  --cos-success-rgb: 16, 185, 129;
  --cos-success-light: #D1FAE5;
  --cos-danger: #EF4444;
  --cos-danger-rgb: 239, 68, 68;
  --cos-danger-light: #FEE2E2;
  --cos-warning: #F59E0B;
  --cos-warning-rgb: 245, 158, 11;
  --cos-warning-light: #FEF3C7;
  --cos-info: #3B82F6;
  --cos-info-rgb: 59, 130, 246;
  --cos-info-light: #DBEAFE;

  /* Background & Surface */
  --cos-bg: #F8FAFC;
  --cos-bg-secondary: #F1F5F9;
  --cos-surface: #FFFFFF;
  --cos-surface-secondary: #F8FAFC;

  /* Text */
  --cos-text-primary: #1E293B;
  --cos-text-secondary: #64748B;
  --cos-text-muted: #94A3B8;
  --cos-text-inverse: #FFFFFF;

  /* Border */
  --cos-border: #E2E8F0;
  --cos-border-light: #F1F5F9;
  --cos-border-dark: #CBD5E1;

  /* Shadow */
  --cos-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --cos-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  --cos-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --cos-shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);

  /* Radius */
  --cos-radius-sm: 0.25rem;
  --cos-radius: 0.5rem;
  --cos-radius-md: 0.75rem;
  --cos-radius-lg: 1rem;
  --cos-radius-full: 9999px;
}
```

### 11.2 Dark Mode Variables

```css
[data-theme="dark"] {
  /* Background & Surface */
  --cos-bg: #0F172A;
  --cos-bg-secondary: #1E293B;
  --cos-surface: #1E293B;
  --cos-surface-secondary: #334155;

  /* Text */
  --cos-text-primary: #F1F5F9;
  --cos-text-secondary: #94A3B8;
  --cos-text-muted: #64748B;
  --cos-text-inverse: #0F172A;

  /* Border */
  --cos-border: #334155;
  --cos-border-light: #1E293B;
  --cos-border-dark: #475569;

  /* Primary (lebih terang di dark mode) */
  --cos-primary: #818CF8;
  --cos-primary-hover: #A5B4FC;
  --cos-primary-light: #312E81;
  --cos-primary-dark: #C7D2FE;

  /* Shadow */
  --cos-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.2);
  --cos-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  --cos-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
  --cos-shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
}
```

### 11.3 Color Generation

Saat admin mengubah warna primer, sistem akan **secara otomatis** menghasilkan:

| Turunan | Rumus | Contoh |
| :--- | :--- | :--- |
| `hover` | Darken 10% | `#4F46E5` → `#4338CA` |
| `light` | Opacity 10% + white overlay | `#4F46E5` → `#EEF2FF` |
| `dark` | Darken 20% | `#4F46E5` → `#3730A3` |
| `rgb` | Konversi hex ke RGB | `#4F46E5` → `79, 70, 229` |
| Gradient | Primary + Secondary blend | Linear gradient |

---

## 12. Typography

### 12.1 Font System

Theme Engine menggunakan sistem font yang fleksibel:

```css
:root {
  /* Font Families */
  --cos-font-body: 'Inter', -apple-system, sans-serif;
  --cos-font-heading: 'Outfit', 'Inter', sans-serif;
  --cos-font-code: 'JetBrains Mono', 'Fira Code', monospace;

  /* Font Sizes */
  --cos-text-xs: 0.75rem;    /* 12px */
  --cos-text-sm: 0.875rem;   /* 14px */
  --cos-text-base: 1rem;     /* 16px */
  --cos-text-lg: 1.125rem;   /* 18px */
  --cos-text-xl: 1.25rem;    /* 20px */
  --cos-text-2xl: 1.5rem;    /* 24px */
  --cos-text-3xl: 1.875rem;  /* 30px */
  --cos-text-4xl: 2.25rem;   /* 36px */

  /* Heading Sizes */
  --cos-h1: 2.5rem;          /* 40px */
  --cos-h2: 2rem;            /* 32px */
  --cos-h3: 1.75rem;         /* 28px */
  --cos-h4: 1.5rem;          /* 24px */
  --cos-h5: 1.25rem;         /* 20px */
  --cos-h6: 1rem;            /* 16px */

  /* Line Heights */
  --cos-leading-none: 1;
  --cos-leading-tight: 1.25;
  --cos-leading-normal: 1.5;
  --cos-leading-relaxed: 1.75;

  /* Font Weights */
  --cos-weight-light: 300;
  --cos-weight-normal: 400;
  --cos-weight-medium: 500;
  --cos-weight-semibold: 600;
  --cos-weight-bold: 700;
}
```

### 12.2 Font Pairing Default

| Tipe | Font Default | Fallback | Berat |
| :--- | :--- | :--- | :--- |
| **Body Text** | Inter | system-ui, -apple-system, sans-serif | 400 (regular) |
| **Heading** | Outfit | Inter, system-ui, sans-serif | 600 (semibold) |
| **Code/Monospace** | JetBrains Mono | Fira Code, monospace | 400 (regular) |
| **Display (Display besar)** | Space Grotesk | Outfit, sans-serif | 700 (bold) |

### 12.3 Font Loading

Theme Engine mendukung tiga metode loading font:

| Metode | Kecepatan | Cocok Untuk | Implementasi |
| :--- | :--- | :--- | :--- |
| **System Font** | ⚡ Sangat Cepat | Tema default | Menggunakan font bawaan OS |
| **Google Fonts** | 🟡 Sedang | Tema dengan font kustom | @import Google Fonts API |
| **Self-Hosted** | 🔴 Lebih Lambat | Tema premium / offline | Font di folder Assets/Fonts/ |

---

## 13. Dark Mode

### 13.1 Mode Dark

Theme Engine mendukung tiga mode dark/light:

| Mode | Penjelasan | Cocok Untuk |
| :--- | :--- | :--- |
| **Auto** | Mengikuti preferensi sistem operasi (`prefers-color-scheme`) | Sebagian besar pengguna |
| **Manual** | Pengguna memilih sendiri via toggle di UI | Pengguna dengan preferensi khusus |
| **System** | Selalu mengikuti sistem operasi | Sekolah dengan kebijakan tertentu |

### 13.2 Alur Dark Mode

```text
Dark Mode Resolution
    │
    ├── 1. Cek user preference (database)
    │       ├── "auto" → Lanjut ke langkah 2
    │       ├── "light" → Gunakan Light Palette
    │       └── "dark" → Gunakan Dark Palette
    │
    ├── 2. Cek system preference (JavaScript)
    │       ├── prefers-color-scheme: dark → Dark Palette
    │       └── prefers-color-scheme: light → Light Palette
    │
    ├── 3. Set attribute [data-theme] di <html>
    │       ├── data-theme="light"
    │       └── data-theme="dark"
    │
    └── 4. CSS Variables berubah sesuai theme
```

### 13.3 Dark Mode Toggle

Toggle dark mode harus tersedia di:

- **Navbar** — icon toggle di pojok kanan atas.
- **Halaman Login** — toggle sebelum login (mempengaruhi session).
- **User Settings** — preferensi permanen di halaman profil.

### 13.4 Dark Palette vs Light Palette

Setiap tema wajib menyediakan dua palet warna:

```text
Theme Color Scheme
    │
    ├── ☀️ Light Palette
    │       ├── Background: putih/terang
    │       ├── Surface: putih
    │       ├── Text: gelap
    │       └── Border: abu-abu terang
    │
    └── 🌙 Dark Palette
            ├── Background: gelap (navy/charcoal)
            ├── Surface: gelap lebih terang
            ├── Text: putih/terang
            └── Border: abu-abu gelap
```

---

## 14. Responsive Design

### 14.1 Breakpoint

Semua tema CosmicLib **WAJIB** responsif dengan breakpoint berikut:

| Device | Min Width | Target | Approach |
| :--- | :--- | :--- | :--- |
| **Mobile** | 320px — 575px | Smartphone kecil | Single column, stacked |
| **Mobile Large** | 576px — 767px | Smartphone besar | Single column, wider |
| **Tablet** | 768px — 991px | Tablet portrait | Two columns, collapsed sidebar |
| **Laptop** | 992px — 1199px | Laptop kecil | Full layout |
| **Desktop** | 1200px — 1399px | Desktop standar | Full layout, wider |
| **Large Desktop** | 1400px+ | Monitor besar | Maximum width container |

### 14.2 Responsive Behavior

| Elemen | Mobile (<768px) | Tablet (768-991px) | Desktop (≥992px) |
| :--- | :--- | :--- | :--- |
| **Sidebar** | Hidden (overlay) | Collapsed (icon) | Visible (full) |
| **Navbar** | Compact (icon only) | Compact (icon + text) | Full (logo + menu) |
| **Content Padding** | 12px | 16px | 24px |
| **Cards** | 1 column | 2 columns | 3-4 columns |
| **Tables** | Horizontal scroll | Horizontal scroll | Normal |
| **Font Size** | 14px | 15px | 16px |
| **Modal** | Fullscreen | Centered | Centered |
| **Footer** | Stacked | 2 columns | 3-4 columns |

### 14.3 Testing Checklist Responsive

| Item | Wajib? |
| :--- | :--- |
| Navbar tidak overflow | ✅ Ya |
| Sidebar tidak menutup konten | ✅ Ya |
| Tabel bisa di-scroll horizontal | ✅ Ya |
| Form input tidak terpotong | ✅ Ya |
| Gambar tidak pecah/terdistorsi | ✅ Ya |
| Tombol tetap bisa diklik | ✅ Ya |
| Modal bisa ditutup di mobile | ✅ Ya |
| Dropdown tidak terpotong | ✅ Ya |
| Font terbaca di semua ukuran | ✅ Ya |
| Touch target minimal 44x44px | ✅ Ya |

---

## 15. Theme Assets

### 15.1 Jenis Aset

Theme Engine mengelola beberapa jenis aset:

| Jenis Aset | Ekstensi | Loading | Optimasi |
| :--- | :--- | :--- | :--- |
| **CSS** | `.css` | Vite bundling | Minify, autoprefix, purge unused |
| **JavaScript** | `.js` | Vite bundling | Minify, tree-shaking, defer |
| **Images** | `.png, .jpg, .svg, .webp` | Direct URL | Lazy loading, WebP conversion |
| **Icons** | `.svg` | Inline / Sprite | SVG sprite, minify |
| **Fonts** | `.woff2, .woff` | Preload | Subset, woff2 priority |
| **SVG** | `.svg` | Inline / Asset | Minify, cache |

### 15.2 Asset Loading Strategy

```text
Asset Loading Priority
    │
    ├── ⚡ Critical (load segera)
    │       ├── theme-critical.css (di head)
    │       └── font preload (di head)
    │
    ├── 🟡 High Priority (load setelah render)
    │       ├── theme.css (di head, async)
    │       ├── theme.js (defer)
    │       └── theme-init.js (defer)
    │
    ├── 🔵 Medium Priority (lazy load)
    │       ├── theme-dark.css (jika dark mode aktif)
    │       ├── theme-rtl.css (jika RTL aktif)
    │       └── images (lazy loading native)
    │
    └── ⚪ Low Priority (on demand)
            ├── custom.css (dari customizer)
            └── custom.js (dari customizer)
```

### 15.3 Asset Optimization

| Optimasi | Deskripsi | Implementasi |
| :--- | :--- | :--- |
| **CSS Minification** | Hapus whitespace, komentar | Vite build |
| **JS Minification** | Hapus whitespace, rename vars | Vite build |
| **CSS Purge** | Hapus CSS yang tidak digunakan | PurgeCSS via Vite |
| **Image Compression** | Optimasi ukuran gambar | Vite plugin / pipeline |
| **WebP Conversion** | Konversi PNG/JPG ke WebP | Tersedia di Vite |
| **Lazy Loading** | Muat gambar saat terlihat | Native `loading="lazy"` |
| **Font Subsetting** | Hanya muat karakter yang diperlukan | Self-hosted font subset |
| **Cache Busting** | Hash filename untuk cache | Vite file hashing |

---

## 16. Theme Configuration

### 16.1 Sumber Konfigurasi

Theme Engine CosmicLib menggabungkan konfigurasi dari **lima lapisan** secara berurutan, dengan prioritas meningkat dari atas ke bawah (rendah → tinggi). Lapisan yang lebih tinggi akan menimpa lapisan di bawahnya.

```text
┌─────────────────────────────────────────────────────┐
│  Lapisan 1 — Theme Default                          │
│  Nilai fallback bawaan yang di-hardcode di Core     │
│  Engine. Digunakan jika tidak ada lapisan lain      │
│  yang mendefinisikan nilai tertentu.                │
└─────────────────────────┬───────────────────────────┘
                          │ (ditimpa oleh)
                          ▼
┌─────────────────────────────────────────────────────┐
│  Lapisan 2 — theme.json                             │
│  File manifest tema. Mendefinisikan nilai default   │
│  yang ditetapkan pengembang tema untuk setiap       │
│  konfigurasi visual.                                │
└─────────────────────────┬───────────────────────────┘
                          │ (ditimpa oleh)
                          ▼
┌─────────────────────────────────────────────────────┐
│  Lapisan 3 — Config Theme (config/theme.php)        │
│  File konfigurasi Laravel. Mengizinkan override     │
│  per environment (lokal, staging, production)       │
│  tanpa mengubah file tema asli.                     │
└─────────────────────────┬───────────────────────────┘
                          │ (ditimpa oleh)
                          ▼
┌─────────────────────────────────────────────────────┐
│  Lapisan 4 — System Settings (Setting Engine)       │
│  Konfigurasi yang disimpan di database melalui      │
│  Theme Customizer oleh admin sekolah. Mencakup      │
│  logo, warna, font, layout, dsb.                    │
└─────────────────────────┬───────────────────────────┘
                          │ (ditimpa oleh)
                          ▼
┌─────────────────────────────────────────────────────┐
│  Lapisan 5 — User Preferences                       │
│  Preferensi per pengguna yang tersimpan di          │
│  database. Mencakup dark mode, sidebar style,       │
│  dan preferensi tampilan pribadi lainnya.           │
└─────────────────────────────────────────────────────┘
```

### 16.2 Detail Setiap Lapisan

| # | Lapisan | Lokasi | Dikelola Oleh | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Theme Default** | Core Engine PHP | Developer Core | `primary_color: #4F46E5` |
| 2 | **theme.json** | `themes/{slug}/theme.json` | Developer Tema | `"primary_color": "#6366F1"` |
| 3 | **Config Theme** | `config/theme.php` | Developer / DevOps | `'cache_ttl' => 3600` |
| 4 | **System Settings** | Database — Setting Engine | Admin Sekolah | Warna, logo, font, layout |
| 5 | **User Preferences** | Database — per user | Pengguna | Dark mode, sidebar style |

### 16.3 Pola Key Setting

Semua konfigurasi tema disimpan di Setting Engine dengan pola key terstruktur:

| Key Pattern | Contoh | Deskripsi |
| :--- | :--- | :--- |
| `theme.{slug}.{setting}` | `theme.cosmic-default.primary_color` | Kustomisasi tema umum |
| `theme.{slug}.assets.{asset}` | `theme.cosmic-default.assets.logo` | Upload aset tema |
| `theme.{slug}.custom.{type}` | `theme.cosmic-default.custom.css` | Custom CSS/JS tema |
| `theme.active` | `cosmic-default` | Tema yang sedang aktif |
| `user.{id}.theme.{setting}` | `user.1.theme.dark_mode` | Preferensi pengguna |
| `config.theme.{setting}` | `config.theme.cache_ttl` | Pengaturan `config/theme.php` |

### 16.4 Alur Resolusi Konfigurasi

Saat Theme Loader memuat tema, konfigurasi diselesaikan dengan urutan berikut:

```text
1. Baca nilai default dari Core Engine
         │
         ▼
2. Merge dengan theme.json
   (nilai theme.json menimpa default Core)
         │
         ▼
3. Merge dengan config/theme.php
   (config Laravel menimpa theme.json)
         │
         ▼
4. Merge dengan System Settings (database)
   (kustomisasi admin menimpa config Laravel)
         │
         ▼
5. Merge dengan User Preferences (database)
   (preferensi user menimpa segalanya)
         │
         ▼
Konfigurasi Final — digunakan untuk render view
```

### 16.5 Larangan Hardcode

| ❌ Dilarang | ✅ Wajib |
| :--- | :--- |
| Warna hardcode di CSS | CSS Variables dari Theme config |
| Logo hardcode di view | `setting('theme.{slug}.assets.logo')` |
| Nama sekolah hardcode | `setting('school.name')` |
| Font hardcode | CSS Variables dari font family config |
| URL asset hardcode | `Theme::asset('path/to/file')` helper |
| Layout hardcode | Mapping layout dari `theme.json` |

---

## 17. Theme Compatibility

### 17.1 Kompatibilitas dengan Engine Lain

Theme Engine harus kompatibel dengan seluruh engine CosmicLib:

| Engine | Hubungan dengan Theme Engine |
| :--- | :--- |
| **Module Engine** | Theme menyediakan layout dan komponen untuk view modul |
| **Widget Engine** | Theme menyediakan container dan styling untuk widget |
| **Menu Engine** | Theme merender menu yang disediakan Menu Engine |
| **Media Engine** | Theme menggunakan aset (logo, gambar) dari Media Engine |
| **Notification Engine** | Theme menampilkan notifikasi dengan komponen alert |
| **Permission Engine** | Theme menampilkan/menyembunyikan elemen berdasarkan permission |
| **Setting Engine** | Theme membaca semua konfigurasi dari Setting Engine |
| **Plugin Engine** | Plugin dapat menyediakan komponen yang di-render oleh theme |

### 17.2 Dependency Matrix

```text
Theme Engine
    │
    ├── Memerlukan
    │       ├── Core Engine      → Wajib (bootstrap, DI, event)
    │       ├── Setting Engine   → Wajib (konfigurasi tema)
    │       └── Menu Engine      → Wajib (navigasi)
    │
    ├── Berintegrasi dengan
    │       ├── Module Engine    → View, layout, komponen
    │       ├── Widget Engine    → Dashboard widgets
    │       ├── Media Engine     → Asset management
    │       ├── Notification Engine → Notifikasi UI
    │       └── Permission Engine    → UI visibility
    │
    └── Dapat diperluas oleh
            ├── Plugin Engine    → Komponen plugin di theme
            └── Module Engine    → Custom styling per modul
```

---

## 18. Theme Security

### 18.1 Escape Output

Semua output data di Blade **WAJIB** menggunakan escaping:

| Konteks | Fungsi | Keterangan |
| :--- | :--- | :--- |
| HTML | `{{ $var }}` | Blade auto-escape |
| HTML (raw) | `{!! $var !!}` | Hanya untuk HTML tepercaya dari admin |
| JavaScript | `@json($var)` | JSON encode untuk JS |
| Attribute | `{{ $var }}` | Auto-escape di dalam attribute |
| URL | `{{ url($var) }}` | Validasi URL sebelum output |

### 18.2 Sanitasi HTML

Konten HTML dari Theme Customizer (seperti Custom CSS, Custom JS, Footer Content) **WAJIB** melalui sanitasi:

| Konten | Sanitasi | Metode |
| :--- | :--- | :--- |
| Custom CSS | Allowlist CSS properties | CSS Purifier |
| Custom JS | Hanya dari admin tepercaya | Role-based access |
| Footer Content | Allowlist HTML tags | HTML Purifier |
| Hero Content | Allowlist HTML tags | HTML Purifier |
| Announcement | Allowlist HTML tags | HTML Purifier |

### 18.3 Asset Validation

Setiap aset yang diupload melalui Theme Customizer **WAJIB** divalidasi:

| Aset | Validasi | Batasan |
| :--- | :--- | :--- |
| Logo | Tipe file, dimensi, ukuran | PNG/SVG/WEBP, max 2MB |
| Favicon | Tipe file, ukuran | ICO/PNG/SVG, max 1MB |
| Background | Tipe file, dimensi, ukuran | JPG/PNG/WEBP, max 5MB |
| Banner | Tipe file, dimensi, ukuran | JPG/PNG/WEBP, max 5MB |
| Font | Tipe file, ukuran | WOFF2/WOFF, max 5MB |

### 18.4 Content Security Policy

Theme Engine mendukung Content Security Policy (CSP) untuk keamanan tambahan:

```text
CSP Headers (direkomendasikan)
    │
    ├── default-src 'self'
    ├── script-src 'self' 'unsafe-inline' (untuk customizer)
    ├── style-src 'self' 'unsafe-inline'
    ├── img-src 'self' data: *.googleapis.com (jika Google Fonts)
    ├── font-src 'self' *.gstatic.com (jika Google Fonts)
    └── connect-src 'self'
```

---

## 19. Theme Marketplace (Future)

### 19.1 Visi Marketplace

Theme Marketplace adalah fitur masa depan yang memungkinkan pengguna menginstal tema dari repository pusat CosmicLib.

### 19.2 Fitur Marketplace

| Fitur | Deskripsi | Status |
| :--- | :--- | :--- |
| **Browse Themes** | Menjelajahi tema yang tersedia | 🔮 Future |
| **Install Theme** | Instal tema satu klik dari marketplace | 🔮 Future |
| **Update Theme** | Mendapatkan notifikasi pembaruan tema | 🔮 Future |
| **Digital Signature** | Verifikasi keaslian tema | 🔮 Future |
| **Compatibility Check** | Cek kompatibilitas sebelum instalasi | 🔮 Future |
| **Version Check** | Riwayat versi dan changelog | 🔮 Future |

### 19.3 Digital Signature

Setiap tema dari marketplace akan ditandatangani secara digital:

```text
Theme Package
    │
    ├── theme.json (manifest)
    ├── signature.sig (digital signature)
    │       └── SHA-256 hash dari theme.json
    │           yang di-encrypt dengan private key marketplace
    │
    └── public_key.asc (public key)
            └── Untuk verifikasi signature
```

### 19.4 Alur Instalasi Marketplace

```text
Install from Marketplace
    │
    ├── 1. Request theme dari API marketplace
    │
    ├── 2. Download ZIP
    │
    ├── 3. Verifikasi digital signature
    │       ├── Valid → Lanjut
    │       └── Invalid → Tolak + log security alert
    │
    ├── 4. Compatibility Check
    │       ├── Core version
    │       ├── PHP version
    │       ├── Laravel version
    │       └── Module dependencies
    │
    ├── 5. Extract to themes/{slug}/
    │
    └── 6. Registrasi & siap digunakan
```

---

## 20. Default Themes

### 20.1 Daftar Tema Default

CosmicLib menyediakan lima tema default:

| # | Tema | Slug | Gaya | Dark Mode | Target |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | **Cosmic Default** | `cosmic-default` | Modern, minimalis, bersih | ✅ Ya | Semua sekolah (default) |
| 2 | **Cosmic Modern** | `cosmic-modern` | Futuristik, glassmorphism | ✅ Ya | Sekolah dengan tampilan modern |
| 3 | **Cosmic Classic** | `cosmic-classic` | Tradisional, elegan | ✅ Ya | Sekolah dengan nuansa klasik |
| 4 | **School Blue** | `school-blue` | Profesional, biru koporat | ✅ Ya | SMA/SMK negeri |
| 5 | **School Green** | `school-green` | Natural, hijau segar | ✅ Ya | Madrasah / sekolah islam |

### 20.2 Cosmic Default (Tema Bawaan)

Cosmic Default adalah tema yang **WAJIB** ada dan tidak boleh dihapus. Berfungsi sebagai **fallback** jika tema lain gagal dimuat.

| Aspek | Spesifikasi |
| :--- | :--- |
| **Desain** | Modern minimalis, clean, whitespace cukup |
| **Warna** | Indigo primary, purple secondary, cyan accent |
| **Font** | Inter (body), Outfit (heading) |
| **Dark Mode** | Didukung penuh |
| **Responsif** | Semua device |
| **Kompatibilitas** | Semua modul CosmicLib |
| **Performa** | Optimasi maksimal, lightweight |

---

## 21. AI Rules

### 21.1 Aturan untuk AI Coding Assistant

Berikut aturan yang **WAJIB** dipatuhi oleh AI saat mengembangkan atau memodifikasi Theme Engine:

| Aturan | Penjelasan | Sanksi jika Dilanggar |
| :--- | :--- | :--- |
| **No Business Logic** | AI tidak boleh menaruh logika bisnis di theme | Kode ditolak review |
| **No Database Queries** | AI tidak boleh membuat query database di Blade | Kode ditolak review |
| **No Hardcode Colors** | AI tidak boleh hardcode warna, wajib CSS Variables | Kode ditolak review |
| **No Hardcode Logo** | AI tidak boleh hardcode path logo | Kode ditolak review |
| **No Hardcode Text** | AI tidak boleh hardcode teks UI (wajib trans) | Kode ditolak review |
| **Use Theme Config** | AI wajib menggunakan Theme Configuration | Kode ditolak review |
| **Use Components** | AI wajib menggunakan Blade Components | Kode ditolak review |
| **Escape Output** | AI wajib escape semua output dinamis | Security issue |
| **Responsive** | AI wajib memastikan semua view responsif | Kualitas ditolak |
| **Accessibility** | AI wajib mengikuti standar WCAG | Kualitas ditolak |

### 21.2 Aturan Khusus untuk Blade

```text
✅ BOLEH (di Theme):
    - Menampilkan data dengan {{ }}
    - Menggunakan komponen <x-theme-* />
    - Menggunakan CSS Variables
    - Menggunakan layout inheritance
    - Menggunakan @push dan @stack untuk asset
    - Menulis HTML, CSS, JS murni
    - Menggunakan directive @auth, @guest, @can

❌ TIDAK BOLEH (di Theme):
    - Query database (Eloquent, DB::, raw SQL)
    - Logika bisnis (if/else berisi business rules)
    - Hardcode nilai konfigurasi
    - env() atau config() untuk secrets
    - Menyimpan data ke session
    - Membuat atau mengubah data
    - Redirect atau abort (kecuali di middleware theme)
```

---

## 22. Best Practice

### 22.1 Bootstrap 5

- Gunakan **Bootstrap 5.3** utility classes secara maksimal.
- Kustomisasi Bootstrap melalui `_variables.scss` tema.
- Jangan override Bootstrap secara langsung — gunakan CSS Variables.
- Gunakan Bootstrap components (`modal`, `tooltip`, `popover`) via Blade Components.
- Hindari custom CSS jika sudah ada utility class Bootstrap yang mencukupi.

### 22.2 Blade Components

- Gunakan **Blade Components** (`<x-*>`) untuk semua elemen UI.
- Komponen harus memiliki **props** yang terdokumentasi.
- Komponen harus **reusable** — tidak terkait data spesifik.
- Gunakan **slot** untuk konten dinamis.
- Komponen harus memiliki **default styling** yang baik.

### 22.3 CSS Variables

> **Golden Rule:** Semua nilai visual harus dari CSS Variables. Tidak ada toleransi untuk hardcode warna, font, radius, shadow, atau spacing.

### 22.4 Accessibility (WCAG)

| Standar | Level | Implementasi |
| :--- | :--- | :--- |
| Color Contrast | AA (4.5:1) | Semua teks harus kontras cukup |
| Keyboard Navigation | AA | Semua elemen interaktif accessible via keyboard |
| Focus Indicators | AA | Focus ring visible di semua elemen |
| ARIA Labels | A | Label untuk icon-only buttons |
| Semantic HTML | A | Gunakan `<header>`, `<nav>`, `<main>`, `<footer>` |
| Alt Text | A | Semua gambar dekoratif punya alt text |
| Skip Navigation | AA | Link "Skip to content" di halaman |

### 22.5 Lazy Load Assets

- Gambar: gunakan `loading="lazy"` native.
- CSS non-critical: gunakan `media="print" onload="this.media='all'"`.
- JS: gunakan `defer` atau `type="module"`.
- Font: gunakan `font-display: swap` untuk menghindari invisible text.
- Komponen di bawah fold: lazy load dengan Intersection Observer.

### 22.6 Performance Optimization

| Teknik | Dampak | Implementasi |
| :--- | :--- | :--- |
| CSS Purge | -50% ukuran CSS | PurgeCSS via Vite |
| JS Code Splitting | -30% ukuran JS | Dynamic imports |
| Image Optimization | -60% ukuran gambar | WebP + compression |
| Font Subsetting | -70% ukuran font | Hanya karakter Latin + aksen Indonesia |
| Critical CSS | +20% First Paint | Inline CSS untuk above-fold |
| Preload Key Assets | +15% LCP | Preload font, hero image |
| Cache Headers | -90% repeat request | Cache CSS, JS, font 1 tahun |
| Minify HTML | -10% ukuran HTML | Blade minification |

---

## 23. Architecture Diagram

### 23.1 Alur Request Lengkap

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                           THEME ENGINE ARCHITECTURE                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   CLIENT (Browser)                                                          │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    CORE ENGINE                                        │  │
│   │  ┌────────────────────────────────────────────────────────────────┐  │  │
│   │  │                    THEME LOADER                                │  │  │
│   │  │                                                                 │  │  │
│   │  │  1. Read active_theme (Setting Engine)                         │  │  │
│   │  │  2. Validate theme (theme.json, compatibility)                 │  │  │
│   │  │  3. Register view namespace                                    │  │  │
│   │  │  4. Register assets (CSS, JS, Fonts)                           │  │  │
│   │  │  5. Merge configuration (default + customizer)                 │  │  │
│   │  │  6. Generate CSS Variables                                     │  │  │
│   │  │  7. Register Blade Components                                  │  │  │
│   │  └────────────────────────────────────────────────────────────────┘  │  │
│   │                                                                       │  │
│   └───────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    THEME MANAGER (UI Admin)                          │  │
│   │  ┌────────────────────────────────────────────────────────────────┐  │  │
│   │  │  • Daftar Theme    • Preview    • Activate    • Deactivate     │  │  │
│   │  │  • Import ZIP      • Export     • Delete      • Customize      │  │  │
│   │  └────────────────────────────────────────────────────────────────┘  │  │
│   └───────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    LAYOUT SYSTEM                                     │  │
│   │                                                                      │  │
│   │   master.blade.php                                                   │  │
│   │       │                                                              │  │
│   │       ├── admin.blade.php    → Halaman admin (dashboard, dll)       │  │
│   │       ├── guest.blade.php    → Halaman publik (katalog, dll)        │  │
│   │       ├── auth.blade.php     → Halaman login/register               │  │
│   │       ├── landing.blade.php  → Halaman beranda                      │  │
│   │       ├── error.blade.php    → Halaman error                        │  │
│   │       └── print.blade.php    → Halaman cetak                        │  │
│   │                                                                      │  │
│   └──────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    COMPONENT SYSTEM                                  │  │
│   │                                                                      │  │
│   │   Navbar  Sidebar  Footer  Card   Widget  Modal   Alert              │  │
│   │   Breadcrumb  Pagination  Table  Form  Button  Badge  Tabs  Avatar  │  │
│   │                                                                      │  │
│   └──────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    ASSETS                                             │  │
│   │                                                                      │  │
│   │   ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐               │  │
│   │   │  CSS    │  │   JS    │  │ Images  │  │  Fonts  │               │  │
│   │   │Variables│  │Theme Init│  │ Logo    │  │ Inter   │               │  │
│   │   │Theme.css│  │Theme.js  │  │ Favicon │  │ Outfit  │               │  │
│   │   │Dark.css │  │Customizer│  │ BG      │  │ Mono    │               │  │
│   │   └─────────┘  └─────────┘  └─────────┘  └─────────┘               │  │
│   │                                                                      │  │
│   └──────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   ┌──────────────────────────────────────────────────────────────────────┐  │
│   │                    RENDER VIEW                                       │  │
│   │                                                                      │  │
│   │   HTML (layout + components + content) + CSS Variables + Assets     │  │
│   │                                                                      │  │
│   └──────────────────────────────────────────────────────────────────────┘  │
│       │                                                                      │
│       ▼                                                                      │
│   CLIENT (HTML Page with Theme Applied)                                      │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 23.2 Dependency Diagram

```text
                    ┌──────────────────┐
                    │   Setting Engine  │ ← Menyediakan konfigurasi tema
                    └────────┬─────────┘
                             │
                             ▼
┌──────────┐    ┌──────────────────────┐    ┌─────────────┐
│ Menu     │◄───│                      │───►│ Media       │
│ Engine   │    │    THEME ENGINE      │    │ Engine      │
└──────────┘    │                      │    └─────────────┘
                │  ┌────────────────┐  │
┌──────────┐    │  │ Theme Loader   │  │    ┌─────────────┐
│ Widget   │◄───│  │ Theme Manager  │  │───►│ Notification│
│ Engine   │    │  │ Theme Customizer│  │    │ Engine      │
└──────────┘    │  │ Layout System  │  │    └─────────────┘
                │  │ Component System│  │
┌──────────┐    │  └────────────────┘  │    ┌─────────────┐
│ Module   │───►│                      │◄───│ Permission  │
│ Engine   │    └──────────────────────┘    │ Engine      │
└──────────┘                                └─────────────┘
```

---

## 24. Theme Checklist

### 24.1 Checklist untuk Developer Tema

Setiap tema harus memenuhi checklist berikut sebelum dirilis:

#### ✅ File Wajib

| Item | Status | Catatan |
| :--- | :--- | :--- |
| `theme.json` | ✅ Wajib | Manifest lengkap dengan semua property |
| `README.md` | ✅ Wajib | Dokumentasi tema (cara instal, fitur, dependensi) |
| `preview.png` | ✅ Wajib | Gambar preview tema (1200×900px, max 500KB) |
| `Layouts/master.blade.php` | ✅ Wajib | Minimal layout master |
| `Assets/CSS/theme.css` | ✅ Wajib | CSS utama tema |

#### ✅ Responsive

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Desktop (≥1200px) | ✅ Wajib | Full layout |
| Laptop (≥992px) | ✅ Wajib | Layout normal |
| Tablet (≥768px) | ✅ Wajib | Sidebar collapsed |
| Mobile (≥320px) | ✅ Wajib | Single column |

#### ✅ Dark Mode

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Light Palette | ✅ Wajib | Default |
| Dark Palette | ✅ Wajib | Semua elemen punya dark variant |
| Dark Mode Toggle | ✅ Wajib | Di navbar |
| Auto Detect | ✅ Wajib | `prefers-color-scheme` |

#### ✅ CSS Variables

| Item | Status | Catatan |
| :--- | :--- | :--- |
| `--cos-primary` | ✅ Wajib | Warna utama |
| `--cos-secondary` | ✅ Wajib | Warna sekunder |
| `--cos-accent` | ✅ Wajib | Warna aksen |
| `--cos-bg` | ✅ Wajib | Background |
| `--cos-surface` | ✅ Wajib | Surface |
| `--cos-text-primary` | ✅ Wajib | Teks utama |
| `--cos-border` | ✅ Wajib | Border |

#### ✅ Components

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Navbar | ✅ Wajib | Navigasi atas |
| Sidebar | ✅ Wajib | Navigasi samping |
| Footer | ✅ Wajib | Footer halaman |
| Card | ✅ Wajib | Kontainer konten |
| Alert | ✅ Wajib | Notifikasi |
| Button | ✅ Wajib | Tombol aksi |
| Modal | ✅ Wajib | Dialog popup |
| Table | ✅ Wajib | Tabel data |
| Form | ✅ Wajib | Form input |
| Breadcrumb | ✅ Wajib | Navigasi lokasi |
| Pagination | ✅ Wajib | Navigasi halaman |

#### ✅ Accessibility

| Item | Status | Standar |
| :--- | :--- | :--- |
| Color Contrast AA | ✅ Wajib | 4.5:1 untuk teks normal |
| Keyboard Navigation | ✅ Wajib | Tab order logis |
| Focus Indicators | ✅ Wajib | Visible focus ring |
| Semantic HTML | ✅ Wajib | `<header>`, `<nav>`, `<main>`, `<footer>` |
| Alt Text | ✅ Wajib | Semua gambar |
| Skip Navigation | ✅ Wajib | Link skip to content |
| ARIA Labels | ✅ Wajib | Icon buttons |

#### ✅ Performance

| Item | Target | Catatan |
| :--- | :--- | :--- |
| CSS Size | ≤50KB | Setelah minification |
| JS Size | ≤30KB | Setelah minification & tree-shaking |
| First Paint | ≤1.5s | Di koneksi 3G |
| Lighthouse Score | ≥90 | Performance, Accessibility, SEO |
| No Render Blocking | ✅ | CSS critical inline, JS defer |
| Font Loading | `font-display: swap` | Cegah invisible text |

#### ✅ Dokumentasi

| Item | Status | Catatan |
| :--- | :--- | :--- |
| README.md | ✅ Wajib | Instalasi, fitur, dependensi, changelog |
| Screenshots | ✅ Wajib | Dashboard, login, landing, mobile |
| theme.json lengkap | ✅ Wajib | Semua property terisi |
| Contoh konfigurasi | ✅ Direkomendasikan | Di README |

### 24.2 Checklist Verifikasi Theme Engine

| Item | Status | Keterangan |
| :--- | :--- | :--- |
| Theme Loader dapat membaca theme.json | ✅ | Validasi format & property |
| Compatibility Check berfungsi | ✅ | Core, PHP, Laravel version |
| Fallback ke default theme | ✅ | Jika tema aktif corrupt |
| Theme Customizer menyimpan ke DB | ✅ | Setting Engine integration |
| CSS Variables tergenerate otomatis | ✅ | Dari konfigurasi tema |
| Dark mode toggle berfungsi | ✅ | Light/Dark/System mode |
| Responsive di semua device | ✅ | 5 breakpoint |
| Asset loading via Vite | ✅ | Bundling & optimasi |
| Blade Components reusable | ✅ | Props & slot |
| Preview mode session-based | ✅ | Hanya admin yang preview |

---

## Referensi

| Dokumen | Hubungan |
| :--- | :--- |
| [07_CORE_ENGINE.md](07_CORE_ENGINE.md) | Core Engine — Theme Loader, Service Provider, Event System |
| [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md) | Module Engine — view module yang di-render oleh theme |
| [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) | Permission Engine — visibility elemen UI |
| [11_MENU_ENGINE.md](11_MENU_ENGINE.md) | Menu Engine — navigasi yang di-render theme |
| [12_WIDGET_ENGINE.md](12_WIDGET_ENGINE.md) | Widget Engine — widget di dashboard theme |
| [14_MEDIA_ENGINE.md](14_MEDIA_ENGINE.md) | Media Engine — upload & management asset tema |
| [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md) | Notification Engine — notifikasi UI |
| [16_SETTING_ENGINE.md](16_SETTING_ENGINE.md) | Setting Engine — semua konfigurasi tema |
| [26_UI_GUIDELINE.md](26_UI_GUIDELINE.md) | UI Guideline — standar desain visual CosmicLib |

## Catatan

- Dokumen ini adalah **spesifikasi resmi Theme Engine CosmicLib**.
- Semua implementasi tema **WAJIB** mengikuti standar yang ditetapkan di sini.
- Tema **tidak boleh** mengandung Business Logic — hanya presentasi.
- Semua warna **WAJIB** menggunakan CSS Variables — dilarang hardcode.
- Tema default **Cosmic Default** adalah tema wajib yang tidak boleh dihapus.
- Untuk panduan pengembangan tema, lihat [26_UI_GUIDELINE.md](26_UI_GUIDELINE.md).