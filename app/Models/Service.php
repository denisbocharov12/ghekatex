<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Услуга. `highlights` и `process_steps` — упорядоченные списки,
 * переводы хранятся внутри элементов, поэтому поля не в $translatable.
 */
class Service extends Model implements HasMedia
{
    use HasSeo, HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    /** Иконки ключевых фактов — сопоставляются с набором lucide на витрине. */
    public const HIGHLIGHT_ICONS = [
        'clock', 'shield-check', 'ruler', 'truck', 'award', 'factory',
        'scissors', 'palette', 'package', 'users', 'globe', 'badge-check',
    ];

    public array $translatable = ['name', 'short_description', 'description', 'lead_time'];

    protected $fillable = [
        'slug',
        'name',
        'short_description',
        'description',
        'lead_time',
        'icon',
        'highlights',
        'process_steps',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'process_steps' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
        $this->addMediaCollection('gallery')->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(480)->height(360)->nonQueued();
        $this->addMediaConversion('card')->width(960)->height(720)->nonQueued();
        $this->addMediaConversion('wide')->width(1920)->height(1080)->nonQueued();
    }
}
