<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «MediaAlbums». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class MediaAlbumRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'seo' => ['nullable', 'array'],
            'seo.robots' => ['nullable', 'string', 'max:60'],
            'seo.canonical_url' => ['nullable', 'string', 'max:255'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
