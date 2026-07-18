# 🌌 10 — Permission Engine

> **Spesifikasi resmi Permission Engine** — sistem manajemen hak akses CosmicLib yang mengontrol seluruh aspek keamanan aplikasi berbasis Role-Based Access Control (RBAC).
>
> Permission Engine memastikan bahwa setiap pengguna hanya dapat mengakses fitur, data, dan modul yang menjadi wewenangnya.
>
> Baca setelah [`07_CORE_ENGINE.md`](07_CORE_ENGINE.md), [`08_MODULE_ENGINE.md`](08_MODULE_ENGINE.md), dan [`09_THEME_ENGINE.md`](09_THEME_ENGINE.md).

| Atribut | Nilai |
| :--- | :--- |
| **Dokumen** | `docs/10_PERMISSION_ENGINE.md` |
| **Versi** | 1.0 |
| **Status** | `🟢 Final Blueprint` — spesifikasi resmi Permission Engine |
| **Engine** | CosmicLib Engine |
| **Framework** | Laravel 12 · PHP 8.3+ · MySQL 8+ |
| **Arsitektur** | RBAC (Role-Based Access Control) + Policy + Middleware |

---

## 🗂️ Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Filosofi](#2-filosofi)
3. [Authorization Model](#3-authorization-model)
4. [Permission Structure](#4-permission-structure)
5. [Role System](#5-role-system)
6. [Permission Categories](#6-permission-categories)
7. [Action Permission](#7-action-permission)
8. [Module Permission](#8-module-permission)
9. [Menu Permission](#9-menu-permission)
10. [Widget Permission](#10-widget-permission)
11. [Theme Permission](#11-theme-permission)
12. [API Permission](#12-api-permission)
13. [Policy](#13-policy)
14. [Middleware](#14-middleware)
15. [User Override](#15-user-override)
16. [Permission Registration](#16-permission-registration)
17. [Permission Cache](#17-permission-cache)
18. [Permission UI](#18-permission-ui)
19. [Security](#19-security)
20. [Future Features](#20-future-features)
21. [AI Rules](#21-ai-rules)
22. [Best Practice](#22-best-practice)
23. [Architecture Diagram](#23-architecture-diagram)
24. [Permission Matrix](#24-permission-matrix)
25. [Checklist](#25-checklist)

---

## 1. Pendahuluan

### 1.1 Apa Itu Permission Engine?

**Permission Engine** adalah subsistem CosmicLib yang bertanggung jawab mengelola seluruh aspek otorisasi dan hak akses aplikasi. Permission Engine menentukan **siapa** dapat mengakses **apa** dalam sistem — mulai dari modul, menu, halaman, aksi CRUD, widget dashboard, API endpoints, hingga pengaturan tema.

Permission Engine menggunakan **Role-Based Access Control (RBAC)** sebagai model otorisasi utama, dilengkapi dengan **Laravel Policy** untuk otorisasi berbasis model dan **Middleware** untuk proteksi rute.

### 1.2 Mengapa Seluruh Module Harus Menggunakan Permission Engine?

| Alasan | Penjelasan |
| :--- | :--- |
| **Konsistensi Keamanan** | Semua modul menggunakan satu sistem otorisasi yang sama — tidak ada celah keamanan |
| **Sentralisasi Manajemen** | Hak akses dikelola dari satu panel — admin tidak perlu mengatur permission di setiap modul |
| **Auditability** | Semua perubahan hak akses tercatat — siapa, kapan, apa yang berubah |
| **Granularitas** | Permission dapat diatur hingga level aksi (`view`, `create`, `edit`, `delete`) |
| **Modularitas** | Setiap modul mendefinisikan permission-nya sendiri tetapi tetap terintegrasi |
| **Dynamic** | Role dan permission dapat diubah kapan saja tanpa restart aplikasi |
| **Scalability** | Mendukung dari 1 pengguna hingga ribuan dengan struktur organisasi kompleks |

### 1.3 Prinsip Utama

```text
┌─────────────────────────────────────────────────┐
│           PRINSIP PERMISSION ENGINE               │
├─────────────────────────────────────────────────┤
│  1. RBAC       — Role-Based Access Control       │
│  2. Granular   — Permission hingga level aksi    │
│  3. Dynamic    — Role & permission runtime       │
│  4. Cached     — Performa tinggi dengan cache    │
│  5. Auditable  — Semua perubahan tercatat        │
│  6. Deny First — Default deny, allow explicit    │
│  7. Policy     — Otorisasi berbasis model        │
│  8. Middleware — Proteksi di level route         │
└─────────────────────────────────────────────────┘
```

---

## 2. Filosofi

### 2.1 Permission Bukan Hanya Role

Permission Engine CosmicLib menganut filosofi bahwa **Permission lebih dari sekadar Role**. Role adalah **grup permission**, tetapi sistem harus fleksibel untuk mengakomodasi:

```text
┌─────────────────────────────────────────────────────────────┐
│                    YANG DIKONTROL PERMISSION                  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  📦 Module     → Apakah user bisa mengakses modul ini?      │
│  📋 Menu       → Apakah user bisa melihat menu ini?         │
│  ⚡ Action     → Apakah user bisa create/edit/delete ini?   │
│  📊 Widget     → Widget dashboard mana yang terlihat?        │
│  🖥️ Dashboard  → Layout dashboard untuk role tertentu        │
│  🔌 API        → Apakah user bisa akses endpoint ini?       │
│  ⚙️ Setting    → Apakah user bisa mengubah pengaturan ini?   │
│  🎨 Theme      → Apakah user bisa mengganti tema?            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Filosofi Keamanan

| Prinsip | Implementasi |
| :--- | :--- |
| **Least Privilege** | Setiap user hanya mendapat permission minimum yang diperlukan |
| **Deny by Default** | Semua akses ditolak secara default — permission harus diberikan eksplisit |
| **Separation of Duty** | Role kritis dipisah (contoh: pustakawan tidak bisa approve peminjaman sendiri) |
| **Defense in Depth** | Permission dicek di Middleware + Policy + Service Layer |
| **Audit Trail** | Setiap keputusan akses (grant/deny) dicatat untuk audit |

### 2.3 Hierarki Akses

```text
                   ┌──────────────────┐
                   │   Super Admin    │ ← Bypass semua pengecekan
                   └──────────────────┘
                           │
              ┌────────────┴────────────┐
              │                         │
     ┌────────────────┐       ┌─────────────────┐
     │ Admin Sekolah  │       │  Kepala Sekolah  │
     └────────────────┘       └─────────────────┘
              │                         │
     ┌────────────────┐       ┌─────────────────┐
     │ Kepala         │       │    Guru         │
     │ Perpustakaan   │       │                 │
     └────────────────┘       └─────────────────┘
              │                         │
     ┌────────────────┐       ┌─────────────────┐
     │  Pustakawan    │       │     Siswa       │
     └────────────────┘       └─────────────────┘
              │                         │
     ┌────────────────┐       ┌─────────────────┐
     │   Operator     │       │     Guest       │
     └────────────────┘       └─────────────────┘
```

---

## 3. Authorization Model

### 3.1 RBAC Flow

CosmicLib menggunakan model RBAC dengan alur otorisasi sebagai berikut:

```text
┌──────────┐     ┌──────────┐     ┌──────────────┐     ┌──────────┐     ┌────────────────┐     ┌───────────────┐
│          │     │          │     │              │     │          │     │                │     │               │
│   USER   │────▶│   ROLE   │────▶│  PERMISSION  │────▶│  POLICY  │────▶│  MIDDLEWARE    │────▶│     ACCESS    │
│          │     │          │     │              │     │          │     │                │     │   DECISION    │
└──────────┘     └──────────┘     └──────────────┘     └──────────┘     └────────────────┘     └───────────────┘
     │                                                                                              │
     │                                                                                              │
     └──────────────────────── User Override (opsional) ────────────────────────────────────────────┘
```

### 3.2 Penjelasan Setiap Lapisan

| Lapisan | Tanggung Jawab | Contoh |
| :--- | :--- | :--- |
| **User** | Entitas pengguna yang melakukan request | User ID: 42 |
| **Role** | Grup permission yang diassign ke user | Role: Pustakawan |
| **Permission** | Izin spesifik untuk melakukan aksi | `library.books.create` |
| **Policy** | Otorisasi berbasis model dan konteks | `BookPolicy::update($user, $book)` |
| **Middleware** | Proteksi akses di level route | `middleware('can:library.books.view')` |
| **Access Decision** | Hasil akhir: GRANT atau DENY | ✅ Grant / ❌ Deny |

### 3.3 Alur Pengecekan Akses

```text
User melakukan aksi
    │
    ├── 1. CEK SUPER ADMIN
    │       ├── Jika Super Admin → ✅ GRANT (bypass semua)
    │       └── Jika bukan Super Admin → Lanjut
    │
    ├── 2. CEK POLICY (jika ada)
    │       ├── Policy menangani logika berbasis model
    │       ├── Contoh: "Apakah user ini bisa edit buku ini?"
    │       └── Jika Policy deny → ❌ DENY
    │
    ├── 3. CEK MIDDLEWARE (jika ada)
    │       ├── Middleware mengecek permission umum
    │       ├── Contoh: "Apakah user punya akses ke route ini?"
    │       └── Jika middleware deny → ❌ DENY
    │
    ├── 4. CEK PERMISSION
    │       ├── Ambil permission dari cache
    │       ├── Cek apakah user memiliki permission yang diperlukan
    │       │       ├── Dari role → Cek role_user → role_permission
    │       │       └── Dari user override → Cek user_permission
    │       └── Jika tidak memiliki permission → ❌ DENY
    │
    └── 5. DECISION
            ├── Semua cek lolos → ✅ GRANT
            ├── Salah satu gagal → ❌ DENY + Log audit
            └── Jika deny → 403 Forbidden
```

---

## 4. Permission Structure

### 4.1 Entity Relationship

Permission Engine terdiri dari entitas-entitas berikut:

```text
┌─────────────────┐       ┌──────────────────────┐
│      users      │       │       roles           │
├─────────────────┤       ├──────────────────────┤
│ id (bigint)     │       │ id (bigint)           │
│ name            │──────┐│ name                  │
│ email           │      ││ slug                  │
│ password        │      ││ description           │
│ ...             │      ││ guard_name            │
└─────────────────┘      ││ is_system             │
                         ││ created_at            │
┌──────────────────┐     ││ updated_at            │
│   role_user       │     │└──────────────────────┘
├──────────────────┤     │
│ id (bigint)      │     │ ┌──────────────────────────┐
│ user_id          │◄────┘ │     permissions           │
│ role_id          │◄──────├──────────────────────────┤
│ created_at       │       │ id (bigint)               │
└──────────────────┘       │ name (contoh: books.view) │
                           │ slug                      │
┌──────────────────────┐   │ group (contoh: library)   │
│   role_permission     │   │ guard_name                │
├──────────────────────┤   │ created_at                │
│ id (bigint)          │   │ updated_at                │
│ role_id              │◄──└──────────────────────────┘
│ permission_id        │◄──┘
│ created_at           │       ┌──────────────────────────┐
└──────────────────────┘       │    user_permission       │
                               ├──────────────────────────┤
┌──────────────────────┐       │ id (bigint)              │
│       policies        │       │ user_id                  │
├──────────────────────┤       │ permission_id            │
│ id (bigint)          │       │ type: 'grant' | 'deny'   │
│ name                 │       │ created_at               │
│ module               │       └──────────────────────────┘
│ model                │
│ actions (JSON)       │
│ created_at           │
└──────────────────────┘
```

### 4.2 Penjelasan Entity

| Entity | Fungsi | Relasi |
| :--- | :--- | :--- |
| **users** | Pengguna sistem | Memiliki banyak roles (via role_user) |
| **roles** | Grup permission | Dimiliki banyak users, memiliki banyak permissions |
| **permissions** | Izin spesifik | Dimiliki banyak roles, dapat diassign langsung ke user |
| **role_user** | Pivot user ↔ role | Jembatan many-to-many user dan role |
| **role_permission** | Pivot role ↔ permission | Jembatan many-to-many role dan permission |
| **user_permission** | Override permission user | Grant/deny individual tambahan di luar role |
| **policies** | Aturan otorisasi model | Opsional, digunakan untuk logika kompleks |

### 4.3 Permission Naming Convention

Permission Engine menggunakan format penamaan yang konsisten:

```text
Format: {module}.{resource}.{action}

Contoh:
library.books.view
library.books.create
library.books.edit
library.books.delete
library.books.import
library.books.export

library.members.view
library.members.create
library.members.edit
library.members.delete

library.loans.view
library.loans.create
library.loans.approve
library.loans.reject
library.loans.return

cms.posts.view
cms.posts.create
cms.posts.edit
cms.posts.delete
cms.posts.publish
cms.posts.unpublish

system.settings.view
system.settings.manage
system.users.view
system.users.create
system.users.edit
system.users.delete
system.users.manage

system.theme.view
system.theme.activate
system.theme.customize
system.theme.manage

system.backup.create
system.backup.restore
system.backup.download
system.backup.delete

system.update.view
system.update.install

media.files.upload
media.files.download
media.files.delete
media.files.manage

report.general.view
report.general.export
report.finance.view
report.finance.export
```

### 4.4 Aturan Penamaan Permission

| Aturan | Contoh ✅ | Contoh ❌ |
| :--- | :--- | :--- |
| Gunakan lowercase | `library.books.view` | `Library.Books.View` |
| Gunakan dot (`.`) sebagai separator | `system.users.manage` | `system_users_manage` |
| Module name sebagai prefix | `library.books.view` | `view_books` |
| Resource name di tengah | `library.books.view` | `library.view_books` |
| Action name di akhir | `library.books.view` | `view.library.books` |
| Gunakan kata tunggal untuk resource | `library.books.view` | `library.listing_buku.view` |
| Maksimal 3 segment | `library.books.view` | `library.books.detail.view` |

---

## 5. Role System

### 5.1 Default Role

CosmicLib menyediakan sembilan role default yang mencakup struktur organisasi sekolah:

| # | Role | Slug | Level | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Super Administrator** | `super-admin` | 100 | Akses penuh ke seluruh sistem — bypass semua permission check |
| 2 | **Administrator Sekolah** | `admin-sekolah` | 90 | Manajemen pengguna, konfigurasi sekolah, semua modul kecuali super |
| 3 | **Kepala Sekolah** | `kepala-sekolah` | 80 | Lihat laporan & statistik, approve kebijakan, tidak bisa operasional |
| 4 | **Kepala Perpustakaan** | `kepala-perpustakaan` | 70 | Kelola modul library, staff, kebijakan peminjaman |
| 5 | **Pustakawan** | `pustakawan` | 60 | Operasional perpustakaan — buku, anggota, peminjaman, pengembalian |
| 6 | **Guru** | `guru` | 50 | Lihat katalog, laporan baca siswa, rekomendasi buku |
| 7 | **Siswa** | `siswa` | 40 | Cari buku, pinjam, riwayat peminjaman, profil sendiri |
| 8 | **Operator** | `operator` | 30 | Input data, entri anggota, administrasi ringan |
| 9 | **Guest** | `guest` | 10 | Hanya lihat katalog publik tanpa login |

### 5.2 Detail Setiap Role

#### 5.2.1 Super Administrator (Level 100)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Pemilik sistem — akses penuh tanpa batas |
| **Permission** | **Bypass semua pengecekan** — tidak perlu assign permission manual |
| **Manajemen** | Buat/hapus role, assign permission, manage semua modul |
| **Keamanan** | Hanya 1 akun super admin per instalasi (bisa multiple, sangat tidak disarankan) |
| **Audit** | Semua aksi super admin wajib di-log |

#### 5.2.2 Administrator Sekolah (Level 90)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Admin operasional sekolah — mengelola pengguna & konfigurasi |
| **Permission** | Semua permission `system.*`, `module.*` (kecuali super admin) |
| **Manajemen** | CRUD user, assign role, import data, backup, update |
| **Batasan** | Tidak bisa mengubah role super admin, tidak bisa menghapus log audit |

#### 5.2.3 Kepala Sekolah (Level 80)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Pimpinan sekolah — melihat laporan dan statistik |
| **Permission** | `report.*`, `library.reports.*`, `dashboard.admin` |
| **Manajemen** | Lihat semua laporan, export data, approve kebijakan |
| **Batasan** | Tidak bisa CRUD data, tidak bisa manage user |

#### 5.2.4 Kepala Perpustakaan (Level 70)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Manajer perpustakaan — mengelola staff dan kebijakan |
| **Permission** | Semua `library.*`, `library.loans.approve`, `library.settings.*` |
| **Manajemen** | Tambah/hapus pustakawan, atur denda, atur kebijakan peminjaman |
| **Batasan** | Tidak bisa akses sistem, manajemen user, atau modul lain |

#### 5.2.5 Pustakawan (Level 60)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Staff perpustakaan — operasional sehari-hari |
| **Permission** | `library.books.*`, `library.members.*`, `library.loans.*` (kecuali approve) |
| **Manajemen** | Input buku, daftarkan anggota, proses pinjam/kembali |
| **Batasan** | Tidak bisa approve peminjaman sendiri, tidak bisa ubah settings |

#### 5.2.6 Guru (Level 50)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Tenaga pengajar — akses terbatas ke perpustakaan |
| **Permission** | `library.books.view`, `library.reports.read`, `cms.posts.view` |
| **Manajemen** | Cari buku, lihat laporan baca siswa, beri rekomendasi |
| **Batasan** | Tidak bisa pinjam untuk siswa, tidak bisa kelola data |

#### 5.2.7 Siswa (Level 40)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Peserta didik — akses minimal untuk kebutuhan belajar |
| **Permission** | `library.books.view`, `library.loans.self.*`, `profile.*` |
| **Manajemen** | Cari buku, pinjam buku sendiri, lihat riwayat sendiri |
| **Batasan** | Hanya akses data diri sendiri, tidak bisa akses modul lain |

#### 5.2.8 Operator (Level 30)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Staff administrasi — entri data |
| **Permission** | `library.members.create`, `library.books.create`, `membership.*` |
| **Manajemen** | Input data anggota, input data buku baru, cetak kartu anggota |
| **Batasan** | Tidak bisa edit/delete data yang sudah ada |

#### 5.2.9 Guest (Level 10)

| Aspek | Detail |
| :--- | :--- |
| **Fungsi** | Pengunjung belum login — akses publik |
| **Permission** | `library.books.view` (halaman publik), `landing.view` |
| **Manajemen** | Lihat katalog publik, cari buku, daftar akun |
| **Batasan** | Tidak bisa pinjam, tidak bisa akses dashboard |

### 5.3 Aturan Role

| Aturan | Keterangan |
| :--- | :--- |
| **Role bersifat additive** | Permission role diakumulasi — user dengan 2 role mendapat permission gabungan |
| **User override mengalahkan role** | `user_permission` tipe `deny` dapat mencabut permission yang didapat dari role |
| **Super Admin bypass** | Super Admin tidak perlu permission check — akses penuh |
| **Role system tidak bisa dihapus** | Role bawaan (super-admin, admin-sekolah, guest) tidak bisa dihapus |
| **Role custom dapat dibuat** | Admin sekolah dapat membuat role kustom dengan permission spesifik |
| **Role bisa di-clone** | Admin dapat menduplikasi role yang sudah ada untuk membuat role baru |

---

## 6. Permission Categories

### 6.1 Kategori Permission

Permission di CosmicLib dikelompokkan ke dalam kategori berikut:

| Kategori | Prefix | Lingkup | Contoh |
| :--- | :--- | :--- | :--- |
| **Core** | `core.*` | Fungsi dasar engine | `core.access`, `core.maintenance` |
| **System** | `system.*` | Konfigurasi sistem | `system.settings.manage` |
| **Module** | `{module}.*` | Per modul | `library.books.view` |
| **Menu** | `menu.*` | Visibilitas menu | `menu.library.view` |
| **Widget** | `widget.*` | Visibilitas widget | `widget.dashboard.stats.view` |
| **Theme** | `theme.*` | Manajemen tema | `theme.activate`, `theme.customize` |
| **Media** | `media.*` | Upload & file | `media.files.upload` |
| **Library** | `library.*` | Modul perpustakaan | `library.books.create` |
| **CMS** | `cms.*` | Konten halaman | `cms.posts.publish` |
| **API** | `api.*` | Akses API | `api.library.read` |

### 6.2 Struktur Kategori

```text
Permission Categories
    │
    ├── 🔧 CORE (core.*)
    │       └── core.access, core.maintenance
    │
    ├── ⚙️ SYSTEM (system.*)
    │       ├── system.settings.*
    │       ├── system.users.*
    │       ├── system.roles.*
    │       ├── system.backup.*
    │       ├── system.update.*
    │       └── system.logs.*
    │
    ├── 📦 MODULE ({module}.*)
    │       └── Setiap module memiliki prefix sendiri
    │           ├── library.*
    │           ├── cms.*
    │           ├── membership.*
    │           └── report.*
    │
    ├── 📋 MENU (menu.*)
    │       ├── menu.dashboard.view
    │       ├── menu.library.view
    │       ├── menu.reports.view
    │       └── menu.settings.view
    │
    ├── 📊 WIDGET (widget.*)
    │       ├── widget.dashboard.stats.view
    │       ├── widget.dashboard.chart.view
    │       └── widget.dashboard.recent.view
    │
    ├── 🎨 THEME (theme.*)
    │       ├── theme.view
    │       ├── theme.activate
    │       ├── theme.customize
    │       ├── theme.css.edit
    │       ├── theme.js.edit
    │       ├── theme.logo.upload
    │       └── theme.favicon.upload
    │
    ├── 🖼️ MEDIA (media.*)
    │       ├── media.files.upload
    │       ├── media.files.download
    │       ├── media.files.delete
    │       └── media.files.manage
    │
    ├── 📚 LIBRARY (library.*)
    │       ├── library.books.*
    │       ├── library.members.*
    │       ├── library.loans.*
    │       ├── library.categories.*
    │       ├── library.publishers.*
    │       ├── library.authors.*
    │       ├── library.reports.*
    │       └── library.settings.*
    │
    ├── 📝 CMS (cms.*)
    │       ├── cms.posts.*
    │       ├── cms.pages.*
    │       ├── cms.announcements.*
    │       └── cms.settings.*
    │
    └── 🔌 API (api.*)
            ├── api.library.read
            ├── api.library.write
            ├── api.library.delete
            └── api.library.manage
```

---

## 7. Action Permission

### 7.1 Standarisasi Aksi

Permission Engine menstandarisasi aksi-aksi berikut yang dapat dikombinasikan dengan resource:

| Aksi | Slug | Deskripsi | Contoh Permission |
| :--- | :--- | :--- | :--- |
| **View** | `view` | Melihat/membaca data | `library.books.view` |
| **Create** | `create` | Membuat data baru | `library.books.create` |
| **Edit** | `edit` | Mengubah data yang sudah ada | `library.books.edit` |
| **Delete** | `delete` | Menghapus data | `library.books.delete` |
| **Restore** | `restore` | Mengembalikan data yang dihapus | `library.books.restore` |
| **Import** | `import` | Mengimpor data dari file | `library.books.import` |
| **Export** | `export` | Mengekspor data ke file | `library.books.export` |
| **Print** | `print` | Mencetak data | `library.books.print` |
| **Approve** | `approve` | Menyetujui permintaan | `library.loans.approve` |
| **Reject** | `reject` | Menolak permintaan | `library.loans.reject` |
| **Publish** | `publish` | Menerbitkan konten | `cms.posts.publish` |
| **Unpublish** | `unpublish` | Menarik konten | `cms.posts.unpublish` |
| **Activate** | `activate` | Mengaktifkan sesuatu | `system.module.activate` |
| **Deactivate** | `deactivate` | Menonaktifkan sesuatu | `system.module.deactivate` |
| **Download** | `download` | Mengunduh file | `media.files.download` |
| **Upload** | `upload` | Mengunggah file | `media.files.upload` |
| **Manage** | `manage` | Manajemen penuh (all-in-one) | `system.settings.manage` |
| **Configure** | `configure` | Mengubah konfigurasi | `library.settings.configure` |

### 7.2 Permission Calculator

Beberapa permission bersifat **kumulatif**:

```text
manage = view + create + edit + delete + configure

Contoh:
Jika user memiliki system.settings.manage
→ Maka user otomatis memiliki:
  ✓ system.settings.view
  ✓ system.settings.create
  ✓ system.settings.edit
  ✓ system.settings.delete
  ✓ system.settings.configure
```

### 7.3 Daftar Permission Per Resource

Setiap resource standar memiliki permission sebagai berikut:

| Resource | View | Create | Edit | Delete | Restore | Import | Export | Manage |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **books** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **members** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **loans** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **users** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **roles** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **posts** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **pages** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **categories** | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **settings** | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ |

---

## 8. Module Permission

### 8.1 Setiap Module WAJIB memiliki `permission.php`

Setiap modul CosmicLib **WAJIB** mendefinisikan permission-nya sendiri dalam file `permission.php`. File ini menjadi **kontrak permission** yang didaftarkan ke Permission Engine saat modul diaktifkan.

### 8.2 Struktur `permission.php`

```text
modules/
└── Library/                              ← Nama modul
    ├── module.json                       ← Manifest modul
    ├── Permission/
    │   └── permission.php                ← WAJIB: Definisi permission
    │       │
    │       └── Isi:
    │           [
    │               {
    │                   "name": "library.books.view",
    │                   "display_name": "Lihat Buku",
    │                   "description": "Melihat daftar dan detail buku",
    │                   "group": "library.books",
    │                   "guard_name": "web"
    │               },
    │               {
    │                   "name": "library.books.create",
    │                   "display_name": "Tambah Buku",
    │                   "description": "Menambahkan buku baru ke perpustakaan",
    │                   "group": "library.books",
    │                   "guard_name": "web"
    │               },
    │               ...
    │           ]
    │
    └── ...
```

### 8.3 Contoh `permission.php` Lengkap untuk Modul Library

```text
Module: Library

Permission yang diregistrasi:
┌──────────────────────────────────────┬──────────────────────────┬──────────────────────────────┐
│ Permission                          │ Display Name             │ Group                       │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.books.view                  │ Lihat Buku               │ library.books               │
│ library.books.create                │ Tambah Buku              │ library.books               │
│ library.books.edit                  │ Ubah Buku                │ library.books               │
│ library.books.delete                │ Hapus Buku               │ library.books               │
│ library.books.restore               │ Pulihkan Buku            │ library.books               │
│ library.books.import                │ Import Buku              │ library.books               │
│ library.books.export                │ Export Buku              │ library.books               │
│ library.books.print                 │ Cetak Buku               │ library.books               │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.members.view                │ Lihat Anggota            │ library.members             │
│ library.members.create              │ Daftarkan Anggota        │ library.members             │
│ library.members.edit                │ Ubah Anggota             │ library.members             │
│ library.members.delete              │ Hapus Anggota            │ library.members             │
│ library.members.restore             │ Pulihkan Anggota         │ library.members             │
│ library.members.import              │ Import Anggota           │ library.members             │
│ library.members.export              │ Export Anggota           │ library.members             │
│ library.members.print               │ Cetak Kartu Anggota      │ library.members             │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.loans.view                  │ Lihat Peminjaman         │ library.loans               │
│ library.loans.create                │ Pinjam Buku              │ library.loans               │
│ library.loans.return                │ Kembalikan Buku          │ library.loans               │
│ library.loans.approve               │ Setujui Peminjaman       │ library.loans               │
│ library.loans.reject                │ Tolak Peminjaman         │ library.loans               │
│ library.loans.extend                │ Perpanjang Peminjaman   │ library.loans               │
│ library.loans.export                │ Export Data Peminjaman   │ library.loans               │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.categories.view             │ Lihat Kategori           │ library.categories          │
│ library.categories.create           │ Tambah Kategori          │ library.categories          │
│ library.categories.edit             │ Ubah Kategori            │ library.categories          │
│ library.categories.delete           │ Hapus Kategori           │ library.categories          │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.publishers.view             │ Lihat Penerbit           │ library.publishers          │
│ library.publishers.create           │ Tambah Penerbit          │ library.publishers          │
│ library.publishers.edit             │ Ubah Penerbit            │ library.publishers          │
│ library.publishers.delete           │ Hapus Penerbit           │ library.publishers          │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.authors.view                │ Lihat Penulis            │ library.authors             │
│ library.authors.create              │ Tambah Penulis           │ library.authors             │
│ library.authors.edit                │ Ubah Penulis             │ library.authors             │
│ library.authors.delete              │ Hapus Penulis            │ library.authors             │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.reports.view                │ Lihat Laporan            │ library.reports             │
│ library.reports.generate            │ Generate Laporan         │ library.reports             │
│ library.reports.export              │ Export Laporan           │ library.reports             │
│ library.reports.print               │ Cetak Laporan            │ library.reports             │
├──────────────────────────────────────┼──────────────────────────┼──────────────────────────────┤
│ library.settings.view               │ Lihat Pengaturan         │ library.settings            │
│ library.settings.manage             │ Kelola Pengaturan        │ library.settings            │
│ library.settings.fines              │ Atur Denda               │ library.settings            │
│ library.settings.policy             │ Atur Kebijakan           │ library.settings            │
└──────────────────────────────────────┴──────────────────────────┴──────────────────────────────┘
```

### 8.4 Alur Registrasi Permission Module

```text
Module diaktifkan
    │
    ├── 1. Module Engine membaca module.json
    │
    ├── 2. Cek file Permission/permission.php
    │       ├── Ada → Lanjut ke langkah 3
    │       └── Tidak ada → Skip (module tanpa permission)
    │
    ├── 3. Parse file permission.php
    │       ├── Validasi format array
    │       ├── Cek duplikasi permission name
    │       └── Cek format penamaan sesuai standar
    │
    ├── 4. Registrasi ke Database
    │       ├── Insert permission yang belum ada
    │       ├── Update permission yang sudah ada (jika ada perubahan)
    │       └── Jangan hapus permission yang sudah ada (untuk mencegah data loss)
    │
    ├── 5. Assign ke Default Role (jika ada mapping)
    │       ├── Baca default_role_assignment dari module.json
    │       └── Assign permission ke role yang ditentukan
    │
    ├── 6. Sinkronisasi ke Cache
    │       ├── Clear permission cache
    │       └── Rebuild cache dengan data terbaru
    │
    └── 7. Dispatch Event
            └── ModulePermissionsRegistered { module: name, permissions: count }
```

---

## 9. Menu Permission

### 9.1 Setiap Menu Memiliki Permission

Setiap menu di CosmicLib **WAJIB** memiliki permission. Menu akan otomatis disembunyikan jika user tidak memiliki akses.

### 9.2 Struktur Menu dengan Permission

```text
Menu Item
    │
    ├── name: "Perpustakaan"
    ├── route: "admin.library.index"
    ├── icon: "book"
    │
    ├── permission_view: "menu.library.view"
    │       └── Jika user tidak punya → Menu tidak muncul
    │
    ├── permission_manage: "library.books.manage"
    │       └── Jika user punya → Tampilkan tombol "Kelola" di submenu
    │
    └── children: [
            {
                name: "Buku",
                route: "admin.library.books.index",
                permission_view: "library.books.view"
            },
            {
                name: "Anggota",
                route: "admin.library.members.index",
                permission_view: "library.members.view"
            },
            {
                name: "Peminjaman",
                route: "admin.library.loans.index",
                permission_view: "library.loans.view"
            }
        ]
```

### 9.3 Aturan Visibilitas Menu

```text
Menu Render
    │
    ├── 1. Menu Engine mengambil daftar menu
    │
    ├── 2. Untuk setiap menu, cek permission_view
    │       ├── User punya permission → Tampilkan menu
    │       ├── User tidak punya → Sembunyikan menu
    │       └── Jika menu memiliki children
    │               ├── Cek permission_view masing-masing child
    │               └── Hanya tampilkan child yang diizinkan
    │
    ├── 3. Jika menu parent memiliki children yang diizinkan
    │       → Tampilkan parent (meskipun parent sendiri tidak punya akses)
    │
    └── 4. Jika semua children disembunyikan
            → Sembunyikan parent juga
```

### 9.4 Menu Permission Categories

| Permission | Fungsi |
| :--- | :--- |
| `menu.dashboard.view` | Tampilkan menu Dashboard |
| `menu.library.view` | Tampilkan menu Perpustakaan |
| `menu.library.manage` | Tampilkan opsi kelola di menu Perpustakaan |
| `menu.reports.view` | Tampilkan menu Laporan |
| `menu.cms.view` | Tampilkan menu CMS/Konten |
| `menu.settings.view` | Tampilkan menu Pengaturan |
| `menu.users.view` | Tampilkan menu Manajemen Pengguna |
| `menu.theme.view` | Tampilkan menu Tema |
| `menu.backup.view` | Tampilkan menu Backup |

---

## 10. Widget Permission

### 10.1 Widget Dashboard Berdasarkan Permission

Widget dashboard CosmicLib dapat diatur visibilitasnya berdasarkan permission:

| Permission | Widget |
| :--- | :--- |
| `widget.dashboard.stats.view` | Statistik umum (total buku, anggota, peminjaman) |
| `widget.dashboard.chart.view` | Grafik peminjaman per bulan |
| `widget.dashboard.recent.view` | Peminjaman terbaru |
| `widget.dashboard.popular.view` | Buku paling populer |
| `widget.dashboard.overdue.view` | Peminjaman terlambat |
| `widget.dashboard.members.view` | Anggota baru |
| `widget.dashboard.activity.view` | Aktivitas terkini |

### 10.2 Widget Visibility Logic

```text
Dashboard Render
    │
    ├── 1. Widget Engine mengambil daftar widget untuk dashboard
    │
    ├── 2. Untuk setiap widget
    │       ├── Cek permission widget
    │       ├── User punya → Tampilkan widget dengan data
    │       └── User tidak punya → Sembunyikan widget
    │
    ├── 3. Widget dari Module
    │       ├── Modul mendaftarkan widget dengan permission
    │       └── Widget hanya tampil jika user punya akses ke modul
    │
    └── 4. User dapat memilih widget sendiri (preferensi)
            └── Hanya widget yang diizinkan yang bisa dipilih
```

### 10.3 Widget Permission Per Role

| Role | Statistik | Grafik | Terbaru | Populer | Terlambat | Anggota Baru |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Admin Sekolah** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Kepala Sekolah** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Kepala Perpus** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Pustakawan** | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ |
| **Guru** | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Siswa** | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |

---

## 11. Theme Permission

### 11.1 Permission untuk Manajemen Tema

Permission berikut mengontrol akses ke Theme Engine:

| Permission | Deskripsi | Diberikan ke Role |
| :--- | :--- | :--- |
| `theme.view` | Melihat halaman daftar tema | Admin Sekolah, Kepala Perpus |
| `theme.activate` | Mengaktifkan tema | Super Admin, Admin Sekolah |
| `theme.preview` | Pratinjau tema sebelum diaktifkan | Admin Sekolah, Kepala Perpus |
| `theme.customize` | Mengakses Theme Customizer | Super Admin, Admin Sekolah |
| `theme.css.edit` | Mengedit Custom CSS tema | Super Admin (hati-hati) |
| `theme.js.edit` | Mengedit Custom JS tema | Super Admin (hati-hati) |
| `theme.logo.upload` | Upload logo sekolah | Admin Sekolah |
| `theme.favicon.upload` | Upload favicon sekolah | Admin Sekolah |
| `theme.import` | Import tema dari ZIP | Super Admin |
| `theme.export` | Export tema ke ZIP | Super Admin |
| `theme.delete` | Hapus tema dari sistem | Super Admin |

### 11.2 Theme Permission oleh Role

| Role | View | Activate | Customize | Edit CSS | Edit JS | Upload Logo | Import | Export |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Admin Sekolah** | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Kepala Sekolah** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Kepala Perpus** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Pustakawan** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 12. API Permission

### 12.1 Permission untuk API

CosmicLib menyediakan permission khusus untuk akses API (REST/GraphQL):

| Permission | Deskripsi | Scope |
| :--- | :--- | :--- |
| `api.library.read` | Membaca data perpustakaan via API | GET endpoints |
| `api.library.write` | Menulis data perpustakaan via API | POST, PUT, PATCH |
| `api.library.delete` | Menghapus data perpustakaan via API | DELETE endpoints |
| `api.library.manage` | Manajemen penuh API perpustakaan | All methods |
| `api.system.read` | Membaca data sistem via API | GET endpoints |
| `api.system.manage` | Manajemen sistem via API | All methods |
| `api.report.read` | Mengakses laporan via API | GET reports |
| `api.report.generate` | Generate laporan via API | POST generate |

### 12.2 API Token Permission

```text
API Token
    │
    ├── Personal Access Token (untuk user)
    │       └── Mewarisi permission dari role user
    │
    └── Client Credential Token (untuk aplikasi)
            └── Permission ditentukan saat pembuatan token
                ├── library:read
                ├── library:write
                └── report:read
```

### 12.3 API Permission Check

```text
Request ke API endpoint
    │
    ├── 1. Authentikasi token
    │       ├── Valid → Lanjut
    │       └── Invalid → 401 Unauthorized
    │
    ├── 2. Cek scope permission token
    │       ├── Token memiliki scope yang diperlukan → Lanjut
    │       └── Token tidak memiliki scope → 403 Forbidden
    │
    ├── 3. Jika token Personal Access Token (milik user)
    │       └── Cek permission user (sama seperti web)
    │
    └── 4. Decision
            ├── Lolos → Akses diberikan
            └── Gagal → 403 Forbidden + Log audit
```

---

## 13. Policy

### 13.1 Laravel Policy

Permission Engine menggunakan **Laravel Policy** untuk otorisasi berbasis model. Policy menangani logika yang lebih kompleks dari sekadar permission — seperti kepemilikan data, status record, atau relasi.

### 13.2 Contoh Penggunaan Policy

Policy digunakan ketika pengecekan memerlukan **konteks data**:

| Skenario | Tanpa Policy | Dengan Policy |
| :--- | :--- | :--- |
| User edit buku | Cek permission `library.books.edit` | Cek permission + apakah user adalah pembuat buku? |
| User approve peminjaman | Cek permission `library.loans.approve` | Cek permission + apakah user bukan peminjam? |
| User hapus post | Cek permission `cms.posts.delete` | Cek permission + apakah post masih draft? |
| User lihat laporan | Cek permission `report.view` | Cek permission + apakah user adalah kepala sekolah? |

### 13.3 Policy Methods Standar

Setiap Policy kelas harus mengimplementasikan method berikut sesuai kebutuhan:

| Method | Parameter | Digunakan Untuk |
| :--- | :--- | :--- |
| `viewAny` | `$user` | Melihat daftar semua resource |
| `view` | `$user, $model` | Melihat detail resource tertentu |
| `create` | `$user` | Membuat resource baru |
| `update` | `$user, $model` | Mengupdate resource |
| `delete` | `$user, $model` | Menghapus resource |
| `restore` | `$user, $model` | Mengembalikan resource yang dihapus |
| `forceDelete` | `$user, $model` | Menghapus permanen resource |

### 13.4 Alur Policy

```text
Policy Check
    │
    ├── 1. Cek Super Admin
    │       ├── Ya → ✅ Grant
    │       └── Tidak → Lanjut
    │
    ├── 2. Cek Permission Dasar
    │       ├── User punya permission → Lanjut ke policy
    │       └── User tidak punya → ❌ Deny
    │
    ├── 3. Eksekusi Policy Method
    │       ├── Contoh: BookPolicy::update($user, $book)
    │       │
    │       ├── Cek 1: Apakah user adalah penulis buku?
    │       │       ├── Ya → ✅ Grant
    │       │       └── Tidak → Lanjut
    │       │
    │       ├── Cek 2: Apakah user memiliki role khusus?
    │       │       ├── Kepala Perpustakaan → ✅ Grant
    │       │       └── Bukan → Lanjut
    │       │
    │       └── Cek 3: Apakah buku sedang dalam status tertentu?
    │               ├── Draft → ✅ Grant
    │               └── Published → ❌ Deny (hanya admin yang bisa)
    │
    └── 4. Decision
            ├── Policy return true → ✅ Grant
            └── Policy return false → ❌ Deny
```

### 13.5 Aturan Policy

| Aturan | Keterangan |
| :--- | :--- |
| **Policy Wajib untuk Model** | Setiap model yang memiliki akses berbasis kepemilikan WAJIB memiliki Policy |
| **Policy Bukan Pengganti Permission** | Policy melengkapi, bukan menggantikan permission check |
| **Policy Dipanggil Setelah Permission** | Permission dicek dulu, baru policy dieksekusi |
| **Policy Bisa Melewati Permission** | Policy dapat mengabulkan akses meskipun user tidak memiliki permission (untuk pemilik data) |

---

## 14. Middleware

### 14.1 Middleware Permission

Permission Engine menggunakan **Middleware** untuk melindungi route dari akses yang tidak sah.

### 14.2 Jenis Middleware

| Middleware | Fungsi | Contoh Penggunaan |
| :--- | :--- | :--- |
| `can:permission` | Cek permission user | `can:library.books.view` |
| `can:policy,model` | Cek policy pada model | `can:update,book` |
| `role:slug` | Cek role user | `role:admin-sekolah` |
| `permission:permission1,permission2` | Cek multiple permission (ALL) | `permission:library.books.view,library.books.create` |
| `permission_or:permission1,permission2` | Cek multiple permission (ANY) | `permission_or:library.books.view,library.members.view` |

### 14.3 Contoh Penggunaan Middleware di Route

```text
Route Definitions
    │
    ├── Route::get('/books', [BookController::class, 'index'])
    │       → middleware('can:library.books.view')
    │
    ├── Route::post('/books', [BookController::class, 'store'])
    │       → middleware('can:library.books.create')
    │
    ├── Route::put('/books/{book}', [BookController::class, 'update'])
    │       → middleware('can:update,book')
    │
    ├── Route::delete('/books/{book}', [BookController::class, 'destroy'])
    │       → middleware('can:delete,book')
    │
    ├── Route::group dengan prefix admin
    │       → middleware('can:system.access.admin')
    │
    └── Route::group untuk role tertentu
            → middleware('role:admin-sekolah,pustakawan')
```

### 14.4 Middleware Alur

```text
Request → Route → Middleware
    │
    ├── Middleware: auth
    │       ├── User login → Lanjut
    │       └── Tidak login → Redirect ke login
    │
    ├── Middleware: can:{permission}
    │       ├── Cek permission dari cache
    │       ├── Punya permission → Lanjut
    │       └── Tidak punya → 403 Forbidden
    │
    ├── Middleware: can:{policy},{model}
    │       ├── Panggil Policy method
    │       ├── Grant → Lanjut
    │       └── Deny → 403 Forbidden
    │
    └── → Controller → Service → Response
```

### 14.5 Aturan Middleware

| Aturan | Keterangan |
| :--- | :--- |
| **Middleware di Route** | Semua route yang memerlukan otorisasi WAJIB memiliki middleware |
| **Middleware Group** | Gunakan route group untuk middleware yang sama |
| **Naming Consistent** | Gunakan format `{module}.{resource}.{action}` di middleware parameter |
| **Policy Middleware** | Untuk operasi berbasis model, gunakan `can:{policy},{model}` |

---

## 15. User Override

### 15.1 Konsep User Override

Selain melalui Role, permission dapat diberikan atau dicabut secara individual untuk user tertentu melalui **User Override**. Ini memberikan fleksibilitas tanpa harus membuat role baru.

### 15.2 Tipe User Override

| Tipe | Fungsi | Contoh Skenario |
| :--- | :--- | :--- |
| **Grant** | Menambahkan permission ke user di luar role | Pustakawan yang dipercaya bisa approve peminjaman |
| **Deny** | Mencabut permission dari user meskipun role memilikinya | Admin yang tidak boleh menghapus data |

### 15.3 Alur User Override

```text
Cek Permission untuk User
    │
    ├── 1. Ambil semua permission dari Role user
    │       ├── Role 1: [library.books.view, library.books.create]
    │       ├── Role 2: [library.members.view]
    │       └── Total Role Permissions: [library.books.view, library.books.create, library.members.view]
    │
    ├── 2. Cek User Override — Grant
    │       ├── User memiliki override grant: [library.loans.approve]
    │       └── Tambahkan ke daftar: [... + library.loans.approve]
    │
    ├── 3. Cek User Override — Deny
    │       ├── User memiliki override deny: [library.books.delete]
    │       └── Hapus dari daftar: [... - library.books.delete]
    │
    └── 4. Final Permission Set
            ├── [library.books.view, library.books.create, library.members.view, library.loans.approve]
            └── (library.books.delete telah dicabut)
```

### 15.4 Aturan User Override

| Aturan | Keterangan |
| :--- | :--- |
| **Override Bersifat Tambahan** | Override GRANT hanya menambah, tidak mengganti role |
| **Deny Mengalahkan Grant** | Jika user memiliki deny dan grant untuk permission yang sama → deny menang |
| **Role Tetap Basis** | Role adalah sumber permission utama — override hanya untuk pengecualian |
| **Audit Wajib** | Setiap override harus dicatat dengan alasan dan pemberi akses |
| **Override Sementara** | Dapat memiliki masa berlaku (opsional) |
| **Super Admin Tidak Terpengaruh** | Super Admin bypass semua override |

---

## 16. Permission Registration

### 16.1 Alur Registrasi Permission

```text
Saat Module diaktifkan
    │
    ├── 1. Module Engine membaca module.json
    │
    ├── 2. Deteksi file Permission/permission.php
    │       ├── Ada → Baca definisi permission
    │       └── Tidak ada → Skip (module tanpa permission)
    │
    ├── 3. Parsing & Validasi
    │       ├── Validasi format JSON/PHP array
    │       ├── Validasi naming convention
    │       ├── Cek duplikasi dalam modul
    │       └── Cek duplikasi dengan modul lain
    │
    ├── 4. Sinkronisasi ke Database
    │       ├── Permission baru → INSERT
    │       ├── Permission berubah → UPDATE
    │       ├── Permission tidak ada di file baru → JANGAN HAPUS (data safety)
    │       └── Gunakan transaction untuk atomicity
    │
    ├── 5. Assign ke Default Role
    │       ├── Baca mapping dari module.json
    │       ├── Contoh: "default_roles": { "pustakawan": ["library.*"] }
    │       └── Insert ke role_permission
    │
    ├── 6. Update Cache
    │       ├── Clear cache permissions
    │       └── Rebuild cache
    │
    ├── 7. Log Activity
    │       └── "Module {name}: {count} permissions registered"
    │
    └── 8. Dispatch Event
            └── ModulePermissionsRegistered { module, permissions[] }
```

### 16.2 Saat Module Dinonaktifkan

```text
Module dinonaktifkan
    │
    ├── 1. Permission module tidak dihapus (untuk mencegah orphan data)
    │
    ├── 2. Permission ditandai is_active = false
    │
    ├── 3. Role assignments dipertahankan (untuk reaktivasi)
    │
    ├── 4. Cache di-clear
    │
    └── 5. Log: "Module {name}: permissions disabled"
```

### 16.3 Saat Module Diaktifkan Kembali

```text
Module diaktifkan kembali
    │
    ├── 1. Permission di-set is_active = true
    │
    ├── 2. Role assignments otomatis aktif kembali
    │
    ├── 3. Cache di-clear & rebuild
    │
    └── 4. Log: "Module {name}: permissions re-activated"
```

### 16.4 Permission Sync Command

Permission Engine menyediakan perintah untuk sinkronisasi manual:

```text
Command: permission:sync
    │
    ├── 1. Scan semua module aktif
    ├── 2. Baca permission.php dari setiap modul
    ├── 3. Sinkronisasi ke database
    │       ├── Tambah permission baru
    │       ├── Update permission yang berubah
    │       └── Laporkan permission yang tidak terpakai (warning)
    ├── 4. Update cache
    └── 5. Tampilkan laporan sinkronisasi
```

---

## 17. Permission Cache

### 17.1 Mengapa Cache Diperlukan

Permission dicek **pada setiap request**. Tanpa cache, setiap request akan melakukan query ke database untuk mengambil role dan permission user. Cache sangat penting untuk performa.

### 17.2 Cache Strategy

```text
Cache Structure
    │
    ├── 🔑 Key: "permissions:user:{user_id}"
    │       └── Value: array of permission names (e.g., ["library.books.view", ...])
    │
    ├── 🔑 Key: "permissions:role:{role_id}"
    │       └── Value: array of permission names
    │
    ├── 🔑 Key: "permissions:all"
    │       └── Value: array of all registered permissions
    │
    └── 🔑 Key: "permissions:module:{module_slug}"
            └── Value: array of permissions for this module
```

### 17.3 Cache TTL & Refresh

| Cache | TTL | Refresh Trigger |
| :--- | :--- | :--- |
| `permissions:user:{id}` | 3600 detik (1 jam) | Role user berubah, permission berubah |
| `permissions:role:{id}` | 3600 detik (1 jam) | Role permission berubah |
| `permissions:all` | 3600 detik (1 jam) | Module aktif/dinonaktifkan |
| `permissions:module:{slug}` | 3600 detik (1 jam) | Module diupdate |

### 17.4 Auto Refresh

Cache akan otomatis di-refresh saat:

| Event | Aksi |
| :--- | :--- |
| Role ditambahkan/dihapus dari user | Clear cache user tersebut |
| Permission ditambahkan/dihapus dari role | Clear cache semua user dengan role tersebut |
| Module diaktifkan/dinonaktifkan | Clear cache `permissions:all` |
| Permission baru diregistrasi | Clear cache `permissions:all` |
| User override ditambahkan | Clear cache user tersebut |

### 17.5 Manual Refresh

Admin dapat melakukan refresh cache secara manual melalui:

```text
Panel Admin → Permission → Refresh Cache
    │
    └── Clear semua cache permission
        └── Semua user akan mendapatkan permission terbaru di request berikutnya
```

### 17.6 Cache Fallback

Jika cache tidak tersedia (misalnya Redis mati):

```text
Cache tidak tersedia
    │
    ├── 1. Log peringatan: "Cache unavailable, using database"
    ├── 2. Query langsung ke database
    ├── 3. Set cache sementara (file cache) untuk session ini
    └── 4. Coba restore koneksi cache di request berikutnya
```

---

## 18. Permission UI

### 18.1 Dashboard Permission

Panel admin untuk manajemen permission terdiri dari beberapa halaman:

| Halaman | Deskripsi | Akses |
| :--- | :--- | :--- |
| **Role Manager** | Daftar semua role, CRUD role | Super Admin, Admin Sekolah |
| **Permission Matrix** | Matriks role × permission | Super Admin, Admin Sekolah |
| **Assign Permission** | Mengatur permission untuk role | Super Admin, Admin Sekolah |
| **Assign User Role** | Mengatur role untuk user | Super Admin, Admin Sekolah |
| **User Override** | Override permission per user | Super Admin |
| **Audit Permission** | Log perubahan permission | Super Admin |

### 18.2 Role Manager UI

```text
┌─────────────────────────────────────────────────────────────────┐
│  👥 Manajemen Role                              [Tambah Role]  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┬──────────────┬──────────┬──────────────────┐  │
│  │ Role          │ Users        │ Level    │ Aksi             │  │
│  ├──────────────┼──────────────┼──────────┼──────────────────┤  │
│  │ Super Admin   │ 1 user       │ 100      │ [Edit] [Clone]  │  │
│  │ Admin Sekolah │ 3 users      │ 90       │ [Edit] [Clone]  │  │
│  │ Pustakawan    │ 12 users     │ 60       │ [Edit] [Clone]  │  │
│  │ Guru          │ 45 users     │ 50       │ [Edit] [Clone]  │  │
│  │ Siswa         │ 520 users    │ 40       │ [Edit] [Clone]  │  │
│  │ Guest         │ —            │ 10       │ [Edit]          │  │
│  └──────────────┴──────────────┴──────────┴──────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 18.3 Permission Matrix UI

```text
┌─────────────────────────────────────────────────────────────────────────┐
│  📊 Matriks Permission: Role × Permission                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Role: [Pustakawan ▼]                                                    │
│                                                                          │
│  ┌────────────────────────┬────────┬──────────┬────────┬────────┐       │
│  │ Permission             │ View   │ Create   │ Edit   │ Delete │       │
│  ├────────────────────────┼────────┼──────────┼────────┼────────┤       │
│  │ 📚 Perpustakaan        │        │          │        │        │       │
│  │  library.books.*       │   ✅   │    ✅    │   ✅   │   ❌   │       │
│  │  library.members.*     │   ✅   │    ✅    │   ✅   │   ❌   │       │
│  │  library.loans.*       │   ✅   │    ✅    │   ❌   │   ❌   │       │
│  │  library.loans.approve │   ❌   │    —     │   —    │   —    │       │
│  │                        │        │          │        │        │       │
│  │ 📊 Laporan             │        │          │        │        │       │
│  │  library.reports.*     │   ✅   │    ❌    │   —    │   —    │       │
│  │                        │        │          │        │        │       │
│  │ ⚙️ Pengaturan          │        │          │        │        │       │
│  │  library.settings.*    │   ❌   │    —     │   —    │   —    │       │
│  └────────────────────────┴────────┴──────────┴────────┴────────┘       │
│                                                                          │
│  [Simpan Perubahan]                                                      │
└─────────────────────────────────────────────────────────────────────────┘
```

### 18.4 Assign User Role UI

```text
┌─────────────────────────────────────────────────────────────────┐
│  👤 Assign Role ke User                                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  User: [Budi Santoso ▼]                                         │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  Role yang tersedia:                                     │    │
│  │                                                          │    │
│  │  ☑ Pustakawan         ☐ Kepala Perpustakaan             │    │
│  │  ☐ Guru               ☐ Kepala Sekolah                  │    │
│  │  ☐ Operator                                             │    │
│  │                                                          │    │
│  │  Role saat ini: Pustakawan                               │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                  │
│  [Simpan]                                                        │
└─────────────────────────────────────────────────────────────────┘
```

### 18.5 Audit Permission UI

```text
┌─────────────────────────────────────────────────────────────────┐
│  📋 Audit Log Permission                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────┬──────────┬──────────────┬─────────────┬───────────┐  │
│  │ Waktu│ Admin    │ Aksi         │ Detail       │ Status   │  │
│  ├──────┼──────────┼──────────────┼─────────────┼───────────┤  │
│  │ 10:45│ Super    │ Assign Role  │ User: Budi   │ ✅ Success│  │
│  │      │ Admin    │              │ Role:Pustakawan│          │  │
│  │ 10:30│ Admin    │ Grant        │ User: Siti   │ ✅ Success│  │
│  │      │ Sekolah  │ Override     │ Perm:approve │          │  │
│  │ 10:15│ Super    │ Tambah Role  │ Role: Staff  │ ✅ Success│  │
│  │      │ Admin    │              │ Level: 55    │          │  │
│  │ 09:45│ System   │ Module       │ Module: CMS  │ ✅ Success│  │
│  │      │          │ Permission   │ 12 perms reg │          │  │
│  └──────┴──────────┴──────────────┴─────────────┴───────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 19. Security

### 19.1 Prinsip Keamanan Permission Engine

```text
┌─────────────────────────────────────────────────────────────┐
│                 PRINSIP KEAMANAN PERMISSION                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. LEAST PRIVILEGE                                          │
│     Setiap user hanya mendapat permission minimum yang       │
│     diperlukan untuk menjalankan tugasnya.                   │
│                                                              │
│  2. DENY BY DEFAULT                                          │
│     Semua akses ditolak secara default. Permission harus     │
│     diberikan secara eksplisit. Tidak ada akses implisit.    │
│                                                              │
│  3. DEFENSE IN DEPTH                                         │
│     Akses dicek di tiga lapisan: Middleware → Policy →       │
│     Service. Jika satu lapisan lolos, masih ada lapisan      │
│     berikutnya.                                              │
│                                                              │
│  4. AUDIT LOG                                                │
│     Setiap keputusan akses (grant/deny) dan setiap perubahan │
│     permission dicatat untuk audit keamanan.                 │
│                                                              │
│  5. SEPARATION OF DUTY                                       │
│     Role kritis dipisah — pustakawan tidak bisa approve      │
│     peminjaman sendiri, admin tidak bisa approve perubahan   │
│     role sendiri.                                            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 19.2 Least Privilege

| Praktik | Implementasi |
| :--- | :--- |
| **Default deny** | Semua permission tidak dimiliki sampai diberikan eksplisit |
| **Permission granular** | Hingga level aksi (`view`, `create`, `edit`, `delete`) |
| **Role minimal** | Default role hanya memiliki permission esensial |
| **Review berkala** | Admin direkomendasikan review permission setiap 3 bulan |
| **Hapus yang tidak perlu** | Permission yang tidak digunakan harus dihapus |

### 19.3 Deny by Default

```text
Cek permission: library.books.delete
    │
    ├── 1. Apakah user Super Admin?
    │       ├── Ya → ✅ Grant (bypass)
    │       └── Tidak → Lanjut
    │
    ├── 2. Apakah user memiliki permission dari Role?
    │       ├── Ya → Simpan sebagai grant sementara
    │       └── Tidak → ❌ Deny
    │
    ├── 3. Apakah user memiliki User Override Grant?
    │       ├── Ya → Tambahkan ke grant sementara
    │       └── Tidak → Lanjut
    │
    ├── 4. Apakah user memiliki User Override Deny?
    │       ├── Ya → ❌ Deny (override deny menang)
    │       └── Tidak → ✅ Grant (dari langkah 2)
    │
    └── Hasil: ❌ DENY (jika tidak ada grant eksplisit)
```

### 19.4 Audit Log

| Event | Dicatat | Detail |
| :--- | :--- | :--- |
| **Permission Check** | ✅ Opsional | User, permission, result (untuk debug mode) |
| **Role Created** | ✅ Wajib | Admin, role name, permissions |
| **Role Updated** | ✅ Wajib | Admin, role, changes |
| **Role Deleted** | ✅ Wajib | Admin, role name |
| **Permission Assigned** | ✅ Wajib | Admin, role, permission |
| **Permission Revoked** | ✅ Wajib | Admin, role, permission |
| **User Role Assigned** | ✅ Wajib | Admin, user, role |
| **User Role Revoked** | ✅ Wajib | Admin, user, role |
| **User Override Added** | ✅ Wajib | Admin, user, permission, type |
| **Access Denied** | ✅ Wajib | User, permission, URL, timestamp |

### 19.5 Permission Validation

Permission Engine melakukan validasi pada setiap operasi:

| Validasi | Saat | Aksi |
| :--- | :--- | :--- |
| **Permission exists** | Assign/Pengecekan | Cek apakah permission terdaftar di database |
| **Module active** | Pengecekan | Cek apakah module yang memiliki permission masih aktif |
| **Format valid** | Registrasi | Cek format `{module}.{resource}.{action}` |
| **No orphan** | Hapus role | Cek apakah masih ada user dengan role tersebut |
| **No self-escalate** | Assign role | Cegah admin menaikkan role sendiri di luar wewenang |

### 19.6 Policy Validation

Validasi khusus untuk Policy:

| Validasi | Skenario | Aksi |
| :--- | :--- | :--- |
| **Ownership** | Edit resource | Cek apakah user adalah pemilik resource |
| **Status** | Approve/reject | Cek apakah resource dalam status yang tepat |
| **Duplicate** | Create resource | Cek duplikasi data |
| **Self-action** | Approve own loan | Cegah approve peminjaman sendiri |
| **Hierarchy** | Manage user | Cek apakah user target berada di bawah hierarki admin |

---

## 20. Future Features

### 20.1 Fitur Masa Depan

Permission Engine direncanakan untuk mendukung fitur-fitur berikut di masa depan:

| Fitur | Deskripsi | Prioritas |
| :--- | :--- | :--- |
| **Permission Template** | Template permission untuk jenis sekolah berbeda | 🟡 Medium |
| **Role Template** | Template role yang bisa di-clone untuk sekolah baru | 🟡 Medium |
| **Delegated Access** | User bisa mendelegasikan akses ke user lain (sementara) | 🔴 High |
| **Temporary Permission** | Permission dengan masa berlaku (start — end date) | 🔴 High |
| **Time Based Permission** | Permission hanya aktif di jam tertentu (08:00-16:00) | 🟢 Low |
| **Department Based** | Permission berdasarkan departemen (IPA, IPS, Bahasa) | 🟡 Medium |
| **Multi School** | Satu instalasi CosmicLib untuk multiple sekolah | 🔴 High |
| **Permission Approval** | Perubahan permission memerlukan approval atasan | 🟡 Medium |
| **Permission Analytics** | Laporan permission usage — permission mana yang paling sering digunakan | 🟢 Low |
| **Auto Permission Suggestion** | Sistem menyarankan permission berdasarkan pola penggunaan | 🟢 Low |

### 20.2 Detail Fitur Future

#### 20.2.1 Permission Template

```text
Sekolah baru dibuat
    │
    ├── 1. Pilih template permission
    │       ├── SMA Negeri
    │       ├── SMK
    │       ├── Madrasah (MI/MTS/MA)
    │       ├── SD Sederajat
    │       └── Custom (dari awal)
    │
    ├── 2. Template menentukan:
    │       ├── Role apa saja yang tersedia
    │       ├── Permission default untuk setiap role
    │       └── Hirarki role
    │
    └── 3. Admin sekolah dapat menyesuaikan setelah template diterapkan
```

#### 20.2.2 Delegated Access

```text
User A (Kepala Sekolah) mendelegasikan ke User B (Wakasek)
    │
    ├── 1. Pilih user tujuan (User B)
    ├── 2. Pilih permission yang didelegasikan
    │       ├── library.reports.view
    │       ├── library.reports.export
    │       └── report.finance.view
    │
    ├── 3. Tentukan masa berlaku
    │       ├── 1 minggu
    │       ├── 1 bulan
    │       └── Kustom (start — end)
    │
    └── 4. Delegasi aktif — User B dapat mengakses permission tersebut
```

#### 20.2.3 Temporary Permission

```text
Permission: library.loans.approve
    │
    ├── Start: 2026-07-20 08:00
    ├── End:   2026-07-27 16:00
    │
    └── Status: Aktif (dalam periode) / Expired (di luar periode)
```

#### 20.2.4 Time Based Permission

```text
Permission: library.settings.manage
    │
    ├── Hanya aktif: Senin - Jumat, 08:00 - 16:00
    │
    ├── Di luar jam kerja → Permission otomatis dicabut sementara
    │
    └── Cocok untuk: membatasi akses admin di luar jam sekolah
```

#### 20.2.5 Multi School Permission

```text
Satu instalasi CosmicLib dengan 3 sekolah
    │
    ├── School A (SMA Negeri 1)
    │       ├── Super Admin: — (hanya 1 untuk semua sekolah)
    │       ├── Admin Sekolah: User A
    │       └── Permission: hanya untuk data School A
    │
    ├── School B (SMA Negeri 2)
    │       ├── Admin Sekolah: User B
    │       └── Permission: hanya untuk data School B
    │
    └── School C (SMA Negeri 3)
            ├── Admin Sekolah: User C
            └── Permission: hanya untuk data School C
```

---

## 21. AI Rules

### 21.1 Aturan untuk AI Coding Assistant

Berikut aturan yang **WAJIB** dipatuhi oleh AI saat mengembangkan atau memodifikasi Permission Engine:

| Aturan | Penjelasan | Sanksi |
| :--- | :--- | :--- |
| **Wajib Gunakan Permission Engine** | AI harus selalu menggunakan sistem permission — jangan buat sistem sendiri | Kode ditolak review |
| **Jangan Hardcode Role** | AI tidak boleh hardcode role di controller, view, atau service | Kode ditolak review |
| **Jangan Hardcode Permission** | AI tidak boleh hardcode permission string di luar konfigurasi | Kode ditolak review |
| **Buat permission.php** | Setiap module baru WAJIB memiliki file `permission.php` | Kode ditolak review |
| **Gunakan Policy** | Untuk operasi berbasis model, AI wajib menggunakan Policy | Kode ditolak review |
| **Gunakan Middleware** | Untuk proteksi route, AI wajib menggunakan Middleware | Kode ditolak review |
| **Format Penamaan** | Permission WAJIB mengikuti format `{module}.{resource}.{action}` | Kode ditolak review |
| **Ikuti Standar** | Permission display name dalam Bahasa Indonesia | Kode ditolak review |
| **Jangan Bypass** | AI tidak boleh membuat bypass permission (kecuali super admin) | Security issue |
| **Audit Log** | AI wajib menambahkan audit log untuk perubahan permission | Kualitas ditolak |

### 21.2 Aturan Khusus untuk Implementasi

```text
✅ BOLEH:
    - Menggunakan `Auth::user()->can('permission.name')`
    - Menggunakan middleware `can:permission.name`
    - Menggunakan `@can('permission.name')` di Blade
    - Membuat Policy class untuk model
    - Membuat permission.php di module baru
    - Menggunakan Gate::define untuk permission dinamis
    - Menggunakan cache permission

❌ TIDAK BOLEH:
    - Mengecek role secara hardcode: `if ($user->role === 'admin')`
    - Mengecek permission dengan string hardcode di controller
    - Membypass permission check untuk user tertentu
    - Meletakkan logika permission di Blade
    - Membuat query manual untuk cek permission
    - Mengabaikan Policy untuk model yang memerlukannya
```

---

## 22. Best Practice

### 22.1 RBAC Implementation

| Praktik | Deskripsi |
| :--- | :--- |
| **Role-based, not user-based** | Atur permission di role, bukan per user (kecuali override) |
| **Minimal roles** | Jangan membuat terlalu banyak role — gunakan permission granular |
| **Role naming** | Gunakan nama yang jelas: `pustakawan`, bukan `staff_perpus_1` |
| **Hierarchy** | Gunakan level untuk hierarki role |
| **Default role untuk user baru** | User baru mendapat role default (bisa dikonfigurasi) |

### 22.2 Least Privilege

| Praktik | Deskripsi |
| :--- | :--- |
| **Start from zero** | Role default tanpa permission — tambahkan sesuai kebutuhan |
| **Permission granular** | Jangan gunakan `manage` jika cukup dengan `view` |
| **Review berkala** | Audit permission setiap 3 bulan |
| **Hapus orphan permission** | Permission yang tidak digunakan oleh role mana pun |
| **Batasi super admin** | Super admin hanya untuk emergency — gunakan admin sekolah |

### 22.3 Policy First

| Praktik | Deskripsi |
| :--- | :--- |
| **Policy untuk model** | Setiap model dengan akses berbasis kepemilikan WAJIB punya Policy |
| **Policy methods** | Implementasikan method yang diperlukan saja |
| **Policy + Permission** | Policy melengkapi permission, bukan menggantikan |
| **Policy caching** | Hindari query berat di policy — gunakan cache |

### 22.4 Middleware

| Praktik | Deskripsi |
| :--- | :--- |
| **Route protection** | Setiap route yang sensitif WAJIB memiliki middleware |
| **Group middleware** | Gunakan route group untuk middleware yang sama |
| **Middleware naming** | Konsisten dengan permission naming |
| **Middleware order** | `auth` → `can:permission` → controller |

### 22.5 Cache Permission

| Praktik | Deskripsi |
| :--- | :--- |
| **Cache semua permission** | Jangan query database setiap request |
| **Warm cache** | Cache di-load saat pertama kali user login |
| **Auto refresh** | Cache otomatis refresh saat permission berubah |
| **Fallback** | Jika cache mati, gunakan database query |

### 22.6 Audit Log

| Praktik | Deskripsi |
| :--- | :--- |
| **Log semua perubahan** | Setiap perubahan role/permission wajib di-log |
| **Log access denied** | Catat akses yang ditolak (untuk deteksi serangan) |
| **Retensi log** | Simpan audit log minimal 1 tahun |
| **Log immutable** | Audit log tidak bisa diubah atau dihapus |

### 22.7 Permission Naming Convention Summary

```text
Format Baku:    {module}.{resource}.{action}
Contoh:         library.books.view
                system.users.manage
                cms.posts.publish

Aturan:
├── Huruf kecil semua
├── Pisah dengan titik (.)
├── Module prefix WAJIB
├── Resource di tengah
├── Action di akhir
└── Maksimal 3 segment
```

---

## 23. Architecture Diagram

### 23.1 Alur Request Lengkap dengan Permission

```text
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                        PERMISSION ENGINE ARCHITECTURE                                 │
├─────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                      │
│   REQUEST Masuk                                                                     │
│       │                                                                            │
│       ▼                                                                            │
│   ┌──────────────────────────────────────────────────────────────────────────────┐ │
│   │                        MIDDLEWARE STACK                                      │ │
│   │                                                                              │ │
│   │  1. auth          → Pastikan user login                                     │ │
│   │  2. can:permission → Cek permission user terhadap route ini                 │ │
│   │  3. can:policy    → Cek policy untuk model spesifik                        │ │
│   │  4. role          → Cek role user (opsional)                               │ │
│   │                                                                              │ │
│   └──────────────────────────────────────────────────────────────────────────────┘ │
│       │                                                                            │
│       ▼                                                                            │
│   ┌──────────────────────────────────────────────────────────────────────────────┐ │
│   │                        PERMISSION CHECK                                      │ │
│   │                                                                              │ │
│   │  ┌────────────────────────────────────────────────────────────────────────┐  │ │
│   │  │  Permission Cache                                                      │  │ │
│   │  │                                                                        │  │ │
│   │  │  1. Cek cache: permissions:user:{id}                                   │  │ │
│   │  │     ├── Ada → Gunakan cache                                            │  │ │
│   │  │     └── Tidak ada → Query database + set cache                         │  │ │
│   │  │                                                                        │  │ │
│   │  │  2. Cek Super Admin bypass                                             │  │ │
│   │  │     ├── Super Admin → ✅ GRANT                                         │  │ │
│   │  │     └── Bukan → Lanjut cek                                             │  │ │
│   │  │                                                                        │  │ │
│   │  │  3. Cek Role Permission                                                │  │ │
│   │  │     ├── User punya permission via role?                                │  │ │
│   │  │     ├── Ya → Simpan sebagai grant sementara                           │  │ │
│   │  │     └── Tidak → ❌ DENY                                                │  │ │
│   │  │                                                                        │  │ │
│   │  │  4. Cek User Override                                                  │  │ │
│   │  │     ├── Override Grant → Tambah grant                                  │  │ │
│   │  │     └── Override Deny  → ❌ DENY                                       │  │ │
│   │  │                                                                        │  │ │
│   │  │  5. Decision → ✅ GRANT / ❌ DENY                                      │  │ │
│   │  └────────────────────────────────────────────────────────────────────────┘  │ │
│   │                                                                              │ │
│   └──────────────────────────────────────────────────────────────────────────────┘ │
│       │                                                                            │
│       ▼                                                                            │
│   ┌──────────────────────────────────────────────────────────────────────────────┐ │
│   │                        POLICY CHECK (jika ada)                               │ │
│   │                                                                              │ │
│   │  1. Apakah ada Policy untuk model ini?                                      │ │
│   │     ├── Ada → Panggil method policy                                        │ │
│   │     └── Tidak → Skip                                                        │ │
│   │                                                                              │ │
│   │  2. Policy Method                                                           │ │
│   │     ├── Contoh: BookPolicy::update($user, $book)                            │ │
│   │     ├── true  → ✅ GRANT                                                    │ │
│   │     └── false → ❌ DENY                                                     │ │
│   │                                                                              │ │
│   └──────────────────────────────────────────────────────────────────────────────┘ │
│       │                                                                            │
│       ▼                                                                            │
│   ┌──────────────────────────────────────────────────────────────────────────────┐ │
│   │                        CONTROLLER → SERVICE → RESPONSE                        │ │
│   │                                                                              │ │
│   │  Controller tipis → Panggil Service → Service akses Repository              │ │
│   │  → Kembalikan response atau tampilkan view                                  │ │
│   │                                                                              │ │
│   └──────────────────────────────────────────────────────────────────────────────┘ │
│       │                                                                            │
│       ▼                                                                            │
│   ┌──────────────────────────────────────────────────────────────────────────────┐ │
│   │                        AUDIT LOG                                             │ │
│   │                                                                              │ │
│   │  Catat: user, action, resource, timestamp, status (grant/deny)             │ │
│   │                                                                              │ │
│   └──────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                      │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

### 23.2 Dependency Diagram

```text
                    ┌──────────────────┐
                    │   Module Engine   │ ← Menyediakan definisi permission per modul
                    └────────┬─────────┘
                             │
                             ▼
┌──────────┐    ┌──────────────────────┐    ┌─────────────┐
│   Menu   │◄───│                      │───►│   Theme     │
│  Engine  │    │   PERMISSION ENGINE  │    │   Engine    │
└──────────┘    │                      │    └─────────────┘
                │  ┌────────────────┐  │
┌──────────┐    │  │ RBAC Core      │  │    ┌─────────────┐
│  Widget  │◄───│  │ Policy System  │  │───►│  Setting    │
│  Engine  │    │  │ Middleware      │  │    │   Engine    │
└──────────┘    │  │ Cache          │  │    └─────────────┘
                │  │ Permission UI  │  │
┌──────────┐    │  └────────────────┘  │    ┌─────────────┐
│  Module  │───►│                      │◄───│    Core     │
│  Engine  │    └──────────────────────┘    │   Engine    │
└──────────┘                                └─────────────┘
```

---

## 24. Permission Matrix

### 24.1 Matriks Role × Module × Resource × Action

Berikut adalah contoh matriks permission untuk CosmicLib:

#### Library Module

| Resource | Action | Super Admin | Admin Sekolah | Kepala Sekolah | Kepala Perpus | Pustakawan | Guru | Siswa | Operator |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **books** | view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| | create | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ |
| | edit | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| | delete | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | restore | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | import | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| | export | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **members** | view | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ |
| | create | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ |
| | edit | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| | delete | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **loans** | view | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| | create | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| | approve | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | reject | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | return | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **reports** | view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| | export | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **settings** | view | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | manage | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |

#### System Module

| Resource | Action | Super Admin | Admin Sekolah | Kepala Sekolah | Kepala Perpus | Pustakawan | Guru | Siswa | Operator |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **users** | view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | create | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | edit | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | delete | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **roles** | view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | create | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | edit | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | delete | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **settings** | view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | manage | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **backup** | create | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | restore | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | download | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **update** | view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | install | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

#### Theme Module

| Resource | Action | Super Admin | Admin Sekolah | Kepala Sekolah | Kepala Perpus | Pustakawan | Guru | Siswa | Operator |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **theme** | view | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| | activate | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | customize | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | css.edit | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | js.edit | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | logo.upload | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | favicon.upload | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | import | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| | export | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

#### Menu Permissions

| Menu | Super Admin | Admin Sekolah | Kepala Sekolah | Kepala Perpus | Pustakawan | Guru | Siswa | Operator |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Perpustakaan | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Buku | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Anggota | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Peminjaman | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Laporan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| CMS | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Pengaturan | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manajemen User | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Tema | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Backup | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 25. Checklist

### 25.1 Checklist Verifikasi Permission Engine

#### ✅ Role System

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Default roles terdefinisi (9 role) | ✅ Wajib | Super Admin s/d Guest |
| Role memiliki level/urutan | ✅ Wajib | Level 10 — 100 |
| Role dapat dibuat kustom | ✅ Wajib | Admin bisa tambah role baru |
| Role dapat di-clone | ✅ Wajib | Duplikasi role yang sudah ada |
| Role system tidak bisa dihapus | ✅ Wajib | Super admin, admin sekolah, guest |

#### ✅ Permission System

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Format penamaan konsisten | ✅ Wajib | `{module}.{resource}.{action}` |
| Permission granular | ✅ Wajib | Hingga level aksi |
| Action standar terdefinisi | ✅ Wajib | 18 action (view, create, edit, dll) |
| Permission categories | ✅ Wajib | 10 kategori |
| Permission calculator (manage) | ✅ Wajib | manage = view+create+edit+delete+configure |

#### ✅ Module Integration

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Setiap module punya `permission.php` | ✅ Wajib | File definisi permission |
| Auto-registrasi saat module aktif | ✅ Wajib | Baca file + sinkronisasi DB |
| Permission tidak dihapus saat deaktivasi | ✅ Wajib | Hanya di-disable |
| Permission sync command | ✅ Wajib | `permission:sync` |

#### ✅ Policy & Middleware

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Laravel Policy untuk model | ✅ Wajib | Otorisasi berbasis konteks |
| Middleware permission | ✅ Wajib | `can:permission` |
| Middleware role | ✅ Wajib | `role:slug` |
| Middleware multiple permission | ✅ Wajib | ALL dan ANY |

#### ✅ User Override

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Grant override | ✅ Wajib | Tambah permission di luar role |
| Deny override | ✅ Wajib | Cabut permission dari role |
| Deny mengalahkan grant | ✅ Wajib | Deny priority |
| Audit override | ✅ Wajib | Semua override tercatat |

#### ✅ Cache

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Cache permission user | ✅ Wajib | Key: `permissions:user:{id}` |
| Cache permission role | ✅ Wajib | Key: `permissions:role:{id}` |
| Auto refresh | ✅ Wajib | Saat role/permission berubah |
| Fallback tanpa cache | ✅ Wajib | Query database langsung |
| Manual refresh | ✅ Wajib | Tombol refresh di panel |

#### ✅ Audit Log

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Log perubahan role | ✅ Wajib | Create, update, delete |
| Log assign permission | ✅ Wajib | Grant/revoke permission |
| Log assign user role | ✅ Wajib | Assign/revoke user dari role |
| Log access denied | ✅ Wajib | User, permission, URL |
| Log immutable | ✅ Wajib | Tidak bisa diubah/dihapus |

#### ✅ Security

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Least privilege | ✅ Wajib | Permission minimum |
| Deny by default | ✅ Wajib | Tidak ada akses implisit |
| Defense in depth | ✅ Wajib | Middleware + Policy + Service |
| Super Admin bypass | ✅ Wajib | Bypass semua pengecekan |
| Separation of duty | ✅ Wajib | Pisah role kritis |
| No self-escalate | ✅ Wajib | Cegah escalation sendiri |

#### ✅ UI Admin

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Role Manager | ✅ Wajib | CRUD role |
| Permission Matrix | ✅ Wajib | Matriks role × permission |
| Assign Permission | ✅ Wajib | Atur permission per role |
| Assign User Role | ✅ Wajib | Atur role per user |
| User Override | ✅ Wajib | Override permission user |
| Audit Permission | ✅ Wajib | Log perubahan |

#### ✅ Permission Matrix Lengkap

| Item | Status | Catatan |
| :--- | :--- | :--- |
| Role × Module × Resource × Action | ✅ Wajib | Matriks lengkap |
| Setiap role punya definisi akses | ✅ Wajib | Per resource & action |
| Menu permissions | ✅ Wajib | Visibilitas menu per role |
| Widget permissions | ✅ Wajib | Visibilitas widget per role |

---

## Referensi

| Dokumen | Hubungan |
| :--- | :--- |
| [07_CORE_ENGINE.md](07_CORE_ENGINE.md) | Core Engine — Gate registration, Event System, Service Container |
| [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md) | Module Engine — permission.php dalam struktur modul |
| [09_THEME_ENGINE.md](09_THEME_ENGINE.md) | Theme Engine — permission untuk manajemen tema |
| [11_MENU_ENGINE.md](11_MENU_ENGINE.md) | Menu Engine — menu permission untuk visibilitas |
| [12_WIDGET_ENGINE.md](12_WIDGET_ENGINE.md) | Widget Engine — widget permission untuk visibilitas dashboard |
| [16_SETTING_ENGINE.md](16_SETTING_ENGINE.md) | Setting Engine — menyimpan konfigurasi permission |
| [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md) | Security Guideline — standar keamanan CosmicLib |
| [23_CODING_STANDARD.md](23_CODING_STANDARD.md) | Coding Standard — PSR-12, SOLID, Service Layer Pattern |

## Catatan

- Dokumen ini adalah **spesifikasi resmi Permission Engine CosmicLib**.
- Semua modul **WAJIB** menggunakan Permission Engine — dilarang membuat sistem otorisasi sendiri.
- **Dilarang keras** hardcode role atau permission di kode.
- Gunakan format penamaan permission `{module}.{resource}.{action}` secara konsisten.
- Super Admin adalah satu-satunya role yang **bypass** semua pengecekan permission.
- Untuk panduan keamanan lebih lanjut, lihat [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md).