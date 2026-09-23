<?php

namespace App\Enums;

enum SettingGroup: string
{
    case General = 'general';
    case Contacts = 'contacts';
    case Social = 'social';
    case Analytics = 'analytics';
    case Seo = 'seo';
    case Home = 'home';
    case Legal = 'legal';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Общие',
            self::Contacts => 'Контакты',
            self::Social => 'Социальные сети',
            self::Analytics => 'Аналитика',
            self::Seo => 'SEO по умолчанию',
            self::Home => 'Главная страница',
            self::Legal => 'Юридические данные',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
