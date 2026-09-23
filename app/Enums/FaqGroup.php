<?php

namespace App\Enums;

/** Подборка вопросов: общая на главной и профильная в разделе услуг. */
enum FaqGroup: string
{
    case General = 'general';
    case Services = 'services';
    case Production = 'production';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Общие вопросы',
            self::Services => 'Услуги и сотрудничество',
            self::Production => 'Производство и сроки',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
