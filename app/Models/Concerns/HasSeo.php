<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Полиморфная SEO-карточка сущности: заголовки, описания и OG по локалям.
 */
trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Создаёт или обновляет SEO-карточку. Пустой массив ничего не меняет.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function syncSeo(array $attributes): void
    {
        if ($attributes === []) {
            return;
        }

        $this->seo()->updateOrCreate([], $attributes);
    }
}
