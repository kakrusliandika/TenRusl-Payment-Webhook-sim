# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0-rc.1] - 2026-07-09

### Added

#### Payment Engine
- Public REST API: `POST /api/payments`, `GET /api/payments/{id}`, `GET /api/payments/{provider}/{providerRef}/status`
- 15 payment provider adapters: mock, xendit, midtrans, stripe, paypal, paddle, lemonsqueezy, airwallex, tripay, doku, dana, oy, payoneer, skrill, amazon_bwp
- Abstract simulated payment adapter base class for reduced duplication
- Idempotency key support via `Idempotency-Key` header
- Canonical `meta` field with legacy `metadata` alias (normalized internally)

#### Webhook System
- `POST /api/webhooks/{provider}` — receive webhooks from 15 providers
- Signature validation per provider
- Duplicate webhook detection
- Dead-letter queue for failed webhooks
- `POST /api/admin/webhooks/{id}/retry` — retry dead-lettered webhooks

#### Admin Panel API
- `GET /api/admin/health` — application health check
- `GET /api/admin/health/database` — database health check
- `GET /api/admin/metrics` — aggregated payment metrics
- `GET /api/admin/payments` — paginated payment list
- `GET /api/admin/webhooks` — paginated webhook event list
- `GET /api/admin/webhooks/dead-letter` — dead-letter webhook list
- `GET /api/admin/webhooks/{id}` — webhook event detail
- `POST /api/admin/payments/{id}/simulate/mark_paid` — simulate payment success
- `POST /api/admin/payments/{id}/simulate/mark_failed` — simulate payment failure
- `POST /api/admin/payments/{id}/simulate/mark_expired` — simulate payment expiry
- `POST /api/admin/payments/{id}/simulate/send_webhook` — simulate webhook dispatch
- `GET /api/admin/providers/capabilities` — provider capability matrix
- All admin endpoints available on both `/api/admin/*` and `/api/v1/admin/*`
- Admin key authentication via `Authorization: Bearer <key>`, raw `Authorization: <key>`, or `X-Admin-Key: <key>`

#### Security
- Security headers via `bepsvpt/secure-headers`: nosniff, sameorigin frame, strict referrer, permissions policy
- CORS configuration with explicit allowed origins, exposed request ID and rate limit headers
- Rate limiting on public and admin endpoints
- Request ID tracking (`X-Request-ID`)

#### Frontend Integration
- CORS exposes: `X-Request-ID`, `Idempotency-Key`, `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- Rate limit headers for client retry logic

#### Infrastructure
- Redis support for cache, rate limiter, idempotency store, queue, session
- Database queue driver as fallback
- File cache driver as fallback
- Docker production image (multi-stage)
- Supervisor process management
- Nginx reverse proxy config
- Fail2ban integration
- Makefile with `qa`, `audit`, `release-check`, `artifact-check`, `secret-check` targets

#### Documentation
- OpenAPI 3.1 specification (`docs/openapi.yaml`)
- Postman collection
- Architecture docs, deployment guides, security docs, runbooks
- Environment variable documentation (`docs/ENVIRONMENT.md`)
- Provider capability matrix (`docs/PROVIDERS.md`)
- Demo guide with smoke test script

#### Testing
- Pest test framework with Unit and Feature test suites
- OpenAPI contract tests
- Frontend quality checks (lint, typecheck, build)
- Release artifact validation (secret scan, artifact scan)

### Breaking Changes
- None (initial release)

### Migration Notes
- See `docs/DATABASE_RUNBOOK.md` for migration procedures
- Requires PHP 8.3+, MySQL 8.0+ or PostgreSQL 15+
- Requires Redis 7+ for production (file cache fallback available for development)

### Environment Variables (New)
- `TENRUSL_ADMIN_KEY` — Admin API authentication key
- `TENRUSL_ADMIN_HEADER` — Admin header name (default: `Authorization`)
- `CACHE_STORE` — Cache driver: `redis` (production), `file` (development)
- `QUEUE_CONNECTION` — Queue driver: `redis` (production), `database` (development)
- `SESSION_DRIVER` — Session driver: `redis` (production), `file` (development)
- `REDIS_CLIENT` — Redis client: `phpredis` (recommended), `predis`

### Known Issues
- Redis is not configured as default driver in `.env.example` — production deployments must set `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`
- CSP is disabled by default — enable gradual rollout in production
- HSTS is disabled by default — enable in production with proper SSL
- `pdo_sqlite` extension not detected in some environments — use MySQL/PostgreSQL for development