# 🌌 06 — Database Design

> **Spesifikasi database resmi** CosmicLib Engine v1.0.
>
> Seluruh AI (Claude, Codex, Cline, ChatGPT, AI Studio, GitHub Copilot), developer, dan kontributor **WAJIB** membaca dan mengikuti dokumen ini sebelum membuat Migration, Model, atau Query Database.
>
> Baca setelah [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) dan [`05_FOLDER_STRUCTURE.md`](05_FOLDER_STRUCTURE.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/06_DATABASE_DESIGN.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Final Blueprint` — acuan resmi desain database |
| **Engine** | CosmicLib Engine |
| **Product** | CosmicLib Library |
| **Framework** | Laravel 12 · PHP 8.3+ |
| **Database** | MySQL 8+ |
| **Charset** | `utf8mb4` |
| **Collation** | `utf8mb4_unicode_ci` |
| **Timezone** | `Asia/Jakarta` (UTC+7) |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Database Philosophy](#2-database-philosophy)
3. [Database Architecture](#3-database-architecture)
4. [Database Convention](#4-database-convention)
5. [Core Tables](#5-core-tables)
6. [Library Tables](#6-library-tables)
7. [School Tables](#7-school-tables)
8. [CMS Tables](#8-cms-tables)
9. [System Tables](#9-system-tables)
10. [Module Tables](#10-module-tables)
11. [Relationship](#11-relationship)
12. [Naming Convention](#12-naming-convention)
13. [Standard Column](#13-standard-column)
14. [Migration Standard](#14-migration-standard)
15. [Seeder Strategy](#15-seeder-strategy)
16. [Database Security](#16-database-security)
17. [Performance](#17-performance)
18. [Backup Strategy](#18-backup-strategy)
19. [Future Database](#19-future-database)
20. [AI Rules](#20-ai-rules)
21. [ERD Blueprint](#21-erd-blueprint)
22. [Database Checklist](#22-database-checklist)

---

## 1. Pendahuluan

### 1.1 Tujuan Database Design

Dokumen ini adalah **kontrak desain database** CosmicLib Engine. Tujuannya:

1. Menjadi **Single Source of Truth (SSOT)** untuk seluruh rancangan database CosmicLib Engine.
2. Menetapkan aturan, konvensi, dan standar yang **wajib** diikuti sebelum membuat migration, model, atau query.
3. Memastikan database bersifat **modular**, **normalized**, **scalable**, **secure**, **maintainable**, **multi-module ready**, **AI friendly**, dan **shared hosting friendly**.
4. Mencegah inkonsistensi, duplikasi skema, dan pelanggaran arsitektur.

### 1.2 Peran Database dalam CosmicLib Engine

| Peran | Penjelasan |
| :--- | :--- |
| **Persistence Layer** | Menyimpan seluruh data bisnis, konfigurasi, dan state sistem. |
| **Engine Foundation** | Setiap engine (Permission, Menu, Setting, Module, dll.) memiliki tabel pendukung. |
| **Module Support** | Setiap modul domain (Library, School, CMS) memiliki tabel terisolasi. |
| **Audit Trail** | Menyimpan jejak perubahan dan aktivitas pengguna. |
| **Configuration Store** | Menyimpan konfigurasi dinamis yang tidak cocok di file `.env`. |
| **Queue & Job Store** | Menyimpan antrian pekerjaan dan status eksekusi. |

### 1.3 Dokumen Terkait

| Dokumen | Peran |
| :--- | :--- |
| [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) | Arsitektur sistem keseluruhan |
| [`24_DATABASE_SCHEMA.md`](24_DATABASE_SCHEMA.md) | Detail skema tabel (ERD, kolom, tipe data) |
| [`blueprint/database_schema.sql`](../blueprint/database_schema.sql) | Blueprint SQL referensi |
| [`23_CODING_STANDARD.md`](23_CODING_STANDARD.md) | Standar kode termasuk query |
| [`22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md) | Keamanan data |

---

## 2. Database Philosophy

Database CosmicLib Engine dirancang dengan filosofi berikut:

| Prinsip | Penjelasan |
| :--- | :--- |
| **Normalized** | Normalisasi hingga 3NF (Third Normal Form) untuk integritas data. Denormalisasi strategis hanya pada tabel laporan/statistik untuk performa. |
| **Modular** | Tabel dikelompokkan berdasarkan domain (Core, Library, School, CMS, System). Setiap modul memiliki tabel terisolasi yang tidak saling bergantung secara langsung. |
| **Consistent** | Semua tabel mengikuti konvensi penamaan, tipe data, dan struktur kolom yang seragam. |
| **Maintainable** | Perubahan skema dilakukan melalui migration yang terversioning. Tidak ada perubahan langsung ke database production. |
| **Expandable** | Skema dirancang agar modul baru dapat menambahkan tabel tanpa mengubah tabel core. |
| **Soft Delete Ready** | Setiap tabel data bisnis memiliki kolom `deleted_at` untuk mendukung pemulihan data. |
| **Audit Ready** | Setiap tabel memiliki kolom `created_by`, `updated_by`, `deleted_by` untuk jejak audit. |
| **AI Friendly** | Penamaan yang konsisten dan prediktif memudahkan AI menghasilkan query dan migration yang benar. |
| **Shared Hosting Friendly** | Tidak bergantung pada fitur database eksotis. Kompatibel dengan MySQL 8+ standar shared hosting. |

### Aturan Normalisasi

```text
┌─────────────────────────────────────────────────────────┐
│  1NF — Tidak ada kolom berisi nilai ganda/array         │
│  2NF — Tidak ada ketergantungan parsial pada PK         │
│  3NF — Tidak ada ketergantungan transitif               │
│  ──────────────────────────────────────────────────────  │
│  Denormalisasi hanya diizinkan pada:                    │
│  • Tabel statistik/reporting                            │
│  • Cache table untuk performa                           │
│  • Harus didokumentasikan alasannya                     │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Database Architecture

CosmicLib menggunakan arsitektur database berlapis yang mencerminkan arsitektur engine:

```text
┌─────────────────────────────────────────────────────────┐
│                   APPLICATION DATABASE                   │
│   (sessions, cache, jobs, failed_jobs, queue)            │
├─────────────────────────────────────────────────────────┤
│                    MODULE DATABASE                        │
│   ┌───────────┐  ┌───────────┐  ┌───────────┐          │
│   │  Library   │  │  School   │  │   CMS     │  ...     │
│   │  Tables    │  │  Tables   │  │  Tables   │          │
│   └───────────┘  └───────────┘  └───────────┘          │
├─────────────────────────────────────────────────────────┤
│                    ENGINE DATABASE                        │
│   ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │
│   │Permission│ │  Menu    │ │ Setting  │ │  Media   │  │
│   │ Tables   │ │ Tables   │ │ Tables   │ │ Tables   │  │
│   └──────────┘ └──────────┘ └──────────┘ └──────────┘  │
├─────────────────────────────────────────────────────────┤
│                     CORE DATABASE                        │
│   (users, roles, permissions, notifications,             │
│    activity_logs, audit_logs)                             │
└─────────────────────────────────────────────────────────┘
```

### Hierarki Database

| Layer | Fungsi | Contoh Tabel |
| :--- | :--- | :--- |
| **Core Database** | Tabel fundamental yang digunakan oleh seluruh sistem. Tidak boleh dihapus atau dimodifikasi secara destruktif. | `users`, `roles`, `permissions`, `notifications` |
| **Engine Database** | Tabel pendukung engine platform. Menyimpan konfigurasi dan data operasional engine. | `menus`, `menu_items`, `settings`, `setting_groups`, `modules`, `plugins`, `themes`, `widgets`, `media` |
| **Module Database** | Tabel domain bisnis. Terisolasi per modul dan dapat ditambahkan/dihapus bersama modul. | `books`, `members`, `loans`, `fines`, `pages`, `posts` |
| **Application Database** | Tabel infrastruktur Laravel. Mendukung session, cache, queue, dan job. | `sessions`, `cache`, `jobs`, `failed_jobs` |

### Aturan Dependency Antar Layer

```text
Application Database  →  dapat mereferensi  →  Core Database
Module Database       →  dapat mereferensi  →  Core Database, Engine Database
Engine Database       →  dapat mereferensi  →  Core Database
Core Database         →  TIDAK mereferensi  →  layer di atasnya
```

> **Prinsip:** Layer bawah tidak boleh bergantung pada layer atas. Foreign key selalu mengarah ke bawah (menuju Core).

---

## 4. Database Convention

### 4.1 Primary Key

| Aspek | Standar |
| :--- | :--- |
| **Nama kolom** | `id` |
| **Tipe data** | `BIGINT UNSIGNED AUTO_INCREMENT` |
| **Laravel** | `$table->id()` |
| **Catatan** | Selalu sebagai kolom pertama di tabel. |

### 4.2 UUID (Future)

| Aspek | Standar |
| :--- | :--- |
| **Nama kolom** | `uuid` |
| **Tipe data** | `CHAR(36)` |
| **Laravel** | `$table->uuid('uuid')->unique()` |
| **Penggunaan** | Identifier publik untuk API dan URL. Tidak menggantikan `id` sebagai PK. |
| **Catatan** | Ditambahkan di fase lanjutan untuk entitas yang terekspos ke publik (buku, anggota, pinjaman). |

### 4.3 Foreign Key

| Aspek | Standar |
| :--- | :--- |
| **Nama kolom** | `{table_singular}_id` (contoh: `user_id`, `book_id`) |
| **Tipe data** | `BIGINT UNSIGNED` (sesuai PK referensi) |
| **Laravel** | `$table->foreignId('user_id')->constrained()->onDelete('cascade')` |
| **Nullable** | Gunakan `->nullable()` jika relasi opsional. |
| **Catatan** | Selalu deklarasikan constraint eksplisit. Jangan biarkan FK tanpa constraint. |

### 4.4 Timestamp

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `created_at` | `TIMESTAMP` | Waktu pembuatan record. Otomatis oleh Laravel. |
| `updated_at` | `TIMESTAMP` | Waktu terakhir diperbarui. Otomatis oleh Laravel. |
| **Laravel** | — | `$table->timestamps()` |

### 4.5 Soft Delete

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `deleted_at` | `TIMESTAMP NULL` | `NULL` = aktif, terisi = dihapus. |
| **Laravel** | — | `$table->softDeletes()` |
| **Catatan** | Wajib untuk tabel data bisnis. Opsional untuk tabel pivot dan infrastruktur. |

### 4.6 Index

| Jenis | Kapan Digunakan | Laravel |
| :--- | :--- | :--- |
| **Primary** | Otomatis pada `id` | `$table->id()` |
| **Unique** | Kolom yang harus unik (`email`, `isbn`, `slug`) | `$table->unique('email')` |
| **Index** | Kolom yang sering di-`WHERE`, `JOIN`, `ORDER BY` | `$table->index('status')` |
| **Composite Index** | Kombinasi kolom yang sering di-query bersama | `$table->index(['status', 'due_date'])` |
| **Full-text** | Kolom pencarian teks (`title`, `description`) | `$table->fullText('title')` |

### 4.7 Composite Index Strategy

Composite index digunakan pada skenario berikut:

| Tabel | Composite Index | Alasan |
| :--- | :--- | :--- |
| `loans` | `['member_id', 'status']` | Query peminjaman aktif per anggota |
| `loans` | `['status', 'due_date']` | Query peminjaman jatuh tempo |
| `book_copies` | `['book_id', 'status']` | Cek ketersediaan eksemplar per buku |
| `activity_logs` | `['user_id', 'created_at']` | Riwayat aktivitas per pengguna |
| `model_has_roles` | `['model_type', 'model_id']` | Polymorphic role lookup |
| `model_has_permissions` | `['model_type', 'model_id']` | Polymorphic permission lookup |

---

## 5. Core Tables

Tabel core adalah fondasi sistem. **Tidak boleh dihapus** dan perubahan harus melalui migration yang disetujui.

### 5.1 Authentication & Authorization

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `users` | Data pengguna sistem (admin, pustakawan, guru, siswa) | `id` bigint | ✅ |
| `roles` | Definisi peran pengguna (dari Permission Engine) | `id` bigint | ✅ |
| `permissions` | Definisi hak akses atomik (dari Permission Engine) | `id` bigint | ✅ |
| `role_has_permissions` | Pivot: role ↔ permission | composite | ❌ |
| `model_has_roles` | Polymorphic pivot: model (user) ↔ role | composite | ❌ |
| `model_has_permissions` | Polymorphic pivot: model (user) ↔ permission langsung | composite | ❌ |

**Kolom utama `users`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `name` | `VARCHAR(255)` | Nama lengkap |
| `email` | `VARCHAR(255) UNIQUE` | Email (login credential) |
| `password` | `VARCHAR(255)` | Hashed password |
| `email_verified_at` | `TIMESTAMP NULL` | Waktu verifikasi email |
| `avatar` | `VARCHAR(255) NULL` | Path foto profil |
| `phone` | `VARCHAR(20) NULL` | Nomor telepon |
| `is_active` | `TINYINT(1) DEFAULT 1` | Status aktif/nonaktif |
| `last_login_at` | `TIMESTAMP NULL` | Waktu login terakhir |
| `last_login_ip` | `VARCHAR(45) NULL` | IP login terakhir |
| `remember_token` | `VARCHAR(100) NULL` | Token "remember me" |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit: dibuat oleh |
| `updated_by` | `BIGINT UNSIGNED NULL` | Audit: diperbarui oleh |
| `deleted_by` | `BIGINT UNSIGNED NULL` | Audit: dihapus oleh |

**Kolom utama `roles`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `name` | `VARCHAR(50) UNIQUE` | Slug role (`admin`, `librarian`, `teacher`, `student`) |
| `label` | `VARCHAR(100)` | Label tampil (Bahasa Indonesia) |
| `guard_name` | `VARCHAR(50) DEFAULT 'web'` | Guard Laravel |
| `description` | `TEXT NULL` | Deskripsi peran |
| `is_system` | `TINYINT(1) DEFAULT 0` | Role bawaan sistem (tidak boleh dihapus) |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |

**Kolom utama `permissions`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `name` | `VARCHAR(100) UNIQUE` | Slug permission (`books.view`, `loans.create`) |
| `label` | `VARCHAR(150)` | Label tampil (Bahasa Indonesia) |
| `guard_name` | `VARCHAR(50) DEFAULT 'web'` | Guard Laravel |
| `group` | `VARCHAR(50)` | Grup permission untuk pengelompokan UI |
| `module` | `VARCHAR(50) NULL` | Modul asal permission |
| `description` | `TEXT NULL` | Deskripsi permission |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |

### 5.2 Menu Engine

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `menus` | Grup menu (sidebar, topbar, footer) | `id` bigint | ✅ |
| `menu_items` | Item menu individual dalam hierarki | `id` bigint | ✅ |

**Kolom utama `menu_items`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `menu_id` | `BIGINT UNSIGNED` | FK → `menus` |
| `parent_id` | `BIGINT UNSIGNED NULL` | FK self-referencing untuk hierarki |
| `title` | `VARCHAR(100)` | Label menu (Bahasa Indonesia) |
| `icon` | `VARCHAR(50) NULL` | Kelas ikon (Bootstrap Icons / custom) |
| `route` | `VARCHAR(255) NULL` | Nama route Laravel |
| `url` | `VARCHAR(255) NULL` | URL langsung (jika bukan route) |
| `permission` | `VARCHAR(100) NULL` | Permission yang diperlukan |
| `module` | `VARCHAR(50) NULL` | Modul asal menu |
| `order` | `INT UNSIGNED DEFAULT 0` | Urutan tampil |
| `is_active` | `TINYINT(1) DEFAULT 1` | Status aktif |
| `badge` | `VARCHAR(50) NULL` | Teks/angka badge |
| `badge_color` | `VARCHAR(20) NULL` | Warna badge |
| `target` | `VARCHAR(10) DEFAULT '_self'` | Target link |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |

### 5.3 Module, Plugin, Theme, Widget Engine

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `modules` | Daftar modul terpasang | `id` bigint | ✅ |
| `plugins` | Daftar plugin terpasang | `id` bigint | ✅ |
| `themes` | Daftar tema terpasang | `id` bigint | ✅ |
| `widgets` | Daftar widget terdaftar | `id` bigint | ✅ |

**Kolom umum untuk `modules`, `plugins`, `themes`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `name` | `VARCHAR(100) UNIQUE` | Slug unik |
| `label` | `VARCHAR(150)` | Nama tampil |
| `description` | `TEXT NULL` | Deskripsi |
| `version` | `VARCHAR(20)` | Versi semver |
| `author` | `VARCHAR(100) NULL` | Pembuat |
| `is_active` | `TINYINT(1) DEFAULT 0` | Status aktif |
| `is_system` | `TINYINT(1) DEFAULT 0` | Bawaan sistem (tidak boleh dihapus) |
| `settings` | `JSON NULL` | Konfigurasi JSON (jika diperlukan) |
| `installed_at` | `TIMESTAMP NULL` | Waktu instalasi |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |

### 5.4 Setting Engine

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `settings` | Konfigurasi key-value sistem | `id` bigint | ❌ |
| `setting_groups` | Grup pengaturan untuk UI | `id` bigint | ❌ |

**Kolom utama `settings`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `group_id` | `BIGINT UNSIGNED NULL` | FK → `setting_groups` |
| `key` | `VARCHAR(100) UNIQUE` | Kunci pengaturan (`app.name`, `library.loan_days`) |
| `value` | `TEXT NULL` | Nilai pengaturan |
| `type` | `VARCHAR(30) DEFAULT 'string'` | Tipe data (`string`, `integer`, `boolean`, `json`, `file`) |
| `label` | `VARCHAR(150) NULL` | Label UI (Bahasa Indonesia) |
| `description` | `TEXT NULL` | Keterangan pengaturan |
| `is_public` | `TINYINT(1) DEFAULT 0` | Apakah terekspos ke frontend |
| `is_system` | `TINYINT(1) DEFAULT 0` | Pengaturan sistem (tidak boleh dihapus) |
| `order` | `INT UNSIGNED DEFAULT 0` | Urutan tampil dalam grup |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |

### 5.5 Media Engine

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `media` | File media yang diunggah | `id` bigint | ✅ |

**Kolom utama `media`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `model_type` | `VARCHAR(255) NULL` | Polymorphic: tipe model pemilik |
| `model_id` | `BIGINT UNSIGNED NULL` | Polymorphic: ID model pemilik |
| `collection` | `VARCHAR(100) DEFAULT 'default'` | Koleksi media (`cover`, `document`, `avatar`) |
| `file_name` | `VARCHAR(255)` | Nama file asli |
| `file_path` | `VARCHAR(500)` | Path penyimpanan |
| `mime_type` | `VARCHAR(100)` | Tipe MIME |
| `file_size` | `BIGINT UNSIGNED` | Ukuran file dalam bytes |
| `disk` | `VARCHAR(50) DEFAULT 'public'` | Disk Laravel (public, local, s3) |
| `alt_text` | `VARCHAR(255) NULL` | Teks alternatif |
| `title` | `VARCHAR(255) NULL` | Judul media |
| `order` | `INT UNSIGNED DEFAULT 0` | Urutan |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |

### 5.6 Notification, Activity Log, Audit Log

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `notifications` | Notifikasi pengguna (Laravel Notification) | `uuid` | ❌ |
| `activity_logs` | Log aktivitas pengguna umum | `id` bigint | ❌ |
| `audit_logs` | Log perubahan data sensitif | `id` bigint | ❌ |

**Kolom utama `activity_logs`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `user_id` | `BIGINT UNSIGNED NULL` | FK → `users` (NULL jika system) |
| `action` | `VARCHAR(50)` | Aksi (`login`, `logout`, `view`, `create`, `update`, `delete`) |
| `model_type` | `VARCHAR(255) NULL` | Polymorphic: tipe model target |
| `model_id` | `BIGINT UNSIGNED NULL` | Polymorphic: ID model target |
| `description` | `TEXT NULL` | Deskripsi aktivitas |
| `ip_address` | `VARCHAR(45) NULL` | IP pengguna |
| `user_agent` | `TEXT NULL` | Browser/agent |
| `properties` | `JSON NULL` | Data tambahan (old values, new values) |
| `created_at` | `TIMESTAMP` | Waktu aktivitas |

**Kolom utama `audit_logs`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `user_id` | `BIGINT UNSIGNED NULL` | FK → `users` |
| `event` | `VARCHAR(50)` | Tipe event (`created`, `updated`, `deleted`, `restored`) |
| `auditable_type` | `VARCHAR(255)` | Polymorphic: tipe model yang diaudit |
| `auditable_id` | `BIGINT UNSIGNED` | Polymorphic: ID model yang diaudit |
| `old_values` | `JSON NULL` | Nilai sebelum perubahan |
| `new_values` | `JSON NULL` | Nilai setelah perubahan |
| `ip_address` | `VARCHAR(45) NULL` | IP pengguna |
| `url` | `VARCHAR(500) NULL` | URL saat perubahan |
| `created_at` | `TIMESTAMP` | Waktu audit |

### 5.7 Job & Queue

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `jobs` | Antrian pekerjaan Laravel | `id` bigint | ❌ |
| `failed_jobs` | Pekerjaan yang gagal | `id` bigint | ❌ |
| `cache` | Cache store (database driver) | `key` varchar | ❌ |
| `sessions` | Session store (database driver) | `id` varchar | ❌ |

> Tabel `jobs`, `failed_jobs`, `cache`, dan `sessions` menggunakan struktur standar Laravel. Tidak memerlukan modifikasi.

---

## 6. Library Tables

Tabel modul perpustakaan — domain bisnis utama CosmicLib.

### 6.1 Katalog Buku

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `books` | Data bibliografi buku | `id` bigint | ✅ |
| `book_categories` | Kategori/klasifikasi buku (DDC) | `id` bigint | ✅ |
| `book_publishers` | Data penerbit | `id` bigint | ✅ |
| `book_authors` | Data pengarang | `id` bigint | ✅ |
| `book_locations` | Lokasi rak/ruang penyimpanan | `id` bigint | ✅ |
| `book_copies` | Eksemplar fisik buku individual | `id` bigint | ✅ |
| `book_stock` | Ringkasan stok per buku (denormalisasi terkontrol) | `id` bigint | ❌ |

**Kolom utama `books`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `title` | `VARCHAR(255)` | Judul buku |
| `isbn` | `VARCHAR(20) UNIQUE NULL` | ISBN |
| `category_id` | `BIGINT UNSIGNED NULL` | FK → `book_categories` |
| `publisher_id` | `BIGINT UNSIGNED NULL` | FK → `book_publishers` |
| `location_id` | `BIGINT UNSIGNED NULL` | FK → `book_locations` |
| `publish_year` | `YEAR NULL` | Tahun terbit |
| `edition` | `VARCHAR(50) NULL` | Edisi |
| `language` | `VARCHAR(30) DEFAULT 'id'` | Bahasa |
| `page_count` | `INT UNSIGNED NULL` | Jumlah halaman |
| `ddc_classification` | `VARCHAR(20) NULL` | Klasifikasi DDC |
| `description` | `TEXT NULL` | Sinopsis |
| `cover_image` | `VARCHAR(255) NULL` | Path gambar sampul |
| `status` | `ENUM('active','inactive') DEFAULT 'active'` | Status buku |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |
| `updated_by` | `BIGINT UNSIGNED NULL` | Audit |
| `deleted_by` | `BIGINT UNSIGNED NULL` | Audit |

**Relasi Many-to-Many `books` ↔ `book_authors`:**

| Tabel Pivot | Kolom | Keterangan |
| :--- | :--- | :--- |
| `book_author` | `book_id` | FK → `books` |
| | `author_id` | FK → `book_authors` |
| | `role` | `VARCHAR(50) DEFAULT 'author'` (author, editor, translator) |

**Kolom utama `book_copies`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `book_id` | `BIGINT UNSIGNED` | FK → `books` |
| `barcode` | `VARCHAR(100) UNIQUE` | Barcode eksemplar |
| `accession_number` | `VARCHAR(50) UNIQUE NULL` | Nomor induk buku |
| `condition` | `ENUM('good','fair','damaged','lost') DEFAULT 'good'` | Kondisi fisik |
| `status` | `ENUM('available','borrowed','reserved','damaged','lost','withdrawn') DEFAULT 'available'` | Status ketersediaan |
| `acquisition_date` | `DATE NULL` | Tanggal perolehan |
| `acquisition_source` | `VARCHAR(100) NULL` | Sumber perolehan (pembelian, hibah, dll.) |
| `price` | `DECIMAL(12,2) NULL` | Harga perolehan |
| `notes` | `TEXT NULL` | Catatan |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |

### 6.2 Keanggotaan

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `members` | Data anggota perpustakaan | `id` bigint | ✅ |
| `member_types` | Tipe keanggotaan (siswa, guru, staf, umum) | `id` bigint | ✅ |

**Kolom utama `members`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `user_id` | `BIGINT UNSIGNED NULL` | FK → `users` (opsional, bisa tanpa akun) |
| `member_type_id` | `BIGINT UNSIGNED` | FK → `member_types` |
| `member_number` | `VARCHAR(50) UNIQUE` | Nomor anggota (NISN/NIP/custom) |
| `name` | `VARCHAR(255)` | Nama anggota |
| `email` | `VARCHAR(255) NULL` | Email |
| `phone` | `VARCHAR(20) NULL` | Telepon |
| `address` | `TEXT NULL` | Alamat |
| `photo` | `VARCHAR(255) NULL` | Path foto |
| `class_name` | `VARCHAR(50) NULL` | Kelas (untuk siswa) |
| `status` | `ENUM('active','suspended','inactive','graduated') DEFAULT 'active'` | Status keanggotaan |
| `registered_at` | `DATE` | Tanggal terdaftar |
| `expired_at` | `DATE NULL` | Tanggal kedaluwarsa |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |

### 6.3 Sirkulasi

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `loans` | Transaksi peminjaman | `id` bigint | ✅ |
| `loan_items` | Item per transaksi peminjaman | `id` bigint | ❌ |
| `returns` | Transaksi pengembalian | `id` bigint | ❌ |
| `fines` | Data denda | `id` bigint | ✅ |
| `reservations` | Reservasi/pemesanan buku | `id` bigint | ✅ |

**Kolom utama `loans`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `member_id` | `BIGINT UNSIGNED` | FK → `members` |
| `loan_number` | `VARCHAR(50) UNIQUE` | Nomor transaksi peminjaman |
| `loan_date` | `DATE` | Tanggal peminjaman |
| `due_date` | `DATE` | Tanggal jatuh tempo |
| `status` | `ENUM('active','returned','overdue','lost') DEFAULT 'active'` | Status peminjaman |
| `notes` | `TEXT NULL` | Catatan |
| `librarian_id` | `BIGINT UNSIGNED` | FK → `users` (pustakawan yang memproses) |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |

**Kolom utama `loan_items`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `loan_id` | `BIGINT UNSIGNED` | FK → `loans` |
| `book_copy_id` | `BIGINT UNSIGNED` | FK → `book_copies` |
| `return_date` | `DATE NULL` | Tanggal pengembalian aktual |
| `condition_on_return` | `ENUM('good','fair','damaged','lost') NULL` | Kondisi saat dikembalikan |
| `status` | `ENUM('borrowed','returned','overdue','lost') DEFAULT 'borrowed'` | Status item |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |

**Kolom utama `fines`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `loan_item_id` | `BIGINT UNSIGNED` | FK → `loan_items` |
| `member_id` | `BIGINT UNSIGNED` | FK → `members` |
| `fine_type` | `ENUM('overdue','damage','loss') DEFAULT 'overdue'` | Jenis denda |
| `fine_amount` | `DECIMAL(12,2)` | Jumlah denda |
| `paid_amount` | `DECIMAL(12,2) DEFAULT 0.00` | Jumlah yang sudah dibayar |
| `status` | `ENUM('unpaid','partially_paid','paid','waived') DEFAULT 'unpaid'` | Status pembayaran |
| `payment_date` | `DATE NULL` | Tanggal pelunasan |
| `notes` | `TEXT NULL` | Catatan |
| `created_at` | `TIMESTAMP` | — |
| `updated_at` | `TIMESTAMP` | — |
| `deleted_at` | `TIMESTAMP NULL` | Soft Delete |
| `created_by` | `BIGINT UNSIGNED NULL` | Audit |

### 6.4 Pengunjung & Digital

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `visitors` | Log kunjungan perpustakaan | `id` bigint | ❌ |
| `digital_books` | Data buku digital/e-book | `id` bigint | ✅ |
| `book_files` | File lampiran buku digital | `id` bigint | ✅ |
| `reading_logs` | Log pembacaan buku digital | `id` bigint | ❌ |

**Kolom utama `visitors`:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | Primary Key |
| `member_id` | `BIGINT UNSIGNED NULL` | FK → `members` (NULL jika tamu) |
| `visitor_name` | `VARCHAR(255) NULL` | Nama (jika bukan anggota) |
| `purpose` | `VARCHAR(100) NULL` | Tujuan kunjungan |
| `visit_date` | `DATE` | Tanggal kunjungan |
| `check_in` | `TIME` | Waktu masuk |
| `check_out` | `TIME NULL` | Waktu keluar |
| `created_at` | `TIMESTAMP` | — |

---

## 7. School Tables

Tabel untuk integrasi data sekolah. Modul opsional — hanya diaktifkan jika diperlukan.

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `school_profiles` | Profil dan identitas sekolah | `id` bigint | ❌ |
| `school_users` | Mapping user ke entitas sekolah (guru/siswa) | `id` bigint | ✅ |
| `academic_years` | Tahun ajaran | `id` bigint | ✅ |
| `departments` | Jurusan/program keahlian | `id` bigint | ✅ |
| `classes` | Kelas/rombongan belajar | `id` bigint | ✅ |
| `teachers` | Data guru | `id` bigint | ✅ |
| `students` | Data siswa | `id` bigint | ✅ |

**Relasi utama:**

- `teachers.user_id` → `users.id` (One-to-One)
- `students.user_id` → `users.id` (One-to-One)
- `students.class_id` → `classes.id` (Many-to-One)
- `classes.department_id` → `departments.id` (Many-to-One)
- `classes.academic_year_id` → `academic_years.id` (Many-to-One)

---

## 8. CMS Tables

Tabel untuk fitur Content Management. Modul opsional.

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `pages` | Halaman statis (tentang, kontak, dll.) | `id` bigint | ✅ |
| `page_categories` | Kategori halaman | `id` bigint | ✅ |
| `posts` | Artikel/berita | `id` bigint | ✅ |
| `post_categories` | Kategori artikel | `id` bigint | ✅ |
| `banners` | Banner promosi | `id` bigint | ✅ |
| `sliders` | Slider halaman depan | `id` bigint | ✅ |
| `announcements` | Pengumuman | `id` bigint | ✅ |

**Kolom umum CMS tables:**

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `title` | `VARCHAR(255)` | Judul konten |
| `slug` | `VARCHAR(255) UNIQUE` | URL-friendly identifier |
| `content` | `LONGTEXT NULL` | Isi konten (HTML/Markdown) |
| `excerpt` | `TEXT NULL` | Ringkasan |
| `featured_image` | `VARCHAR(255) NULL` | Gambar utama |
| `status` | `ENUM('draft','published','archived') DEFAULT 'draft'` | Status publikasi |
| `published_at` | `TIMESTAMP NULL` | Waktu terbit |
| `author_id` | `BIGINT UNSIGNED` | FK → `users` |
| `order` | `INT UNSIGNED DEFAULT 0` | Urutan tampil |
| `meta_title` | `VARCHAR(255) NULL` | SEO title |
| `meta_description` | `TEXT NULL` | SEO description |

> Tabel `media` (Media Engine, Bagian 5.5) digunakan bersama oleh CMS dan modul lain melalui polymorphic relationship.

---

## 9. System Tables

Tabel infrastruktur dan konfigurasi sistem.

| Tabel | Deskripsi | PK | Soft Delete |
| :--- | :--- | :--- | :--- |
| `backups` | Log dan metadata backup database | `id` bigint | ❌ |
| `system_updates` | Riwayat pembaruan sistem | `id` bigint | ❌ |
| `licenses` | Informasi lisensi dan aktivasi | `id` bigint | ❌ |
| `smtp_settings` | Konfigurasi SMTP (terenkripsi) | `id` bigint | ❌ |
| `whatsapp_settings` | Konfigurasi gateway WhatsApp | `id` bigint | ❌ |
| `theme_settings` | Konfigurasi tema aktif | `id` bigint | ❌ |

**Catatan keamanan:** Kolom sensitif pada `smtp_settings` dan `whatsapp_settings` (password, API key, token) **wajib** dienkripsi menggunakan Laravel `Crypt` facade.

---

## 10. Module Tables

Setiap modul custom dapat memiliki tabel sendiri. Aturan untuk tabel modul:

| Aturan | Penjelasan |
| :--- | :--- |
| **Prefix opsional** | Modul custom **boleh** menggunakan prefix `{module_name}_` untuk menghindari konflik nama. Contoh: `inventory_items`, `inventory_categories`. |
| **Mandatory columns** | Semua tabel modul wajib mengikuti [Standard Column](#13-standard-column). |
| **FK ke Core** | Modul boleh mereferensi tabel Core (misalnya `users.id`) tetapi **tidak boleh** mereferensi tabel modul lain secara langsung. |
| **Isolasi** | Menghapus modul harus bisa dilakukan tanpa merusak tabel core atau modul lain. |
| **Migration terpisah** | Setiap modul memiliki migration tersendiri di `modules/<ModuleName>/Database/Migrations/`. |

### Contoh Struktur Tabel Modul Custom

```text
modules/Inventory/
  └── Database/
      └── Migrations/
          ├── 2025_01_01_000001_create_inventory_items_table.php
          ├── 2025_01_01_000002_create_inventory_categories_table.php
          └── 2025_01_01_000003_create_inventory_transactions_table.php
```

---

## 11. Relationship

### 11.1 One-to-One

| Relasi | Penjelasan |
| :--- | :--- |
| `users` ↔ `teachers` | Setiap guru memiliki satu akun user |
| `users` ↔ `students` | Setiap siswa memiliki satu akun user |
| `users` ↔ `members` | Setiap user opsional memiliki satu keanggotaan perpustakaan |

### 11.2 One-to-Many

| Parent | Child | Penjelasan |
| :--- | :--- | :--- |
| `books` | `book_copies` | Satu buku memiliki banyak eksemplar |
| `book_categories` | `books` | Satu kategori memiliki banyak buku |
| `book_publishers` | `books` | Satu penerbit memiliki banyak buku |
| `book_locations` | `book_copies` | Satu lokasi memiliki banyak eksemplar |
| `members` | `loans` | Satu anggota memiliki banyak peminjaman |
| `loans` | `loan_items` | Satu transaksi memiliki banyak item pinjaman |
| `loan_items` | `fines` | Satu item pinjaman dapat memiliki banyak denda |
| `member_types` | `members` | Satu tipe keanggotaan memiliki banyak anggota |
| `menus` | `menu_items` | Satu menu memiliki banyak item |
| `menu_items` | `menu_items` | Self-referencing: parent ↔ children (hierarki) |
| `setting_groups` | `settings` | Satu grup memiliki banyak pengaturan |
| `users` | `activity_logs` | Satu user memiliki banyak log aktivitas |
| `academic_years` | `classes` | Satu tahun ajaran memiliki banyak kelas |
| `departments` | `classes` | Satu jurusan memiliki banyak kelas |
| `classes` | `students` | Satu kelas memiliki banyak siswa |
| `users` | `posts` | Satu user (author) memiliki banyak artikel |

### 11.3 Many-to-Many

| Tabel A | Tabel B | Pivot Table | Penjelasan |
| :--- | :--- | :--- | :--- |
| `roles` | `permissions` | `role_has_permissions` | Role memiliki banyak permission, dan sebaliknya |
| `users` | `roles` | `model_has_roles` | User memiliki banyak role (polymorphic) |
| `users` | `permissions` | `model_has_permissions` | User memiliki permission langsung (polymorphic) |
| `books` | `book_authors` | `book_author` | Buku memiliki banyak pengarang, dan sebaliknya |

### 11.4 Morph Relationship (Polymorphic)

| Morph | Morph Columns | Digunakan Pada |
| :--- | :--- | :--- |
| `media` | `model_type`, `model_id` | `books`, `members`, `posts`, `pages`, `users` — semua model yang memiliki file media |
| `activity_logs` | `model_type`, `model_id` | Semua model yang perlu di-log aktivitasnya |
| `audit_logs` | `auditable_type`, `auditable_id` | Semua model yang perlu jejak perubahan |
| `model_has_roles` | `model_type`, `model_id` | `users` dan model lain yang memerlukan role |
| `model_has_permissions` | `model_type`, `model_id` | `users` dan model lain yang memerlukan permission langsung |
| `notifications` | `notifiable_type`, `notifiable_id` | Laravel notification polymorphic |

---

## 12. Naming Convention

### 12.1 Tabel

| Aturan | Contoh |
| :--- | :--- |
| `snake_case` | ✅ `book_copies`, ❌ `BookCopies` |
| Plural | ✅ `books`, ❌ `book` |
| Pivot: alfabet | ✅ `book_author`, ❌ `author_book` |
| Deskriptif | ✅ `loan_items`, ❌ `li` |

### 12.2 Kolom

| Aturan | Contoh |
| :--- | :--- |
| `snake_case` | ✅ `first_name`, ❌ `firstName` |
| Tidak disingkat | ✅ `description`, ❌ `desc` |
| Boolean: prefix `is_` / `has_` | ✅ `is_active`, ❌ `active` |
| Tanggal: suffix `_at` / `_date` | ✅ `published_at`, `loan_date` |

### 12.3 Foreign Key

| Aturan | Contoh |
| :--- | :--- |
| `{table_singular}_id` | ✅ `user_id`, `book_id`, `loan_id` |
| Khusus context | ✅ `librarian_id` (FK → `users`), `author_id` (FK → `users`) |
| Morphable | ✅ `model_type` + `model_id`, `auditable_type` + `auditable_id` |

### 12.4 Index

| Aturan | Contoh |
| :--- | :--- |
| Auto-generated | `books_isbn_unique`, `loans_member_id_status_index` |
| Laravel default | Framework memberi nama otomatis berdasarkan konvensi |

---

## 13. Standard Column

Setiap tabel data bisnis **minimal** memiliki kolom berikut:

```text
┌──────────────────────────────────────────────────────────────┐
│  STANDARD COLUMNS — Wajib pada setiap tabel data bisnis     │
├──────────────────────────────────────────────────────────────┤
│  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY   │
│  uuid           CHAR(36) UNIQUE  (future, opsional)         │
│  created_at     TIMESTAMP                                    │
│  updated_at     TIMESTAMP                                    │
│  deleted_at     TIMESTAMP NULL   (soft delete)               │
│  created_by     BIGINT UNSIGNED NULL  (FK → users)           │
│  updated_by     BIGINT UNSIGNED NULL  (FK → users)           │
│  deleted_by     BIGINT UNSIGNED NULL  (FK → users)           │
└──────────────────────────────────────────────────────────────┘
```

| Kolom | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED AI` | ✅ | Primary Key. Selalu kolom pertama. |
| `uuid` | `CHAR(36) UNIQUE` | ❌ (Future) | Identifier publik. Ditambahkan di fase API. |
| `created_at` | `TIMESTAMP` | ✅ | Laravel `$table->timestamps()` |
| `updated_at` | `TIMESTAMP` | ✅ | Laravel `$table->timestamps()` |
| `deleted_at` | `TIMESTAMP NULL` | ✅* | Laravel `$table->softDeletes()`. *Opsional untuk pivot/log. |
| `created_by` | `BIGINT UNSIGNED NULL` | ✅* | Audit trail. *Opsional untuk tabel infrastruktur. |
| `updated_by` | `BIGINT UNSIGNED NULL` | ✅* | Audit trail. |
| `deleted_by` | `BIGINT UNSIGNED NULL` | ✅* | Audit trail. |

### Pengecualian

Tabel berikut **tidak** memerlukan semua standard column:

| Kategori | Contoh Tabel | Alasan |
| :--- | :--- | :--- |
| Pivot table | `role_has_permissions`, `book_author` | Hanya berisi FK, tidak perlu audit/soft delete |
| Log table | `activity_logs`, `audit_logs`, `visitors` | Append-only, tidak perlu soft delete atau audit |
| Laravel infrastructure | `jobs`, `failed_jobs`, `cache`, `sessions` | Menggunakan struktur standar Laravel |

---

## 14. Migration Standard

### 14.1 Aturan Migration

| Aturan | Penjelasan |
| :--- | :--- |
| **Satu file per tabel** | Setiap tabel memiliki satu file migration `create`. |
| **Penamaan** | `YYYY_MM_DD_HHMMSS_create_{table_name}_table.php` |
| **Perubahan** | Jangan edit migration yang sudah di-commit. Buat migration baru: `add_column_to_{table}_table.php` |
| **Foreign Key** | Wajib dideklarasikan eksplisit dengan `->constrained()` |
| **Index** | Wajib dideklarasikan pada kolom yang sering di-query |
| **Comment** | Tambahkan `->comment()` pada kolom yang perlu penjelasan |
| **Rollback** | Setiap migration wajib memiliki method `down()` yang fungsional |
| **Urutan** | Tabel referensi dibuat lebih dulu (categories sebelum books) |

### 14.2 Contoh Standar Migration

```text
Migration WAJIB mengandung:

1. Schema::create() dengan kolom lengkap
2. Foreign key constraints eksplisit
3. Index pada kolom yang sering di-query
4. Comment pada kolom yang perlu penjelasan
5. Soft delete (jika tabel data bisnis)
6. Audit columns (created_by, updated_by, deleted_by)
7. Method down() yang drop table/column
```

### 14.3 Urutan Migration

```text
01. users
02. roles
03. permissions
04. role_has_permissions
05. model_has_roles
06. model_has_permissions
07. menus
08. menu_items
09. modules
10. plugins
11. themes
12. widgets
13. setting_groups
14. settings
15. media
16. notifications
17. activity_logs
18. audit_logs
19. jobs, failed_jobs, cache, sessions
20. book_categories
21. book_publishers
22. book_authors
23. book_locations
24. books
25. book_author (pivot)
26. book_copies
27. book_stock
28. member_types
29. members
30. loans
31. loan_items
32. returns
33. fines
34. reservations
35. visitors
36. digital_books
37. book_files
38. reading_logs
39. school_profiles
40. academic_years
41. departments
42. classes
43. teachers
44. students
45. school_users
46. page_categories
47. pages
48. post_categories
49. posts
50. banners
51. sliders
52. announcements
53. backups
54. system_updates
55. licenses
56. smtp_settings
57. whatsapp_settings
58. theme_settings
```

---

## 15. Seeder Strategy

### 15.1 Urutan Seeder

Seeder dijalankan dalam urutan dependency:

| Urutan | Seeder | Fungsi |
| :--- | :--- | :--- |
| 1 | **CoreSeeder** | Memanggil semua seeder core dalam urutan yang benar |
| 2 | **PermissionSeeder** | Menanam seluruh permission default sistem |
| 3 | **RoleSeeder** | Menanam role default (admin, librarian, teacher, student, guest) dan assign permission |
| 4 | **UserSeeder** | Menanam user admin default |
| 5 | **MenuSeeder** | Menanam menu default (sidebar, topbar) |
| 6 | **SettingGroupSeeder** | Menanam grup pengaturan |
| 7 | **SettingSeeder** | Menanam pengaturan default (app name, timezone, library config) |
| 8 | **ThemeSeeder** | Menanam tema default |
| 9 | **ModuleSeeder** | Menanam modul bawaan (Library, School, CMS) |
| 10 | **LibrarySeeder** | Menanam data awal perpustakaan (kategori DDC, tipe anggota, lokasi rak) |

### 15.2 Aturan Seeder

| Aturan | Penjelasan |
| :--- | :--- |
| **Idempotent** | Seeder harus bisa dijalankan berulang tanpa duplikasi (`firstOrCreate`, `updateOrCreate`) |
| **Environment-aware** | Data dummy hanya ditanam di environment `local` / `testing` |
| **Documented** | Setiap seeder menjelaskan data apa yang ditanam |
| **Ordered** | Dependency FK harus dipenuhi (role sebelum user, permission sebelum role) |

---

## 16. Database Security

### 16.1 Foreign Key Constraints

| Aksi | Strategi | Penerapan |
| :--- | :--- | :--- |
| **CASCADE** | Hapus child saat parent dihapus | Pivot tables, data yang tidak bisa exist tanpa parent |
| **RESTRICT** | Cegah hapus parent jika masih ada child | Data bisnis kritis (jangan hapus member jika masih ada loan aktif) |
| **SET NULL** | Set FK ke NULL saat parent dihapus | Relasi opsional (`members.user_id`) |
| **NO ACTION** | Default MySQL, sama dengan RESTRICT | Jarang digunakan eksplisit |

### 16.2 Strategi per Tabel

| Parent | Child | On Delete | Alasan |
| :--- | :--- | :--- | :--- |
| `users` | `model_has_roles` | CASCADE | Role assignment dihapus bersama user |
| `users` | `model_has_permissions` | CASCADE | Permission assignment dihapus bersama user |
| `roles` | `role_has_permissions` | CASCADE | Permission assignment dihapus bersama role |
| `users` | `members` | SET NULL | Anggota tetap ada walau akun user dihapus |
| `books` | `book_copies` | RESTRICT | Jangan hapus buku jika masih ada eksemplar |
| `members` | `loans` | RESTRICT | Jangan hapus anggota jika masih ada pinjaman |
| `loans` | `loan_items` | CASCADE | Item pinjaman dihapus bersama transaksi |
| `loan_items` | `fines` | CASCADE | Denda dihapus bersama item pinjaman |
| `menus` | `menu_items` | CASCADE | Item menu dihapus bersama menu |

### 16.3 Validation

| Layer | Validasi |
| :--- | :--- |
| **Form Request** | Validasi input sebelum masuk Service Layer |
| **Model** | Cast tipe data, mutator untuk sanitasi |
| **Database** | Constraint (UNIQUE, NOT NULL, FK, CHECK) sebagai pertahanan terakhir |

### 16.4 Encryption

Data sensitif yang **wajib** dienkripsi:

| Tabel | Kolom | Metode |
| :--- | :--- | :--- |
| `smtp_settings` | `password` | Laravel `Crypt::encryptString()` |
| `whatsapp_settings` | `api_key`, `token` | Laravel `Crypt::encryptString()` |
| `licenses` | `license_key` | Laravel `Crypt::encryptString()` |

### 16.5 Sensitive Data

| Data | Perlindungan |
| :--- | :--- |
| Password user | Hashed (`bcrypt`/`argon2`), TIDAK PERNAH disimpan plaintext |
| Token API | Hashed di database, ditampilkan sekali saat pembuatan |
| Email/telepon | Ditampilkan termasking di log (`j***@email.com`) |
| Data anggota | Hanya diakses oleh role dengan permission yang sesuai |

---

## 17. Performance

### 17.1 Index Strategy

| Kategori | Kolom yang Di-index | Alasan |
| :--- | :--- | :--- |
| **Lookup** | `email`, `isbn`, `barcode`, `member_number`, `slug` | Pencarian unik / WHERE clause |
| **Filter** | `status`, `is_active`, `type` | Filter UI / query bersyarat |
| **Sort** | `order`, `created_at`, `published_at` | Pengurutan data |
| **FK** | Semua foreign key | JOIN performance |
| **Search** | `title`, `name` (FULLTEXT) | Pencarian teks |

### 17.2 Composite Index

| Tabel | Index | Kolom |
| :--- | :--- | :--- |
| `loans` | `idx_loans_member_status` | `member_id`, `status` |
| `loans` | `idx_loans_status_due` | `status`, `due_date` |
| `loan_items` | `idx_loan_items_loan_status` | `loan_id`, `status` |
| `book_copies` | `idx_copies_book_status` | `book_id`, `status` |
| `activity_logs` | `idx_activity_user_date` | `user_id`, `created_at` |
| `audit_logs` | `idx_audit_model` | `auditable_type`, `auditable_id` |
| `posts` | `idx_posts_status_published` | `status`, `published_at` |
| `menu_items` | `idx_menu_items_menu_order` | `menu_id`, `order` |

### 17.3 Pagination

| Aturan | Penjelasan |
| :--- | :--- |
| Default per page | 15 – 25 record |
| Maximum per page | 100 record |
| Gunakan `simplePaginate()` | Untuk list tanpa total count (lebih cepat) |
| Gunakan `paginate()` | Untuk list yang perlu total count |
| Cursor pagination | Untuk dataset sangat besar (future) |

### 17.4 Loading Strategy

| Strategi | Kapan | Laravel |
| :--- | :--- | :--- |
| **Eager Loading** | Ketika relasi pasti digunakan | `Book::with('copies', 'category')->get()` |
| **Lazy Loading** | Ketika relasi belum tentu dipakai | `$book->copies` (akses saat dibutuhkan) |
| **Lazy Eager Loading** | Load relasi setelah collection diambil | `$books->load('copies')` |
| **Prevent Lazy Loading** | Di development, deteksi N+1 | `Model::preventLazyLoading(!app()->isProduction())` |

### 17.5 Query Optimization

| Praktik | Penjelasan |
| :--- | :--- |
| Hindari `SELECT *` | Gunakan `->select()` untuk kolom yang dibutuhkan |
| Chunking | `->chunk(500)` untuk operasi batch (ekspor, impor) |
| Query caching | Cache data yang jarang berubah (settings, menus, permissions) |
| Subquery | Gunakan subquery daripada multiple query terpisah |
| Raw query | **Hanya** jika Eloquent/Query Builder tidak memadai. **Wajib** parameter binding. |
| Database view | Opsional untuk reporting kompleks (future) |

---

## 18. Backup Strategy

### 18.1 Jadwal Backup

| Jenis | Frekuensi | Retensi | Keterangan |
| :--- | :--- | :--- | :--- |
| **Daily Backup** | Setiap hari pukul 02:00 WIB | 7 hari | Backup otomatis via scheduler |
| **Weekly Backup** | Setiap Minggu pukul 02:00 WIB | 4 minggu | Backup mingguan |
| **Manual Backup** | Kapan saja via admin panel | Tidak terbatas | Backup manual oleh admin |

### 18.2 Komponen Backup

| Komponen | Termasuk | Keterangan |
| :--- | :--- | :--- |
| **Database** | ✅ | Full dump MySQL |
| **Media files** | ✅ | Folder `storage/app/public` |
| **Configuration** | ✅ | File `.env` (terenkripsi) |
| **Uploaded files** | ✅ | File yang diunggah pengguna |

### 18.3 Restore

| Aspek | Keterangan |
| :--- | :--- |
| **Full restore** | Restore database + file dari backup tertentu |
| **Selective restore** | Restore hanya database atau hanya file |
| **Validation** | Verifikasi integritas backup sebelum restore |
| **Rollback** | Backup otomatis sebelum restore (safety net) |

### 18.4 Compression

| Format | Keterangan |
| :--- | :--- |
| **GZIP** | Default compression untuk backup SQL |
| **ZIP** | Compression untuk file media |
| **Naming** | `cosmiclib_backup_YYYYMMDD_HHMMSS.sql.gz` |

---

## 19. Future Database

### 19.1 Multi School

| Aspek | Rencana |
| :--- | :--- |
| **Strategi** | Kolom `school_id` pada tabel yang perlu isolasi per sekolah |
| **Global scope** | Laravel Global Scope untuk auto-filter berdasarkan sekolah aktif |
| **Migration** | Migration tambahan untuk menambah kolom `school_id` |
| **Catatan** | Desain saat ini sudah siap ditambahi `school_id` tanpa refactor besar |

### 19.2 Multi Tenant (Future)

| Aspek | Rencana |
| :--- | :--- |
| **Strategi** | Database per tenant ATAU shared database dengan tenant isolation |
| **Prioritas** | Fase lanjutan — setelah Multi School stabil |
| **Library** | Evaluasi `stancl/tenancy` atau implementasi custom |

### 19.3 Redis Cache

| Aspek | Rencana |
| :--- | :--- |
| **Penggunaan** | Cache permission, menu, setting untuk performa |
| **Fallback** | File cache sebagai default (shared hosting) |
| **Aktivasi** | Otomatis jika Redis tersedia di environment |

### 19.4 ElasticSearch

| Aspek | Rencana |
| :--- | :--- |
| **Penggunaan** | Full-text search katalog buku dan konten CMS |
| **Fallback** | MySQL FULLTEXT index sebagai default |
| **Library** | Laravel Scout dengan Elasticsearch driver |
| **Aktivasi** | Opsional — hanya untuk deployment dengan Elasticsearch tersedia |

---

## 20. AI Rules

### 20.1 Larangan

AI assistant (Claude, Codex, Cline, ChatGPT, AI Studio, GitHub Copilot) **TIDAK BOLEH:**

| No | Larangan | Alasan |
| :--- | :--- | :--- |
| 1 | Mengubah struktur database tanpa migration | Perubahan harus terversioning dan reversible |
| 2 | Menghapus kolom tanpa analisis dampak | Kolom mungkin direferensi di banyak tempat |
| 3 | Menggunakan raw SQL jika Eloquent mencukupi | Raw SQL rawan SQL injection dan sulit di-maintain |
| 4 | Melanggar normalisasi tanpa alasan terdokumentasi | Normalisasi melindungi integritas data |
| 5 | Menghapus foreign key constraint | FK adalah penjaga integritas referensial |
| 6 | Menghapus index tanpa analisis performa | Index penting untuk kecepatan query |
| 7 | Mengubah tipe data PK dari `BIGINT` | Dapat merusak seluruh referensi FK |
| 8 | Menulis migration tanpa method `down()` | Rollback harus selalu tersedia |
| 9 | Menanam data production di seeder | Seeder hanya untuk data referensi/default |
| 10 | Mengakses database langsung dari Controller | Gunakan Service → Repository → Model |

### 20.2 Kewajiban

AI assistant **WAJIB:**

| No | Kewajiban | Keterangan |
| :--- | :--- | :--- |
| 1 | Membaca dokumen ini sebelum membuat migration | Pastikan konvensi dipatuhi |
| 2 | Menggunakan standard column pada tabel baru | Lihat [Bagian 13](#13-standard-column) |
| 3 | Mendeklarasikan FK constraint eksplisit | Jangan biarkan FK tanpa constraint |
| 4 | Menambahkan index pada kolom yang sering di-query | Lihat [Bagian 17](#17-performance) |
| 5 | Menulis method `down()` yang fungsional | Rollback harus bekerja |
| 6 | Menggunakan Eloquent/Query Builder | Hindari raw SQL kecuali benar-benar diperlukan |
| 7 | Menjelaskan perubahan skema di CHANGELOG | Transparansi perubahan |

---

## 21. ERD Blueprint

### 21.1 Diagram Hubungan Utama

```text
┌─────────────────────────────────────────────────────────────────┐
│                        CORE LAYER                                │
│                                                                  │
│   ┌──────────┐    ┌──────────┐    ┌──────────────┐              │
│   │  users   │───▶│  roles   │───▶│ permissions  │              │
│   └──────────┘    └──────────┘    └──────────────┘              │
│        │               │                │                        │
│        │          role_has_         model_has_                    │
│        │          permissions      roles/permissions              │
│        │                                                         │
│        ├──────────────────────────────────────┐                  │
│        │                                      │                  │
│        ▼                                      ▼                  │
│   ┌──────────┐                          ┌──────────┐            │
│   │  menus   │                          │ settings │            │
│   └──────────┘                          └──────────┘            │
│        │                                      │                  │
│        ▼                                      ▼                  │
│   ┌──────────┐                          ┌──────────┐            │
│   │menu_items│                          │setting_  │            │
│   └──────────┘                          │groups    │            │
│                                         └──────────┘            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      LIBRARY MODULE                              │
│                                                                  │
│   ┌──────────────┐    ┌──────────┐    ┌──────────┐              │
│   │book_categories│   │  books   │───▶│book_copies│              │
│   └──────┬───────┘    └──────────┘    └──────────┘              │
│          │                 │               │                     │
│          └─────────────────┘               │                     │
│                                            │                     │
│   ┌──────────────┐    ┌──────────┐         │                     │
│   │book_publishers│──▶│  books   │         │                     │
│   └──────────────┘    └──────────┘         │                     │
│                                            │                     │
│   ┌──────────────┐    ┌──────────┐    ┌──────────┐              │
│   │book_authors  │◀──▶│  books   │    │loan_items│              │
│   └──────────────┘    └──────────┘    └──────────┘              │
│         (M:N via book_author)              │                     │
│                                            │                     │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │ members  │───▶│  loans   │───▶│loan_items│                  │
│   └──────────┘    └──────────┘    └──────────┘                  │
│        │                               │                         │
│        │                               ▼                         │
│        │                          ┌──────────┐                   │
│        │                          │  fines   │                   │
│        │                          └──────────┘                   │
│        │                                                         │
│        ▼                                                         │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │member_   │    │reserva-  │    │ visitors │                  │
│   │types     │    │tions     │    └──────────┘                  │
│   └──────────┘    └──────────┘                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      SCHOOL MODULE                               │
│                                                                  │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │school_   │    │academic_ │───▶│ classes  │                  │
│   │profiles  │    │years     │    └──────────┘                  │
│   └──────────┘    └──────────┘         │                        │
│                                        │                        │
│   ┌──────────┐    ┌──────────┐         ▼                        │
│   │departments│──▶│ classes  │    ┌──────────┐                  │
│   └──────────┘    └──────────┘    │ students │                  │
│                                   └──────────┘                  │
│                                        │                        │
│   ┌──────────┐                         ▼                        │
│   │ teachers │──────────────────▶ users (FK)                    │
│   └──────────┘                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                       CMS MODULE                                 │
│                                                                  │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │page_     │───▶│  pages   │    │  posts   │◀── post_        │
│   │categories│    └──────────┘    └──────────┘    categories    │
│   └──────────┘                         │                        │
│                                        ▼                        │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │ banners  │    │ sliders  │    │announce- │                  │
│   └──────────┘    └──────────┘    │ments     │                  │
│                                   └──────────┘                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    CROSS-CUTTING (Polymorphic)                    │
│                                                                  │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐                  │
│   │  media   │    │activity_ │    │ audit_   │                  │
│   │(morph)   │    │logs      │    │ logs     │                  │
│   └──────────┘    │(morph)   │    │(morph)   │                  │
│       │           └──────────┘    └──────────┘                  │
│       ▼                                                         │
│   Attaches to: books, members, users, posts, pages, etc.        │
└─────────────────────────────────────────────────────────────────┘
```

### 21.2 Alur Relasi Inti

```text
Users
  ↓ (has many)
Roles ←→ Permissions
  ↓ (controls)
Menus → Menu Items
  ↓ (navigates to)
Modules
  ↓ (provides)
Library Module
  ├── Books → Book Copies
  ├── Members → Loans → Loan Items → Fines
  ├── Reservations
  ├── Visitors
  └── Digital Books → Book Files → Reading Logs
```

---

## 22. Database Checklist

Sebelum menjalankan migration, pastikan seluruh item berikut terpenuhi:

### 22.1 Structural Checklist

- [ ] Primary Key (`id BIGINT UNSIGNED AI`) pada setiap tabel
- [ ] Foreign Key constraint eksplisit pada setiap relasi
- [ ] Index pada kolom yang sering di-WHERE, JOIN, ORDER BY
- [ ] Composite index pada kombinasi kolom yang sering di-query bersama
- [ ] UNIQUE constraint pada kolom yang harus unik (`email`, `isbn`, `barcode`, `slug`)
- [ ] Soft Delete (`deleted_at`) pada setiap tabel data bisnis
- [ ] Timestamp (`created_at`, `updated_at`) pada setiap tabel
- [ ] Audit columns (`created_by`, `updated_by`, `deleted_by`) pada tabel data bisnis

### 22.2 Migration Checklist

- [ ] Penamaan file mengikuti konvensi Laravel
- [ ] Method `down()` fungsional di setiap migration
- [ ] Urutan migration mematuhi dependency FK
- [ ] Comment pada kolom yang perlu penjelasan
- [ ] Tipe data sesuai standar (lihat [Bagian 4](#4-database-convention))

### 22.3 Seeder Checklist

- [ ] Seeder idempotent (menggunakan `firstOrCreate` / `updateOrCreate`)
- [ ] Data default lengkap (roles, permissions, menus, settings)
- [ ] Data dummy hanya di environment `local` / `testing`
- [ ] Factory tersedia untuk tabel yang perlu testing data

### 22.4 Security Checklist

- [ ] Data sensitif dienkripsi (password, API key, token)
- [ ] FK on-delete strategy didefinisikan (CASCADE / RESTRICT / SET NULL)
- [ ] Validasi input di Form Request sebelum masuk database
- [ ] Tidak ada raw SQL tanpa parameter binding

### 22.5 Performance Checklist

- [ ] Eager loading digunakan untuk relasi yang selalu dibutuhkan
- [ ] Pagination diterapkan pada semua list/query
- [ ] Query caching untuk data statis (settings, menus, permissions)
- [ ] `preventLazyLoading()` aktif di development

---

## Referensi

| Dokumen | Peran |
| :--- | :--- |
| [`03_ARCHITECTURE.md`](03_ARCHITECTURE.md) | Arsitektur sistem keseluruhan |
| [`24_DATABASE_SCHEMA.md`](24_DATABASE_SCHEMA.md) | Detail skema tabel (ERD lengkap, definisi kolom) |
| [`blueprint/database_schema.sql`](../blueprint/database_schema.sql) | Blueprint SQL referensi awal |
| [`22_SECURITY_GUIDELINE.md`](22_SECURITY_GUIDELINE.md) | Panduan keamanan |
| [`23_CODING_STANDARD.md`](23_CODING_STANDARD.md) | Standar kode |
| [`25_LIBRARY_MODULE.md`](25_LIBRARY_MODULE.md) | Spesifikasi modul perpustakaan |
| [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md) | Spesifikasi Core Engine |
| [`10_PERMISSION_ENGINE.md`](10_PERMISSION_ENGINE.md) | Spesifikasi Permission Engine |
| [`16_SETTING_ENGINE.md`](16_SETTING_ENGINE.md) | Spesifikasi Setting Engine |

---

## Catatan

- Dokumen ini adalah **kontrak desain database**. Semua migration, model, dan query **wajib** mengacu pada dokumen ini.
- Perubahan material pada desain database wajib dicatat di `CHANGELOG.md`.
- Semua desain harus divalidasi terhadap blueprint SQL (`blueprint/database_schema.sql`) sebelum implementasi.
- Prioritaskan kompatibilitas shared hosting — jangan gunakan fitur MySQL yang tidak tersedia di shared hosting standar.
- Dokumen ini akan diperbarui seiring evolusi arsitektur, namun prinsip dasar (normalisasi, modular, konvensi) tetap berlaku.

---

*CosmicLib Engine v1.0 — Sprint 2 · Prompt 009*