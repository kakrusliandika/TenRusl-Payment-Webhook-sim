<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Add method mixins so static analyzers (e.g., Intelephense) know these exist on FormRequest.
 *
 * @mixin \Illuminate\Http\Request
 * @method bool has(string|array $key)
 * @method mixed input(string $key = null, mixed $default = null)
 * @method void merge(array $input)
 */
class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validation:
     * - Map client "metadata" → internal "meta"
     * - Uppercase currency (if provided)
     * - Default currency to IDR when missing/empty
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Map metadata -> meta to align with model/DB
        if ($this->has('metadata') && !$this->has('meta')) {
            /** @var array|null $metadata */
            $metadata = $this->input('metadata');
            $merge['meta'] = $metadata;
        }

        // Uppercase currency if sent
        $currency = $this->input('currency');
        if (is_string($currency) && $currency !== '') {
            $merge['currency'] = strtoupper($currency);
        }

        // Default currency when missing/empty
        if (!$this->has('currency') || $this->input('currency') === null || $this->input('currency') === '') {
            $merge['currency'] = 'IDR';
        }

        $this->merge($merge);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string> */
    public function rules(): array
    {
        $allowlist = (array) config('tenrusl.providers_allowlist', []);

        return [
            'provider'    => ['required', 'string', Rule::in($allowlist)],

            // amount as integer (minor unit) to align with DB & casts
            'amount'      => ['required', 'integer', 'min:1'],

            // strict 3-letter ISO code, uppercase
            'currency'    => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],

            'description' => ['nullable', 'string', 'max:255'],

            // accept either "metadata" or "meta" from client
            'metadata'    => ['nullable', 'array'],
            'meta'        => ['nullable', 'array'],
        ];
    }

    /**
     * Return validated payload preferring "meta" (internal) over "metadata" (client-facing).
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        /** @var array<string,mixed> $data */
        $data = parent::validated($key, $default);

        if (!array_key_exists('meta', $data) && array_key_exists('metadata', $data)) {
            $data['meta'] = $data['metadata'];
        }

        unset($data['metadata']);

        return $data;
    }

    /**
     * Optional nicer attribute labels.
     *
     * @return array<string,string>
     */
    public function attributes(): array
    {
        return [
            'provider' => 'payment provider',
            'amount'   => 'amount',
            'currency' => 'currency (ISO-4217)',
            'meta'     => 'metadata',
        ];
    }
}
