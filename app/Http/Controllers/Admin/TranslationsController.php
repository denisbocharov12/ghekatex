<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TranslationCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Редактор строк интерфейса. Исходные значения читаются из словарей витрины
 * и панели, правки складываются в базу и накладываются поверх файлов.
 */
class TranslationsController extends Controller
{
    private const GROUPS = ['site', 'admin', 'php'];

    public function __construct(
        private readonly TranslationCatalogService $catalog,
    ) {}

    public function index(Request $request): Response
    {
        $group = $request->string('group')->toString();
        $group = in_array($group, self::GROUPS, true) ? $group : 'site';

        return Inertia::render('Translations/Index', [
            'group' => $group,
            'groups' => self::GROUPS,
            'rows' => $this->catalog->catalog($group, $this->source($group)),
            'localeCodes' => config('ghekatex.locales.available'),
            'localeLabels' => config('ghekatex.locales.labels'),
            'defaultLocale' => config('ghekatex.locales.default'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group' => ['required', 'string', 'in:'.implode(',', self::GROUPS)],
            'key' => ['required', 'string', 'max:255'],
            'values' => ['required', 'array'],
            'values.*' => ['nullable', 'string'],
        ]);

        $this->catalog->put($validated['group'], $validated['key'], $validated['values']);

        return back()->with('success', __('Перевод сохранён'));
    }

    /**
     * Исходный словарь группы: витрина и панель держат ключи в JSON,
     * серверные строки — в lang/<locale>.json.
     *
     * @return array<string, string>
     */
    private function source(string $group): array
    {
        $default = config('ghekatex.locales.default');

        $path = match ($group) {
            'site' => resource_path("js/site/i18n/{$default}.json"),
            'admin' => resource_path('js/admin/i18n/ru.json'),
            default => lang_path("{$default}.json"),
        };

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $this->flatten($decoded) : [];
    }

    /**
     * Вложенный словарь превращаем в плоский: ключи вида `nav.about`.
     *
     * @param  array<string, mixed>  $items
     * @return array<string, string>
     */
    private function flatten(array $items, string $prefix = ''): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $full = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $result += $this->flatten($value, $full);

                continue;
            }

            $result[$full] = (string) $value;
        }

        return $result;
    }
}
