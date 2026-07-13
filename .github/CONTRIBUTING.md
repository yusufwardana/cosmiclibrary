# Panduan Kontribusi (Contributing Guide)

Terima kasih telah tertarik untuk berkontribusi pada **CosmicLib Engine**! Proyek ini dirancang untuk menjadi dasar Sistem Informasi Perpustakaan SMA yang tangguh berbasis Laravel 12.

## Alur Kontribusi (Workflow)

1. **Fork & Clone**: Lakukan fork pada repository ini dan clone ke mesin lokal Anda.
2. **Buat Branch Baru**: Gunakan penamaan branch yang deskriptif:
   - `feature/nama-fitur` untuk fitur baru.
   - `bugfix/nama-bug` untuk perbaikan bug.
   - `docs/perubahan-dokumen` untuk pembaruan dokumentasi.
3. **Commit Messages**: Ikuti standar [Conventional Commits](https://www.conventionalcommits.org/):
   - `feat: tambah modul sirkulasi`
   - `fix: perbaiki kalkulasi denda`
   - `docs: perbarui panduan instalasi`
4. **Push & Pull Request**: Push branch Anda ke remote repository Anda, lalu buka Pull Request (PR) ke branch `main` di repository utama.

## Standar Kode (Coding Standards)

- **Framework**: Laravel 12 (mengikuti standar PSR-12).
- **Arsitektur**: Modular & Service-oriented.
- **Frontend**: Blade, Bootstrap 5, dan Vite.
- **Bahasa**: Bahasa Indonesia untuk antarmuka pengguna (UI), bahasa Inggris untuk penamaan variabel, kelas, dan database schema agar tetap konsisten dengan standar global.

## Melaporkan Masalah (Reporting Issues)

Gunakan halaman GitHub Issues untuk melaporkan bug atau meminta fitur baru. Sediakan informasi selengkap mungkin termasuk langkah-langkah reproduksi (steps to reproduce) jika melaporkan bug.
