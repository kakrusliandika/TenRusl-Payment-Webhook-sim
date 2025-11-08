# ADR 0001 — Idempotency pada POST /payments

## Status

Accepted — v0.1.0

## Konteks

Permintaan `POST /payments` rentan dipanggil berulang (double submit, retry jaringan). Tanpa idempotensi, bisa tercipta pembayaran ganda.

## Keputusan

- **Header**: klien mengirim `Idempotency-Key` unik per niat transaksi.
- **Fingerprint**: server membuat jejak `endpoint + body (canonical)` untuk konsistensi.
- **Penyimpanan**: snapshot respons pertama disimpan dan dikembalikan untuk permintaan ulang dengan key sama.
- **Lock ringan**: mencegah race condition pada proses pertama.
- **Respon duplikat**: kembalikan snapshot 201 (atau 409 sesuai kebijakan).

## Konsekuensi

- Proses create bersifat “exactly-once per key”.
- Perlu penyimpanan & pembersihan data idempotensi.
- Memudahkan klien untuk retry tanpa risiko duplikasi.

## Alternatives Considered

- Tanpa idempotensi → rawan duplikasi.
- Hanya dedup di sisi pembayaran eksternal → tidak menutup gap saat create internal.

## Referensi

- Patterns: Idempotent Consumer, Request De-duplication.
