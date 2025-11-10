# ADR 0001 — Idempotency untuk Create Payment & Webhook Processing

**Status**: Accepted
**Tanggal**: 2025-11-09

## Konteks

Operasi pembayaran rentan terhadap retry akibat jaringan/timeouts. Tanpa mekanisme idempoten, retry berisiko membuat duplikasi transaksi atau mengubah state lebih dari sekali. Webhook dari penyedia pembayaran juga sering dikirim ulang (delivery-at-least-once), sehingga handler harus aman dieksekusi berulang.

## Keputusan

1) **Request Idempotency-Key**
- API `POST /api/v1/payments` menerima header `Idempotency-Key`.
- Bila header tidak dikirim, server membangkitkan **request fingerprint** deterministik dari `(method, path, headers penting, body)`.
- Server menyimpan **response pertama** untuk key tsb (status, headers, body) selama `TENRUSL_IDEMPOTENCY_TTL` detik.
- Request paralel dengan key sama ditolak dengan `409 Idempotency conflict` (lock).

2) **Cache & Lock**
- Menggunakan cache driver Laravel (default file) untuk:
  - **Lock key**: mencegah eksekusi ganda paralel.
  - **Stored response**: replay hasil pertama untuk retry selanjutnya dalam window TTL.

3) **Webhook Idempotency**
- Tabel `webhook_events` melakukan deduplikasi berbasis `(provider, event_id)`.
- Payload mentah (`payload_raw`) disimpan untuk audit/verifikasi signature.
- Event id kosong → digenerate (ULID) agar tetap dapat didedup.
- Mapping status generik: `succeeded|failed|pending` (lihat `PaymentStatus`).

4) **Retry Backoff**
- Simulasi retry memakai **exponential backoff + jitter** (mode default: full jitter).
- Konfigurasi:
  - `TENRUSL_MAX_RETRY_ATTEMPTS` (default 5)
  - base delay 500 ms, cap 30 s
- Jadwal retry disimpan di kolom `next_retry_at`; ada command `tenrusl:webhooks:retry` & scheduler minutely.

## Konsekuensi

- **Pro**: repeatable, aman, deterministic; cocok untuk demo & produksi.
- **Kontra**: membutuhkan storage cache; perlu disiplin penggunaan key; TTL harus disetel realistis.
- **Operasional**: observer/monitoring memeriksa metrik hit lock, replay, dan jumlah retry.

## Detail Implementasi

- Service: `IdempotencyKeyService` (resolve key, lock, store response, replay).
- Fingerprint: `RequestFingerprint` (kanonisasi body JSON & header).
- Webhook: `WebhookProcessor` (dedup, persist, infer status, schedule retry).
- Backoff: `RetryBackoff` (full/equal/decorrelated jitter).

## Alternatif yang Dipertimbangkan

- Menyimpan idempotensi di DB (row-level locks). Ditolak untuk demo karena overhead & dependency DB; cache sudah cukup.
- Mengharuskan client selalu mengirim key. Ditolak; fallback fingerprint mempermudah pengujian via Postman/CLI.

## Referensi
- Stripe — konsep & praktik Idempotency-Key untuk retry aman. :contentReference[oaicite:0]{index=0}
- AWS — Exponential backoff **with jitter** (preferred). :contentReference[oaicite:1]{index=1}
