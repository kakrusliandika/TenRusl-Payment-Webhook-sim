# 💳 TenRusl Payment Webhook Simulator

[![CI](https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim/actions/workflows/ci.yml/badge.svg)](https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim/actions)
![License](https://img.shields.io/github/license/kakrusliandika/TenRusl-Payment-Webhook-sim)
![Release](https://img.shields.io/github/v/release/kakrusliandika/TenRusl-Payment-Webhook-sim?include_prereleases&sort=semver)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Pest](https://img.shields.io/badge/Tests-Pest-18181B?logo=pestphp&logoColor=white)
![OpenAPI](https://img.shields.io/badge/OpenAPI-3.1-6BA539?logo=openapi-initiative&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)

Demo **Laravel 12** yang niru pola aplikasi payment/webhook yang *production-minded*: **idempotency**, **dedup webhook**, **signature verification (gate)**, dan **exponential backoff retry** — tapi semuanya dalam mode **simulator** (tanpa kredensial gateway asli).

🌍 Live Demo: **https://tenrusl.alwaysdata.net/payment-webhook-sim/**

> Semua provider di repo ini simulator. Jangan pernah taruh kredensial produksi di repo ini (atau di log).

---

## 🧭 Daftar Isi

- [Production Contract (wajib dibaca)](#-production-contract-wajib-dibaca)
- [Fitur](#-fitur)
- [Arsitektur Singkat](#-arsitektur-singkat)
- [Reliability Guarantees](#-reliability-guarantees)
- [Quick Start (SQLite)](#-quick-start-sqlite)
- [Demo Data untuk Admin Panel](#-demo-data-untuk-admin-panel)
- [Admin API (Protected)](#-admin-api-protected)
- [Commands Cheat Sheet](#-commands-cheat-sheet)
- [Konfigurasi](#-konfigurasi)
- [Endpoint API](#-endpoint-api)
- [Webhook Signature Cookbook](#-webhook-signature-cookbook)
- [Retry Engine & Scheduler](#-retry-engine--scheduler)
- [OpenAPI → Bundle → Postman](#-openapi--bundle--postman)
- [Testing](#-testing)
- [CI Workflows](#-ci-workflows)
- [Observability (logs/queue/horizon)](#-observability-logsqueuehorizon)
- [Docker (Dev)](#-docker-dev)
- [Deploy (Render/Railway)](#-deploy-renderrain)
- [Struktur Direktori](#-struktur-direktori)
- [Limitations & Next Steps](#-limitations--next-steps)
- [Production Hardening Checklist](#-production-hardening-checklist)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## 🧾 Production Contract (wajib dibaca)

Kalau kamu mau bilang “ini siap production”, minimal ini harus **dipenuhi**. Kalau enggak, biasanya kelihatan aman pas traffic sepi, terus panik pas ada burst 😂

### 0) TL;DR checklist

- ✅ `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` **wajib**
- ✅ DB managed (Postgres/MySQL), **jangan SQLite**
- ✅ Redis wajib untuk lock/queue (apalagi kalau scale > 1)
- ✅ **3 proses**: web + worker + scheduler (cron `schedule:run`)
- ✅ Healthcheck platform pakai **`/up`**
- ✅ Signature gate aktif di webhook route
- ✅ Worker `timeout` selaras sama `retry_after` (biar job gak double-run)
- ✅ Deploy step jalanin cache yang bener (config/route/view/event)

### 1) Tiga proses wajib (web / worker / scheduler)

**Web**
- tugas: nerima request (API + webhook)
- start: `sh ./start.sh web`

**Worker (queue)**
- tugas: proses job (kalau `TENRUSL_SCHEDULER_QUEUE=true` atau kamu dispatch job)
- start: `sh ./start.sh worker`
- catatan: aman diskalakan, tapi jangan kebablasan (lihat bagian scaling)

**Scheduler trigger**
- tugas: tiap menit trigger `php artisan schedule:run --no-interaction`
- ini bukan “service yang long-running” wajib; yang wajib itu trigger per menitnya.

Contoh setup scheduler (Linux cron):
```bash
* * * * * cd /path/to/app && php artisan schedule:run --no-interaction
```

> Render/Railway paling enak: bikin cron service/job terpisah, share env yang sama.

### 2) Health endpoint resmi

- Endpoint health **resmi**: **`/up`**
  Ini yang dipakai buat container/service healthcheck dan uptime monitor.
- Kalau kamu masih punya `/health`, gapapa, tapi jadikan `/up` sebagai “kontrak publik”.

> Biasanya `/up` cukup return status `200` tanpa logic berat. Jangan bikin healthcheck ikut query DB berat yang bikin healthcheck malah ikut bikin outage 😄

### 3) Env wajib (tanpa rahasia di docs)

**Core**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:...` (jangan generate tiap boot)
- `APP_URL=https://...`

**Database**
- `DB_CONNECTION=pgsql` / `mysql`
- `DB_URL=...` (recommended) atau `DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD`

**Redis & Queue**
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `SESSION_DRIVER=redis` (recommended)
- `REDIS_HOST`, `REDIS_PORT`, (optional) `REDIS_PASSWORD`

**TenRusl runtime knobs**
- `TENRUSL_PROVIDERS_ALLOWLIST=mock,xendit,midtrans,...`
- `TENRUSL_MAX_RETRY_ATTEMPTS=8` (contoh)
- `TENRUSL_RETRY_BASE_MS=500`
- `TENRUSL_RETRY_CAP_MS=60000`
- `TENRUSL_SCHEDULER_BACKOFF_MODE=decorrelated`
- `TENRUSL_SCHEDULER_LIMIT=500`
- `TENRUSL_SCHEDULER_QUEUE=true` (kalau mau scheduler cuma enqueue)

**Admin**
- `TENRUSL_ADMIN_HEADER=X-Admin-Key`
- `TENRUSL_ADMIN_KEY=...` (secret)
- `TENRUSL_ADMIN_DEMO_KEY=...` (secret)

> Provider secret/token (Stripe webhook secret, Midtrans server key, dll) **wajib** via platform secrets.

### 4) “No demo mode” di production

Di production, ini pantang:
- SQLite
- `APP_DEBUG=true`
- `APP_KEY` ephemeral
- cache store `array`/`file` (buat lock, itu jebakan 😅)

`start.sh` sengaja dibuat fail-fast supaya salah config ketahuan dari awal.

### 5) Deploy step: cache yang bener (biar boot cepet + konsisten)

Buat Laravel, set cache saat deploy itu “power move”:
- `php artisan config:cache`
- `php artisan route:cache` (optional; kadang bisa gagal kalau ada route closure)
- `php artisan view:cache` (optional)
- `php artisan event:cache` (kalau app punya event discovery/listener yang stable)

Migrations sebaiknya jadi release step (bukan saat boot container):
```bash
php artisan migrate --force --no-interaction
```

### 6) Queue timeout vs retry_after harus selaras

Rule of thumb: `retry_after` harus **lebih besar** dari `--timeout` worker + buffer.

Contoh aman:
- `WORKER_TIMEOUT=90`
- `retry_after=120` (buffer 30s)

Kalau enggak selaras, job bisa dianggap “expired” lalu diambil worker lain, hasilnya: **double-run**.

---

## ✨ Fitur

### 🔐 Idempotency — `POST /api/v1/payments`
- Header: `Idempotency-Key`
- Store hasil response (status+headers+body) untuk replay yang konsisten.
- Lock untuk mencegah eksekusi paralel dengan key yang sama (menghindari double-create).
- (Opsional) Deteksi konflik: key sama tapi body beda → bisa ditolak (`409`) pakai fingerprint request (hash).

### 🧬 Dedup Webhook — `(provider, event_id)`
- Unique constraint di DB untuk race-condition safe.
- Insert → kalau duplicate → ambil row existing dan lock row (agar state konsisten).
- Attempts di-*touch* saat duplicate datang dari provider (bukan internal retry).

### 🔏 Signature Verification Gate (sebelum masuk domain)
- Webhook route dipasangi middleware `verify.webhook.signature`.
- Raw body disimpan ke request attribute (mis. `tenrusl_raw_body`) biar signature hitungannya presisi.
- `SignatureVerifier` jadi source-of-truth: mapping `provider → VerifierClass` + enforce allowlist.

### 🔁 Retry dengan Exponential Backoff + Jitter
- `RetryBackoff` dukung mode: `full`, `equal`, `decorrelated` (AWS-style).
- Scheduler/command memilih event “due” dan melakukan claiming agar tidak double-process.
- Jalur retry bisa inline atau queue (job `ProcessWebhookEvent`), dan aman dipanggil ulang.

### 🧾 Correlation ID buat tracing incident
- Middleware `CorrelationIdMiddleware` inject `X-Request-ID`
- Masuk ke response header + masuk ke logging context (jadi enak cari 1 request end-to-end)

---

## 🧱 Arsitektur Singkat

```mermaid
flowchart TD
  A[Client] -->|"Idempotency-Key"| B["POST /api/v1/payments"]
  B -->|"create pending"| P[(payments)]

  W[Provider] -->|"POST /api/v1/webhooks/{provider}"| M[VerifyWebhookSignature]
  M -->|"rawBody saved to request attr"| C[WebhooksController]
  C -->|"Dedup + orchestrate"| R[WebhookProcessor]
  R -->|"Update status (atomic)"| P
  R -->|"Schedule retry"| E[(webhook_events)]

  S[Scheduler] -->|"everyMinute"| K["tenrusl:webhooks:retry"]
  K -->|"claim due events"| E
  K -->|"queue/inline"| J[ProcessWebhookEvent Job]
  J -->|"skip processed / not-due"| R
```

**Komponen inti:**
- **SignatureVerifier**: gate signature per provider + allowlist.
- **WebhookProcessor**: dedup + update payment + update event audit + scheduling retry.
- **RetryWebhookCommand**: selection due events + claiming/locking + dispatch inline/queue.
- **ProcessWebhookEvent Job**: proses async, aman dipanggil ulang.
- **CorrelationIdMiddleware**: inject `X-Request-ID` untuk tracing konsisten.

---

## 🧷 Reliability Guarantees

Bagian ini menjelaskan “janji” sistem (yang bikin dia tahan banting) dan kenapa implementasinya aman.

1) **Idempotent create payment**
   - Replay request yang sama → response sama (body/status/headers).
   - Paralel request key sama → ditahan lock; collision bisa jadi `409` (opsional).

2) **Dedup webhook race-safe**
   - Unique DB `(provider, event_id)` bikin event yang sama *cuma ada satu*.
   - Duplikasi event dari provider tidak bikin domain jadi “ke-trigger dua kali”.

3) **Update event + payment konsisten**
   - Saat event berhasil memfinalkan payment, event ditandai `processed` + timestamp.
   - Status event (`received|processed|failed`) dipisah dari status payment (`pending|succeeded|failed`) biar audit jelas.

4) **Retry enggak gampang mandek**
   - Query event due: `next_retry_at <= now OR next_retry_at IS NULL`
   - Claiming via transaksi + lock, lalu proses inline/queue
   - Guard tambahan di job: skip kalau event sudah final / belum due (antisipasi delay queue)

---

## 🚀 Quick Start (SQLite)

**Prasyarat:** PHP 8.3+, Composer, Node 20+, Git

```bash
git clone https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim.git
cd TenRusl-Payment-Webhook-sim

composer install

cp .env.example .env
php artisan key:generate

# SQLite dev cepat
mkdir -p database && touch database/database.sqlite

# migrate + seed biar demo panel langsung ada data
php artisan migrate --seed

php artisan serve
# http://127.0.0.1:8000
```

Swagger UI (kalau `l5-swagger` aktif):
- `http://127.0.0.1:8000/api/documentation`

---

## 🧪 Demo Data untuk Admin Panel

Folder `database/` sengaja dibuat “bernilai demo” biar UI/admin panel enggak kosong pas pertama buka.

Yang biasanya tersedia:
- **Factories**: `PaymentFactory`, `WebhookEventFactory`, `UserFactory`
- **Seeders**: `DatabaseSeeder` isi beberapa payment + webhook events

Cara pakai:
```bash
php artisan migrate --seed
# atau reset:
php artisan migrate:fresh --seed
```

---

## 🛡️ Admin API (Protected)

Skenario admin/demo panel:
- list payments (paginated/filter)
- list webhook events (status/attempts/next_retry_at)
- (opsional) trigger retry/replay

**Kontrak keamanan sederhana (disarankan):**
- `.env`: `TENRUSL_ADMIN_API_KEY=changeme`
- Header: `X-Admin-Key: changeme`
- Alternatif: `Authorization: Bearer <key>`

> Nama header final tetap ngikut OpenAPI (`docs/openapi.yaml`) kalau repo kamu sudah jadikan itu source-of-truth.

---

## 🧰 Commands Cheat Sheet

### 🐘 Composer scripts (composer.json)

```bash
# Setup lengkap (install + env + key + migrate + npm + build)
composer setup

# Dev mode (server + queue + logs pail + vite) via concurrently
composer dev

# Code style
composer format
composer format:check

# Static analysis (Larastan/PHPStan)
composer analyse
composer analyse:larastan

# Tests
composer test
composer test:unit

# Prepare test env (aman untuk CI/local)
composer test:prepare

# “All-in-one” QA
composer qa
```

### 🟩 NPM scripts (package.json)

```bash
# Frontend dev (Vite)
npm run dev

# Production build
npm run build

# Docs pipeline
npm run docs:prepare
npm run openapi:lint
npm run openapi:bundle
npm run postman:generate
npm run docs:sync
```

### 🧩 Artisan commands penting

```bash
# Retry processor utama (dipanggil scheduler)
php artisan tenrusl:webhooks:retry --limit=200 --max-attempts=5 --mode=full
php artisan tenrusl:webhooks:retry --provider=mock --limit=50 --mode=decorrelated

# Wrapper manual trigger
php artisan tenrusl:webhooks:retry-once

# Jalankan scheduler loop (local/dev)
php artisan schedule:work

# Jalankan queue worker khusus webhook (kalau mode queue dipakai)
php artisan queue:work --queue=webhooks

# Utility
php artisan route:list --path=api/v1
php artisan migrate --seed
php artisan test
```

---

## 🔧 Konfigurasi

Konfigurasi utama di `config/tenrusl.php` dan dikontrol via `.env`.

### 🎛️ Knob inti (dipakai nyata di service)
| Config Key | Env | Default | Dipakai oleh |
|---|---|---:|---|
| `tenrusl.max_retry_attempts` | `TENRUSL_MAX_RETRY_ATTEMPTS` | `5` | WebhookProcessor, RetryWebhookCommand, scheduler |
| `tenrusl.retry_base_ms` | `TENRUSL_RETRY_BASE_MS` | `500` | RetryBackoff |
| `tenrusl.retry_cap_ms` | `TENRUSL_RETRY_CAP_MS` | `30000` | RetryBackoff |
| `tenrusl.retry_min_lease_ms` | `TENRUSL_RETRY_MIN_LEASE_MS` | `250` | RetryWebhookCommand |
| `tenrusl.scheduler_limit` | `TENRUSL_SCHEDULER_LIMIT` | `200` | scheduler |
| `tenrusl.scheduler_backoff_mode` | `TENRUSL_SCHEDULER_BACKOFF_MODE` | `full` | scheduler |
| `tenrusl.scheduler_provider` | `TENRUSL_SCHEDULER_PROVIDER` | `""` | scheduler filter provider |
| `tenrusl.signature.timestamp_leeway_seconds` | `TENRUSL_SIG_TS_LEEWAY_SECONDS` | `300` | verifiers yang pakai timestamp |
| `tenrusl.admin.api_key` | `TENRUSL_ADMIN_API_KEY` | `""` | Admin endpoints |

### ✅ Allowlist provider
Allowlist dipakai konsisten oleh:
- constraint route (`whereIn('provider', $providers)`)
- SignatureVerifier allowlist gate

Contoh:
```text
mock, xendit, midtrans, stripe, paypal, paddle, lemonsqueezy,
airwallex, tripay, doku, dana, oy, payoneer, skrill, amazon_bwp
```

---

## 📡 Endpoint API

Base URL: `http://127.0.0.1:8000/api/v1`

| Method | Path | Deskripsi | Catatan |
|---:|---|---|---|
| POST | `/payments` | Create payment (idempotent) | Header `Idempotency-Key` |
| GET | `/payments/{provider}/{provider_ref}/status` | Status check | provider constrained |
| GET | `/payments/{id}` | Get payment by id | cocok buat admin/detail |
| POST | `/webhooks/{provider}` | Receive webhook | middleware signature wajib |
| OPTIONS | `/webhooks/{provider}` | Preflight | untuk CORS |
| GET | `/admin/*` | Admin list/ops | protected |

### Contoh cURL — create payment
```bash
curl -X POST http://127.0.0.1:8000/api/v1/payments   -H "Content-Type: application/json"   -H "Idempotency-Key: 123e4567-e89b-12d3-a456-426614174000"   -H "X-Request-ID: req-demo-001"   -d '{"provider":"mock","amount":25000,"currency":"IDR","description":"Topup","metadata":{"order_id":"ORD-123"}}'
```

---

## 🔏 Webhook Signature Cookbook

Prinsip umum (yang selalu sama):
1) Ambil **raw body** persis yang dikirim provider
2) Hitung signature sesuai kontrak provider
3) Compare **constant-time**
4) (Kalau ada timestamp) enforce leeway

Webhook lewat gate middleware: `VerifyWebhookSignature` → `SignatureVerifier` → `ProviderSignature::verify(rawBody, Request)`.

| Provider | Header/Metode | Cara validasi (ringkas, tanpa rahasia) |
|---|---|---|
| `mock` | `X-Mock-Signature` | `hex(hmac_sha256(raw_body, MOCK_SECRET))` |
| `xendit` | `X-CALLBACK-TOKEN` | token header harus match env |
| `midtrans` | `signature_key` | `sha512(order_id + status_code + gross_amount + MIDTRANS_SERVER_KEY)` |
| `stripe` | `Stripe-Signature` | HMAC + timestamp (leeway) |
| `paddle` | `p_signature` | simulasi RSA/HMAC (edukatif) |
| `lemonsqueezy` | `X-Signature` | HMAC raw body |
| `airwallex` | `x-timestamp`, `x-signature` | HMAC SHA256 `timestamp + body` |
| `tripay` | `X-Callback-Signature` | HMAC raw JSON |
| `doku` | `Signature` (+Digest, dll.) | gaya signer DOKU (disederhanakan) |
| `dana` | RSA signature header | verifikasi RSA (public key) |
| `oy` | secret/whitelist | dipersiapkan (simulasi) |
| `payoneer` | shared secret | dipersiapkan (simulasi) |
| `skrill` | MD5/IPN | dipersiapkan (simulasi) |
| `amazon_bwp` | RSA signature header | dipersiapkan (simulasi) |

> Karena ini simulator, beberapa provider dibuat “edukatif”: fokus pada pola gate + raw body + compare aman.

---

## 🔁 Retry Engine & Scheduler

### Backoff modes
- **full**: `random(0, exp)`
- **equal**: `exp/2 + random(0, exp/2)`
- **decorrelated**: `min(cap, random(base, prev*3))`

### Prinsip command retry
- Ambil event due (`next_retry_at <= now OR next_retry_at IS NULL`)
- Claiming via transaksi + lock:
  - `attempts++`
  - `last_attempt_at = now`
  - set lease `next_retry_at = now + backoff`
- Proses inline atau queue (job)

### ✅ Scheduler requirement di production (WAJIB)

Laravel scheduler **tidak otomatis jalan** hanya karena definisi schedule ada di repo. Wajib ada trigger per menit:

```bash
* * * * * cd /path/to/app && php artisan schedule:run --no-interaction
```

Cara verifikasi:
```bash
php artisan schedule:list
php artisan schedule:run -vvv
```

Kalau mode queue aktif (`TENRUSL_SCHEDULER_QUEUE=true`), pastikan worker hidup:
```bash
php artisan queue:work --queue=webhooks
```

---

## 📜 OpenAPI → Bundle → Postman

Berkas:
- `docs/openapi.yaml` (source of truth)
- `redocly.yaml` (lint rules)
- output bundle: `storage/api-docs/openapi.yaml`
- output Postman: `postman/TenRusl.postman_collection.json`

One-liner:
```bash
npm run docs:sync
```

Biasanya step-nya:
1) buat folder output
2) lint OpenAPI
3) bundle
4) generate Postman

---

## 🧪 Testing

```bash
composer test
```

Skenario yang penting:
- idempotency key replay konsisten
- dedup webhook: event_id sama → hanya 1 row + attempts naik
- signature invalid → 401
- retry command: cuma ambil event due + hormati limit
- admin endpoint: tanpa auth → ditolak

---

## ✅ CI Workflows

Folder: `.github/workflows/`

Biasanya isinya:
- `ci.yml`: install → migrate SQLite → pint → larastan → pest → (opsional) audit → docs artifact
- `docs.yml`: `npm ci` → `npm run docs:sync` lalu fail kalau ada file generated yang belum dicommit
- `php-ci.yml`: tests cepat (SQLite)
- (opsional) `retry-schedule.yml`: schedule workflow buat demo retry processor
- (opsional) `railway-deploy.yml`: deploy on push main

---

## 📈 Observability (logs/queue/horizon)

Yang worth dimonitor (minimal):
- queue depth (terutama queue `webhooks`)
- failure rate (jobs failed / webhook 4xx/5xx)
- p95/p99 processing time per webhook
- jumlah event `due` menumpuk (indikasi scheduler/worker mati)
- Redis availability (latency/error)
- DB slow queries / connection errors

### Correlation ID (`X-Request-ID`)
- Kirim `X-Request-ID` dari client atau gateway.
- Middleware akan propagate ke response dan masuk ke log context.

### Logging yang enak di produksi
- Untuk container: log ke **STDERR** (enak diambil platform)
- Kalau mau rapi, gunakan JSON logger + centralized log aggregator

### Queue yang tahan banting
- Selaraskan `retry_after` dengan `--timeout` worker (plus buffer).
- Gunakan failed jobs driver supaya bisa investigasi.

### Horizon (opsional tapi mantap)
Kalau pakai Redis queue, Horizon enak buat:
- dashboard throughput/runtime/failures
- config worker yang versioned

---

## 🐳 Docker (Dev)

```bash
docker compose up -d --build
# http://localhost:8000
```

Tips:
- Kalau vendor kosong, compose pakai volume `tenrusl-vendor` supaya install composer enggak hilang saat bind mount.
- Kalau admin panel butuh data: `php artisan migrate --seed`

---

## 🚢 Deploy (Render/Railway)

### Render (Docker)
- Blueprint: `render.yaml`
- Web service + worker + cron scheduler dipisah biar jelas

Wajib:
- cron job per menit: `php artisan schedule:run --no-interaction`
- healthcheck: `/up`

Deploy flow yang ideal:
1) build image
2) release step: `php artisan migrate --force --no-interaction`
3) cache step (config/route/view/event)
4) start web container

### Railway (Nixpacks)
- Config: `railway.toml`
- Pastikan ada scheduler trigger juga (cron job)
- Untuk demo, kamu bisa seed sekali (optional): `php artisan migrate --seed`

---

## 🗂️ Struktur Direktori

```text
app/
  Console/Commands/RetryWebhookCommand.php
  Http/Controllers/Api/V1/
  Http/Middleware/CorrelationIdMiddleware.php
  Http/Middleware/VerifyWebhookSignature.php
  Jobs/ProcessWebhookEvent.php
  Models/
  Repositories/
  Services/
config/tenrusl.php
routes/api.php
routes/console.php
docs/openapi.yaml
redocly.yaml
postman/
tests/Feature/
.github/workflows/
database/
```

---

## ⚠️ Limitations & Next Steps

Repo ini fokus edukasi + portfolio.

Yang sengaja disederhanakan:
- payload provider tidak selalu identik 1:1 dengan kontrak terbaru
- sebagian verifikasi signature dibuat generik (pola gate + raw body), bukan implementasi produksi lengkap

Next steps yang bagus:
- housekeeping/pruning event lama
- UI kecil buat lihat webhook events + retry timeline
- rate limit / circuit breaker per provider
- metrics p95/p99 processing time + failure rate

---

## ➕ Production Hardening Checklist

Bagian ini opsional, tapi kalau kamu ikutin, biasanya hidup lebih tenang 😄

### 1) Minimum yang harus ada (prod)

- ✅ Trigger scheduler per menit (`schedule:run`)
- ✅ Cache shared untuk lock (Redis recommended)
- ✅ Queue worker kalau mode queue aktif
- ✅ Rotasi secrets bila `.env` pernah kepublikasi
- ✅ Logs bisa diakses (stderr untuk container, atau log aggregation)

### 2) Multi-instance (horizontal scaling)

Kalau web service discale jadi >1 instance:

- Pastikan schedule pakai `onOneServer()` + `withoutOverlapping()`
- Pastikan semua instance **pakai Redis yang sama** (`CACHE_STORE=redis`)
- Verifikasi: `php artisan schedule:list` dan pastikan event tidak dobel-run

### 3) Queue tuning (biar gak “ngunci” DB)

- Mulai kecil dulu: 1–2 worker, cek DB load & p95 processing time
- Kalau webhook burst, naikkan concurrency pelan-pelan (mis. 2 → 4 → 6)
- Pisahkan queue `webhooks` dari queue lain biar gak saling ganggu

### 4) Alarm yang kepake (yang bikin kamu tahu duluan)

- Alarm kalau failure rate webhook naik (401/403/5xx)
- Alarm kalau queue backlog `webhooks` naik terus (worker mati/lambat)
- Alarm kalau banyak event due menumpuk (scheduler mati / lock stuck)
- Alarm kalau p95 processing time naik terus (DB/Redis slow)

### 5) Timezone notes (biar gak salah baca jadwal)

Banyak platform cron pakai **UTC**. Untuk WIB (UTC+7):
- 09:00 WIB = 02:00 UTC
- 17:00 WIB = 10:00 UTC

Kalau scheduler kamu cuma “tiap menit”, timezone bukan isu besar, tapi buat job harian/mingguan itu penting.

### 6) Template env produksi (aman buat dicopy)

> Ini contoh placeholder, bukan value beneran.

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:CHANGE_ME
APP_URL=https://example.com

DB_CONNECTION=pgsql
DB_URL=postgresql://user:pass@host:5432/dbname

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379

TENRUSL_PROVIDERS_ALLOWLIST=mock,xendit,midtrans,stripe,lemonsqueezy,tripay
TENRUSL_MAX_RETRY_ATTEMPTS=8
TENRUSL_RETRY_BASE_MS=500
TENRUSL_RETRY_CAP_MS=60000
TENRUSL_SCHEDULER_BACKOFF_MODE=decorrelated
TENRUSL_SCHEDULER_LIMIT=500
TENRUSL_SCHEDULER_QUEUE=true

TENRUSL_ADMIN_HEADER=X-Admin-Key
TENRUSL_ADMIN_KEY=CHANGE_ME
TENRUSL_ADMIN_DEMO_KEY=CHANGE_ME
```

---

> Butuh SOP operasi yang lebih detail (incident, lonjakan retry, backlog queue, Redis down, dll)? cek **RUNBOOK.md**.
## 🛟 Troubleshooting

### Webhook selalu 401
- Pastikan middleware signature aktif di route webhook.
- Pastikan signature dihitung dari **raw body** persis.

### Retry enggak jalan
- Pastikan scheduler trigger ada:
  ```bash
  php artisan schedule:run -vvv
  ```
- Kalau mode queue aktif, worker harus hidup:
  ```bash
  php artisan queue:work --queue=webhooks
  ```
- Kalau lock overlap nyangkut (jarang, tapi bisa), coba:
  ```bash
  php artisan schedule:clear-cache
  ```

### Swagger UI 404
Kalau `l5-swagger` dipakai:
```bash
php artisan l5-swagger:generate
```

---

## 📝 Lisensi

MIT © TenRusl - Andika Rusli

---

> Butuh SOP operasi yang lebih detail (incident, lonjakan retry, backlog queue, Redis down, dll)? cek **RUNBOOK.md**.


---
