# 🌌 15 — Notification Engine

## Deskripsi

Dokumen ini merancang **Notification Engine** — sistem notifikasi terpusat yang mengelola pengiriman pemberitahuan kepada pengguna melalui berbagai saluran komunikasi (in-app, email, WhatsApp gateway).

## Tujuan

Menyediakan infrastruktur notifikasi yang fleksibel dan extensible untuk mengirimkan pengingat, peringatan, dan informasi penting kepada anggota perpustakaan (siswa, guru, pustakawan) secara otomatis dan terjadwal.

## Ruang Lingkup

- Notifikasi in-app (bell notification di dashboard)
- Notifikasi email menggunakan SMTP/Mailgun
- Integrasi WhatsApp gateway untuk pengingat keterlambatan
- Sistem template notifikasi yang dapat dikustomisasi
- Penjadwalan notifikasi berbasis event dan waktu
- Riwayat dan log notifikasi terkirim

---

## 🗂️ Table of Contents

1. [Arsitektur Notification Channel](#arsitektur-notification-channel)
2. [In-App Notification](#in-app-notification)
3. [Email Notification](#email-notification)
4. [WhatsApp Gateway Integration](#whatsapp-gateway-integration)
5. [Template Notifikasi](#template-notifikasi)
6. [Penjadwalan & Event Triggers](#penjadwalan--event-triggers)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Arsitektur Notification Channel

*Placeholder: Menggunakan Laravel Notification System sebagai fondasi. Setiap notifikasi dikirim melalui satu atau beberapa channel (database, mail, whatsapp) secara bersamaan. Channel dapat ditambahkan melalui Plugin Engine.*

### In-App Notification

*Placeholder: Bell icon di navbar dashboard dengan badge counter. Notifikasi disimpan di tabel `notifications` dan ditampilkan sebagai dropdown list. Mendukung status read/unread dan mark-all-as-read.*

### Email Notification

*Placeholder: Pengiriman email menggunakan Laravel Mail dengan konfigurasi SMTP dari Setting Engine. Template email menggunakan Blade dan Markdown mail. Mendukung antrian (queue) untuk menghindari timeout.*

### WhatsApp Gateway Integration

*Placeholder: Integrasi opsional dengan layanan WhatsApp Business API atau gateway pihak ketiga (Fonnte, Wablas, dll). Konfigurasi API key dan nomor pengirim disimpan di Setting Engine.*

### Template Notifikasi

*Placeholder: Sistem template notifikasi yang mendukung variabel dinamis (nama siswa, judul buku, tanggal jatuh tempo). Admin dapat mengedit template melalui panel admin tanpa mengubah kode.*

### Penjadwalan & Event Triggers

*Placeholder: Notifikasi dipicu oleh event (buku dipinjam, buku jatuh tempo, denda aktif) atau dijadwalkan secara berkala (pengingat H-1, H-3 sebelum jatuh tempo).*

---

## Referensi

- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [16_SETTING_ENGINE.md](16_SETTING_ENGINE.md)
- [25_LIBRARY_MODULE.md](25_LIBRARY_MODULE.md)

## Catatan

- Integrasi WhatsApp bersifat opsional dan membutuhkan konfigurasi API terpisah.
- Semua notifikasi harus menggunakan Bahasa Indonesia yang santun dan ramah.
- Queue notification harus kompatibel dengan shared hosting (database driver).
