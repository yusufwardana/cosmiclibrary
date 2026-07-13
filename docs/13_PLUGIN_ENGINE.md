# 🌌 13_PLUGIN_ENGINE.md

## 🎯 Tujuan (Goal)
Dokumen ini mendesain Plugin Engine yang menyediakan titik-temu kait (Action Hooks and Filter Hooks) mirip dengan pola WordPress untuk memperluas fungsionalitas core CosmicLib tanpa memodifikasi file inti.

---

## 🗂️ Table of Contents
1. [Arsitektur Hook & Event](#arsitektur-hook--event)
2. [Action Hooks (Aksi)](#action-hooks-aksi)
3. [Filter Hooks (Penyaringan Data)](#filter-hooks-penyaringan-data)
4. [Manajemen Aktivitas Plugin](#manajemen-aktivitas-plugin)

---

## ⚙️ Placeholder & Kerangka Sistem

### Arsitektur Hook & Event
*Menjelaskan integrasi dengan sistem Event dispatcher milik Laravel 12 kustom untuk mendaftarkan dan memicu callback.*

### Action Hooks (Aksi)
*Placeholder: Contoh penempatan hook aksi seperti `action_after_book_borrowed` untuk memicu notifikasi otomatis via WhatsApp.*

### Filter Hooks (Penyaringan Data)
*Placeholder: Contoh penempatan kait penyaring data seperti `filter_fine_amount` untuk mengubah nominal denda berdasarkan diskon hari libur nasional.*

### Manajemen Aktivitas Plugin
*Placeholder: Antarmuka pengaktifan plugin terisolasi pada folder `/plugins`.*
