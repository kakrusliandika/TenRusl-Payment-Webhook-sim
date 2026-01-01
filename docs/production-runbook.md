# Production Runbook — TenRusl Payment Webhook Simulator

Dokumen ini fokus ke operasional harian: konfigurasi, rotasi secret, handling backlog, retensi payload, dan scaling.

---

## 1) Environment variables wajib (minimum)

### A. Aplikasi

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:...` **wajib** (jangan generate otomatis di runtime)
- `APP_URL=https://...` (opsional tapi disarankan untuk URL generation)

### B. Database

Sesuaikan dengan environment:

- `DB_CONNECTION=mysql|pgsql`
- `DB_HOST=...`
- `DB_PORT=...`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

### C. Redis (multi-instance safe)

Minimal untuk lock + idempotency cache:

- `REDIS_HOST=...`
- `REDIS_PORT=6379`
- `REDIS_PASSWORD=...` (jika pakai)
- `CACHE_STORE=redis` ✅ wajib untuk distributed lock

Disarankan juga:

- `QUEUE_CONNECTION=redis`
- `SESSION_DRIVER=redis`

### D. Admin protection (demo / internal ops)

- `TENRUSL_ADMIN_DEMO_KEY=...` atau `ADMIN_DEMO_KEY=...`
  - Gunakan key kuat (random panjang).

### E. Webhook secrets (contoh)

Nama env menyesuaikan implementasi middleware/verifier kamu:

- `TENRUSL_WEBHOOK_SECRET_XENDIT=...`
- `TENRUSL_WEBHOOK_SECRET_MIDTRANS=...`
- `TENRUSL_WEBHOOK_SECRET_MOCK=...`

> Prinsip: jangan taruh secret di repo, selalu via secret manager/platform.

---

## 2) Rotasi secret webhook (tanpa downtime)

Tujuan: menerima signature lama dan baru selama window rotasi.

Langkah aman:

1. Tambahkan secret baru di secret manager (versi N+1).
2. Deploy konfigurasi verifier agar menerima **(N dan N+1)** selama masa transisi.
3. Update konfigurasi provider agar mulai menandatangani dengan secret baru (N+1).
4. Monitor error signature (rate 4xx/401) selama 15–60 menit.
5. Setelah stabil, deploy lagi untuk menolak secret lama (N).

Catatan:

- Simpan timestamp rotasi dan owner yang melakukan rotasi.
- Jika provider tidak mendukung dual secret, jadwalkan cutover singkat dan pasang monitoring lebih ketat.

---

## 3) Prosedur menangani backlog webhook

### Gejala

- `webhook_events` banyak status `received/failed` dengan `payment_status=pending`.
- `attempts` naik mendekati `max_attempts`.
- Latency processing naik, atau terjadi timeouts.

### Checklist tindakan

1. **Pastikan penyebab utama**
   - DB / Redis latency?
   - Provider mengirim burst?
   - Worker mati / queue stuck?
2. **Naikkan kapasitas worker**
   - Tambah replika worker (queue: redis).
   - Pastikan web layer tidak ikut memproses heavy job.
3. **Jalankan retry batch**
   - `php artisan tenrusl:webhooks:retry --limit=500 --max-attempts=5 --mode=full --queue`
4. **Jika stuck/overlap**
   - Pastikan `CACHE_STORE=redis` agar global lock aktif.
   - Periksa `next_retry_at` apakah jauh di masa depan (lease terlalu panjang).
5. **Jika backlog ekstrem**
   - Pertimbangkan throttle ingress (Nginx `limit_req`, atau API gateway rate limit).
   - Terapkan “dead letter” policy: event > max attempts dipindah/ditandai untuk investigasi manual.

---

## 4) Kebijakan retensi `payload_raw`

`payload_raw` membantu audit dan verifikasi signature, tapi berpotensi mengandung data sensitif.

Rekomendasi:

- Default retensi: **7–30 hari**, tergantung kebutuhan audit.
- Setelah lewat retensi:
  - Hapus `payload_raw` (dan/atau `payload`) atau
  - Replace dengan hash ringkas + metadata minimal.
- Bila harus disimpan lebih lama:
  - Enkripsi at-rest (DB encryption / column encryption),
  - Batasi akses (least privilege),
  - Audit akses ke tabel webhook.

---

## 5) Rencana scaling (web vs worker)

### Prinsip

- Web/API harus stateless.
- Processing webhook berat pindahkan ke worker queue (redis).
- Scheduler terpisah (platform cron / GH Actions / managed scheduler).

### Pola rekomendasi

- **web**: Nginx + PHP-FPM, hanya menerima request & enqueue
- **worker**: queue worker (redis), autoscale berdasarkan queue depth
- **scheduler**:
  - platform scheduler memanggil `tenrusl:webhooks:retry`
  - atau GH Actions schedule (dengan concurrency) bila tidak ada cron

### Multi-instance safety

- Lock idempotency & retry harus distributed (Redis).
- Hindari file-based cache/lock di production.

---

## 6) Operasional rutin

- Review harian:
  - jumlah `failed` dan `received` yang due
  - rata-rata attempts
  - puncak traffic webhook
- Review mingguan:
  - rotasi secret (jika kebijakan mengharuskan)
  - retensi payload_raw (purge job)
- Saat incident:
  - simpan sample payload + header subset + source IP (tanpa secret)
  - korelasikan dengan `X-Request-ID` dari middleware

---
