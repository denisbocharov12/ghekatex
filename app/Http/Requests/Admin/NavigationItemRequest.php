<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Navigation». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class NavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'label' => ['array'],
            'label.ro' => ['required', 'string'],
            'label.en' => ['nullable', 'string'],
            'label.ru' => ['nullable', 'string'],
            'menu' => ['required', 'string', 'in:header,footer_primary,footer_secondary,legal'],
            'parent_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:60'],
            'opens_in_new_tab' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
