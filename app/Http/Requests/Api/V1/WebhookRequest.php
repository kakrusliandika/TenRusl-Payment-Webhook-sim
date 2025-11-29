<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ambil raw body dari Request.
     *
     * Urutan:
     * 1. Cek attribute 'tenrusl_raw_body' yang di-set oleh middleware
     *    VerifyWebhookSignature (sumber utama dan tunggal pembacaan stream).
     * 2. Jika tidak ada (misalnya middleware tidak dipakai), fallback ke getContent().
     */
    public function rawBody(): string
    {
        $attr = $this->attributes->get('tenrusl_raw_body');

        if (\is_string($attr) && $attr !== '') {
            return $attr;
        }

        // Fallback aman: getContent() milik Request (sudah meng-cache php://input)
        $raw = $this->getContent();

        return \is_string($raw) ? $raw : '';
    }

    /**
     * Aturan validasi untuk webhook.
     *
     * - 'provider' tidak divalidasi di sini karena sudah dibatasi pada route
     *   melalui whereIn() terhadap allowlist di config.
     * - event_id dan type opsional; WebhooksController akan memakai nilai yang
     *   tervalidasi jika ada, lalu fallback ke hasil ekstraksi dari payload.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => ['sometimes', 'string', 'max:191'],
            'type'     => ['sometimes', 'string', 'max:191'],
        ];
    }
}
