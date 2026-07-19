# 24 — Search Engine

> **CosmicLib Engine Documentation**
> Versi: 1.0.0 | Status: Blueprint | Bahasa: ID (UI) / EN (Code)

---

## Daftar Isi

1. [Tujuan](#tujuan)
2. [Arsitektur](#arsitektur)
3. [Komponen](#komponen)
4. [Lifecycle](#lifecycle)
5. [Konfigurasi](#konfigurasi)
6. [Integrasi](#integrasi)
7. [AI Rules](#ai-rules)
8. [Best Practice](#best-practice)
9. [Checklist](#checklist)
10. [Roadmap](#roadmap)

---

## Tujuan

Search Engine menyediakan kemampuan **pencarian terpadu** di seluruh konten CosmicLib — mulai dari katalog buku, anggota, hingga dokumen digital. Engine ini mendukung pencarian full-text, pencarian tersaring (faceted search), dan saran pencarian (autocomplete).

**Fungsi utama:**
- Pencarian full-text di seluruh koleksi buku (judul, pengarang, ISBN, sinopsis)
- Pencarian anggota perpustakaan
- Pencarian dokumen digital
- Autocomplete dan saran pencarian
- Filter dan faceted search (kategori, tahun, penerbit, ketersediaan)
- Search indexing otomatis saat data berubah
- Riwayat dan trending pencarian
- Penilaian relevansi (relevance scoring)

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    Search Engine                            │
│                                                             │
│  User Input                                                 │
│      │                                                      │
│      ▼                                                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Search Layer                             │  │
│  │                                                       │  │
│  │  SearchRequest ──▶ SearchService ──▶ SearchDriver    │  │
│  │                                            │          │  │
│  │                          ┌─────────────────┤          │  │
│  │                          ▼                 ▼          │  │
│  │                   MySQL FULLTEXT     Meilisearch/     │  │
│  │                   (default)          Typesense/       │  │
│  │                                      Elasticsearch   │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Search Index Manager                     │  │
│  │                                                       │  │
│  │  Book Index | Member Index | Digital Doc Index       │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Search Result Types                      │  │
│  │                                                       │  │
│  │  BookResult | MemberResult | DocumentResult           │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Driver Strategy

```php
// Default: MySQL FULLTEXT (tidak perlu service tambahan)
// Upgrade: Meilisearch (self-hosted, performa tinggi)
// Enterprise: Elasticsearch / Typesense
```

---

## Komponen

### 1. Service — `SearchService`

```
SearchService
├── search(SearchQuery $query): SearchResult
├── searchBooks(string $q, array $filters): Collection
├── searchMembers(string $q): Collection
├── searchDigital(string $q, array $filters): Collection
├── autocomplete(string $q, string $type): array
├── indexBook(Book $book): bool
├── removeBook(int $bookId): bool
├── indexAll(string $model): void
├── getTrending(string $type, int $limit): array
└── logSearch(string $query, string $type, int $results): void
```

### 2. Search Query Object

```php
class SearchQuery
{
    public string $query;
    public string $type = 'all';     // all | books | members | digital
    public array  $filters = [];
    public array  $sorts = [];
    public int    $perPage = 15;
    public int    $page = 1;
    public bool   $highlight = true; // Highlight matched text
    public bool   $suggest = true;   // Saran penulisan
}
```

### 3. Search Filters — Buku

```php
$filters = [
    'category'      => 'Fiksi',
    'publisher'     => 'Gramedia',
    'year_from'     => 2020,
    'year_to'       => 2026,
    'language'      => 'Indonesia',
    'available'     => true,         // Hanya yang tersedia
    'has_digital'   => false,        // Hanya yang punya versi digital
    'location'      => 'Rak A-1',
];
```

### 4. Search Result

```php
class SearchResult
{
    public Collection $hits;
    public int        $total;
    public float      $took;        // Waktu pencarian dalam ms
    public array      $facets;      // Hasil filter counts
    public array      $suggestions; // Saran kata kunci alternatif
    public string     $query;       // Query yang digunakan
}
```

### 5. Database Tables

**Tabel `search_logs`**
```sql
CREATE TABLE search_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NULL,
    query       VARCHAR(500) NOT NULL,
    type        VARCHAR(50) DEFAULT 'all',
    result_count INT NOT NULL DEFAULT 0,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP NULL,
    INDEX idx_sl_query (query(100)),
    INDEX idx_sl_type (type),
    INDEX idx_sl_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 6. MySQL FULLTEXT Index

```sql
-- Pada tabel books
ALTER TABLE books ADD FULLTEXT INDEX ft_books_search (
    title, author, isbn, publisher, synopsis, keywords
);

-- Contoh query
SELECT *, MATCH(title, author, isbn, synopsis) 
AGAINST('Harry Potter' IN BOOLEAN MODE) AS relevance
FROM books
WHERE MATCH(title, author, isbn, synopsis) 
AGAINST('Harry Potter' IN BOOLEAN MODE)
ORDER BY relevance DESC;
```

---

## Lifecycle

### Search Request Lifecycle

```
1. User input query di search bar
2. Autocomplete: debounce 300ms → GET /api/v1/search/autocomplete?q=harry
3. Submit: GET /search?q=harry+potter&type=books&category=Fiksi

4. SearchService::search(SearchQuery)
   ├── Sanitize query (strip HTML, escape special chars)
   ├── Check search cache (Redis, TTL 5 menit)
   │   └── [HIT] Return cached result
   └── [MISS]
       ├── SearchDriver::search()
       │   ├── MySQL: FULLTEXT query + filter
       │   └── Meilisearch: API call + filter
       ├── Build SearchResult
       ├── Cache result (Redis 5 menit)
       └── Log search ke search_logs

5. Return SearchResult ke view/API
6. Render hasil dengan highlight
```

### Indexing Lifecycle

```
Book::created  → Dispatch IndexBookJob
Book::updated  → Dispatch UpdateBookIndexJob
Book::deleted  → Dispatch RemoveBookIndexJob

IndexBookJob::handle()
  └── SearchService::indexBook(book)
      └── Driver-specific indexing
```

---

## Konfigurasi

### Driver Configuration

```php
// config/search.php
return [
    'driver' => env('SEARCH_DRIVER', 'mysql'),

    'drivers' => [
        'mysql' => [
            'min_word_length' => 3,
        ],
        'meilisearch' => [
            'host'   => env('MEILISEARCH_HOST', 'http://localhost:7700'),
            'key'    => env('MEILISEARCH_KEY'),
            'index'  => env('MEILISEARCH_INDEX', 'cosmiclib'),
        ],
        'elasticsearch' => [
            'hosts' => [env('ELASTICSEARCH_HOST', 'localhost:9200')],
            'index' => env('ELASTICSEARCH_INDEX', 'cosmiclib'),
        ],
    ],

    'cache' => [
        'driver' => 'redis',
        'ttl'    => 300,  // 5 menit
    ],

    'autocomplete_limit' => 10,
    'trending_limit'     => 10,
    'highlight_tag'      => ['<mark>', '</mark>'],
];
```

### Searchable Models

```php
// Menggunakan trait Searchable (Laravel Scout)
class Book extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'author'    => $this->author,
            'isbn'      => $this->isbn,
            'publisher' => $this->publisher,
            'synopsis'  => $this->synopsis,
            'category'  => $this->category?->name,
            'year'      => $this->publication_year,
            'language'  => $this->language,
        ];
    }
}
```

---

## Integrasi

### Dengan Book Module
- Setiap perubahan data buku → update search index
- Hasil pencarian buku mencakup status ketersediaan

### Dengan Digital Library Module
- Dokumen digital diindeks termasuk konten teks
- Filter khusus untuk format file (PDF, ePub, dll)

### Dengan Queue Engine
- Re-indexing massal dijalankan via Queue
- Index update per-item via Queue (non-blocking)

### Dengan API Engine
- Endpoint: `GET /api/v1/search?q=...&type=...`
- Endpoint autocomplete: `GET /api/v1/search/autocomplete?q=...`

### Dengan Log Engine
- Semua pencarian dicatat di search_logs
- Trending search dihitung dari search_logs

---

## AI Rules

```yaml
search_engine_rules:
  - WAJIB sanitize query sebelum digunakan dalam SQL FULLTEXT
  - JANGAN tampilkan data sensitif (password, token) dalam hasil pencarian
  - WAJIB cache hasil pencarian yang umum untuk performa
  - JANGAN block UI saat indexing — gunakan background job
  - WAJIB log semua pencarian untuk analitik trending
  - JANGAN hardcode driver pencarian — gunakan konfigurasi driver
  - WAJIB sediakan fallback jika search service tidak tersedia
  - JANGAN return lebih dari 100 hasil per halaman
```

---

## Best Practice

1. **Driver Abstraction** — Sembunyikan detail driver di balik interface SearchDriver
2. **Cache Aggressively** — Cache hasil pencarian populer (Redis)
3. **Async Indexing** — Selalu index via background job, bukan synchronous
4. **Debounce Autocomplete** — Client-side debounce 300ms untuk autocomplete
5. **Relevance Tuning** — Bobot field: title > author > isbn > synopsis
6. **Minimal Re-index** — Update index hanya field yang berubah
7. **Search Analytics** — Gunakan trending search untuk improve UX

---

## Checklist

### Implementasi
- [ ] `SearchService` dengan multi-driver support
- [ ] MySQL FULLTEXT driver
- [ ] Meilisearch driver (opsional)
- [ ] `SearchQuery` object
- [ ] `SearchResult` object
- [ ] Searchable trait untuk Book, Member, Digital
- [ ] Search cache layer (Redis)
- [ ] Autocomplete endpoint
- [ ] Search log table
- [ ] Trending search calculator

### UI
- [ ] Search bar global dengan autocomplete
- [ ] Halaman hasil pencarian
- [ ] Filter panel (faceted search)
- [ ] Highlight kata kunci di hasil
- [ ] Pagination hasil
- [ ] Pesan "tidak ditemukan" dengan saran

### Testing
- [ ] Unit test SearchService
- [ ] Feature test search endpoint
- [ ] Test autocomplete
- [ ] Test faceted filtering
- [ ] Performance test (response < 200ms)

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | MySQL FULLTEXT search + filter | Planned |
| v1.1 | Autocomplete + search logs | Planned |
| v1.2 | Meilisearch driver | Planned |
| v2.0 | Semantic search (AI-powered) | Future |
| v2.1 | Voice search | Future |
| v2.2 | Cross-library federated search | Future |

---

## Referensi

- [28_BOOK_MODULE.md](28_BOOK_MODULE.md) — Book Module
- [27_LIBRARY_MODULE.md](27_LIBRARY_MODULE.md) — Library Module
- [34_DIGITAL_LIBRARY.md](34_DIGITAL_LIBRARY.md) — Digital Library
- [26_QUEUE_ENGINE.md](26_QUEUE_ENGINE.md) — Queue Engine

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*