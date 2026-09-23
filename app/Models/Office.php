<?php

namespace App\Models;

use App\Enums\OfficeType;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Office extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'address', 'city', 'working_hours'];

    protected $fillable = [
        'type',
        'name',
        'address',
        'city',
        'country_code',
        'postal_code',
        'phones',
        'emails',
        'working_hours',
        'latitude',
        'longitude',
        'is_primary',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'type' => OfficeType::class,
    ];

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->width(800)->height(600)->nonQueued();
    }
}
