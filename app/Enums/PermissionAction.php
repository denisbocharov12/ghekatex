<?php

namespace App\Enums;

enum PermissionAction: string
{
    case View = 'view';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';

    public function label(): string
    {
        return match ($this) {
            self::View => 'Просмотр',
            self::Create => 'Создание',
            self::Update => 'Изменение',
            self::Delete => 'Удаление',
        };
    }
}
