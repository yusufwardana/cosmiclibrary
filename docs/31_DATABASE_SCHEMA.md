# ðŸŒŒ 31 — Database Schema

## Deskripsi

Dokumen ini mendokumentasikan skema database detail untuk CosmicLib Engine, termasuk Entity Relationship Diagram (ERD), definisi tabel, relasi antar-entitas, dan strategi indeks untuk optimasi performa.

## Tujuan

Menyediakan referensi tunggal dan otoritatif untuk seluruh struktur database yang akan digunakan oleh CosmicLib Engine dan modul-modulnya.

## Ruang Lingkup

- ERD (Entity Relationship Diagram) keseluruhan sistem
- Definisi tabel core engine (users, roles, permissions, settings, menus)
- Definisi tabel modul perpustakaan (books, members, borrow_records, fines)
- Relasi foreign key antar-tabel
- Strategi indeks untuk query optimization
- Konvensi penamaan kolom dan tabel

---

## 🗂️ Table of Contents

1. [ERD Overview](#erd-overview)
2. [Tabel Core Engine](#tabel-core-engine)
3. [Tabel Modul Perpustakaan](#tabel-modul-perpustakaan)
4. [Relasi Foreign Key](#relasi-foreign-key)
5. [Strategi Indeks](#strategi-indeks)
6. [Konvensi Penamaan Database](#konvensi-penamaan-database)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan. Lihat juga `blueprint/database_schema.sql` untuk detail SQL.

---

## ⚙️ Kerangka Sistem

### ERD Overview

*Placeholder: Diagram ERD yang menggambarkan hubungan antar-entitas utama. Akan dilengkapi dengan diagram Mermaid atau gambar PNG setelah skema final disepakati.*

### Tabel Core Engine

*Placeholder: Daftar tabel inti:*

| Tabel | Deskripsi | Primary Key |
|:---|:---|:---|
| `users` | Data pengguna sistem | `bigint unsigned AI` |
| `roles` | Definisi peran pengguna | `bigint unsigned AI` |
| `permissions` | Definisi hak akses | `bigint unsigned AI` |
| `role_permission` | Pivot role ↔ permission | composite |
| `user_role` | Pivot user ↔ role | composite |
| `settings` | Konfigurasi key-value | `bigint unsigned AI` |
| `menus` | Item menu navigasi | `bigint unsigned AI` |
| `notifications` | Log notifikasi pengguna | `uuid` |
| `modules` | Daftar modul terdaftar | `bigint unsigned AI` |
| `audit_logs` | Log audit aktivitas | `bigint unsigned AI` |

### Tabel Modul Perpustakaan

*Placeholder: Daftar tabel modul Library:*

| Tabel | Deskripsi | Primary Key |
|:---|:---|:---|
| `books` | Katalog buku | `bigint unsigned AI` |
| `book_categories` | Kategori/klasifikasi DDC | `bigint unsigned AI` |
| `book_copies` | Eksemplar/salinan buku | `bigint unsigned AI` |
| `members` | Data anggota perpustakaan | `bigint unsigned AI` |
| `borrow_records` | Transaksi peminjaman | `bigint unsigned AI` |
| `fines` | Data denda keterlambatan | `bigint unsigned AI` |
| `reservations` | Reservasi buku | `bigint unsigned AI` |

### Relasi Foreign Key

*Placeholder: Peta relasi foreign key antar-tabel — setiap FK harus dideklarasikan secara eksplisit di migration dengan `onDelete('cascade')` atau `onDelete('restrict')` sesuai konteks bisnis.*

### Strategi Indeks

*Placeholder: Indeks yang direkomendasikan untuk tabel dengan volume tinggi:*
- `borrow_records`: index pada `member_id`, `book_copy_id`, `status`, `due_date`
- `books`: index pada `isbn`, `category_id`, `title`
- `members`: index pada `user_id`, `member_number`
- `fines`: index pada `borrow_record_id`, `status`

### Konvensi Penamaan Database

*Placeholder:*
- Nama tabel: `snake_case`, plural (`books`, `borrow_records`)
- Nama kolom: `snake_case` (`created_at`, `book_title`)
- Foreign key: `{tabel_singular}_id` (`user_id`, `book_id`)
- Primary key: `id` (`bigint unsigned AUTO_INCREMENT`)
- Pivot table: alphabetical order (`role_permission`, bukan `permission_role`)

---

## Referensi

- [06_DATABASE_DESIGN.md](06_DATABASE_DESIGN.md)
- [blueprint/database_schema.sql](../blueprint/database_schema.sql)
- [25_LIBRARY_MODULE.md](25_LIBRARY_MODULE.md)

## Catatan

- Semua primary key menggunakan `bigint unsigned AUTO_INCREMENT` kecuali dinyatakan lain.
- Gunakan soft deletes (`deleted_at`) untuk data yang mungkin perlu dipulihkan.
- Setiap tabel wajib memiliki `created_at` dan `updated_at` (Laravel timestamps).
