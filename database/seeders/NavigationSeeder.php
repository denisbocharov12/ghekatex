<?php

namespace Database\Seeders;

use App\Enums\NavigationMenu;
use App\Models\NavigationItem;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $items = [
            // Верхнее меню повторяет структуру сайта из технического задания
            [NavigationMenu::Header, 'home', null, $this->t('Acasă', 'Home', 'Главная')],
            [NavigationMenu::Header, 'about', null, $this->t('Despre companie', 'About', 'О компании')],
            [NavigationMenu::Header, 'catalog.index', null, $this->t('Ce coasem', 'What we make', 'Что мы шьём')],
            [NavigationMenu::Header, 'services.index', null, $this->t('Servicii', 'Services', 'Услуги')],
            [NavigationMenu::Header, 'news.index', null, $this->t('Noutăți', 'News', 'Новости и медиа')],
            [NavigationMenu::Header, 'contacts.index', null, $this->t('Contacte', 'Contacts', 'Контакты')],

            [NavigationMenu::FooterPrimary, 'about', null, $this->t('Despre companie', 'About the company', 'О компании')],
            [NavigationMenu::FooterPrimary, 'catalog.index', null, $this->t('Ce coasem', 'What we make', 'Что мы шьём')],
            [NavigationMenu::FooterPrimary, 'services.index', null, $this->t('Servicii de producție', 'Manufacturing services', 'Производственные услуги')],

            [NavigationMenu::FooterSecondary, 'news.index', null, $this->t('Noutăți', 'News', 'Новости')],
            [NavigationMenu::FooterSecondary, 'media.index', null, $this->t('Galerie media', 'Media gallery', 'Медиа-галерея')],
            [NavigationMenu::FooterSecondary, 'contacts.index', null, $this->t('Contacte', 'Contacts', 'Контакты')],

            [NavigationMenu::Legal, 'pages.show', ['page' => 'privacy-policy'], $this->t('Confidențialitate', 'Privacy Policy', 'Конфиденциальность')],
            [NavigationMenu::Legal, 'pages.show', ['page' => 'cookie-policy'], $this->t('Cookie-uri', 'Cookie Policy', 'Cookie')],
            [NavigationMenu::Legal, 'pages.show', ['page' => 'terms-of-use'], $this->t('Termeni', 'Terms of Use', 'Условия использования')],
        ];

        foreach ($items as $index => [$menu, $route, $params, $label]) {
            /*
            | Сравнение по route_params на уровне запроса ненадёжно: колонка
            | приводится к JSON, и повторный запуск сидера плодил дубликаты
            | (три пункта в legal превращались в шесть). Ищем совпадение в PHP.
            */
            $existing = NavigationItem::query()
                ->where('menu', $menu->value)
                ->where('route_name', $route)
                ->get()
                ->first(fn (NavigationItem $item): bool => $item->route_params == $params);

            $attributes = [
                'menu' => $menu->value,
                'route_name' => $route,
                'route_params' => $params,
                'label' => $label,
                'sort_order' => $index,
                'is_active' => true,
            ];

            $existing ? $existing->update($attributes) : NavigationItem::query()->create($attributes);
        }
    }
}
