<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest untuk:
 * - POST /api/v1/payments
 *
 * Fokus:
 * - Validasi input FE (provider, amount, currency, meta/metadata, description).
 * - Normalisasi input sebelum validasi:
 *   - "metadata" (alias/legacy) -> "meta" (internal)
 *   - provider -> lowercase
 *   - currency -> uppercase dan default IDR
 *   - amount -> integer jika numeric-string (mis. "10000")
 *
 * Catatan:
 * - validated() di FormRequest Laravel mengembalikan mixed bila $key diisi,
 *   jadi override tetap mengikuti signature aslinya. :contentReference[oaicite:1]{index=1}
 */
class CreatePaymentRequest extends FormRequest
{
    /**
     * Endpoint simulator (public).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi input sebelum rules() dipakai.
     *
     * prepareForValidation adalah hook resmi FormRequest untuk menyiapkan data sebelum validasi. :contentReference[oaicite:2]{index=2}
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Normalisasi provider -> lowercase agar cocok dengan allowlist config (umumnya lowercase)
        $provider = $this->input('provider');
        if (is_string($provider) && $provider !== '') {
            $merge['provider'] = strtolower($provider);
        }

        // Normalisasi amount: jika numeric-string integer, cast ke int
        $amount = $this->input('amount');
        if (is_string($amount) && $amount !== '' && preg_match('/^\d+$/', $amount) === 1) {
            $merge['amount'] = (int) $amount;
        }

        // Map metadata -> meta (hanya jika meta belum ada)
        if ($this->has('metadata') && ! $this->has('meta')) {
            $metadata = $this->input('metadata');
            if (is_array($metadata)) {
                $merge['meta'] = $metadata;
            }
        }

        // Currency uppercase bila dikirim
        $currency = $this->input('currency');
        if (is_string($currency) && $currency !== '') {
            $merge['currency'] = strtoupper($currency);
        }

        // Default currency jika tidak ada / kosong
        if (! $this->has('currency') || $this->input('currency') === null || $this->input('currency') === '') {
            $merge['currency'] = 'IDR';
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Allowlist provider dari config/tenrusl.php
        $allowlist = (array) config('tenrusl.providers_allowlist', ['mock', 'xendit', 'midtrans']);

        return [
            'provider' => ['required', 'string', Rule::in($allowlist)],

            // minor unit (integer), minimal 1
            'amount' => ['required', 'integer', 'min:1'],

            // ISO-4217 3 huruf uppercase, default disuntik IDR di prepareForValidation
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],

            'description' => ['nullable', 'string', 'max:255'],

            // Alias/legacy support: terima dua-duanya
            'metadata' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * Pastikan output validated() saat dipanggil tanpa parameter selalu:
     * - punya "meta" (array)
     * - tidak menyisakan "metadata"
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // Bila $key diisi, ikuti perilaku bawaan (mixed).
        if ($key !== null) {
            return $validated;
        }

        /** @var array<string, mixed> $data */
        $data = is_array($validated) ? $validated : [];

        if (
            ! array_key_exists('meta', $data)
            && array_key_exists('metadata', $data)
            && is_array($data['metadata'])
        ) {
            $data['meta'] = $data['metadata'];
        }

        unset($data['metadata']);

        if (! array_key_exists('meta', $data) || ! is_array($data['meta'])) {
            $data['meta'] = [];
        }

        return $data;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'provider' => 'payment provider',
            'amount' => 'amount',
            'currency' => 'currency (ISO-4217)',
            'metadata' => 'metadata',
            'meta' => 'metadata',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provider.in' => 'Provider tidak dikenali / tidak ada di allowlist.',
            'amount.integer' => 'Amount harus integer (minor unit).',
            'amount.min' => 'Amount minimal 1.',
            'currency.regex' => 'Currency harus 3 huruf uppercase (contoh: IDR).',
        ];
    }
}
