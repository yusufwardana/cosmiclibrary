# 🌌 CosmicLib Engine — Architecture

> **Spesifikasi arsitektur utama** CosmicLib Engine.
>
> Seluruh AI (Claude, Codex, Cline, ChatGPT, AI Studio, GitHub Copilot) **WAJIB** membaca dokumen ini sebelum mengubah kode.
>
> Baca setelah [`01_PROJECT_OVERVIEW.md`](01_PROJECT_OVERVIEW.md) dan [`02_VISION.md`](02_VISION.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/03_ARCHITECTURE.md` |
| **Versi** | 1.0 |
| **Status** | `🟡 Blueprint` — acuan desain sebelum coding |
| **Pola** | Modular CMS Engine (Modular Monolith) |
| **Framework** | Laravel 12 · PHP 8.3+ · MySQL 8+ |
| **Frontend** | Blade · Bootstrap 5 · Vite |
| **Hosting** | Shared Hosting cPanel · VPS Linux · Cloud |
| **Bahasa UI** | Bahasa Indonesia |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Architecture Principles](#2-architecture-principles)
3. [Architecture Overview](#3-architecture-overview)
4. [Layer Architecture](#4-layer-architecture)
5. [Core Engine](#5-core-engine)
6. [Engine Ecosystem](#6-engine-ecosystem)
7. [Module Architecture](#7-module-architecture)
8. [Theme Architecture](#8-theme-architecture)
9. [Permission Architecture](#9-permission-architecture)
10. [Menu Architecture](#10-menu-architecture)
11. [Plugin Architecture](#11-plugin-architecture)
12. [Widget Architecture](#12-widget-architecture)
13. [System Setting Architecture](#13-system-setting-architecture)
14. [Data Flow](#14-data-flow)
15. [Dependency Rule](#15-dependency-rule)
16. [Security Architecture](#16-security-architecture)
17. [Deployment Architecture](#17-deployment-architecture)
18. [AI Architecture](#18-ai-architecture)
19. [Future Architecture](#19-future-architecture)
20. [Architecture Decision](#20-architecture-decision)
21. [Architecture Checklist](#21-architecture-checklist)

---

## 1. Pendahuluan

Dokumen Architecture adalah **kontrak desain** CosmicLib Engine. Tujuannya:

1. Menjelaskan bagaimana sistem disusun secara konseptual dan teknis tingkat tinggi.
2. Menjadi referensi tunggal hubungan antar engine, lapisan, dan alur request.
3. Menuntun developer dan AI agar perubahan kode tidak melanggar batas arsitektur.
4. Menjaga kompatibilitas dengan visi jangka panjang (platform CMS modular pendidikan).

### Peran Architecture dalam CosmicLib

| Peran | Penjelasan |
| :--- | :--- |
| **Guardrail** | Mencegah hardcode, coupling liar, dan logika bisnis di controller. |
| **SSOT desain** | Keputusan struktur diambil di sini sebelum implementasi. |
| **Peta navigasi** | Menunjuk dokumen engine detail (`07`–`20`) untuk spesifikasi mendalam. |
| **Dasar evolusi** | Future architecture direncanakan tanpa merusak fondasi Modular Monolith. |

> **Keputusan penting:** CosmicLib memilih **Modular Monolith**, bukan microservices. Satu aplikasi Laravel, banyak modul/engine logis—lebih realistis untuk sekolah dan shared hosting.

---

## 2. Architecture Principles

| Prinsip | Makna dalam CosmicLib |
| :--- | :--- |
| **Modular** | Fitur domain dan kapabilitas platform dipisah menjadi modul/engine mandiri. |
| **Clean Architecture** | Ketergantungan mengarah ke dalam; domain tidak bergantung pada detail UI/DB. |
| **Separation of Concerns** | Presentasi, aplikasi, domain, infrastruktur, dan persistensi punya tanggung jawab jelas. |
| **SOLID** | Desain kelas mematuhi Single Responsibility hingga Dependency Inversion. |
| **DRY** | Hindari duplikasi; reuse service, policy, dan kontrak engine. |
| **KISS** | Pilih solusi sederhana yang cukup untuk skala sekolah. |
| **YAGNI** | Jangan bangun abstraksi spekulatif sebelum kebutuhan nyata. |
| **Convention over Configuration** | Ikuti konvensi Laravel + konvensi CosmicLib untuk mengurangi wiring manual. |
| **Configuration over Hardcode** | Menu, role, permission, warna, dan setting bisnis dari engine—bukan literal di kode. |
| **Extensible** | Titik ekstensi resmi: Module, Theme, Plugin, Widget. |
| **Maintainable** | Mudah dipelihara lintas kontributor manusia dan AI. |
| **Secure by Default** | AuthN/AuthZ, validasi, CSRF, dan policy aktif secara default. |
| **AI Friendly** | Struktur, naming, dan dokumen memudahkan AI menghasilkan perubahan konsisten. |

---

## 3. Architecture Overview

CosmicLib Engine adalah **Modular CMS Engine**: satu codebase Laravel dengan engine inti + modul domain (misalnya katalog, sirkulasi) yang dimuat dinamis.

```text
┌─────────────────────────────────────────────────────────────┐
│                     CosmicLib Engine                        │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │  Core   │ │ Module  │ │ Theme   │ │Permission│          │
│  │ Engine  │ │ Engine  │ │ Engine  │ │ Engine  │ ...       │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│                          │                                  │
│                          ▼                                  │
│              ┌───────────────────────┐                      │
│              │  Domain Modules       │                      │
│              │  (Library, …)         │                      │
│              └───────────────────────┘                      │
└─────────────────────────────────────────────────────────────┘
```

### Alur request tingkat tinggi

```text
Client
  ↓
HTTP Request
  ↓
Laravel Router
  ↓
Middleware
  ↓
Authentication
  ↓
Authorization
  ↓
Module Engine
  ↓
Business Service
  ↓
Repository
  ↓
Database
  ↓
Response (Blade / JSON)
```

---

## 4. Layer Architecture

```text
┌──────────────────────────────────────┐
│     Presentation Layer               │  Blade, HTTP resources, API response
├──────────────────────────────────────┤
│     Application Layer                │  Controllers, Form Requests, Jobs, Commands
├──────────────────────────────────────┤
│     Domain Layer                     │  Services, Policies, Domain rules/events
├──────────────────────────────────────┤
│     Infrastructure Layer             │  Mail, storage, queue, cache, notifikasi pihak ketiga
├──────────────────────────────────────┤
│     Persistence Layer                │  Repositories, Eloquent Models, Migrations
└──────────────────────────────────────┘
```

| Layer | Tanggung jawab | Contoh artefak |
| :--- | :--- | :--- |
| **Presentation** | Menampilkan UI / membentuk response | Blade views, API Resources |
| **Application** | Orkestrasi request, validasi input, thin controller | Controllers, Form Requests |
| **Domain** | Aturan bisnis dan otorisasi | Services, Policies |
| **Infrastructure** | Integrasi teknis ke luar aplikasi | Mailer, filesystem, WhatsApp gateway |
| **Persistence** | Akses dan representasi data | Repositories, Models, Migrations |

> Controller berada di Application Layer dan **tidak** berisi query database atau aturan bisnis kompleks.

---

## 5. Core Engine

**Core Engine** adalah tulang punggung lifecycle aplikasi. Detail operasional ada di [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md).

Core Engine bertanggung jawab terhadap:

| Area | Peran |
| :--- | :--- |
| **Bootstrap** | Inisialisasi aplikasi dan registrasi engine. |
| **Service Provider** | Wiring dependency & boot engine/modul. |
| **Configuration** | Memuat config file + sinkronisasi dengan Setting Engine. |
| **Event** | Bus event inti antar komponen. |
| **Exception** | Penanganan error terpusat & respons ramah pengguna. |
| **Logging** | Logging aplikasi dan audit teknis. |
| **Queue** | Antrian pekerjaan (disesuaikan kapabilitas hosting). |
| **Cache** | Cache konfigurasi/menu/permission (prefer file cache di shared hosting). |
| **Localization** | Locale default Bahasa Indonesia untuk UI. |

---

## 6. Engine Ecosystem

Seluruh kapabilitas platform diekspos sebagai **engine**. Menu, permission, warna, konfigurasi, modul, widget, dan plugin **wajib** melalui engine terkait.

```text
                    ┌──────────────┐
                    │ Core Engine  │
                    └──────┬───────┘
           ┌───────────────┼───────────────┐
           ▼               ▼               ▼
    Auth / Permission   Module / Menu   Theme / Widget
           │               │               │
           └───────┬───────┴───────┬───────┘
                   ▼               ▼
            Plugin / Media   Setting / Notify
                   │               │
                   ▼               ▼
         Installer / Backup / Update / License
```

| Engine | Tanggung jawab ringkas | Dokumen |
| :--- | :--- | :--- |
| **Authentication Engine** | Login, session/guard, reset kredensial | terkait Permission / Core |
| **Permission Engine** | ACL, gate, policy integration | [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md) |
| **Role Engine** | Manajemen role sebagai bagian dari model RBAC (subsistem Permission) | [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md) |
| **Menu Engine** | Navigasi dinamis berbasis permission | [`11_MENU_ENGINE.md`](11_MENU_ENGINE.md) |
| **Theme Engine** | Layout, token visual, dark mode | [`09_THEME_ENGINE.md`](09_THEME_ENGINE.md) |
| **Module Engine** | Load, enable/disable, lifecycle modul | [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md) |
| **Widget Engine** | Widget dashboard yang dapat dikonfigurasi | [`12_WIDGET_ENGINE.md`](12_WIDGET_ENGINE.md) |
| **Plugin Engine** | Ekstensi pihak ketiga ber-lifecycle | [`13_PLUGIN_ENGINE.md`](13_PLUGIN_ENGINE.md) |
| **Media Engine** | Upload, storage, optimasi file | [`14_MEDIA_ENGINE.md`](14_MEDIA_ENGINE.md) |
| **Notification Engine** | Email, WhatsApp, in-app | [`15_NOTIFICATION_ENGINE.md`](15_NOTIFICATION_ENGINE.md) |
| **Setting Engine** | Konfigurasi key-value dari database | [`16_SETTING_ENGINE.md`](16_SETTING_ENGINE.md) |
| **Installer Engine** | Wizard instalasi web | [`17_INSTALLER_ENGINE.md`](17_INSTALLER_ENGINE.md) |
| **Backup Engine** | Backup & restore | [`18_BACKUP_ENGINE.md`](18_BACKUP_ENGINE.md) |
| **Update Engine** | Pembaruan sistem | [`19_UPDATE_ENGINE.md`](19_UPDATE_ENGINE.md) |
| **License Engine** | Lisensi & aktivasi | [`20_LICENSE_ENGINE.md`](20_LICENSE_ENGINE.md) |
| **Audit Log Engine** | Jejak perubahan sensitif (siapa/apa/kapan) | Blueprint lanjutan |
| **Activity Log Engine** | Jejak aktivitas pengguna umum | Blueprint lanjutan |
| **API Engine** | Kontrak REST & autentikasi API | [`21_API_GUIDELINE.md`](21_API_GUIDELINE.md) |
| **Search Engine** | Penelusuran katalog/konten (fase lanjutan) | Blueprint lanjutan |

> **Catatan evolusi:** Authentication, Role, Audit/Activity Log, API, dan Search diperjelas sebagai kapabilitas arsitektur eksplisit. **Role** diimplementasikan sebagai bagian integral Permission Engine (bukan engine terpisah yang menduplikasi ACL), agar tetap DRY dan selaras visi Modular CMS.

---

## 7. Module Architecture

Setiap modul domain (contoh: modul Buku, Sirkulasi) adalah paket mandiri yang dimuat Module Engine.

### Struktur kanonik modul

```text
modules/<ModuleName>/
├── module.json          # metadata, versi, dependency
├── routes.php           # registrasi route modul
├── permissions.php      # deklarasi permission modul
├── menu.php             # deklarasi menu modul
├── config.php           # konfigurasi modul
├── Controllers/
├── Services/
├── Repositories/
├── Models/
├── Policies/
├── Requests/
├── Resources/
├── Views/
├── Database/            # migrations, seeders, factories
└── Tests/
```

| Artefak | Fungsi |
| :--- | :--- |
| `module.json` | Identitas, versi, dependensi antar modul |
| `permissions.php` / `menu.php` | Registrasi ke Permission & Menu Engine |
| `Services/` | Aturan bisnis modul |
| `Repositories/` | Akses data modul |
| `Policies/` | Otorisasi berbasis permission |

Modul **tidak** boleh hardcode menu/role/warna. Deklarasi masuk ke engine melalui file registrasi.

---

## 8. Theme Architecture

Theme Engine mengelola pengalaman visual tanpa mengubah logika bisnis.

| Komponen tema | Fungsi |
| :--- | :--- |
| **Layout** | Kerangka halaman (app, auth, print) |
| **Components** | Partial Blade yang dapat dipakai ulang |
| **Assets** | CSS/JS/gambar tema (dibundle Vite) |
| **CSS Variables** | Token warna, spasi, radius |
| **Typography** | Hierarki tipografi |
| **Color Palette** | Palet resmi tema (dari Theme Engine, bukan hardcode) |
| **Icons** | Set ikon konsisten |
| **Dark Mode** | Mode gelap siap pakai |
| **Theme Configuration** | Opsi tema (logo, warna sekolah) |
| **Theme Preview** | Pratinjau sebelum aktivasi |
| **Theme Installer** | Pasang/aktifkan/nonaktifkan tema |

---

## 9. Permission Architecture

CosmicLib menggunakan **RBAC (Role-Based Access Control)**.

```text
User ──▶ Role(s) ──▶ Permission(s)
                         │
         ┌───────────────┼───────────────┐
         ▼               ▼               ▼
   Menu Permission  Module Permission  Action Permission
         │               │               │
         └───────────────┴───────┬───────┘
                                 ▼
                    Policy + Middleware Gate
```

| Elemen | Peran |
| :--- | :--- |
| **Role** | Kumpulan permission (Admin, Pustakawan, Guru, Siswa, …) |
| **Permission** | Hak atomik (`books.view`, `circulation.borrow`, …) |
| **Menu Permission** | Menentukan menu yang terlihat |
| **Module Permission** | Mengontrol akses fitur modul |
| **Action Permission** | Mengontrol aksi CRUD / operasi spesifik |
| **Policy** | Keputusan otorisasi di Domain Layer |
| **Middleware** | Penjaga di Application Layer |

> Role dan permission **tidak di-hardcode** di controller. Sumber kebenaran: Permission Engine + registrasi modul.

---

## 10. Menu Architecture

Menu berasal dari **Module Engine** (dan plugin) melalui **Menu Engine**. Menu bersifat dinamis, difilter oleh permission pengguna aktif.

Menu mendukung:

| Fitur | Keterangan |
| :--- | :--- |
| **Nested** | Hierarki induk–anak |
| **Icon** | Ikon navigasi |
| **Order** | Urutan tampil |
| **Permission** | Visibility berdasarkan hak akses |
| **Visibility** | Aturan tampil tambahan (aktif modul, context) |
| **Badge** | Indikator angka/status |
| **Shortcut** | Pintasan navigasi |

---

## 11. Plugin Architecture

Plugin memperluas sistem tanpa memodifikasi core secara langsung.

Plugin harus mendukung:

| Lifecycle | Keterangan |
| :--- | :--- |
| **Installable** | Dapat dipasang dari paket plugin |
| **Enable / Disable** | Aktif/nonaktif tanpa uninstall |
| **Update** | Pembaruan versi terkontrol |
| **Uninstall** | Pelepasan bersih + rollback registrasi |
| **Dependency** | Deklarasi ketergantungan engine/modul |
| **Version** | Semantic versioning |
| **Lifecycle hooks** | Boot, activate, deactivate, uninstall |

Plugin wajib mematuhi Dependency Rule dan Engine Principle yang sama dengan modul.

---

## 12. Widget Architecture

Widget Engine mengelola komponen dashboard yang dapat disusun pengguna berwenang.

| Kapabilitas | Keterangan |
| :--- | :--- |
| **Pasang** | Menambahkan widget ke dashboard |
| **Pindah** | Mengubah posisi / urutan |
| **Hapus** | Melepas dari dashboard |
| **Configurable** | Opsi per-instance (filter, limit, periode) |

Widget hanya membaca data melalui Service/Repository; tidak boleh query database langsung dari view.

---

## 13. System Setting Architecture

Semua konfigurasi bisnis dan operasional berasal dari **Setting Engine** (database key-value), bukan hardcode.

| Kategori | Contoh |
| :--- | :--- |
| **General** | Nama aplikasi, timezone, locale |
| **School** | Identitas sekolah, logo, alamat |
| **SMTP** | Konfigurasi email |
| **WhatsApp** | Gateway & template notifikasi |
| **Theme** | Tema aktif & token turunan |
| **Library** | Lama pinjam, denda, kuota |
| **Backup** | Jadwal & retensi backup |
| **Security** | Kebijakan sandi, session, rate limit |
| **API** | Token, throttle, versioning |

> `env()` hanya untuk secrets/bootstrap di file `config/`. Nilai bisnis runtime → Setting Engine.

---

## 14. Data Flow

```text
User
  ↓
Route
  ↓
Middleware          (auth, permission, locale, …)
  ↓
Controller          (thin — validasi via Form Request)
  ↓
Service             (aturan bisnis)
  ↓
Repository          (akses data)
  ↓
Model               (Eloquent entity)
  ↓
Database            (MySQL 8+)
  ↓
Response            (Blade / JSON Resource)
```

Alur yang sama berlaku untuk Command/Job: masuk ke Service, bukan langsung ke Model dari entrypoint.

---

## 15. Dependency Rule

| Lapisan | Boleh | Tidak boleh |
| :--- | :--- | :--- |
| **Controller** | Memanggil Service; menerima Form Request | Query DB langsung; aturan bisnis kompleks |
| **Service** | Orkestrasi domain; memanggil Repository & Policy | Bergantung pada detail Blade |
| **Repository** | Query Eloquent / builder | Aturan bisnis lintas aggregate |
| **Model** | Representasi entity & relasi | Menyembunyikan seluruh use-case bisnis |

Aturan inti:

1. **Controller tidak boleh langsung query database.**
2. **Business logic berada di Service.**
3. **Repository hanya akses data.**
4. **Model hanya representasi entity.**

---

## 16. Security Architecture

| Kontrol | Penerapan arsitektur |
| :--- | :--- |
| **Authentication** | Authentication Engine / guard Laravel |
| **Authorization** | RBAC + Policy + Middleware |
| **CSRF** | Proteksi form state-changing |
| **XSS** | Escape Blade; sanitasi input |
| **SQL Injection** | Eloquent/Query Builder; binding wajib |
| **Validation** | Form Request sebelum Service |
| **Encryption** | Secrets & data sensitif sesuai Laravel crypt |
| **Audit Log** | Jejak perubahan kritis (fase lanjutan wajib untuk operasi sensitif) |

Detail: [`22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md).

---

## 17. Deployment Architecture

```text
                 ┌──────────────────┐
                 │ CosmicLib Build  │
                 │ (Vite di CI/Dev) │
                 └────────┬─────────┘
                          │
        ┌─────────────────┼─────────────────┐
        ▼                 ▼                 ▼
 Shared Hosting         VPS Linux          Cloud
   (cPanel)           (Nginx/Apache)    (VM/PaaS)
        │                 │                 │
        └────────────┬────┴─────────────────┘
                     ▼
              MySQL 8+ + Storage
                     │
                     ▼
              Docker (Future)
```

| Target | Strategi |
| :--- | :--- |
| **Shared Hosting cPanel** | Target utama; asset prebuilt; cache file; installer web |
| **VPS Linux** | Kontrol penuh; queue worker & scheduler native |
| **Cloud** | Setara VPS dengan praktik 12-factor yang disesuaikan |
| **Docker** | Future — kontainerisasi resmi tanpa mematahkan jalur cPanel |

---

## 18. AI Architecture

AI adalah bagian dari proses pengembangan, bukan pengganti kontrak arsitektur.

Workflow AI yang wajib:

```text
Membaca Blueprint
    ↓
Membaca Manifest
    ↓
Membaca Architecture (dokumen ini)
    ↓
Analisis
    ↓
Rencana & daftar file
    ↓
Implementasi
    ↓
Testing
    ↓
Dokumentasi / CHANGELOG
```

AI wajib menolak atau menegosiasikan permintaan yang memaksa hardcode menu/role/warna, melewati Service Layer, atau mengubah skema tanpa migration.

Referensi: [`00_SYSTEM_PROMPT.md`](00_SYSTEM_PROMPT.md) · [`31_AI_GUIDELINE.md`](31_AI_GUIDELINE.md)

---

## 19. Future Architecture

Kapabilitas berikut direncanakan **di atas** fondasi Modular Monolith saat ini (tanpa memaksa microservices dini):

| Kapabilitas | Arah |
| :--- | :--- |
| **Multi School** | Isolasi data multi-tenant / multi-unit yayasan |
| **REST API** | API Engine sebagai pintu resmi integrasi |
| **Mobile API** | Kontrak khusus klien mobile |
| **Plugin Marketplace** | Distribusi plugin terkurasi |
| **Theme Marketplace** | Distribusi tema terkurasi |
| **Cloud Sync** | Sinkronisasi terbatas antar instance |
| **Realtime Notification** | Push/websocket bila infrastruktur memungkinkan |
| **AI Assistant** | Asisten operasional pustakawan di dalam produk |

> Evolusi ke depan **tidak** boleh merusak Dependency Rule, Engine Principle, dan dukungan shared hosting sebagai jalur utama.

---

## 20. Architecture Decision

| Keputusan | Alasan |
| :--- | :--- |
| **Laravel 12** | Ekosistem matang, konvensi kuat, cocok Modular Monolith + shared hosting. |
| **Blade** | Rendering server-side sederhana, aman default, selaras Bootstrap. |
| **Bootstrap 5** | Cepat, familiar bagi developer sekolah/vendor, cukup untuk admin UI. |
| **MySQL 8+** | Standar shared hosting Indonesia; tooling & skill umum tersedia. |
| **RBAC** | Cocok multi-peran sekolah; mudah diaudit dan dikonfigurasi. |
| **Module Engine** | Isolasi domain (katalog, sirkulasi) tanpa microservices. |
| **Theme Engine** | Branding sekolah tanpa fork aplikasi. |
| **Plugin Engine** | Ekstensi komunitas tanpa mengotori core. |
| **Modular Monolith** (bukan microservices) | Operasional lebih sederhana untuk skala sekolah; latency & DevOps lebih rendah. |
| **File cache sebagai default shared hosting** | Redis/Memcached sering tidak tersedia di cPanel. |

Keputusan di atas selaras visi: modern, aman, mudah dikembangkan, AI Friendly, dan realistis untuk sekolah Indonesia.

---

## 21. Architecture Checklist

Sebelum coding aplikasi (keluar dari Fase Blueprint), pastikan:

- [ ] Blueprint selesai
- [ ] Documentation selesai
- [ ] Database Design selesai
- [ ] Module design selesai
- [ ] Theme design selesai
- [ ] Permission design selesai
- [ ] Menu design selesai
- [ ] Dependency Rule dipahami tim/AI
- [ ] Engine Principle dipahami tim/AI
- [ ] Security baseline terdokumentasi

---

## Referensi

| Dokumen | Peran |
| :--- | :--- |
| [`00_SYSTEM_PROMPT.md`](00_SYSTEM_PROMPT.md) | Standar perilaku AI |
| [`01_PROJECT_OVERVIEW.md`](01_PROJECT_OVERVIEW.md) | Gambaran proyek |
| [`02_VISION.md`](02_VISION.md) | Visi jangka panjang |
| [`04_TECH_STACK.md`](04_TECH_STACK.md) | Detail stack |
| [`05_FOLDER_STRUCTURE.md`](05_FOLDER_STRUCTURE.md) | Struktur folder |
| [`06_DATABASE_DESIGN.md`](06_DATABASE_DESIGN.md) | Desain database |
| [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md) … [`20_LICENSE_ENGINE.md`](20_LICENSE_ENGINE.md) | Spesifikasi per engine |
| [`22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md) | Keamanan |
| [`23_CODING_STANDARD.md`](23_CODING_STANDARD.md) | Standar kode |
| [`PROJECT_MANIFEST.md`](../PROJECT_MANIFEST.md) | Manifest teknis |

## Catatan

- Dokumen ini adalah **arsitektur tingkat tinggi**. Spesifikasi operasional tiap engine ada di file terpisah.
- Perubahan arsitektur material wajib dicatat di `CHANGELOG.md` dan tidak boleh mematahkan visi Modular CMS Engine.

---

*CosmicLib Engine v1.0 — Sprint 1 · Prompt 006*
