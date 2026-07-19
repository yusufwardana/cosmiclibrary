# 23 — API Engine

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

API Engine menyediakan **REST API** yang komprehensif untuk CosmicLib, memungkinkan integrasi pihak ketiga, aplikasi mobile, dan antarmuka frontend yang terpisah (headless). Engine ini mengelola autentikasi API, versioning, rate limiting, dan dokumentasi otomatis.

**Fungsi utama:**
- REST API untuk seluruh modul CosmicLib
- Autentikasi via Laravel Sanctum (Bearer Token)
- API versioning (v1, v2, dst)
- Rate limiting per token dan per IP
- Dokumentasi API otomatis (OpenAPI / Swagger)
- API key management untuk integrasi pihak ketiga
- Response format standar (JSON)
- Error handling terstandardisasi

---

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                       API Engine                            │
│                                                             │
│  External Client                                            │
│  (Mobile/Frontend/3rd Party)                               │
│          │                                                  │
│          ▼                                                  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              API Middleware Stack                     │  │
│  │                                                       │  │
│  │  ThrottleRequests ──▶ Authenticate:sanctum ──▶        │  │
│  │  LicenseCheck ──▶ ForceJSON ──▶ ApiVersion           │  │
│  └──────────────────────────────┬────────────────────────┘  │
│                                 │                           │
│          ┌──────────────────────┼──────────────────────┐   │
│          ▼                      ▼                       ▼   │
│  ┌──────────────┐    ┌───────────────────┐    ┌──────────┐  │
│  │ API Resource │    │  API Controllers  │    │  API     │  │
│  │ (Transformer)│    │  (v1, v2, ...)    │    │  Docs    │  │
│  └──────────────┘    └───────────────────┘    │ (Swagger)│  │
│                                               └──────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Standard Response Format                 │  │
│  │  { status, message, data, meta, errors }              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### API Route Namespace

```
/api/v1/
├── auth/
│   ├── POST   login
│   ├── POST   logout
│   └── GET    me
├── books/
├── members/
├── loans/
├── returns/
├── fines/
├── catalog/
├── settings/ (public)
└── reports/

/api/v1/admin/
├── users/
├── settings/
├── modules/
└── system/
```

---

## Komponen

### 1. Response Format Standard

```json
{
  "status": "success",
  "message": "Data berhasil dimuat",
  "data": { ... },
  "meta": {
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 72
    },
    "version": "1.0.0",
    "timestamp": "2026-07-19T07:45:00+07:00"
  },
  "errors": null
}
```

### 2. Error Response Format

```json
{
  "status": "error",
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "email": ["Email tidak valid"],
    "password": ["Password minimal 8 karakter"]
  },
  "meta": {
    "code": 422,
    "timestamp": "2026-07-19T07:45:00+07:00"
  }
}
```

### 3. API Resource Classes

```
app/Http/Resources/
├── UserResource
├── BookResource
├── MemberResource
├── LoanResource
├── ReturnResource
├── FineResource
├── VisitorResource
└── NotificationResource
```

### 4. API Controllers

```
app/Http/Controllers/Api/V1/
├── AuthApiController
├── BookApiController
├── CatalogApiController
├── MemberApiController
├── LoanApiController
├── ReturnApiController
├── FineApiController
├── VisitorApiController
├── ReportApiController
└── SettingApiController
```

### 5. API Rate Limiting

| Tier | Limit | Window |
|------|-------|--------|
| Public (unauthenticated) | 30 req | 1 menit |
| Member token | 100 req | 1 menit |
| Staff token | 300 req | 1 menit |
| Admin token | 600 req | 1 menit |
| System/Integration token | 1000 req | 1 menit |

### 6. Middleware Chain

```php
Route::middleware([
    'throttle:api',
    'auth:sanctum',
    'license.check:api_access',
    'force.json',
    'api.version',
])->prefix('api/v1')->group(function () {
    // API routes
});
```

### 7. HTTP Status Codes

| Code | Kondisi |
|------|---------|
| 200 | Success (GET, PUT, PATCH) |
| 201 | Created (POST) |
| 204 | No Content (DELETE) |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden (tidak punya permission) |
| 404 | Not Found |
| 409 | Conflict |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

---

## Lifecycle

### API Request Lifecycle

```
1. Client kirim request: POST /api/v1/loans
   Headers: Authorization: Bearer {token}

2. Middleware Stack:
   ├── ThrottleRequests: cek rate limit
   ├── ForceJson: set Accept: application/json
   ├── Authenticate:sanctum: validasi token
   └── LicenseCheck: cek feature api_access

3. Route dispatch ke LoanApiController::store()

4. Controller:
   ├── Form Request validation
   ├── Call LoanService::create()
   └── Return JsonResource

5. Response:
   └── LoanResource → standard JSON format → 201 Created
```

### Token Lifecycle

```
1. POST /api/v1/auth/login
   └── Return: { token: "...", token_type: "Bearer", expires_at: "..." }

2. Client simpan token, sertakan di setiap request
   └── Authorization: Bearer {token}

3. Token divalidasi oleh Sanctum middleware

4. POST /api/v1/auth/logout
   └── Revoke token dari DB
   └── Return 204 No Content
```

---

## Konfigurasi

### Sanctum Configuration

```php
// config/sanctum.php
return [
    'stateful'   => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost')),
    'expiration' => env('API_TOKEN_EXPIRY_DAYS', 30) * 60 * 24, // dalam menit
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies'   => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

### Rate Limiting

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();
    if (!$user) return Limit::perMinute(30)->by($request->ip());

    $limits = [
        'admin'  => 600,
        'staff'  => 300,
        'member' => 100,
    ];
    $limit = $limits[$user->user_type] ?? 60;
    return Limit::perMinute($limit)->by($user->id);
});
```

### API Routes

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login',  [AuthApiController::class, 'login']);
    Route::get('/catalog',      [CatalogApiController::class, 'index']);

    // Authenticated routes
    Route::middleware(['auth:sanctum', 'license.check:api_access'])
         ->group(function () {
             Route::apiResource('books',   BookApiController::class);
             Route::apiResource('members', MemberApiController::class);
             Route::apiResource('loans',   LoanApiController::class);
             // ...
         });
});
```

---

## Integrasi

### Dengan Auth Engine
- Semua API endpoint (kecuali public) memerlukan Sanctum token
- Token dibuat via Auth Engine saat login

### Dengan Permission Engine
```php
// API Controller mengecek permission
public function destroy(Book $book)
{
    $this->authorize('books.delete');
    // ...
}
```

### Dengan License Engine
- Fitur API hanya tersedia untuk lisensi Professional/Enterprise
- Rate limit berbeda per tipe lisensi

### Dengan Log Engine
- Semua API request dicatat (endpoint, method, user, response time)
- Error dan exception di-log detail

### Dengan Notification Engine
- API dapat trigger notifikasi (misalnya notifikasi keterlambatan via API)

---

## AI Rules

```yaml
api_engine_rules:
  - SELALU gunakan API Resources untuk transform data — JANGAN return model langsung
  - WAJIB validasi input via Form Request sebelum proses
  - SELALU kembalikan response dalam format standar { status, message, data, meta, errors }
  - JANGAN expose field sensitif (password, token) dalam API response
  - WAJIB versioning semua endpoint (/api/v1/, /api/v2/)
  - JANGAN ubah existing API endpoint tanpa backward compatibility
  - WAJIB sertakan pagination meta untuk list response
  - JANGAN kembalikan 500 tanpa pesan yang informatif (untuk client)
  - WAJIB rate limit semua endpoint termasuk yang public
  - JANGAN return HTML response dari API endpoint
```

---

## Best Practice

1. **Versioning** — Semua endpoint di-prefix dengan versi (`/api/v1/`)
2. **Resource Transformation** — Gunakan API Resources, jangan expose model langsung
3. **Consistent Response** — Format respons identik untuk semua endpoint
4. **Pagination** — Semua list endpoint wajib paginate (default 15, max 100)
5. **Error Codes** — HTTP status code yang tepat untuk setiap kondisi
6. **Documentation** — Semua endpoint terdokumentasi via OpenAPI/Swagger
7. **Testing** — Setiap endpoint memiliki feature test
8. **CORS** — Konfigurasi CORS yang ketat (whitelist domain)

---

## Checklist

### Implementasi
- [ ] Setup Laravel Sanctum
- [ ] Base API Resource class
- [ ] Standard response trait/helper
- [ ] Rate limiting per user type
- [ ] API versioning middleware
- [ ] Force JSON middleware
- [ ] CORS configuration
- [ ] Error handler untuk API

### Endpoints
- [ ] Auth API (login, logout, me, refresh)
- [ ] Book API (CRUD + search + catalog)
- [ ] Member API (CRUD + status)
- [ ] Loan API (create, return, history)
- [ ] Fine API (list, pay)
- [ ] Visitor API (checkin, checkout)
- [ ] Report API (statistics, exports)
- [ ] Setting API (public settings)

### Dokumentasi
- [ ] OpenAPI/Swagger setup
- [ ] Postman collection
- [ ] API changelog

### Testing
- [ ] Feature test semua endpoint
- [ ] Rate limiting test
- [ ] Authentication test
- [ ] Permission test per endpoint

---

## Roadmap

| Versi | Fitur | Status |
|-------|-------|--------|
| v1.0 | REST API + Sanctum + Rate Limit | Planned |
| v1.1 | OpenAPI / Swagger Docs | Planned |
| v1.2 | Webhook support | Planned |
| v2.0 | GraphQL API | Future |
| v2.1 | API Gateway integration | Future |
| v2.2 | SDK untuk mobile (Flutter/React Native) | Future |

---

## Referensi

- [17_AUTH_ENGINE.md](17_AUTH_ENGINE.md) — Auth Engine
- [10_PERMISSION_ENGINE.md](10_PERMISSION_ENGINE.md) — Permission Engine
- [22_LICENSE_ENGINE.md](22_LICENSE_ENGINE.md) — License Engine
- [42_SECURITY_GUIDE.md](42_SECURITY_GUIDE.md) — Security Guide

---

*Dokumen ini adalah bagian dari CosmicLib Engine Documentation Suite.*
*Dibuat: 2026 | Terakhir diperbarui: 2026-07-19*