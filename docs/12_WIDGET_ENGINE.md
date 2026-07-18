# 🌌 12 — Widget Engine

## Deskripsi

Dokumen ini merancang **Widget Engine** — sistem widget dinamis yang memungkinkan modul dan plugin meregistrasikan blok konten interaktif (statistik, grafik, shortcut) di dashboard dan halaman lainnya.

## Tujuan

Menyediakan mekanisme widget yang fleksibel sehingga dashboard admin dan halaman lainnya dapat dikustomisasi dengan berbagai blok informasi sesuai kebutuhan pengguna.

## Ruang Lingkup

- Arsitektur widget (definisi, registrasi, rendering)
- Widget bawaan (statistik, grafik, daftar)
- Kustomisasi dashboard (drag-and-drop)
- Widget per modul
- Permission-aware widgets

---

## 🗂️ Table of Contents

1. [Arsitektur Widget](#arsitektur-widget)
2. [Widget Bawaan](#widget-bawaan)
3. [Registrasi Widget oleh Modul](#registrasi-widget-oleh-modul)
4. [Kustomisasi Dashboard](#kustomisasi-dashboard)
5. [Permission-Aware Widgets](#permission-aware-widgets)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Arsitektur Widget

*Placeholder: Setiap widget adalah class yang mengimplementasikan `WidgetInterface` dengan metode `render()`, `getData()`, dan `getConfig()`. Widget diregistrasikan ke Widget Engine dan dirender di area yang ditentukan.*

### Widget Bawaan

*Placeholder: Widget default yang disertakan:*
- **Statistik Ringkas**: Total buku, anggota, peminjaman aktif, denda tertunggak
- **Grafik Peminjaman**: Chart peminjaman per bulan
- **Buku Terpopuler**: Daftar buku paling sering dipinjam
- **Peminjaman Jatuh Tempo**: Daftar peminjaman mendekati/melewati deadline
- **Aktivitas Terbaru**: Log aktivitas terkini

### Registrasi Widget oleh Modul

*Placeholder: Modul meregistrasikan widget via service provider. Widget Engine mengumpulkan semua widget yang terdaftar dan menampilkannya di dashboard berdasarkan konfigurasi user.*

### Kustomisasi Dashboard

*Placeholder: Admin dapat mengatur layout dashboard — memilih widget yang ditampilkan, mengatur urutan dan ukuran. Konfigurasi disimpan per-user di database.*

### Permission-Aware Widgets

*Placeholder: Setiap widget memiliki permission requirement. Widget hanya ditampilkan jika user memiliki permission yang sesuai. Misalnya, widget statistik keuangan hanya untuk Admin.*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md)
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md)
- [09_THEME_ENGINE.md](09_THEME_ENGINE.md)

## Catatan

- Widget harus ringan — data di-cache untuk menghindari query berat di setiap page load.
- Widget harus responsif dan terlihat baik di layar mobile maupun desktop.
- Desain widget mengikuti panduan di [26_UI_GUIDELINE.md](26_UI_GUIDELINE.md).
