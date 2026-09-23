<?php

namespace App\Services;

use App\Enums\NavigationMenu;
use App\Models\NavigationItem;
use App\Repositories\Contracts\NavigationItemRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * Дерево пунктов меню для витрины. Ссылка задаётся либо именованным
 * маршрутом, либо произвольным URL — редактор выбирает, что удобнее.
 */
class NavigationService
{
    public function __construct(
        private readonly NavigationItemRepositoryInterface $items,
    ) {}

    /**
     * @return array<string, array<int, array<string, mixed>>> меню => список пунктов
     */
    public function menus(string $locale): array
    {
        return Cache::rememberForever("ghekatex.navigation.{$locale}", function () use ($locale) {
            $all = $this->items->activeOrdered();

            $result = [];

            foreach (NavigationMenu::cases() as $menu) {
                $result[$menu->value] = $all
                    ->where('menu', $menu)
                    ->whereNull('parent_id')
                    ->map(fn (NavigationItem $item) => $this->present($item, $all, $locale))
                    ->values()
                    ->all();
            }

            return $result;
        });
    }

    /**
     * @param  Collection<int, NavigationItem>  $all
     * @return array<string, mixed>
     */
    private function present(NavigationItem $item, $all, string $locale): array
    {
        return [
            'id' => $item->id,
            'label' => $item->getTranslation('label', $locale, true),
            'href' => $this->href($item, $locale),
            'icon' => $item->icon,
            'external' => $item->opens_in_new_tab,
            'children' => $all
                ->where('parent_id', $item->id)
                ->map(fn (NavigationItem $child) => $this->present($child, $all, $locale))
                ->values()
                ->all(),
        ];
    }

    private function href(NavigationItem $item, string $locale): string
    {
        if ($item->route_name !== null && $item->route_name !== '') {
            $params = array_merge(['locale' => $locale], $item->route_params ?? []);

            // Пункт может ссылаться на маршрут, удалённый в следующем релизе
            return Route::has($item->route_name)
                ? route($item->route_name, $params)
                : '#';
        }

        return $item->url ?? '#';
    }

    public function flush(): void
    {
        foreach (config('ghekatex.locales.available') as $locale) {
            Cache::forget("ghekatex.navigation.{$locale}");
        }
    }
}
