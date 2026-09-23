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

class Partner extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['description'];

    protected $fillable = [
        'name',
        'description',
        'website_url',
        'country_code',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Вектор не растрируем: логотип и так масштабируется без потерь
        if ($media?->mime_type === 'image/svg+xml') {
            return;
        }

        // Логотипы выводятся в монохроме, конверсия сохраняет прозрачность
        $this->addMediaConversion('logo')->width(320)->height(160)->nonQueued();
    }
}
