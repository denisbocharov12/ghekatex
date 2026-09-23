<?php

namespace App\Enums;

/** Откуда пришла заявка — влияет на маршрутизацию письма и аналитику. */
enum ContactRequestSource: string
{
    case HomeQuick = 'home_quick';
    case Contacts = 'contacts';
    case Service = 'service';
    case Product = 'product';
    case Footer = 'footer';
    case Dialog = 'dialog';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
