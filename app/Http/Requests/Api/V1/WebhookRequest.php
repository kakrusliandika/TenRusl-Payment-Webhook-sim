<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Ambil raw body aman tanpa getContent(): gunakan php://input */
    public function rawBody(): string
    {
        $raw = @file_get_contents('php://input');
        return is_string($raw) ? $raw : '';
    }

    /**
     * Catatan:
     * - Validasi 'provider' tidak diperlukan di sini karena sudah dibatasi
     *   di routes via whereIn('provider', config allowlist).
     * - Payload webhook sangat bervariasi; event_id/type opsional.
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
