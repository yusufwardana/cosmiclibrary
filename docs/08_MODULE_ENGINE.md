# 08 — Module Engine

> **CosmicLib Engine v1.0**
> Dokumen Spesifikasi Resmi — Module Engine
> Terakhir diperbarui: 2026-07-18

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi](#2-filosofi)
3. [Module Lifecycle](#3-module-lifecycle)
4. [Module Structure](#4-module-structure)
5. [module.json](#5-modulejson)
6. [Module Loader](#6-module-loader)
7. [Module Registration](#7-module-registration)
8. [Dependency Management](#8-dependency-management)
9. [Module Versioning](#9-module-versioning)
10. [Module Installer](#10-module-installer)
11. [Module Configuration](#11-module-configuration)
12. [Module Permission](#12-module-permission)
13. [Module Menu](#13-module-menu)
14. [Module Assets](#14-module-assets)
15. [Module Database](#15-module-database)
16. [Module Communication](#16-module-communication)
17. [Module Event](#17-module-event)
18. [Module Update](#18-module-update)
19. [Module Security](#19-module-security)
20. [Module Marketplace (Future)](#20-module-marketplace-future)
21. [Default Modules](#21-default-modules)
22. [AI Rules](#22-ai-rules)
23. [Best Practice](#23-best-practice)
24. [Architecture Diagram](#24-architecture-diagram)
25. [Checklist](#25-checklist)

---

## 1. Pendahuluan

### 1.1. Apa itu Module Engine?

Module Engine adalah **fondasi arsitektur** seluruh fitur CosmicLib. Ia bertanggung jawab atas penemuan, registrasi, pemuatan, pengaktifan, pembaruan, dan pencabutan seluruh modul dalam ekosistem CosmicLib Engine.

Setiap fitur aplikasi — tanpa kecuali — **wajib dibangun sebagai Module**. Core Engine hanya menyediakan infrastruktur dasar (bootstrapping, service container, module loader, engine contracts) dan **tidak pernah berisi Business Logic**.

### 1.2. Mengapa Semua Fitur Harus Berbentuk Module?

| Alasan | Penjelasan |
|---|---|
| **Isolasi** | Setiap modul memiliki domain sendiri — perubahan pada satu modul tidak merusak modul lain. |
| **Reusability** | Modul dapat digunakan ulang di proyek CosmicLib lain tanpa modifikasi. |
| **Scalability** | Fitur dapat ditambah atau dikurangi tanpa menyentuh Core Engine. |
| **Maintainability** | Tim dapat mengerjakan modul secara paralel dan independen. |
| **Testability** | Setiap modul dapat diuji secara terpisah dengan test suite-nya sendiri. |
| **Deployability** | Modul dapat di-deploy, di-enable, atau di-disable per instalasi. |
| **Marketplace Ready** | Modul dapat didistribusikan secara independen melalui marketplace. |
| **AI Friendly** | AI agent dapat memahami batasan dan kontrak setiap modul secara deterministik. |

### 1.3. Prinsip Utama

```
┌─────────────────────────────────────────────┐
│           PRINSIP MODULE ENGINE              │
├─────────────────────────────────────────────┤
│  1. Modular    — Fitur = Module             │
│  2. Independent — Module berdiri sendiri    │
│  3. Extensible  — Dapat diperluas           │
│  4. Installable — Dapat dipasang/dicopot    │
│  5. Toggleable  — Enable/Disable dinamis    │
│  6. Versioned   — Semantic Versioning       │
│  7. Managed     — Dependency terkelola      │
│  8. AI Friendly — Kontrak eksplisit         │
│  9. Enterprise  — Siap skala enterprise     │
└─────────────────────────────────────────────┘
```

---

## 2. Filosofi

### 2.1. Module Adalah Aplikasi Kecil

Setiap module dalam CosmicLib diperlakukan sebagai **aplikasi kecil yang mandiri** (_self-contained mini-application_). Ia memiliki semua komponen yang diperlukan untuk beroperasi:

| Komponen | Deskripsi |
|---|---|
| **Database** | Migration, seeder, factory — skema data milik modul sendiri. |
| **Routes** | Web routes dan API routes khusus modul. |
| **Views** | Blade views atau komponen UI milik modul. |
| **Controller** | HTTP controller tipis — hanya orchestration. |
| **Service** | Business logic utama berada di Service Layer. |
| **Repository** | Data access layer — abstraksi query database. |
| **Assets** | CSS, JS, gambar, ikon, font milik modul. |
| **Permission** | Definisi permission dan policy milik modul. |
| **Menu** | Definisi menu dinamis milik modul. |
| **Config** | File konfigurasi default modul. |
| **Migration** | Skema database yang dapat di-migrate dan di-rollback. |
| **Seeder** | Data awal (seed) untuk modul. |
| **Tests** | Unit test, feature test, integration test modul. |
| **README** | Dokumentasi modul — cara install, konfigurasi, penggunaan. |

### 2.2. Module Dapat Hidup Sendiri

Sebuah module harus mampu:

- Di-install tanpa modul lain (kecuali dependency eksplisit).
- Di-test secara independen.
- Di-remove tanpa merusak Core Engine.
- Di-upgrade tanpa mempengaruhi modul lain.
- Dipindahkan ke proyek CosmicLib lain.

### 2.3. Core Engine vs Module

```
┌──────────────────────────────────────────────────┐
│                CORE ENGINE                        │
│  ┌────────────────────────────────────────────┐  │
│  │  Bootstrapping, Container, Module Loader,  │  │
│  │  Engine Contracts, Event Dispatcher,       │  │
│  │  Config Manager, Route Registrar           │  │
│  └────────────────────────────────────────────┘  │
│                     │                             │
│          ┌──────────┼──────────┐                  │
│          ▼          ▼          ▼                   │
│     ┌────────┐ ┌────────┐ ┌────────┐             │
│     │Module A│ │Module B│ │Module C│             │
│     │(Fitur) │ │(Fitur) │ │(Fitur) │             │
│     └────────┘ └────────┘ └────────┘             │
│                                                   │
│  Business Logic = 0% di Core                     │
│  Business Logic = 100% di Module                 │
└──────────────────────────────────────────────────┘
```

---

## 3. Module Lifecycle

### 3.1. Diagram Lifecycle

```
    ┌──────────┐
    │  DRAFT   │  Module dibuat / ditulis oleh developer
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │ INSTALL  │  File module ditempatkan di Modules/
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │ REGISTER │  module.json dibaca, provider didaftarkan
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │  ENABLE  │  Module diaktifkan oleh admin / artisan
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │   LOAD   │  Route, view, migration, asset dimuat
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │   RUN    │  Module beroperasi penuh di aplikasi
    └────┬─────┘
         │
    ┌────┴─────┐
    │          │
    ▼          ▼
┌────────┐ ┌──────────┐
│ UPDATE │ │ DISABLE  │  Module dinonaktifkan
└────┬───┘ └────┬─────┘
     │          │
     ▼          ▼
┌────────┐ ┌────────────┐
│  RUN   │ │ UNINSTALL  │  Module dihapus dari sistem
└────────┘ └────────────┘
```

### 3.2. Penjelasan Setiap Tahap

| Tahap | Deskripsi | Aksi Sistem |
|---|---|---|
| **Draft** | Module sedang dikembangkan oleh developer. | Belum masuk ke `Modules/`. |
| **Install** | Module ditempatkan ke direktori `Modules/`. | File dicopy, dependency diperiksa. |
| **Register** | Module Loader membaca `module.json` dan mendaftarkan Service Provider. | Provider didaftarkan ke container. |
| **Enable** | Admin atau artisan command mengaktifkan module. | Status diubah ke `enabled` di database. |
| **Load** | Core Engine memuat semua komponen module yang aktif. | Route, view, config, migration dimuat. |
| **Run** | Module beroperasi penuh — menerima request, menjalankan logic. | Semua fitur module tersedia. |
| **Update** | Versi baru module dipasang. | Migration dijalankan, cache di-clear. |
| **Disable** | Admin menonaktifkan module. | Route dan fitur module tidak lagi tersedia. |
| **Uninstall** | Module dihapus dari sistem. | Migration di-rollback (opsional), file dihapus. |

### 3.3. Status Module

| Status | Kode | Deskripsi |
|---|---|---|
| Draft | `draft` | Belum terpasang. |
| Installed | `installed` | Terpasang, belum aktif. |
| Enabled | `enabled` | Aktif dan berjalan. |
| Disabled | `disabled` | Terpasang tapi tidak aktif. |
| Error | `error` | Gagal dimuat (dependency error, dll). |
| Updating | `updating` | Sedang dalam proses update. |

---

## 4. Module Structure

### 4.1. Struktur Direktori Standar

```
Modules/
└── Library/
    ├── module.json                  # Manifest module
    ├── README.md                    # Dokumentasi module
    ├── composer.json                # Dependency PHP (opsional)
    │
    ├── Config/
    │   ├── config.php               # Konfigurasi default module
    │   ├── permission.php           # Definisi permission
    │   └── menu.php                 # Definisi menu
    │
    ├── Database/
    │   ├── Migrations/              # File migration database
    │   ├── Seeders/                 # File seeder
    │   └── Factories/              # Model factory untuk testing
    │
    ├── Http/
    │   ├── Controllers/            # HTTP Controller (tipis)
    │   ├── Middleware/             # Middleware khusus module
    │   └── Requests/              # Form Request validation
    │
    ├── Models/                     # Eloquent Model
    │
    ├── Policies/                   # Authorization Policy
    │
    ├── Providers/
    │   ├── LibraryServiceProvider.php    # Service Provider utama
    │   ├── RouteServiceProvider.php      # Route Provider
    │   └── EventServiceProvider.php      # Event Provider
    │
    ├── Repositories/
    │   ├── Contracts/              # Repository Interface
    │   └── Eloquent/               # Eloquent Implementation
    │
    ├── Resources/
    │   ├── views/                  # Blade Views
    │   ├── lang/                   # Translation files
    │   └── assets/                 # CSS, JS, Image, Font
    │       ├── css/
    │       ├── js/
    │       ├── images/
    │       └── fonts/
    │
    ├── Routes/
    │   ├── web.php                 # Web routes
    │   └── api.php                 # API routes
    │
    ├── Services/
    │   ├── Contracts/              # Service Interface
    │   └── LibraryService.php      # Service Implementation
    │
    ├── Support/
    │   ├── Helpers/                # Helper functions
    │   ├── Traits/                 # Shared traits
    │   └── Enums/                  # Enum definitions
    │
    └── Tests/
        ├── Unit/                   # Unit tests
        └── Feature/                # Feature tests
```

### 4.2. Penjelasan Direktori

| Direktori | Fungsi |
|---|---|
| `Config/` | File konfigurasi default, definisi permission, dan definisi menu. |
| `Database/` | Migration, seeder, dan factory untuk skema data module. |
| `Http/` | Controller tipis, middleware, dan form request validation. |
| `Models/` | Eloquent model untuk entitas data module. |
| `Policies/` | Authorization policy untuk model-model module. |
| `Providers/` | Service provider — titik masuk registrasi module ke container. |
| `Repositories/` | Data access layer — contract dan implementasi Eloquent. |
| `Resources/` | View, translation, dan static assets. |
| `Routes/` | Definisi route web dan API module. |
| `Services/` | Business logic utama — contract dan implementasi. |
| `Support/` | Helper, trait, dan enum pendukung. |
| `Tests/` | Unit test dan feature test module. |

---

## 5. module.json

### 5.1. Deskripsi

File `module.json` adalah **manifest resmi** setiap module. File ini menjadi sumber kebenaran (_single source of truth_) tentang identitas, versi, dependency, kapabilitas, hooks lifecycle, dan integritas module.

Module Loader membaca file ini untuk menentukan apakah module valid, kompatibel, dan layak dimuat.

### 5.2. Struktur Manifest

```
module.json
├── metadata          # Identitas module
├── provider          # Service Provider utama
├── dependencies      # Dependency module lain
├── permissions       # Deklarasi permission
├── menus             # Deklarasi menu
├── widgets           # Deklarasi widget
├── settings          # Deklarasi setting key
├── routes            # Deklarasi file route
├── migrations        # Deklarasi path migration
├── assets            # Deklarasi asset dan publikasi
├── install           # Hook saat install
├── uninstall         # Hook saat uninstall
├── update            # Hook saat update
├── compatibility     # Compatibility matrix
└── checksum          # Hash integritas file
```

### 5.3. Spesifikasi Property Lengkap

#### Grup: Identitas (Metadata)

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `name` | `string` | ✅ | Nama resmi module (PascalCase). Contoh: `"Library"`. |
| `slug` | `string` | ✅ | Identifier unik (kebab-case). Contoh: `"library"`. |
| `description` | `string` | ✅ | Deskripsi singkat fungsi module. |
| `version` | `string` | ✅ | Versi module (Semantic Versioning). Contoh: `"1.0.0"`. |
| `author` | `object` | ✅ | Informasi pembuat: `name`, `email`, `url`. |
| `license` | `string` | ❌ | Lisensi module. Contoh: `"MIT"`, `"Proprietary"`. |
| `keywords` | `array` | ❌ | Kata kunci untuk pencarian di marketplace. |

#### Grup: Provider & Priority

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `provider` | `string` | ✅ | Fully qualified class name Service Provider utama. |
| `priority` | `integer` | ❌ | Urutan pemuatan. Semakin kecil = semakin awal. Default: `100`. |

#### Grup: Dependencies

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `dependencies` | `array` | ❌ | Daftar module lain yang dibutuhkan beserta versi minimum dan flag opsional. |

Setiap item dependency memiliki property:

| Sub-property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `module` | `string` | ✅ | Slug module yang dibutuhkan. |
| `version` | `string` | ✅ | Versi minimum. Contoh: `">=1.0.0"`. |
| `optional` | `boolean` | ❌ | Jika `true`, module tetap dimuat meski dependency tidak ada. Default: `false`. |

#### Grup: Compatibility

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `compatibility.minimum_core` | `string` | ✅ | Versi minimum CosmicLib Core Engine. Contoh: `"1.0.0"`. |
| `compatibility.minimum_php` | `string` | ✅ | Versi minimum PHP. Contoh: `"8.2"`. |
| `compatibility.minimum_laravel` | `string` | ✅ | Versi minimum Laravel. Contoh: `"12.0"`. |
| `compatibility.tested_up_to` | `string` | ❌ | Versi core tertinggi yang telah diuji. |
| `compatibility.breaking_changes` | `array` | ❌ | Daftar versi yang memiliki breaking change. |

#### Grup: Registrasi Komponen

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `permissions` | `array` | ❌ | Daftar slug permission yang didaftarkan module. |
| `menus` | `array` | ❌ | Daftar definisi menu yang didaftarkan ke Menu Engine. |
| `widgets` | `array` | ❌ | Daftar widget yang disediakan module ke Widget Engine. |
| `settings` | `array` | ❌ | Daftar setting key yang digunakan module (untuk dokumentasi & validation). |
| `events` | `array` | ❌ | Daftar event yang dipublikasikan module. |
| `contracts` | `array` | ❌ | Daftar contract/interface yang disediakan module. |

#### Grup: Routes

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `routes` | `object` | ❌ | Deklarasi file route module. |
| `routes.web` | `string` | ❌ | Path file web routes relatif terhadap direktori module. Default: `"Routes/web.php"`. |
| `routes.api` | `string` | ❌ | Path file API routes. Default: `"Routes/api.php"`. |
| `routes.prefix` | `string` | ❌ | Prefix URL route. Default mengikuti slug module. |
| `routes.middleware` | `array` | ❌ | Middleware tambahan untuk semua route module. |

#### Grup: Migrations

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `migrations` | `object` | ❌ | Deklarasi konfigurasi migration module. |
| `migrations.path` | `string` | ❌ | Path direktori migration. Default: `"Database/Migrations"`. |
| `migrations.table_prefix` | `string` | ❌ | Prefix tabel database module. Contoh: `"library_"`. |
| `migrations.run_on_install` | `boolean` | ❌ | Jalankan migration otomatis saat install. Default: `true`. |
| `migrations.rollback_on_uninstall` | `boolean` | ❌ | Rollback migration saat uninstall. Default: `false`. |

#### Grup: Assets

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `assets` | `object` | ❌ | Deklarasi asset dan konfigurasi publikasi. |
| `assets.source` | `string` | ❌ | Path direktori asset sumber. Default: `"Resources/assets"`. |
| `assets.destination` | `string` | ❌ | Path tujuan publikasi di `public/`. Default: `"public/modules/{slug}"`. |
| `assets.publish_on_install` | `boolean` | ❌ | Publikasi asset otomatis saat install. Default: `true`. |
| `assets.vite` | `boolean` | ❌ | Gunakan Vite untuk kompilasi asset. Default: `false`. |
| `assets.files` | `array` | ❌ | Daftar file spesifik yang dipublikasikan (jika tidak seluruh direktori). |

#### Grup: Lifecycle Hooks

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `install` | `object` | ❌ | Hook yang dijalankan saat proses install module. |
| `install.before` | `string` | ❌ | Fully qualified class name yang dipanggil **sebelum** install. |
| `install.after` | `string` | ❌ | Fully qualified class name yang dipanggil **setelah** install. |
| `install.seeders` | `array` | ❌ | Daftar seeder class yang dijalankan saat install. |
| `uninstall` | `object` | ❌ | Hook yang dijalankan saat proses uninstall module. |
| `uninstall.before` | `string` | ❌ | Fully qualified class name yang dipanggil **sebelum** uninstall. |
| `uninstall.after` | `string` | ❌ | Fully qualified class name yang dipanggil **setelah** uninstall. |
| `uninstall.cleanup` | `array` | ❌ | Resource yang harus dibersihkan: `"tables"`, `"settings"`, `"permissions"`, `"menus"`, `"assets"`. |
| `update` | `object` | ❌ | Hook yang dijalankan saat proses update module. |
| `update.before` | `string` | ❌ | Fully qualified class name yang dipanggil **sebelum** update. |
| `update.after` | `string` | ❌ | Fully qualified class name yang dipanggil **setelah** update. |
| `update.seeders` | `array` | ❌ | Daftar seeder yang dijalankan saat update (untuk data baru). |

#### Grup: Checksum (Integritas)

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `checksum` | `object` | ❌ | Data integritas file module. Digunakan untuk validasi marketplace. |
| `checksum.algorithm` | `string` | ❌ | Algoritma hash. Contoh: `"sha256"`. |
| `checksum.hash` | `string` | ❌ | Hash SHA-256 dari seluruh file module (dihitung saat build/publish). |
| `checksum.signed_by` | `string` | ❌ | Identitas publisher yang menandatangani module. |
| `checksum.signature` | `string` | ❌ | Digital signature untuk verifikasi keaslian module. |

### 5.4. Contoh module.json Lengkap

```json
{
    "name": "Library",
    "slug": "library",
    "description": "Modul manajemen perpustakaan — buku, anggota, peminjaman, pengembalian, denda.",
    "version": "1.0.0",
    "license": "Proprietary",
    "keywords": ["library", "perpustakaan", "buku", "peminjaman"],
    "author": {
        "name": "CosmicLib Team",
        "email": "dev@cosmiclib.id",
        "url": "https://cosmiclib.id"
    },
    "provider": "Modules\\Library\\Providers\\LibraryServiceProvider",
    "priority": 50,

    "dependencies": [
        {
            "module": "Media",
            "version": ">=1.0.0",
            "optional": false
        },
        {
            "module": "Notification",
            "version": ">=1.0.0",
            "optional": false
        },
        {
            "module": "Barcode",
            "version": ">=1.0.0",
            "optional": true
        }
    ],

    "compatibility": {
        "minimum_core": "1.0.0",
        "minimum_php": "8.2",
        "minimum_laravel": "12.0",
        "tested_up_to": "1.2.0",
        "breaking_changes": ["2.0.0"]
    },

    "permissions": [
        "library.books.view",
        "library.books.create",
        "library.books.edit",
        "library.books.delete",
        "library.members.view",
        "library.members.create",
        "library.members.edit",
        "library.members.delete",
        "library.borrows.view",
        "library.borrows.create",
        "library.borrows.return",
        "library.fines.view",
        "library.fines.manage",
        "library.reports.view",
        "library.settings.manage"
    ],

    "menus": [
        {
            "title": "Perpustakaan",
            "icon": "book-open",
            "route": "library.dashboard",
            "permission": "library.books.view",
            "order": 10,
            "children": [
                {
                    "title": "Daftar Buku",
                    "route": "library.books.index",
                    "permission": "library.books.view"
                },
                {
                    "title": "Anggota",
                    "route": "library.members.index",
                    "permission": "library.members.view"
                },
                {
                    "title": "Peminjaman",
                    "route": "library.borrows.index",
                    "permission": "library.borrows.view"
                },
                {
                    "title": "Denda",
                    "route": "library.fines.index",
                    "permission": "library.fines.view"
                }
            ]
        }
    ],

    "widgets": [
        {
            "name": "library-stats",
            "title": "Statistik Perpustakaan",
            "permission": "library.books.view"
        }
    ],

    "settings": [
        "library.max_borrow_days",
        "library.max_borrow_books",
        "library.fine_per_day",
        "library.allow_renewal",
        "library.max_renewal_count"
    ],

    "events": [
        "BookCreated",
        "BookUpdated",
        "BookDeleted",
        "BorrowCreated",
        "BorrowReturned",
        "FineCreated"
    ],

    "contracts": [
        "Modules\\Library\\Services\\Contracts\\BookServiceInterface",
        "Modules\\Library\\Services\\Contracts\\BorrowServiceInterface",
        "Modules\\Library\\Repositories\\Contracts\\BookRepositoryInterface"
    ],

    "routes": {
        "web": "Routes/web.php",
        "api": "Routes/api.php",
        "prefix": "library",
        "middleware": ["auth", "verified"]
    },

    "migrations": {
        "path": "Database/Migrations",
        "table_prefix": "library_",
        "run_on_install": true,
        "rollback_on_uninstall": false
    },

    "assets": {
        "source": "Resources/assets",
        "destination": "public/modules/library",
        "publish_on_install": true,
        "vite": false,
        "files": []
    },

    "install": {
        "before": "Modules\\Library\\Installers\\LibraryPreInstaller",
        "after": "Modules\\Library\\Installers\\LibraryPostInstaller",
        "seeders": [
            "Modules\\Library\\Database\\Seeders\\BookCategorySeeder"
        ]
    },

    "uninstall": {
        "before": "Modules\\Library\\Installers\\LibraryPreUninstaller",
        "after": null,
        "cleanup": ["settings", "permissions", "menus", "assets"]
    },

    "update": {
        "before": "Modules\\Library\\Installers\\LibraryPreUpdater",
        "after": "Modules\\Library\\Installers\\LibraryPostUpdater",
        "seeders": []
    },

    "checksum": {
        "algorithm": "sha256",
        "hash": "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855",
        "signed_by": "CosmicLib Team <dev@cosmiclib.id>",
        "signature": "MEUCIQDv..."
    }
}
```

### 5.5. Diagram Manifest

```
┌─────────────────────────────────────────────────────────┐
│                      module.json                         │
├─────────────────┬───────────────────────────────────────┤
│  IDENTITAS      │  name, slug, description, version,    │
│                 │  author, license, keywords             │
├─────────────────┼───────────────────────────────────────┤
│  ENGINE         │  provider, priority                   │
├─────────────────┼───────────────────────────────────────┤
│  DEPENDENCIES   │  dependencies[]                       │
│                 │    └─ module, version, optional       │
├─────────────────┼───────────────────────────────────────┤
│  COMPATIBILITY  │  compatibility{}                       │
│                 │    ├─ minimum_core / php / laravel    │
│                 │    ├─ tested_up_to                    │
│                 │    └─ breaking_changes[]              │
├─────────────────┼───────────────────────────────────────┤
│  REGISTRASI     │  permissions[], menus[], widgets[],   │
│                 │  settings[], events[], contracts[]    │
├─────────────────┼───────────────────────────────────────┤
│  ROUTES         │  routes{}                             │
│                 │    ├─ web, api (path file)            │
│                 │    ├─ prefix                         │
│                 │    └─ middleware[]                    │
├─────────────────┼───────────────────────────────────────┤
│  MIGRATIONS     │  migrations{}                         │
│                 │    ├─ path, table_prefix             │
│                 │    ├─ run_on_install                 │
│                 │    └─ rollback_on_uninstall          │
├─────────────────┼───────────────────────────────────────┤
│  ASSETS         │  assets{}                             │
│                 │    ├─ source, destination            │
│                 │    ├─ publish_on_install, vite       │
│                 │    └─ files[]                        │
├─────────────────┼───────────────────────────────────────┤
│  LIFECYCLE      │  install{}, uninstall{}, update{}    │
│                 │    ├─ before / after (hooks)         │
│                 │    ├─ seeders[]                      │
│                 │    └─ cleanup[]                      │
├─────────────────┼───────────────────────────────────────┤
│  CHECKSUM       │  checksum{}                           │
│                 │    ├─ algorithm, hash                │
│                 │    └─ signed_by, signature           │
└─────────────────┴───────────────────────────────────────┘
```

### 5.6. Validasi module.json

Module Loader **wajib** memvalidasi `module.json` sebelum memuat module:

| Validasi | Aksi Jika Gagal |
|---|---|
| File `module.json` tidak ditemukan | Module diabaikan, log warning. |
| Property wajib tidak ada | Module tidak dimuat, log error. |
| Format versi tidak valid | Module tidak dimuat, log error. |
| `compatibility.minimum_core` tidak terpenuhi | Module tidak dimuat, notifikasi admin. |
| `compatibility.minimum_php` tidak terpenuhi | Module tidak dimuat, notifikasi admin. |
| `compatibility.minimum_laravel` tidak terpenuhi | Module tidak dimuat, notifikasi admin. |
| Dependency tidak terpenuhi | Module tidak dimuat, notifikasi admin. |
| `checksum.hash` tidak cocok (marketplace only) | Module ditolak, log security warning. |
| `checksum.signature` tidak valid (marketplace only) | Module ditolak, log security alert. |

---

## 6. Module Loader

### 6.1. Tanggung Jawab

Module Loader adalah komponen Core Engine yang bertugas **menemukan, memvalidasi, dan memuat** semua module yang terdaftar dan aktif.

### 6.2. Proses Pemuatan

```
┌──────────────────────────────────────────────────────┐
│                   MODULE LOADER                       │
├──────────────────────────────────────────────────────┤
│                                                       │
│  1. Scan Modules/ directory                          │
│     └─ Temukan semua subdirektori                    │
│                                                       │
│  2. Baca module.json                                 │
│     └─ Parse dan validasi manifest                   │
│                                                       │
│  3. Dependency Check                                 │
│     └─ Pastikan semua dependency terpenuhi           │
│                                                       │
│  4. Version Check                                    │
│     └─ Periksa kompatibilitas core/php/laravel       │
│                                                       │
│  5. Priority Sort                                    │
│     └─ Urutkan module berdasarkan priority           │
│                                                       │
│  6. Register Provider                                │
│     └─ Daftarkan Service Provider ke container       │
│                                                       │
│  7. Register Route                                   │
│     └─ Muat web.php dan api.php                      │
│                                                       │
│  8. Register View                                    │
│     └─ Daftarkan namespace view Blade                │
│                                                       │
│  9. Register Migration                               │
│     └─ Daftarkan path migration                      │
│                                                       │
│  10. Register Seeder                                 │
│      └─ Daftarkan seeder class                       │
│                                                       │
│  11. Register Translation                            │
│      └─ Daftarkan file bahasa/lokalisasi             │
│                                                       │
│  12. Register Assets                                 │
│      └─ Publikasikan atau daftarkan static assets    │
│                                                       │
└──────────────────────────────────────────────────────┘
```

### 6.3. Urutan Pemuatan (Priority)

Module dimuat berdasarkan nilai `priority` di `module.json`:

| Priority | Kategori | Contoh Module |
|---|---|---|
| `0–9` | Core Modules | Core, Authentication, Permission |
| `10–29` | Infrastructure | Menu, Theme, Widget, Media |
| `30–49` | System | Notification, Setting, Backup |
| `50–99` | Application | Library, CMS |
| `100+` | Custom / Third-party | Module eksternal |

### 6.4. Caching

Module Loader **harus** mendukung caching manifest untuk performa:

- Cache daftar module aktif dan konfigurasinya.
- Cache di-invalidasi saat module di-install, di-enable, di-disable, atau di-update.
- Artisan command `module:cache` dan `module:cache-clear` tersedia.

---

## 7. Module Registration

### 7.1. Registrasi Otomatis

Saat module berstatus **enabled**, Module Loader secara otomatis meregistrasi semua komponen berikut:

| Komponen | Mekanisme Registrasi |
|---|---|
| **Service Provider** | `App::register(ModuleServiceProvider::class)` |
| **Routes** | `Route::group()` dengan prefix dan middleware module |
| **Views** | `View::addNamespace('module-slug', viewPath)` |
| **Menu** | Menu Engine menerima definisi dari `menu.php` |
| **Permission** | Permission Engine menerima definisi dari `permission.php` |
| **Widget** | Widget Engine menerima definisi dari `module.json > widgets` |
| **Configuration** | `Config::set()` dari `config.php` dengan namespace module |
| **Translation** | `Lang::addNamespace()` untuk lokalisasi module |
| **Migration** | `loadMigrationsFrom()` mendaftarkan path migration |
| **Event** | Event dan listener didaftarkan via `EventServiceProvider` |

### 7.2. Service Provider Utama

Setiap module **wajib** memiliki satu Service Provider utama yang merupakan titik masuk registrasi:

```
Modules/{ModuleName}/Providers/{ModuleName}ServiceProvider.php
```

Service Provider ini bertanggung jawab untuk:

1. Mendaftarkan binding ke service container.
2. Memuat konfigurasi, route, view, migration, translation.
3. Mendaftarkan event listener dan observer.
4. Mempublikasikan asset jika diperlukan.
5. Mendaftarkan Artisan command milik module.

### 7.3. Route Registration

Route didaftarkan dengan isolasi namespace:

| Tipe | File | Prefix | Middleware |
|---|---|---|---|
| Web | `Routes/web.php` | `/module-slug` | `web`, `auth` |
| API | `Routes/api.php` | `/api/module-slug` | `api`, `auth:sanctum` |

---

## 8. Dependency Management

### 8.1. Konsep

Module dapat bergantung pada module lain. Dependency **wajib** dideklarasikan secara eksplisit di `module.json` pada property `dependencies`.

### 8.2. Aturan Dependency

| Aturan | Deskripsi |
|---|---|
| **Deklarasi Eksplisit** | Semua dependency harus tercantum di `module.json`. |
| **Versi Minimum** | Setiap dependency menyertakan versi minimum yang dibutuhkan. |
| **Dependency Check on Install** | Saat install, semua dependency diperiksa sebelum module diaktifkan. |
| **Circular Dependency Forbidden** | Module A → Module B → Module A **dilarang**. |
| **Soft Dependency** | Dependency opsional ditandai dengan flag `optional: true`. |

### 8.3. Contoh Dependency Tree

```
Library (v1.0.0)
├── Media (>=1.0.0)          # Untuk upload cover buku
│   └── Core (>=1.0.0)
├── Notification (>=1.0.0)   # Untuk notifikasi peminjaman
│   └── Core (>=1.0.0)
└── Core (>=1.0.0)           # Fondasi sistem

CMS (v1.0.0)
├── Media (>=1.0.0)          # Untuk upload konten
├── Menu (>=1.0.0)           # Untuk navigasi halaman
└── Core (>=1.0.0)
```

### 8.4. Dependency Resolution

```
┌─────────────────────────────────────────────────┐
│           DEPENDENCY RESOLUTION FLOW             │
├─────────────────────────────────────────────────┤
│                                                  │
│  1. Baca module.json → ambil dependencies        │
│                                                  │
│  2. Untuk setiap dependency:                     │
│     ├─ Apakah module dependency ter-install?     │
│     │  └─ TIDAK → Gagal, notifikasi admin        │
│     ├─ Apakah module dependency enabled?         │
│     │  └─ TIDAK → Gagal, notifikasi admin        │
│     └─ Apakah versi memenuhi minimum?            │
│        └─ TIDAK → Gagal, notifikasi admin        │
│                                                  │
│  3. Periksa circular dependency                  │
│     └─ Jika ditemukan → Gagal, log error         │
│                                                  │
│  4. Semua dependency terpenuhi → Lanjut load     │
│                                                  │
└─────────────────────────────────────────────────┘
```

### 8.5. Proteksi Uninstall

Saat module akan di-uninstall atau di-disable, sistem **wajib** memeriksa apakah ada module lain yang bergantung padanya (_reverse dependency check_). Jika ada, operasi ditolak kecuali admin melakukan _force disable/uninstall_.

---

## 9. Module Versioning

### 9.1. Semantic Versioning

Semua module CosmicLib **wajib** menggunakan [Semantic Versioning 2.0.0](https://semver.org/):

```
MAJOR.MINOR.PATCH

Contoh: 1.2.3
```

| Komponen | Kapan Dinaikkan |
|---|---|
| **MAJOR** | Perubahan yang _breaking_ — API berubah, skema database berubah tidak kompatibel. |
| **MINOR** | Fitur baru yang backward compatible. |
| **PATCH** | Bug fix dan perbaikan kecil. |

### 9.2. Pre-release dan Build Metadata

```
1.0.0-alpha.1    # Pre-release alpha
1.0.0-beta.2     # Pre-release beta
1.0.0-rc.1       # Release candidate
1.0.0+build.123  # Build metadata
```

### 9.3. Compatibility Matrix

| Core Version | Module Version | Kompatibel? |
|---|---|---|
| 1.0.x | 1.x.x | ✅ |
| 1.0.x | 2.x.x | ❌ (perlu core 2.x) |
| 2.0.x | 1.x.x | ⚠️ (backward compat jika ada) |
| 2.0.x | 2.x.x | ✅ |

### 9.4. Version Constraint Syntax

Di `module.json`, dependency versi menggunakan syntax berikut:

| Syntax | Arti |
|---|---|
| `">=1.0.0"` | Versi 1.0.0 atau lebih tinggi. |
| `"^1.0"` | Kompatibel dengan 1.x.x (tidak termasuk 2.0.0). |
| `"~1.2"` | Kompatibel dengan 1.2.x (tidak termasuk 1.3.0). |
| `"1.0.0"` | Tepat versi 1.0.0 saja. |
| `">=1.0.0 <2.0.0"` | Range versi. |

---

## 10. Module Installer

### 10.1. Operasi yang Didukung

| Operasi | Deskripsi | Artisan Command |
|---|---|---|
| **Install** | Memasang module baru ke sistem. | `module:install {slug}` |
| **Upgrade** | Memperbarui module ke versi lebih tinggi. | `module:upgrade {slug}` |
| **Downgrade** | Menurunkan versi module (opsional, jika didukung). | `module:downgrade {slug} {version}` |
| **Repair** | Memperbaiki module yang error (re-register, re-migrate). | `module:repair {slug}` |
| **Enable** | Mengaktifkan module yang ter-install. | `module:enable {slug}` |
| **Disable** | Menonaktifkan module tanpa menghapus. | `module:disable {slug}` |
| **Remove** | Menghapus module dari sistem. | `module:remove {slug}` |

### 10.2. Proses Install

```
┌──────────────────────────────────────────┐
│          MODULE INSTALL FLOW              │
├──────────────────────────────────────────┤
│                                           │
│  1. Validasi module.json                 │
│  2. Periksa minimum_core / php / laravel │
│  3. Periksa dependency                   │
│  4. Jalankan migration                   │
│  5. Jalankan seeder (jika ada)           │
│  6. Registrasi permission                │
│  7. Registrasi menu                      │
│  8. Publikasi asset                      │
│  9. Clear cache                          │
│  10. Set status = installed              │
│  11. Log aktivitas                       │
│                                           │
└──────────────────────────────────────────┘
```

### 10.3. Proses Remove

```
┌──────────────────────────────────────────┐
│          MODULE REMOVE FLOW               │
├──────────────────────────────────────────┤
│                                           │
│  1. Periksa reverse dependency           │
│  2. Disable module terlebih dahulu       │
│  3. Rollback migration (opsional)        │
│  4. Hapus permission terkait             │
│  5. Hapus menu terkait                   │
│  6. Hapus asset terpublikasi             │
│  7. Hapus konfigurasi cache             │
│  8. Hapus record module dari database    │
│  9. Hapus direktori module (opsional)    │
│  10. Log aktivitas                       │
│                                           │
└──────────────────────────────────────────┘
```

---

## 11. Module Configuration

### 11.1. Sumber Konfigurasi

Konfigurasi module berasal dari tiga sumber dengan prioritas berikut:

| Prioritas | Sumber | Deskripsi |
|---|---|---|
| 1 (Tertinggi) | **Database** (Setting Engine) | Konfigurasi yang diubah admin melalui UI. |
| 2 | **System Settings** (`config/`) | Override di level aplikasi. |
| 3 (Terendah) | **Module Default** (`Config/config.php`) | Nilai default dari module. |

### 11.2. File config.php

Setiap module menyediakan file `Config/config.php` berisi konfigurasi default:

```php
// Modules/Library/Config/config.php
return [
    'max_borrow_days'    => 14,
    'max_borrow_books'   => 5,
    'fine_per_day'       => 1000,
    'allow_renewal'      => true,
    'max_renewal_count'  => 2,
    'cover_upload_path'  => 'uploads/library/covers',
    'barcode_enabled'    => false,
];
```

### 11.3. Akses Konfigurasi

Konfigurasi diakses melalui helper atau facade dengan namespace module:

```
config('library.max_borrow_days')    // → 14
setting('library.fine_per_day')      // → 1000 (dari Setting Engine)
```

### 11.4. Larangan

- ❌ **Tidak boleh hardcode** nilai konfigurasi di dalam logic module.
- ❌ **Tidak boleh** menggunakan `env()` di luar file config.
- ❌ **Tidak boleh** menyimpan konfigurasi di file `.env` modul.

---

## 12. Module Permission

### 12.1. Definisi Permission

Setiap module mendefinisikan permission-nya di `Config/permission.php`:

```php
// Modules/Library/Config/permission.php
return [
    'groups' => [
        [
            'name'  => 'Manajemen Buku',
            'slug'  => 'library.books',
            'permissions' => [
                ['slug' => 'library.books.view',   'name' => 'Lihat Buku'],
                ['slug' => 'library.books.create', 'name' => 'Tambah Buku'],
                ['slug' => 'library.books.edit',   'name' => 'Edit Buku'],
                ['slug' => 'library.books.delete', 'name' => 'Hapus Buku'],
            ],
        ],
        [
            'name'  => 'Manajemen Anggota',
            'slug'  => 'library.members',
            'permissions' => [
                ['slug' => 'library.members.view',   'name' => 'Lihat Anggota'],
                ['slug' => 'library.members.create', 'name' => 'Tambah Anggota'],
                ['slug' => 'library.members.edit',   'name' => 'Edit Anggota'],
                ['slug' => 'library.members.delete', 'name' => 'Hapus Anggota'],
            ],
        ],
    ],
];
```

### 12.2. Komponen Permission Module

| Komponen | File/Lokasi | Fungsi |
|---|---|---|
| **Permission Definition** | `Config/permission.php` | Deklarasi semua permission module. |
| **Role Mapping** | Permission Engine (database) | Mapping permission ke role — **tidak di-hardcode**. |
| **Policy** | `Policies/BookPolicy.php` | Authorization logic per model. |
| **Middleware** | `Http/Middleware/` | Middleware untuk route-level authorization. |

### 12.3. Konvensi Penamaan Permission

```
{module-slug}.{resource}.{action}

Contoh:
library.books.view
library.books.create
library.books.edit
library.books.delete
library.borrows.create
library.borrows.return
library.fines.manage
library.reports.view
library.settings.manage
```

### 12.4. Integrasi dengan Permission Engine

- Permission **didaftarkan** saat module di-install atau di-enable.
- Permission **dihapus** saat module di-uninstall.
- Permission **TIDAK** dihapus saat module di-disable (untuk menjaga data role mapping).
- Role assignment dilakukan oleh Permission Engine — module **tidak** menentukan role.

---

## 13. Module Menu

### 13.1. Definisi Menu

Setiap module mendefinisikan menu-nya di `Config/menu.php`:

```php
// Modules/Library/Config/menu.php
return [
    [
        'title'      => 'Perpustakaan',
        'slug'       => 'library',
        'icon'       => 'book-open',
        'route'      => 'library.dashboard',
        'permission' => 'library.books.view',
        'order'      => 10,
        'badge'      => null,
        'target'     => '_self',
        'children'   => [
            [
                'title'      => 'Dashboard',
                'slug'       => 'library.dashboard',
                'icon'       => 'layout-dashboard',
                'route'      => 'library.dashboard',
                'permission' => 'library.books.view',
                'order'      => 1,
            ],
            [
                'title'      => 'Daftar Buku',
                'slug'       => 'library.books',
                'icon'       => 'book',
                'route'      => 'library.books.index',
                'permission' => 'library.books.view',
                'order'      => 2,
            ],
            [
                'title'      => 'Anggota',
                'slug'       => 'library.members',
                'icon'       => 'users',
                'route'      => 'library.members.index',
                'permission' => 'library.members.view',
                'order'      => 3,
            ],
            [
                'title'      => 'Peminjaman',
                'slug'       => 'library.borrows',
                'icon'       => 'arrow-right-left',
                'route'      => 'library.borrows.index',
                'permission' => 'library.borrows.view',
                'order'      => 4,
                'badge'      => 'library.pending_borrows_count',
            ],
            [
                'title'      => 'Denda',
                'slug'       => 'library.fines',
                'icon'       => 'banknote',
                'route'      => 'library.fines.index',
                'permission' => 'library.fines.view',
                'order'      => 5,
            ],
            [
                'title'      => 'Pengaturan',
                'slug'       => 'library.settings',
                'icon'       => 'settings',
                'route'      => 'library.settings.index',
                'permission' => 'library.settings.manage',
                'order'      => 99,
            ],
        ],
    ],
];
```

### 13.2. Properti Menu

| Property | Tipe | Wajib | Deskripsi |
|---|---|---|---|
| `title` | `string` | ✅ | Label menu (Bahasa Indonesia). |
| `slug` | `string` | ✅ | Identifier unik menu. |
| `icon` | `string` | ❌ | Nama ikon (Lucide, Tabler, dll). |
| `route` | `string` | ✅ | Named route Laravel. |
| `permission` | `string` | ✅ | Permission yang diperlukan untuk melihat menu. |
| `order` | `integer` | ❌ | Urutan tampil. Default: `100`. |
| `badge` | `string|null` | ❌ | Key untuk dynamic badge counter. |
| `target` | `string` | ❌ | Target link (`_self`, `_blank`). Default: `_self`. |
| `children` | `array` | ❌ | Sub-menu (nested). |

### 13.3. Integrasi dengan Menu Engine

- Menu **didaftarkan** saat module di-enable.
- Menu **dihapus** dari tampilan saat module di-disable.
- Menu **hanya tampil** jika user memiliki permission yang sesuai.
- Menu Engine menggabungkan menu dari semua module aktif dan mengurutkan berdasarkan `order`.

---

## 14. Module Assets

### 14.1. Lokasi Assets

Assets module berada di direktori:

```
Modules/{ModuleName}/Resources/assets/
├── css/
│   └── library.css
├── js/
│   └── library.js
├── images/
│   └── logo.png
├── icons/
│   └── library-icon.svg
└── fonts/
    └── custom-font.woff2
```

### 14.2. Publikasi Assets

Assets dapat dipublikasikan ke direktori `public/` aplikasi melalui Service Provider:

```
public/modules/{module-slug}/
├── css/
├── js/
├── images/
├── icons/
└── fonts/
```

### 14.3. Artisan Command

| Command | Deskripsi |
|---|---|
| `module:publish {slug}` | Publikasi asset module ke `public/`. |
| `module:publish --all` | Publikasi semua asset module aktif. |
| `module:publish --force` | Force overwrite asset yang sudah ada. |

### 14.4. Aturan

- Assets **tidak boleh** memodifikasi asset Core Engine atau module lain.
- Assets harus menggunakan namespace module untuk menghindari konflik.
- Gunakan Vite atau Laravel Mix untuk kompilasi asset jika diperlukan.

---

## 15. Module Database

### 15.1. Migration

Setiap module menyimpan migration di `Database/Migrations/`:

```
Modules/Library/Database/Migrations/
├── 2026_01_01_000001_create_books_table.php
├── 2026_01_01_000002_create_members_table.php
├── 2026_01_01_000003_create_borrow_records_table.php
├── 2026_01_01_000004_create_fines_table.php
└── 2026_01_01_000005_create_book_categories_table.php
```

### 15.2. Aturan Migration

| Aturan | Deskripsi |
|---|---|
| **Prefix Tabel** | Disarankan menggunakan prefix module: `library_books`, `library_members`. |
| **Primary Key** | Gunakan `bigint unsigned AUTO_INCREMENT` atau `uuid`. |
| **Foreign Key** | Definisikan foreign key secara eksplisit dengan `onDelete` dan `onUpdate`. |
| **Soft Delete** | Gunakan `SoftDeletes` untuk tabel yang memerlukan data historis. |
| **Index** | Tambahkan index pada kolom yang sering di-query untuk performa. |
| **Timestamps** | Selalu gunakan `timestamps()` — `created_at`, `updated_at`. |

### 15.3. Seeder

```
Modules/Library/Database/Seeders/
├── LibraryDatabaseSeeder.php
├── BookCategorySeeder.php
└── SampleBookSeeder.php
```

Seeder dijalankan saat install atau via command:

```
module:seed {slug}
module:seed {slug} --class=BookCategorySeeder
```

### 15.4. Factory

```
Modules/Library/Database/Factories/
├── BookFactory.php
├── MemberFactory.php
└── BorrowRecordFactory.php
```

Factory digunakan untuk testing dan seeding data dummy.

### 15.5. Artisan Command Database

| Command | Deskripsi |
|---|---|
| `module:migrate {slug}` | Jalankan migration module. |
| `module:migrate-rollback {slug}` | Rollback migration module. |
| `module:migrate-refresh {slug}` | Refresh migration module. |
| `module:migrate-status {slug}` | Lihat status migration module. |
| `module:seed {slug}` | Jalankan seeder module. |

---

## 16. Module Communication

### 16.1. Prinsip

Module **TIDAK BOLEH** memanggil module lain secara langsung. Komunikasi antarmodul harus melalui mekanisme yang terdefinisi (_loosely coupled_).

### 16.2. Mekanisme Komunikasi

| Mekanisme | Kapan Digunakan | Deskripsi |
|---|---|---|
| **Contract (Interface)** | Ketika module butuh layanan dari module lain. | Module mendefinisikan interface, module lain menyediakan implementasi. |
| **Service (via Container)** | Ketika perlu memanggil logic dari module lain. | Resolve service dari container, bukan langsung instantiate. |
| **Event & Listener** | Ketika perlu notifikasi atau reaksi antara module. | Module A men-dispatch event, module B listen dan bereaksi. |
| **Facade** | Shortcut untuk service yang sering digunakan. | Facade sebagai proxy ke service container binding. |

### 16.3. Contoh Komunikasi via Event

```
┌──────────────┐          Event: BorrowCreated          ┌──────────────────┐
│   Library     │ ─────────────────────────────────────▶ │   Notification    │
│   Module      │                                        │   Module          │
│               │  Dispatch event saat                   │                   │
│               │  peminjaman dibuat                     │  Listen event,    │
│               │                                        │  kirim notifikasi │
└──────────────┘                                        └──────────────────┘
```

### 16.4. Contoh Komunikasi via Contract

```
┌──────────────┐                                        ┌──────────────────┐
│   Library     │     Resolve: MediaServiceInterface    │   Media           │
│   Module      │ ─────────────────────────────────────▶│   Module          │
│               │                                        │                   │
│  Butuh upload │     Mendapatkan implementasi dari     │  Menyediakan      │
│  cover buku   │     service container                 │  upload service   │
└──────────────┘                                        └──────────────────┘
```

### 16.5. Larangan

- ❌ **DILARANG** import class langsung dari module lain (`use Modules\Media\Services\MediaService`).
- ❌ **DILARANG** akses model module lain secara langsung.
- ❌ **DILARANG** menulis query ke tabel milik module lain.
- ✅ **WAJIB** gunakan contract/interface untuk komunikasi.
- ✅ **WAJIB** gunakan event untuk notifikasi antar-module.

---

## 17. Module Event

### 17.1. Komponen Event

Setiap module dapat membuat dan menggunakan komponen event berikut:

| Komponen | Lokasi | Fungsi |
|---|---|---|
| **Event** | `Events/` | Mendefinisikan event yang terjadi di module. |
| **Listener** | `Listeners/` | Menangani event dari module sendiri atau module lain. |
| **Observer** | `Observers/` | Mengamati lifecycle Eloquent model (creating, updating, deleting). |

### 17.2. Konvensi Penamaan Event

```
{ModuleName}{Entity}{Action}

Contoh:
LibraryBookCreated
LibraryBookUpdated
LibraryBookDeleted
LibraryBorrowCreated
LibraryBorrowReturned
LibraryFineCreated
LibraryFinePaid
```

### 17.3. Event Registration

Event dan listener didaftarkan di `Providers/EventServiceProvider.php`:

```php
protected $listen = [
    LibraryBookCreated::class => [
        UpdateBookStatistics::class,
        IndexBookForSearch::class,
    ],
    LibraryBorrowCreated::class => [
        SendBorrowNotification::class,
        UpdateMemberBorrowCount::class,
    ],
    LibraryBorrowReturned::class => [
        CalculateFine::class,
        SendReturnNotification::class,
    ],
];
```

### 17.4. Cross-Module Event

Module dapat men-listen event dari module lain **tanpa** import class langsung — gunakan event discovery atau string-based event name.

---

## 18. Module Update

### 18.1. Proses Update

| Langkah | Deskripsi |
|---|---|
| 1 | Backup state module saat ini (versi, konfigurasi). |
| 2 | Validasi versi baru — kompatibilitas core, PHP, Laravel. |
| 3 | Periksa dependency baru. |
| 4 | Jalankan migration baru (jika ada). |
| 5 | Jalankan seeder update (jika ada). |
| 6 | Update versi di `module.json` dan database. |
| 7 | Clear cache (config, route, view). |
| 8 | Re-register provider. |
| 9 | Log aktivitas update. |
| 10 | Notifikasi admin. |

### 18.2. Rollback

Jika update gagal, sistem **harus** mampu:

- Rollback migration yang baru dijalankan.
- Mengembalikan versi module ke versi sebelumnya.
- Mengembalikan konfigurasi ke state sebelumnya.
- Log error dan notifikasi admin.

### 18.3. Migration Versioning

Migration baru untuk update **harus** memiliki timestamp lebih baru:

```
Database/Migrations/
├── 2026_01_01_000001_create_books_table.php        # v1.0.0
├── 2026_01_01_000002_create_members_table.php      # v1.0.0
├── 2026_06_15_000001_add_isbn_to_books_table.php   # v1.1.0
└── 2026_09_01_000001_add_barcode_to_books_table.php # v1.2.0
```

### 18.4. Artisan Command

| Command | Deskripsi |
|---|---|
| `module:upgrade {slug}` | Upgrade module ke versi terbaru. |
| `module:upgrade {slug} --to={version}` | Upgrade ke versi spesifik. |
| `module:rollback {slug}` | Rollback update terakhir. |

---

## 19. Module Security

### 19.1. Authorization

Setiap akses ke fitur module **wajib** melalui authorization layer:

| Layer | Mekanisme |
|---|---|
| **Route Middleware** | `permission:library.books.view` |
| **Controller** | `$this->authorize('view', $book)` |
| **Policy** | `BookPolicy@view`, `BookPolicy@create`, dll. |
| **Blade Directive** | `@can('library.books.edit')` |
| **API Gate** | `Gate::allows('library.books.delete')` |

### 19.2. Validation

Semua input **wajib** divalidasi menggunakan Form Request:

```
Http/Requests/
├── StoreBookRequest.php
├── UpdateBookRequest.php
├── StoreMemberRequest.php
└── StoreBorrowRequest.php
```

### 19.3. Sanitization

| Aturan | Deskripsi |
|---|---|
| HTML Purification | Bersihkan input HTML dari XSS. |
| SQL Injection Prevention | Gunakan Eloquent ORM / query builder — **dilarang** raw SQL tanpa parameter binding. |
| File Upload Validation | Validasi tipe, ukuran, dan ekstensi file. |
| CSRF Protection | Semua form web wajib menggunakan `@csrf`. |

### 19.4. Audit Log

Semua operasi sensitif di module **wajib** di-log ke Audit Log:

| Operasi | Contoh |
|---|---|
| Create | "User X menambahkan buku Y" |
| Update | "User X mengubah data buku Y" |
| Delete | "User X menghapus buku Y" |
| Borrow | "User X meminjamkan buku Y kepada anggota Z" |
| Return | "User X menerima pengembalian buku Y dari anggota Z" |
| Config Change | "User X mengubah pengaturan denda harian" |

### 19.5. Error Handling

- Semua operasi sensitif **wajib** dibungkus `try-catch`.
- Error di-log menggunakan Laravel logging.
- Pesan error untuk user menggunakan **Bahasa Indonesia**.
- Detail teknis error **tidak** ditampilkan ke user di production.

---

## 20. Module Marketplace (Future)

> **Status: Rencana Masa Depan (v2.x+)**

### 20.1. Konsep

Module Marketplace adalah platform distribusi module CosmicLib yang memungkinkan developer mempublikasikan module dan admin memasang module secara online.

### 20.2. Fitur yang Direncanakan

| Fitur | Deskripsi |
|---|---|
| **Install Online** | Admin dapat mencari dan memasang module dari marketplace melalui dashboard. |
| **Update Online** | Notifikasi update tersedia, admin dapat meng-update module secara online. |
| **Signature Validation** | Setiap module yang diunduh divalidasi signature digitalnya untuk keamanan. |
| **Compatibility Check** | Marketplace memeriksa kompatibilitas module dengan versi core, PHP, dan Laravel sebelum install. |
| **Rating & Review** | User dapat memberikan rating dan ulasan module. |
| **License Management** | Modul berbayar dilindungi oleh License Engine. |
| **Auto Dependency** | Marketplace otomatis memasang dependency yang dibutuhkan. |

### 20.3. Workflow Marketplace

```
┌────────────────────────────────────────────────┐
│             MARKETPLACE WORKFLOW                │
├────────────────────────────────────────────────┤
│                                                 │
│  1. Admin membuka halaman Marketplace           │
│  2. Mencari module berdasarkan kata kunci       │
│  3. Melihat detail, rating, dan kompatibilitas │
│  4. Klik "Install"                             │
│  5. Sistem memvalidasi signature               │
│  6. Sistem memeriksa dependency                │
│  7. Module diunduh dan diekstrak               │
│  8. Proses install standar dijalankan          │
│  9. Module siap digunakan                      │
│                                                 │
└────────────────────────────────────────────────┘
```

---

## 21. Default Modules

CosmicLib Engine v1.0 menyertakan module bawaan berikut:

| # | Module | Slug | Prioritas | Deskripsi |
|---|---|---|---|---|
| 1 | **Core** | `core` | 0 | Fondasi sistem — helper, base class, contract. |
| 2 | **Authentication** | `authentication` | 1 | Login, register, password reset, 2FA. |
| 3 | **Permission** | `permission` | 2 | Manajemen role, permission, policy. |
| 4 | **Menu** | `menu` | 3 | Manajemen menu dinamis. |
| 5 | **Theme** | `theme` | 4 | Manajemen tema dan tampilan. |
| 6 | **Widget** | `widget` | 5 | Manajemen widget dashboard. |
| 7 | **Media** | `media` | 10 | Manajemen file, upload, galeri. |
| 8 | **Notification** | `notification` | 11 | Notifikasi email, push, in-app. |
| 9 | **System Setting** | `system-setting` | 12 | Konfigurasi sistem via database. |
| 10 | **Library** | `library` | 50 | Manajemen perpustakaan (buku, anggota, peminjaman). |
| 11 | **CMS** | `cms` | 51 | Content Management System. |
| 12 | **Backup** | `backup` | 80 | Backup database dan file. |
| 13 | **System Update** | `system-update` | 81 | Update sistem dan module. |
| 14 | **Installer** | `installer` | 90 | Wizard instalasi awal CosmicLib. |

### 21.1. Dependency Graph Default Modules

```
Installer ─────────────┐
                        │
Core ◄──────────────────┤
 ▲                      │
 ├── Authentication     │
 ├── Permission         │
 ├── Menu               │
 ├── Theme              │
 ├── Widget             │
 ├── System Setting     │
 │                      │
 ├── Media              │
 │    ▲                 │
 │    └── CMS           │
 │    └── Library       │
 │                      │
 ├── Notification       │
 │    ▲                 │
 │    └── Library       │
 │                      │
 ├── Backup             │
 └── System Update      │
```

---

## 22. AI Rules

### 22.1. Kewajiban AI Agent

Setiap AI agent (Claude, Codex, ChatGPT, Cline, Gemini, dll.) yang bekerja di proyek CosmicLib **wajib** mematuhi aturan berikut:

| # | Aturan | Deskripsi |
|---|---|---|
| 1 | **Fitur Baru = Module Baru** | Setiap fitur baru harus dibuat sebagai module di `Modules/`. |
| 2 | **Core = Infrastruktur Only** | Tidak boleh menaruh business logic di Core Engine. |
| 3 | **Loosely Coupled** | Module tidak boleh saling bergantung secara langsung — gunakan contract, event, facade. |
| 4 | **module.json Wajib** | Setiap module harus memiliki `module.json` yang lengkap. |
| 5 | **README.md Wajib** | Setiap module harus memiliki `README.md` dengan dokumentasi. |
| 6 | **Tests Wajib** | Setiap module harus memiliki unit test dan feature test. |
| 7 | **Permission Wajib** | Setiap module harus mendefinisikan permission di `Config/permission.php`. |
| 8 | **Menu Wajib** | Setiap module harus mendefinisikan menu di `Config/menu.php`. |
| 9 | **Config Wajib** | Setiap module harus memiliki `Config/config.php` untuk default values. |
| 10 | **Bahasa Indonesia untuk UI** | Semua label, pesan, notifikasi menggunakan Bahasa Indonesia. |
| 11 | **Bahasa Inggris untuk Code** | Semua class, variable, method, tabel, kolom menggunakan Bahasa Inggris. |

### 22.2. Checklist AI Sebelum Membuat Module

```
┌──────────────────────────────────────────────┐
│        AI MODULE CREATION CHECKLIST           │
├──────────────────────────────────────────────┤
│                                               │
│  □ Baca AGENTS.md dan PROJECT_MANIFEST.md    │
│  □ Baca docs/08_MODULE_ENGINE.md (ini)       │
│  □ Tentukan nama module (PascalCase)         │
│  □ Tentukan slug module (kebab-case)         │
│  □ Identifikasi dependency module            │
│  □ Buat module.json                          │
│  □ Buat README.md module                     │
│  □ Buat struktur direktori standar           │
│  □ Definisikan permission                    │
│  □ Definisikan menu                          │
│  □ Definisikan config default                │
│  □ Buat Service Provider                     │
│  □ Buat migration                            │
│  □ Buat seeder                               │
│  □ Buat tests                                │
│  □ Verifikasi tidak ada hardcoded values     │
│  □ Verifikasi komunikasi via contract/event  │
│                                               │
└──────────────────────────────────────────────┘
```

### 22.3. Template Prompt AI untuk Membuat Module

```
Buat module baru untuk CosmicLib Engine:

Nama Module    : {NamaModule}
Slug           : {nama-module}
Deskripsi      : {deskripsi singkat}
Dependencies   : {daftar dependency}

Ikuti spesifikasi docs/08_MODULE_ENGINE.md:
1. Buat struktur direktori standar
2. Buat module.json lengkap
3. Buat README.md module
4. Definisikan permission di Config/permission.php
5. Definisikan menu di Config/menu.php
6. Buat Config/config.php
7. Buat Service Provider
8. Buat migration
9. Buat seeder
10. Buat tests (unit + feature)
11. Gunakan Service Layer + Repository Pattern
12. Komunikasi antar module via contract/event
```

---

## 23. Best Practice

### 23.1. Prinsip Pengembangan Module

| Prinsip | Penjelasan |
|---|---|
| **SOLID** | Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion. |
| **PSR-12** | Extended Coding Style Guide untuk PHP. |
| **Service Layer** | Business logic di Service class, bukan di Controller. |
| **Repository Pattern** | Data access melalui Repository, bukan langsung Eloquent di Controller/Service. |
| **Dependency Injection** | Inject dependency melalui constructor, bukan manual instantiation. |
| **Contract First** | Definisikan interface/contract sebelum implementasi. |
| **Configuration over Hardcode** | Gunakan config dan setting, jangan hardcode nilai. |

### 23.2. Larangan

| # | Larangan |
|---|---|
| 1 | ❌ Tidak boleh hardcode role — semua role dari Permission Engine. |
| 2 | ❌ Tidak boleh hardcode permission — semua permission dari Permission Engine. |
| 3 | ❌ Tidak boleh hardcode menu — semua menu dari Menu Engine. |
| 4 | ❌ Tidak boleh hardcode warna — semua warna dari Theme Engine. |
| 5 | ❌ Tidak boleh hardcode config — semua config dari Setting Engine. |
| 6 | ❌ Tidak boleh `env()` di luar file config. |
| 7 | ❌ Tidak boleh `dd()` atau `dump()` di production code. |
| 8 | ❌ Tidak boleh raw SQL tanpa parameter binding. |
| 9 | ❌ Tidak boleh business logic di Controller. |
| 10 | ❌ Tidak boleh akses model module lain secara langsung. |

### 23.3. Konvensi Penamaan

| Elemen | Konvensi | Contoh |
|---|---|---|
| Module Name | PascalCase | `Library`, `SystemSetting` |
| Module Slug | kebab-case | `library`, `system-setting` |
| Table Name | snake_case, prefixed | `library_books`, `library_members` |
| Model | PascalCase, singular | `Book`, `Member`, `BorrowRecord` |
| Controller | PascalCase + Controller | `BookController`, `MemberController` |
| Service | PascalCase + Service | `BookService`, `BorrowService` |
| Repository | PascalCase + Repository | `BookRepository`, `MemberRepository` |
| Interface | PascalCase + Interface | `BookServiceInterface`, `BookRepositoryInterface` |
| Policy | PascalCase + Policy | `BookPolicy`, `MemberPolicy` |
| Event | PascalCase | `LibraryBookCreated`, `LibraryBorrowReturned` |
| Migration | timestamp_description | `2026_01_01_000001_create_books_table` |
| Permission | dot-notation | `library.books.view`, `library.books.create` |
| Config Key | dot-notation | `library.max_borrow_days` |
| Route Name | dot-notation | `library.books.index`, `library.books.store` |

### 23.4. Struktur Controller (Tipis)

Controller **hanya** bertanggung jawab untuk:

1. Menerima request.
2. Memanggil service.
3. Mengembalikan response.

```
Request → Controller → Service → Repository → Model → Database
                ↑           ↑
          Form Request  Business Logic
          (Validation)  (di Service)
```

---

## 24. Architecture Diagram

### 24.1. Request Flow

```
┌──────────┐
│  Client  │  (Browser / Mobile / API Consumer)
└────┬─────┘
     │ HTTP Request
     ▼
┌──────────────────────────────────────────────┐
│              CORE ENGINE                      │
│  ┌────────────────────────────────────────┐  │
│  │  Middleware Pipeline                    │  │
│  │  (Auth, CORS, Rate Limit, Module Gate) │  │
│  └───────────────┬────────────────────────┘  │
│                  │                            │
│  ┌───────────────▼────────────────────────┐  │
│  │         MODULE LOADER                   │  │
│  │  ┌──────────────────────────────────┐  │  │
│  │  │  Scan → Validate → Resolve Deps  │  │  │
│  │  │  → Sort Priority → Load          │  │  │
│  │  └──────────────────────────────────┘  │  │
│  └───────────────┬────────────────────────┘  │
└──────────────────┼───────────────────────────┘
                   │
     ┌─────────────┼─────────────┐
     ▼             ▼             ▼
┌─────────┐  ┌─────────┐  ┌─────────┐
│Module A │  │Module B │  │Module C │
├─────────┤  ├─────────┤  ├─────────┤
│         │  │         │  │         │
│ Provider│  │ Provider│  │ Provider│
│    │    │  │    │    │  │    │    │
│    ▼    │  │    ▼    │  │    ▼    │
│Controller│ │Controller│ │Controller│
│    │    │  │    │    │  │    │    │
│    ▼    │  │    ▼    │  │    ▼    │
│ Service │  │ Service │  │ Service │
│    │    │  │    │    │  │    │    │
│    ▼    │  │    ▼    │  │    ▼    │
│Repository│ │Repository│ │Repository│
│    │    │  │    │    │  │    │    │
│    ▼    │  │    ▼    │  │    ▼    │
│  Model  │  │  Model  │  │  Model  │
│    │    │  │    │    │  │    │    │
│    ▼    │  │    ▼    │  │    ▼    │
│Database │  │Database │  │Database │
│         │  │         │  │         │
└─────────┘  └─────────┘  └─────────┘
```

### 24.2. Module Internal Architecture

```
┌─────────────────────────────────────────────────────┐
│                MODULE ARCHITECTURE                    │
├─────────────────────────────────────────────────────┤
│                                                      │
│  ┌──────────┐    ┌────────────┐    ┌─────────────┐  │
│  │  Routes  │───▶│ Controller │───▶│ Form Request│  │
│  │ web/api  │    │   (tipis)  │    │ (validasi)  │  │
│  └──────────┘    └─────┬──────┘    └─────────────┘  │
│                        │                             │
│                        ▼                             │
│                  ┌───────────┐                       │
│                  │  Service  │  ◄── Business Logic   │
│                  │  Layer    │                       │
│                  └─────┬─────┘                       │
│                        │                             │
│              ┌─────────┼─────────┐                   │
│              ▼                   ▼                    │
│        ┌───────────┐      ┌───────────┐             │
│        │Repository │      │  Event    │             │
│        │  Layer    │      │ Dispatch  │             │
│        └─────┬─────┘      └───────────┘             │
│              │                                       │
│              ▼                                       │
│        ┌───────────┐                                 │
│        │   Model   │  ◄── Eloquent ORM              │
│        │  + Policy │                                 │
│        └─────┬─────┘                                 │
│              │                                       │
│              ▼                                       │
│        ┌───────────┐                                 │
│        │ Database  │                                 │
│        └───────────┘                                 │
│                                                      │
└─────────────────────────────────────────────────────┘
```

### 24.3. Inter-Module Communication

```
┌──────────────────────────────────────────────────────────────────┐
│              INTER-MODULE COMMUNICATION                           │
├──────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Module A                    Module B                             │
│  ┌──────────┐               ┌──────────┐                        │
│  │          │  ─ Contract ─▶│          │                        │
│  │ Service  │               │ Service  │                        │
│  │          │  ◀─ Binding ──│          │                        │
│  └──────────┘               └──────────┘                        │
│                                                                   │
│  Module C                    Module D                             │
│  ┌──────────┐    Event      ┌──────────┐                        │
│  │          │ ═══════════▶  │          │                        │
│  │ Service  │  Dispatch     │ Listener │                        │
│  │          │               │          │                        │
│  └──────────┘               └──────────┘                        │
│                                                                   │
│  ✅ Contract (Interface)   — Sinkron, type-safe                  │
│  ✅ Event/Listener         — Asinkron, loosely coupled           │
│  ✅ Service Container      — Resolve via binding                 │
│  ❌ Direct Import          — DILARANG                            │
│  ❌ Direct DB Access       — DILARANG                            │
│                                                                   │
└──────────────────────────────────────────────────────────────────┘
```

---

## 25. Checklist

### 25.1. Checklist Pembuatan Module Baru

| # | Item | Keterangan |
|---|---|---|
| ✅ | `module.json` | Manifest lengkap dengan semua property wajib. |
| ✅ | `README.md` | Dokumentasi module — cara install, konfigurasi, penggunaan. |
| ✅ | `Database/Migrations/` | Skema database module. |
| ✅ | `Database/Seeders/` | Data awal dan data referensi. |
| ✅ | `Routes/web.php` | Web routes module. |
| ✅ | `Routes/api.php` | API routes module. |
| ✅ | `Config/permission.php` | Definisi permission module. |
| ✅ | `Config/menu.php` | Definisi menu module. |
| ✅ | `Config/config.php` | Konfigurasi default module. |
| ✅ | `Providers/ServiceProvider.php` | Service Provider utama. |
| ✅ | `Services/` | Business logic layer. |
| ✅ | `Repositories/` | Data access layer dengan contract. |
| ✅ | `Http/Controllers/` | Controller tipis. |
| ✅ | `Http/Requests/` | Form Request validation. |
| ✅ | `Models/` | Eloquent model. |
| ✅ | `Policies/` | Authorization policy. |
| ✅ | `Tests/Unit/` | Unit test. |
| ✅ | `Tests/Feature/` | Feature test. |
| ✅ | Dokumentasi | Semua fitur terdokumentasi. |

### 25.2. Checklist Review Module

| # | Pertanyaan Review | Jawaban Diharapkan |
|---|---|---|
| 1 | Apakah module memiliki `module.json` yang valid? | Ya |
| 2 | Apakah semua dependency dideklarasikan? | Ya |
| 3 | Apakah business logic ada di Service Layer? | Ya |
| 4 | Apakah Controller tipis (tidak ada business logic)? | Ya |
| 5 | Apakah semua input divalidasi via Form Request? | Ya |
| 6 | Apakah komunikasi antar-module via contract/event? | Ya |
| 7 | Apakah tidak ada hardcoded value? | Ya |
| 8 | Apakah permission terdefinisi dengan benar? | Ya |
| 9 | Apakah menu terdefinisi dengan benar? | Ya |
| 10 | Apakah ada unit test dan feature test? | Ya |
| 11 | Apakah UI menggunakan Bahasa Indonesia? | Ya |
| 12 | Apakah code menggunakan Bahasa Inggris? | Ya |
| 13 | Apakah mengikuti PSR-12? | Ya |
| 14 | Apakah mengikuti prinsip SOLID? | Ya |
| 15 | Apakah dokumentasi lengkap? | Ya |

---

## Catatan Perubahan

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0.0 | 2026-07-18 | Dokumen awal — spesifikasi lengkap Module Engine. |

---

> **Dokumen ini adalah standar resmi Module Engine CosmicLib.**
> Setiap module, developer, dan AI agent **wajib** mengikuti spesifikasi ini.
> Perubahan pada dokumen ini memerlukan persetujuan Principal Architect.