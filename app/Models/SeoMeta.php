<?php

namespace App\Models;

use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/**
 * SEO-карточка сущности. Хранится отдельно, чтобы контентные таблицы
 * не разрастались и чтобы редактор SEO работал единообразно для всех разделов.
 */
class SeoMeta extends Model
{
    use HasTranslations, SerializesTranslations;

    protected $table = 'seo_meta';

    public array $translatable = ['title', 'description', 'keywords', 'og_title', 'og_description'];

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'schema_type',
        'sitemap_priority',
        'sitemap_frequency',
    ];

    protected $casts = [
        'sitemap_priority' => 'float',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
