# 🌌 19 — Update Engine

## Deskripsi

Dokumen ini mendesain **Update Engine** — sistem pembaruan otomatis yang memungkinkan administrator perpustakaan sekolah menarik dan menerapkan pembaruan versi CosmicLib secara otomatis dari repositori resmi via ZIP extraction.

## Tujuan

Mempermudah proses pembaruan CosmicLib Library di lingkungan shared hosting tanpa memerlukan akses Git, Composer, atau terminal SSH.

## Ruang Lingkup

- Mekanisme pengecekan versi terbaru via API
- Alur unduh dan ekstraksi paket pembaruan ZIP
- Migrasi database otomatis pasca-pembaruan
- Sistem rollback otomatis jika pembaruan gagal
- Notifikasi ketersediaan update baru

---

## 🗂️ Table of Contents

1. [Mekanisme Cek Versi Terbaru](#mekanisme-cek-versi-terbaru)
2. [Alur Unduh & Ekstraksi ZIP](#alur-unduh--ekstraksi-zip)
3. [Migrasi Database Pasca-Pembaruan](#migrasi-database-pasca-pembaruan)
4. [Penanganan Rollback Otomatis (Fail-Safe Rollback)](#penanganan-rollback-otomatis-fail-safe-rollback)
5. [Notifikasi Update](#notifikasi-update)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Mekanisme Cek Versi Terbaru

*Sistem memicu request API berkala ke server rilis resmi CosmicLib untuk membandingkan nomor versi lokal dengan versi rilis stabil terbaru. Pengecekan dapat dilakukan secara otomatis (harian) atau manual dari panel admin.*

### Alur Unduh & Ekstraksi ZIP

*Placeholder: Unduh paket pembaruan dalam format ZIP ke folder penyimpanan sementara `/storage/app/updates`, validasi checksum integritas file, dan proses ekstraksi aman menimpa file usang.*

### Migrasi Database Pasca-Pembaruan

*Placeholder: Penjalanan otomatis migrasi database via program internal setelah file baru berhasil diekstrak. Mendukung migrasi inkremental tanpa kehilangan data pengguna.*

### Penanganan Rollback Otomatis (Fail-Safe Rollback)

*Placeholder: Pembuatan salinan cadangan file dan database sebelum diperbarui. Jika terjadi kegagalan, sistem akan otomatis memulihkan kondisi sebelum update dimulai.*

### Notifikasi Update

*Placeholder: Badge notifikasi di dashboard admin ketika versi baru tersedia. Menampilkan changelog singkat dan tombol "Perbarui Sekarang".*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [18_BACKUP_ENGINE.md](18_BACKUP_ENGINE.md)
- [20_LICENSE_ENGINE.md](20_LICENSE_ENGINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)

## Catatan

- Proses update harus membuat backup otomatis sebelum menerapkan pembaruan.
- File konfigurasi pengguna (`.env`, setelan khusus) tidak boleh ditimpa saat update.
- Update harus kompatibel dengan shared hosting tanpa akses terminal.
