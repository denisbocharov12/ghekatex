<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Доступ к настройкам сайта. Значения кешируются целиком: их немного,
 * а читаются они на каждой странице.
 */
class SettingService
{
    private const CACHE_KEY = 'ghekatex.settings';

    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    /** Значение настройки в текущей локали. */
    public function get(string $key, mixed $fallback = null, ?string $locale = null): mixed
    {
        $setting = $this->raw()->get($key);

        if (! $setting instanceof Setting) {
            return $fallback;
        }

        return $setting->resolvedValue($locale) ?? $fallback;
    }

    /**
     * Группа настроек как плоский массив «ключ => значение в текущей локали».
     *
     * @return array<string, mixed>
     */
    public function group(string $group, ?string $locale = null): array
    {
        return $this->raw()
            ->filter(fn (Setting $setting) => $setting->group->value === $group)
            ->mapWithKeys(fn (Setting $setting) => [$setting->key => $setting->resolvedValue($locale)])
            ->all();
    }

    /**
     * Настройки для формы админки: со всеми локалями и метаданными.
     *
     * @return array<int, array<string, mixed>>
     */
    public function editable(): array
    {
        return $this->settings->all()
            ->map(fn (Setting $setting) => [
                'id' => $setting->id,
                'key' => $setting->key,
                'group' => $setting->group->value,
                'type' => $setting->type->value,
                'label' => $setting->label,
                'hint' => $setting->hint,
                'is_translatable' => $setting->is_translatable,
                'value' => $setting->value ?? [],
                'file_url' => $setting->getFirstMediaUrl('file') ?: null,
            ])
            ->all();
    }

    /** @return Collection<string, Setting> */
    private function raw(): Collection
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => $this->settings->all()->keyBy('key'),
        );
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
