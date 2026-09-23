<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Advantages». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class AdvantageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['array'],
            'title.ro' => ['required', 'string'],
            'title.en' => ['nullable', 'string'],
            'title.ru' => ['nullable', 'string'],
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'value_suffix' => ['array'],
            'value_suffix.ro' => ['nullable', 'string'],
            'value_suffix.en' => ['nullable', 'string'],
            'value_suffix.ru' => ['nullable', 'string'],
            'icon' => ['required', 'string', 'max:60'],
            'value' => ['nullable', 'string', 'max:40'],
            'is_counter' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
