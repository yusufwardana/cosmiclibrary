# ðŸŒŒ 32 — Library Module

## Deskripsi

Dokumen ini merancang spesifikasi lengkap **Library Module** (Modul Perpustakaan) — modul utama produk pertama CosmicLib Engine yang mencakup manajemen anggota, katalogisasi buku, transaksi sirkulasi, dan pengelolaan denda.

## Tujuan

Mendokumentasikan seluruh fitur, alur bisnis, dan spesifikasi teknis modul perpustakaan SMA yang menjadi produk inti CosmicLib Library.

## Ruang Lingkup

- Manajemen anggota (siswa, guru, pustakawan)
- Katalogisasi buku dan klasifikasi DDC
- Manajemen eksemplar/salinan buku
- Transaksi sirkulasi (pinjam, kembali, perpanjangan)
- Pengelolaan denda keterlambatan
- Reservasi buku
- Laporan dan statistik perpustakaan

---

## 🗂️ Table of Contents

1. [Manajemen Anggota](#manajemen-anggota)
2. [Katalogisasi Buku](#katalogisasi-buku)
3. [Manajemen Eksemplar](#manajemen-eksemplar)
4. [Transaksi Sirkulasi](#transaksi-sirkulasi)
5. [Pengelolaan Denda](#pengelolaan-denda)
6. [Reservasi Buku](#reservasi-buku)
7. [Laporan & Statistik](#laporan--statistik)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Manajemen Anggota

*Placeholder:*
- Pendaftaran anggota baru (siswa, guru) dengan data kelas, NIS/NIP, dan foto
- Pencetakan kartu anggota ber-barcode/QR code
- Sinkronisasi data siswa per tahun ajaran
- Status keanggotaan aktif/non-aktif/lulus

### Katalogisasi Buku

*Placeholder:*
- Input data buku manual dan via pencarian ISBN API
- Klasifikasi buku berdasarkan sistem Dewey Decimal Classification (DDC)
- Upload cover buku (thumbnail otomatis via Media Engine)
- Pencarian buku multi-field (judul, pengarang, ISBN, kategori)

### Manajemen Eksemplar

*Placeholder:*
- Setiap judul buku dapat memiliki banyak eksemplar/salinan
- Pelabelan barcode per eksemplar
- Status eksemplar: tersedia, dipinjam, rusak, hilang, dimusnahkan
- Inventaris fisik dan stockopname

### Transaksi Sirkulasi

*Placeholder:*
- **Peminjaman**: Scan barcode anggota + barcode buku, validasi batas pinjam
- **Pengembalian**: Scan barcode, kalkulasi otomatis keterlambatan
- **Perpanjangan**: Mandiri atau via pustakawan, dengan batas perpanjangan
- **Aturan sirkulasi** disimpan di Setting Engine (batas pinjam, durasi, perpanjangan)

### Pengelolaan Denda

*Placeholder:*
- Kalkulasi otomatis denda berdasarkan jumlah hari keterlambatan
- Tarif denda per hari dikonfigurasi via Setting Engine
- Status pembayaran denda (lunas, belum lunas, dibebaskan)
- Laporan denda terkumpul per periode

### Reservasi Buku

*Placeholder:*
- Anggota dapat mereservasi buku yang sedang dipinjam
- Notifikasi otomatis saat buku tersedia
- Masa berlaku reservasi terbatas (misal: 3 hari)
- Antrian reservasi jika banyak peminat

### Laporan & Statistik

*Placeholder:*
- Buku terpopuler (most borrowed)
- Statistik peminjaman per kelas/jurusan
- Laporan denda terkumpul per bulan
- Statistik kunjungan harian
- Ekspor laporan ke PDF dan Excel

---

## Referensi

- [06_DATABASE_DESIGN.md](06_DATABASE_DESIGN.md)
- [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md)
- [24_DATABASE_SCHEMA.md](24_DATABASE_SCHEMA.md)
- [15_NOTIFICATION_ENGINE.md](15_NOTIFICATION_ENGINE.md)

## Catatan

- Modul ini adalah modul utama dan wajib aktif pada setiap instalasi CosmicLib Library.
- Aturan bisnis sirkulasi (batas pinjam, durasi, denda) harus fleksibel via Setting Engine.
- Setiap sekolah mungkin memiliki aturan berbeda — hindari hardcode nilai apapun.
