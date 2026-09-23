<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «Offices». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class OfficeRequest extends FormRequest
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
            'address' => ['array'],
            'address.ro' => ['required', 'string'],
            'address.en' => ['nullable', 'string'],
            'address.ru' => ['nullable', 'string'],
            'city' => ['array'],
            'city.ro' => ['nullable', 'string'],
            'city.en' => ['nullable', 'string'],
            'city.ru' => ['nullable', 'string'],
            'working_hours' => ['array'],
            'working_hours.ro' => ['nullable', 'string'],
            'working_hours.en' => ['nullable', 'string'],
            'working_hours.ru' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:office,factory,warehouse'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'phones' => ['nullable', 'array'],
            'emails' => ['nullable', 'array'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_primary' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:8192'],
        ];
    }
}
