<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Сайт на пустой базе.
 *
 * Сразу после установки контента ещё нет: ни настроек, ни меню, ни изделий.
 * Главная обязана открыться и в этом состоянии — иначе разработчик получает
 * 500 на первом же запуске проекта.
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_survives_empty_database(): void
    {
        $this->withoutExceptionHandling();

        $this->get('/')->assertOk()->assertSee('"component":"Home"', escape: false);
    }
}
