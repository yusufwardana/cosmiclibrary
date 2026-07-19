# 12 — Widget Engine

**CosmicLib Engine v1.0**
**Dokumen Resmi — Widget Engine**
**Status:** Draft
**Terakhir Diperbarui:** 2026-07-19

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi Widget](#2-filosofi-widget)
3. [Widget Lifecycle](#3-widget-lifecycle)
4. [Widget Structure](#4-widget-structure)
5. [widget.json](#5-widgetjson)
6. [Widget Loader](#6-widget-loader)
7. [Widget Manager](#7-widget-manager)
8. [Widget Categories](#8-widget-categories)
9. [Widget Type](#9-widget-type)
10. [Widget Layout](#10-widget-layout)
11. [Drag & Drop](#11-drag--drop)
12. [Widget Configuration](#12-widget-configuration)
13. [Widget Permission](#13-widget-permission)
14. [Widget Theme Integration](#14-widget-theme-integration)
15. [Widget Refresh](#15-widget-refresh)
16. [Widget Data Source](#16-widget-data-source)
17. [Widget Communication](#17-widget-communication)
18. [Widget Cache](#18-widget-cache)
19. [Default Widgets](#19-default-widgets)
20. [Widget Marketplace (Future)](#20-widget-marketplace-future)
21. [Dashboard Builder](#21-dashboard-builder)
22. [User Dashboard](#22-user-dashboard)
23. [AI Rules](#23-ai-rules)
24. [Best Practice](#24-best-practice)
25. [Architecture Diagram](#25-architecture-diagram)
26. [Widget Naming Convention](#26-widget-naming-convention)
27. [Widget Checklist](#27-widget-checklist)

---

## 1. Pendahuluan

### 1.1 Tujuan

Widget Engine adalah sub-sistem inti CosmicLib yang bertanggung jawab atas seluruh siklus hidup **Dashboard Widget** — mulai dari pendaftaran, pemasangan, konfigurasi, rendering, hingga penghapusan.

Widget Engine menyediakan antarmuka yang **dinamis**, **modular**, **configurable**, **responsive**, **drag & drop ready**, **AI friendly**, dan **enterprise ready** untuk menampilkan informasi ringkas serta aksi cepat kepada pengguna.

### 1.2 Widget vs Module

| Aspek | Widget | Module |
|:------|:-------|:-------|
| **Fungsi utama** | Menyajikan informasi & aksi cepat | Mengelola business logic & data |
| **Kompleksitas** | Rendah — komponen visual | Tinggi — CRUD, workflow, aturan bisnis |
| **Business logic** | ❌ Tidak boleh ada | ✅ Seluruh logic ada di sini |
| **Database query** | ❌ Tidak langsung | ✅ Melalui Repository/Service |
| **Lingkup** | Dashboard card/panel | Fitur lengkap dengan routes, views, controllers |
| **Hubungan** | Widget **milik** Module | Module **menyediakan** Widget |

> **Prinsip Utama:** Widget hanya menyajikan informasi dan aksi cepat. Seluruh business logic tetap berada di Module.

### 1.3 Peran dalam Arsitektur

```
┌─────────────────────────────────────┐
│           User Interface            │
│         (Dashboard Grid)            │
├─────────────────────────────────────┤
│          Widget Engine              │
│  ┌───────────┐  ┌───────────────┐   │
│  │  Loader   │  │   Manager     │   │
│  └───────────┘  └───────────────┘   │
├─────────────────────────────────────┤
│  Permission   │  Theme   │  Setting │
│  Engine       │  Engine  │  Engine  │
├─────────────────────────────────────┤
│          Module Engine              │
│    (Service / Contract / Repo)      │
└─────────────────────────────────────┘
```

---

## 2. Filosofi Widget

Widget adalah **komponen Dashboard** yang bersifat:

| Sifat | Penjelasan |
|:------|:-----------|
| **Dapat dipasang** | Widget dapat ditambahkan ke Dashboard kapan saja |
| **Dapat dipindahkan** | Posisi widget dapat diatur ulang melalui drag & drop |
| **Dapat dihapus** | Widget dapat dilepas dari Dashboard tanpa merusak sistem |
| **Dapat dikonfigurasi** | Setiap widget memiliki pengaturan sendiri |
| **Dapat dibuat oleh Module** | Module dapat mendaftarkan widget mereka sendiri |
| **Dapat dinonaktifkan** | Widget dapat dimatikan tanpa dihapus |
| **Dapat diperbarui** | Widget dapat diperbarui secara independen |

### Prinsip Desain Widget

1. **Single Responsibility** — Satu widget, satu tujuan informasi.
2. **No Business Logic** — Widget hanya menampilkan, tidak memproses.
3. **Engine-Driven** — Warna dari Theme Engine, izin dari Permission Engine, konfigurasi dari Setting Engine.
4. **Loose Coupling** — Widget berkomunikasi melalui Service/Contract, bukan langsung ke database.
5. **User-Centric** — Pengguna mengatur Dashboard mereka sendiri.

---

## 3. Widget Lifecycle

```
┌──────────┐
│  Create   │  Developer membuat widget baru
└────┬─────┘
     ▼
┌──────────┐
│ Register  │  Widget didaftarkan ke Widget Engine
└────┬─────┘
     ▼
┌──────────┐
│  Install  │  Widget dipasang ke sistem (aset, permission, config)
└────┬─────┘
     ▼
┌──────────┐
│  Enable   │  Widget diaktifkan dan siap ditampilkan
└────┬─────┘
     ▼
┌──────────┐
│ Configure │  Admin/user mengatur preferensi widget
└────┬─────┘
     ▼
┌──────────┐
│  Render   │  Widget ditampilkan di Dashboard
└────┬─────┘
     ▼
┌──────────┐
│  Refresh  │  Data widget diperbarui (manual/auto/real-time)
└────┬─────┘
     ▼
┌──────────┐
│  Disable  │  Widget dinonaktifkan (tetap terpasang)
└────┬─────┘
     ▼
┌──────────┐
│  Remove   │  Widget dihapus dari sistem sepenuhnya
└──────────┘
```

### Lifecycle Hooks

| Hook | Trigger | Kegunaan |
|:-----|:--------|:---------|
| `onRegister` | Saat widget didaftarkan | Validasi `widget.json`, cek dependensi |
| `onInstall` | Saat widget dipasang | Publish aset, register permission |
| `onEnable` | Saat widget diaktifkan | Inisialisasi cache, load konfigurasi |
| `onConfigure` | Saat konfigurasi berubah | Validasi config, flush cache |
| `onRender` | Saat widget ditampilkan | Ambil data, render view |
| `onRefresh` | Saat data diperbarui | Refresh data, update cache |
| `onDisable` | Saat widget dinonaktifkan | Bersihkan cache, simpan state |
| `onRemove` | Saat widget dihapus | Hapus aset, hapus permission, hapus config |

---

## 4. Widget Structure

Setiap widget memiliki struktur direktori standar:

```
Widget/
└── StatisticsWidget/
    ├── widget.json              # Manifest widget
    ├── README.md                # Dokumentasi widget
    ├── Provider/
    │   └── StatisticsWidgetServiceProvider.php
    ├── Services/
    │   └── StatisticsService.php
    ├── Views/
    │   ├── widget.blade.php     # Template utama
    │   └── config.blade.php     # Form konfigurasi
    ├── Assets/
    │   ├── css/
    │   │   └── statistics.css
    │   └── js/
    │       └── statistics.js
    └── Tests/
        └── StatisticsWidgetTest.php
```

### Penjelasan Direktori

| Direktori | Fungsi |
|:----------|:-------|
| `widget.json` | Manifest — metadata, konfigurasi, dependensi |
| `README.md` | Dokumentasi penggunaan widget |
| `Provider/` | Service Provider untuk registrasi widget |
| `Services/` | Service layer — perantara ke Module Service |
| `Views/` | Template Blade untuk rendering widget |
| `Assets/` | CSS, JS, dan aset statis widget |
| `Tests/` | Unit test dan feature test widget |

---

## 5. widget.json

File `widget.json` adalah manifest utama setiap widget. Widget Engine membaca file ini untuk registrasi, validasi, dan konfigurasi.

### Contoh Lengkap

```json
{
    "name": "Library Statistics",
    "slug": "library-statistics",
    "description": "Menampilkan statistik perpustakaan secara ringkas",
    "version": "1.0.0",
    "author": "CosmicLib Team",
    "provider": "Modules\\Library\\Widget\\StatisticsWidget\\Provider\\StatisticsWidgetServiceProvider",
    "module": "library",
    "type": "statistics",
    "category": "statistics",
    "icon": "chart-bar",
    "priority": 10,
    "permission": "widget.library.statistics.view",
    "roles": ["admin", "librarian", "staff"],
    "refresh_interval": 300,
    "size": {
        "default": "2x1",
        "min": "1x1",
        "max": "4x2"
    },
    "supported_themes": ["*"],
    "config": {
        "show_chart": true,
        "chart_type": "bar",
        "date_range": "this_month"
    },
    "dependencies": {
        "module": ["library"],
        "widget": []
    }
}
```

### Referensi Property

| Property | Tipe | Wajib | Penjelasan |
|:---------|:-----|:------|:-----------|
| `name` | string | ✅ | Nama tampilan widget |
| `slug` | string | ✅ | Identifier unik (kebab-case) |
| `description` | string | ✅ | Deskripsi singkat widget |
| `version` | string | ✅ | Versi widget (semver) |
| `author` | string | ✅ | Pembuat widget |
| `provider` | string | ✅ | Fully qualified class name Service Provider |
| `module` | string | ✅ | Slug module induk |
| `type` | string | ✅ | Tipe widget (lihat [§9 Widget Type](#9-widget-type)) |
| `category` | string | ✅ | Kategori widget (lihat [§8 Widget Categories](#8-widget-categories)) |
| `icon` | string | ✅ | Nama ikon (icon set standar) |
| `priority` | integer | ❌ | Urutan tampil (default: `100`, semakin kecil semakin awal) |
| `permission` | string | ✅ | Permission yang diperlukan (dari Permission Engine) |
| `roles` | array | ❌ | Role yang dapat melihat widget (dari Permission Engine) |
| `refresh_interval` | integer | ❌ | Interval refresh otomatis dalam detik (`0` = manual only) |
| `size` | object | ✅ | Ukuran grid (`default`, `min`, `max`) dalam format `KolomxBaris` |
| `supported_themes` | array | ❌ | Daftar theme yang didukung (`["*"]` = semua) |
| `config` | object | ❌ | Konfigurasi default widget |
| `dependencies` | object | ❌ | Dependensi terhadap module atau widget lain |

---

## 6. Widget Loader

Widget Loader adalah komponen yang bertanggung jawab untuk **menemukan**, **memvalidasi**, dan **mendaftarkan** widget ke dalam sistem.

### Alur Kerja Widget Loader

```
1. Scan Widget Directory
       ▼
2. Baca widget.json
       ▼
3. Validasi Schema
       ▼
4. Compatibility Check
   ├── Cek versi engine
   ├── Cek dependensi module
   └── Cek dependensi widget
       ▼
5. Register Widget
       ▼
6. Register Assets (CSS, JS)
       ▼
7. Register Permission (ke Permission Engine)
       ▼
8. Register Configuration (ke Setting Engine)
       ▼
9. Widget Siap
```

### Kapabilitas Widget Loader

| Kapabilitas | Penjelasan |
|:------------|:-----------|
| **Scan Widget** | Memindai direktori widget untuk menemukan `widget.json` |
| **Baca widget.json** | Parsing dan validasi manifest widget |
| **Compatibility Check** | Memeriksa kompatibilitas versi engine, module, dan tema |
| **Register Widget** | Mendaftarkan widget ke registry internal Widget Engine |
| **Register Assets** | Mempublikasi dan mendaftarkan file CSS/JS widget |
| **Register Permission** | Mendaftarkan permission widget ke Permission Engine |
| **Register Configuration** | Mendaftarkan konfigurasi default ke Setting Engine |

### Aturan Loader

1. Widget dengan `widget.json` tidak valid akan **dilewati** dan dicatat ke log.
2. Widget dengan dependensi tidak terpenuhi akan **dilewati** dan dicatat ke log.
3. Widget yang sudah terdaftar dengan slug yang sama akan **ditolak** (tidak ada duplikasi).
4. Loader berjalan saat **boot aplikasi** dan dapat dipicu ulang melalui **artisan command**.

---

## 7. Widget Manager

Widget Manager adalah antarmuka administrasi untuk mengelola seluruh widget yang terpasang di sistem.

### Fitur Widget Manager

| Fitur | Penjelasan |
|:------|:-----------|
| **Daftar Widget** | Menampilkan semua widget terdaftar beserta status |
| **Enable** | Mengaktifkan widget yang dinonaktifkan |
| **Disable** | Menonaktifkan widget tanpa menghapus |
| **Preview** | Menampilkan pratinjau widget sebelum dipasang |
| **Delete** | Menghapus widget dari sistem (termasuk aset, permission, config) |
| **Import ZIP** | Memasang widget baru dari file ZIP |
| **Export** | Mengekspor widget menjadi file ZIP |
| **Bulk Action** | Enable/Disable/Delete beberapa widget sekaligus |

### Tampilan Widget Manager

```
┌─────────────────────────────────────────────────────────────┐
│  Widget Manager                                    [+ Import] │
├──────┬──────────────────┬────────┬────────┬────────┬────────┤
│  #   │ Nama Widget      │ Module │ Status │ Versi  │ Aksi   │
├──────┼──────────────────┼────────┼────────┼────────┼────────┤
│  1   │ Library Stats    │ Lib    │ ✅ On  │ 1.0.0  │ ⚙ ✕   │
│  2   │ Loan Chart       │ Lib    │ ✅ On  │ 1.0.0  │ ⚙ ✕   │
│  3   │ Server Info      │ Sys    │ ❌ Off │ 1.0.0  │ ⚙ ✕   │
│  4   │ Backup Status    │ Sys    │ ✅ On  │ 1.2.0  │ ⚙ ✕   │
└──────┴──────────────────┴────────┴────────┴────────┴────────┘
```

### Permission Widget Manager

| Aksi | Permission |
|:-----|:-----------|
| Lihat daftar widget | `widget.manager.view` |
| Enable/Disable widget | `widget.manager.toggle` |
| Import widget | `widget.manager.import` |
| Export widget | `widget.manager.export` |
| Hapus widget | `widget.manager.delete` |

---

## 8. Widget Categories

Setiap widget harus memiliki satu kategori. Kategori digunakan untuk pengelompokan di Widget Manager dan Dashboard Builder.

| Kategori | Slug | Penjelasan |
|:---------|:-----|:-----------|
| Dashboard | `dashboard` | Widget umum untuk dashboard utama |
| Statistik | `statistics` | Ringkasan angka dan metrik |
| Chart | `chart` | Grafik dan visualisasi data |
| Kalender | `calendar` | Tampilan kalender dan jadwal |
| Notifikasi | `notification` | Daftar notifikasi dan peringatan |
| Aktivitas | `activity` | Log aktivitas dan riwayat terbaru |
| Aksi Cepat | `quick_action` | Tombol dan pintasan aksi cepat |
| Informasi | `information` | Informasi umum dan pengumuman |
| Media | `media` | Galeri, gambar, video |
| Perpustakaan | `library` | Widget khusus modul perpustakaan |
| CMS | `cms` | Widget khusus modul CMS |
| Sistem | `system` | Informasi server, kesehatan sistem, backup |

---

## 9. Widget Type

Tipe widget menentukan **perilaku rendering** dan **format data** yang digunakan.

| Tipe | Slug | Penjelasan | Contoh |
|:-----|:-----|:-----------|:-------|
| Static Widget | `static` | Konten tetap, tidak berubah otomatis | Pengumuman, teks informasi |
| Dynamic Widget | `dynamic` | Konten berubah berdasarkan data | Statistik anggota, jumlah buku |
| Chart Widget | `chart` | Menampilkan grafik/visualisasi | Grafik peminjaman bulanan |
| List Widget | `list` | Menampilkan daftar item | Buku terbaru, aktivitas terakhir |
| Table Widget | `table` | Menampilkan data tabular | Daftar denda aktif |
| Calendar Widget | `calendar` | Menampilkan kalender/jadwal | Jadwal kegiatan perpustakaan |
| Media Widget | `media` | Menampilkan konten media | Galeri sampul buku |
| Custom Widget | `custom` | Widget dengan rendering kustom penuh | Widget yang dibuat khusus |

### Hierarki Tipe

```
Widget Type
├── static         → Render sekali, jarang berubah
├── dynamic        → Render ulang saat data berubah
│   ├── chart      → Variasi dynamic dengan grafik
│   ├── list       → Variasi dynamic dengan daftar
│   └── table      → Variasi dynamic dengan tabel
├── calendar       → Render berdasarkan tanggal
├── media          → Render konten media
└── custom         → Render sepenuhnya kustom
```

---

## 10. Widget Layout

### Grid System

Widget Engine menggunakan **12-column grid system** yang responsif. Setiap widget menempati satu atau lebih kolom dan baris.

| Format Ukuran | Kolom | Baris | Contoh Penggunaan |
|:--------------|:------|:------|:------------------|
| `1x1` | 3 kolom (1/4) | 1 baris | Angka statistik tunggal |
| `2x1` | 6 kolom (1/2) | 1 baris | Statistik dengan grafik kecil |
| `3x1` | 9 kolom (3/4) | 1 baris | Tabel ringkas |
| `4x1` | 12 kolom (full) | 1 baris | Banner, pengumuman |
| `2x2` | 6 kolom (1/2) | 2 baris | Grafik besar |
| `4x2` | 12 kolom (full) | 2 baris | Dashboard overview penuh |

### Contoh Layout Dashboard

```
┌─────────────────────────────────────────────────────────┐
│                    Dashboard Admin                       │
├──────────────┬──────────────┬──────────────┬────────────┤
│  Total Buku  │ Total Anggota│ Peminjaman   │   Denda    │
│    1.250     │    3.420     │  Aktif: 89   │  Rp 150K   │
│    (1x1)     │    (1x1)     │    (1x1)     │   (1x1)    │
├──────────────┴──────────────┼──────────────┴────────────┤
│                             │                           │
│   Grafik Peminjaman (2x2)   │   Aktivitas Terbaru (2x2) │
│                             │                           │
│   ▁▂▃▅▇▆▅▃▂▁▂▃▅▇          │   • Budi meminjam buku    │
│                             │   • Ani mengembalikan     │
│                             │   • Cici mendaftar        │
├──────────────┬──────────────┼───────────────────────────┤
│  Kalender    │ Aksi Cepat   │    Pengumuman             │
│   (1x1)      │   (1x1)      │      (2x1)               │
└──────────────┴──────────────┴───────────────────────────┘
```

### Preset Layout

| Preset | Konfigurasi | Cocok untuk |
|:-------|:------------|:------------|
| 1 Kolom | `[4]` | Mobile, laporan sederhana |
| 2 Kolom | `[2, 2]` | Tablet, dashboard ringkas |
| 3 Kolom | `[1, 2, 1]` | Desktop, dashboard standar |
| 4 Kolom | `[1, 1, 1, 1]` | Desktop lebar, monitoring |

### Responsivitas

| Breakpoint | Lebar | Kolom Maksimal |
|:-----------|:------|:---------------|
| Mobile | < 640px | 1 kolom |
| Tablet | 640px – 1024px | 2 kolom |
| Desktop | 1024px – 1440px | 3 kolom |
| Wide | > 1440px | 4 kolom |

---

## 11. Drag & Drop

Widget mendukung interaksi drag & drop penuh di Dashboard.

### Kemampuan Drag & Drop

| Aksi | Penjelasan |
|:-----|:-----------|
| **Pindah Posisi** | Seret widget ke posisi baru di grid |
| **Resize** | Ubah ukuran widget (dalam batas `min`/`max` dari `widget.json`) |
| **Pin** | Kunci widget di posisi tertentu (tidak dapat dipindahkan) |
| **Collapse** | Lipat widget menjadi header saja |
| **Expand** | Buka kembali widget yang dilipat |
| **Reset Layout** | Kembalikan layout ke posisi default |

### Penyimpanan Layout

Layout disimpan per-user di database:

| Field | Tipe | Penjelasan |
|:------|:-----|:-----------|
| `user_id` | bigint | ID pengguna |
| `dashboard_id` | bigint | ID dashboard |
| `widget_slug` | string | Slug widget |
| `position_x` | integer | Posisi kolom (0-based) |
| `position_y` | integer | Posisi baris (0-based) |
| `width` | integer | Lebar dalam satuan kolom |
| `height` | integer | Tinggi dalam satuan baris |
| `is_pinned` | boolean | Apakah dikunci |
| `is_collapsed` | boolean | Apakah dilipat |
| `order` | integer | Urutan tampil |

---

## 12. Widget Configuration

### Sumber Konfigurasi

Widget memperoleh konfigurasi dari empat sumber dengan urutan prioritas:

```
Prioritas Tertinggi
       ▲
       │
  4. User Preferences      ← Pengaturan per-user
  3. System Settings        ← Setting Engine (global)
  2. Database               ← Konfigurasi tersimpan di DB
  1. widget.json            ← Nilai default dari manifest
       │
       ▼
Prioritas Terendah
```

### Aturan Konfigurasi

1. **Tidak boleh hardcode** — Seluruh konfigurasi harus berasal dari sumber di atas.
2. **Merge strategy** — Konfigurasi prioritas tinggi menimpa yang lebih rendah.
3. **Validasi** — Setiap perubahan konfigurasi harus divalidasi sebelum disimpan.
4. **Cache** — Konfigurasi yang sudah di-merge di-cache untuk performa.

### Contoh Konfigurasi Widget

| Key | Sumber | Nilai |
|:----|:-------|:------|
| `show_chart` | widget.json | `true` |
| `chart_type` | Database | `"line"` (override dari default `"bar"`) |
| `date_range` | System Settings | `"last_30_days"` |
| `color_scheme` | User Preferences | `"blue"` |

---

## 13. Widget Permission

Widget terintegrasi penuh dengan **Permission Engine**.

### Level Akses Widget

| Level | Slug | Penjelasan |
|:------|:-----|:-----------|
| **Hidden** | `hidden` | Widget tidak terlihat sama sekali |
| **Readonly** | `readonly` | Widget terlihat, tidak dapat dikonfigurasi |
| **Editable** | `editable` | Widget terlihat, dapat dikonfigurasi |
| **Manageable** | `manageable` | Akses penuh: konfigurasi, posisi, enable/disable |

### Format Permission Widget

```
widget.{module}.{widget_slug}.{action}
```

Contoh:

| Permission | Penjelasan |
|:-----------|:-----------|
| `widget.library.statistics.view` | Melihat widget statistik perpustakaan |
| `widget.library.statistics.configure` | Mengkonfigurasi widget statistik |
| `widget.system.server_info.view` | Melihat widget informasi server |
| `widget.dashboard.quick_access.manage` | Mengelola widget aksi cepat |

### Alur Pengecekan Permission

```
1. User membuka Dashboard
       ▼
2. Widget Engine memuat daftar widget
       ▼
3. Cek permission per-widget via Permission Engine
   ├── Hidden    → Widget tidak di-render
   ├── Readonly  → Render tanpa tombol konfigurasi
   ├── Editable  → Render dengan tombol konfigurasi
   └── Manageable → Render dengan kontrol penuh
       ▼
4. Render widget yang diizinkan
```

---

## 14. Widget Theme Integration

Seluruh widget **wajib** mengikuti tema aktif dari **Theme Engine**. Tidak boleh hardcode warna, font, atau style.

### Aspek Theme Integration

| Aspek | Penjelasan |
|:------|:-----------|
| **Card Style** | Widget menggunakan card component dari tema aktif |
| **Border** | Border radius, width, dan color mengikuti tema |
| **Color** | Warna primer, sekunder, aksen mengikuti palet tema |
| **Dark Mode** | Widget harus mendukung dark mode |
| **Light Mode** | Widget harus mendukung light mode |
| **Typography** | Font family, size, weight mengikuti tema |

### Variabel Theme yang Digunakan Widget

```
--widget-bg
--widget-border
--widget-text
--widget-heading
--widget-accent
--widget-shadow
--widget-radius
--widget-padding
```

### Aturan Theme

1. Widget tidak boleh mendefinisikan warna secara langsung di CSS inline.
2. Widget harus menggunakan CSS variable dari Theme Engine.
3. Widget harus mendukung `supported_themes` yang dideklarasikan di `widget.json`.
4. Widget dengan `supported_themes: ["*"]` harus kompatibel dengan semua tema.

---

## 15. Widget Refresh

### Mode Refresh

| Mode | Penjelasan | Konfigurasi |
|:-----|:-----------|:------------|
| **Manual Refresh** | Pengguna menekan tombol refresh di widget | Selalu tersedia |
| **Auto Refresh** | Widget memperbarui data secara periodik | `refresh_interval` di `widget.json` (dalam detik) |
| **Real-time** *(Future)* | Widget menerima update secara real-time via WebSocket | Akan didukung di versi mendatang |

### Aturan Refresh

1. `refresh_interval: 0` — hanya manual refresh.
2. `refresh_interval: 300` — auto refresh setiap 5 menit.
3. Minimum interval: **30 detik** (untuk mencegah beban server berlebihan).
4. Refresh hanya mengambil data baru, **bukan** re-render seluruh widget.
5. Indikator loading harus ditampilkan saat refresh berlangsung.

### Refresh Indicator

```
┌──────────────────────────────┐
│  Library Statistics    🔄    │  ← Ikon refresh / loading spinner
│  ─────────────────────────── │
│  Total Buku: 1.250           │
│  Terakhir diperbarui: 12:30  │
└──────────────────────────────┘
```

---

## 16. Widget Data Source

Widget dapat mengambil data dari berbagai sumber melalui **Service layer**.

| Sumber | Penjelasan | Contoh |
|:-------|:-----------|:-------|
| **Database** | Data dari tabel melalui Repository | Jumlah buku, anggota aktif |
| **API** | Data dari API internal atau eksternal | Cuaca, kurs mata uang |
| **Module Service** | Data yang disediakan oleh Module melalui Service class | Statistik peminjaman |
| **Cache** | Data yang sudah di-cache untuk performa | Data yang jarang berubah |
| **Queue** | Hasil proses background yang telah selesai | Laporan yang di-generate |

### Aturan Data Source

1. Widget **tidak boleh** query database secara langsung.
2. Widget **wajib** menggunakan Service atau Contract yang disediakan Module.
3. Data yang jarang berubah **wajib** di-cache.
4. Widget harus menangani kondisi **data tidak tersedia** dengan graceful (tampilkan placeholder).

---

## 17. Widget Communication

Widget tidak boleh langsung mengakses Module atau database. Komunikasi harus melalui lapisan abstraksi.

### Pola Komunikasi yang Diizinkan

| Pola | Penjelasan | Penggunaan |
|:-----|:-----------|:-----------|
| **Service** | Class service yang menyediakan data | Ambil statistik, daftar item |
| **Contract** | Interface/kontrak yang diimplementasikan Module | Decoupling widget dari implementasi Module |
| **Repository** | Abstraksi akses data | Query terstandar melalui Repository Pattern |
| **Event** | Sistem event untuk komunikasi antar-komponen | Notifikasi perubahan data |

### Pola Komunikasi yang DILARANG

| Pola | Alasan |
|:-----|:-------|
| ❌ Direct DB Query | Melanggar separation of concerns |
| ❌ Direct Model Access | Widget menjadi tightly coupled dengan Module |
| ❌ Direct Controller Call | Melanggar arsitektur berlapis |
| ❌ Session Manipulation | Widget tidak boleh mengubah state session |

### Diagram Komunikasi

```
┌──────────┐    Contract     ┌──────────────┐    Repository    ┌──────────┐
│  Widget  │ ──────────────▶ │ Module       │ ───────────────▶ │ Database │
│          │                 │ Service      │                  │          │
│          │ ◀────────────── │              │ ◀─────────────── │          │
│          │    Data/DTO     │              │    Eloquent      │          │
└──────────┘                 └──────────────┘                  └──────────┘
                                    │
                                    │ Event
                                    ▼
                             ┌──────────────┐
                             │ Event System │
                             └──────────────┘
```

---

## 18. Widget Cache

### Strategi Cache

Widget Engine menggunakan cache untuk meningkatkan performa rendering Dashboard.

| Aspek | Strategi |
|:------|:---------|
| **Cache Key** | `widget:{slug}:{user_id}:{config_hash}` |
| **Cache Driver** | Mengikuti konfigurasi cache aplikasi (Redis, File, Database) |
| **TTL Default** | Sama dengan `refresh_interval` widget |
| **Cache Scope** | Per-user (karena data bisa berbeda per permission) |

### Auto Invalidation

Cache widget otomatis di-invalidasi saat:

| Trigger | Penjelasan |
|:--------|:-----------|
| **Data berubah** | Event dari Module memicu invalidasi cache widget terkait |
| **Permission berubah** | Perubahan role/permission user menghapus cache widget |
| **Module berubah** | Module diaktifkan/dinonaktifkan/diperbarui menghapus cache widget terkait |
| **Theme berubah** | Perubahan tema menghapus cache rendering widget |
| **Konfigurasi berubah** | Perubahan config widget menghapus cache widget tersebut |

### Alur Cache

```
1. Request render widget
       ▼
2. Cek cache
   ├── HIT  → Return cached content
   └── MISS → Lanjut ke langkah 3
       ▼
3. Ambil data via Service
       ▼
4. Render widget view
       ▼
5. Simpan ke cache dengan TTL
       ▼
6. Return rendered content
```

---

## 19. Default Widgets

CosmicLib menyediakan widget bawaan berikut yang langsung tersedia setelah instalasi:

### Widget Perpustakaan

| # | Widget | Slug | Tipe | Ukuran Default |
|:--|:-------|:-----|:-----|:---------------|
| 1 | Dashboard Overview | `dashboard.overview` | dynamic | 4x2 |
| 2 | Library Statistics | `library.statistics` | statistics | 2x1 |
| 3 | Books Statistics | `library.books_statistics` | statistics | 1x1 |
| 4 | Members Statistics | `library.members_statistics` | statistics | 1x1 |
| 5 | Loan Statistics | `library.loan_statistics` | statistics | 1x1 |
| 6 | Return Statistics | `library.return_statistics` | statistics | 1x1 |
| 7 | Fine Statistics | `library.fine_statistics` | statistics | 1x1 |
| 8 | Visitor Statistics | `library.visitor_statistics` | statistics | 1x1 |

### Widget Umum

| # | Widget | Slug | Tipe | Ukuran Default |
|:--|:-------|:-----|:-----|:---------------|
| 9 | Announcement Widget | `dashboard.announcement` | static | 2x1 |
| 10 | Calendar Widget | `dashboard.calendar` | calendar | 2x2 |
| 11 | Quick Access Widget | `dashboard.quick_access` | custom | 1x1 |
| 12 | Recent Activity Widget | `dashboard.recent_activity` | list | 2x2 |
| 13 | Notification Widget | `dashboard.notification` | list | 1x2 |

### Widget Sistem

| # | Widget | Slug | Tipe | Ukuran Default |
|:--|:-------|:-----|:-----|:---------------|
| 14 | System Health Widget | `system.health` | dynamic | 2x1 |
| 15 | Server Information Widget | `system.server_info` | static | 2x1 |
| 16 | Backup Status Widget | `system.backup_status` | dynamic | 1x1 |
| 17 | Update Status Widget | `system.update_status` | dynamic | 1x1 |

---

## 20. Widget Marketplace (Future)

> **Status:** Direncanakan untuk versi mendatang.

Widget Marketplace memungkinkan distribusi dan pemasangan widget dari pihak ketiga.

### Fitur Marketplace

| Fitur | Penjelasan |
|:------|:-----------|
| **Install Widget** | Pasang widget dari marketplace ke sistem |
| **Update Widget** | Perbarui widget ke versi terbaru |
| **Compatibility Check** | Periksa kompatibilitas widget dengan versi engine dan module |
| **Signature Validation** | Verifikasi keaslian dan integritas widget melalui tanda tangan digital |

### Alur Install dari Marketplace

```
1. Browse Marketplace
       ▼
2. Pilih Widget
       ▼
3. Compatibility Check
   ├── Engine version
   ├── Module dependencies
   └── Theme compatibility
       ▼
4. Download & Verify Signature
       ▼
5. Extract ke Widget Directory
       ▼
6. Jalankan Widget Loader
       ▼
7. Widget siap digunakan
```

### Keamanan Marketplace

| Aspek | Mekanisme |
|:------|:----------|
| Integritas file | Checksum SHA-256 |
| Keaslian sumber | Digital signature validation |
| Isolasi | Widget berjalan dalam sandbox terbatas |
| Review | Widget marketplace melalui proses review sebelum dipublikasi |

---

## 21. Dashboard Builder

Dashboard Builder adalah antarmuka visual bagi Administrator untuk menyusun dan mengelola layout Dashboard.

### Kemampuan Dashboard Builder

| Fitur | Penjelasan |
|:------|:-----------|
| **Tambah Widget** | Pilih dan tambahkan widget dari daftar yang tersedia |
| **Hapus Widget** | Lepas widget dari Dashboard |
| **Atur Layout** | Susun posisi widget menggunakan drag & drop |
| **Atur Ukuran** | Resize widget dalam batas min/max |
| **Atur Urutan** | Tentukan urutan tampil widget |
| **Reset Dashboard** | Kembalikan layout ke konfigurasi default |
| **Simpan Template** | Simpan layout sebagai template yang dapat digunakan ulang |

### Tampilan Dashboard Builder

```
┌─────────────────────────────────────────────────────────────┐
│  Dashboard Builder                      [Reset] [Simpan]    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Widget Tersedia:                                           │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │ Stats   │ │ Chart   │ │ Calendar│ │ Activity│          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│                                                             │
│  ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─  │
│                                                             │
│  Dashboard Preview:                                         │
│  ┌──────────────┬──────────────┬───────────────────────┐    │
│  │  Widget A    │  Widget B    │      Widget C         │    │
│  │  (1x1)       │  (1x1)       │      (2x1)           │    │
│  ├──────────────┴──────────────┼───────────────────────┤    │
│  │          Widget D           │      Widget E         │    │
│  │          (2x2)              │      (2x2)            │    │
│  │                             │                       │    │
│  └─────────────────────────────┴───────────────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Permission Dashboard Builder

| Aksi | Permission |
|:-----|:-----------|
| Akses Dashboard Builder | `dashboard.builder.access` |
| Tambah/Hapus widget | `dashboard.builder.manage` |
| Simpan template | `dashboard.builder.template` |
| Reset dashboard | `dashboard.builder.reset` |

---

## 22. User Dashboard

Setiap user dapat memiliki Dashboard **personalisasi** mereka sendiri.

### Hierarki Dashboard

```
1. System Default Dashboard
   └── Digunakan jika tidak ada dashboard lain
       ▼
2. Role Default Dashboard
   └── Dashboard default per-role (diatur Admin)
       ▼
3. User Custom Dashboard
   └── Dashboard yang dikustomisasi oleh user sendiri
```

### Aturan Dashboard

| Aturan | Penjelasan |
|:-------|:-----------|
| **Role Dashboard** | Setiap role dapat memiliki Dashboard default |
| **User Dashboard** | User dapat mengkustomisasi Dashboard mereka (jika diizinkan) |
| **Admin Template** | Administrator dapat membuat Dashboard Template untuk role tertentu |
| **Reset** | User dapat mereset ke Dashboard default role mereka |
| **Penyimpanan** | Layout Dashboard disimpan per-user di database |

### Hak Akses Dashboard User

| Aksi | Permission |
|:-----|:-----------|
| Kustomisasi dashboard sendiri | `dashboard.user.customize` |
| Reset ke default | `dashboard.user.reset` |
| Kelola dashboard role | `dashboard.role.manage` |
| Buat template dashboard | `dashboard.template.create` |

---

## 23. AI Rules

Aturan wajib bagi seluruh AI assistant saat bekerja dengan Widget Engine:

### WAJIB

| # | Aturan |
|:--|:-------|
| 1 | **Tidak menaruh business logic di Widget** — Logic ada di Module Service |
| 2 | **Tidak query database langsung** — Gunakan Service/Contract/Repository |
| 3 | **Menggunakan Service** — Semua data diperoleh melalui Service layer |
| 4 | **Menggunakan Permission Engine** — Semua akses dikontrol Permission Engine |
| 5 | **Menggunakan Theme Engine** — Semua styling mengikuti Theme Engine |
| 6 | **Menggunakan Widget Configuration** — Tidak hardcode konfigurasi |
| 7 | **Menggunakan Setting Engine** — Konfigurasi global dari Setting Engine |
| 8 | **Menggunakan Menu Engine** — Jika widget memiliki link navigasi |

### DILARANG

| # | Larangan |
|:--|:---------|
| 1 | ❌ Hardcode warna, font, atau style di widget |
| 2 | ❌ Menggunakan `env()` di luar file config |
| 3 | ❌ Menggunakan `dd()` atau `dump()` |
| 4 | ❌ Menulis raw SQL tanpa parameter binding |
| 5 | ❌ Membuat widget yang melakukan CRUD langsung |
| 6 | ❌ Membuat widget tanpa `widget.json` |
| 7 | ❌ Membuat widget tanpa permission |
| 8 | ❌ Mengabaikan responsive design |

---

## 24. Best Practice

### Pengembangan Widget

| Praktik | Penjelasan |
|:--------|:-----------|
| **Reusable Widget** | Buat widget yang dapat digunakan di berbagai konteks |
| **Responsive Design** | Widget harus tampil baik di semua ukuran layar |
| **Cache** | Selalu cache data yang jarang berubah |
| **Lazy Loading** | Muat data widget hanya saat terlihat di viewport |
| **Accessibility** | Pastikan widget dapat diakses oleh screen reader dan keyboard |
| **Performance** | Optimalkan query, minimalkan aset, gunakan pagination untuk list |
| **Error Handling** | Widget harus menampilkan pesan error yang ramah, bukan crash |
| **Loading State** | Tampilkan skeleton/spinner saat data sedang dimuat |
| **Empty State** | Tampilkan pesan informatif saat data kosong |
| **Documentation** | Setiap widget wajib memiliki README.md |

### Contoh Loading & Empty State

```
Loading State:                    Empty State:
┌─────────────────────┐          ┌─────────────────────┐
│  Library Stats  🔄  │          │  Library Stats      │
│  ─────────────────  │          │  ─────────────────  │
│  ░░░░░░░░░░░░░░░░░  │          │                     │
│  ░░░░░░░░░░░        │          │   📊 Belum ada data │
│  ░░░░░░░░░░░░░░     │          │   untuk ditampilkan │
│                     │          │                     │
└─────────────────────┘          └─────────────────────┘
```

---

## 25. Architecture Diagram

### Diagram Lengkap Widget Engine

```
┌─────────────────────────────────────────────────────────────────┐
│                        User Interface                           │
│                      (Dashboard Grid)                           │
│   ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐      │
│   │ W1   │ │ W2   │ │ W3   │ │ W4   │ │ W5   │ │ W6   │      │
│   └──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘      │
│      └────────┴────────┴────┬───┴────────┴────────┘            │
│                             │                                   │
├─────────────────────────────┼───────────────────────────────────┤
│                      Dashboard Builder                          │
│              (Drag & Drop, Resize, Layout)                      │
├─────────────────────────────┼───────────────────────────────────┤
│                             │                                   │
│                      Widget Manager                             │
│          (Enable, Disable, Import, Export, Delete)               │
├─────────────────────────────┼───────────────────────────────────┤
│                             │                                   │
│                      Widget Loader                              │
│      (Scan, Validate, Register, Compatibility Check)            │
├──────────┬──────────────────┼──────────────────┬────────────────┤
│          │                  │                  │                │
│  Permission      Theme Engine        Setting Engine             │
│  Engine          (Style, Color,      (Config, Prefs)            │
│  (Access)        Dark/Light)                                    │
├──────────┴──────────────────┼──────────────────┴────────────────┤
│                             │                                   │
│                      Module Engine                              │
│            (Service / Contract / Repository)                    │
├─────────────────────────────┼───────────────────────────────────┤
│                             │                                   │
│                      Data Layer                                 │
│              (Database, API, Cache, Queue)                      │
└─────────────────────────────────────────────────────────────────┘
```

### Alur Data

```
Module (Data Source)
       │
       ▼
Module Service / Contract
       │
       ▼
Widget Service
       │
       ▼
Widget Loader → Registrasi
       │
       ▼
Widget Manager → Kontrol status
       │
       ▼
Permission Engine → Cek akses
       │
       ▼
Theme Engine → Terapkan style
       │
       ▼
Dashboard Grid → Atur posisi & ukuran
       │
       ▼
User Interface → Tampilkan ke pengguna
```

---

## 26. Widget Naming Convention

### Format Penamaan

```
{module}.{nama_widget}
```

### Aturan

1. Gunakan **lowercase** seluruhnya.
2. Gunakan **dot** (`.`) sebagai pemisah module dan widget.
3. Gunakan **underscore** (`_`) untuk nama multi-kata.
4. Nama harus **deskriptif** dan **unik** dalam cakupan module.

### Contoh Penamaan

| Slug | Module | Widget |
|:-----|:-------|:-------|
| `library.statistics` | Library | Statistik Perpustakaan |
| `library.latest_books` | Library | Buku Terbaru |
| `library.loan_chart` | Library | Grafik Peminjaman |
| `library.members_statistics` | Library | Statistik Anggota |
| `library.fine_statistics` | Library | Statistik Denda |
| `system.server_info` | System | Informasi Server |
| `system.backup_status` | System | Status Backup |
| `system.health` | System | Kesehatan Sistem |
| `system.update_status` | System | Status Pembaruan |
| `cms.latest_posts` | CMS | Postingan Terbaru |
| `cms.page_statistics` | CMS | Statistik Halaman |
| `dashboard.overview` | Dashboard | Ringkasan Dashboard |
| `dashboard.quick_access` | Dashboard | Aksi Cepat |
| `dashboard.recent_activity` | Dashboard | Aktivitas Terbaru |
| `dashboard.announcement` | Dashboard | Pengumuman |
| `dashboard.calendar` | Dashboard | Kalender |
| `dashboard.notification` | Dashboard | Notifikasi |

---

## 27. Widget Checklist

Setiap widget yang dikembangkan **harus** memenuhi checklist berikut sebelum dinyatakan siap:

| # | Item | Status | Keterangan |
|:--|:-----|:-------|:-----------|
| 1 | `widget.json` | ✅ Wajib | Manifest lengkap dan valid |
| 2 | `README.md` | ✅ Wajib | Dokumentasi penggunaan widget |
| 3 | Permission | ✅ Wajib | Permission terdaftar di Permission Engine |
| 4 | Theme Support | ✅ Wajib | Mendukung tema aktif, dark mode, light mode |
| 5 | Responsive | ✅ Wajib | Tampil baik di mobile, tablet, desktop |
| 6 | Refresh | ✅ Wajib | Manual refresh berfungsi, auto refresh jika dikonfigurasi |
| 7 | Configuration | ✅ Wajib | Konfigurasi dari widget.json / DB / Settings / User Prefs |
| 8 | Documentation | ✅ Wajib | Dokumentasi teknis lengkap |
| 9 | Error Handling | ✅ Wajib | Widget menangani error dengan graceful |
| 10 | Loading State | ✅ Wajib | Skeleton/spinner saat memuat data |
| 11 | Empty State | ✅ Wajib | Pesan informatif saat data kosong |
| 12 | Cache | ✅ Wajib | Data di-cache sesuai strategi |
| 13 | Accessibility | ✅ Wajib | Dapat diakses oleh screen reader & keyboard |
| 14 | Tests | ✅ Wajib | Unit test dan/atau feature test |
| 15 | No Hardcode | ✅ Wajib | Tidak ada hardcode warna, permission, role, menu |
| 16 | Service Layer | ✅ Wajib | Data diperoleh melalui Service, bukan query langsung |

---

## Catatan Perubahan

| Versi | Tanggal | Perubahan |
|:------|:--------|:----------|
| 1.0.0 | 2026-07-19 | Dokumen awal Widget Engine |

---

> **Dokumen ini adalah spesifikasi resmi Widget Engine CosmicLib.**
> Seluruh pengembangan widget harus mengacu pada dokumen ini sebagai sumber kebenaran tunggal.