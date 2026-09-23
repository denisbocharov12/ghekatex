<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class HeroSlide extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = [
        'eyebrow',
        'title',
        'description',
        'cta_label',
        'secondary_cta_label',
    ];

    protected $fillable = [
        'eyebrow',
        'title',
        'description',
        'cta_label',
        'cta_url',
        'secondary_cta_label',
        'secondary_cta_url',
        'overlay_opacity',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'overlay_opacity' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(config('ghekatex.media.image_mimes'));

        // Фоновое видео слайда — необязательное, воспроизводится без звука
        $this->addMediaCollection('video')
            ->singleFile()
            ->acceptsMimeTypes(config('ghekatex.media.video_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('hero')->width(2200)->height(1200)->nonQueued();
        $this->addMediaConversion('mobile')->width(900)->height(1200)->nonQueued();
    }
}
