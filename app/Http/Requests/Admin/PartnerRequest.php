<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Partners». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:160'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
