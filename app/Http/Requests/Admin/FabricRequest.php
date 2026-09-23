<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Fabrics». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class FabricRequest extends FormRequest
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
            'composition' => ['array'],
            'composition.ro' => ['nullable', 'string'],
            'composition.en' => ['nullable', 'string'],
            'composition.ru' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:191'],
            'weight_gsm' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'color_hex' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'swatch' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
