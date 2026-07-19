# ðŸŒŒ 30 — UI Guideline

## Deskripsi

Dokumen ini menyusun panduan desain antarmuka pengguna (UI/UX Guideline) untuk CosmicLib Engine, mencakup palet warna, tipografi, komponen UI, layout, dan prinsip aksesibilitas.

## Tujuan

Menjamin konsistensi visual dan pengalaman pengguna yang optimal di seluruh halaman dan modul CosmicLib Library, sehingga antarmuka terasa profesional dan mudah digunakan oleh pustakawan sekolah maupun siswa.

## Ruang Lingkup

- Palet warna dan tema visual
- Sistem tipografi
- Komponen UI standar (button, form, card, table, modal)
- Layout dan grid system
- Ikon dan ilustrasi
- Prinsip responsivitas dan aksesibilitas
- Panduan penggunaan Bahasa Indonesia di UI

---

## 🗂️ Table of Contents

1. [Palet Warna](#palet-warna)
2. [Tipografi](#tipografi)
3. [Komponen UI Standar](#komponen-ui-standar)
4. [Layout & Grid System](#layout--grid-system)
5. [Ikon & Ilustrasi](#ikon--ilustrasi)
6. [Responsivitas & Aksesibilitas](#responsivitas--aksesibilitas)
7. [Panduan Bahasa UI](#panduan-bahasa-ui)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan.

---

## ⚙️ Kerangka Sistem

### Palet Warna

*Placeholder: Warna utama ditentukan oleh Theme Engine dan dapat dikustomisasi per sekolah. Default palette:*

| Fungsi | Warna | Hex |
|:---|:---|:---|
| Primary | Biru Kosmik | `#3B82F6` |
| Secondary | Teal Aksen | `#14B8A6` |
| Success | Hijau | `#22C55E` |
| Warning | Kuning Amber | `#F59E0B` |
| Danger | Merah | `#EF4444` |
| Background | Putih Bersih | `#FFFFFF` |
| Text | Abu Gelap | `#1F2937` |

### Tipografi

*Placeholder:*
- **Heading**: Inter atau Outfit (bold, clean, modern)
- **Body Text**: Inter (regular, 14-16px)
- **Kode/Sistem**: JetBrains Mono (monospace)
- **Ukuran minimum**: 14px untuk body text (keterbacaan pustakawan)

### Komponen UI Standar

*Placeholder: Daftar komponen yang harus konsisten di seluruh modul:*
- Button (primary, secondary, outline, danger, disabled)
- Form Input (text, select, textarea, date picker, file upload)
- Card (info card, stat card, action card)
- Table (sortable, searchable, pagination)
- Modal (confirm, form, detail view)
- Alert/Toast (success, error, warning, info)
- Breadcrumb dan navigation

### Layout & Grid System

*Placeholder: Menggunakan grid system Bootstrap 5 (12 kolom). Layout utama: sidebar navigation + main content area. Sidebar collapsible untuk layar kecil.*

### Ikon & Ilustrasi

*Placeholder: Menggunakan icon library Bootstrap Icons atau Heroicons. Ikon harus konsisten dalam ukuran dan style (outline atau filled, jangan campur).*

### Responsivitas & Aksesibilitas

*Placeholder:*
- Breakpoint responsive: mobile (< 768px), tablet (768-1024px), desktop (> 1024px)
- Kontras warna memenuhi standar WCAG 2.1 AA
- Label untuk semua form input
- Keyboard navigation support
- Font size yang ramah untuk pengguna lanjut usia

### Panduan Bahasa UI

*Placeholder: Semua teks UI dalam Bahasa Indonesia yang:*
- Formal namun ramah (bukan kaku birokratis)
- Menghindari istilah teknis (gunakan "Simpan" bukan "Submit")
- Pesan error yang jelas dan membantu (bukan "Error 500")
- Contoh: "Data buku berhasil disimpan" bukan "Book data saved successfully"

---

## Referensi

- [09_THEME_ENGINE.md](09_THEME_ENGINE.md)
- [11_MENU_ENGINE.md](11_MENU_ENGINE.md)
- [12_WIDGET_ENGINE.md](12_WIDGET_ENGINE.md)
- [23_CODING_STANDARD.md](23_CODING_STANDARD.md)

## Catatan

- Semua warna harus berasal dari Theme Engine — dilarang hardcode warna di CSS/Blade.
- Desain harus tetap berfungsi dan rapi di layar 1024px (laptop yang umum di sekolah).
- Prioritaskan kemudahan penggunaan di atas keindahan — pengguna utama adalah pustakawan sekolah.
