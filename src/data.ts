/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { FileNode, ProjectStat } from './types';

export const PROJECT_STATS: ProjectStat[] = [
  {
    label: 'Target Framework',
    value: 'Laravel 12.x',
    icon: 'Cpu',
    description: 'PHP >= 8.2, Modular Architecture'
  },
  {
    label: 'Bahasa UI',
    value: 'Indonesia',
    icon: 'Globe',
    description: 'Bahasa formal dan ramah pengguna'
  },
  {
    label: 'Basis Data',
    value: 'MySQL 8.0+',
    icon: 'Database',
    description: 'Kompatibel penuh cPanel & MariaDB'
  },
  {
    label: 'Hosting Target',
    value: 'cPanel Shared',
    icon: 'Server',
    description: 'Optimasi resource rendah & hemat RAM'
  }
];

export const DOCK_FILES_INDEX = [
  { path: 'docs/00_SYSTEM_PROMPT.md', title: '00. System Prompt', purpose: 'Panduan instruksi utama untuk kecerdasan buatan (AI).' },
  { path: 'docs/01_PROJECT_OVERVIEW.md', title: '01. Project Overview', purpose: 'Latar belakang tantangan dan solusi perpustakaan sekolah.' },
  { path: 'docs/02_VISION.md', title: '02. Vision & values', purpose: 'Visi jangka panjang, pilar desain, dan standar sukses.' },
  { path: 'docs/03_ARCHITECTURE.md', title: '03. Architecture', purpose: 'Arsitektur modular monolith dan aliran request.' },
  { path: 'docs/04_TECH_STACK.md', title: '04. Tech Stack', purpose: 'Spesifikasi detail server, basis data, dan frontend.' },
  { path: 'docs/05_FOLDER_STRUCTURE.md', title: '05. Folder Structure', purpose: 'Layout folder Laravel 12 dan pemetaan sub-modul.' },
  { path: 'docs/06_DATABASE_DESIGN.md', title: '06. Database Design', purpose: 'Skema tabel relasional (MySQL) dan rancangan indeks.' },
  { path: 'docs/07_CORE_ENGINE.md', title: '07. Core Engine', purpose: 'Siklus hidup inisialisasi awal dan logging terpusat.' },
  { path: 'docs/08_MODULE_ENGINE.md', title: '08. Module Engine', purpose: 'Sistem deteksi, pemuatan, dan isolasi modul dinamis.' },
  { path: 'docs/09_THEME_ENGINE.md', title: '09. Theme Engine', purpose: 'Penyajian tata letak Blade kustom dan kompilasi Vite.' },
  { path: 'docs/10_PERMISSION_ENGINE.md', title: '10. Permission Engine', purpose: 'Sistem otorisasi peran pengguna (RBAC) & Middleware.' },
  { path: 'docs/11_MENU_ENGINE.md', title: '11. Menu Engine', purpose: 'Registrasi navigasi dinamis berdasar hak akses.' },
  { path: 'docs/12_WIDGET_ENGINE.md', title: '12. Widget Engine', purpose: 'Kerangka dasbor kustom yang fleksibel (asinkron).' },
  { path: 'docs/13_PLUGIN_ENGINE.md', title: '13. Plugin Engine', purpose: 'Action hooks & filters penunjang modifikasi tanpa merusak core.' },
  { path: 'docs/14_MEDIA_ENGINE.md', title: '14. Media Engine', purpose: 'Manajemen file, optimasi gambar, dan pembersihan berkas.' },
  { path: 'docs/15_SYSTEM_SETTING.md', title: '15. System Setting', purpose: 'Penyimpanan konfigurasi key-value terenkripsi & cache.' },
  { path: 'docs/16_INSTALLER.md', title: '16. Installer', purpose: 'Skrip pembuat tabel dan akun admin visual via web wizard.' },
  { path: 'docs/17_SYSTEM_UPDATE.md', title: '17. System Update', purpose: 'Penarik pembaharuan sistem secara otonom lewat zip extraction.' },
  { path: 'docs/18_BACKUP_RESTORE.md', title: '18. Backup & Restore', purpose: 'Modul ekspor database sql dump dan kompresi file media.' },
  { path: 'docs/19_DEPLOYMENT.md', title: '19. Deployment', purpose: 'Langkah migrasi berkas ke public_html cPanel & cron.' },
  { path: 'docs/20_ROADMAP.md', title: '20. Roadmap & Milestones', purpose: 'Prioritas backlog fitur dan kriteria rilis stabil.' }
];

export const FILE_TREE_DATA: FileNode = {
  name: 'CosmicLib Workspace',
  path: '.',
  type: 'directory',
  children: [
    {
      name: '.github',
      path: '.github',
      type: 'directory',
      children: [
        {
          name: 'ISSUE_TEMPLATE',
          path: '.github/ISSUE_TEMPLATE',
          type: 'directory',
          children: [
            { name: 'bug_report.md', path: '.github/ISSUE_TEMPLATE/bug_report.md', type: 'file' },
            { name: 'feature_request.md', path: '.github/ISSUE_TEMPLATE/feature_request.md', type: 'file' }
          ]
        },
        {
          name: 'workflows',
          path: '.github/workflows',
          type: 'directory',
          children: [
            { name: 'ci.yml', path: '.github/workflows/ci.yml', type: 'file' }
          ]
        },
        { name: 'PULL_REQUEST_TEMPLATE.md', path: '.github/PULL_REQUEST_TEMPLATE.md', type: 'file' },
        { name: 'CONTRIBUTING.md', path: '.github/CONTRIBUTING.md', type: 'file' },
        { name: 'CODE_OF_CONDUCT.md', path: '.github/CODE_OF_CONDUCT.md', type: 'file' },
        { name: 'SECURITY.md', path: '.github/SECURITY.md', type: 'file' }
      ]
    },
    {
      name: 'blueprint',
      path: 'blueprint',
      type: 'directory',
      children: [
        { name: 'database_schema.sql', path: 'blueprint/database_schema.sql', type: 'file' },
        { name: 'README.md', path: 'blueprint/README.md', type: 'file' }
      ]
    },
    {
      name: 'docs',
      path: 'docs',
      type: 'directory',
      children: DOCK_FILES_INDEX.map(doc => ({
        name: doc.path.split('/').pop() || doc.title,
        path: doc.path,
        type: 'file',
        isDoc: true
      }))
    },
    {
      name: 'prompts',
      path: 'prompts',
      type: 'directory',
      children: [
        {
          name: 'claude',
          path: 'prompts/claude',
          type: 'directory',
          children: [
            { name: 'README.md', path: 'prompts/claude/README.md', type: 'file' }
          ]
        },
        {
          name: 'codex',
          path: 'prompts/codex',
          type: 'directory',
          children: [
            { name: 'README.md', path: 'prompts/codex/README.md', type: 'file' }
          ]
        },
        {
          name: 'cline',
          path: 'prompts/cline',
          type: 'directory',
          children: [
            { name: 'README.md', path: 'prompts/cline/README.md', type: 'file' }
          ]
        },
        {
          name: 'ai-studio',
          path: 'prompts/ai-studio',
          type: 'directory',
          children: [
            { name: 'README.md', path: 'prompts/ai-studio/README.md', type: 'file' }
          ]
        }
      ]
    },
    {
      name: 'examples',
      path: 'examples',
      type: 'directory',
      children: [
        { name: 'module_example.json', path: 'examples/module_example.json', type: 'file' }
      ]
    },
    {
      name: 'assets',
      path: 'assets',
      type: 'directory',
      children: [
        { name: 'README.md', path: 'assets/README.md', type: 'file' }
      ]
    },
    {
      name: 'scripts',
      path: 'scripts',
      type: 'directory',
      children: [
        { name: 'README.md', path: 'scripts/README.md', type: 'file' }
      ]
    },
    {
      name: 'tests',
      path: 'tests',
      type: 'directory',
      children: [
        { name: 'README.md', path: 'tests/README.md', type: 'file' }
      ]
    },
    { name: 'README.md', path: 'README.md', type: 'file' },
    { name: 'PROJECT_MANIFEST.md', path: 'PROJECT_MANIFEST.md', type: 'file' },
    { name: 'CHANGELOG.md', path: 'CHANGELOG.md', type: 'file' },
    { name: 'LICENSE', path: 'LICENSE', type: 'file' },
    { name: 'ROADMAP.md', path: 'ROADMAP.md', type: 'file' },
    { name: 'CLAUDE.md', path: 'CLAUDE.md', type: 'file' },
    { name: 'AGENTS.md', path: 'AGENTS.md', type: 'file' },
    { name: 'CODEX.md', path: 'CODEX.md', type: 'file' },
    { name: 'AI_STUDIO.md', path: 'AI_STUDIO.md', type: 'file' },
    { name: '.clinerules', path: '.clinerules', type: 'file' },
    { name: '.gitignore', path: '.gitignore', type: 'file' }
  ]
};

// Full File Contents Map to avoid server roundtrips
export const STATIC_FILE_CONTENTS: Record<string, string> = {
  'README.md': `# 🌌 CosmicLib Engine

\`\`\`text
   ______                      _      _       _      ______                 _              
  / _____)                    (_)    | |     | |    |  ____)               (_)             
 | /       ___   ___ ____ ___  _  ___| |      | |   | |___  ____   ____ _ ___  ____  _____ 
 | |      / _ \ /___)    _ _ \\| |/ ___) |     | |   |  ___)|  _ \\ / _  | |  _ \\|  _ \\| ___ |
 | \\_____( |_| |___ | | | | | | | (___| |_____| |___| |____| | | ( (_| | | | | | | | | ____)
  \\______)___/(___/|_|_|_|_|_|_|_|____)_______)_____|______)_| |_|\\___ |_|_| |_|_| |_|_____)
                                                                 (_____|                   
\`\`\`

> **CosmicLib Engine** adalah sebuah *core-engine* modular yang dirancang untuk menjadi pondasi tangguh Sistem Informasi Perpustakaan sekolah tingkat menengah (SMA/SMK) di Indonesia. Produk perdana yang ditenagai oleh engine ini adalah **CosmicLib Library**.

---

## 🌟 Filosofi Desain
CosmicLib dirancang dengan tiga pilar utama:
1. **Modularitas Tanpa Batas (Extensible Modularity)**
2. **Kinerja Maksimal pada Resource Minimal** (cPanel Shared Hosting)
3. **Kemudahan Pengoperasian Sekolah (High Usability)**

---

## 🗺️ Roadmap Utama
- [x] **Fase 1: Inisialisasi & Blueprint (Current)** - Penyusunan struktur repository, standarisasi kode, cetak biru database, dan kerangka arsitektur.
- [ ] **Fase 2: Core Engine & Installer** - Manajemen tema, perizinan pengguna (ACL), pendaftaran menu dinamis, dan installer web.
- [ ] **Fase 3: Modul Dasar Perpustakaan** - Manajemen anggota, katalogisasi buku (DDC), dan inventori buku fisik.
- [ ] **Fase 4: Modul Sirkulasi & Denda** - Transaksi peminjaman, pengembalian, perpanjangan, serta aturan denda otomatis.
- [ ] **Fase 5: Integrasi & Laporan** - Cetak kartu anggota barcode/QR, laporan statistik perpustakaan Kemendikbud, serta ekspor-impor Excel/PDF.
- [ ] **Fase 6: Rilis CosmicLib Library v1.0.0** - Uji coba beta, dokumentasi lengkap, dan penyesuaian hosting.`,

  'PROJECT_MANIFEST.md': `# 📝 CosmicLib Project Manifest

Berkas ini mendokumentasikan spesifikasi teknis, lingkungan target, arsitektur dasar, dan standar pengembangan untuk CosmicLib Engine.

---

## 🏗️ Metadata Proyek

- **Nama Engine**: CosmicLib Engine
- **Nama Produk Utama**: CosmicLib Library (Sistem Informasi Perpustakaan SMA)
- **Target Framework**: Laravel 12.x (PHP >= 8.2)
- **Arsitektur Frontend**: Blade Templates + Bootstrap 5 + Vite Asset Bundling
- **Mesin Database**: MySQL >= 8.0 / MariaDB >= 10.4
- **Target Hosting**: Shared Hosting cPanel
- **Bahasa Antarmuka (UI)**: Bahasa Indonesia (formal & sopan)
- **Status Pengembangan**: Fase 1: Inisialisasi & Cetak Biru (Blueprint Initialized)

---

## 🏛️ Desain Arsitektur & Pola Sistem
1. **Modular Monolith**: Fitur dipecah menjadi modul-modul logis di dalam folder \`/modules/\` kustom.
2. **Service Layer Pattern**: Logika bisnis wajib diekstraksi ke kelas Service independen.
3. **Optimasi Shared Hosting**: Meminimalkan kueri N+1 dan memanfaatkan cache file secara agresif.`,

  'CHANGELOG.md': `# 📜 Changelog (Riwayat Perubahan)

## [1.0.0-alpha.1] - 2026-07-12

### Ditambahkan (Added)
- Struktur direktori standar repository (\`.github/\`, \`docs/\`, \`blueprint/\`, \`prompts/\`, \`examples/\`, \`assets/\`, \`scripts/\`, \`tests/\`).
- Kerangka panduan kontribusi, kode etik, keamanan, dan template isu/pull request pada folder \`.github/\`.
- Cetak biru 21 dokumen awal arsitektur sistem (\`00_SYSTEM_PROMPT.md\` hingga \`20_ROADMAP.md\`) pada folder \`docs/\`.
- Berkas deklaratif proyek di root directory (README, PROJECT_MANIFEST, ROADMAP, LICENSE, CLAUDE, AGENTS, etc).
- Dasbor Peninjau Dokumentasi (Interactive Viewer) berbasis React 19 + Tailwind CSS untuk eksplorasi repositori.`,

  'LICENSE': `MIT License

Copyright (c) 2026 CosmicLib Engine

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.`,

  'ROADMAP.md': `# 🗺️ CosmicLib Engine & Library Roadmap

## 📅 Fase 1: Cetak Biru, Struktur, & Standarisasi (Sedang Berjalan)
- [x] Inisialisasi struktur direktori repositori profesional.
- [x] Pembuatan 21 dokumen fondasi sistem (\`docs/\`).
- [x] Penyusunan file pedoman instruksi AI (AGENTS.md, CLAUDE.md, AI_STUDIO.md, dll).

## 📅 Fase 2: Core Engine & Sistem Integrasi Dasar (Q3 2026)
- [ ] Setup Framework Laravel 12 kustom dengan Vite + Bootstrap 5.
- [ ] Core Theme Engine (sistem layout Blade dinamis).
- [ ] Module Engine Loader (mendeteksi modul pada folder \`/modules\`).
- [ ] System ACL (Access Control List - multi-peran admin, siswa, pustakawan).

## 📅 Fase 3: Modul Sirkulasi & Manajemen Buku (Q4 2026)
- [ ] Manajemen Anggota & cetak kartu barcode.
- [ ] Katalog Buku berdasarkan klasifikasi DDC.
- [ ] Alur Sirkulasi (peminjaman, pengembalian, perpanjangan, kalkulasi denda otomatis).`,

  'CLAUDE.md': `# Claude Guidelines for CosmicLib Engine

This file guides Claude AI on how to interact with this repository and maintain the design architecture of **CosmicLib Engine**.

## Development Commands
- **Composer Dependencies**: \`composer install\`
- **Artisan Command Dev**: \`php artisan serve\`
- **Artisan Migrate**: \`php artisan migrate\`
- **Artisan Test**: \`php artisan test\`
- **NPM Install**: \`npm install\`
- **NPM Run Compile**: \`npm run build\`

## Code Style Guidelines
- **PHP Styling**: Follow PSR-12 formatting.
- **Naming Conventions**: PascalCase classes, camelCase methods/variables, snake_case database tables/columns.`,

  'AGENTS.md': `# CosmicLib Engine - Agent Instructions

This file contains persistent instruction overrides and rules for AI Coding Agents.

## 🛑 STRICT COMMANDMENTS
1. **DO NOT WRITE LARAVEL CODE YET**: Focus purely on the blueprint and documentation phase.
2. **DO NOT EXPOSE SECRETS**: Never commit secrets to Git.
3. **UI LANGUAGE**: Indonesian for user-facing UI, English for classes, databases, and code syntax.`,

  'CODEX.md': `# Codex Guidelines: CosmicLib Engine

This file instructs Codex/GitHub Copilot on how to complete code inline for this project.

- Always write clean DocBlocks in standard PHP DocBlock format.
- Write UI and user-facing messages in Indonesian.
- Write code variables, methods, tables in English.`,

  'AI_STUDIO.md': `# AI Studio Developer Guidelines: CosmicLib Engine

Guidelines for developer-agent iterations inside Google AI Studio:
- Port 3000 on host 0.0.0.0 is the only externally accessible port.
- Create beautiful interactive React components in \`src/\` to let users browse documentation dynamically.`,

  '.clinerules': `# Cline Rules for CosmicLib Engine

- Currently in Phase 1 (Blueprint & Documentation).
- Never overwrite documentation files without specific developer instructions.
- All UI designs must feature high-contrast premium layouts (deep slate dark canvases with cosmic-teal highlights).`,

  '.gitignore': `# React Development Workspace Ignores
node_modules/
build/
dist/
coverage/
.DS_Store
*.log

# Laravel standard ignores
/vendor/
/public/build/
/public/hot/
/public/storage
/storage/*.key`,

  'blueprint/database_schema.sql': `-- CosmicLib MySQL Database Blueprint
-- Target: MySQL 8.0+ / MariaDB 10.4+

CREATE TABLE \`users\` (
    \`id\` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    \`name\` VARCHAR(255) NOT NULL,
    \`email\` VARCHAR(255) UNIQUE NOT NULL,
    \`password\` VARCHAR(255) NOT NULL,
    \`created_at\` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE \`books\` (
    \`id\` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    \`title\` VARCHAR(255) NOT NULL,
    \`isbn\` VARCHAR(20) UNIQUE NULL,
    \`author\` VARCHAR(255) NOT NULL,
    \`publisher\` VARCHAR(255) NOT NULL,
    \`ddc_classification\` VARCHAR(20) NULL
);`,

  'blueprint/README.md': `# 📐 CosmicLib Blueprints

Direktori ini berisi cetak biru (blueprints) teknis database dan relasi untuk CosmicLib Engine.
- \`database_schema.sql\`: Skema MySQL mentah untuk tabel pengguna, peran, anggota, buku, sirkulasi, dan denda.`,

  'prompts/claude/README.md': `# 🧠 Claude Prompt Guide
Panduan rekayasa instruksi untuk asisten Claude AI demi penyusunan modul Laravel 12 kustom yang terstandar.`,

  'prompts/codex/README.md': `# 🧠 Codex Prompt Guide
Pedoman auto-complete inline untuk asisten seperti GitHub Copilot, menekankan komentar DocBlock PHP PSR-12.`,

  'prompts/cline/README.md': `# 🧠 Cline Prompt Guide
Pedoman operasional otonom bagi asisten Cline AI agar tidak menyalahi aturan modul otonom.`,

  'prompts/ai-studio/README.md': `# 🧠 AI Studio Prompt Guide
Pedoman dan batasan platform Google AI Studio (port 3000, environment variables, compile check).`,

  'examples/module_example.json': `{
  "name": "Circulation",
  "version": "1.0.0",
  "description": "Modul utama sirkulasi peminjaman, pengembalian, perpanjangan buku, dan pencatatan denda.",
  "dependencies": {
    "core": ">=1.0.0"
  },
  "settings": {
    "default_borrow_limit_days": 7,
    "fine_amount_per_day": 1000
  }
}`,

  'assets/README.md': `# 🎨 CosmicLib Assets
Penyimpanan visual, logo kustom perpustakaan sekolah, mockup wireframe dasbor, dan aset gambar biner.`,

  'scripts/README.md': `# 🛠️ CosmicLib Scripts
Skrip pembantu eksternal (misal pencadangan database shell script, sinkronisasi cPanel FTP, atau reset server).`,

  'tests/README.md': `# 🧪 CosmicLib Tests
Strategi penulisan uji otomatis berbasis Pest PHP / PHPUnit untuk modul sirkulasi, katalog, dan core engine.`,

  // 21 Documents Contents
  'docs/00_SYSTEM_PROMPT.md': `# 🌌 00_SYSTEM_PROMPT.md
## 🎯 Tujuan (Goal)
Mendefinisikan System Prompt utama untuk kecerdasan buatan (AI) dalam menyusun kode modular CosmicLib.

## 🗂️ Table of Contents
1. Pendahuluan
2. Instruksi Sistem AI
3. Format Keluaran
4. Penanganan Kesalahan

*Draf ini berisi instruksi khusus untuk generator AI agar selalu mematuhi pedoman perancangan modular Laravel 12.*`,

  'docs/01_PROJECT_OVERVIEW.md': `# 🌌 01_PROJECT_OVERVIEW.md
## 🎯 Tujuan (Goal)
Mengidentifikasi tantangan administrasi perpustakaan sekolah di Indonesia dan menyajikan solusi CosmicLib Library.

## 🗂️ Table of Contents
1. Latar Belakang
2. Identifikasi Masalah
3. Solusi Proyek
4. Target Pengguna

*Draf ini merangkum masalah utama sirkulasi buku sekolah dan merancang platform responsif, hemat resource, dan ramah pengguna cPanel.*`,

  'docs/02_VISION.md': `# 🌌 02_VISION.md
## 🎯 Tujuan (Goal)
Menjelaskan visi jangka panjang, pilar desain CosmicLib, dan kriteria keberhasilan digitalisasi sekolah.

## 🗂️ Table of Contents
1. Pernyataan Visi
2. Nilai-Nilai Inti
3. Prinsip Desain Perangkat Lunak
4. Target Jangka Panjang

*Visi CosmicLib adalah mendemokrasikan sistem otomasi perpustakaan di sekolah tingkat menengah dengan sistem plug-and-play yang andal.*`,

  'docs/03_ARCHITECTURE.md': `# 🌌 03_ARCHITECTURE.md
## 🎯 Tujuan (Goal)
Mendokumentasikan pola Modular Monolith dan pemisahan logika bisnis (Service Layer) CosmicLib.

## 🗂️ Table of Contents
1. Ikhtisar Arsitektur
2. Pola Modular Monolith
3. Aliran Data (Data Flow)
4. Pemisahan Tanggung Jawab

*Pola ini membagi batas logis antarmodul untuk menjamin kode inti tetap bersih dan tidak terpengaruh oleh kerusakan pada modul tambahan.*`,

  'docs/04_TECH_STACK.md': `# 🌌 04_TECH_STACK.md
## 🎯 Tujuan (Goal)
Menjaga kompatibilitas dengan server shared hosting cPanel murah di Indonesia.

## 🗂️ Table of Contents
1. Backend & Framework
2. Frontend Assets
3. Basis Data
4. Persyaratan Server

*Teknologi inti: Laravel 12, Bootstrap 5 + Vite, MySQL >= 8.0, RAM minimal 512MB (sangat ringan dan kompatibel).*`,

  'docs/05_FOLDER_STRUCTURE.md': `# 🌌 05_FOLDER_STRUCTURE.md
## 🎯 Tujuan (Goal)
Memetakan tata letak direktori root Laravel dan posisi folder \`/modules/\` kustom.

## 🗂️ Table of Contents
1. Struktur Root Aplikasi
2. Struktur Folder Modul
3. Konvensi Penamaan Berkas
4. Autoloading PSR-4

*Panduan penamaan namespace kustom agar modul dapat terbaca secara asinkron saat bootstrap aplikasi.*`,

  'docs/06_DATABASE_DESIGN.md': `# 🌌 06_DATABASE_DESIGN.md
## 🎯 Tujuan (Goal)
Rancangan skema relasional MySQL, relasi satu-ke-banyak (ERD), dan letak indeks pencarian denda/sirkulasi.

## 🗂️ Table of Contents
1. Skema Tabel Core
2. Skema Tabel Modul Perpustakaan
3. Relasi Antartabel
4. Optimasi Kueri & Indeks

*Skema dirancang khusus untuk mempercepat pencarian data buku dan mengkalkulasi denda secara real-time.*`,

  'docs/07_CORE_ENGINE.md': `# 🌌 07_CORE_ENGINE.md
## 🎯 Tujuan (Goal)
Siklus hidup bootstrap aplikasi, service provider global, dan logging terpusat.

## 🗂️ Table of Contents
1. Siklus Hidup Aplikasi
2. Service Providers Inti
3. Manajemen Dependensi
4. Logging Terpusat

*Penjelasan alur request masuk dari router hingga dianalisis oleh handler kesalahan CosmicLib.*`,

  'docs/08_MODULE_ENGINE.md': `# 🌌 08_MODULE_ENGINE.md
## 🎯 Tujuan (Goal)
Sistem pemuat dinamis (loader) yang mendeteksi manifest \`module.json\` dalam folder modul.

## 🗂️ Table of Contents
1. Arsitektur Loader
2. Siklus Hidup Modul
3. Format Manifest module.json
4. Isolasi Modul & Keamanan

*Sistem modul dapat menginstalasi tabel database-nya sendiri tanpa mengganggu tabel inti.*`,

  'docs/09_THEME_ENGINE.md': `# 🌌 09_THEME_ENGINE.md
## 🎯 Tujuan (Goal)
Manajemen layout Blade dinamis kustom sekolah dan integrasi Vite.

## 🗂️ Table of Contents
1. Prinsip Desain UI
2. Arsitektur Tema Dinamis
3. Integrasi Bootstrap & Vite
4. Kustomisasi Tampilan Sekolah

*Sekolah dapat mengubah warna tema primer, logo, dan slogan tanpa mengedit kode HTML/CSS.*`,

  'docs/10_PERMISSION_ENGINE.md': `# 🌌 10_PERMISSION_ENGINE.md
## 🎯 Tujuan (Goal)
Sistem otorisasi peran pengguna (Access Control List / RBAC) dan middleware Laravel.

## 🗂️ Table of Contents
1. Definisi Peran (Roles)
2. Hierarki Hak Akses
3. Middleware Keamanan
4. Integrasi Blade Directives

*Membatasi fungsionalitas tombol pengembalian buku atau setelan denda hanya untuk peran Pustakawan dan Admin.*`,

  'docs/11_MENU_ENGINE.md': `# 🌌 11_MENU_ENGINE.md
## 🎯 Tujuan (Goal)
Penyusunan menu navigasi panel admin (sidebar) secara dinamis sesuai perizinan pengguna.

## 🗂️ Table of Contents
1. Registrasi Menu Dinamis
2. Hierarki Struktur Menu
3. Penyaringan Hak Akses
4. Caching Menu

*Modul tambahan dapat menyisipkan sub-menu kustom di bawah kategori sirkulasi.*`,

  'docs/12_WIDGET_ENGINE.md': `# 🌌 12_WIDGET_ENGINE.md
## 🎯 Tujuan (Goal)
Pola abstract class BaseWidget untuk membangun visual statistik dasbor secara asinkron.

## 🗂️ Table of Contents
1. Kelas BaseWidget
2. Sistem Grid Dasbor
3. AJAX Widget Loading
4. Daftar Widget Default

*Dasbor dapat memuat kartu statistik secara lazy-load untuk mempercepat respon pemuatan pertama.*`,

  'docs/13_PLUGIN_ENGINE.md': `# 🌌 13_PLUGIN_ENGINE.md
## 🎯 Tujuan (Goal)
Arsitektur penempatan Action Hooks & Filters mirip pola ekosistem WordPress.

## 🗂️ Table of Contents
1. Arsitektur Hook & Event
2. Action Hooks (Aksi)
3. Filter Hooks (Penyaringan)
4. Manajemen Aktivitas Plugin

*Mendukung modifikasi data sirkulasi tanpa mengubah berkas di dalam core system.*`,

  'docs/14_MEDIA_ENGINE.md': `# 🌌 14_MEDIA_ENGINE.md
## 🎯 Tujuan (Goal)
Manajemen upload berkas (sampul buku, foto anggota) dan optimasi kompresi gambar.

## 🗂️ Table of Contents
1. Storage Drivers
2. Image Compression
3. Disk Hygiene (Garbage Collection)
4. URL Publik & Keamanan

*Mengompres ukuran gambar otomatis di bawah 200KB untuk menjaga kuota disk hosting sekolah.*`,

  'docs/15_SYSTEM_SETTING.md': `# 🌌 15_SYSTEM_SETTING.md
## 🎯 Tujuan (Goal)
Penyimpanan setelan konfigurasi di database MySQL menggunakan cache file global.

## 🗂️ Table of Contents
1. Skema Key-Value settings
2. Cache Layer Setelan
3. Antarmuka Pengaturan Admin
4. Sinkronisasi Runtime .env

*Menghindari pembacaan kueri database berulang dengan mematangkan cache setelan.*`,

  'docs/16_INSTALLER.md': `# 🌌 16_INSTALLER.md
## 🎯 Tujuan (Goal)
Desain asisten web interaktif (Web Wizard) untuk migrasi basis data dan pendaftaran admin awal.

## 🗂️ Table of Contents
1. Alur Wizard Steps
2. Pengecekan Kebutuhan Server
3. Konfigurasi Database Visual
4. Pendaftaran Akun Admin Pertama

*Memudahkan sekolah menginstal aplikasi tanpa menyentuh command line terminal cPanel.*`,

  'docs/17_SYSTEM_UPDATE.md': `# 🌌 17_SYSTEM_UPDATE.md
## 🎯 Tujuan (Goal)
Pengunduh update otomatis dan pengekstraksi berkas zip aman.

## 🗂️ Table of Contents
1. Mekanisme Cek Versi
2. Ekstraksi ZIP Terenkripsi
3. Auto-Migrasi Database
4. Fail-Safe Rollback

*Jika pembaruan gagal, sistem akan mengembalikan status file ke salinan cadangan sebelumnya.*`,

  'docs/18_BACKUP_RESTORE.md': `# 🌌 18_BACKUP_RESTORE.md
## 🎯 Tujuan (Goal)
Modul backup database ke sql dump murni PHP dan kompresi file media ke arsip tunggal.

## 🗂️ Table of Contents
1. Strategi Backup
2. Ekspor DB (Pure PHP SQL Dump)
3. Kompresi Folder Media ke ZIP
4. Alur Restore Data

*Menjamin kemandirian sekolah atas kepemilikan data perpustakaan.*`,

  'docs/19_DEPLOYMENT.md': `# 🌌 19_DEPLOYMENT.md
## 🎯 Tujuan (Goal)
Langkah deployment ke public_html shared hosting cPanel dan konfigurasi Laravel task scheduler.

## 🗂️ Table of Contents
1. Arsitektur Folder cPanel
2. Upload via FTP / File Manager
3. Konfigurasi Cron Job
4. Optimasi Produksi

*Panduan memisahkan folder core di luar folder publik demi keamanan tingkat tinggi.*`,

  'docs/20_ROADMAP.md': `# 🌌 20_ROADMAP.md
## 🎯 Tujuan (Goal)
Pemetaan target rilis iteratif (Alpha, Beta, RC) dan kriteria kestabilan rilis.

## 🗂️ Table of Contents
1. Backlog Fitur Sirkulasi
2. Target Milestones
3. Release Checklist
4. Komunitas & Plugin

*Kriteria kelayakan rilis CosmicLib Library v1.0.0 meliputi tes performa dan keamanan XSS.*`
};
