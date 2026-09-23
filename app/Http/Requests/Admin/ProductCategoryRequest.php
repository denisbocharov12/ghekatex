<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «ProductCategories». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class ProductCategoryRequest extends FormRequest
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
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:191'],
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id'],
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
