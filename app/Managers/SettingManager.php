<?php

namespace App\Managers;

use App\Enums\SettingType;
use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Запись настроек. Форма присылает значения пачкой — сохраняем их одной
 * транзакцией, иначе половина настроек применится, а половина нет.
 */
class SettingManager
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    /**
     * @param  array<string, mixed>  $values  ключ настройки => значение или набор локалей
     * @param  array<string, UploadedFile>  $files  ключ настройки => файл
     */
    public function saveMany(array $values, array $files = []): void
    {
        DB::transaction(function () use ($values, $files): void {
            $existing = $this->settings->all()->keyBy('key');

            foreach ($values as $key => $value) {
                $setting = $existing->get($key);

                if (! $setting instanceof Setting) {
                    continue;
                }

                $this->settings->update($setting, ['value' => $this->normalize($setting, $value)]);
            }

            foreach ($files as $key => $file) {
                $setting = $existing->get($key);

                if ($setting instanceof Setting && $file instanceof UploadedFile) {
                    $setting->addMedia($file)->toMediaCollection('file');
                    $this->settings->update($setting, ['value' => ['value' => $setting->getFirstMediaUrl('file')]]);
                }
            }
        });
    }

    /**
     * Приводит значение к форме хранения: переводимое — набор локалей,
     * обычное — один ключ `value` нужного типа.
     *
     * @return array<string, mixed>
     */
    private function normalize(Setting $setting, mixed $value): array
    {
        if ($setting->is_translatable) {
            $result = [];

            foreach (config('ghekatex.locales.available') as $locale) {
                $result[$locale] = is_array($value) ? trim((string) ($value[$locale] ?? '')) : '';
            }

            return $result;
        }

        return ['value' => match ($setting->type) {
            SettingType::Boolean => filter_var($value, FILTER_VALIDATE_BOOL),
            SettingType::Number => $value === null || $value === '' ? null : (float) $value,
            SettingType::Json => is_array($value) ? $value : [],
            default => $value === null ? null : (is_array($value) ? $value : trim((string) $value)),
        }];
    }
}
