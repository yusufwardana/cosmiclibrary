# 🌌 20 — License Engine

## Deskripsi

Dokumen ini merancang **License Engine** — sistem lisensi dan aktivasi yang mengelola validasi lisensi CosmicLib Engine, registrasi modul/plugin premium, dan pembatasan akses berdasarkan tipe lisensi.

## Tujuan

Menyediakan mekanisme lisensi yang aman dan fleksibel untuk mendukung distribusi CosmicLib Engine baik sebagai produk open source maupun versi komersial dengan fitur-fitur premium.

## Ruang Lingkup

- Validasi kunci lisensi (license key)
- Registrasi dan aktivasi instalasi
- Pembatasan fitur berdasarkan tipe lisensi
- Manajemen lisensi modul dan plugin
- Mekanisme pengecekan lisensi offline

---

## 🗂️ Table of Contents

1. [Arsitektur Sistem Lisensi](#arsitektur-sistem-lisensi)
2. [Validasi Kunci Lisensi](#validasi-kunci-lisensi)
3. [Tipe Lisensi](#tipe-lisensi)
4. [Aktivasi & Deaktivasi](#aktivasi--deaktivasi)
5. [Lisensi Modul & Plugin](#lisensi-modul--plugin)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Arsitektur Sistem Lisensi

*Placeholder: Sistem lisensi berbasis kunci unik yang dikaitkan dengan domain/instalasi. Validasi dilakukan saat instalasi dan secara berkala. Mendukung mode offline untuk sekolah dengan koneksi internet terbatas.*

### Validasi Kunci Lisensi

*Placeholder: Format kunci lisensi, algoritma validasi, dan mekanisme pengecekan terhadap server lisensi pusat. Mendukung grace period jika server lisensi tidak dapat dijangkau.*

### Tipe Lisensi

| Tipe | Fitur | Target |
|:---|:---|:---|
| Community | Core engine + modul dasar | Gratis untuk semua sekolah |
| Professional | + modul lanjutan + support | Berbayar per instalasi |
| Enterprise | + custom branding + multi-site | Berbayar per organisasi |

### Aktivasi & Deaktivasi

*Placeholder: Alur aktivasi lisensi baru, transfer lisensi antar server, dan deaktivasi lisensi saat migrasi server.*

### Lisensi Modul & Plugin

*Placeholder: Mekanisme lisensi terpisah untuk modul dan plugin premium. Setiap modul/plugin dapat memiliki kunci lisensi independen yang divalidasi oleh License Engine.*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md)
- [13_PLUGIN_ENGINE.md](13_PLUGIN_ENGINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)

## Catatan

- Versi Community harus tetap fungsional tanpa kunci lisensi (open source).
- Sistem lisensi tidak boleh menghalangi penggunaan dasar perpustakaan oleh sekolah.
- Validasi lisensi harus mendukung mode offline untuk daerah dengan akses internet terbatas.
