<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Certificates». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class CertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['array'],
            'name.ro' => ['required', 'string'],
            'name.en' => ['nullable', 'string'],
            'name.ru' => ['nullable', 'string'],
            'issuer' => ['array'],
            'issuer.ro' => ['nullable', 'string'],
            'issuer.en' => ['nullable', 'string'],
            'issuer.ru' => ['nullable', 'string'],
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'number' => ['nullable', 'string', 'max:120'],
            'issued_at' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:8192'],
            'document' => ['nullable', 'mimes:pdf', 'max:16384'],
        ];
    }
}
