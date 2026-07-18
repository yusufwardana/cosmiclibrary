# 🌌 05 — Folder Structure

## Deskripsi

Dokumen ini merupakan **standar resmi struktur folder** CosmicLib Engine. Dokumen ini mendefinisikan tata letak direktori untuk fase blueprint (saat ini) maupun proyeksi struktur Laravel 12 yang akan datang, mencakup lapisan Core, Modules, Themes, Plugins, Widgets, Storage, Public, dan Resources.

## Tujuan

Menyediakan acuan tunggal tentang tata letak file dan folder yang bersifat **modular, mudah dipelihara, enterprise ready, mengikuti Laravel Best Practice, AI friendly, dan scalable**, sehingga setiap pengembang dan AI assistant mengetahui di mana menempatkan file baru dan di mana mencari file yang ada.

## Ruang Lingkup

- Arsitektur folder dan hubungan antar lapisan
- Struktur root repository dan proyeksi Laravel
- Struktur `app/`, `modules/`, `themes/`, `plugins/`, `resources/`, `public/`, `storage/`, `database/`, `config/`, dan `routes/`
- Konvensi penamaan folder dan file
- Standar module, theme, dan plugin
- Aturan khusus AI dan Best Practice

---

## 🗂️ Table of Contents

1. [Pendahuluan](#1-pendahuluan)
2. [Architecture Folder](#2-architecture-folder)
3. [Root Folder](#3-root-folder)
4. [app/](#4-app)
5. [modules/](#5-modules)
6. [themes/](#6-themes)
7. [plugins/](#7-plugins)
8. [resources/](#8-resources)
9. [public/](#9-public)
10. [storage/](#10-storage)
11. [database/](#11-database)
12. [config/](#12-config)
13. [routes/](#13-routes)
14. [Naming Convention](#14-naming-convention)
15. [File Naming Standard](#15-file-naming-standard)
16. [Module Standard](#16-module-standard)
17. [Theme Standard](#17-theme-standard)
18. [Plugin Standard](#18-plugin-standard)
19. [AI Rules](#19-ai-rules)
20. [Best Practice](#20-best-practice)

---

## Status

`🟡 Blueprint` — Struktur ini adalah standar fase Blueprint. Struktur `app/`, `modules/`, dan Laravel lainnya adalah **proyeksi** yang akan diterapkan pada Fase 2 (Development), belum di-scaffold pada fase saat ini.

---

## ⚙️ Kerangka Sistem

## 1 Pendahuluan

Struktur folder adalah fondasi dari setiap aplikasi enterprise. Pada CosmicLib Engine, struktur folder bukan sekadar tata letak file, melainkan **kontrak arsitektur** yang menjaga konsistensi seluruh kontributor manusia maupun AI.

**Mengapa struktur folder penting:**

| Aspek | Manfaat |
|:---|:---|
| **Keterbacaan** | Pengembang baru dapat memahami proyek tanpa dokumentasi tambahan |
| **Pemeliharaan** | File mudah ditemukan; perubahan terisolasi pada modul terkait |
| **Modularitas** | Setiap fitur berdiri sendiri sebagai modul yang dapat diaktifkan/dinonaktifkan |
| **Skalabilitas** | Penambahan fitur baru tidak merusak struktur yang ada |
| **AI Friendly** | AI assistant memiliki peta yang jelas untuk menempatkan kode |
| **Konsistensi** | Konvensi penamaan seragam mengurangi ambiguitas |

Tujuan dokumen ini adalah memastikan bahwa **satu file hanya memiliki satu tempat yang benar**, dan seluruh AI (Claude, Codex, Cline, ChatGPT, AI Studio) mengikuti struktur yang sama.

---

## 2 Architecture Folder

CosmicLib Engine menggunakan arsitektur **Modular CMS** di atas Laravel 12. Lapisan-lapisan utama saling berhubungan sebagai berikut:

```text
                    ┌─────────────────────────────┐
                    │           CORE (app/)         │
                    │  Kernel, Engine, Base Class   │
                    └──────────────┬────────────────┘
                                   │ menyediakan kontrak & service
              ┌────────────────────┼────────────────────┐
              │                    │                    │
      ┌───────▼──────┐    ┌────────▼───────┐    ┌────────▼───────┐
      │   MODULES     │    │    PLUGINS      │    │    WIDGETS      │
      │ Fitur bisnis  │    │ Ekstensi ringan │    │ Komponen UI     │
      └───────┬───────┘    └────────┬────────┘    └────────┬───────┘
              │                     │                      │
              └──────────┬──────────┴──────────────────────┘
                         │ merender melalui
                 ┌───────▼────────┐
                 │     THEMES      │  ← tampilan (layout, komponen, aset)
                 └───────┬─────────┘
                         │
        ┌────────────────┼─────────────────┐
        │                │                 │
 ┌──────▼─────┐  ┌───────▼──────┐  ┌───────▼──────┐
 │ RESOURCES  │  │    PUBLIC     │  │   STORAGE     │
 │ view/asset │  │ web root asli │  │ file & cache  │
 │  sumber    │  │ hasil compile │  │  runtime      │
 └────────────┘  └───────────────┘  └───────────────┘
```

| Lapisan | Peran | Ketergantungan |
|:---|:---|:---|
| **Core** (`app/`) | Menyediakan kernel, base class, service, dan kontrak untuk seluruh engine | Tidak bergantung pada Modules/Plugins |
| **Modules** (`modules/`) | Unit fitur bisnis mandiri (mis. Library) | Bergantung pada Core |
| **Plugins** (`plugins/`) | Ekstensi ringan yang menambah/mengubah perilaku | Bergantung pada Core, opsional pada Modules |
| **Widgets** | Komponen UI kecil yang dapat ditempatkan di area layout | Dirender oleh Themes |
| **Themes** (`themes/`) | Lapisan presentasi (layout, komponen, aset) | Merender output Modules/Widgets |
| **Resources** (`resources/`) | Sumber view dan aset global (belum dikompilasi) | Dikonsumsi Themes & Vite |
| **Public** (`public/`) | Web root; entry point & aset hasil kompilasi | Menyajikan output Resources/Themes |
| **Storage** (`storage/`) | File runtime: cache, log, upload, backup | Digunakan seluruh lapisan |

**Prinsip aliran ketergantungan:** Core tidak boleh mengetahui detail Modules/Plugins. Ketergantungan selalu mengarah **ke dalam** (Modules → Core), tidak sebaliknya, sesuai prinsip Dependency Inversion (SOLID).

---

## 3 Root Folder

```text
cosmiclib/
├── .github/          # Template isu, PR, workflow CI/CD
├── docs/             # 33 dokumen arsitektur (00–32)
├── blueprint/        # Skema database SQL, API spec
├── prompts/          # Template prompt per AI assistant
├── scripts/          # Skrip pembantu (instalasi, backup)
├── tests/            # Kerangka pengujian global
├── app/              # Core Engine (kode inti Laravel)
├── bootstrap/        # Bootstrap framework & cache
├── config/           # File konfigurasi aplikasi
├── database/         # Migration, seeder, factory, schema
├── lang/             # File terjemahan (i18n)
├── modules/          # Modul fitur bisnis (Modular CMS)
├── plugins/          # Plugin ekstensi
├── themes/           # Tema tampilan
├── storage/          # File runtime (cache, log, upload)
├── public/           # Web root (entry point publik)
├── resources/        # Sumber view & aset
├── routes/           # Definisi rute
└── vendor/           # Dependency Composer (auto-generated)
```

| Folder | Fungsi |
|:---|:---|
| `.github/` | Template issue & PR, workflow GitHub Actions (CI/CD), kebijakan keamanan |
| `docs/` | 33 dokumen arsitektur bernomor 00–32 sebagai sumber kebenaran |
| `blueprint/` | Skema database (SQL/JSON/YAML) dan spesifikasi API |
| `prompts/` | Template prompt terstruktur per AI assistant |
| `scripts/` | Skrip utilitas (instalasi, backup, maintenance) |
| `tests/` | Kerangka pengujian tingkat aplikasi |
| `app/` | Core Engine — kode inti aplikasi Laravel |
| `bootstrap/` | Berkas bootstrap framework dan cache bootstrap |
| `config/` | Seluruh file konfigurasi (`app.php`, `database.php`, dll.) |
| `database/` | Migration, seeder, factory, dan definisi schema |
| `lang/` | File terjemahan multi-bahasa (UI Bahasa Indonesia) |
| `modules/` | Modul fitur bisnis yang mandiri dan dapat diaktifkan |
| `plugins/` | Plugin ekstensi ringan |
| `themes/` | Tema tampilan (layout, komponen, aset) |
| `storage/` | File runtime: cache, log, upload, backup, temp |
| `public/` | Web root; berisi `index.php` dan aset hasil kompilasi |
| `resources/` | Sumber view Blade, CSS, JS, dan aset mentah |
| `routes/` | Definisi rute web, api, console, channel, dan modul |
| `vendor/` | Paket dependency dari Composer (tidak diedit manual) |

> **Catatan Blueprint:** Pada fase saat ini hanya `docs/`, `blueprint/`, `prompts/`, `scripts/`, `tests/`, `assets/`, dan `examples/` yang terisi. Folder Laravel (`app/`, `modules/`, dll.) adalah proyeksi Fase 2.

---

## 4 app/

Folder `app/` berisi **Core Engine** — kode inti yang menopang seluruh sistem.

```text
app/
├── Console/          # Perintah Artisan kustom
├── Exceptions/       # Handler & exception kustom
├── Helpers/          # Fungsi global pembantu
├── Http/             # Controller, Middleware, Request
├── Jobs/             # Job antrian (queue)
├── Listeners/        # Listener event
├── Mail/             # Kelas Mailable
├── Models/           # Model Eloquent inti
├── Notifications/    # Kelas notifikasi
├── Observers/        # Observer model
├── Policies/         # Kebijakan otorisasi
├── Providers/        # Service Provider
├── Services/         # Service Layer (logika bisnis)
├── Traits/           # Trait yang dapat digunakan ulang
├── Support/          # Kelas pembantu tingkat rendah
├── Enums/            # Enumerasi PHP 8.1+
├── Actions/          # Single-action class
├── Contracts/        # Interface & kontrak
└── Repositories/     # Repository Pattern (akses data)
```

| Folder | Fungsi |
|:---|:---|
| `Console/` | Perintah Artisan kustom (mis. `cosmiclib:install`) |
| `Exceptions/` | Exception handler global dan kelas exception kustom |
| `Helpers/` | Fungsi helper global (di-autoload via Composer) |
| `Http/` | Berisi `Controllers/`, `Middleware/`, dan `Requests/` |
| `Jobs/` | Job untuk pemrosesan asinkron melalui queue |
| `Listeners/` | Listener yang menangani event yang dipancarkan |
| `Mail/` | Kelas Mailable untuk komposisi email |
| `Models/` | Model Eloquent inti (mis. `User`, `Setting`) |
| `Notifications/` | Kelas notifikasi multi-channel |
| `Observers/` | Observer siklus hidup model |
| `Policies/` | Kebijakan otorisasi terkait Permission Engine |
| `Providers/` | Service Provider (registrasi engine & binding) |
| `Services/` | **Service Layer** — seluruh logika bisnis inti |
| `Traits/` | Trait reusable (mis. `HasUuid`, `Auditable`) |
| `Support/` | Kelas utilitas tingkat rendah (value object, formatter) |
| `Enums/` | Enum untuk status, tipe, dan konstanta tersruktur |
| `Actions/` | Single-action class (pola Action) |
| `Contracts/` | Interface untuk Dependency Inversion |
| `Repositories/` | Implementasi Repository Pattern untuk abstraksi data |

---

## 5 modules/

Setiap modul adalah aplikasi mini yang mandiri. Contoh struktur modul `Library`:

```text
modules/
└── Library/
    ├── module.json       # Manifest modul (metadata & config)
    ├── config/           # Konfigurasi khusus modul
    ├── routes/           # Rute web & api modul
    ├── database/         # Migration, seeder, factory modul
    ├── Controllers/      # Controller modul (thin)
    ├── Models/           # Model Eloquent modul
    ├── Repositories/     # Repository modul
    ├── Services/         # Service Layer modul
    ├── Requests/         # Form Request (validasi input)
    ├── Policies/         # Kebijakan otorisasi modul
    ├── Resources/        # API Resource / transformer
    ├── Views/            # Blade view khusus modul
    ├── Helpers/          # Helper khusus modul
    ├── Providers/        # Service Provider modul
    └── Tests/            # Pengujian modul
```

| Folder/File | Fungsi |
|:---|:---|
| `module.json` | Manifest berisi nama, versi, dependency, dan status modul |
| `config/` | File konfigurasi khusus modul (di-merge oleh Setting Engine) |
| `routes/` | Definisi rute web dan api yang di-load Provider modul |
| `database/` | Migration, seeder, dan factory milik modul |
| `Controllers/` | Controller tipis; hanya mengorkestrasi Service |
| `Models/` | Model Eloquent domain modul |
| `Repositories/` | Abstraksi akses data modul |
| `Services/` | Logika bisnis modul (Service Layer Pattern) |
| `Requests/` | Form Request untuk validasi input |
| `Policies/` | Kebijakan otorisasi terintegrasi Permission Engine |
| `Resources/` | Transformer output API (JSON Resource) |
| `Views/` | Template Blade khusus modul |
| `Helpers/` | Fungsi pembantu spesifik modul |
| `Providers/` | Service Provider yang meregistrasi modul |
| `Tests/` | Unit & feature test modul |

---

## 6 themes/

```text
themes/
└── default/
    ├── layouts/          # Layout induk (app, auth, admin)
    ├── pages/            # Halaman penuh
    ├── components/       # Komponen Blade reusable
    ├── assets/           # Aset sumber tema
    │   ├── css/          # Stylesheet
    │   ├── js/           # Script
    │   ├── images/       # Gambar tema
    │   └── fonts/        # Font kustom
    ├── theme.json        # Manifest tema (metadata & config)
    └── preview.png       # Pratinjau tema untuk selektor
```

| Folder/File | Fungsi |
|:---|:---|
| `layouts/` | Layout induk yang membungkus halaman |
| `pages/` | View halaman penuh spesifik tema |
| `components/` | Komponen Blade yang dapat digunakan ulang |
| `assets/css/` | Stylesheet sumber (dikompilasi Vite) |
| `assets/js/` | Script sumber tema |
| `assets/images/` | Gambar dan grafis tema |
| `assets/fonts/` | Berkas font kustom |
| `theme.json` | Manifest berisi nama, versi, author, dan opsi konfigurasi |
| `preview.png` | Gambar pratinjau untuk selektor tema di Theme Engine |

> Semua warna berasal dari **Theme Engine** — dilarang hardcode warna di dalam view.

---

## 7 plugins/

```text
plugins/
└── PluginName/
    ├── plugin.json       # Manifest plugin (metadata & dependency)
    ├── src/              # Kode sumber plugin
    ├── resources/        # View & aset plugin
    ├── config/           # Konfigurasi plugin
    ├── routes/           # Rute plugin
    ├── database/         # Migration & seeder plugin
    └── tests/            # Pengujian plugin
```

| Folder/File | Fungsi |
|:---|:---|
| `plugin.json` | Manifest berisi nama, versi, author, dependency, dan provider |
| `src/` | Kode sumber utama plugin (kelas & service) |
| `resources/` | View Blade dan aset plugin |
| `config/` | File konfigurasi plugin |
| `routes/` | Definisi rute yang didaftarkan plugin |
| `database/` | Migration dan seeder milik plugin |
| `tests/` | Unit & feature test plugin |

---

## 8 resources/

```text
resources/
├── views/            # Blade view global
├── css/              # Stylesheet sumber global
├── js/               # Script sumber global
├── images/           # Gambar sumber global
└── lang/             # File terjemahan (i18n)
```

| Folder | Fungsi |
|:---|:---|
| `views/` | Template Blade global (di luar tema/modul) |
| `css/` | Stylesheet sumber yang dikompilasi Vite |
| `js/` | Script sumber yang dikompilasi Vite |
| `images/` | Gambar sumber yang diproses saat build |
| `lang/` | Berkas terjemahan (UI Bahasa Indonesia) |

---

## 9 public/

```text
public/
├── themes/           # Aset tema hasil kompilasi
├── storage/          # Symlink ke storage/app/public
├── uploads/          # Berkas unggahan publik
└── favicon/          # Favicon & app icon
```

| Folder | Fungsi |
|:---|:---|
| `themes/` | Aset tema (css/js/gambar) hasil kompilasi Vite |
| `storage/` | Symlink `php artisan storage:link` menuju `storage/app/public` |
| `uploads/` | Berkas unggahan yang dapat diakses publik |
| `favicon/` | Favicon dan ikon aplikasi berbagai ukuran |

> `public/` adalah **web root**. Hanya berkas yang aman diakses publik yang boleh ditempatkan di sini.

---

## 10 storage/

```text
storage/
├── app/              # Berkas aplikasi (private & public)
├── framework/        # Cache, session, view terkompilasi
├── logs/             # Berkas log aplikasi
├── backups/          # Arsip backup (Backup Engine)
└── temp/             # Berkas sementara
```

| Folder | Fungsi |
|:---|:---|
| `app/` | Penyimpanan berkas aplikasi (private dan public) |
| `framework/` | Cache framework, session, dan Blade terkompilasi |
| `logs/` | Berkas log (`laravel.log` dan channel kustom) |
| `backups/` | Arsip backup database & file oleh Backup Engine |
| `temp/` | Berkas sementara yang dibersihkan secara berkala |

---

## 11 database/

```text
database/
├── migrations/       # Skema perubahan tabel
├── seeders/          # Data awal / dummy
├── factories/        # Factory data untuk testing
└── schema/           # Dump skema & definisi blueprint
```

| Folder | Fungsi |
|:---|:---|
| `migrations/` | Definisi perubahan skema tabel (versioned) |
| `seeders/` | Data awal sistem dan data dummy |
| `factories/` | Factory untuk pembuatan data pengujian |
| `schema/` | Dump skema SQL dan definisi blueprint terstruktur |

---

## 12 config/

Daftar seluruh file konfigurasi CosmicLib Engine:

| File | Fungsi |
|:---|:---|
| `app.php` | Konfigurasi inti aplikasi (nama, timezone, locale) |
| `auth.php` | Konfigurasi autentikasi dan guard |
| `cache.php` | Driver dan penyimpanan cache |
| `database.php` | Koneksi database dan pool |
| `mail.php` | Konfigurasi pengiriman email |
| `queue.php` | Konfigurasi antrian job |
| `session.php` | Konfigurasi session |
| `theme.php` | Konfigurasi **Theme Engine** |
| `module.php` | Konfigurasi **Module Engine** |
| `permission.php` | Konfigurasi **Permission Engine** |
| `menu.php` | Konfigurasi **Menu Engine** |
| `library.php` | Konfigurasi modul Library |
| `installer.php` | Konfigurasi **Installer Engine** |
| `backup.php` | Konfigurasi **Backup Engine** |
| `update.php` | Konfigurasi **Update Engine** |
| `license.php` | Konfigurasi **License Engine** |

> Seluruh konfigurasi berasal dari **Setting Engine**. Dilarang menggunakan `env()` di luar file config.

---

## 13 routes/

```text
routes/
├── web.php           # Rute web (session, CSRF)
├── api.php           # Rute API (stateless)
├── console.php       # Rute perintah Artisan
├── channels.php      # Rute broadcasting channel
└── modules.php       # Loader rute modul dinamis
```

| File | Fungsi |
|:---|:---|
| `web.php` | Rute antarmuka web dengan middleware session & CSRF |
| `api.php` | Rute API stateless dengan prefix `/api` |
| `console.php` | Definisi perintah Artisan berbasis closure |
| `channels.php` | Autorisasi channel broadcasting |
| `modules.php` | Registrasi rute modul secara dinamis oleh Module Engine |

---

## 14 Naming Convention

Konvensi penamaan **folder** dan kapan tiap gaya digunakan:

| Gaya | Contoh | Kapan Digunakan |
|:---|:---|:---|
| **PascalCase** | `Controllers/`, `Services/`, `Library/` | Folder yang memuat kelas PHP (namespace) dan nama modul/plugin |
| **camelCase** | `helperFunctions`, `myVariable` | Nama variabel dan method di dalam kode |
| **snake_case** | `module.json`, `theme.php`, `borrow_records` | Nama file config, tabel database, dan kolom |
| **kebab-case** | `default/`, `book-detail.blade.php` | Slug tema, view Blade, dan aset publik |

**Ringkasan aturan folder:**
- Folder berisi kelas PHP → `PascalCase` (mengikuti PSR-4 namespace)
- Folder non-kelas (aset, config) → `lowercase`/`kebab-case`

---

## 15 File Naming Standard

| Tipe File | Konvensi | Contoh |
|:---|:---|:---|
| **Controller** | `PascalCase` + suffix `Controller` | `BookController.php` |
| **Service** | `PascalCase` + suffix `Service` | `BorrowService.php` |
| **Repository** | `PascalCase` + suffix `Repository` | `BookRepository.php` |
| **Policy** | `PascalCase` + suffix `Policy` | `BookPolicy.php` |
| **Request** | `PascalCase` + suffix `Request` | `StoreBookRequest.php` |
| **Migration** | `snake_case` + timestamp | `2026_01_01_000000_create_books_table.php` |
| **Seeder** | `PascalCase` + suffix `Seeder` | `BookSeeder.php` |
| **Blade** | `kebab-case` + `.blade.php` | `book-detail.blade.php` |
| **Theme** | `kebab-case` (folder) + `theme.json` | `default/theme.json` |
| **Plugin** | `PascalCase` (folder) + `plugin.json` | `SeoBooster/plugin.json` |

**Bahasa:** Seluruh nama kode (kelas, variabel, method, tabel, kolom, route) menggunakan **Bahasa Inggris**; seluruh teks UI (label, pesan, notifikasi) menggunakan **Bahasa Indonesia**.

---

## 16 Module Standard

Setiap modul **WAJIB** menyertakan berkas dan komponen berikut:

| Wajib | Fungsi |
|:---|:---|
| `module.json` | Manifest metadata modul |
| `permission.php` | Definisi permission (dibaca **Permission Engine**) |
| `menu.php` | Definisi menu (dibaca **Menu Engine**) |
| `config.php` | Konfigurasi modul (dibaca **Setting Engine**) |
| `ServiceProvider` | Registrasi modul ke aplikasi |
| `Tests/` | Pengujian modul (unit & feature) |
| `README.md` | Dokumentasi ringkas modul |

> Modul dilarang hardcode role, permission, atau menu. Semua berasal dari engine terkait.

---

## 17 Theme Standard

Setiap tema **WAJIB** menyertakan:

| Wajib | Fungsi |
|:---|:---|
| `theme.json` | Manifest metadata tema |
| `preview.png` | Pratinjau tema |
| `layouts/` | Layout induk |
| `components/` | Komponen Blade reusable |
| `assets/` | Aset sumber (css, js, images, fonts) |
| Konfigurasi | Opsi tema yang dikelola **Theme Engine** |

> Warna, tipografi, dan token desain wajib melalui **Theme Engine** — dilarang hardcode.

---

## 18 Plugin Standard

Setiap plugin **WAJIB** mendefinisikan:

| Wajib | Fungsi |
|:---|:---|
| `plugin.json` | Manifest metadata plugin |
| `version` | Versi plugin (semantic versioning) |
| `author` | Informasi pembuat plugin |
| `dependency` | Daftar ketergantungan plugin/modul |
| `provider` | Service Provider plugin |
| `installer` | Rutin instalasi/uninstalasi plugin |

---

## 19 AI Rules

Aturan khusus untuk seluruh AI assistant (Claude, Codex, Cline, ChatGPT, AI Studio):

1. **Dilarang membuat folder baru tanpa analisis** — setiap penambahan folder harus dijustifikasi terhadap struktur ini.
2. **Wajib mengikuti struktur folder** yang didefinisikan dalam dokumen ini.
3. **Wajib menjelaskan** setiap usulan perubahan struktur sebelum menerapkannya.
4. **Dilarang menghapus** file atau folder yang sudah ada.
5. **Dilarang membuat duplicate code** — gunakan Service Layer dan pola yang sudah ada.
6. **Hormati batas engine** — menu dari Menu Engine, permission dari Permission Engine, warna dari Theme Engine, config dari Setting Engine, modul dari Module Engine.

---

## 20 Best Practice

Struktur folder ini menerapkan prinsip-prinsip berikut:

| Prinsip | Penerapan |
|:---|:---|
| **Laravel Best Practice** | Thin controller, Service Layer, Form Request, Service Provider |
| **SOLID** | Single Responsibility per folder, Dependency Inversion via `Contracts/` |
| **PSR-12** | Struktur namespace PSR-4, penamaan kelas konsisten |
| **Modular** | Fitur terisolasi dalam `modules/` yang dapat diaktifkan/dinonaktifkan |
| **Scalable** | Penambahan modul/plugin/tema tanpa mengubah Core |
| **Reusable** | `Traits/`, `Support/`, `components/` untuk komponen berbagi |

---

## Referensi

- [03_ARCHITECTURE.md](03_ARCHITECTURE.md)
- [04_TECH_STACK.md](04_TECH_STACK.md)
- [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md)
- [09_THEME_ENGINE.md](09_THEME_ENGINE.md)
- [13_PLUGIN_ENGINE.md](13_PLUGIN_ENGINE.md)
- [23_CODING_STANDARD.md](23_CODING_STANDARD.md)
- [AGENTS.md](../AGENTS.md)

## Catatan

- Struktur `app/`, `modules/`, `themes/`, `plugins/`, dan Laravel lainnya adalah **proyeksi Fase 2** dan belum di-scaffold pada fase Blueprint saat ini.
- Hormati batas direktori — jangan menempatkan file di luar konvensi yang ditetapkan.
- Referensi file `AGENTS.md` untuk aturan terkini tentang penempatan file.
- Beberapa penyempurnaan dari rancangan awal: penambahan `assets/css|js|images|fonts` pada tema, `lang/` di root untuk i18n, dan penegasan aliran ketergantungan antar lapisan pada bagian Architecture Folder.
