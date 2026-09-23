<?php

namespace App\Enums;

enum OfficeType: string
{
    case Office = 'office';
    case Factory = 'factory';
    case Warehouse = 'warehouse';

    public function label(): string
    {
        return match ($this) {
            self::Office => 'Офис',
            self::Factory => 'Производство',
            self::Warehouse => 'Склад',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
