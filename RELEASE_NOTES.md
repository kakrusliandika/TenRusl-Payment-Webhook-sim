# Release Notes — v1.0.0-rc.1

**Tanggal:** 9 Juli 2026
**Status:** Release Candidate 1
**Tag:** `v1.0.0-rc.1`

---

## 🎯 Ringkasan

Release Candidate pertama untuk **TenRusl Payment Webhook Simulator** — platform simulasi payment gateway dengan 15 provider adapter, webhook system, admin panel API, dan integrasi frontend React.

---

## ✨ Fitur Utama

### Payment Simulation Engine
- REST API untuk membuat dan melacak payment: `POST /api/payments`, `GET /api/payments/{id}`, `GET /api/payments/{provider}/{providerRef}/status`
- 15 payment provider adapters: mock, xendit, midtrans, stripe, paypal, paddle, lemonsqueezy, airwallex, tripay, doku, dana, oy, payoneer, skrill, amazon_bwp
- Idempotency key support — mencegah duplicate payment creation
- Metadata normalization: canonical `meta` field, legacy `metadata` alias

### Webhook Simulator
- Receive webhooks dari 15 provider: `POST /api/webhooks/{provider}`
- Signature validation per provider
- Duplicate detection dan dead-letter queue
- Admin retry: `POST /api/admin/webhooks/{id}/retry`

### Admin Panel API
- Health checks: `/api/admin/health`, `/api/admin/health/database`
- Metrics: `/api/admin/metrics`
- Payment management: `/api/admin/payments`
- Webhook management: `/api/admin/webhooks`, `/api/admin/webhooks/dead-letter`
- Simulation controls: mark paid/failed/expired, send webhook
- All endpoints di `/api/admin/*` dan `/api/v1/admin/*`

### Security
- Security headers (nosniff, sameorigin, strict referrer, permissions policy)
- CORS dengan explicit allowed origins dan exposed rate limit headers
- Rate limiting on public dan admin endpoints
- Request ID tracking

### Infrastructure
- Redis support: cache, rate limiter, idempotency store, queue, session
- Docker production image (multi-stage + supervisor + nginx)
- Fail2ban integration
- Comprehensive Makefile targets

---

## ⚠️ Breaking Changes

**None** — ini adalah initial release.

---

## 🗄️ Database Migration

```bash
# Fresh install
php artisan migrate --force

# Existing database
php artisan migrate --force
```

**Safety notes:**
- Backup database sebelum migrate: `php artisan db:backup` (jika tersedia) atau `mysqldump`
- Test migration di staging environment terlebih dahulu
- Lihat `docs/DATABASE_RUNBOOK.md` untuk prosedur lengkap

---

## 🔧 Environment Variables

### Required
| Variable | Description | Example |
|----------|-------------|---------|
| `APP_KEY` | Laravel app key | `base64:...` |
| `DB_*` | Database connection | See `.env.example` |
| `TENRUSL_ADMIN_KEY` | Admin API auth key | `your-secret-key` |

### Production Required
| Variable | Description | Production Value |
|----------|-------------|------------------|
| `CACHE_STORE` | Cache driver | `redis` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `SESSION_DRIVER` | Session driver | `redis` |
| `REDIS_CLIENT` | Redis client | `phpredis` |

### Optional
| Variable | Description | Default |
|----------|-------------|---------|
| `TENRUSL_ADMIN_HEADER` | Admin auth header | `Authorization` |

---

## 🚀 Deployment Checklist

1. **Install dependencies:** `composer install --no-dev --optimize-autoloader`
2. **Build frontend:** `npm ci && npm run build`
3. **Cache config:** `php artisan config:cache && php artisan route:cache`
4. **Run migrations:** `php artisan migrate --force`
5. **Set Redis:** `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`
6. **Enable HSTS:** Set `hsts.enable=true` di `config/secure-headers.php`
7. **Enable CSP:** Set `csp.enable=true` di `config/secure-headers.php` (gradual)
8. **Verify health:** `curl /api/admin/health`
9. **Verify CORS:** Test dari frontend origin

---

## 🔄 Rollback Plan

Jika terjadi masalah setelah deploy:

### Quick Rollback (< 5 menit)
```bash
# 1. Restore previous deployment artifact
cp -r /path/to/backup/v1.0.0-beta/* /path/to/current/

# 2. Restore config cache
php artisan config:cache

# 3. Rollback database (jika ada migration baru)
php artisan migrate:rollback --force

# 4. Restart services
supervisorctl restart all
# atau
php artisan queue:restart
```

### Database Rollback
```bash
# Check migration status
php artisan migrate:status

# Rollback last N batches
php artisan migrate:rollback --force

# Rollback specific migration
php artisan migrate:rollback --step=1 --force
```

### Docker Rollback
```bash
# Revert to previous image
docker-compose down
docker-compose up -d --force-recreate <previous-image-tag>
```

---

## 🐛 Known Issues

| Issue | Severity | Workaround |
|-------|----------|------------|
| Redis not default in `.env.example` | LOW | Set `CACHE_STORE=redis` manually |
| CSP disabled by default | LOW | Enable gradually per directive |
| HSTS disabled by default | LOW | Enable in production with SSL |
| `pdo_sqlite` not available | LOW | Use MySQL/PostgreSQL |

---

## 📦 Release Artifact

Artifact bersih dari file sensitif:

| File | Status |
|------|--------|
| `.env` | ❌ Tidak ada |
| `vendor/` | ❌ Tidak ada (install via `composer install`) |
| `node_modules/` | ❌ Tidak ada (install via `npm install`) |
| `storage/logs/` | ❌ Tidak ada |
| `storage/framework/cache/` | ❌ Tidak ada |
| `database/*.sqlite*` | ❌ Tidak ada |
| `coverage/` | ❌ Tidak ada |
| `.phpstan/` | ❌ Tidak ada |

**Validate artifact:** `make artifact-check`

---

## 🧪 QA Status

| Kategori | PASS | FAIL | BLOCKED |
|----------|------|------|---------|
| Backend Boot | 7 | 2 | 16 |
| API Contract | 20 | 1 | 5 |
| Frontend Integration | 23 | 0 | 12 |

**Detail:** See `docs/QA_FINAL_REPORT.md`, `docs/QA_CHECKLIST.md`

---

## 📚 Dokumentasi

- `docs/openapi.yaml` — OpenAPI 3.1 specification
- `docs/API.md` — API usage guide
- `docs/DEPLOYMENT.md` — Deployment guide
- `docs/SECURITY.md` — Security documentation
- `docs/ENVIRONMENT.md` — Environment variables
- `docs/RUNBOOK.md` — Operations runbook
- `docs/DATABASE_RUNBOOK.md` — Database procedures
- `docs/PROVIDERS.md` — Provider capability matrix

---

## 🙏 Acknowledgments

Built with Laravel 12, Pest 3, PHPStan/Larastan, React 19, TypeScript 6, Vite 7.