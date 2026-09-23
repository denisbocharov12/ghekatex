<?php

namespace App\Enums;

enum NavigationMenu: string
{
    case Header = 'header';
    case FooterPrimary = 'footer_primary';
    case FooterSecondary = 'footer_secondary';
    case Legal = 'legal';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'Верхнее меню',
            self::FooterPrimary => 'Футер — первая колонка',
            self::FooterSecondary => 'Футер — вторая колонка',
            self::Legal => 'Юридические ссылки',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
