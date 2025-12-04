<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest untuk:
 * - POST /api/v1/webhooks/{provider}
 *
 * Catatan penting:
 * - Verifikasi signature dilakukan oleh middleware VerifyWebhookSignature
 *   sebelum masuk controller.
 * - Middleware itu juga menyimpan raw body ke attribute 'tenrusl_raw_body'
 *   supaya stream tidak dibaca berkali-kali.
 */
class WebhookRequest extends FormRequest
{
    /**
     * Endpoint webhook adalah public; auth dilakukan via signature (middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ambil raw body dari request.
     *
     * Urutan:
     * 1) Attribute 'tenrusl_raw_body' yang di-set middleware VerifyWebhookSignature.
     *    Ini jadi sumber utama supaya tidak membaca php://input dua kali.
     * 2) Fallback ke getContent() (Laravel/Symfony biasanya meng-cache body).
     */
    public function rawBody(): string
    {
        $attr = $this->attributes->get('tenrusl_raw_body');

        if (is_string($attr) && $attr !== '') {
            return $attr;
        }

        // getContent() dipanggil tanpa $asResource, jadi body dibaca sebagai string.
        return (string) $this->getContent();
    }

    /**
     * Rule validasi untuk field "resmi" (opsional) di webhook.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => ['sometimes', 'string', 'max:191'],
            'type' => ['sometimes', 'string', 'max:191'],
        ];
    }
}
