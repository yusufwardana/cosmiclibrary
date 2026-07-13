# 🌌 06_DATABASE_DESIGN.md

## 🎯 Tujuan (Goal)
Dokumen ini merancang skema basis data relasional (MySQL) untuk CosmicLib, mencakup struktur tabel sistem inti, hubungan antartabel (ERD), dan optimasi kueri menggunakan indeks.

---

## 🗂️ Table of Contents
1. [Skema Tabel Core (Sistem Inti)](#skema-tabel-core-sistem-inti)
2. [Skema Tabel Modul Perpustakaan](#skema-tabel-modul-perpustakaan)
3. [Relasi Antartabel (ERD Blueprint)](#relasi-antartabel-erd-blueprint)
4. [Optimasi Kueri & Indeks](#optimasi-kueri--indeks)

---

## ⚙️ Placeholder & Kerangka Sistem

### Skema Tabel Core (Sistem Inti)
*Tabel pengguna (`users`), peran (`roles`), hak akses (`permissions`), setelan sistem (`settings`), dan registrasi menu (`menus`).*

### Skema Tabel Modul Perpustakaan
*Placeholder: Skema tabel anggota (`members`), buku (`books`), eksemplar (`book_items`), transaksi sirkulasi (`borrow_records`), dan denda (`fines`).*

### Relasi Antartabel (ERD Blueprint)
*Placeholder: Gambaran relasi satu-ke-banyak (One-to-Many) antara buku dengan eksemplar, serta eksemplar dengan rekam peminjaman.*

### Optimasi Kueri & Indeks
*Placeholder: Penempatan indeks pada kolom pencarian cepat seperti `isbn`, `barcode_id`, `member_number`, dan status peminjaman.*
