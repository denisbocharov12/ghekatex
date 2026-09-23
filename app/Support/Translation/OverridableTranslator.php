<?php

namespace App\Support\Translation;

use App\Services\TranslationCatalogService;
use Closure;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Translation\Translator;

/**
 * Обёртка над штатным переводчиком: сначала смотрит правку из админки,
 * затем отдаёт работу файловому переводчику.
 *
 * Сервис каталога резолвится лениво — переводчик создаётся раньше,
 * чем становится доступна база.
 *
 * @mixin Translator
 */
class OverridableTranslator implements TranslatorContract
{
    private ?TranslationCatalogService $catalog = null;

    public function __construct(
        private readonly TranslatorContract $inner,
        private readonly Closure $catalogResolver,
    ) {}

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale ??= $this->getLocale();
        $override = $this->override($key, $locale);

        if ($override !== null) {
            return $this->replace($override, $replace);
        }

        return $this->inner->get($key, $replace, $locale, $fallback);
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $this->inner->choice($key, $number, $replace, $locale);
    }

    public function getLocale(): string
    {
        return $this->inner->getLocale();
    }

    public function setLocale($locale): void
    {
        $this->inner->setLocale($locale);
    }

    /** @param  array<string, mixed>  $parameters */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->inner->{$method}(...$parameters);
    }

    private function override(string $key, string $locale): ?string
    {
        // До готовности базы правок нет — работают только файлы
        if (! app()->bound('db') || ! app()->isBooted()) {
            return null;
        }

        try {
            $this->catalog ??= ($this->catalogResolver)();
            $value = $this->catalog->overrides($locale, 'php')[$key] ?? null;
        } catch (\Throwable) {
            return null;
        }

        return $value === null || $value === '' ? null : $value;
    }

    /** @param  array<string, mixed>  $replace */
    private function replace(string $line, array $replace): string
    {
        foreach ($replace as $search => $value) {
            $line = str_replace([':'.$search, ':'.ucfirst($search), ':'.strtoupper($search)], (string) $value, $line);
        }

        return $line;
    }
}
