<?php

namespace App\Enums;

enum VideoProvider: string
{
    case YouTube = 'youtube';
    case Vimeo = 'vimeo';
    case File = 'file';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
