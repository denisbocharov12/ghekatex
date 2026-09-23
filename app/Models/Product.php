<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Изделие витрины. Цен нет: сайт корпоративный, стоимость обсуждается
 * по запросу, поэтому карточка ведёт к форме заявки.
 */
class Product extends Model implements HasMedia
{
    use HasSeo, HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'short_description', 'description', 'composition'];

    protected $fillable = [
        'category_id',
        'slug',
        'article',
        'name',
        'short_description',
        'description',
        'composition',
        'attributes',
        'min_order_quantity',
        'lead_time_days',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'min_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function fabrics(): BelongsToMany
    {
        return $this->belongsToMany(Fabric::class);
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class);
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
        // Лукбук ведём в портретном формате 3:4
        $this->addMediaConversion('thumb')->width(480)->height(640)->nonQueued();
        $this->addMediaConversion('card')->width(900)->height(1200)->nonQueued();
        $this->addMediaConversion('full')->width(1600)->height(2133)->nonQueued();
    }
}
