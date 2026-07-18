# 🌌 23 — Coding Standard

## Deskripsi

Dokumen ini menetapkan standar penulisan kode untuk seluruh pengembangan CosmicLib Engine, mencakup konvensi penamaan, format kode, prinsip desain, dan praktik terbaik Laravel.

## Tujuan

Menjamin konsistensi, keterbacaan, dan maintainability kode di seluruh modul dan plugin CosmicLib Engine, sehingga setiap kontributor menghasilkan kode dengan kualitas yang seragam.

## Ruang Lingkup

- Standar PSR-12 (Extended Coding Style)
- Prinsip SOLID
- Konvensi penamaan (kelas, metode, variabel, tabel, route)
- Laravel coding conventions
- Dokumentasi kode (DocBlock)
- Kebijakan bahasa (UI Indonesia, kode Inggris)

---

## 🗂️ Table of Contents

1. [PSR-12 Extended Coding Style](#psr-12-extended-coding-style)
2. [Prinsip SOLID](#prinsip-solid)
3. [Konvensi Penamaan](#konvensi-penamaan)
4. [Laravel Conventions](#laravel-conventions)
5. [Dokumentasi Kode (DocBlock)](#dokumentasi-kode-docblock)
6. [Kebijakan Bahasa](#kebijakan-bahasa)
7. [Anti-Pattern yang Dilarang](#anti-pattern-yang-dilarang)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### PSR-12 Extended Coding Style

*Placeholder: Aturan formatting PHP mengikuti PSR-12:*
- Indent 4 spasi (bukan tab)
- Baris maksimal 120 karakter
- Opening brace `{` di baris baru untuk kelas dan metode
- Satu pernyataan per baris
- `declare(strict_types=1)` di setiap file service

### Prinsip SOLID

*Placeholder:*
- **S**ingle Responsibility: Setiap kelas hanya memiliki satu alasan untuk berubah.
- **O**pen/Closed: Terbuka untuk ekstensi, tertutup untuk modifikasi.
- **L**iskov Substitution: Subclass harus dapat menggantikan parent class.
- **I**nterface Segregation: Interface kecil dan spesifik, bukan satu interface besar.
- **D**ependency Inversion: Bergantung pada abstraksi, bukan implementasi konkret.

### Konvensi Penamaan

| Elemen | Format | Contoh |
|:---|:---|:---|
| Kelas & Controller | PascalCase | `BookCirculationService` |
| Metode & Variabel | camelCase | `borrowBook()`, `$memberId` |
| Tabel Database | snake_case (plural) | `borrow_records` |
| Kolom Database | snake_case | `return_date` |
| Route Name | kebab-case + dot | `admin.books.index` |
| Config Key | snake_case + dot | `cosmiclib.module.enabled` |
| Constant | UPPER_SNAKE_CASE | `MAX_BORROW_LIMIT` |

### Laravel Conventions

*Placeholder:*
- Controller: Thin controller, fat service — logika bisnis di Service layer.
- Model: Definisikan `$fillable`, relationships, scopes, dan accessors.
- Migration: Satu migrasi per perubahan skema, penamaan deskriptif.
- Form Request: Validasi di FormRequest class, bukan di controller.
- Resource: Gunakan API Resource untuk transformasi response.

### Dokumentasi Kode (DocBlock)

*Placeholder: Setiap kelas dan metode publik wajib memiliki PHPDoc:*
```php
/**
 * Memproses transaksi peminjaman buku oleh anggota.
 *
 * @param int   $memberId  ID anggota peminjam
 * @param array $bookIds   Daftar ID buku yang dipinjam
 * @return BorrowRecord    Record peminjaman yang dibuat
 * @throws CirculationException Jika batas pinjaman terlampaui
 */
```

### Kebijakan Bahasa

- **User Interface (UI)**: Wajib Bahasa Indonesia — label, pesan, notifikasi, help text.
- **Source Code**: Wajib Bahasa Inggris — kelas, variabel, metode, tabel, kolom, route.
- **Komentar Kode**: Boleh Bahasa Indonesia atau Inggris, konsisten per file.
- **Dokumentasi**: Bahasa Indonesia untuk docs publik, Bahasa Inggris untuk inline code docs.

### Anti-Pattern yang Dilarang

*Placeholder:*
- ❌ Hardcode role, permission, menu, atau warna
- ❌ Query database di dalam view/blade
- ❌ Logika bisnis di dalam controller
- ❌ Raw SQL query tanpa parameter binding
- ❌ `dd()` atau `dump()` di production code
- ❌ Menggunakan `env()` di luar file config

---

## Referensi

- [PROJECT_MANIFEST.md](../PROJECT_MANIFEST.md)
- [AGENTS.md](../AGENTS.md)
- [21_API_GUIDELINE.md](21_API_GUIDELINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)

## Catatan

- Gunakan tool linter (PHP CS Fixer / Laravel Pint) untuk otomatisasi format kode.
- Code review wajib memeriksa kepatuhan terhadap standar ini sebelum merge.
- Standar ini berlaku untuk semua kontributor termasuk AI coding assistant.
