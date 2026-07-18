# 🌌 07 — Core Engine

> **Spesifikasi resmi Core Engine** — jantung dari seluruh CosmicLib Engine.
>
> Semua engine lain bergantung pada Core Engine. Core Engine adalah satu-satunya komponen yang **tidak dapat dinonaktifkan**.
>
> Baca setelah [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) dan [`06_DATABASE_DESIGN.md`](06_DATABASE_DESIGN.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/07_CORE_ENGINE.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Final Blueprint` — spesifikasi resmi Core Engine |
| **Engine** | CosmicLib Engine |
| **Framework** | Laravel 12 · PHP 8.3+ · MySQL 8+ |
| **Arsitektur** | Modular CMS Engine (Modular Monolith) |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi Core Engine](#2-filosofi-core-engine)
3. [Core Responsibilities](#3-core-responsibilities)
4. [Engine Lifecycle](#4-engine-lifecycle)
5. [Core Components](#5-core-components)
6. [Service Container](#6-service-container)
7. [Service Provider](#7-service-provider)
8. [Configuration Engine](#8-configuration-engine)
9. [Module Loader](#9-module-loader)
10. [Theme Loader](#10-theme-loader)
11. [Plugin Loader](#11-plugin-loader)
12. [Widget Loader](#12-widget-loader)
13. [Event System](#13-event-system)
14. [Configuration Hierarchy](#14-configuration-hierarchy)
15. [Core Helper](#15-core-helper)
16. [Core Services](#16-core-services)
17. [Error Handling](#17-error-handling)
18. [Performance](#18-performance)
19. [Security](#19-security)
20. [AI Rules](#20-ai-rules)
21. [Future Roadmap](#21-future-roadmap)
22. [Architecture Diagram](#22-architecture-diagram)
23. [Dependency Rules](#23-dependency-rules)
24. [Best Practice](#24-best-practice)
25. [Checklist](#25-checklist)

---

## 1. Pendahuluan

### 1.1 Apa Itu Core Engine?

Core Engine adalah **fondasi utama** seluruh sistem CosmicLib. Core Engine menyediakan layanan dasar yang dibutuhkan oleh semua engine, modul, plugin, theme, dan widget untuk berfungsi dengan benar.

Core Engine bertanggung jawab atas:

- **Inisialisasi aplikasi** — bootstrap seluruh sistem dari titik masuk HTTP hingga respons akhir.
- **Orkestrasi engine** — memuat, meregistrasi, dan mengkoordinasikan seluruh engine dalam ekosistem.
- **Layanan infrastruktur** — menyediakan konfigurasi, caching, logging, event, queue, dan utilitas bersama.
- **Kontrak dan standar** — menetapkan interface dan konvensi yang wajib diikuti seluruh komponen.

### 1.2 Mengapa Core Engine Menjadi Fondasi?

| Alasan | Penjelasan |
| :--- | :--- |
| **Single Point of Bootstrap** | Seluruh engine dimuat dan diinisialisasi melalui Core Engine. |
| **Shared Services** | Konfigurasi, caching, logging, dan event dikelola terpusat. |
| **Consistency** | Standar kontrak dan konvensi ditegakkan dari satu titik. |
| **Dependency Resolution** | Service Container dan Dependency Injection dikelola oleh Core. |
| **Lifecycle Management** | Siklus hidup request dan boot aplikasi dikendalikan Core Engine. |
| **Cannot Be Disabled** | Core Engine adalah satu-satunya komponen yang tidak dapat dinonaktifkan. |

> **Keputusan penting:** Core Engine bukan fitur — Core Engine adalah **platform**. Tanpa Core Engine, tidak ada engine lain yang dapat beroperasi.

---

## 2. Filosofi Core Engine

### 2.1 Core Engine Bukan Aplikasi

Core Engine **bukan** aplikasi perpustakaan, bukan modul katalog, bukan dashboard admin. Core Engine adalah **platform layanan** yang menyediakan infrastruktur dasar bagi seluruh komponen sistem.

```text
┌──────────────────────────────────────────┐
│            Aplikasi (Modules)            │   ← Fitur bisnis
├──────────────────────────────────────────┤
│          Engine Ecosystem                │   ← Kapabilitas platform
├──────────────────────────────────────────┤
│          ★ CORE ENGINE ★                 │   ← Fondasi layanan
├──────────────────────────────────────────┤
│          Laravel Framework               │   ← Framework dasar
└──────────────────────────────────────────┘
```

### 2.2 Prinsip Desain Core Engine

| Prinsip | Implementasi |
| :--- | :--- |
| **Modular** | Setiap tanggung jawab Core dipisah menjadi komponen mandiri (Service, Provider, Helper). |
| **Extensible** | Titik ekstensi resmi melalui Service Provider, Event, dan Contract. |
| **Maintainable** | Kode terstruktur, terdokumentasi, mudah dipahami manusia dan AI. |
| **AI Friendly** | Naming konsisten, kontrak eksplisit, dokumentasi lengkap. |
| **Enterprise Ready** | Mendukung skala besar, multi-role, audit trail, dan security layer. |
| **Shared Hosting Friendly** | Tidak bergantung Redis/Memcached wajib; file cache sebagai default. |

### 2.3 Core Engine Sebagai Platform

Core Engine menyediakan **layanan dasar** yang dikonsumsi oleh seluruh modul dan engine:

```text
  Module A ──┐
  Module B ──┤
  Module C ──┤──▶  Core Engine Services
  Plugin X ──┤      ├── Configuration
  Theme Y  ──┤      ├── Authentication
  Widget Z ──┘      ├── Authorization
                    ├── Event Bus
                    ├── Caching
                    ├── Logging
                    ├── Queue
                    └── Helpers & Utilities
```

> **Filosofi inti:** Seluruh modul **menggunakan** layanan Core Engine. Tidak ada modul yang boleh melewati atau menduplikasi layanan Core.

---

## 3. Core Responsibilities

Core Engine bertanggung jawab terhadap area berikut:

| # | Area | Tanggung Jawab | Prioritas |
| :--- | :--- | :--- | :--- |
| 1 | **Application Bootstrap** | Inisialisasi aplikasi, registrasi service provider, boot engine | Kritis |
| 2 | **Configuration** | Memuat dan mengelola konfigurasi dari berbagai sumber | Kritis |
| 3 | **Dependency Injection** | Resolusi dependensi melalui Service Container | Kritis |
| 4 | **Service Container** | Binding, singleton, dan resolusi otomatis | Kritis |
| 5 | **Service Provider** | Registrasi dan boot seluruh komponen sistem | Kritis |
| 6 | **Event System** | Event bus untuk komunikasi antar komponen | Kritis |
| 7 | **Logging** | Pencatatan log aplikasi, error, dan audit | Kritis |
| 8 | **Queue** | Antrian pekerjaan asinkron (disesuaikan hosting) | Penting |
| 9 | **Cache** | Caching konfigurasi, menu, permission, data | Penting |
| 10 | **Localization** | Dukungan multi-bahasa, default Bahasa Indonesia | Penting |
| 11 | **Helper** | Fungsi global untuk akses cepat layanan engine | Penting |
| 12 | **Utilities** | Utilitas umum: string, date, file, format | Pendukung |
| 13 | **Scheduler** | Penjadwalan tugas berkala (backup, cleanup, notifikasi) | Pendukung |
| 14 | **Exception Handler** | Penanganan error terpusat, reporting, recovery | Kritis |

### Matriks Tanggung Jawab

```text
Core Engine
├── Bootstrap ──────────── Inisialisasi & registrasi
├── Configuration ──────── Memuat config dari semua sumber
├── DI Container ───────── Binding & resolusi dependensi
├── Service Provider ───── Registrasi komponen
├── Event System ───────── Event, Listener, Observer
├── Logging ────────────── Application, Error, Audit log
├── Queue ──────────────── Job dispatch & processing
├── Cache ──────────────── File/database cache
├── Localization ───────── i18n & l10n
├── Helper ─────────────── Global helper functions
├── Utilities ──────────── Utilitas umum
├── Scheduler ──────────── Task scheduling
└── Exception Handler ──── Error handling & recovery
```

---

## 4. Engine Lifecycle

### 4.1 Siklus Request Lengkap

Berikut adalah siklus hidup lengkap setiap request yang diproses oleh Core Engine:

```text
┌──────────────────────────────────────────────────────────────┐
│                    ENGINE LIFECYCLE                           │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│   Client (Browser / API Client)                              │
│       │                                                      │
│       ▼                                                      │
│   HTTP Request                                               │
│       │                                                      │
│       ▼                                                      │
│   ┌─────────────────────────────────┐                        │
│   │  1. Bootstrap                   │                        │
│   │     • Autoload                  │                        │
│   │     • Service Container         │                        │
│   │     • Environment Detection     │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  2. Load Configuration          │                        │
│   │     • .env → config/ → DB      │                        │
│   │     • Merge & cache             │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  3. Load Module                 │                        │
│   │     • Deteksi modul aktif       │                        │
│   │     • Registrasi route          │                        │
│   │     • Registrasi permission     │                        │
│   │     • Registrasi menu           │                        │
│   │     • Registrasi event          │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  4. Load Theme                  │                        │
│   │     • Deteksi tema aktif        │                        │
│   │     • Load layout & assets      │                        │
│   │     • Apply CSS variables       │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  5. Authentication              │                        │
│   │     • Session / Token check     │                        │
│   │     • Guard resolution          │                        │
│   │     • User identification       │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  6. Authorization               │                        │
│   │     • Permission check          │                        │
│   │     • Policy evaluation         │                        │
│   │     • Gate middleware            │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  7. Business Logic              │                        │
│   │     • Controller (thin)         │                        │
│   │     • Form Request validation   │                        │
│   │     • Service Layer             │                        │
│   │     • Repository                │                        │
│   │     • Model / Database          │                        │
│   └──────────────┬──────────────────┘                        │
│                  ▼                                            │
│   ┌─────────────────────────────────┐                        │
│   │  8. Response                    │                        │
│   │     • Blade view / JSON         │                        │
│   │     • Theme rendering           │                        │
│   │     • HTTP response             │                        │
│   └─────────────────────────────────┘                        │
│       │                                                      │
│       ▼                                                      │
│   Client (Response Diterima)                                 │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 4.2 Detail Setiap Fase

| Fase | Komponen | Tanggung Jawab |
| :--- | :--- | :--- |
| **Bootstrap** | `index.php`, `bootstrap/app.php` | Autoload Composer, inisialisasi Service Container, deteksi environment |
| **Load Configuration** | Configuration Engine | Memuat `.env`, file `config/`, sinkronisasi dengan database Setting Engine |
| **Load Module** | Module Loader | Scan direktori modul, baca `module.json`, registrasi komponen modul aktif |
| **Load Theme** | Theme Loader | Identifikasi tema aktif, load layout, registrasi view namespace |
| **Authentication** | Auth Guard / Session | Verifikasi identitas pengguna via session atau token |
| **Authorization** | Permission Engine | Evaluasi hak akses berdasarkan role dan permission |
| **Business Logic** | Controller → Service → Repository | Proses logika bisnis melalui Service Layer Pattern |
| **Response** | Blade / JSON Resource | Render output akhir dan kirim ke client |

### 4.3 Boot Sequence Engine

Urutan boot engine sangat kritis. Perubahan urutan dapat menyebabkan error fatal.

```text
Boot Order:
  1. Core Engine          ← Wajib pertama
  2. Setting Engine       ← Konfigurasi database
  3. Permission Engine    ← RBAC & gates
  4. Module Engine        ← Deteksi & load modul
  5. Menu Engine          ← Navigasi dinamis
  6. Theme Engine         ← Visual & layout
  7. Plugin Engine        ← Ekstensi pihak ketiga
  8. Widget Engine        ← Dashboard widgets
  9. Media Engine         ← File management
  10. Notification Engine ← Sistem notifikasi
  11. Backup Engine       ← Backup & restore
  12. Update Engine       ← Pembaruan sistem
  13. License Engine      ← Validasi lisensi
```

> **Peringatan:** Urutan boot di atas adalah **kontrak arsitektur**. Mengubah urutan boot tanpa analisis dampak penuh **dilarang**.

---

## 5. Core Components

### 5.1 Daftar Komponen Inti

| # | Komponen | Namespace | Tanggung Jawab |
| :--- | :--- | :--- | :--- |
| 1 | **Core Service** | `App\Core\Services\` | Layanan bisnis inti yang digunakan lintas engine |
| 2 | **Core Repository** | `App\Core\Repositories\` | Akses data terpusat untuk entitas inti |
| 3 | **Core Provider** | `App\Core\Providers\` | Service provider untuk registrasi dan boot komponen |
| 4 | **Core Helper** | `App\Core\Helpers\` | Fungsi global untuk akses cepat layanan engine |
| 5 | **Core Utility** | `App\Core\Utilities\` | Utilitas umum (string, date, format, file) |
| 6 | **Core Config** | `config/cosmiclib/` | File konfigurasi khusus CosmicLib |
| 7 | **Core Event** | `App\Core\Events\` | Event inti yang di-dispatch oleh Core Engine |
| 8 | **Core Middleware** | `App\Core\Middleware\` | Middleware inti (locale, theme, module detection) |
| 9 | **Core Command** | `App\Core\Commands\` | Artisan command untuk operasi sistem |
| 10 | **Core Exception** | `App\Core\Exceptions\` | Exception handler dan custom exception |
| 11 | **Core Facade** | `App\Core\Facades\` | Facade untuk akses statis ke service inti |
| 12 | **Core Contract** | `App\Core\Contracts\` | Interface/kontrak yang wajib diimplementasikan |

### 5.2 Detail Komponen

#### Core Service

Core Service menyediakan layanan bisnis inti yang dapat digunakan oleh semua engine dan modul.

| Service | Fungsi |
| :--- | :--- |
| `ConfigurationService` | Membaca dan menulis konfigurasi dari berbagai sumber |
| `BootstrapService` | Inisialisasi dan orkestrasi boot sequence |
| `CacheService` | Abstraksi caching yang hosting-friendly |
| `LogService` | Pencatatan log terpusat |
| `LocalizationService` | Manajemen bahasa dan terjemahan |

#### Core Repository

Core Repository menyediakan akses data untuk entitas inti sistem.

| Repository | Fungsi |
| :--- | :--- |
| `SettingRepository` | Akses data setting dari database |
| `ModuleRepository` | Akses data status modul |
| `AuditRepository` | Akses data audit log |

#### Core Provider

Core Provider adalah service provider utama yang meregistrasikan seluruh komponen Core Engine.

| Provider | Fungsi |
| :--- | :--- |
| `CoreServiceProvider` | Registrasi service, binding, dan singleton inti |
| `CoreEventServiceProvider` | Registrasi event dan listener inti |
| `CoreMiddlewareProvider` | Registrasi middleware inti |

#### Core Helper

Fungsi global yang tersedia di seluruh aplikasi. Lihat [Bagian 15](#15-core-helper) untuk daftar lengkap.

#### Core Utility

| Utility | Fungsi |
| :--- | :--- |
| `StringUtility` | Manipulasi string (slug, excerpt, sanitize) |
| `DateUtility` | Format tanggal Indonesia, kalkulasi hari kerja |
| `FileUtility` | Operasi file (size format, extension check, mime detection) |
| `FormatUtility` | Format angka, mata uang, nomor telepon Indonesia |

#### Core Config

File konfigurasi khusus CosmicLib di direktori `config/cosmiclib/`:

| File Config | Fungsi |
| :--- | :--- |
| `core.php` | Konfigurasi inti (versi, nama, debug mode) |
| `modules.php` | Konfigurasi module loader |
| `themes.php` | Konfigurasi theme loader |
| `plugins.php` | Konfigurasi plugin loader |
| `widgets.php` | Konfigurasi widget loader |
| `cache.php` | Override cache untuk CosmicLib |
| `logging.php` | Konfigurasi logging channel |

#### Core Event

Event inti yang di-dispatch oleh Core Engine:

| Event | Kapan Di-dispatch |
| :--- | :--- |
| `SystemBooted` | Setelah seluruh engine selesai boot |
| `ConfigurationLoaded` | Setelah konfigurasi dimuat |
| `ModuleLoaded` | Setelah sebuah modul dimuat |
| `ThemeActivated` | Setelah tema diaktifkan |
| `SettingChanged` | Setelah setting diubah |
| `UserLoggedIn` | Setelah pengguna berhasil login |
| `UserLoggedOut` | Setelah pengguna logout |
| `PermissionChanged` | Setelah permission diperbarui |
| `CacheCleared` | Setelah cache dibersihkan |

#### Core Middleware

| Middleware | Fungsi |
| :--- | :--- |
| `SetLocale` | Mengatur locale berdasarkan setting pengguna / sistem |
| `LoadTheme` | Memuat tema aktif ke dalam view |
| `DetectModule` | Mendeteksi modul aktif berdasarkan route |
| `CheckSystemReady` | Memverifikasi sistem telah terinstal dan terkonfigurasi |
| `LogRequest` | Mencatat request untuk audit (opsional) |

#### Core Command

| Command | Fungsi |
| :--- | :--- |
| `cosmiclib:install` | Wizard instalasi awal |
| `cosmiclib:cache` | Cache konfigurasi, route, dan view |
| `cosmiclib:clear` | Bersihkan semua cache |
| `cosmiclib:status` | Tampilkan status sistem dan engine |
| `cosmiclib:module:list` | Daftar modul terpasang |
| `cosmiclib:module:enable` | Aktifkan modul |
| `cosmiclib:module:disable` | Nonaktifkan modul |

#### Core Exception

| Exception | Fungsi |
| :--- | :--- |
| `CoreException` | Base exception untuk Core Engine |
| `ModuleNotFoundException` | Modul yang diminta tidak ditemukan |
| `ThemeNotFoundException` | Tema yang diminta tidak ditemukan |
| `ConfigurationException` | Kesalahan konfigurasi sistem |
| `EngineBootException` | Kegagalan saat boot engine |
| `PermissionDeniedException` | Akses ditolak (wrapper friendly) |

#### Core Facade

| Facade | Service yang Di-wrap |
| :--- | :--- |
| `CosmicLib` | `CoreService` — akses utama ke Core Engine |
| `ModuleManager` | `ModuleService` — manajemen modul |
| `ThemeManager` | `ThemeService` — manajemen tema |
| `SettingManager` | `SettingService` — manajemen setting |

#### Core Contract

| Contract (Interface) | Fungsi |
| :--- | :--- |
| `ModuleInterface` | Kontrak yang wajib diimplementasikan setiap modul |
| `ThemeInterface` | Kontrak yang wajib diimplementasikan setiap tema |
| `PluginInterface` | Kontrak yang wajib diimplementasikan setiap plugin |
| `WidgetInterface` | Kontrak yang wajib diimplementasikan setiap widget |
| `EngineInterface` | Kontrak dasar untuk semua engine |
| `BootableInterface` | Kontrak untuk komponen yang memiliki fase boot |
| `ConfigurableInterface` | Kontrak untuk komponen yang dapat dikonfigurasi |
| `InstallableInterface` | Kontrak untuk komponen yang dapat diinstal/diuninstal |

---

## 6. Service Container

### 6.1 Penggunaan Laravel Service Container

Core Engine memanfaatkan **Laravel Service Container** sebagai inti resolusi dependensi. Service Container bertindak sebagai registry terpusat untuk semua service, repository, dan komponen sistem.

### 6.2 Dependency Injection

Semua dependensi di-resolve melalui **constructor injection** untuk memastikan:

- **Testability** — mudah di-mock saat pengujian.
- **Loose coupling** — komponen tidak bergantung pada implementasi konkret.
- **Transparency** — dependensi terlihat jelas di constructor.

```text
Controller
    │
    ├── inject: BookService (via constructor)
    │       │
    │       ├── inject: BookRepository (via constructor)
    │       │       │
    │       │       └── uses: Book (Eloquent Model)
    │       │
    │       └── inject: CacheService (via constructor)
    │
    └── inject: FormRequest (via method injection)
```

### 6.3 Binding

Core Engine melakukan binding di `CoreServiceProvider`:

| Tipe Binding | Penggunaan | Contoh |
| :--- | :--- | :--- |
| **Interface to Implementation** | Kontrak ke implementasi konkret | `ModuleInterface` → `ModuleService` |
| **Contextual Binding** | Implementasi berbeda berdasarkan context | Controller A → `FileLogger`, Controller B → `DatabaseLogger` |
| **Primitive Binding** | Nilai primitif ke parameter | `$cacheTtl` → `3600` |

### 6.4 Singleton

Beberapa service didaftarkan sebagai **singleton** untuk efisiensi:

| Singleton Service | Alasan |
| :--- | :--- |
| `ConfigurationService` | Konfigurasi hanya perlu dimuat sekali per request |
| `ModuleService` | Daftar modul tidak berubah selama request |
| `ThemeService` | Tema aktif tetap sama selama request |
| `PermissionService` | Permission pengguna di-cache per request |
| `MenuService` | Menu dibangun sekali per request |
| `SettingService` | Setting dibaca sekali dan di-cache |

> **Keputusan penting:** Singleton digunakan untuk service yang **read-heavy** dan **immutable selama satu request**. Service yang bersifat transaksional (write-heavy) **tidak** boleh singleton.

---

## 7. Service Provider

### 7.1 Daftar Provider Utama

Berikut adalah service provider utama yang diregistrasikan oleh Core Engine:

| # | Provider | Tanggung Jawab | Boot Order |
| :--- | :--- | :--- | :--- |
| 1 | `AppServiceProvider` | Binding umum aplikasi, konfigurasi Eloquent | 1 |
| 2 | `RouteServiceProvider` | Registrasi route web dan API | 2 |
| 3 | `SettingServiceProvider` | Memuat setting dari database, merge ke config | 3 |
| 4 | `PermissionServiceProvider` | Registrasi gate, policy, dan middleware permission | 4 |
| 5 | `ModuleServiceProvider` | Deteksi dan registrasi modul aktif | 5 |
| 6 | `MenuServiceProvider` | Bangun navigasi dinamis berdasarkan permission | 6 |
| 7 | `ThemeServiceProvider` | Load tema aktif, registrasi view namespace | 7 |
| 8 | `PluginServiceProvider` | Deteksi dan registrasi plugin aktif | 8 |
| 9 | `WidgetServiceProvider` | Registrasi widget yang tersedia | 9 |

### 7.2 Tanggung Jawab Setiap Provider

#### AppServiceProvider

- Binding umum aplikasi.
- Konfigurasi Eloquent (strict mode, prevent lazy loading).
- Registrasi macro dan mixin global.
- Boot health check.

#### RouteServiceProvider

- Registrasi route group: `web`, `api`, `admin`.
- Rate limiting configuration.
- Route model binding.
- Delegasi registrasi route ke Module Engine.

#### ModuleServiceProvider

- Scan direktori `modules/` untuk modul yang tersedia.
- Membaca `module.json` setiap modul.
- Memverifikasi dependensi modul.
- Meregistrasi route, permission, menu, event, migration, dan seeder modul aktif.
- Dispatch event `ModuleLoaded` untuk setiap modul.

#### ThemeServiceProvider

- Identifikasi tema aktif dari Setting Engine.
- Load konfigurasi tema (`theme.json`).
- Registrasi view namespace dan path.
- Publish asset tema via Vite.

#### PluginServiceProvider

- Scan direktori `plugins/` untuk plugin yang tersedia.
- Verifikasi dependensi dan kompatibilitas versi.
- Registrasi plugin aktif (route, event, middleware).
- Dispatch event `PluginLoaded`.

#### WidgetServiceProvider

- Registrasi widget yang tersedia dari modul, tema, dan plugin.
- Binding widget ke dashboard berdasarkan konfigurasi pengguna.
- Validasi permission widget.

#### SettingServiceProvider

- Memuat setting dari tabel `settings` di database.
- Merge setting database ke dalam Laravel `config()`.
- Menyediakan fallback ke file config jika database belum tersedia.
- Cache setting untuk performa.

### 7.3 Siklus Service Provider

```text
┌─────────────────────────────────────────┐
│         Service Provider Lifecycle       │
├─────────────────────────────────────────┤
│                                         │
│   1. register()                         │
│      • Binding interface → implementation│
│      • Singleton registration           │
│      • Merge config                     │
│                                         │
│   2. boot()                             │
│      • Load routes                      │
│      • Register middleware              │
│      • Register event listeners         │
│      • Publish assets                   │
│      • Execute boot logic              │
│                                         │
└─────────────────────────────────────────┘
```

> **Aturan:** Jangan gunakan `boot()` untuk binding. Jangan gunakan `register()` untuk logika yang bergantung pada service lain.

---

## 8. Configuration Engine

### 8.1 Sumber Konfigurasi

Semua konfigurasi CosmicLib berasal dari empat sumber utama:

| # | Sumber | Lokasi | Tipe |
| :--- | :--- | :--- | :--- |
| 1 | **Environment** | `.env` | Secrets, bootstrap, environment-specific |
| 2 | **File Config** | `config/` | Konfigurasi statis aplikasi |
| 3 | **Database** | Tabel `settings` | Konfigurasi dinamis runtime |
| 4 | **System Settings** | Setting Engine | Konfigurasi bisnis yang bisa diubah admin |

### 8.2 Apa yang Disimpan di Mana?

| Tipe Data | Sumber | Contoh |
| :--- | :--- | :--- |
| **Secrets** | `.env` | `DB_PASSWORD`, `APP_KEY`, `MAIL_PASSWORD` |
| **Environment** | `.env` | `APP_ENV`, `APP_DEBUG`, `DB_HOST` |
| **Framework Config** | `config/` | Cache driver, session driver, mail driver |
| **CosmicLib Config** | `config/cosmiclib/` | Modul default, tema default |
| **Bisnis Config** | Database (`settings`) | Nama sekolah, lama pinjam, denda per hari |
| **User Preference** | Database (`user_settings`) | Bahasa, tema, timezone per pengguna |

### 8.3 Prioritas Konfigurasi

Saat terjadi konflik, konfigurasi dengan prioritas lebih tinggi menang:

```text
Prioritas (tinggi ke rendah):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  1. User Configuration     ← Preferensi pengguna (per user)
  2. Module Config           ← Konfigurasi spesifik modul
  3. Database (Settings)     ← Konfigurasi runtime dari admin
  4. config/ files           ← Konfigurasi statis Laravel
  5. .env                    ← Environment variables
```

### 8.4 Alur Resolusi Konfigurasi

```text
setting('library.loan_days')
        │
        ▼
   ┌─────────────┐     Ada?     ┌──────────────┐
   │ User Config  │────Yes──────▶│ Return value │
   └──────┬──────┘              └──────────────┘
          │ No
          ▼
   ┌──────────────┐    Ada?     ┌──────────────┐
   │ Module Config │────Yes─────▶│ Return value │
   └──────┬───────┘             └──────────────┘
          │ No
          ▼
   ┌──────────────┐    Ada?     ┌──────────────┐
   │ DB Settings   │────Yes─────▶│ Return value │
   └──────┬───────┘             └──────────────┘
          │ No
          ▼
   ┌──────────────┐    Ada?     ┌──────────────┐
   │ config/ file  │────Yes─────▶│ Return value │
   └──────┬───────┘             └──────────────┘
          │ No
          ▼
   ┌──────────────┐    Ada?     ┌──────────────┐
   │ .env          │────Yes─────▶│ Return value │
   └──────┬───────┘             └──────────────┘
          │ No
          ▼
   ┌──────────────┐
   │ Default value │
   └──────────────┘
```

> **Keputusan penting:** `env()` **hanya** boleh digunakan di dalam file `config/`. Tidak boleh dipanggil langsung di controller, service, atau view. Nilai bisnis runtime menggunakan Setting Engine.

---

## 9. Module Loader

### 9.1 Tanggung Jawab Module Loader

Module Loader adalah subsistem Core Engine yang bertanggung jawab mendeteksi, memvalidasi, dan memuat modul ke dalam sistem.

| Tanggung Jawab | Detail |
| :--- | :--- |
| **Mendeteksi Module** | Scan direktori `modules/` untuk modul yang tersedia |
| **Memuat Module** | Parse `module.json`, validasi struktur, load komponen |
| **Registrasi Route** | Load `routes.php` modul ke dalam Laravel Router |
| **Registrasi Permission** | Load `permissions.php` ke Permission Engine |
| **Registrasi Menu** | Load `menu.php` ke Menu Engine |
| **Registrasi Event** | Load event & listener modul ke Event System |
| **Registrasi Migration** | Daftarkan path migration modul |
| **Registrasi Seeder** | Daftarkan seeder modul |

### 9.2 Alur Loading Modul

```text
Module Loader
    │
    ├── 1. Scan modules/ directory
    │       └── Temukan semua direktori modul
    │
    ├── 2. Parse module.json
    │       ├── Baca metadata (nama, versi, deskripsi)
    │       ├── Cek status (enabled/disabled)
    │       └── Baca dependensi
    │
    ├── 3. Validate Dependencies
    │       ├── Cek dependensi modul lain
    │       ├── Cek versi minimum
    │       └── Cek engine requirement
    │
    ├── 4. Register Module (jika enabled & valid)
    │       ├── Load routes.php
    │       ├── Load permissions.php
    │       ├── Load menu.php
    │       ├── Load config.php
    │       ├── Register event listeners
    │       ├── Register migrations path
    │       └── Register seeders
    │
    └── 5. Dispatch ModuleLoaded event
```

### 9.3 Format module.json

| Field | Tipe | Wajib | Deskripsi |
| :--- | :--- | :--- | :--- |
| `name` | `string` | Ya | Nama unik modul (PascalCase) |
| `slug` | `string` | Ya | Slug URL modul (kebab-case) |
| `description` | `string` | Ya | Deskripsi singkat modul |
| `version` | `string` | Ya | Semantic version (e.g., `1.0.0`) |
| `author` | `string` | Ya | Nama pembuat modul |
| `enabled` | `boolean` | Ya | Status aktif/nonaktif |
| `order` | `integer` | Tidak | Urutan loading (default: 0) |
| `dependencies` | `array` | Tidak | Daftar modul yang dibutuhkan |
| `permissions` | `array` | Tidak | Deklarasi permission modul |
| `menu` | `object` | Tidak | Deklarasi menu modul |

---

## 10. Theme Loader

### 10.1 Kemampuan Theme Loader

Core Engine melalui Theme Loader harus mampu mengelola seluruh aspek tema visual:

| Kemampuan | Deskripsi |
| :--- | :--- |
| **Load Theme** | Memuat tema aktif berdasarkan konfigurasi di Setting Engine |
| **Switch Theme** | Mengganti tema aktif tanpa restart aplikasi |
| **Theme Configuration** | Membaca dan menerapkan opsi tema (warna, logo, font) |
| **Theme Assets** | Mengelola asset tema (CSS, JS, gambar) melalui Vite |
| **Theme Preview** | Memungkinkan pratinjau tema sebelum diaktifkan |

### 10.2 Alur Loading Tema

```text
Theme Loader
    │
    ├── 1. Baca setting: active_theme
    │       └── Dari Setting Engine (tabel settings)
    │
    ├── 2. Validasi tema
    │       ├── Cek direktori themes/{name}/ ada
    │       ├── Parse theme.json
    │       └── Validasi kompatibilitas versi
    │
    ├── 3. Load tema
    │       ├── Registrasi view namespace
    │       ├── Registrasi asset path
    │       ├── Apply CSS variables
    │       └── Load layout template
    │
    ├── 4. Merge konfigurasi tema
    │       ├── Default config dari theme.json
    │       └── Override dari Setting Engine
    │
    └── 5. Dispatch ThemeActivated event
```

### 10.3 Fallback Theme

Jika tema aktif tidak ditemukan atau corrupt, Core Engine **wajib** fallback ke tema default (`cosmiclib-default`) untuk memastikan aplikasi tetap dapat diakses.

```text
Load Active Theme
    │
    ├── Sukses → Gunakan tema aktif
    │
    └── Gagal → Fallback ke cosmiclib-default
                    │
                    ├── Sukses → Gunakan tema default
                    │
                    └── Gagal → Fatal Error + Log
```

---

## 11. Plugin Loader

### 11.1 Lifecycle Plugin

Plugin dalam CosmicLib memiliki lifecycle lengkap yang dikelola oleh Plugin Loader:

| Lifecycle | Deskripsi | Efek |
| :--- | :--- | :--- |
| **Install** | Memasang plugin dari paket (zip/composer) | Copy file, run migration, register |
| **Enable** | Mengaktifkan plugin yang sudah terinstal | Load route, event, middleware |
| **Disable** | Menonaktifkan plugin tanpa menghapus | Unload route, event, middleware |
| **Update** | Memperbarui plugin ke versi baru | Run migration, update file |
| **Remove** | Menghapus plugin sepenuhnya | Rollback migration, hapus file |

### 11.2 Dependency Check

Sebelum plugin dapat diinstal atau diaktifkan, Plugin Loader **wajib** memverifikasi:

| Check | Detail |
| :--- | :--- |
| **Engine Version** | Versi minimum CosmicLib yang dibutuhkan |
| **PHP Version** | Versi minimum PHP yang dibutuhkan |
| **Module Dependencies** | Modul yang harus aktif |
| **Plugin Dependencies** | Plugin lain yang harus terinstal |
| **Conflict Check** | Plugin yang berkonflik (tidak boleh aktif bersamaan) |

### 11.3 Version Check

```text
Plugin Install Request
    │
    ├── 1. Baca plugin.json
    │       └── Ambil required_engine_version
    │
    ├── 2. Bandingkan dengan versi CosmicLib aktif
    │       ├── Kompatibel → Lanjutkan
    │       └── Tidak kompatibel → Tolak instalasi
    │
    ├── 3. Cek dependensi modul
    │       ├── Semua terpenuhi → Lanjutkan
    │       └── Ada yang kurang → Tolak + tampilkan daftar
    │
    └── 4. Install jika semua check lolos
```

---

## 12. Widget Loader

### 12.1 Jenis Widget

| Jenis | Sumber | Contoh |
| :--- | :--- | :--- |
| **Widget Dashboard** | Core Engine | Statistik sistem, log aktivitas terkini |
| **Widget Module** | Modul spesifik | Buku populer, peminjaman hari ini |
| **Widget Theme** | Tema aktif | Jam, kalender, pengumuman |
| **Widget Plugin** | Plugin pihak ketiga | Cuaca, feed berita |

### 12.2 Widget Configuration

Setiap widget mendukung konfigurasi per-instance:

| Konfigurasi | Deskripsi |
| :--- | :--- |
| **Position** | Lokasi widget di dashboard (grid position) |
| **Size** | Ukuran widget (small, medium, large, full) |
| **Refresh Rate** | Interval refresh data (opsional) |
| **Filters** | Filter data spesifik (tanggal, kategori, limit) |
| **Visibility** | Permission yang dibutuhkan untuk melihat widget |
| **Custom Options** | Opsi khusus per tipe widget |

### 12.3 Alur Loading Widget

```text
Widget Loader
    │
    ├── 1. Scan widget registry
    │       ├── Core widgets
    │       ├── Module widgets
    │       ├── Theme widgets
    │       └── Plugin widgets
    │
    ├── 2. Filter berdasarkan permission user
    │
    ├── 3. Load konfigurasi widget user
    │       └── Posisi, ukuran, opsi custom
    │
    └── 4. Render widget ke dashboard
```

---

## 13. Event System

### 13.1 Arsitektur Event

Core Engine menggunakan **Laravel Event System** sebagai bus komunikasi antar komponen. Event memungkinkan komponen berkomunikasi tanpa coupling langsung.

### 13.2 Komponen Event System

| Komponen | Fungsi | Contoh |
| :--- | :--- | :--- |
| **Event** | Objek yang merepresentasikan kejadian | `BookBorrowed`, `UserLoggedIn` |
| **Listener** | Handler yang merespons event | `SendBorrowNotification`, `LogUserActivity` |
| **Observer** | Listener khusus untuk Eloquent Model | `BookObserver` (creating, updating, deleting) |
| **Broadcast** | Event yang disiarkan ke frontend (Future) | `NotificationReceived` |

### 13.3 Event Inti Core Engine

| Event | Payload | Kapan Di-dispatch |
| :--- | :--- | :--- |
| `SystemBooted` | Engine list, boot time | Setelah seluruh engine selesai boot |
| `SystemShuttingDown` | — | Sebelum aplikasi shutdown |
| `ConfigurationLoaded` | Config array | Setelah konfigurasi dimuat |
| `ConfigurationChanged` | Key, old value, new value | Setelah konfigurasi diubah |
| `ModuleLoaded` | Module name, version | Setelah sebuah modul berhasil dimuat |
| `ModuleEnabled` | Module name | Setelah modul diaktifkan |
| `ModuleDisabled` | Module name | Setelah modul dinonaktifkan |
| `ThemeActivated` | Theme name, config | Setelah tema diaktifkan |
| `ThemeSwitched` | Old theme, new theme | Setelah tema diganti |
| `PluginInstalled` | Plugin name, version | Setelah plugin diinstal |
| `PluginEnabled` | Plugin name | Setelah plugin diaktifkan |
| `PluginDisabled` | Plugin name | Setelah plugin dinonaktifkan |
| `UserLoggedIn` | User model | Setelah login berhasil |
| `UserLoggedOut` | User model | Setelah logout |
| `PermissionChanged` | User/role, permissions | Setelah permission diperbarui |
| `SettingChanged` | Key, old value, new value | Setelah setting diubah |
| `CacheCleared` | Cache tags | Setelah cache dibersihkan |
| `BackupCreated` | Backup path, size | Setelah backup dibuat |

### 13.4 Konvensi Event

| Aturan | Detail |
| :--- | :--- |
| **Naming** | PascalCase, past tense (`BookBorrowed`, bukan `BorrowBook`) |
| **Namespace** | `App\Core\Events\` untuk event inti; `Modules\{Name}\Events\` untuk modul |
| **Payload** | Event membawa data minimal yang dibutuhkan listener |
| **Immutable** | Event bersifat immutable setelah di-dispatch |

### 13.5 Broadcast (Future)

Pada fase lanjutan, event tertentu akan mendukung broadcasting ke frontend menggunakan Laravel Broadcasting (Pusher/Ably/Soketi) untuk notifikasi realtime.

```text
Server Event
    │
    ├── Internal Listener (sync)
    │       └── Log, notifikasi DB, update cache
    │
    └── Broadcast (future, async)
            └── WebSocket → Frontend → Notifikasi realtime
```

---

## 14. Configuration Hierarchy

### 14.1 Urutan Prioritas

Konfigurasi dalam CosmicLib mengikuti hierarki prioritas dari **paling rendah** ke **paling tinggi**:

```text
Prioritas (rendah ke tinggi):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  Level 1: .env
     │     Environment variables
     │     Secrets & bootstrap values
     │
     ▼
  Level 2: config/
     │     File konfigurasi Laravel
     │     Nilai statis default
     │
     ▼
  Level 3: Database (settings table)
     │     Konfigurasi runtime
     │     Dikelola admin via UI
     │
     ▼
  Level 4: Module Config
     │     Konfigurasi spesifik modul
     │     Override config global untuk modul tertentu
     │
     ▼
  Level 5: User Configuration
           Preferensi pengguna individual
           Override tertinggi (per user)
```

### 14.2 Kapan Menggunakan Level Mana?

| Level | Gunakan Untuk | Contoh |
| :--- | :--- | :--- |
| **`.env`** | Secrets, database credentials, environment | `DB_PASSWORD`, `APP_KEY` |
| **`config/`** | Default framework, driver config | Cache driver, session lifetime |
| **Database** | Konfigurasi bisnis runtime | Nama sekolah, denda per hari |
| **Module Config** | Override per modul | Limit upload khusus modul media |
| **User Config** | Preferensi personal | Bahasa, tema, dashboard layout |

### 14.3 Aturan Konfigurasi

| Aturan | Penjelasan |
| :--- | :--- |
| `env()` hanya di `config/` | Tidak boleh dipanggil di controller, service, atau view |
| Secrets di `.env` | Password, API key, token tidak boleh di database |
| Bisnis config di database | Nilai yang bisa diubah admin via UI |
| Cache configuration | Konfigurasi di-cache untuk performa production |

---

## 15. Core Helper

### 15.1 Daftar Helper Global

Core Engine menyediakan helper function global untuk akses cepat ke layanan engine:

#### Configuration Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `setting($key, $default)` | Ambil konfigurasi dari Setting Engine | `setting('app.name', 'CosmicLib')` |
| `setting_set($key, $value)` | Simpan konfigurasi ke Setting Engine | `setting_set('app.name', 'Perpustakaan')` |
| `cosmiclib_version()` | Ambil versi CosmicLib aktif | `cosmiclib_version()` → `1.0.0` |

#### Menu Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `menu_items($group)` | Ambil item menu berdasarkan grup | `menu_items('sidebar')` |
| `menu_active($route)` | Cek apakah menu aktif | `menu_active('books.index')` |
| `menu_badge($key)` | Ambil badge count menu | `menu_badge('notifications')` |

#### Theme Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `theme_asset($path)` | URL asset tema aktif | `theme_asset('css/app.css')` |
| `theme_config($key)` | Konfigurasi tema aktif | `theme_config('primary_color')` |
| `active_theme()` | Nama tema aktif | `active_theme()` → `cosmiclib-default` |

#### Permission Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `user_can($permission)` | Cek permission pengguna aktif | `user_can('books.create')` |
| `user_has_role($role)` | Cek role pengguna aktif | `user_has_role('admin')` |
| `user_permissions()` | Ambil semua permission pengguna aktif | `user_permissions()` |

#### Module Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `module_active($name)` | Cek apakah modul aktif | `module_active('Library')` |
| `module_path($name, $path)` | Path absolut ke file modul | `module_path('Library', 'Views')` |
| `module_config($name, $key)` | Konfigurasi modul | `module_config('Library', 'loan_days')` |

#### Route Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `module_route($name, $route)` | Generate URL route modul | `module_route('Library', 'books.index')` |
| `admin_route($route)` | Generate URL route admin | `admin_route('dashboard')` |
| `is_admin_route()` | Cek apakah route saat ini adalah admin | `is_admin_route()` |

#### Media Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `media_url($path)` | URL publik file media | `media_url('covers/book1.jpg')` |
| `media_size($path)` | Ukuran file dalam format readable | `media_size('covers/book1.jpg')` → `2.5 MB` |

#### Setting Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `app_name()` | Nama aplikasi dari setting | `app_name()` → `Perpustakaan SMA 1` |
| `app_logo()` | URL logo dari setting | `app_logo()` |
| `school_name()` | Nama sekolah dari setting | `school_name()` |

#### Library Helper

| Helper | Fungsi | Contoh |
| :--- | :--- | :--- |
| `format_isbn($isbn)` | Format ISBN dengan pemisah | `format_isbn('9780134685991')` |
| `format_currency($amount)` | Format mata uang Indonesia | `format_currency(5000)` → `Rp 5.000` |
| `format_date_id($date)` | Format tanggal Indonesia | `format_date_id(now())` → `18 Juli 2026` |

---

## 16. Core Services

### 16.1 Daftar Service Inti

Berikut adalah service inti yang disediakan oleh Core Engine:

| # | Service | Namespace | Fungsi |
| :--- | :--- | :--- | :--- |
| 1 | **Configuration Service** | `App\Core\Services\ConfigurationService` | Baca/tulis konfigurasi dari semua sumber |
| 2 | **Module Service** | `App\Core\Services\ModuleService` | Manajemen lifecycle modul |
| 3 | **Theme Service** | `App\Core\Services\ThemeService` | Manajemen tema (load, switch, preview) |
| 4 | **Permission Service** | `App\Core\Services\PermissionService` | Manajemen RBAC, gate, policy |
| 5 | **Menu Service** | `App\Core\Services\MenuService` | Bangun dan kelola navigasi dinamis |
| 6 | **Setting Service** | `App\Core\Services\SettingService` | CRUD setting dari database |
| 7 | **Backup Service** | `App\Core\Services\BackupService` | Backup dan restore database & file |
| 8 | **Update Service** | `App\Core\Services\UpdateService` | Pembaruan sistem dan modul |
| 9 | **Notification Service** | `App\Core\Services\NotificationService` | Kirim notifikasi (email, WA, in-app) |
| 10 | **Media Service** | `App\Core\Services\MediaService` | Upload, optimasi, dan manajemen file |

### 16.2 Hubungan Antar Service

```text
┌─────────────────────────────────────────────────────────────┐
│                     Core Services                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   ConfigurationService ◄──── SettingService                  │
│          │                        │                          │
│          ▼                        ▼                          │
│   ModuleService ──────► PermissionService                    │
│          │                        │                          │
│          ▼                        ▼                          │
│   ThemeService             MenuService                       │
│          │                        │                          │
│          └────────┬───────────────┘                          │
│                   ▼                                          │
│            NotificationService                               │
│                   │                                          │
│          ┌────────┴────────┐                                 │
│          ▼                 ▼                                  │
│   BackupService      UpdateService                           │
│                                                              │
│   MediaService (independen, digunakan lintas service)        │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 16.3 Detail Service

#### Configuration Service

| Method | Fungsi |
| :--- | :--- |
| `get($key, $default)` | Ambil konfigurasi dengan fallback hierarchy |
| `set($key, $value)` | Simpan konfigurasi ke sumber yang sesuai |
| `has($key)` | Cek apakah konfigurasi ada |
| `all($group)` | Ambil semua konfigurasi dalam grup |
| `refresh()` | Refresh cache konfigurasi |

#### Module Service

| Method | Fungsi |
| :--- | :--- |
| `discover()` | Scan dan temukan semua modul |
| `load($name)` | Muat modul tertentu |
| `enable($name)` | Aktifkan modul |
| `disable($name)` | Nonaktifkan modul |
| `isActive($name)` | Cek apakah modul aktif |
| `getAll()` | Daftar semua modul |
| `getDependencies($name)` | Daftar dependensi modul |

#### Theme Service

| Method | Fungsi |
| :--- | :--- |
| `load($name)` | Muat tema tertentu |
| `switch($name)` | Ganti tema aktif |
| `preview($name)` | Pratinjau tema tanpa mengaktifkan |
| `getActive()` | Ambil tema aktif |
| `getAll()` | Daftar semua tema |
| `getConfig($key)` | Ambil konfigurasi tema |

#### Permission Service

| Method | Fungsi |
| :--- | :--- |
| `check($user, $permission)` | Cek permission pengguna |
| `assign($role, $permissions)` | Assign permission ke role |
| `revoke($role, $permissions)` | Cabut permission dari role |
| `getRolePermissions($role)` | Daftar permission role |
| `getUserPermissions($user)` | Daftar permission pengguna |
| `syncModulePermissions($module)` | Sinkronisasi permission modul |

#### Menu Service

| Method | Fungsi |
| :--- | :--- |
| `build($group)` | Bangun menu berdasarkan grup |
| `register($items)` | Daftarkan item menu baru |
| `filterByPermission($user)` | Filter menu berdasarkan permission |
| `getActive()` | Ambil menu yang sedang aktif |
| `getBadges()` | Ambil badge count untuk semua menu |

#### Setting Service

| Method | Fungsi |
| :--- | :--- |
| `get($key, $default)` | Ambil setting dari database |
| `set($key, $value, $group)` | Simpan setting ke database |
| `getGroup($group)` | Ambil semua setting dalam grup |
| `delete($key)` | Hapus setting |
| `flush()` | Bersihkan cache setting |

#### Backup Service

| Method | Fungsi |
| :--- | :--- |
| `createFull()` | Backup database + file lengkap |
| `createDatabase()` | Backup database saja |
| `restore($path)` | Restore dari file backup |
| `list()` | Daftar backup yang tersedia |
| `delete($path)` | Hapus file backup |
| `schedule($cron)` | Jadwalkan backup otomatis |

#### Update Service

| Method | Fungsi |
| :--- | :--- |
| `checkAvailable()` | Cek pembaruan yang tersedia |
| `download($version)` | Unduh paket pembaruan |
| `apply($version)` | Terapkan pembaruan |
| `rollback()` | Kembalikan ke versi sebelumnya |
| `getChangelog($version)` | Ambil changelog versi |

#### Notification Service

| Method | Fungsi |
| :--- | :--- |
| `send($user, $notification)` | Kirim notifikasi ke pengguna |
| `sendBulk($users, $notification)` | Kirim notifikasi massal |
| `markAsRead($id)` | Tandai notifikasi sebagai dibaca |
| `getUnread($user)` | Ambil notifikasi belum dibaca |
| `getChannels()` | Daftar channel yang tersedia |

#### Media Service

| Method | Fungsi |
| :--- | :--- |
| `upload($file, $path)` | Upload file ke storage |
| `delete($path)` | Hapus file dari storage |
| `optimize($path)` | Optimasi file (compress gambar) |
| `getUrl($path)` | Ambil URL publik file |
| `getMeta($path)` | Ambil metadata file |

---

## 17. Error Handling

### 17.1 Exception

Core Engine menyediakan exception handler terpusat yang menangkap semua exception di seluruh aplikasi.

| Tipe Exception | Penanganan |
| :--- | :--- |
| **ValidationException** | Redirect back dengan pesan error validasi |
| **AuthenticationException** | Redirect ke halaman login |
| **AuthorizationException** | Tampilkan halaman 403 dengan pesan ramah |
| **ModelNotFoundException** | Tampilkan halaman 404 |
| **CoreException** | Log error, tampilkan halaman error custom |
| **EngineBootException** | Log fatal, mode degraded / maintenance |
| **Throwable (catch-all)** | Log error, tampilkan halaman 500 generik |

### 17.2 Logging

Core Engine mengkonfigurasi logging channel terstruktur:

| Channel | File | Konten |
| :--- | :--- | :--- |
| **application** | `storage/logs/application.log` | Log operasional umum |
| **error** | `storage/logs/error.log` | Error dan exception |
| **audit** | `storage/logs/audit.log` | Jejak aktivitas sensitif (login, permission change) |
| **query** | `storage/logs/query.log` | Slow query logging (development) |
| **module** | `storage/logs/module.log` | Log spesifik modul |

Format log standar:

```text
[2026-07-18 15:30:00] cosmiclib.ERROR: ModuleNotFoundException:
  Module 'Library' not found
  {"module":"Library","user_id":1,"ip":"192.168.1.1"}
  [stacktrace]
```

### 17.3 Reporting

| Level | Channel | Tindakan |
| :--- | :--- | :--- |
| **DEBUG** | `application` | Hanya di environment `local` |
| **INFO** | `application` | Informasi operasional |
| **WARNING** | `application` | Peringatan (deprecated, fallback) |
| **ERROR** | `error` | Error yang memerlukan perhatian |
| **CRITICAL** | `error` | Error yang memengaruhi operasional |
| **EMERGENCY** | `error` + notifikasi | Sistem tidak bisa beroperasi |

### 17.4 Recovery

Core Engine menyediakan mekanisme recovery untuk skenario kegagalan:

| Skenario | Recovery Strategy |
| :--- | :--- |
| **Modul gagal load** | Skip modul, log warning, lanjutkan boot |
| **Tema gagal load** | Fallback ke tema default |
| **Plugin gagal load** | Nonaktifkan plugin, log error |
| **Database setting gagal** | Fallback ke file config |
| **Cache corrupt** | Clear cache, rebuild |
| **Migration gagal** | Rollback, nonaktifkan modul terkait |

> **Keputusan penting:** Core Engine harus **resilient**. Kegagalan satu modul atau plugin **tidak boleh** menyebabkan seluruh sistem down. Gunakan strategi **graceful degradation**.

---

## 18. Performance

### 18.1 Caching Strategy

| Cache Target | Driver Default | TTL | Invalidation |
| :--- | :--- | :--- | :--- |
| **Configuration** | File | Infinite (manual refresh) | Saat admin ubah setting |
| **Route** | File | Infinite (manual refresh) | Saat modul enable/disable |
| **View** | File | Infinite (manual refresh) | Saat tema berubah |
| **Permission** | File / Database | 60 menit | Saat permission diperbarui |
| **Menu** | File / Database | 60 menit | Saat menu diperbarui |
| **Module List** | File | Infinite (manual refresh) | Saat modul berubah |
| **Query Result** | File / Database | 15 menit | TTL-based |

> **Keputusan penting:** Default cache driver adalah **file** untuk kompatibilitas shared hosting. Redis/Memcached digunakan **jika tersedia**, bukan sebagai keharusan.

### 18.2 Lazy Loading

| Komponen | Strategi |
| :--- | :--- |
| **Module** | Modul hanya di-load penuh saat route-nya diakses |
| **Widget** | Widget hanya di-render saat dashboard ditampilkan |
| **Plugin** | Plugin hanya di-boot saat diperlukan |
| **Theme Assets** | Asset di-load via Vite dengan code splitting |
| **Eloquent Relations** | Eager loading eksplisit (`with()`), prevent lazy loading di strict mode |

### 18.3 Autoload Optimization

| Teknik | Detail |
| :--- | :--- |
| **Composer Autoload Optimization** | `composer dump-autoload --optimize` untuk production |
| **Class Map** | Generate class map untuk performa autoloading |
| **PSR-4 Strict** | Setiap class di namespace yang benar |

### 18.4 Service Provider Optimization

| Teknik | Detail |
| :--- | :--- |
| **Deferred Provider** | Provider yang jarang dipakai di-defer (load on demand) |
| **Package Discovery** | Disable auto-discovery untuk package yang tidak perlu |
| **Compiled Provider** | Cache daftar provider di production |

### 18.5 Route Cache

```text
php artisan route:cache     ← Production: cache semua route
php artisan route:clear     ← Development: clear route cache
```

Route cache **wajib** dijalankan di production. Module Engine harus mendukung route caching.

### 18.6 Config Cache

```text
php artisan config:cache    ← Production: cache semua config
php artisan config:clear    ← Development: clear config cache
```

> **Aturan:** Jika `config:cache` digunakan, `env()` di luar file `config/` **tidak akan berfungsi**. Ini memperkuat aturan bahwa `env()` hanya boleh di file `config/`.

---

## 19. Security

### 19.1 Validation

| Layer | Validasi |
| :--- | :--- |
| **Form Request** | Validasi input user sebelum masuk Service |
| **Service Layer** | Validasi business rule |
| **Repository** | Validasi tipe data sebelum query |
| **Model** | Cast dan mutator untuk konsistensi data |

> **Aturan:** Semua input dari pengguna **wajib** melewati Form Request. Tidak ada input mentah yang langsung masuk ke Service atau Repository.

### 19.2 CSRF Protection

| Penerapan | Detail |
| :--- | :--- |
| **Blade Form** | `@csrf` directive wajib di setiap form |
| **AJAX Request** | CSRF token via meta tag atau header `X-CSRF-TOKEN` |
| **API Route** | Menggunakan Sanctum token, bukan session CSRF |
| **Exception** | Route webhook dari pihak ketiga dikecualikan secara eksplisit |

### 19.3 XSS Prevention

| Penerapan | Detail |
| :--- | :--- |
| **Blade Escape** | Default `{{ }}` melakukan escape otomatis |
| **Raw Output** | `{!! !!}` hanya untuk konten yang sudah di-sanitize |
| **Input Sanitization** | Strip tag HTML berbahaya sebelum simpan |
| **Content Security Policy** | Header CSP untuk membatasi resource loading |

### 19.4 Authorization

| Layer | Mekanisme |
| :--- | :--- |
| **Middleware** | Permission check di route level |
| **Policy** | Authorization check di domain level |
| **Gate** | Check permission atomik |
| **Blade Directive** | `@can`, `@cannot` untuk UI conditional |

### 19.5 Encryption

| Data | Metode |
| :--- | :--- |
| **Password** | Bcrypt/Argon2 hashing (Laravel Hash) |
| **Sensitive Setting** | Encrypt/decrypt via Laravel Crypt |
| **API Token** | Hashed storage |
| **Session** | Encrypted session data |
| **Backup** | Encrypted backup files (opsional) |

### 19.6 Sensitive Data

| Aturan | Detail |
| :--- | :--- |
| **Tidak log password** | Password tidak boleh muncul di log |
| **Mask sensitive data** | API key, token di-mask di UI (`****xxxx`) |
| **Env file protection** | `.env` tidak boleh accessible via web |
| **Database credentials** | Hanya di `.env`, tidak di database setting |
| **Audit trail** | Perubahan data sensitif dicatat di audit log |

---

## 20. AI Rules

### 20.1 Larangan untuk AI

Semua AI assistant (Claude, Codex, ChatGPT, Cline, Gemini, dan lainnya) **dilarang keras**:

| # | Larangan | Alasan |
| :--- | :--- | :--- |
| 1 | **Mengubah Core Engine tanpa analisis dampak** | Core Engine memengaruhi seluruh sistem |
| 2 | **Menghapus Core Service** | Service inti digunakan oleh semua engine dan modul |
| 3 | **Mengubah Engine Lifecycle** | Urutan boot adalah kontrak arsitektur |
| 4 | **Mengubah konfigurasi inti** | Perubahan config core bisa menyebabkan cascading failure |
| 5 | **Mengubah Boot Sequence** | Urutan provider sangat kritis |
| 6 | **Bypass Service Container** | Semua dependensi melalui DI, bukan `new` langsung |
| 7 | **Hardcode di Core** | Menu, role, permission, warna — semua dari engine |
| 8 | **Menghapus Contract/Interface** | Kontrak adalah perjanjian antar komponen |

### 20.2 Kewajiban AI Sebelum Mengubah Core

```text
1. Baca AGENTS.md dan PROJECT_MANIFEST.md
2. Baca docs/07_CORE_ENGINE.md (dokumen ini)
3. Baca docs/03_ARCHITECTURE.md
4. Analisis dampak perubahan terhadap:
   • Boot sequence
   • Service dependency graph
   • Existing contracts
   • Module compatibility
5. Jelaskan rencana perubahan
6. Daftarkan file yang akan diubah
7. Implementasikan dengan test
8. Dokumentasikan perubahan di CHANGELOG.md
```

### 20.3 Panduan AI untuk Core Engine

| Aspek | Panduan |
| :--- | :--- |
| **Menambah Service** | Boleh — ikuti pattern existing, buat contract dulu |
| **Menambah Event** | Boleh — gunakan naming convention, dokumentasikan |
| **Menambah Helper** | Boleh — registrasi di `CoreServiceProvider`, hindari duplikasi |
| **Menambah Command** | Boleh — prefix `cosmiclib:`, dokumentasikan |
| **Mengubah Service** | Hati-hati — analisis semua consumer service |
| **Mengubah Contract** | Sangat hati-hati — breaking change untuk semua implementor |
| **Mengubah Lifecycle** | Dilarang tanpa persetujuan eksplisit |

---

## 21. Future Roadmap

### 21.1 Rencana Pengembangan

| # | Fitur | Fase | Prioritas | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Hook System** | 2 | Tinggi | Sistem hook untuk ekstensi tanpa modifikasi core |
| 2 | **Event Marketplace** | 3 | Sedang | Marketplace untuk event listener pihak ketiga |
| 3 | **Plugin Marketplace** | 3 | Tinggi | Distribusi dan instalasi plugin dari marketplace |
| 4 | **Theme Marketplace** | 3 | Tinggi | Distribusi dan instalasi tema dari marketplace |
| 5 | **Cloud Sync** | 4 | Sedang | Sinkronisasi data antar instance CosmicLib |
| 6 | **Package Manager** | 3 | Sedang | Manajer paket internal untuk modul/plugin/tema |
| 7 | **CLI Installer** | 2 | Tinggi | Instalasi via command line `cosmiclib:install` |
| 8 | **REST API** | 2 | Tinggi | API Engine untuk integrasi eksternal |
| 9 | **GraphQL** | 4 | Rendah | Query language alternatif (evaluasi kebutuhan) |
| 10 | **Realtime Engine** | 4 | Sedang | WebSocket untuk notifikasi dan data realtime |

### 21.2 Roadmap Visual

```text
Fase 1 (Saat ini)           Fase 2                  Fase 3                  Fase 4
━━━━━━━━━━━━━━━━━━━━       ━━━━━━━━━━━━━━━━━━━━    ━━━━━━━━━━━━━━━━━━━━    ━━━━━━━━━━━━━━━━
• Core Engine               • Hook System           • Plugin Marketplace    • Cloud Sync
• Module Engine              • CLI Installer         • Theme Marketplace     • GraphQL
• Theme Engine               • REST API              • Event Marketplace     • Realtime Engine
• Permission Engine          • Package Manager
• Menu Engine
• Setting Engine
• Plugin Engine
• Widget Engine
• Media Engine
• Notification Engine
• Backup Engine
• Update Engine
• License Engine
```

### 21.3 Prinsip Evolusi

> **Keputusan penting:** Evolusi Core Engine ke depan **tidak boleh**:
> - Merusak Dependency Rule yang sudah ditetapkan.
> - Memaksa migrasi breaking change tanpa migration path.
> - Menghapus dukungan shared hosting sebagai target deployment utama.
> - Mengubah arsitektur dari Modular Monolith ke Microservices tanpa evaluasi menyeluruh.

---

## 22. Architecture Diagram

### 22.1 Diagram Arsitektur Lengkap

```text
┌──────────────────────────────────────────────────────────────────────┐
│                                                                      │
│    Client (Browser / Mobile / API Client)                            │
│                                                                      │
└───────────────────────────┬──────────────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────────┐
│                          HTTP Layer                                   │
│    ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐           │
│    │   Web    │  │   API    │  │  Webhook │  │  Assets  │           │
│    │  Routes  │  │  Routes  │  │  Routes  │  │ (Vite)   │           │
│    └──────────┘  └──────────┘  └──────────┘  └──────────┘           │
└───────────────────────────┬──────────────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────────┐
│                        Laravel Framework                              │
│    ┌──────────────────────────────────────────────────────────┐      │
│    │                     Middleware Stack                      │      │
│    │   CSRF · Auth · Permission · Locale · Theme · Module     │      │
│    └──────────────────────────────────────────────────────────┘      │
└───────────────────────────┬──────────────────────────────────────────┘
                            │
                            ▼
┌══════════════════════════════════════════════════════════════════════┐
║                                                                      ║
║                      ★ CORE ENGINE ★                                 ║
║                                                                      ║
║   ┌────────────────────────────────────────────────────────────┐     ║
║   │  Bootstrap · Configuration · DI Container · Event System   │     ║
║   │  Logging · Queue · Cache · Localization · Exception Handler│     ║
║   │  Helper · Utilities · Scheduler · Service Provider         │     ║
║   └────────────────────────────────────────────────────────────┘     ║
║                              │                                       ║
║           ┌──────────────────┼──────────────────┐                    ║
║           ▼                  ▼                  ▼                     ║
║   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              ║
║   │Module Engine │  │ Theme Engine │  │Permission Eng│              ║
║   │              │  │              │  │              │              ║
║   │• Load Module │  │• Load Theme  │  │• RBAC        │              ║
║   │• Routes      │  │• Switch      │  │• Gates       │              ║
║   │• Migrations  │  │• Assets      │  │• Policies    │              ║
║   │• Events      │  │• Preview     │  │• Middleware   │              ║
║   └──────────────┘  └──────────────┘  └──────────────┘              ║
║           │                  │                  │                     ║
║   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              ║
║   │ Menu Engine  │  │Plugin Engine │  │Widget Engine │              ║
║   │              │  │              │  │              │              ║
║   │• Navigation  │  │• Install     │  │• Dashboard   │              ║
║   │• Permission  │  │• Enable      │  │• Module      │              ║
║   │• Badge       │  │• Disable     │  │• Theme       │              ║
║   │• Hierarchy   │  │• Update      │  │• Config      │              ║
║   └──────────────┘  └──────────────┘  └──────────────┘              ║
║           │                  │                  │                     ║
║   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              ║
║   │Setting Engine│  │ Media Engine │  │Notification  │              ║
║   │              │  │              │  │   Engine     │              ║
║   │• Key-Value   │  │• Upload      │  │• Email       │              ║
║   │• Groups      │  │• Optimize    │  │• WhatsApp    │              ║
║   │• Cache       │  │• Storage     │  │• In-App      │              ║
║   │• UI Admin    │  │• Metadata    │  │• Template    │              ║
║   └──────────────┘  └──────────────┘  └──────────────┘              ║
║           │                  │                  │                     ║
║   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              ║
║   │Backup Engine │  │Update Engine │  │License Engine│              ║
║   │              │  │              │  │              │              ║
║   │• Full Backup │  │• Check       │  │• Activation  │              ║
║   │• DB Backup   │  │• Download    │  │• Validation  │              ║
║   │• Restore     │  │• Apply       │  │• Expiry      │              ║
║   │• Schedule    │  │• Rollback    │  │• Feature Lock│              ║
║   └──────────────┘  └──────────────┘  └──────────────┘              ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────────┐
│                      Application Modules                              │
│                                                                      │
│   ┌────────────┐  ┌────────────┐  ┌────────────┐  ┌────────────┐   │
│   │  Library   │  │ Circulation│  │  Members   │  │  Reports   │   │
│   │  Module    │  │  Module    │  │  Module    │  │  Module    │   │
│   └────────────┘  └────────────┘  └────────────┘  └────────────┘   │
│                                                                      │
└───────────────────────────┬──────────────────────────────────────────┘
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────────┐
│                         Database Layer                                │
│                                                                      │
│                      MySQL 8+ / MariaDB                              │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

### 22.2 Diagram Aliran Data

```text
┌──────┐    ┌───────┐    ┌────────────┐    ┌─────────┐    ┌────────────┐
│Client│───▶│Router │───▶│Middleware  │───▶│Controller│───▶│FormRequest │
└──────┘    └───────┘    └────────────┘    └────┬─────┘    └────────────┘
                                                │
                                                ▼
                                          ┌──────────┐
                                          │ Service  │ ← Business Logic
                                          └────┬─────┘
                                               │
                                               ▼
                                         ┌───────────┐
                                         │Repository │ ← Data Access
                                         └────┬──────┘
                                              │
                                              ▼
                                         ┌─────────┐
                                         │  Model  │ ← Eloquent Entity
                                         └────┬────┘
                                              │
                                              ▼
                                         ┌──────────┐
                                         │ Database │
                                         └──────────┘
```

### 22.3 Diagram Engine Dependency

```text
                    ┌──────────────┐
                    │ Core Engine  │
                    └──────┬───────┘
           ┌───────────────┼───────────────┐
           ▼               ▼               ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   Setting    │ │  Permission  │ │    Module    │
    │   Engine     │ │   Engine     │ │    Engine    │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘
           │                │                │
           ▼                ▼                ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │    Menu      │ │    Theme     │ │    Plugin    │
    │   Engine     │ │   Engine     │ │    Engine    │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘
           │                │                │
           ▼                ▼                ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   Widget     │ │    Media     │ │ Notification │
    │   Engine     │ │   Engine     │ │    Engine    │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘
           │                │                │
           └────────────────┼────────────────┘
                            ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   Backup     │ │   Update     │ │   License    │
    │   Engine     │ │   Engine     │ │    Engine    │
    └──────────────┘ └──────────────┘ └──────────────┘
```

> **Aturan:** Panah menunjukkan arah dependensi. Engine di bawah bergantung pada engine di atas. Tidak boleh ada dependensi terbalik (circular dependency).

---

## 23. Dependency Rules

### 23.1 Aturan Utama

> **Keputusan penting:** Modul **tidak boleh** bergantung langsung pada modul lain. Semua komunikasi antar modul harus melalui mekanisme resmi yang disediakan Core Engine.

### 23.2 Mekanisme Komunikasi Antar Modul

| # | Mekanisme | Penggunaan | Contoh |
| :--- | :--- | :--- | :--- |
| 1 | **Core Service** | Akses layanan inti yang tersedia untuk semua modul | `PermissionService::check()` |
| 2 | **Event** | Komunikasi loose-coupled antar modul | Modul A dispatch `BookBorrowed`, Modul B listen |
| 3 | **Contract (Interface)** | Kontrak yang diimplementasikan modul | `SearchableInterface` yang bisa diimplementasikan modul mana pun |
| 4 | **Repository** | Akses data melalui abstraksi repository | `BookRepository::findByIsbn()` |

### 23.3 Aturan Dependency

| Aturan | Penjelasan |
| :--- | :--- |
| **Modul → Core Engine** | ✅ Diizinkan — modul boleh menggunakan layanan Core |
| **Modul → Engine lain** | ✅ Diizinkan — modul boleh menggunakan Permission, Menu, Setting Engine |
| **Modul → Modul lain (langsung)** | ❌ Dilarang — tidak boleh `use` class modul lain langsung |
| **Modul → Modul lain (via Event)** | ✅ Diizinkan — komunikasi melalui event |
| **Modul → Modul lain (via Contract)** | ✅ Diizinkan — melalui interface yang didefinisikan di Core |
| **Engine → Core Engine** | ✅ Diizinkan — semua engine bergantung pada Core |
| **Engine → Engine lain** | ⚠️ Hati-hati — hanya sesuai hierarki dependency yang ditetapkan |
| **Core → Modul** | ❌ Dilarang — Core tidak boleh bergantung pada modul spesifik |

### 23.4 Contoh Komunikasi yang Benar vs Salah

```text
✅ BENAR:
   Modul Library ──dispatch──▶ BookBorrowed Event
   Modul Notification ──listen──▶ BookBorrowed Event
   Modul Notification ──send──▶ Notifikasi ke peminjam

❌ SALAH:
   Modul Library ──use──▶ NotificationModule\Services\NotificationService
   (Coupling langsung antar modul)

✅ BENAR:
   Modul Library ──use──▶ App\Core\Services\NotificationService
   (Melalui Core Service)

❌ SALAH:
   Modul Library ──use──▶ CirculationModule\Models\BorrowRecord
   (Akses model modul lain langsung)

✅ BENAR:
   Modul Library ──dispatch──▶ BookAvailabilityRequested Event
   Modul Circulation ──listen──▶ Respond via Event
   (Komunikasi via Event System)
```

### 23.5 Dependency Injection Rule

| Layer | Inject Via | Contoh |
| :--- | :--- | :--- |
| **Controller** | Constructor + Method (FormRequest) | `__construct(BookService $service)` |
| **Service** | Constructor | `__construct(BookRepository $repo, CacheService $cache)` |
| **Repository** | Constructor (Model) | `__construct(Book $model)` |
| **Command** | Constructor | `__construct(BackupService $backup)` |
| **Listener** | Constructor | `__construct(NotificationService $notify)` |

> **Aturan:** Jangan gunakan `app()` atau `resolve()` untuk mendapatkan dependensi di dalam class. Gunakan constructor injection. `app()` hanya diperbolehkan di helper function global dan service provider.

---

## 24. Best Practice

### 24.1 SOLID Principles

| Prinsip | Penerapan di Core Engine |
| :--- | :--- |
| **S** — Single Responsibility | Setiap service punya satu tanggung jawab jelas |
| **O** — Open/Closed | Extensible via Event dan Contract, tanpa modifikasi core |
| **L** — Liskov Substitution | Implementasi contract bisa diganti tanpa merusak sistem |
| **I** — Interface Segregation | Interface kecil dan spesifik, bukan satu interface besar |
| **D** — Dependency Inversion | Bergantung pada abstraksi (interface), bukan implementasi konkret |

### 24.2 PSR-12

| Aspek | Aturan |
| :--- | :--- |
| **Indentasi** | 4 spasi, bukan tab |
| **Line Length** | Maksimal 120 karakter |
| **Naming** | PascalCase untuk class, camelCase untuk method/property |
| **Namespace** | Sesuai struktur direktori PSR-4 |
| **Import** | Satu `use` per baris, diurutkan alfabetis |
| **Brace** | Opening brace di baris baru untuk class/method |

### 24.3 Repository Pattern

| Aturan | Detail |
| :--- | :--- |
| **Satu Repository per Model** | `BookRepository` untuk model `Book` |
| **Tidak ada query di Controller** | Semua query melalui Repository |
| **Tidak ada business logic di Repository** | Repository hanya akses data |
| **Interface Repository** | Setiap repository punya interface/contract |
| **Reusable Methods** | Method umum: `find()`, `findBy()`, `create()`, `update()`, `delete()` |

### 24.4 Service Layer Pattern

| Aturan | Detail |
| :--- | :--- |
| **Business logic di Service** | Bukan di Controller atau Repository |
| **Satu Service per domain** | `BookService`, `CirculationService`, `MemberService` |
| **Thin Controller** | Controller hanya: validasi → service → response |
| **Try-catch di Service** | Operasi sensitif dibungkus try-catch dan di-log |
| **Return konsisten** | Service mengembalikan data atau throw exception |

### 24.5 Dependency Injection

| Aturan | Detail |
| :--- | :--- |
| **Constructor Injection** | Preferensi utama untuk semua dependensi |
| **Method Injection** | Hanya untuk FormRequest di Controller |
| **Interface Binding** | Bind interface ke implementasi di ServiceProvider |
| **Jangan `new` Service** | Resolve melalui container, bukan instansiasi manual |

### 24.6 Domain Separation

| Aturan | Detail |
| :--- | :--- |
| **Modul = Domain** | Setiap modul merepresentasikan satu domain bisnis |
| **Tidak ada cross-domain query** | Modul A tidak query database modul B langsung |
| **Event untuk cross-domain** | Komunikasi lintas domain melalui Event System |
| **Shared kernel di Core** | Konsep yang dipakai bersama (User, Setting) di Core |

---

## 25. Checklist

### 25.1 Checklist Sebelum Implementasi Core Engine

Sebelum memulai implementasi (keluar dari fase Blueprint), pastikan semua item berikut terpenuhi:

#### Arsitektur & Desain

- [ ] Core Engine design selesai dan disetujui
- [ ] Architecture document (`03_ARCHITECTURE.md`) selaras dengan Core Engine spec
- [ ] Boot sequence terdefinisi dan terdokumentasi
- [ ] Service dependency graph tervalidasi (tidak ada circular dependency)
- [ ] Contract/Interface untuk semua engine terdefinisi

#### Dokumentasi

- [ ] `docs/07_CORE_ENGINE.md` (dokumen ini) final
- [ ] Semua engine document (`08`–`20`) selesai
- [ ] Coding standard (`23_CODING_STANDARD.md`) terdokumentasi
- [ ] Security guideline (`22_SECURITY_GUIDELINE.md`) terdokumentasi
- [ ] AI guideline (`31_AI_GUIDELINE.md`) terdokumentasi

#### Database

- [ ] Database design (`06_DATABASE_DESIGN.md`) selesai
- [ ] Database schema (`24_DATABASE_SCHEMA.md`) tervalidasi
- [ ] Migration strategy terdefinisi
- [ ] Seeder untuk data awal disiapkan

#### Infrastruktur

- [ ] Configuration hierarchy terdefinisi
- [ ] Caching strategy terdokumentasi
- [ ] Logging channel terkonfigurasi
- [ ] Error handling strategy terdefinisi
- [ ] Recovery strategy untuk setiap skenario kegagalan terdokumentasi

#### Modul & Engine

- [ ] Module standard structure terdefinisi
- [ ] Theme standard structure terdefinisi
- [ ] Plugin standard structure terdefinisi
- [ ] Widget standard structure terdefinisi
- [ ] Permission model terdefinisi

#### Keamanan

- [ ] Authentication strategy terdefinisi
- [ ] Authorization (RBAC) model terdefinisi
- [ ] Validation strategy terdokumentasi
- [ ] CSRF dan XSS protection terkonfigurasi
- [ ] Encryption untuk data sensitif terdefinisi

#### Kualitas

- [ ] Test strategy terdefinisi (unit, feature, integration)
- [ ] CI/CD pipeline direncanakan
- [ ] Code review checklist tersedia
- [ ] Performance baseline terdefinisi

---

## Referensi

| Dokumen | Peran |
| :--- | :--- |
| [`00_SYSTEM_PROMPT.md`](00_SYSTEM_PROMPT.md) | Standar perilaku AI |
| [`01_PROJECT_OVERVIEW.md`](01_PROJECT_OVERVIEW.md) | Gambaran proyek |
| [`02_VISION.md`](02_VISION.md) | Visi jangka panjang |
| [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) | Arsitektur tingkat tinggi |
| [`04_TECH_STACK.md`](04_TECH_STACK.md) | Detail stack teknologi |
| [`05_FOLDER_STRUCTURE.md`](05_FOLDER_STRUCTURE.md) | Struktur folder |
| [`06_DATABASE_DESIGN.md`](06_DATABASE_DESIGN.md) | Desain database |
| [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md) | Spesifikasi Module Engine |
| [`09_THEME_ENGINE.md`](09_THEME_ENGINE.md) | Spesifikasi Theme Engine |
| [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md) | Spesifikasi Permission Engine |
| [`11_MENU_ENGINE.md`](11_MENU_ENGINE.md) | Spesifikasi Menu Engine |
| [`12_WIDGET_ENGINE.md`](12_WIDGET_ENGINE.md) | Spesifikasi Widget Engine |
| [`13_PLUGIN_ENGINE.md`](13_PLUGIN_ENGINE.md) | Spesifikasi Plugin Engine |
| [`14_MEDIA_ENGINE.md`](14_MEDIA_ENGINE.md) | Spesifikasi Media Engine |
| [`15_NOTIFICATION_ENGINE.md`](15_NOTIFICATION_ENGINE.md) | Spesifikasi Notification Engine |
| [`16_SETTING_ENGINE.md`](16_SETTING_ENGINE.md) | Spesifikasi Setting Engine |
| [`22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md) | Panduan keamanan |
| [`23_CODING_STANDARD.md`](23_CODING_STANDARD.md) | Standar kode |
| [`31_AI_GUIDELINE.md`](31_AI_GUIDELINE.md) | Panduan AI |
| [`PROJECT_MANIFEST.md`](../PROJECT_MANIFEST.md) | Manifest teknis proyek |

---

## Catatan

- Core Engine adalah **satu-satunya komponen** yang tidak dapat dinonaktifkan.
- Semua engine lain **diregistrasikan melalui** Core Engine service provider.
- Urutan boot engine sangat penting — **perubahan urutan bisa menyebabkan error fatal**.
- Dokumen ini adalah **referensi utama** seluruh engine CosmicLib.
- Perubahan terhadap Core Engine spec wajib dicatat di `CHANGELOG.md`.
- Core Engine harus tetap **modular, extensible, maintainable, AI friendly, enterprise ready, dan shared hosting friendly**.

---

*CosmicLib Engine v1.0 — Sprint 2 · Prompt 010*
