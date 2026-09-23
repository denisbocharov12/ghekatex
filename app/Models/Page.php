<?php

namespace App\Models;

use App\Enums\PageTemplate;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Контентная страница. `blocks` — конструктор секций, переводы лежат
 * внутри элементов, поэтому поле не входит в $translatable.
 */
class Page extends Model implements HasMedia
{
    use HasSeo, HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'subtitle', 'body'];

    protected $fillable = [
        'slug',
        'template',
        'title',
        'subtitle',
        'body',
        'blocks',
        'is_system',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'template' => PageTemplate::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('wide')->width(1920)->height(800)->nonQueued();
        $this->addMediaConversion('card')->width(800)->height(600)->nonQueued();
    }
}
