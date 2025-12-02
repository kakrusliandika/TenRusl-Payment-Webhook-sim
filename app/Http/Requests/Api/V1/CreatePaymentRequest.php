<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest untuk:
 * - POST /api/v1/payments
 *
 * Tujuan:
 * - Validasi input sesuai dokumentasi (provider allowlist, amount, currency, dsb).
 * - Normalisasi input sebelum validasi:
 *   - "metadata" (client) -> "meta" (internal)
 *   - currency uppercase
 *   - default currency ke IDR
 *
 * Catatan tooling:
 * - Docblock @mixin + @method membantu static analyzer (Intelephense, PHPStan, Psalm).
 *
 * @mixin \Illuminate\Http\Request
 *
 * @method bool has(string|array $key)
 * @method mixed input(string $key = null, mixed $default = null)
 * @method void merge(array $input)
 */
class CreatePaymentRequest extends FormRequest
{
    /**
     * Endpoint ini public (simulator).
     * Kalau nanti ada auth, ubah jadi cek token/guard di sini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi input sebelum rules() dijalankan.
     *
     * - Jika client kirim "metadata" tapi tidak kirim "meta", kita map ke "meta".
     * - Currency kita uppercase (IDR, USD, dst).
     * - Jika currency kosong/tidak ada, kita default ke IDR.
     *
     * Ini membuat rule 'currency' tetap "required", tapi request tanpa currency tetap lolos
     * karena di-inject IDR di tahap ini.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Map metadata -> meta agar konsisten dengan internal field yang dipakai service/model
        if ($this->has('metadata') && ! $this->has('meta')) {
            /** @var mixed $metadata */
            $metadata = $this->input('metadata');
            if (is_array($metadata)) {
                $merge['meta'] = $metadata;
            }
        }

        // Uppercase currency bila dikirim
        $currency = $this->input('currency');
        if (is_string($currency) && $currency !== '') {
            $merge['currency'] = strtoupper($currency);
        }

        // Default currency saat tidak ada / kosong
        if (! $this->has('currency') || $this->input('currency') === null || $this->input('currency') === '') {
            $merge['currency'] = 'IDR';
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Rules validasi request create payment.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Allowlist provider diambil dari config/tenrusl.php
        // (fallback default agar dev experience tetap enak kalau config belum ke-load)
        $allowlist = (array) config('tenrusl.providers_allowlist', ['mock', 'xendit', 'midtrans']);

        return [
            // Provider wajib sesuai allowlist (route & signature service juga memakai allowlist yang sama)
            'provider' => ['required', 'string', Rule::in($allowlist)],

            // amount wajib integer (minor unit), minimal 1
            'amount' => ['required', 'integer', 'min:1'],

            // currency 3 huruf ISO (uppercase), default IDR jika tidak dikirim
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],

            'description' => ['nullable', 'string', 'max:255'],

            // Terima kedua-duanya: metadata (legacy/client) atau meta (internal)
            'metadata' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * Pastikan output validated() selalu mengandung "meta" (internal),
     * dan tidak mengekspose "metadata" (alias).
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        /** @var array<string, mixed> $data */
        $data = parent::validated($key, $default);

        // Jika meta belum ada tapi metadata ada, map ke meta.
        if (! array_key_exists('meta', $data) && array_key_exists('metadata', $data)) {
            $data['meta'] = $data['metadata'];
        }

        // Bersihkan alias agar downstream tidak bingung
        unset($data['metadata']);

        // Default meta ke array kosong biar downstream simple
        if (! array_key_exists('meta', $data) || ! is_array($data['meta'])) {
            $data['meta'] = [];
        }

        return $data;
    }

    /**
     * Label attribute agar message validasi lebih rapi.
     *
     * @return array<string,string>
     */
    public function attributes(): array
    {
        return [
            'provider' => 'payment provider',
            'amount' => 'amount',
            'currency' => 'currency (ISO-4217)',
            // Label konsisten untuk keduanya
            'metadata' => 'metadata',
            'meta' => 'metadata',
        ];
    }
}
