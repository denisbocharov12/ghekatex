<?php

namespace App\Enums;

/**
 * Роли панели управления. `SuperAdmin` обходит проверки прав через Gate::before.
 */
enum RoleName: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case ContentManager = 'content-manager';
    case Sales = 'sales';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Супер-администратор',
            self::Admin => 'Администратор',
            self::ContentManager => 'Контент-менеджер',
            self::Sales => 'Отдел продаж',
            self::Viewer => 'Наблюдатель',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}
