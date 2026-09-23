<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/** Производственная площадка: мощности, технологии, оборудование. */
class Facility extends Model implements HasMedia
{
    use HasSeo, HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'summary', 'description'];

    protected $fillable = [
        'slug',
        'name',
        'summary',
        'description',
        'specs',
        'capacity_per_month',
        'employees_count',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'specs' => 'array',
        'capacity_per_month' => 'integer',
        'employees_count' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
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
