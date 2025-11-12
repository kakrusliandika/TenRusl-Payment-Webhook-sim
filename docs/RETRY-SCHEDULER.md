# 🔁 Webhook Retry Scheduler

Dokumen ini menjelaskan bagaimana **retry** event webhook diproses secara terjadwal menggunakan command:

```
php artisan tenrusl:webhooks:retry       --provider=mock       --limit=100       --max-attempts=5       --mode=full
```

## Apa yang Dilakukan?

- Menemukan event `webhook_events` yang:
  - `payment_status` belum `succeeded` (atau masih `pending`),
  - `next_retry_at` sudah lewat,
  - `attempts` masih di bawah batas `--max-attempts`.
- Menghitung **exponential backoff + jitter** (mode `full|equal|decorrelated`), lalu menjadwalkan ulang `next_retry_at`.
- Mencatat `attempts`, `last_attempt_at`, `processed_at` (bila sukses), serta `payment_status`.

> **Catatan:** Field yang dipakai:
>
> - `attempts`, `last_attempt_at`, `processed_at`, `payment_status`, `next_retry_at`
> - Pastikan migrasi alter `webhook_events` sudah dijalankan.

---

## Menjalankan Lokal (Manual)

1. **Siapkan .env & DB**

   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

2. **Jalankan Retry**

   ```bash
   php artisan tenrusl:webhooks:retry --limit=100 --max-attempts=5 --mode=full
   # opsional: --provider=mock
   ```

3. **Amati Log**
   - Periksa output terminal dan tabel `webhook_events` untuk perubahan `attempts`, `next_retry_at`, dan `payment_status`.

---

## Menjadwalkan via Cron (Linux)

Tambahkan entri crontab (mis. tiap 15 menit):

```cron
*/15 * * * * cd /path/to/project && php artisan tenrusl:webhooks:retry --limit=100 --max-attempts=5 --mode=full >> storage/logs/retry.log 2>&1
```

**Tips:**

- Gunakan `--provider=xendit` atau `--provider=mock` untuk memfilter provider tertentu.
- Sesuaikan `--max-attempts` agar tidak agresif.

---

## Menjadwalkan via GitHub Actions

Repo ini sudah menyertakan workflow:

```
.github/workflows/retry-schedule.yml
```

- Default: berjalan **tiap 15 menit** (`cron: "*/15 * * * *"`).
- Menggunakan **SQLite ephemeral** untuk contoh. Di produksi, jalankan di server aplikasi agar memakai DB yang sama dengan aplikasi.

Override variabel lewat **workflow dispatch**:

- `RETRY_LIMIT`, `RETRY_MAX_ATTEMPTS`, `RETRY_BACKOFF_MODE` (`full|equal|decorrelated`), `RETRY_PROVIDER`.

---

## Integrasi Laravel Scheduler (Opsional)

Jika ingin memanfaatkan `app/Console/Kernel.php`:

```php
$schedule->command('tenrusl:webhooks:retry --limit=100 --max-attempts=5 --mode=full')->everyFifteenMinutes();
```

Lalu jalankan cron tunggal:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Best Practices

- **Idempoten**: Pastikan processor aman terhadap duplikasi (sudah ditangani oleh repository & dedup).
- **Backoff**: Pilih `full` jitter untuk distribusi beban terbaik.
- **Observability**: Tambahkan logging terstruktur/Sentry bila diperlukan.
- **Limit konservatif**: Mulai dari `--limit=100`, naikkan bertahap sesuai throughput.

---
