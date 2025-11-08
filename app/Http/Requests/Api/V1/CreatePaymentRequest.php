<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bebas di demo; gunakan policy/guard di produksi.
    }

    public function rules(): array
    {
        return [
            'amount'      => ['required', 'integer', 'min:1000'],
            'currency'    => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:140'],
            'metadata'    => ['nullable', 'array'],
        ];
    }
}
