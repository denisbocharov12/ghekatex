<?php

namespace App\Enums;

enum SettingType: string
{
    case String = 'string';
    case Text = 'text';
    case Html = 'html';
    case Image = 'image';
    case Json = 'json';
    case Boolean = 'bool';
    case Number = 'number';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
