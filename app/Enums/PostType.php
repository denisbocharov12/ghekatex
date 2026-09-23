<?php

namespace App\Enums;

enum PostType: string
{
    case News = 'news';
    case Article = 'article';
    case Review = 'review';

    public function label(): string
    {
        return match ($this) {
            self::News => 'Новость',
            self::Article => 'Статья',
            self::Review => 'Обзор',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
