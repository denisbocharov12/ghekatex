<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Правила раздела «MediaItems». Основная локаль обязательна, остальные — нет:
 * перевод может появиться позже, но запись не должна оставаться без названия.
 */
class MediaItemRequest extends FormRequest
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
            'title.ro' => ['nullable', 'string'],
            'title.en' => ['nullable', 'string'],
            'title.ru' => ['nullable', 'string'],
            'caption' => ['array'],
            'caption.ro' => ['nullable', 'string'],
            'caption.en' => ['nullable', 'string'],
            'caption.ru' => ['nullable', 'string'],
            'album_id' => ['nullable', 'integer', 'exists:media_albums,id'],
            'type' => ['required', 'string', 'in:image,video'],
            'video_provider' => ['nullable', 'string', 'in:youtube,vimeo,file'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'file' => ['nullable', 'file', 'max:102400'],
            'poster' => ['nullable', 'image', 'max:8192'],
        ];
    }
}
