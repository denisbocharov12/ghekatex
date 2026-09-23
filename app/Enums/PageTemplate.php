<?php

namespace App\Enums;

/** Шаблон отрисовки контентной страницы на витрине. */
enum PageTemplate: string
{
    case Default = 'default';
    case About = 'about';
    case Legal = 'legal';
    case Contacts = 'contacts';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Обычная страница',
            self::About => 'О компании',
            self::Legal => 'Юридический документ',
            self::Contacts => 'Контакты',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
