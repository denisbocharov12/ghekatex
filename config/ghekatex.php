<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Локали сайта
    |--------------------------------------------------------------------------
    |
    | `default` отдаётся, когда язык не определён по URL, cookie или заголовку.
    | `labels` — подписи переключателя, `html` — значения для атрибута lang и hreflang.
    |
    */

    'locales' => [
        'default' => 'ro',
        'available' => ['ro', 'en', 'ru'],
        'labels' => [
            'ro' => 'Română',
            'en' => 'English',
            'ru' => 'Русский',
        ],
        'html' => [
            'ro' => 'ro-MD',
            'en' => 'en',
            'ru' => 'ru',
        ],
        'cookie' => 'ghekatex_locale',
    ],

    /*
    |--------------------------------------------------------------------------
    | Медиа
    |--------------------------------------------------------------------------
    */

    'media' => [
        'image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/svg+xml'],
        'video_mimes' => ['video/mp4', 'video/webm'],
        'doc_mimes' => ['application/pdf'],
        'max_image_kb' => 8192,
        'max_video_kb' => 102400,
    ],

    /*
    |--------------------------------------------------------------------------
    | Формы обратной связи
    |--------------------------------------------------------------------------
    |
    | `honeypot` — имя скрытого поля-ловушки, `min_seconds` — минимальное время
    | заполнения формы: быстрее заполняют только боты.
    |
    */

    'forms' => [
        'honeypot' => 'company_website',
        'min_seconds' => 3,
        'throttle' => '5,1',
        'notify_to' => env('GHEKATEX_NOTIFY_EMAIL', 'office@ghekatex.md'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie и согласие
    |--------------------------------------------------------------------------
    */

    'consent' => [
        'cookie' => 'ghekatex_consent',
        'lifetime_days' => 180,
        'policy_version' => '1.0',
        'categories' => ['necessary', 'analytics', 'marketing'],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'default_og_image' => '/brand/og-default.jpg',
        'twitter_card' => 'summary_large_image',
        'organization' => [
            'legal_name' => 'Ghekatex Group SRL',
            'brand' => 'GHEKATEX',
            'founded' => '2014',
            'country' => 'MD',
        ],
    ],
];
