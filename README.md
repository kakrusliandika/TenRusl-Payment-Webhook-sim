# 💳 TenRusl Payment Webhook Simulator

[![CI](https://github.com/kakrusliandika/tenrusl-payment-webhook-sim/actions/workflows/ci.yml/badge.svg)](https://github.com/kakrusliandika/tenrusl-payment-webhook-sim/actions)
![License](https://img.shields.io/github/license/kakrusliandika/tenrusl-payment-webhook-sim)
![Release](https://img.shields.io/github/v/release/kakrusliandika/tenrusl-payment-webhook-sim?include_prereleases&sort=semver)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Pest](https://img.shields.io/badge/Tests-Pest-18181B?logo=pestphp&logoColor=white)

Demo **Laravel 12** untuk memamerkan arsitektur payment yang rapi: **idempotency**, **dedup webhook**, **signature verification**, dan **exponential backoff retry (simulasi)** — tanpa kredensial gateway asli. Cocok untuk portfolio & pembelajaran praktik produksi.

> **Catatan**: Semua provider di mode *simulator*. Jangan memakai kredensial asli di repo.

---

## 🧭 Daftar Isi

- [Fitur](#-fitur)
- [Arsitektur Singkat](#-arsitektur-singkat)
- [Quick Start](#-quick-start)
- [Konfigurasi Lingkungan](#-konfigurasi-lingkungan)
- [Endpoint API](#-endpoint-api)
- [Webhook Signature (Simulasi)](#-webhook-signature-simulasi)
- [Swagger & Postman](#-swagger--postman)
- [Testing & CI](#-testing--ci)
- [Struktur Direktori](#-struktur-direktori)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## ✨ Fitur

- 🔐 **Idempotency** untuk `POST /payments` via header `Idempotency-Key`
- 🧬 **Dedup Webhook** berdasarkan `provider + event_id`
- 🔏 **Signature Verification** siap multi-provider:
  - **mock** (HMAC-SHA256 raw body + `MOCK_SECRET`)
  - **xendit** (`x-callback-token`)
  - **midtrans** (`signature_key`)
  - **stripe**, **paypal**, **paddle**, **lemonsqueezy**
  - **airwallex**, **tripay**, **doku**, **dana**, **oy**
  - **payoneer**, **skrill**, **amazon_bwp**
- 🔁 **Exponential Backoff Retry** (simulasi) untuk event gagal
- 📜 **OpenAPI (Swagger UI)** + 🗂️ **Postman collection & environment**
- 🧪 **Pest tests** lengkap (Feature + Unit) + ✅ **GitHub Actions CI**
- 🐳 **Docker dev** (opsional) & siap deploy (Render/Railway)

---

## 🧱 Arsitektur Singkat

```mermaid
flowchart TD
  A[Client] -->|Idempotency-Key| B[POST /api/v1/payments]
  B -->|create pending| P[(payments)]
  W[Provider] -->|POST /api/v1/webhooks/&#123;provider&#125;| C[WebhooksController]
  C -->|Verify Signature| S[SignatureVerifier]
  C -->|Dedup + Orkestrasi| R[WebhookProcessor]
  R -->|Update Status| P
  R -->|Retry Fail| Q[(webhook_events)]
```

- **PaymentsService**: create & status + idempotensi (TTL/Cache)
- **SignatureVerifier**: verifikasi per provider (header/token/HMAC/RSA)
- **WebhookProcessor**: dedup → update payment → set retry
- **RetryBackoff**: jeda eksponensial (dengan jitter, batas maksimum)

---

## 🚀 Quick Start

**Prasyarat**: PHP 8.3+, Composer, SQLite (untuk dev cepat), Git

```bash
git clone https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim.git
cd TenRusl-Payment-Webhook-sim

composer install

cp .env.example .env
php artisan key:generate

# SQLite (dev cepat)
mkdir -p database && touch database/database.sqlite
php artisan migrate

php artisan serve  # http://127.0.0.1:8000
```

**Swagger UI**: `http://127.0.0.1:8000/api/documentation`

> **Tip**: untuk uji *idempotency*, kirim `POST /api/v1/payments` dengan **header** `Idempotency-Key` yang sama, dua kali.

---

## 🔧 Konfigurasi Lingkungan

Semua kunci tersedia di `.env.example`. Ringkasan kunci penting:

| Kunci                        | Contoh        | Keterangan singkat |
|-----------------------------|---------------|--------------------|
| `MOCK_SECRET`               | `changeme`    | HMAC untuk provider **mock** |
| `XENDIT_CALLBACK_TOKEN`     | `changeme`    | Token callback **Xendit** |
| `MIDTRANS_SERVER_KEY`       | `changeme`    | *Server key* **Midtrans** |
| `STRIPE_WEBHOOK_SECRET`     | `...`         | Secret **Stripe** (HMAC) |
| `PAYPAL_ENV`                | `sandbox`     | Env PayPal |
| `PADDLE_SIGNING_SECRET`     | `...`         | Secret **Paddle** (baru) |
| `LS_WEBHOOK_SECRET`         | `...`         | Secret **Lemon Squeezy** |
| `AIRWALLEX_WEBHOOK_SECRET`  | `...`         | Secret **Airwallex** |
| `TRIPAY_PRIVATE_KEY`        | `...`         | Secret **Tripay** |
| `DOKU_CLIENT_ID`            | `...`         | Client id **DOKU** |
| `DOKU_SECRET_KEY`           | `...`         | Secret **DOKU** |
| `DANA_PUBLIC_KEY`           | `PEM`         | Public key **DANA** (RSA) |
| `OY_CALLBACK_SECRET`        | `...`         | Secret **OY!** *(opsional)* |
| `PAYONEER_SHARED_SECRET`    | `...`         | Secret **Payoneer** |
| `SKRILL_MD5_SECRET`         | `...`         | Secret **Skrill** |
| `AMZN_BWP_PUBLIC_KEY`       | `PEM`         | Public key **Amazon Buy with Prime** |

Konfigurasi dipetakan di `config/tenrusl.php` termasuk `providers_allowlist`:

```
mock, xendit, midtrans, stripe, paypal, paddle, lemonsqueezy,
airwallex, tripay, doku, dana, oy, payoneer, skrill, amazon_bwp
```

---

## 📡 Endpoint API

**Base URL**: `http://127.0.0.1:8000/api/v1`

| Method | Path                                       | Deskripsi                                           | Header Penting                     |
|:-----:|--------------------------------------------|-----------------------------------------------------|------------------------------------|
| POST  | `/payments`                                 | Buat payment *(idempotent)*                         | `Idempotency-Key: <uuid>`          |
| GET   | `/payments/{provider}/{provider_ref}/status`| Lihat status payment berdasarkan provider & referensi| –                                  |
| POST  | `/webhooks/{provider}`                      | Terima event webhook dari provider                  | Lihat tabel [Signature](#-webhook-signature-simulasi) |
| OPT   | `/webhooks/{provider}`                      | Preflight CORS                                       | –                                  |

**Contoh cURL**:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/payments \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: 123e4567-e89b-12d3-a456-426614174000" \
  -d '{"provider":"mock","amount":25000,"currency":"IDR","description":"Topup"}'
```

---

## 🔏 Webhook Signature (Simulasi)

| Provider        | Header/Metode                | Rumus singkat / Catatan                                                                 |
|----------------|------------------------------|-----------------------------------------------------------------------------------------|
| **mock**       | `X-Mock-Signature`           | `hex(hmac_sha256(raw_body, MOCK_SECRET))`                                               |
| **xendit**     | `x-callback-token`           | Harus sama dengan `XENDIT_CALLBACK_TOKEN`                                              |
| **midtrans**   | `signature_key`              | `sha512(order_id + status_code + gross_amount + MIDTRANS_SERVER_KEY)`                   |
| **stripe**     | `Stripe-Signature`           | HMAC SHA-256 atas `t.payload` (verifikasi timestamp + signature)                        |
| **paypal**     | Verify Webhook Signature     | Gunakan API PayPal (simulator menyiapkan struktur verify)                               |
| **paddle**     | `p_signature`/signing secret | Mode lama RSA / mode baru HMAC (simulator siap keduanya)                                |
| **lemonsqueezy**| `X-Signature`               | HMAC SHA-256 atas raw body                                                             |
| **airwallex**  | `x-timestamp` + `x-signature`| Base64(HMAC-SHA256(`timestamp + body`))                                                 |
| **tripay**     | `X-Callback-Signature`       | HMAC SHA-256 raw JSON body                                                             |
| **doku**       | `Signature` (+Digest, dll.)  | HMACSHA256=base64(...), memanfaatkan `Client-Id`,`Request-Id`,`Digest`,`Request-Target` |
| **dana**       | `X-SIGNATURE` (RSA)          | Verifikasi RSA-2048 SHA-256 atas raw body (pakai public key)                            |
| **oy**         | `X-OY-Signature`/whitelist   | Tergantung produk; dukungan signature/whitelist disiapkan                               |
| **payoneer**   | Header signature             | Tergantung produk; disediakan adapter & verifier dasar                                  |
| **skrill**     | `md5sig` (form encoded)      | MD5 gabungan field IPN                                                                  |
| **amazon_bwp** | `x-amzn-signature` (RSA)     | Verifikasi tanda tangan dengan public key                                               |

> **Catatan**: beberapa provider punya variasi dan *environment specific*. Di simulator, verifikasi difokuskan di *header presence/structure* + HMAC/RSA generik untuk edukasi.

---

## 📜 Swagger & 📨 Postman

- **Swagger UI**: `http://127.0.0.1:8000/api/documentation`
  Output generator di `storage/api-docs/openapi.yaml|json`.
- **Postman**: impor berkas berikut:
  - `postman/TenRusl-Payment-Sim.postman_collection.json`
  - `postman/TenRusl-Local.postman_environment.json`

**Fitur Postman**:
- Auto generate `Idempotency-Key`
- Auto HMAC `X-Mock-Signature`
- Inject `x-callback-token` (Xendit)
- (Opsional) Hitung `signature_key` Midtrans

---

## 🧪 Testing & ✅ CI

```bash
php artisan test        # semua test (Pest)
php artisan test --unit # unit saja
php artisan test --testsuite=Feature
```

- **Feature**: Payments + Webhooks (semua provider) + Retry.
- **Unit**: IdempotencyKeyService, RetryBackoff, SignatureVerifier, Bootstrap config.
- **CI**: GitHub Actions menjalankan composer install → migrate (SQLite) → test.

---

## 🗂️ Struktur Direktori (ringkas)

```
app/
  Console/Commands/RetryWebhookCommand.php
  Http/Controllers/Api/V1/{PaymentsController,WebhooksController}.php
  Http/Middleware/{CorrelationIdMiddleware,VerifyWebhookSignature}.php
  Http/Requests/Api/V1/{CreatePaymentRequest,WebhookRequest}.php
  Http/Resources/Api/V1/{PaymentResource,WebhookEventResource}.php
  Models/{Payment,WebhookEvent}.php
  Services/
    Idempotency/{IdempotencyKeyService,RequestFingerprint}.php
    Payments/Adapters/*.php
    Payments/{PaymentsService}.php
    Signatures/*.php
    Webhooks/{RetryBackoff,WebhookProcessor}.php
  ValueObjects/PaymentStatus.php
config/tenrusl.php
routes/{api,web,console}.php
docs/{openapi.yaml,architecture.md,decisions/0001-idempotency.md}
postman/{TenRusl-Payment-Sim.postman_collection.json,TenRusl-Local.postman_environment.json}
tests/{Feature,Unit,CreatesApplication.php,Pest.php,TestCase.php}
```

---

## 🛠️ Operasional

- **Retry sekali jalan**: `php artisan tenrusl:webhooks:retry-once`
- **Retry periodik (cron)**: jalankan scheduler `php artisan schedule:work` lalu daftarkan jadwal di `app/Console/Kernel.php`.

---

## 🛟 Troubleshooting

- **Swagger UI 404 / fetch error**
  Pastikan `l5-swagger` mengeluarkan file ke `storage/api-docs` dan route `/api/documentation` aktif.

- **Webhook 401 (Mock/Stripe/etc.)**
  Hitung signature dari **raw body** yang benar-benar terkirim. Postman sudah menyediakan *pre-request script*.

- **Gagal migrate SQLite:**
  Buat file `database/database.sqlite`, set `.env`: `DB_CONNECTION=sqlite`, lalu `php artisan migrate`.

- **Intelephense false positive (Laravel Request methods)**
  Abaikan peringatan seperti `Undefined method input/merge/route` jika test tetap *green* — itu limite plugin.

---

## 📦 Rilis

`v1.0.1` — Payments API (idempotent), webhook receiver multi-provider, retry simulasi, OpenAPI, Postman, Pest, CI.

---

## 📝 Lisensi

MIT © TenRusl - Andika Rusli
