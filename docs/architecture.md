# TenRusl Payment Webhook Simulator — Architecture

## Ringkasan

Aplikasi demo **Laravel 11/12** untuk memamerkan arsitektur pembayaran produksi:

- **Idempotency** pada `POST /payments`
- **Webhook dedup** + **signature verification**
- **Exponential backoff retry (simulasi)**
- **OpenAPI (Swagger UI)** & **Postman collection**
- **Pest tests & CI**

## Tujuan Teknis

1. Tahan terhadap **double submit** (idempotency).
2. Webhook **aman** (signature) dan **idempotent** (dedup event).
3. **Dapat dipantau** (structured logging + correlation id).
4. **Mudah diuji** (Pest unit/feature).
5. **Mudah di-deploy** (Docker / host apa saja).

## Komponen

- **API Layer**
  Controllers, Requests (validasi), Resources (serializer).
- **Domain Services**
  PaymentsService, IdempotencyKeyService, WebhookProcessor, RetryBackoff, SignatureVerifier (+ provider adapters).
- **Repositories**
  PaymentRepository, WebhookEventRepository.
- **Infra**
  DB MySQL/SQLite, Redis (opsional), queue (opsional), Nginx (Docker).

## Aliran Utama

### Create Payment (idempotent)

Client
→ POST /payments (Idempotency-Key)
→ PaymentsService → IdempotencyKeyService (cek/simpan snapshot)
→ PaymentRepository (persist pending)
← 201 Payment + idempotency snapshot

shell
Salin kode

### Webhook Processing (dedup + signature)

Gateway
→ POST /webhooks/{provider} (header signature/token)
→ VerifyWebhookSignature (middleware)
→ WebhookProcessor:

- Dedup (provider+event_id)
- Update Payment status (paid/failed)
- Jika gagal → schedule retry (next_retry_at, attempt_count)
  ← 200 processed (atau 409 duplicate)

shell
Salin kode

### Retry Simulation (exponential backoff)

Scheduler/Command
→ Scan webhook_events where status=failed AND next_retry_at <= now
→ Re-process with backoff 1s, 2s, 4s, 8s, 16s (maks 5)

bash
Salin kode

## Model Data

- **payments**: id, amount, currency, description, metadata(json), status(enum), idempotency_key(unique), timestamps
- **webhook_events**: id, provider, event_id(unique per provider), signature_hash, payload(json), status, attempt_count, next_retry_at, error_message, timestamps

## Keamanan

- Validasi request (FormRequest).
- Signature per provider: Mock(HMAC), Xendit(token), Midtrans(signature-key).
- Correlation ID di setiap request & log.

## Observability

- Struktur log: request_id, provider, event_id, payment_id, outcome.
- Jalur error mencatat error_message pada `webhook_events`.

## CI / CD

- GitHub Actions: install → key:generate → DB sqlite → migrate → test.
- Badge di README.

## Batasan

- Demo; tidak menggunakan kredensial gateway asli.
- Retry disimulasikan via command/scheduler (tanpa queue prod).

## Roadmap

- Queue nyata (Redis/Horizon), healthcheck & metrics, lock idempotensi via Redis, integrasi sandbox Xendit/Midtrans.
