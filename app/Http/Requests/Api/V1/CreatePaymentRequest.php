<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string> */
    public function rules(): array
    {
        $allowlist = (array) config('tenrusl.providers_allowlist', []);

        return [
            'provider'    => ['required', 'string', Rule::in($allowlist)],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            // jika ingin paksa UPPERCASE: ganti ke ['nullable','string','size:3','regex:/^[A-Z]{3}$/']
            'currency'    => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:255'],
            'metadata'    => ['nullable', 'array'],
        ];
    }
}
