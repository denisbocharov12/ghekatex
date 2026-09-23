<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Posts». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class PostRequest extends FormRequest
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
            'excerpt' => ['array'],
            'excerpt.ro' => ['nullable', 'string'],
            'excerpt.en' => ['nullable', 'string'],
            'excerpt.ru' => ['nullable', 'string'],
            'body' => ['array'],
            'body.ro' => ['nullable', 'string'],
            'body.en' => ['nullable', 'string'],
            'body.ru' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:191'],
            'type' => ['required', 'string', 'in:news,article,review'],
            'category_id' => ['nullable', 'integer', 'exists:post_categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'reading_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'is_active' => ['boolean'],
            'cover' => ['nullable', 'image', 'max:8192'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:8192'],
            'seo' => ['nullable', 'array'],
            'seo.robots' => ['nullable', 'string', 'max:60'],
            'seo.canonical_url' => ['nullable', 'string', 'max:255'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
