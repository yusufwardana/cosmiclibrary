# 🌌 11 — Menu Engine

> **Spesifikasi resmi Menu Engine** — sistem navigasi dinamis CosmicLib yang mengelola registrasi, rendering, hierarki, caching, dan pengaturan visibilitas menu berdasarkan permission, role, dan modul yang aktif.
>
> Menu Engine memastikan bahwa seluruh navigasi aplikasi bersifat dinamis, modular, dan sepenuhnya terintegrasi dengan Permission Engine, Module Engine, dan Theme Engine.
>
> Baca setelah [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md), [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md), [`09_THEME_ENGINE.md`](09_THEME_ENGINE.md), dan [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/11_MENU_ENGINE.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Final Blueprint` — spesifikasi resmi Menu Engine |
| **Engine** | CosmicLib Engine |
| **Framework** | Laravel 12 · PHP 8.3+ · MySQL 8+ |
| **Arsitektur** | Dynamic Navigation · Permission-Based · Theme-Aware |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi](#2-filosofi)
3. [Menu Lifecycle](#3-menu-lifecycle)
4. [Menu Structure](#4-menu-structure)
5. [Menu Entity](#5-menu-entity)
6. [Menu Registration](#6-menu-registration)
7. [Menu Hierarchy](#7-menu-hierarchy)
8. [Menu Properties](#8-menu-properties)
9. [Menu Permission](#9-menu-permission)
10. [Menu Theme Integration](#10-menu-theme-integration)
11. [Menu Search](#11-menu-search)
12. [Favorite Menu](#12-favorite-menu)
13. [Recent Menu](#13-recent-menu)
14. [Dashboard Shortcut](#14-dashboard-shortcut)
15. [Menu Badge](#15-menu-badge)
16. [Responsive Navigation](#16-responsive-navigation)
17. [Menu Configuration](#17-menu-configuration)
18. [Menu Cache](#18-menu-cache)
19. [Menu Security](#19-menu-security)
20. [Future Features](#20-future-features)
21. [AI Rules](#21-ai-rules)
22. [Best Practice](#22-best-practice)
23. [Architecture Diagram](#23-architecture-diagram)
24. [Menu Naming Convention](#24-menu-naming-convention)
25. [Menu Matrix](#25-menu-matrix)
26. [Checklist](#26-checklist)

---

## 1. Pendahuluan

### 1.1 Apa Itu Menu Engine?

**Menu Engine** adalah subsistem CosmicLib yang bertanggung jawab mengelola seluruh aspek navigasi aplikasi. Menu Engine menangani penemuan, registrasi, validasi, sinkronisasi, caching, dan rendering seluruh item navigasi — semuanya secara dinamis berdasarkan modul yang aktif, permission pengguna, dan konfigurasi tema.

Menu Engine memastikan bahwa:

- **Navigasi sepenuhnya dinamis** — tidak ada menu yang di-hardcode di Blade, controller, atau file view manapun.
- **Menu berasal dari Module** — setiap modul mendefinisikan menu-nya sendiri melalui file `menu.php`.
- **Permission-aware** — menu otomatis disembunyikan atau dinonaktifkan berdasarkan hak akses pengguna.
- **Theme-aware** — menu mengikuti layout tema aktif (sidebar, topbar, mobile drawer, dll).
- **Enterprise ready** — mendukung hierarki tak terbatas, badge dinamis, favorit, pencarian, dan shortcut.

### 1.2 Mengapa Seluruh Menu Harus Berasal dari Module Engine?

| Alasan | Penjelasan |
| :--- | :--- |
| **Konsistensi** | Satu sumber kebenaran — menu didefinisikan di modul, bukan tersebar di file view |
| **Modularitas** | Saat modul diaktifkan, menu-nya otomatis muncul. Saat dinonaktifkan, menu hilang |
| **Keamanan** | Setiap menu memiliki permission — pengguna hanya melihat menu yang diizinkan |
| **Maintainability** | Perubahan menu cukup di satu file (`menu.php`), tidak perlu ubah layout |
| **Scalability** | Ratusan modul dapat meregistrasikan menu tanpa konflik |
| **AI Friendly** | AI agent dapat membaca dan memodifikasi menu secara deterministik |
| **Audit Trail** | Setiap perubahan menu tercatat di database |

### 1.3 Menu Engine Mengelola Navigasi, Bukan Business Logic

Prinsip fundamental Menu Engine:

> **Menu Engine adalah lapisan navigasi murni.**
>
> - Menu Engine **tidak boleh** menjalankan business logic.
> - Menu Engine **tidak boleh** mengubah data bisnis.
> - Menu Engine **tidak boleh** menangani otorisasi aksi (hanya visibilitas).
> - Menu Engine **hanya** mengatur navigasi — apa yang terlihat dan ke mana tujuannya.
> - Business logic tetap berada di **Service Layer** masing-masing module.

### 1.4 Prinsip Utama

```text
┌─────────────────────────────────────────────────┐
│            PRINSIP MENU ENGINE                    │
├─────────────────────────────────────────────────┤
│  1. Dynamic      — Menu dibangun saat runtime    │
│  2. Modular      — Setiap module = menu sendiri  │
│  3. Permission   — Visibility berbasis hak akses │
│  4. Theme Aware  — Mengikuti layout tema aktif   │
│  5. Responsive   — Desktop, tablet, mobile       │
│  6. Cached       — Performa tinggi via cache     │
│  7. Searchable   — Menu dapat dicari user        │
│  8. AI Friendly  — Kontrak eksplisit & konsisten │
│  9. Enterprise   — Siap skala besar              │
└─────────────────────────────────────────────────┘
```

---

## 2. Filosofi

### 2.1 Menu Adalah Representasi Navigasi dari Module

Setiap menu di CosmicLib merupakan **cerminan langsung** dari modul yang terpasang dan aktif. Menu bukan entitas mandiri — menu adalah **jendela navigasi** menuju fitur yang disediakan oleh module.

```text
┌──────────────────────────────────────────────────┐
│                 HUBUNGAN MODULE ↔ MENU             │
├──────────────────────────────────────────────────┤
│                                                    │
│   Module Library aktif                             │
│       → Menu "Perpustakaan" muncul                 │
│           → Submenu "Buku", "Anggota", "Pinjaman" │
│                                                    │
│   Module CMS aktif                                 │
│       → Menu "Konten" muncul                       │
│           → Submenu "Artikel", "Halaman"            │
│                                                    │
│   Module Library dinonaktifkan                     │
│       → Menu "Perpustakaan" HILANG otomatis        │
│       → Submenu terkait HILANG otomatis            │
│                                                    │
└──────────────────────────────────────────────────┘
```

### 2.2 Menu Tidak Ditulis Manual

Menu **dilarang** ditulis langsung di file Blade, controller, atau konfigurasi global. Setiap menu harus:

| Aturan | Penjelasan |
| :--- | :--- |
| **Didefinisikan di `menu.php`** | Setiap module menyediakan file `Config/menu.php` sebagai deklarasi menu |
| **Diregistrasi otomatis** | Menu Loader membaca `menu.php` saat module diaktifkan |
| **Disinkronisasi ke database** | Menu disimpan di tabel `menus` untuk manajemen runtime |
| **Di-cache untuk performa** | Menu yang sudah dibangun di-cache per role/user |
| **Di-render oleh Theme Engine** | Tampilan menu mengikuti komponen tema aktif |

### 2.3 Menu Diregistrasi Otomatis Saat Module Diaktifkan

```text
Module diaktifkan
    │
    ├── Module Engine membaca module.json
    │
    ├── Module Engine menemukan Config/menu.php
    │
    ├── Menu Loader membaca definisi menu
    │
    ├── Menu disinkronisasi ke tabel menus
    │
    ├── Cache menu di-invalidasi
    │
    └── Menu baru muncul di navigasi
```

### 2.4 Menu Dapat Disembunyikan Berdasarkan Permission

Menu Engine bekerja sama dengan Permission Engine untuk menentukan visibilitas:

```text
User membuka dashboard
    │
    ├── Menu Engine mengambil semua menu aktif
    │
    ├── Untuk setiap menu:
    │       ├── Cek permission user → Tampilkan / Sembunyikan
    │       ├── Cek status module → Aktif / Nonaktif
    │       └── Cek visibility flag → Visible / Hidden
    │
    └── Hanya menu yang lolos semua pengecekan yang ditampilkan
```

---

## 3. Menu Lifecycle

### 3.1 Diagram Lifecycle

```text
┌────────────────┐
│ MODULE INSTALL  │  Module baru dipasang ke sistem
└───────┬────────┘
        │
        ▼
┌────────────────┐
│  BACA menu.php │  Menu Loader membaca definisi menu dari module
└───────┬────────┘
        │
        ▼
┌────────────────┐
│   VALIDASI     │  Validasi format, slug unik, permission valid
└───────┬────────┘
        │
        ▼
┌────────────────┐
│  REGISTRASI    │  Menu didaftarkan ke Menu Registry
└───────┬────────┘
        │
        ▼
┌────────────────┐
│ SINKRONISASI   │  Menu disinkronisasi ke tabel database menus
│   DATABASE     │
└───────┬────────┘
        │
        ▼
┌────────────────┐
│  CACHE MENU    │  Menu di-cache per role untuk performa
└───────┬────────┘
        │
        ▼
┌────────────────┐
│  RENDER MENU   │  Theme Engine merender menu ke layout
└───────┬────────┘
        │
        ▼
┌────────────────┐
│ PERMISSION     │  Permission Engine memfilter menu per user
│    CHECK       │
└───────┬────────┘
        │
        ▼
┌────────────────┐
│ TAMPILKAN KE   │  Menu final ditampilkan di sidebar/topbar
│    USER        │
└────────────────┘
```

### 3.2 Penjelasan Setiap Tahap

| Tahap | Deskripsi | Aksi Sistem |
| :--- | :--- | :--- |
| **Module Install** | Module baru dipasang ke direktori `Modules/` | File module dicopy, dependency diperiksa |
| **Baca menu.php** | Menu Loader membaca file `Config/menu.php` dari module | Parse array definisi menu |
| **Validasi** | Validasi format, slug unik, permission terdaftar, route valid | Jika gagal → log warning, menu diabaikan |
| **Registrasi** | Menu didaftarkan ke Menu Registry di memory | Menu masuk ke daftar pending sinkronisasi |
| **Sinkronisasi Database** | Menu di-insert/update ke tabel `menus` | Data persisten untuk manajemen runtime |
| **Cache Menu** | Menu tree yang sudah dibangun di-cache per role | Menghindari query berulang |
| **Render Menu** | Theme Engine merender menu ke komponen sidebar/topbar | Menggunakan Blade component tema aktif |
| **Permission Check** | Permission Engine memfilter menu berdasarkan hak akses user | Menu tanpa izin disembunyikan |
| **Tampilkan ke User** | Menu final yang lolos semua filter ditampilkan | User melihat navigasi sesuai perannya |

### 3.3 Lifecycle Saat Module Dinonaktifkan

```text
Module dinonaktifkan
    │
    ├── 1. Menu Engine menerima event ModuleDisabled
    │
    ├── 2. Semua menu milik module ditandai is_active = false
    │
    ├── 3. Cache menu di-invalidasi
    │
    ├── 4. Menu tidak lagi muncul di navigasi
    │
    └── 5. Data menu tetap di database (tidak dihapus)
            └── Jika module diaktifkan kembali → menu muncul lagi
```

---

## 4. Menu Structure

### 4.1 File `menu.php` Wajib Ada di Setiap Module

Setiap module yang membutuhkan navigasi **wajib** menyediakan file:

```text
Modules/{ModuleName}/Config/menu.php
```

### 4.2 Komponen File `menu.php`

File `menu.php` berisi array definisi menu dengan komponen berikut:

| Komponen | Deskripsi | Wajib |
| :--- | :--- | :--- |
| **Menu Group** | Kelompok menu utama (level 1) — contoh: "Perpustakaan" | Ya |
| **Menu Item** | Item menu individual — contoh: "Daftar Buku" | Ya |
| **Sub Menu** | Item menu anak (level 2, 3, dst) | Tidak |
| **Permission** | Slug permission yang diperlukan untuk melihat menu | Ya |
| **Icon** | Nama ikon dari icon library (contoh: Lucide, Heroicons) | Ya |
| **Route** | Named route Laravel tujuan menu | Ya |
| **Order** | Urutan tampil menu (angka kecil = lebih atas) | Ya |
| **Badge** | Badge dinamis (contoh: jumlah notifikasi) | Tidak |
| **Visibility** | Flag visibilitas tambahan | Tidak |

### 4.3 Contoh File `menu.php` — Modul Library

```text
Modules/Library/Config/menu.php

return [
    [
        'group'       => 'library',
        'title'       => 'Perpustakaan',
        'description' => 'Manajemen perpustakaan digital',
        'icon'        => 'book-open',
        'route'       => 'library.dashboard',
        'permission'  => 'library.books.view',
        'order'       => 30,
        'badge'       => null,
        'visible'     => true,
        'children'    => [
            [
                'title'      => 'Dashboard',
                'icon'       => 'layout-dashboard',
                'route'      => 'library.dashboard',
                'permission' => 'library.books.view',
                'order'      => 1,
            ],
            [
                'title'      => 'Daftar Buku',
                'icon'       => 'book',
                'route'      => 'library.books.index',
                'permission' => 'library.books.view',
                'order'      => 2,
                'badge'      => [
                    'type'   => 'count',
                    'source' => 'library.books.total',
                    'color'  => 'primary',
                ],
            ],
            [
                'title'      => 'Anggota',
                'icon'       => 'users',
                'route'      => 'library.members.index',
                'permission' => 'library.members.view',
                'order'      => 3,
            ],
            [
                'title'      => 'Peminjaman',
                'icon'       => 'book-copy',
                'route'      => 'library.loans.index',
                'permission' => 'library.loans.view',
                'order'      => 4,
                'badge'      => [
                    'type'   => 'count',
                    'source' => 'library.loans.pending',
                    'color'  => 'warning',
                ],
            ],
            [
                'title'      => 'Pengembalian',
                'icon'       => 'undo-2',
                'route'      => 'library.returns.index',
                'permission' => 'library.loans.return',
                'order'      => 5,
            ],
            [
                'title'      => 'Denda',
                'icon'       => 'banknote',
                'route'      => 'library.fines.index',
                'permission' => 'library.fines.view',
                'order'      => 6,
            ],
            [
                'title'      => 'Kategori',
                'icon'       => 'tags',
                'route'      => 'library.categories.index',
                'permission' => 'library.categories.view',
                'order'      => 7,
            ],
            [
                'title'      => 'Laporan',
                'icon'       => 'bar-chart-3',
                'route'      => 'library.reports.index',
                'permission' => 'library.reports.view',
                'order'      => 8,
            ],
            [
                'title'      => 'Pengaturan',
                'icon'       => 'settings',
                'route'      => 'library.settings.index',
                'permission' => 'library.settings.manage',
                'order'      => 99,
            ],
        ],
    ],
];
```

### 4.4 Contoh File `menu.php` — Modul System

```text
Modules/System/Config/menu.php

return [
    [
        'group'       => 'system',
        'title'       => 'Sistem',
        'description' => 'Pengaturan dan manajemen sistem',
        'icon'        => 'settings-2',
        'route'       => 'system.settings.index',
        'permission'  => 'system.settings.view',
        'order'       => 90,
        'visible'     => true,
        'children'    => [
            [
                'title'      => 'Pengaturan Umum',
                'icon'       => 'sliders-horizontal',
                'route'      => 'system.settings.index',
                'permission' => 'system.settings.view',
                'order'      => 1,
            ],
            [
                'title'      => 'Manajemen Pengguna',
                'icon'       => 'user-cog',
                'route'      => 'system.users.index',
                'permission' => 'system.users.view',
                'order'      => 2,
            ],
            [
                'title'      => 'Role & Permission',
                'icon'       => 'shield',
                'route'      => 'system.roles.index',
                'permission' => 'system.roles.view',
                'order'      => 3,
            ],
            [
                'title'      => 'Backup',
                'icon'       => 'hard-drive-download',
                'route'      => 'system.backup.index',
                'permission' => 'system.backup.view',
                'order'      => 4,
            ],
            [
                'title'      => 'Pembaruan Sistem',
                'icon'       => 'refresh-cw',
                'route'      => 'system.update.index',
                'permission' => 'system.update.view',
                'order'      => 5,
            ],
            [
                'title'      => 'Log Aktivitas',
                'icon'       => 'file-text',
                'route'      => 'system.logs.index',
                'permission' => 'system.logs.view',
                'order'      => 6,
            ],
        ],
    ],
];
```

---

## 5. Menu Entity

### 5.1 Entity Relationship

Menu Engine terdiri dari entitas-entitas berikut:

```text
┌─────────────────────────┐        ┌──────────────────────┐
│          menus           │        │    menu_badges       │
├─────────────────────────┤        ├──────────────────────┤
│ id (bigint PK)          │        │ id (bigint PK)       │
│ parent_id (FK → menus)  │──┐     │ menu_id (FK)         │
│ module (string)         │  │     │ type (string)        │
│ slug (string, unique)   │  │     │ source (string)      │
│ title (string)          │  │     │ color (string)       │
│ description (text)      │  │     │ max_display (int)    │
│ icon (string)           │  │     │ created_at           │
│ route (string)          │  │     │ updated_at           │
│ permission (string)     │  │     └──────────────────────┘
│ order (integer)         │  │
│ color (string, null)    │  │     ┌──────────────────────┐
│ is_visible (boolean)    │  │     │   menu_shortcuts     │
│ is_active (boolean)     │  │     ├──────────────────────┤
│ metadata (JSON, null)   │  │     │ id (bigint PK)       │
│ created_at              │  │     │ menu_id (FK)         │
│ updated_at              │  │     │ role_id (FK)         │
└─────────────────────────┘  │     │ order (integer)      │
         │                   │     │ created_at           │
         └───────────────────┘     └──────────────────────┘
         (self-referencing
          parent-child)            ┌──────────────────────┐
                                   │   menu_favorites     │
┌──────────────────────┐           ├──────────────────────┤
│    menu_recents      │           │ id (bigint PK)       │
├──────────────────────┤           │ user_id (FK)         │
│ id (bigint PK)       │           │ menu_id (FK)         │
│ user_id (FK)         │           │ order (integer)      │
│ menu_id (FK)         │           │ created_at           │
│ visited_at (ts)      │           └──────────────────────┘
│ visit_count (int)    │
└──────────────────────┘
```

### 5.2 Penjelasan Entity

| Entity | Fungsi | Relasi |
| :--- | :--- | :--- |
| **menus** | Entitas utama menu — menyimpan seluruh item navigasi | Self-referencing (parent-child) via `parent_id` |
| **menu_badges** | Badge dinamis yang melekat pada menu | Belongs to `menus` |
| **menu_shortcuts** | Shortcut dashboard yang dapat diatur per role | Belongs to `menus` dan `roles` |
| **menu_favorites** | Menu favorit yang dipilih oleh user | Belongs to `menus` dan `users` |
| **menu_recents** | Riwayat menu terakhir yang diakses user | Belongs to `menus` dan `users` |

### 5.3 Detail Entity: Menu Group

**Menu Group** adalah menu level teratas yang menjadi pengelompokan utama navigasi.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Kelompok navigasi utama di sidebar |
| **Contoh** | Perpustakaan, Sistem, Konten, Laporan |
| **Parent** | `null` (tidak punya parent) |
| **Icon** | Wajib memiliki icon |
| **Permission** | Wajib — jika semua children hidden, group juga hidden |

### 5.4 Detail Entity: Menu Item

**Menu Item** adalah item navigasi individual yang mengarah ke halaman tertentu.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Link navigasi ke halaman spesifik |
| **Contoh** | Daftar Buku, Anggota, Peminjaman |
| **Parent** | Bisa Menu Group atau Menu Item lain |
| **Route** | Wajib — named route Laravel |
| **Permission** | Wajib — slug permission dari Permission Engine |

### 5.5 Detail Entity: Sub Menu

**Sub Menu** adalah menu anak dari Menu Item, mendukung hierarki multi-level.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Navigasi sub-level untuk organisasi yang lebih detail |
| **Contoh** | Kategori → Fiksi, Non-Fiksi, Referensi |
| **Parent** | Menu Item (level 2 atau lebih) |
| **Kedalaman** | Disarankan maksimal 3 level, opsional unlimited |

### 5.6 Detail Entity: Menu Permission

Setiap menu terintegrasi dengan Permission Engine untuk kontrol visibilitas.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Menentukan siapa yang bisa melihat menu |
| **Mekanisme** | Cek `user_can(permission_slug)` |
| **Default** | Jika tidak ada permission → menu visible untuk semua user terotentikasi |
| **Cascading** | Jika parent hidden → semua children otomatis hidden |

### 5.7 Detail Entity: Menu Badge

Badge adalah indikator visual dinamis yang menampilkan informasi kuantitatif.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Menampilkan angka atau status di samping menu |
| **Contoh** | Peminjaman (5), Approval (3), Notifikasi (12) |
| **Sumber** | Service class yang terdaftar di `badge.source` |
| **Warna** | `primary`, `success`, `warning`, `danger`, `info` |
| **Refresh** | Otomatis saat halaman dimuat, interval configurable |

### 5.8 Detail Entity: Menu Shortcut

Shortcut adalah pintasan menu yang tampil di dashboard.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Akses cepat ke menu penting dari dashboard |
| **Konfigurasi** | Administrator mengatur per role |
| **Tampilan** | Card / icon grid di halaman dashboard |

### 5.9 Detail Entity: Menu Favorite

Favorite adalah menu pilihan personal user.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | User menandai menu yang sering diakses |
| **Tampilan** | Bagian khusus di sidebar (atas) atau quick access panel |
| **Limit** | Configurable, default 10 item |
| **Urutkan** | User dapat mengubah urutan favorit |

### 5.10 Detail Entity: Menu Recent

Recent adalah riwayat menu yang terakhir diakses user.

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Menampilkan menu yang baru saja dibuka |
| **Jumlah** | Configurable via Setting Engine, default 10 item |
| **Otomatis** | Direkam otomatis saat user mengakses halaman |
| **Privasi** | Data recent bersifat per-user, tidak bisa dilihat user lain |

---

## 6. Menu Registration

### 6.1 Alur Registrasi Menu

```text
┌──────────────────────────────────────────────────────┐
│                 MENU REGISTRATION FLOW                 │
├──────────────────────────────────────────────────────┤
│                                                        │
│  1. Module Engine mengaktifkan module                  │
│     └─ Dispatch event: ModuleEnabled                   │
│                                                        │
│  2. Menu Loader menerima event                         │
│     └─ Cari file: Modules/{Name}/Config/menu.php       │
│                                                        │
│  3. Baca dan parse menu.php                            │
│     └─ Konversi array PHP ke koleksi menu              │
│                                                        │
│  4. Validasi setiap item menu                          │
│     ├─ Cek slug unik                                   │
│     ├─ Cek route terdaftar                             │
│     ├─ Cek permission terdaftar di Permission Engine   │
│     ├─ Cek icon tersedia                               │
│     └─ Jika gagal → log warning, skip item             │
│                                                        │
│  5. Sinkronisasi ke database                           │
│     ├─ INSERT menu baru                                │
│     ├─ UPDATE menu yang berubah                        │
│     └─ Soft-deactivate menu yang dihapus dari menu.php │
│                                                        │
│  6. Invalidasi cache                                   │
│     └─ Clear cache menu untuk semua role               │
│                                                        │
│  7. Dispatch event: MenuRegistered                     │
│     └─ Payload: { module, menu_count }                 │
│                                                        │
└──────────────────────────────────────────────────────┘
```

### 6.2 Core Engine Membaca `menu.php`

Proses pemuatan `menu.php` terjadi dalam konteks Module Engine boot sequence:

```text
Core Engine Boot
    │
    ├── Module Engine dimuat (boot order: 4)
    │       └── Untuk setiap module aktif:
    │               ├── Baca module.json
    │               ├── Registrasi route, permission, event
    │               └── Registrasi menu via menu.php
    │
    ├── Menu Engine dimuat (boot order: 5)
    │       ├── Kumpulkan semua menu dari registry
    │       ├── Bangun hierarki (parent-child tree)
    │       ├── Sinkronisasi ke database
    │       └── Cache menu tree per role
    │
    └── Theme Engine dimuat (boot order: 6)
            └── Render menu tree ke sidebar/topbar
```

### 6.3 Validasi Menu

Menu Loader **wajib** memvalidasi setiap item menu sebelum menyimpan ke database:

| Validasi | Aksi Jika Gagal |
| :--- | :--- |
| Slug duplikat | Menu ditolak, log error |
| Route tidak terdaftar | Menu ditolak, log warning |
| Permission tidak terdaftar | Menu diterima dengan warning (graceful) |
| Icon kosong | Menu diterima, gunakan icon default |
| Order duplikat dalam parent yang sama | Menu diterima, auto-increment order |
| Title kosong | Menu ditolak, log error |
| Parent tidak ditemukan | Menu dijadikan root level, log warning |

---

## 7. Menu Hierarchy

### 7.1 Level yang Didukung

Menu Engine mendukung hierarki multi-level:

| Level | Nama | Contoh | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Level 1** | Menu Group | Perpustakaan, Sistem, Konten | ✅ Wajib |
| **Level 2** | Menu Item | Daftar Buku, Anggota, Peminjaman | ✅ Wajib |
| **Level 3** | Sub Menu | Kategori → Fiksi, Non-Fiksi | ⚠️ Opsional |
| **Level 4+** | Deep Nesting | Nesting lebih dalam | ⚠️ Tidak disarankan |

### 7.2 Diagram Hierarki

```text
Menu Tree
│
├── 📦 Perpustakaan (Level 1 — Group)
│   ├── 📊 Dashboard (Level 2 — Item)
│   ├── 📚 Daftar Buku (Level 2 — Item)
│   │   ├── 📂 Berdasarkan Kategori (Level 3 — Sub)
│   │   └── 📂 Berdasarkan Penerbit (Level 3 — Sub)
│   ├── 👥 Anggota (Level 2 — Item)
│   ├── 📋 Peminjaman (Level 2 — Item)
│   ├── 💰 Denda (Level 2 — Item)
│   ├── 📈 Laporan (Level 2 — Item)
│   └── ⚙️ Pengaturan (Level 2 — Item)
│
├── 📝 Konten (Level 1 — Group)
│   ├── 📄 Artikel (Level 2 — Item)
│   ├── 📃 Halaman (Level 2 — Item)
│   └── 📢 Pengumuman (Level 2 — Item)
│
└── ⚙️ Sistem (Level 1 — Group)
    ├── 🔧 Pengaturan Umum (Level 2 — Item)
    ├── 👤 Manajemen Pengguna (Level 2 — Item)
    ├── 🛡️ Role & Permission (Level 2 — Item)
    ├── 💾 Backup (Level 2 — Item)
    └── 🔄 Pembaruan (Level 2 — Item)
```

### 7.3 Aturan Hierarki

| Aturan | Penjelasan |
| :--- | :--- |
| **Unlimited nesting** | Secara teknis didukung, tapi disarankan maksimal 3 level |
| **Auto-hide parent** | Jika semua children disembunyikan (permission), parent otomatis disembunyikan |
| **Orphan protection** | Menu tanpa parent yang valid dijadikan root level |
| **Circular reference** | Dilarang — child tidak boleh menjadi parent dari ancestor-nya |
| **Order scoping** | Urutan menu berlaku dalam scope parent yang sama |

### 7.4 Resolusi Visibilitas Parent

```text
Menu Group "Perpustakaan"
    │
    ├── "Daftar Buku"     → permission: library.books.view     → ✅ User punya
    ├── "Anggota"          → permission: library.members.view   → ❌ User tidak punya
    ├── "Peminjaman"       → permission: library.loans.view     → ✅ User punya
    └── "Pengaturan"       → permission: library.settings.manage → ❌ User tidak punya

Hasil:
    Menu Group "Perpustakaan" → ✅ TAMPILKAN (ada children yang visible)
        ├── "Daftar Buku"    → ✅ TAMPILKAN
        ├── "Anggota"        → ❌ SEMBUNYIKAN
        ├── "Peminjaman"     → ✅ TAMPILKAN
        └── "Pengaturan"     → ❌ SEMBUNYIKAN

─────────────────────────────────────────

Menu Group "Sistem"
    │
    ├── "Pengaturan"       → permission: system.settings.view → ❌ User tidak punya
    ├── "Pengguna"         → permission: system.users.view    → ❌ User tidak punya
    └── "Backup"           → permission: system.backup.view   → ❌ User tidak punya

Hasil:
    Menu Group "Sistem" → ❌ SEMBUNYIKAN (semua children hidden)
```

---

## 8. Menu Properties

### 8.1 Daftar Property Menu

Setiap item menu memiliki property berikut:

| Property | Tipe | Wajib | Deskripsi | Contoh |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | Auto | Primary key, auto-increment | `1` |
| `slug` | `string` | ✅ | Identifier unik menu (dot notation) | `library.books` |
| `title` | `string` | ✅ | Label menu dalam Bahasa Indonesia | `Daftar Buku` |
| `description` | `string` | ❌ | Deskripsi singkat (untuk tooltip/search) | `Kelola koleksi buku` |
| `icon` | `string` | ✅ | Nama ikon dari icon library | `book-open` |
| `route` | `string` | ✅ | Named route Laravel tujuan menu | `library.books.index` |
| `module` | `string` | ✅ | Slug module pemilik menu | `library` |
| `permission` | `string` | ✅ | Slug permission untuk visibilitas | `library.books.view` |
| `order` | `integer` | ✅ | Urutan tampil (kecil = atas) | `10` |
| `parent_id` | `bigint` | ❌ | ID parent menu (null = root level) | `null` atau `1` |
| `badge` | `JSON` | ❌ | Konfigurasi badge dinamis | `{"type":"count","source":"..."}` |
| `color` | `string` | ❌ | Warna aksen menu (override tema) | `#4F46E5` |
| `is_visible` | `boolean` | ✅ | Flag visibilitas manual | `true` |
| `is_active` | `boolean` | ✅ | Status aktif (terkait module status) | `true` |
| `metadata` | `JSON` | ❌ | Data tambahan (target, CSS class, dll) | `{"target":"_blank"}` |

### 8.2 Property Detail: `slug`

Slug menu menggunakan format **dot notation** yang konsisten:

```text
Format: {module}.{resource}
Atau:   {module}.{resource}.{sub-resource}

Contoh:
  library.dashboard
  library.books
  library.members
  library.loans
  library.settings
  system.settings
  system.users
  system.backup
  cms.posts
  cms.pages
```

### 8.3 Property Detail: `order`

Order menentukan posisi menu di dalam parent yang sama:

| Range Order | Kategori | Contoh |
| :--- | :--- | :--- |
| `1–9` | Dashboard & Overview | Dashboard |
| `10–29` | Fitur utama module | Buku, Anggota |
| `30–59` | Fitur operasional | Peminjaman, Pengembalian |
| `60–79` | Laporan & Analitik | Laporan, Statistik |
| `80–89` | Konfigurasi module | Kategori, Penerbit |
| `90–99` | Pengaturan | Settings |

### 8.4 Property Detail: `metadata`

Metadata adalah field JSON fleksibel untuk data tambahan:

| Key Metadata | Tipe | Deskripsi |
| :--- | :--- | :--- |
| `target` | `string` | Target link: `_self`, `_blank` |
| `css_class` | `string` | CSS class tambahan untuk styling |
| `data_attributes` | `object` | HTML data attributes |
| `divider_before` | `boolean` | Tampilkan garis pemisah sebelum menu |
| `divider_after` | `boolean` | Tampilkan garis pemisah setelah menu |
| `new_tab` | `boolean` | Buka di tab baru |
| `external_url` | `string` | URL eksternal (override route) |

---

## 9. Menu Permission

### 9.1 Integrasi dengan Permission Engine

Menu Engine terintegrasi penuh dengan Permission Engine. Setiap menu memiliki field `permission` yang mereferensi slug permission dari Permission Engine.

```text
┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│  MENU ENGINE  │────▶│ PERMISSION ENGINE │────▶│  ACCESS DECISION │
│              │     │                  │     │                  │
│  menu.       │     │  user_can()      │     │  SHOW / HIDE     │
│  permission  │     │  = true / false  │     │                  │
└──────────────┘     └──────────────────┘     └──────────────────┘
```

### 9.2 Mekanisme Pengecekan

```text
Render menu item
    │
    ├── 1. Ambil permission slug dari menu
    │       └── permission = "library.books.view"
    │
    ├── 2. CEK SUPER ADMIN
    │       ├── Jika Super Admin → ✅ TAMPILKAN (bypass)
    │       └── Jika bukan → Lanjut
    │
    ├── 3. CEK PERMISSION USER
    │       ├── user_can('library.books.view')
    │       │       ├── TRUE  → ✅ TAMPILKAN
    │       │       └── FALSE → ❌ SEMBUNYIKAN
    │       │
    │       └── Sumber permission:
    │               ├── Role permission (role_permission)
    │               └── User override (user_permission)
    │
    ├── 4. CEK MODULE AKTIF
    │       ├── Module library enabled?
    │       │       ├── TRUE  → ✅ Lanjut
    │       │       └── FALSE → ❌ SEMBUNYIKAN
    │
    └── 5. CEK VISIBILITY FLAG
            ├── is_visible = true  → ✅ TAMPILKAN
            └── is_visible = false → ❌ SEMBUNYIKAN
```

### 9.3 Aksi Otomatis Berdasarkan Permission

| Aksi | Kondisi | Efek Visual |
| :--- | :--- | :--- |
| **Hide** | User tidak memiliki permission `view` | Menu tidak muncul sama sekali |
| **Disable** | User memiliki permission `view` tapi tidak `access` | Menu muncul tapi tidak bisa diklik (grayed out) |
| **Readonly** | User memiliki permission `view` tapi tidak `edit` | Menu mengarah ke halaman read-only |

### 9.4 Permission Cascading

```text
Permission Cascading Rules:

1. Jika parent HIDDEN → semua children HIDDEN
   (Tidak peduli permission children)

2. Jika parent VISIBLE → children dicek individual
   (Setiap child memiliki permission sendiri)

3. Jika semua children HIDDEN → parent HIDDEN
   (Auto-hide parent kosong)

4. Jika minimal 1 child VISIBLE → parent VISIBLE
   (Parent ditampilkan meskipun beberapa children hidden)
```

---

## 10. Menu Theme Integration

### 10.1 Menu Mengikuti Theme

Menu Engine tidak menentukan tampilan visual — itu adalah tanggung jawab Theme Engine. Menu Engine hanya menyediakan **data navigasi**, sementara Theme Engine bertanggung jawab atas **rendering visual**.

```text
┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│  MENU ENGINE  │────▶│  THEME ENGINE    │────▶│    USER SEES     │
│              │     │                  │     │                  │
│  Menu Tree   │     │  Sidebar         │     │  ┌──────────┐   │
│  (data)      │     │  Topbar          │     │  │ Sidebar  │   │
│              │     │  Mobile Drawer   │     │  │ Menu     │   │
│              │     │  Footer Menu     │     │  │          │   │
│              │     │  Quick Menu      │     │  └──────────┘   │
└──────────────┘     └──────────────────┘     └──────────────────┘
```

### 10.2 Komponen Navigasi Theme

Setiap tema **wajib** menyediakan komponen berikut untuk merender menu:

| Komponen | Lokasi di Theme | Fungsi |
| :--- | :--- | :--- |
| **Sidebar** | `Components/sidebar.blade.php` | Navigasi utama di sisi kiri (desktop) |
| **Topbar** | `Components/navbar.blade.php` | Navigasi atas dengan menu horizontal |
| **Mobile Menu** | `Partials/mobile-menu.blade.php` | Drawer navigasi untuk perangkat mobile |
| **Footer Menu** | `Partials/footer-main.blade.php` | Link navigasi di footer (opsional) |
| **Quick Menu** | `Components/quick-menu.blade.php` | Menu akses cepat (shortcut, favorit, recent) |

### 10.3 Theme Menerima Data Menu

Theme Engine menerima menu tree dari Menu Engine dalam format terstruktur:

```text
Menu Tree yang diterima Theme:
{
    menus: [
        {
            slug: "library",
            title: "Perpustakaan",
            icon: "book-open",
            route: "library.dashboard",
            is_active: true,
            badge: { value: 5, color: "warning" },
            children: [
                {
                    slug: "library.books",
                    title: "Daftar Buku",
                    icon: "book",
                    route: "library.books.index",
                    is_current: true,
                    badge: null,
                    children: []
                },
                ...
            ]
        },
        ...
    ],
    favorites: [...],
    recents: [...],
    shortcuts: [...]
}
```

### 10.4 Active State

Theme Engine harus menandai menu yang sedang aktif (halaman saat ini):

```text
Deteksi Active Menu:

1. Cocokkan current route dengan menu route
   └── Request::route()->getName() == menu.route → ACTIVE

2. Jika current route adalah child route
   └── Parent menu juga ditandai ACTIVE (expanded)

3. Active state disertakan dalam data menu tree
   └── is_current: true/false
   └── is_expanded: true/false (untuk parent)
```

---

## 11. Menu Search

### 11.1 Smart Search

Menu Engine menyediakan fitur pencarian menu yang memungkinkan user menemukan halaman dengan cepat tanpa menavigasi hierarki menu.

### 11.2 Kriteria Pencarian

| Kriteria | Contoh Query | Contoh Hasil |
| :--- | :--- | :--- |
| **Nama Menu** | "buku" | Daftar Buku, Kategori Buku |
| **Route** | "library.books" | Daftar Buku |
| **Module** | "perpustakaan" | Semua menu modul Library |
| **Keyword** | "pinjam" | Peminjaman, Pengembalian |
| **Deskripsi** | "kelola anggota" | Manajemen Anggota |

### 11.3 Fitur Search

```text
Menu Search
    │
    ├── Input: query string dari user
    │
    ├── Filter: hanya menu yang user punya permission
    │
    ├── Match: fuzzy matching pada title, description, route, module
    │
    ├── Rank: hasil diurutkan berdasarkan relevansi
    │       ├── Exact match pada title → skor tinggi
    │       ├── Partial match pada title → skor sedang
    │       ├── Match pada description → skor rendah
    │       └── Match pada route/module → skor rendah
    │
    └── Output: daftar menu yang cocok (maksimal 10 hasil)
```

### 11.4 Antarmuka Search

```text
┌─────────────────────────────────────────┐
│  🔍 Cari menu...                 [⌘K]  │
├─────────────────────────────────────────┤
│                                         │
│  📚 Daftar Buku                         │
│     Perpustakaan → Daftar Buku          │
│                                         │
│  📋 Peminjaman                          │
│     Perpustakaan → Peminjaman           │
│                                         │
│  💰 Denda                               │
│     Perpustakaan → Denda                │
│                                         │
│  ⌨️ Tekan Enter untuk membuka           │
│  ↑↓ Navigasi  •  Esc Tutup             │
│                                         │
└─────────────────────────────────────────┘
```

### 11.5 Keyboard Shortcut

| Shortcut | Aksi |
| :--- | :--- |
| `Ctrl+K` / `⌘K` | Buka pencarian menu |
| `↑` `↓` | Navigasi hasil |
| `Enter` | Buka menu yang dipilih |
| `Esc` | Tutup pencarian |

---

## 12. Favorite Menu

### 12.1 Fitur Favorit

User dapat menandai menu yang sering digunakan sebagai favorit untuk akses cepat.

### 12.2 Operasi Favorit

| Operasi | Deskripsi | Mekanisme |
| :--- | :--- | :--- |
| **Tambah Favorit** | User menandai menu sebagai favorit | Klik ikon bintang / tombol favorit |
| **Hapus Favorit** | User menghapus menu dari favorit | Klik ikon bintang lagi (toggle) |
| **Urutkan Favorit** | User mengubah urutan favorit | Drag & drop di panel favorit |

### 12.3 Penyimpanan Favorit

```text
Tabel: menu_favorites

├── user_id     → FK ke users
├── menu_id     → FK ke menus
├── order       → Urutan tampil
└── created_at  → Waktu ditambahkan
```

### 12.4 Tampilan Favorit

```text
┌─────────────────────────────┐
│  ⭐ Favorit                  │
├─────────────────────────────┤
│  📚 Daftar Buku              │
│  📋 Peminjaman               │
│  📈 Laporan                  │
│  👥 Anggota                  │
│  ────────────────────────── │
│  📌 Kelola Favorit           │
└─────────────────────────────┘
```

### 12.5 Aturan Favorit

| Aturan | Penjelasan |
| :--- | :--- |
| **Limit** | Maksimal item favorit configurable (default: 10) |
| **Permission check** | Favorit yang permission-nya dicabut otomatis disembunyikan |
| **Module check** | Favorit dari module nonaktif otomatis disembunyikan |
| **Per-user** | Setiap user memiliki daftar favorit sendiri |
| **Persistent** | Favorit disimpan di database, tersedia di semua perangkat |

---

## 13. Recent Menu

### 13.1 Penyimpanan Riwayat

Menu Engine menyimpan riwayat menu yang terakhir diakses user untuk navigasi cepat kembali ke halaman yang baru dikunjungi.

### 13.2 Mekanisme Pencatatan

```text
User mengakses halaman
    │
    ├── 1. Middleware RecordMenuVisit mendeteksi route
    │
    ├── 2. Cocokkan route dengan menu di database
    │       ├── Ditemukan → Lanjut
    │       └── Tidak ditemukan → Skip
    │
    ├── 3. Cek apakah menu sudah ada di recent user
    │       ├── Ada → Update visited_at dan increment visit_count
    │       └── Tidak ada → Insert record baru
    │
    ├── 4. Jika jumlah recent melebihi limit
    │       └── Hapus record terlama (FIFO)
    │
    └── 5. Data tersedia di sidebar "Terakhir Dikunjungi"
```

### 13.3 Konfigurasi Recent

| Setting | Default | Deskripsi |
| :--- | :--- | :--- |
| `menu.recent.max_items` | `10` | Jumlah maksimal item recent per user |
| `menu.recent.enabled` | `true` | Aktifkan/nonaktifkan fitur recent menu |
| `menu.recent.show_count` | `false` | Tampilkan jumlah kunjungan |
| `menu.recent.ttl_days` | `30` | Hapus otomatis setelah N hari tidak diakses |

### 13.4 Tampilan Recent

```text
┌─────────────────────────────┐
│  🕐 Terakhir Dikunjungi      │
├─────────────────────────────┤
│  📚 Daftar Buku      • 2m   │
│  📋 Peminjaman        • 15m  │
│  👥 Anggota           • 1j   │
│  📈 Laporan           • 3j   │
│  ⚙️ Pengaturan        • 1h   │
└─────────────────────────────┘
```

---

## 14. Dashboard Shortcut

### 14.1 Konsep Shortcut

Dashboard Shortcut adalah pintasan navigasi yang ditampilkan di halaman dashboard sebagai card atau icon grid. Shortcut dikonfigurasi oleh administrator dan dapat berbeda untuk setiap role.

### 14.2 Konfigurasi Per Role

| Role | Shortcut Default |
| :--- | :--- |
| **Super Admin** | Pengguna, Modul, Tema, Backup, Log |
| **Admin Sekolah** | Pengguna, Pengaturan, Laporan, Backup |
| **Kepala Perpustakaan** | Laporan, Peminjaman, Denda, Pengaturan |
| **Pustakawan** | Buku, Anggota, Peminjaman, Pengembalian |
| **Guru** | Katalog Buku, Laporan Baca Siswa |
| **Siswa** | Katalog Buku, Riwayat Pinjaman, Profil |

### 14.3 Penyimpanan Shortcut

```text
Tabel: menu_shortcuts

├── menu_id    → FK ke menus
├── role_id    → FK ke roles
├── order      → Urutan tampil di dashboard
└── created_at → Waktu dibuat
```

### 14.4 Tampilan Dashboard Shortcut

```text
┌──────────────────────────────────────────────────────────┐
│  🚀 Akses Cepat                                          │
├──────────────────────────────────────────────────────────┤
│                                                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │   📚     │  │   👥     │  │   📋     │  │   📈     │ │
│  │  Buku    │  │ Anggota  │  │ Pinjam   │  │ Laporan  │ │
│  │  1.250   │  │   340    │  │    28    │  │  Lihat   │ │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘ │
│                                                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │   💰     │  │   🔄     │  │   ⚙️     │  │   💾     │ │
│  │  Denda   │  │ Kembali  │  │ Setting  │  │ Backup   │ │
│  │    5     │  │    12    │  │  Kelola  │  │  Buat    │ │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘ │
│                                                           │
└──────────────────────────────────────────────────────────┘
```

### 14.5 Aturan Shortcut

| Aturan | Penjelasan |
| :--- | :--- |
| **Per role** | Shortcut berbeda untuk setiap role |
| **Permission check** | Shortcut hanya tampil jika user punya permission |
| **Admin configurable** | Administrator mengatur shortcut melalui panel admin |
| **Badge support** | Shortcut dapat menampilkan badge (jumlah) |
| **Max items** | Configurable, default 8 item per role |

---

## 15. Menu Badge

### 15.1 Konsep Badge

Badge adalah indikator visual dinamis yang menampilkan informasi kuantitatif pada item menu. Badge diperbarui secara otomatis dan memberikan informasi real-time tentang jumlah item yang membutuhkan perhatian.

### 15.2 Jenis Badge

| Jenis | Deskripsi | Contoh |
| :--- | :--- | :--- |
| **Count** | Jumlah item | Peminjaman: `28` |
| **Alert** | Indikator peringatan | Terlambat: `!` |
| **Status** | Indikator status | Aktif: `●` |
| **New** | Indikator item baru | Buku Baru: `NEW` |

### 15.3 Contoh Badge Dinamis

| Menu | Badge | Sumber Data | Warna |
| :--- | :--- | :--- | :--- |
| Peminjaman | `28` | Jumlah peminjaman aktif | `primary` |
| Notifikasi | `5` | Jumlah notifikasi belum dibaca | `danger` |
| Pengunjung | `142` | Jumlah pengunjung hari ini | `info` |
| Approval | `3` | Jumlah approval menunggu | `warning` |
| Terlambat | `7` | Jumlah peminjaman terlambat | `danger` |
| Buku Baru | `NEW` | Ada buku baru ditambahkan | `success` |

### 15.4 Konfigurasi Badge

```text
Badge Configuration (dalam menu.php):

'badge' => [
    'type'        => 'count',                  // count | alert | status | new
    'source'      => 'library.loans.pending',  // service method reference
    'color'       => 'warning',                // primary | success | warning | danger | info
    'max_display' => 99,                       // Tampilkan "99+" jika melebihi
    'refresh'     => 'page_load',              // page_load | interval | realtime
    'interval'    => 60,                       // Interval refresh dalam detik
],
```

### 15.5 Badge Service

Badge mendapatkan data dari **Badge Service** yang terdaftar di module:

```text
Badge Resolution:

1. Menu memiliki badge.source = "library.loans.pending"

2. Badge Service mencari method yang terdaftar:
   └── LibraryBadgeService::getPendingLoansCount()

3. Method mengembalikan integer atau null

4. Jika null → badge tidak ditampilkan
   Jika 0   → badge tidak ditampilkan
   Jika > 0 → badge ditampilkan dengan value

5. Badge di-cache per role (TTL: configurable)
```

---

## 16. Responsive Navigation

### 16.1 Breakpoint Navigasi

Menu Engine mendukung tampilan responsif untuk berbagai ukuran layar:

| Breakpoint | Lebar | Tipe Navigasi | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Desktop** | ≥ 1280px | Full Sidebar | Sidebar terbuka penuh dengan label teks |
| **Desktop Collapsed** | ≥ 1024px | Collapsed Sidebar | Sidebar hanya menampilkan ikon |
| **Tablet** | 768px – 1023px | Overlay Sidebar | Sidebar muncul sebagai overlay saat diklik |
| **Mobile** | < 768px | Mobile Drawer | Navigation drawer dari sisi kiri |
| **Bottom Nav (Future)** | < 768px | Bottom Navigation | Tab bar di bagian bawah layar |

### 16.2 Desktop Sidebar

```text
┌────────────────────────────────────────────────────────┐
│  Navbar Top                                 [🔍] [🔔] [👤] │
├──────────────┬─────────────────────────────────────────┤
│              │                                         │
│  ⭐ Favorit   │                                         │
│  ──────────  │                                         │
│  📚 Buku     │          Konten Halaman                 │
│  📋 Pinjam   │                                         │
│              │                                         │
│  📦 PERPUS   │                                         │
│  ──────────  │                                         │
│  📊 Dashboard│                                         │
│  📚 Buku     │                                         │
│  👥 Anggota  │                                         │
│  📋 Pinjaman │                                         │
│  💰 Denda    │                                         │
│  📈 Laporan  │                                         │
│  ⚙️ Setting  │                                         │
│              │                                         │
│  ⚙️ SISTEM   │                                         │
│  ──────────  │                                         │
│  🔧 Setting  │                                         │
│  👤 User     │                                         │
│              │                                         │
│  🕐 Terakhir │                                         │
│  ──────────  │                                         │
│  📚 Buku     │                                         │
│  📋 Pinjam   │                                         │
│              │                                         │
├──────────────┴─────────────────────────────────────────┤
│  Footer                                                 │
└────────────────────────────────────────────────────────┘
```

### 16.3 Collapsed Sidebar

```text
┌──────────────────────────────────────────┐
│  Navbar Top                    [🔍][🔔][👤]│
├─────┬────────────────────────────────────┤
│     │                                    │
│ ⭐  │                                    │
│ ──  │        Konten Halaman              │
│ 📚  │                                    │
│ 📋  │                                    │
│     │                                    │
│ 📦  │                                    │
│ ──  │                                    │
│ 📊  │      (Hover untuk expand           │
│ 📚  │       submenu tooltip)             │
│ 👥  │                                    │
│ 📋  │                                    │
│ 💰  │                                    │
│ 📈  │                                    │
│ ⚙️  │                                    │
│     │                                    │
│ ⚙️  │                                    │
│ ──  │                                    │
│ 🔧  │                                    │
│ 👤  │                                    │
│     │                                    │
├─────┴────────────────────────────────────┤
│  Footer                                  │
└──────────────────────────────────────────┘
```

### 16.4 Mobile Drawer

```text
┌─────────────────────┐
│  ☰  CosmicLib  [✕]  │
├─────────────────────┤
│                     │
│  🔍 Cari menu...    │
│                     │
│  ⭐ Favorit          │
│  ───────────────── │
│  📚 Buku            │
│  📋 Pinjam          │
│                     │
│  📦 PERPUSTAKAAN    │
│  ───────────────── │
│  📊 Dashboard       │
│  📚 Daftar Buku     │
│  👥 Anggota         │
│  📋 Peminjaman  (5) │
│  💰 Denda           │
│  📈 Laporan         │
│                     │
│  ⚙️ SISTEM           │
│  ───────────────── │
│  🔧 Pengaturan      │
│  👤 Pengguna        │
│                     │
│  ───────────────── │
│  👤 Profil Saya     │
│  🚪 Keluar          │
│                     │
└─────────────────────┘
```

### 16.5 Bottom Navigation (Future)

```text
┌─────────────────────────────────┐
│                                 │
│         Konten Halaman          │
│                                 │
│                                 │
├─────────────────────────────────┤
│  📊    📚    📋    🔔    ☰     │
│ Home  Buku  Pinjam Notif Menu  │
└─────────────────────────────────┘
```

---

## 17. Menu Configuration

### 17.1 Sumber Konfigurasi

Konfigurasi Menu Engine berasal dari empat sumber dengan hierarki prioritas:

| Prioritas | Sumber | Deskripsi |
| :--- | :--- | :--- |
| 1 (Tertinggi) | **Database** (Setting Engine) | Konfigurasi runtime yang diubah admin |
| 2 | **System Settings** (`config/cosmiclib/menu.php`) | Override di level sistem |
| 3 | **Theme Configuration** (`theme.json > menus`) | Konfigurasi tampilan dari tema |
| 4 (Terendah) | **Module `menu.php`** | Definisi default dari module |

### 17.2 Konfigurasi Sistem Menu

| Key | Tipe | Default | Deskripsi |
| :--- | :--- | :--- | :--- |
| `menu.sidebar.style` | `string` | `default` | Gaya sidebar: `default`, `collapsed`, `mini`, `hidden` |
| `menu.sidebar.position` | `string` | `left` | Posisi sidebar: `left`, `right` |
| `menu.sidebar.theme` | `string` | `dark` | Tema sidebar: `dark`, `light`, `gradient` |
| `menu.topbar.enabled` | `boolean` | `true` | Aktifkan menu di topbar |
| `menu.topbar.style` | `string` | `default` | Gaya topbar: `default`, `sticky`, `floating` |
| `menu.search.enabled` | `boolean` | `true` | Aktifkan fitur pencarian menu |
| `menu.search.shortcut` | `string` | `Ctrl+K` | Keyboard shortcut pencarian |
| `menu.favorites.enabled` | `boolean` | `true` | Aktifkan fitur menu favorit |
| `menu.favorites.max_items` | `integer` | `10` | Jumlah maksimal item favorit |
| `menu.recent.enabled` | `boolean` | `true` | Aktifkan fitur menu recent |
| `menu.recent.max_items` | `integer` | `10` | Jumlah maksimal item recent |
| `menu.badge.enabled` | `boolean` | `true` | Aktifkan badge dinamis |
| `menu.badge.cache_ttl` | `integer` | `300` | Cache badge selama N detik |
| `menu.cache.enabled` | `boolean` | `true` | Aktifkan cache menu |
| `menu.cache.ttl` | `integer` | `3600` | Cache menu selama N detik |
| `menu.max_depth` | `integer` | `3` | Kedalaman maksimal hierarki |

### 17.3 Larangan Hardcode

```text
┌─────────────────────────────────────────────────────────┐
│              ❌ YANG DILARANG                             │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ❌ Menulis menu langsung di file Blade                  │
│  ❌ Menulis menu di controller                           │
│  ❌ Menulis menu di file konfigurasi global              │
│  ❌ Hardcode route menu                                  │
│  ❌ Hardcode permission di menu rendering                │
│  ❌ Hardcode ikon atau warna di komponen navigasi        │
│  ❌ Menulis menu di JavaScript statis                    │
│                                                          │
├─────────────────────────────────────────────────────────┤
│              ✅ YANG DIWAJIBKAN                           │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ✅ Definisikan menu di Config/menu.php per module       │
│  ✅ Gunakan named route                                  │
│  ✅ Gunakan permission slug dari Permission Engine       │
│  ✅ Gunakan ikon dari icon library yang terdaftar        │
│  ✅ Biarkan Theme Engine menangani rendering             │
│  ✅ Biarkan Menu Engine menangani caching                │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 18. Menu Cache

### 18.1 Strategi Caching

Menu Engine menggunakan strategi caching berlapis untuk performa optimal:

```text
Cache Layer
    │
    ├── Layer 1: Menu Tree Cache (per role)
    │       └── Cache seluruh menu tree yang sudah difilter
    │           Key: "menu:tree:role:{role_id}"
    │           TTL: configurable (default: 3600 detik)
    │
    ├── Layer 2: Badge Cache (per role)
    │       └── Cache nilai badge untuk setiap menu
    │           Key: "menu:badge:role:{role_id}"
    │           TTL: configurable (default: 300 detik)
    │
    └── Layer 3: User Preference Cache (per user)
            └── Cache favorit dan konfigurasi user
                Key: "menu:user:{user_id}"
                TTL: configurable (default: 3600 detik)
```

### 18.2 Auto Refresh

Cache menu otomatis di-invalidasi saat terjadi perubahan berikut:

| Event | Cache yang Di-invalidasi |
| :--- | :--- |
| **Module berubah** (enable/disable/install/remove) | Seluruh cache menu tree |
| **Permission berubah** (role permission update) | Cache menu tree per role yang berubah |
| **Theme berubah** (activate/switch theme) | Seluruh cache menu tree |
| **Role berubah** (role create/update/delete) | Cache menu tree role terkait |
| **Menu diubah admin** (order, visibility, badge) | Seluruh cache menu tree |
| **User favorit berubah** | Cache user preference yang berubah |

### 18.3 Cache Invalidation Flow

```text
Event: ModuleEnabled('library')
    │
    ├── 1. Menu Engine menerima event
    │
    ├── 2. Registrasi menu baru dari module
    │
    ├── 3. Sinkronisasi ke database
    │
    ├── 4. Invalidasi cache:
    │       ├── Cache::forget('menu:tree:role:*')
    │       ├── Cache::forget('menu:badge:role:*')
    │       └── Log: "Menu cache invalidated: module library enabled"
    │
    └── 5. Cache dibangun ulang pada request berikutnya (lazy)
```

### 18.4 Artisan Command

| Command | Fungsi |
| :--- | :--- |
| `menu:cache` | Bangun dan simpan cache menu untuk semua role |
| `menu:cache-clear` | Hapus seluruh cache menu |
| `menu:sync` | Sinkronisasi menu dari semua module ke database |
| `menu:list` | Tampilkan daftar menu terdaftar |

---

## 19. Menu Security

### 19.1 Lapisan Keamanan

Menu Engine menerapkan empat lapisan keamanan:

```text
┌──────────────────────────────────────────────────┐
│              MENU SECURITY LAYERS                 │
├──────────────────────────────────────────────────┤
│                                                    │
│  Layer 1: PERMISSION VALIDATION                    │
│  ─────────────────────────────                    │
│  Setiap menu dicek permission-nya via              │
│  Permission Engine sebelum ditampilkan              │
│                                                    │
│  Layer 2: ROUTE VALIDATION                         │
│  ─────────────────────────────                    │
│  Menu hanya mengarah ke route yang terdaftar        │
│  Route yang tidak valid → menu diabaikan           │
│                                                    │
│  Layer 3: VISIBILITY CHECK                         │
│  ─────────────────────────────                    │
│  Flag is_visible dan is_active diperiksa           │
│  Module nonaktif → menu hidden                     │
│                                                    │
│  Layer 4: ACTIVE STATE VALIDATION                  │
│  ─────────────────────────────                    │
│  Menu hanya aktif jika module-nya aktif            │
│  Orphan menu (module dihapus) → auto hidden        │
│                                                    │
└──────────────────────────────────────────────────┘
```

### 19.2 Proteksi Terhadap Manipulasi

| Ancaman | Proteksi |
| :--- | :--- |
| **URL manipulation** | Route middleware tetap mengecek permission di server-side |
| **Menu tampil tanpa izin** | Double-check: Menu Engine + Middleware Authorization |
| **Direct access bypass** | Permission check di route middleware, bukan hanya di menu |
| **Cache poisoning** | Cache key mengandung role ID, tidak bisa diakses cross-role |
| **SQL injection via search** | Query parameterized, input sanitized |
| **XSS via menu title** | Semua output di-escape oleh Blade engine |

### 19.3 Prinsip Keamanan Menu

> **Menyembunyikan menu BUKAN pengganti otorisasi.**
>
> Menu Engine menyembunyikan navigasi untuk UX yang bersih. Namun, proteksi sebenarnya ada di:
> - **Route Middleware** — mengecek permission di level HTTP
> - **Policy** — mengecek otorisasi di level model
> - **Service Layer** — mengecek business rules
>
> Jika user mengakses URL langsung (bypass menu), tetap ada pengecekan akses di server.

---

## 20. Future Features

### 20.1 Fitur yang Direncanakan

| Fitur | Deskripsi | Fase |
| :--- | :--- | :--- |
| **Mega Menu** | Menu dropdown besar dengan multiple kolom dan konten kaya | v2.0 |
| **Menu Builder** | Panel admin untuk membangun navigasi kustom secara visual | v2.0 |
| **Drag & Drop Menu** | Antarmuka drag & drop untuk mengatur hierarki dan urutan | v2.0 |
| **Context Menu** | Right-click menu kontekstual pada halaman tertentu | v2.5 |
| **Workspace Menu** | Menu berbeda per workspace (multi-sekolah) | v3.0 |
| **Bookmark Menu** | User menyimpan halaman spesifik (bukan hanya menu) | v2.0 |
| **Multi School Menu** | Navigasi antar sekolah dalam satu instalasi multi-tenant | v3.0 |
| **Bottom Navigation** | Tab bar navigasi di bagian bawah layar mobile | v2.0 |
| **Breadcrumb Auto** | Breadcrumb otomatis dari hierarki menu | v1.5 |
| **Menu Analytics** | Statistik penggunaan menu (menu mana paling sering diakses) | v2.0 |

### 20.2 Mega Menu (Future)

```text
┌─────────────────────────────────────────────────────────────┐
│  📦 Perpustakaan ▼                                           │
├─────────────────┬───────────────┬───────────────────────────┤
│  📚 Koleksi     │  📋 Sirkulasi │  📈 Laporan               │
│  ──────────    │  ────────── │  ──────────               │
│  Daftar Buku   │  Peminjaman  │  Laporan Harian           │
│  Kategori      │  Pengembalian│  Laporan Bulanan          │
│  Penerbit      │  Perpanjang  │  Statistik                │
│  Penulis       │  Denda       │  Export                   │
│                │              │                           │
│  [+ Tambah     │  [+ Pinjam   │  [📊 Lihat Semua]        │
│     Buku]      │     Buku]    │                           │
└─────────────────┴───────────────┴───────────────────────────┘
```

### 20.3 Menu Builder (Future)

```text
┌───────────────────────────────────────────────────────────┐
│  🔧 Menu Builder                          [Simpan] [Reset] │
├────────────────────────┬──────────────────────────────────┤
│  Menu Tersedia         │  Struktur Menu Aktif              │
│                        │                                   │
│  🔍 Cari menu...       │  📦 Perpustakaan                  │
│                        │  ├── 📊 Dashboard                 │
│  ☐ Dashboard           │  ├── 📚 Daftar Buku   [↑][↓][✕]  │
│  ☐ Profil              │  ├── 👥 Anggota       [↑][↓][✕]  │
│  ☐ Pengaturan          │  ├── 📋 Peminjaman    [↑][↓][✕]  │
│  ☐ Laporan Keuangan    │  └── 📈 Laporan       [↑][↓][✕]  │
│  ☐ Audit Log           │                                   │
│                        │  ⚙️ Sistem                         │
│  [+ Menu Kustom]       │  ├── 🔧 Pengaturan    [↑][↓][✕]  │
│                        │  └── 👤 Pengguna      [↑][↓][✕]  │
│                        │                                   │
│                        │  [Drag item untuk mengubah urutan] │
└────────────────────────┴──────────────────────────────────┘
```

---

## 21. AI Rules

### 21.1 Aturan Wajib untuk AI Agent

Semua AI agent (Claude, Codex, ChatGPT, Cline, Gemini, dan lainnya) **WAJIB** mematuhi aturan berikut saat bekerja dengan Menu Engine:

| # | Aturan | Penjelasan |
| :--- | :--- | :--- |
| 1 | **Tidak hardcode menu** | Dilarang menulis menu langsung di Blade, controller, atau view |
| 2 | **Semua menu berasal dari Module** | Setiap menu harus didefinisikan di `Config/menu.php` milik module |
| 3 | **Selalu buat `menu.php`** | Saat membuat module baru, wajib menyertakan file `Config/menu.php` |
| 4 | **Selalu gunakan Permission Engine** | Setiap menu item wajib memiliki `permission` yang mereferensi Permission Engine |
| 5 | **Selalu mengikuti Theme Engine** | Tidak boleh membuat komponen navigasi custom di luar Theme Engine |
| 6 | **Gunakan named route** | Property `route` harus berisi named route Laravel, bukan URL path |
| 7 | **Gunakan naming convention** | Slug menu menggunakan format `{module}.{resource}` |
| 8 | **Gunakan ikon dari library** | Ikon harus dari icon library resmi (Lucide, Heroicons), bukan hardcode SVG |
| 9 | **Definisikan order** | Setiap menu item wajib memiliki `order` untuk urutan yang konsisten |
| 10 | **Jangan duplikasi menu** | Satu route hanya boleh dipetakan ke satu menu item |

### 21.2 Checklist AI Saat Membuat Module

```text
Saat AI membuat module baru, pastikan:

☐ File Config/menu.php dibuat
☐ Setiap menu item memiliki slug unik
☐ Setiap menu item memiliki title (Bahasa Indonesia)
☐ Setiap menu item memiliki icon
☐ Setiap menu item memiliki route (named route)
☐ Setiap menu item memiliki permission
☐ Setiap menu item memiliki order
☐ Hierarki parent-child benar
☐ Badge dikonfigurasi jika diperlukan
☐ Menu diuji dengan berbagai role
```

### 21.3 Contoh Kesalahan Umum AI

| Kesalahan | Contoh | Koreksi |
| :--- | :--- | :--- |
| Hardcode menu di Blade | `<li><a href="/books">Buku</a></li>` | Gunakan komponen Theme yang membaca Menu Engine |
| Hardcode permission check | `@if(auth()->user()->role === 'admin')` | Gunakan `@can('library.books.view')` atau Menu Engine filter |
| Tidak buat `menu.php` | Module tanpa file `Config/menu.php` | Selalu sertakan `Config/menu.php` |
| URL path di route | `'route' => '/library/books'` | Gunakan `'route' => 'library.books.index'` |
| Slug tidak konsisten | `'slug' => 'buku-perpustakaan'` | Gunakan `'slug' => 'library.books'` |

---

## 22. Best Practice

### 22.1 Prinsip Navigasi Terbaik

| Prinsip | Penjelasan |
| :--- | :--- |
| **Dynamic Navigation** | Seluruh menu dibangun secara dinamis dari module, bukan hardcode |
| **Permission First** | Setiap menu wajib memiliki permission — security by default |
| **Configuration over Hardcode** | Konfigurasi melalui Setting Engine, bukan hardcode di kode |
| **Cache Navigation** | Cache menu tree untuk performa — invalidasi saat ada perubahan |
| **Responsive Design** | Navigasi bekerja optimal di desktop, tablet, dan mobile |
| **Accessibility (WCAG)** | Menu harus dapat dinavigasi via keyboard dan screen reader |

### 22.2 Accessibility Standards

| Standar | Implementasi |
| :--- | :--- |
| **Keyboard Navigation** | Semua menu bisa diakses via Tab, Enter, Escape, Arrow keys |
| **ARIA Labels** | Setiap menu item memiliki `aria-label` deskriptif |
| **ARIA Expanded** | Menu yang bisa di-expand memiliki `aria-expanded` state |
| **Focus Visible** | Indikator fokus yang jelas saat navigasi via keyboard |
| **Screen Reader** | Struktur heading dan landmark yang semantik |
| **Color Contrast** | Rasio kontras minimal 4.5:1 untuk teks menu |
| **Skip Navigation** | Link "Langsung ke konten" di awal halaman |

### 22.3 Performance Best Practice

| Praktik | Detail |
| :--- | :--- |
| **Cache per role** | Satu cache entry per role, bukan per user |
| **Lazy load badge** | Badge dimuat setelah menu dirender (async) |
| **Minimal query** | Satu query untuk seluruh menu tree, bukan N+1 |
| **Eager load children** | Load hierarki menu dalam satu query dengan eager loading |
| **CDN icon** | Ikon dimuat dari CDN atau sprite, bukan inline SVG per item |

### 22.4 UX Best Practice

| Praktik | Detail |
| :--- | :--- |
| **Konsistensi ikon** | Gunakan satu icon library secara konsisten |
| **Maksimal 3 level** | Nesting lebih dari 3 level membingungkan user |
| **Group yang jelas** | Kelompok menu harus memiliki label yang deskriptif |
| **Active state** | Menu yang sedang aktif harus terlihat jelas |
| **Breadcrumb** | Sertakan breadcrumb untuk konteks lokasi user |
| **Loading state** | Tampilkan skeleton saat menu dimuat |

---

## 23. Architecture Diagram

### 23.1 Alur Lengkap Menu Engine

```text
┌─────────────────────────────────────────────────────────────────┐
│                    MENU ENGINE ARCHITECTURE                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   │
│   │ Module A │   │ Module B │   │ Module C │   │ Module N │   │
│   │ menu.php │   │ menu.php │   │ menu.php │   │ menu.php │   │
│   └────┬─────┘   └────┬─────┘   └────┬─────┘   └────┬─────┘   │
│        │               │               │               │         │
│        └───────────────┴───────┬───────┴───────────────┘         │
│                                │                                  │
│                                ▼                                  │
│                    ┌───────────────────┐                          │
│                    │    MENU LOADER    │                          │
│                    │  (Discovery &     │                          │
│                    │   Parse menu.php) │                          │
│                    └─────────┬─────────┘                          │
│                              │                                    │
│                              ▼                                    │
│                    ┌───────────────────┐                          │
│                    │    VALIDATOR      │                          │
│                    │  (Slug, Route,    │                          │
│                    │   Permission)     │                          │
│                    └─────────┬─────────┘                          │
│                              │                                    │
│                              ▼                                    │
│                    ┌───────────────────┐                          │
│                    │    DATABASE       │                          │
│                    │  (tabel menus)    │                          │
│                    └─────────┬─────────┘                          │
│                              │                                    │
│                    ┌─────────┴─────────┐                          │
│                    │                   │                          │
│                    ▼                   ▼                          │
│         ┌──────────────────┐ ┌──────────────────┐                │
│         │ PERMISSION ENGINE│ │   CACHE ENGINE   │                │
│         │ (Filter by role) │ │ (Per-role cache) │                │
│         └────────┬─────────┘ └────────┬─────────┘                │
│                  │                    │                           │
│                  └────────┬───────────┘                           │
│                           │                                       │
│                           ▼                                       │
│                ┌───────────────────┐                              │
│                │   THEME ENGINE    │                              │
│                │  (Render layout)  │                              │
│                └─────────┬─────────┘                              │
│                          │                                        │
│              ┌───────────┼───────────┐                            │
│              │           │           │                            │
│              ▼           ▼           ▼                            │
│       ┌──────────┐ ┌──────────┐ ┌──────────┐                    │
│       │ SIDEBAR  │ │  TOPBAR  │ │  MOBILE  │                    │
│       │          │ │          │ │  DRAWER  │                    │
│       └──────────┘ └──────────┘ └──────────┘                    │
│              │           │           │                            │
│              └───────────┼───────────┘                            │
│                          │                                        │
│                          ▼                                        │
│               ┌───────────────────┐                               │
│               │  USER INTERFACE   │                               │
│               │  (Final rendered  │                               │
│               │   navigation)    │                               │
│               └───────────────────┘                               │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### 23.2 Diagram Interaksi Engine

```text
┌──────────────┐
│ Module Engine │  ← Sumber registrasi menu (menu.php)
└──────┬───────┘
       │ meregistrasikan
       ▼
┌──────────────┐
│  Menu Engine  │  ← Inti navigasi: load, validate, store, cache
└──┬───┬───┬───┘
   │   │   │
   │   │   └──────────────┐
   │   │                  │
   ▼   ▼                  ▼
┌──────────┐ ┌───────────────┐ ┌──────────────┐
│Permission│ │ Setting Engine │ │ Theme Engine  │
│ Engine   │ │               │ │              │
│(filter)  │ │(configuration)│ │(render)      │
└──────────┘ └───────────────┘ └──────────────┘
```

---

## 24. Menu Naming Convention

### 24.1 Format Penamaan

Slug menu menggunakan format **dot notation** yang konsisten dan hierarkis:

```text
Format:
    {module}.{resource}
    {module}.{resource}.{sub-resource}

Aturan:
    - Semua huruf kecil (lowercase)
    - Pemisah: titik (dot)
    - Bahasa Inggris
    - Singkat dan deskriptif
    - Tidak mengandung spasi atau karakter khusus
```

### 24.2 Contoh Naming Convention

| Module | Menu Slug | Title (Bahasa Indonesia) |
| :--- | :--- | :--- |
| **Library** | `library.dashboard` | Dashboard Perpustakaan |
| **Library** | `library.books` | Daftar Buku |
| **Library** | `library.books.categories` | Kategori Buku |
| **Library** | `library.members` | Daftar Anggota |
| **Library** | `library.loans` | Peminjaman |
| **Library** | `library.returns` | Pengembalian |
| **Library** | `library.fines` | Denda |
| **Library** | `library.reports` | Laporan |
| **Library** | `library.settings` | Pengaturan Perpustakaan |
| **CMS** | `cms.dashboard` | Dashboard Konten |
| **CMS** | `cms.posts` | Artikel |
| **CMS** | `cms.pages` | Halaman |
| **CMS** | `cms.media` | Media |
| **CMS** | `cms.categories` | Kategori Konten |
| **System** | `system.dashboard` | Dashboard Sistem |
| **System** | `system.settings` | Pengaturan Umum |
| **System** | `system.users` | Manajemen Pengguna |
| **System** | `system.roles` | Role & Permission |
| **System** | `system.backup` | Backup |
| **System** | `system.update` | Pembaruan Sistem |
| **System** | `system.logs` | Log Aktivitas |

### 24.3 Anti-pattern Penamaan

| ❌ Salah | ✅ Benar | Alasan |
| :--- | :--- | :--- |
| `daftar-buku` | `library.books` | Gunakan dot notation, bukan kebab |
| `BookList` | `library.books` | Gunakan lowercase |
| `perpustakaan.buku` | `library.books` | Gunakan Bahasa Inggris |
| `lib_books` | `library.books` | Gunakan nama module lengkap |
| `menu_library_books` | `library.books` | Tidak perlu prefix "menu" |
| `library-books-index` | `library.books` | Slug menu ≠ slug route |

---

## 25. Menu Matrix

### 25.1 Contoh Menu Matrix — Module Library

| Module | Menu | Route | Permission | Roles | Visibility |
| :--- | :--- | :--- | :--- | :--- | :--- |
| Library | Dashboard | `library.dashboard` | `library.books.view` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Daftar Buku | `library.books.index` | `library.books.view` | Super Admin, Admin, Pustakawan, Guru, Siswa | ✅ Visible |
| Library | Anggota | `library.members.index` | `library.members.view` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Peminjaman | `library.loans.index` | `library.loans.view` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Pengembalian | `library.returns.index` | `library.loans.return` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Denda | `library.fines.index` | `library.fines.view` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Kategori | `library.categories.index` | `library.categories.view` | Super Admin, Admin, Pustakawan | ✅ Visible |
| Library | Laporan | `library.reports.index` | `library.reports.view` | Super Admin, Admin, Kepala Perpustakaan | ✅ Visible |
| Library | Pengaturan | `library.settings.index` | `library.settings.manage` | Super Admin, Admin | ✅ Visible |

### 25.2 Contoh Menu Matrix — Module System

| Module | Menu | Route | Permission | Roles | Visibility |
| :--- | :--- | :--- | :--- | :--- | :--- |
| System | Pengaturan Umum | `system.settings.index` | `system.settings.view` | Super Admin | ✅ Visible |
| System | Manajemen Pengguna | `system.users.index` | `system.users.view` | Super Admin, Admin | ✅ Visible |
| System | Role & Permission | `system.roles.index` | `system.roles.view` | Super Admin | ✅ Visible |
| System | Backup | `system.backup.index` | `system.backup.view` | Super Admin | ✅ Visible |
| System | Pembaruan Sistem | `system.update.index` | `system.update.view` | Super Admin | ✅ Visible |
| System | Log Aktivitas | `system.logs.index` | `system.logs.view` | Super Admin | ✅ Visible |

### 25.3 Contoh Menu Matrix — Module CMS

| Module | Menu | Route | Permission | Roles | Visibility |
| :--- | :--- | :--- | :--- | :--- | :--- |
| CMS | Artikel | `cms.posts.index` | `cms.posts.view` | Super Admin, Admin, Editor | ✅ Visible |
| CMS | Halaman | `cms.pages.index` | `cms.pages.view` | Super Admin, Admin, Editor | ✅ Visible |
| CMS | Pengumuman | `cms.announcements.index` | `cms.announcements.view` | Super Admin, Admin | ✅ Visible |
| CMS | Media | `cms.media.index` | `cms.media.view` | Super Admin, Admin, Editor | ✅ Visible |

---

## 26. Checklist

### 26.1 Checklist Implementasi Menu Engine

| # | Item | Status | Deskripsi |
| :--- | :--- | :--- | :--- |
| 1 | `menu.php` | ✅ | Setiap module memiliki file `Config/menu.php` |
| 2 | Permission | ✅ | Setiap menu item memiliki permission dari Permission Engine |
| 3 | Route | ✅ | Setiap menu item menggunakan named route |
| 4 | Icon | ✅ | Setiap menu item memiliki ikon dari icon library resmi |
| 5 | Order | ✅ | Setiap menu item memiliki order untuk urutan yang konsisten |
| 6 | Parent | ✅ | Hierarki parent-child terdefinisi dengan benar |
| 7 | Badge | ✅ | Badge dinamis dikonfigurasi untuk menu yang membutuhkan |
| 8 | Cache | ✅ | Strategi caching per-role terdefinisi |
| 9 | Responsive | ✅ | Navigasi bekerja di desktop, tablet, dan mobile |
| 10 | Documentation | ✅ | Spesifikasi Menu Engine terdokumentasi lengkap |

### 26.2 Checklist Validasi Per Module

```text
Untuk setiap module yang dibuat, validasi:

✅ File Config/menu.php ada dan valid
✅ Semua slug mengikuti naming convention: {module}.{resource}
✅ Semua title menggunakan Bahasa Indonesia
✅ Semua route menggunakan named route yang terdaftar
✅ Semua permission terdaftar di Permission Engine
✅ Semua icon menggunakan nama dari icon library resmi
✅ Order tidak konflik dalam parent yang sama
✅ Hierarki parent-child tidak circular
✅ Badge source terdaftar di Badge Service
✅ Menu dapat diakses sesuai role dan permission
```

### 26.3 Checklist Keamanan Menu

```text
✅ Setiap menu memiliki permission check
✅ Route middleware tetap memvalidasi akses (server-side)
✅ Menu hidden bukan pengganti authorization
✅ Input pencarian menu di-sanitize
✅ Cache key terisolasi per role
✅ Output menu di-escape dari XSS
✅ Orphan menu (module dihapus) otomatis disembunyikan
```

---

## 📎 Referensi Terkait

| Dokumen | Relasi dengan Menu Engine |
| :--- | :--- |
| [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md) | Boot sequence dan loading order engine |
| [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md) | Sumber registrasi menu via `menu.php` |
| [`09_THEME_ENGINE.md`](09_THEME_ENGINE.md) | Rendering visual navigasi (sidebar, topbar, mobile) |
| [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md) | Filter visibilitas menu berdasarkan hak akses |
| [`16_SETTING_ENGINE.md`](16_SETTING_ENGINE.md) | Konfigurasi runtime menu |
| [`25_LIBRARY_MODULE.md`](25_LIBRARY_MODULE.md) | Contoh implementasi menu di module perpustakaan |

---

> **CosmicLib Engine v1.0** — Menu Engine Specification
> Dokumen ini adalah standar resmi untuk seluruh implementasi navigasi dalam ekosistem CosmicLib.
> Seluruh module, theme, dan AI agent wajib mematuhi spesifikasi ini.
