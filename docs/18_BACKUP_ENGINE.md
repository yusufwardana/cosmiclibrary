# 🌌 18_BACKUP_RESTORE.md

## 🎯 Tujuan (Goal)
Dokumen ini mendesain modul Pencadangan dan Pemulihan (Backup & Restore) yang dapat mengekspor seluruh database MySQL dan file media ke dalam satu arsip ZIP, serta memulihkannya jika terjadi kehilangan data di server sekolah.

---

## 🗂️ Table of Contents
1. [Strategi Pencadangan (Backup Strategy)](#strategi-pencadangan-backup-strategy)
2. [Ekspor Database ke SQL Dump](#ekspor-database-ke-sql-dump)
3. [Pengarsipan Berkas Media ke ZIP](#pengarsipan-berkas-media-ke-zip)
4. [Alur Pemulihan Data (Restore Wizard)](#alur-pemulihan-data-restore-wizard)

---

## ⚙️ Placeholder & Kerangka Sistem

### Strategi Pencadangan (Backup Strategy)
*Pilihan pencadangan manual lewat klik tombol di panel admin atau pencadangan terjadwal mingguan menggunakan Cron Job.*

### Ekspor Database ke SQL Dump
*Placeholder: Kode utilitas pembantu untuk mengekstraksi seluruh skema tabel dan baris data MySQL ke dalam file `.sql` menggunakan pustaka PHP murni (tanpa ketergantungan `mysqldump` eksternal).*

### Pengarsipan Berkas Media ke ZIP
*Placeholder: Pengemasan direktori `/public/storage/uploads` dan file SQL dump ke dalam satu nama berkas unik.*

### Alur Pemulihan Data (Restore Wizard)
*Placeholder: Pustaka pengunggah berkas zip backup kustom, melakukan dekompresi, dan menulis ulang baris database secara aman.*
