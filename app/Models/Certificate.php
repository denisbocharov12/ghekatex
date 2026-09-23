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

class Certificate extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'issuer', 'description'];

    protected $fillable = [
        'name',
        'issuer',
        'description',
        'number',
        'issued_at',
        'valid_until',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'valid_until' => 'date',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
        // Скан сертификата для скачивания
        $this->addMediaCollection('document')->singleFile()->acceptsMimeTypes(config('ghekatex.media.doc_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(560)->nonQueued();
    }
}
