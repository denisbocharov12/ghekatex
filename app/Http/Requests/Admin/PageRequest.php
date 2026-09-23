<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Pages». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class PageRequest extends FormRequest
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
            'subtitle' => ['array'],
            'subtitle.ro' => ['nullable', 'string'],
            'subtitle.en' => ['nullable', 'string'],
            'subtitle.ru' => ['nullable', 'string'],
            'body' => ['array'],
            'body.ro' => ['nullable', 'string'],
            'body.en' => ['nullable', 'string'],
            'body.ru' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:191'],
            'template' => ['required', 'string', 'in:default,about,legal,contacts'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'cover' => ['nullable', 'image', 'max:8192'],
            'seo' => ['nullable', 'array'],
            'seo.robots' => ['nullable', 'string', 'max:60'],
            'seo.canonical_url' => ['nullable', 'string', 'max:255'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
