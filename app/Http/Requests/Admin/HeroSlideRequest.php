<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «HeroSlides». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class HeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'eyebrow' => ['array'],
            'eyebrow.ro' => ['nullable', 'string'],
            'eyebrow.en' => ['nullable', 'string'],
            'eyebrow.ru' => ['nullable', 'string'],
            'title' => ['array'],
            'title.ro' => ['required', 'string'],
            'title.en' => ['nullable', 'string'],
            'title.ru' => ['nullable', 'string'],
            'description' => ['array'],
            'description.ro' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
            'cta_label' => ['array'],
            'cta_label.ro' => ['nullable', 'string'],
            'cta_label.en' => ['nullable', 'string'],
            'cta_label.ru' => ['nullable', 'string'],
            'secondary_cta_label' => ['array'],
            'secondary_cta_label.ro' => ['nullable', 'string'],
            'secondary_cta_label.en' => ['nullable', 'string'],
            'secondary_cta_label.ru' => ['nullable', 'string'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'overlay_opacity' => ['nullable', 'integer', 'between:0,100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:8192'],
            'video' => ['nullable', 'mimetypes:video/mp4,video/webm', 'max:102400'],
        ];
    }
}
