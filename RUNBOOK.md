# RUNBOOK — TenRusl Payment Webhook Simulator (Production Ops)

Dokumen ini dibuat supaya operasional “webhook + retry” tetap kalem walau kondisi production lagi ramai. Fokusnya: **scheduler hidup**, **retry nggak storm**, **queue nggak numpuk**, dan **tracing kejadian cepat ketemu**.

> Repo ini simulator, tapi pola ops-nya dibuat realistis untuk sistem webhook/payment.

---

## 0) Ringkasan cepat (buat on-call yang baru bangun tidur 😄)

Kalau ada masalah, urutan cek paling cepat:

1. **Health**: `GET /up` harus 200/204 (tergantung platform).
2. **Scheduler**: `php artisan schedule:list` → event retry harus ada.
3. **Scheduler trigger**: pastikan ada proses yang menjalankan `php artisan schedule:run` tiap menit (cron / Render cron service).
4. **Queue** (kalau mode queue aktif): worker hidup + queue `webhooks` nggak numpuk.
5. **Logs**: cari `request_id` / `webhook_event_id` untuk trace.

---

## 1) Komponen layanan (wajib vs opsional)

**Minimal supaya retry otomatis jalan:**

- **Web service** (Laravel app)
- **Database** (Postgres/MySQL recommended; SQLite hanya demo)
- **Scheduler trigger per menit** menjalankan `php artisan schedule:run --no-interaction`
- **Cache store shared untuk lock** (Redis recommended) bila memakai `withoutOverlapping()` / `onOneServer()`

**Opsional (recommended):**

- **Queue worker** untuk memproses retry lewat queue (sangat recommended untuk production)
- **Horizon** (Redis queue monitoring) kalau kamu ingin dashboard queue

---

## 2) “Kontrak” health check (satu pintu, biar konsisten)

**Endpoint resmi:** `GET /up`

Kenapa penting: platform (Render/Railway/K8s) butuh 1 endpoint yang konsisten buat healthcheck. Hindari duplikasi `/health` vs `/up` kecuali salah satunya benar-benar alias yang jelas.

**Ekspektasi respon:**

- 200 atau 204 (no content) itu sama-sama OK — yang penting cepat dan stabil.

---

## 3) Observability minimal (yang wajib ada biar gampang debug)

### 3.1 Correlation / Request ID

Header canonical: **`X-Request-ID`**

Aturan praktis:

- Kalau upstream sudah kirim `X-Request-ID`, kita propagate.
- Kalau tidak ada, sistem generate ULID dan tempel di request+response.
- Semua log harus punya field `request_id`.

Cara pakai di incident:

- Ambil `X-Request-ID` dari response / gateway log.
- Cari di log aggregator: `request_id=<nilai>`

### 3.2 Log yang “enak diolah”

Recommended: **JSON logs** ke `stderr` (paling cocok buat ELK/Datadog/Grafana/Loki).
Pisahkan log webhooks (kalau pakai channel khusus) supaya noise log web biasa tidak ganggu.

### 3.3 Metrics yang ideal dipantau

Kalau belum punya metrics stack, minimal pantau dari DB + logs.

**Golden signals untuk webhook:**

- **Ingress rate**: jumlah request webhook / menit
- **Failure rate**: 4xx/5xx webhook, signature invalid
- **Queue depth**: panjang queue `webhooks`
- **Processing latency**: p95 durasi job / processing (kalau ada)
- **Retry pressure**: jumlah “claimed retry” / run, jumlah `attempts` yang naik per menit

---

## 4) “Lampu kuning” & alert yang masuk akal

Kamu bisa mulai dari rule sederhana:

- **Queue backlog**: queue `webhooks` > 1.000 job selama > 10 menit
- **Failure spike**: webhook 5xx > 1% selama 5 menit
- **Signature invalid**: 401/403 naik tajam tiba-tiba
- **Retry storm**: `CLAIMED` per run naik drastis + provider latency tinggi
- **DB slow**: p95 query > 200ms (atau error koneksi/timeout muncul)

---

## 5) SOP Incident (playbook)

### A) Lonjakan retry (retry storm)

**Gejala:**

- Log retry command nunjukin `CLAIMED` tinggi terus.
- Queue `webhooks` numpuk cepat.
- Provider sering timeout/5xx.

**Langkah cepat (urut):**

1. Pastikan ini bukan “scheduler double-run”:
   - Cek apakah ada lebih dari 1 scheduler trigger aktif.
   - Pastikan lock store shared (Redis) untuk `onOneServer()` / `withoutOverlapping()`.
2. Kurangi tekanan sementara (pilih salah satu):
   - Turunkan limit scheduler (mis. `TENRUSL_SCHEDULER_LIMIT=100` → 50)
   - Naikkan `TENRUSL_RETRY_BASE_MS` dan/atau `TENRUSL_RETRY_CAP_MS`
   - Pakai mode jitter yang lebih “nyebar”: `TENRUSL_SCHEDULER_BACKOFF_MODE=decorrelated`
3. Kalau provider benar-benar down:
   - Stop sementara retry processor (paling aman: pause cron / scheduler service).
4. Setelah provider pulih:
   - Nyalakan lagi scheduler, tapi scale worker bertahap (jangan langsung gas pol).

**Catatan penting:**

- Kalau kamu pakai queue, scaling worker itu “gas pedal” utama. Naikkan pelan-pelan sambil pantau DB/Redis.

---

### B) Signature failures (401/403 meledak)

**Gejala:**

- Banyak webhook ditolak.
- Log signature verifier menunjukkan mismatch.

**Langkah cepat:**

1. Pastikan secret/token tidak berubah tanpa sync.
2. Pastikan request yang diverifikasi memakai **raw body** (bukan hasil json_encode ulang).
3. Cek clock drift (provider yang pakai timestamp leeway):
   - Pastikan server time benar (NTP/managed platform biasanya aman).
4. Jika ada gateway/proxy yang mengubah body (jarang, tapi mungkin):
   - Pastikan tidak ada middleware yang memodifikasi body sebelum verifier.

**Recovery:**

- Setelah secret benar, request baru harus lolos. Untuk event yang sudah tersimpan “failed”, biarkan retry engine proses yang due (atau manual retry per event id).

---

### C) Backlog queue (job numpuk)

**Gejala:**

- `queue depth` naik dan tidak turun.
- Worker log sepi atau error terus.

**Langkah cepat:**

1. Pastikan worker hidup:
   ```bash
   php artisan queue:work --queue=webhooks
   ```
2. Cek failed jobs:
   ```bash
   php artisan queue:failed
   ```
3. Kalau banyak failed karena error sementara:
   ```bash
   php artisan queue:retry all
   ```
4. Kalau job double-running:
   - Pastikan `retry_after` > `timeout` worker + buffer.
   - Pastikan hanya satu sistem yang memproses queue (jangan ada worker dobel dari deploy lama).

---

### D) Database lambat / deadlock / timeouts

**Gejala:**

- Banyak error DB timeout.
- Retry command/worker melambat.

**Langkah cepat:**

1. Kurangi concurrency:
   - Turunkan jumlah worker atau queue concurrency.
2. Kurangi beban retry:
   - Turunkan scheduler limit.
3. Cek koneksi DB & pool (platform managed biasanya ada metrics).
4. Jika deadlock muncul:
   - Pastikan transaksi pendek (di sini claiming pakai transaksi singkat, itu bagus).
   - Pastikan index untuk query due (mis. `next_retry_at`, `status`, `attempts`).

---

### E) Redis down / unstable

**Gejala:**

- Locks tidak jalan (overlap/singleton jadi kacau).
- Queue error / connection refused.

**Langkah cepat:**

1. Konfirmasi Redis:
   ```bash
   redis-cli -h $REDIS_HOST -p $REDIS_PORT ping
   ```
2. Kalau Redis mati total:
   - Scheduler sebaiknya **dipause** (biar nggak bikin chaos).
   - Web bisa tetap jalan (tergantung kebutuhan), tapi idempotency/locks/queue akan terdampak.
3. Setelah Redis pulih:
   - Nyalakan scheduler, lalu worker bertahap.

---

## 6) Verifikasi harian (simple checklist)

1. Schedule terdaftar:

```bash
php artisan schedule:list
```

2. Scheduler bisa jalan manual:

```bash
php artisan schedule:run -vvv
```

3. Worker sehat (kalau queue):

```bash
php artisan queue:work --queue=webhooks --once
```

4. Health endpoint OK:

```bash
curl -i https://<host>/up
```

---

## 7) Operasi manual (ops tools)

### Retry sekali (batch)

```bash
php artisan tenrusl:webhooks:retry-once
```

### Retry satu event (admin “retry now”)

```bash
php artisan tenrusl:webhooks:retry-one <EVENT_ID> --force
```

### Clear scheduler overlap locks (kalau nyangkut)

```bash
php artisan schedule:clear-cache
```

---

## 8) Post-incident checklist (biar kejadian nggak keulang)

- [ ] Catat timeline + `request_id` contoh yang representatif
- [ ] Identifikasi root cause: provider outage / misconfig / infra
- [ ] Tuning knobs (limit, base/cap, mode jitter) sesuai beban nyata
- [ ] Tambah alert untuk gejala yang telat ketahuan
- [ ] Kalau queue dipakai, pertimbangkan Horizon dashboard

---

## 9) Timezone notes (biar tidak salah paham jadwal)

Banyak cron platform pakai **UTC**. Untuk WIB (UTC+7):

- 09:00 WIB = 02:00 UTC
