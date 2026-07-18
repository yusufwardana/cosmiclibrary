# 🌌 13 — Plugin Engine

## Deskripsi

Dokumen ini merancang **Plugin Engine** — sistem ekstensi pihak ketiga yang memungkinkan pengembang komunitas menambahkan fungsionalitas baru ke CosmicLib tanpa memodifikasi core engine atau modul bawaan.

## Tujuan

Membuka ekosistem CosmicLib untuk kontribusi komunitas melalui sistem plugin yang aman, terisolasi, dan mudah dikelola.

## Ruang Lingkup

- Perbedaan plugin vs modul
- Struktur standar plugin
- Hook system (action dan filter)
- Instalasi dan manajemen plugin
- Keamanan dan sandboxing plugin
- Plugin marketplace (roadmap)

---

## 🗂️ Table of Contents

1. [Plugin vs Modul](#plugin-vs-modul)
2. [Struktur Standar Plugin](#struktur-standar-plugin)
3. [Hook System](#hook-system)
4. [Instalasi & Manajemen](#instalasi--manajemen)
5. [Keamanan Plugin](#keamanan-plugin)
6. [Plugin Marketplace](#plugin-marketplace)

---

## Status

`🟡 Blueprint` — Dokumen dalam tahap perancangan arsitektur.

---

## ⚙️ Kerangka Sistem

### Plugin vs Modul

*Placeholder:*

| Aspek | Modul | Plugin |
|:---|:---|:---|
| Scope | Fitur utama, besar | Ekstensi kecil, spesifik |
| Pengembang | Tim inti | Komunitas/pihak ketiga |
| Akses DB | Bisa membuat tabel sendiri | Terbatas, gunakan hook |
| Contoh | Library Module | WhatsApp Gateway, ISBN Lookup |

### Struktur Standar Plugin

*Placeholder:*
```text
plugins/{plugin-name}/
├── plugin.json            ← Manifest plugin
├── {PluginName}Plugin.php ← Entry point
├── src/
└── resources/
```

### Hook System

*Placeholder: Plugin Engine menyediakan action hooks dan filter hooks yang terinspirasi dari pola WordPress namun diimplementasikan menggunakan Laravel Event System:*
- **Action Hook**: `do_action('before_borrow', $data)` — menjalankan callback
- **Filter Hook**: `apply_filter('borrow_limit', $limit)` — memodifikasi nilai

### Instalasi & Manajemen

*Placeholder: Plugin diinstal dengan mengupload ZIP file dari panel admin. Plugin Engine mengekstrak, memvalidasi manifest, dan meregistrasikan plugin. Admin dapat mengaktifkan, menonaktifkan, dan menghapus plugin.*

### Keamanan Plugin

*Placeholder: Plugin berjalan dalam konteks yang terbatas — tidak boleh mengakses file system sembarangan, tidak boleh memodifikasi tabel core, dan harus menggunakan API yang disediakan oleh engine.*

### Plugin Marketplace

*Placeholder: Roadmap fitur marketplace untuk browsing, rating, dan instalasi plugin dari repositori komunitas (Fase 5+).*

---

## Referensi

- [08_MODULE_ENGINE.md](08_MODULE_ENGINE.md)
- [07_CORE_ENGINE.md](07_CORE_ENGINE.md)
- [20_LICENSE_ENGINE.md](20_LICENSE_ENGINE.md)
- [22_SECURITY_GUIDELINE.md](22_SECURITY_GUIDELINE.md)

## Catatan

- Plugin tidak boleh memodifikasi core engine atau modul bawaan secara langsung.
- Plugin yang menggunakan hook harus defensive — tidak crash jika hook dihapus.
- Review keamanan diperlukan sebelum plugin masuk ke marketplace resmi.
