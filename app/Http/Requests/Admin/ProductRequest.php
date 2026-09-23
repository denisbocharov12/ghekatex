<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Products». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class ProductRequest extends FormRequest
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
            'short_description' => ['array'],
            'short_description.ro' => ['nullable', 'string'],
            'short_description.en' => ['nullable', 'string'],
            'short_description.ru' => ['nullable', 'string'],
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'composition' => ['array'],
            'composition.ro' => ['nullable', 'string'],
            'composition.en' => ['nullable', 'string'],
            'composition.ru' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:191'],
            'article' => ['nullable', 'string', 'max:60'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'min_order_quantity' => ['nullable', 'integer', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'attributes' => ['nullable', 'array'],
            'cover' => ['nullable', 'image', 'max:8192'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:8192'],
            'fabrics' => ['nullable', 'array'],
            'fabrics.*' => ['integer', 'exists:fabrics,id'],
            'treatments' => ['nullable', 'array'],
            'treatments.*' => ['integer', 'exists:treatments,id'],
            'seo' => ['nullable', 'array'],
            'seo.robots' => ['nullable', 'string', 'max:60'],
            'seo.canonical_url' => ['nullable', 'string', 'max:255'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
