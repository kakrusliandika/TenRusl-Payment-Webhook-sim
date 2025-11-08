<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Receiver webhook; tambahkan whitelist IP/token jika perlu.
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'string', 'max:150'],
            'type'     => ['required', 'string', 'max:100'],
            'data'     => ['required', 'array'],
            'sent_at'  => ['nullable', 'date'],
        ];
    }
}
