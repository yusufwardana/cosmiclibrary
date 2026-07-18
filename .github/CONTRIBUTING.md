# Contributing to CosmicLib Engine

Terima kasih atas minat Anda untuk berkontribusi pada **CosmicLib Engine**! 🎉

Dokumen ini berisi panduan untuk membantu Anda berkontribusi secara efektif.

---

## 📋 Sebelum Memulai

1. Baca [`README.md`](../README.md) untuk memahami proyek secara keseluruhan.
2. Baca [`AGENTS.md`](../AGENTS.md) untuk memahami aturan pengembangan.
3. Baca [`docs/23_CODING_STANDARD.md`](../docs/23_CODING_STANDARD.md) untuk standar penulisan kode.
4. Periksa [Issues](https://github.com/cosmiclib/cosmiclib-engine/issues) untuk melihat tugas yang tersedia.

---

## 🔄 Workflow Kontribusi

### 1. Fork & Clone

```bash
git clone https://github.com/YOUR_USERNAME/cosmiclib-engine.git
cd cosmiclib-engine
```

### 2. Buat Branch

Gunakan konvensi penamaan branch:

```
feature/nama-fitur
fix/deskripsi-bug
docs/nama-dokumen
```

```bash
git checkout -b feature/nama-fitur
```

### 3. Buat Perubahan

- Ikuti [Coding Standard](../docs/23_CODING_STANDARD.md)
- Tulis test untuk setiap fitur baru
- Pastikan semua test lulus
- Perbarui dokumentasi jika diperlukan

### 4. Commit

Gunakan [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: tambah fitur pencarian buku
fix: perbaiki kalkulasi denda
docs: perbarui panduan deployment
style: format ulang kode sesuai PSR-12
refactor: ekstrak logika sirkulasi ke service
test: tambah test untuk BorrowService
chore: update dependensi
```

### 5. Push & Pull Request

```bash
git push origin feature/nama-fitur
```

Buat Pull Request menggunakan [PR Template](PULL_REQUEST_TEMPLATE.md).

---

## 🎨 Standar Kode

- **PHP**: PSR-12, SOLID principles
- **UI Language**: Bahasa Indonesia
- **Code Language**: English
- **Testing**: PHPUnit (unit & feature tests)
- **Documentation**: Update relevant docs in `docs/`

---

## 📝 Tipe Kontribusi

| Tipe | Deskripsi |
|:---|:---|
| 🐛 Bug Fix | Perbaikan bug yang ditemukan |
| ✨ Feature | Penambahan fitur baru |
| 📚 Documentation | Perbaikan atau penambahan dokumentasi |
| 🎨 UI/UX | Perbaikan tampilan dan pengalaman pengguna |
| ♻️ Refactor | Perbaikan struktur kode tanpa mengubah fungsionalitas |
| 🧪 Test | Penambahan atau perbaikan test |

---

## ❓ Pertanyaan?

Jika ada pertanyaan, silakan buka [Discussion](https://github.com/cosmiclib/cosmiclib-engine/discussions) atau hubungi maintainer.

Terima kasih telah membantu membangun CosmicLib Engine! 🚀
