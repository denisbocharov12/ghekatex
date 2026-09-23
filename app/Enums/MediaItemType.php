<?php

namespace App\Enums;

enum MediaItemType: string
{
    case Image = 'image';
    case Video = 'video';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
