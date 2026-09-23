<?php

namespace App\Models;

use App\Enums\SettingGroup;
use App\Enums\SettingType;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Настройка сайта. Значение всегда лежит в JSON-колонке: для переводимых —
 * набор локалей, для остальных — ключ `value`. Такая форма избавляет от
 * приведения типов на каждом чтении.
 */
class Setting extends Model implements HasMedia
{
    use InteractsWithMedia, RecordsActivity;

    protected $fillable = [
        'key',
        'group',
        'type',
        'value',
        'is_translatable',
        'label',
        'hint',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'array',
        'is_translatable' => 'boolean',
        'sort_order' => 'integer',
        'group' => SettingGroup::class,
        'type' => SettingType::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
    }

    /** Значение для текущей или указанной локали. */
    public function resolvedValue(?string $locale = null): mixed
    {
        $value = $this->value ?? [];

        if (! $this->is_translatable) {
            return $value['value'] ?? null;
        }

        $locale ??= app()->getLocale();
        $fallback = config('ghekatex.locales.default');

        return $value[$locale] ?? $value[$fallback] ?? null;
    }
}
