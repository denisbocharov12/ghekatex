<?php

namespace App\Enums;

enum ContactRequestStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Answered = 'answered';
    case Spam = 'spam';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новая',
            self::InProgress => 'В работе',
            self::Answered => 'Отвечена',
            self::Spam => 'Спам',
            self::Archived => 'В архиве',
        };
    }

    /** Тон бейджа в админке. */
    public function tone(): string
    {
        return match ($this) {
            self::New => 'accent',
            self::InProgress => 'info',
            self::Answered => 'success',
            self::Spam => 'danger',
            self::Archived => 'muted',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
