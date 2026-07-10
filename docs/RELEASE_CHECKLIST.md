# 🚀 Release Checklist — v1.0.0-rc.1

> **Tanggal:** 9 Juli 2026
> **Release Manager:** Senior Release Manager
> **Status:** CHECKLIST READY — Execute before tagging

---

## 📋 Pre-Release Checklist

### 1. Code Quality ✅

| # | Check | Command | Status | Notes |
|---|-------|---------|--------|-------|
| 1 | `composer.json` valid | `composer validate --no-check-publish --strict` | ✅ PASS | `./composer.json is valid` |
| 2 | PHP lint — no syntax errors | `composer lint:php` | ✅ PASS | No syntax errors detected |
| 3 | Pint — code style | `composer format:check` | ⏸️ PENDING | Run: `vendor/bin/pint --test` |
| 4 | PHPStan/Larastan | `composer analyse:larastan` | ⏸️ PENDING | Run: `vendor/bin/phpstan analyse -c phpstan.neon` |
| 5 | Pest — unit & feature tests | `composer test` | ⏸️ PENDING | Requires database |
| 6 | OpenAPI contract tests | `composer test:contract` | ⏸️ PENDING | Run: `pest tests/Unit/OpenApiContractTest.php` |
| 7 | OpenAPI lint | `npm run openapi:lint` | ⏸️ PENDING | Run: `redocly lint docs/openapi.yaml` |
| 8 | Frontend lint | `npm run frontend:lint` | ⏸️ PENDING | Run: `node scripts/frontend-quality.mjs lint` |
| 9 | Frontend typecheck | `npm run frontend:typecheck` | ⏸️ PENDING | Run: `node scripts/frontend-quality.mjs typecheck` |
| 10 | Frontend build | `npm run frontend:build` | ⏸️ PENDING | Run: `vite build` |
| 11 | Composer audit | `composer audit --locked` | ⏸️ PENDING | Security audit of dependencies |
| 12 | Shell script syntax | `composer lint:shell` | ⏸️ PENDING | `sh -n start.sh start-postgres.sh scripts/release.sh` |

**Run all at once:**
```bash
make qa
```

---

### 2. Artifact Hygiene ✅

| # | Check | Command | Status | Notes |
|---|-------|---------|--------|-------|
| 13 | No `.env` in git | `git ls-files .env` | ✅ PASS | Not tracked |
| 14 | No secrets in code | `make secret-check` | ⏸️ PENDING | AWS keys, private keys, webhook secrets |
| 15 | No database files | `git ls-files database/*.sqlite*` | ✅ PASS | Not tracked |
| 16 | No vendor dir | `git ls-files vendor/` | ✅ PASS | Not tracked |
| 17 | No node_modules | `git ls-files node_modules/` | ✅ PASS | Not tracked |
| 18 | No cache files | `git ls-files bootstrap/cache/*.php` | ✅ PASS | Not tracked |
| 19 | No logs | `git ls-files storage/logs/` | ✅ PASS | Not tracked |
| 20 | Artifact check | `make artifact-check` | ⏸️ PENDING | Validates release archive |

**Run all at once:**
```bash
make artifact-check
make secret-check
```

---

### 3. Configuration ✅

| # | Check | Command | Status | Notes |
|---|-------|---------|--------|-------|
| 21 | `.gitignore` correct | Manual review | ✅ PASS | Excludes .env, vendor, node_modules, storage/logs, database.sqlite |
| 22 | `.dockerignore` correct | Manual review | ✅ PASS | Excludes .env, vendor, node_modules, storage/logs, database.sqlite |
| 23 | `.gitattributes` export-ignore | Manual review | ✅ PASS | All dev files excluded from `git archive` |
| 24 | Docker compose config | `docker compose config` | ⏸️ PENDING | Validate docker-compose.yml |
| 25 | Docker compose prod config | `docker compose -f docker/compose.yml config` | ⏸️ PENDING | Validate production compose |

---

### 4. Documentation ✅

| # | Check | Status | Notes |
|---|-------|--------|-------|
| 26 | `CHANGELOG.md` exists | ✅ DONE | Keep a Changelog format, v1.0.0-rc.1 section |
| 27 | `RELEASE_NOTES.md` exists | ✅ DONE | Features, breaking changes, migration, env, known issues |
| 28 | `.env.example` up to date | ⏸️ PENDING | Verify all required env vars documented |
| 29 | OpenAPI spec matches code | ⏸️ PENDING | Run contract tests |
| 30 | README.md accurate | ⏸️ PENDING | Verify install/setup commands work |

---

## 🗄️ Migration Safety Notes

### Pre-Migration Checklist

```bash
# 1. Backup database BEFORE any migration
mysqldump -u root -p tenrusl > backup_$(date +%Y%m%d_%H%M%S).sql
# atau
pg_dump tenrusl > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Check current migration status
php artisan migrate:status

# 3. Review pending migrations
ls -la database/migrations/

# 4. Test migration on staging FIRST
# (Never run untested migrations on production)
```

### Migration Rules

| Rule | Description |
|------|-------------|
| **Always backup** | Backup database sebelum setiap migration |
| **Test on staging** | Jalankan migration di staging environment terlebih dahulu |
| **No destructive changes** | Jangan drop kolom/tabel tanpa deprecation period |
| **Reversible** | Pastikan semua migration punya `down()` method yang benar |
| **Small batches** | Jalankan migration dalam batch kecil, verify setiap batch |
| **Off-peak hours** | Jalankan migration di luar jam sibuk |
| **Monitor** | Monitor database load selama migration berjalan |

### Migration Rollback Procedure

```bash
# Check status
php artisan migrate:status

# Rollback last batch
php artisan migrate:rollback --force

# Rollback specific steps
php artisan migrate:rollback --step=1 --force

# Rollback to specific migration
php artisan migrate:rollback --to=2026_01_01_000000_create_payments_table --force

# Nuclear option (DANGEROUS — drops all tables)
php artisan migrate:fresh --force
```

### Zero-Downtime Migration Tips

1. **Add columns only** — Jangan drop/rename kolom yang sedang dipakai
2. **Default values** — Selalu set `default` untuk kolom NOT NULL baru
3. **Index creation** — Gunakan `CONCURRENTLY` untuk large tables (PostgreSQL)
4. **Backfill** — Jalankan data backfill sebagai job terpisah, bukan di migration
5. **Feature flags** — Gunakan feature flag untuk fitur yang bergantung pada schema baru

---

## 🔄 Rollback Plan

### Level 1: Quick Rollback (< 5 menit)

Untuk masalah minor (config error, broken route, etc.):

```bash
# 1. Revert ke artifact sebelumnya
cd /var/www/tenrusl
cp -r /backups/tenrusl/v1.0.0-beta/* .

# 2. Re-cache config
php artisan config:cache
php artisan route:cache

# 3. Restart workers
php artisan queue:restart
# atau
supervisorctl restart tenrusl-worker:*
```

### Level 2: Database Rollback (5-15 menit)

Untuk masalah yang melibatkan migration:

```bash
# 1. Rollback migration
php artisan migrate:rollback --force

# 2. Revert code
cd /var/www/tenrusl
git checkout v1.0.0-beta

# 3. Re-install dependencies
composer install --no-dev --optimize-autoloader

# 4. Re-cache
php artisan config:cache
php artisan route:cache

# 5. Restart
php artisan queue:restart
supervisorctl restart all
```

### Level 3: Full Restore (15-30 menit)

Untuk kegagalan total (data corruption, critical bug):

```bash
# 1. Stop semua workers
supervisorctl stop all
# atau
php artisan queue:restart

# 2. Restore database dari backup
mysql -u root -p tenrusl < /backups/db/tenrusl_20260709.sql
# atau
psql tenrusl < /backups/db/tenrusl_20260709.sql

# 3. Restore application code
cd /var/www/tenrusl
rm -rf *
cp -r /backups/tenrusl/v1.0.0-beta/* .

# 4. Re-install
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan migrate --force

# 5. Restart
supervisorctl start all
```

### Level 4: Docker Rollback

```bash
# 1. Stop current containers
docker-compose down

# 2. Revert to previous image
export TENRUSL_IMAGE=tenrusl-app:v1.0.0-beta
docker-compose up -d

# 3. Verify
curl -f http://localhost/api/admin/health
```

### Rollback Decision Matrix

| Symptom | Level | Action |
|---------|-------|--------|
| Config typo, broken route | 1 | Revert code, re-cache |
| Bad migration, schema mismatch | 2 | Rollback migration + revert code |
| Data corruption, critical bug | 3 | Full restore from backup |
| Container won't start | 4 | Docker rollback |
| Performance degradation | 1-2 | Identify bottleneck, rollback if needed |
| Security incident | 3 | Full restore + rotate secrets |

---

## 🏷️ Tagging & Release

```bash
# 1. Run full QA
make qa

# 2. Build release artifact
make release-zip

# 3. Validate artifact
make artifact-check
make secret-check

# 4. Tag version
git tag -a v1.0.0-rc.1 -m "Release Candidate 1"
git push origin v1.0.0-rc.1

# 5. Create GitHub release (optional)
gh release create v1.0.0-rc.1 dist/tenrusl-payment-webhook-sim-v1.0.0-rc.1.zip \
  --title "v1.0.0-rc.1" \
  --notes-file RELEASE_NOTES.md \
  --prerelease
```

---

## ✅ Final Sign-Off

| Role | Name | Date | Status |
|------|------|------|--------|
| Release Manager | __________ | __/__/2026 | ⬜ |
| Backend Lead | __________ | __/__/2026 | ⬜ |
| Frontend Lead | __________ | __/__/2026 | ⬜ |
| DevOps Lead | __________ | __/__/2026 | ⬜ |
| Security Auditor | __________ | __/__/2026 | ⬜ |
| QA Lead | __________ | __/__/2026 | ⬜ |

---

## 📞 Emergency Contacts

| Role | Contact | When to Call |
|------|---------|--------------|
| On-call Engineer | TBD | Production incident |
| DBA | TBD | Database issue |
| Security | TBD | Security incident |
| Release Manager | TBD | Rollback decision |