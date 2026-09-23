<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Fabric extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'description', 'composition'];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'composition',
        'weight_gsm',
        'color_hex',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight_gsm' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function registerMediaCollections(): void
    {
        // Фотография выкраски ткани
        $this->addMediaCollection('swatch')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('swatch')->width(400)->height(400)->nonQueued();
    }
}
