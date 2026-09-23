<?php

namespace App\Models;

use App\Enums\PostType;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/** Новость, статья или обзор раздела «Новости и медиа». */
class Post extends Model implements HasMedia
{
    use HasSeo, HasTranslations, InteractsWithMedia, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'excerpt', 'body'];

    protected $fillable = [
        'category_id',
        'author_id',
        'type',
        'slug',
        'title',
        'excerpt',
        'body',
        'published_at',
        'is_featured',
        'reading_minutes',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'reading_minutes' => 'integer',
        'type' => PostType::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** Опубликованные: активные и с наступившей датой публикации. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
        $this->addMediaCollection('gallery')->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(480)->height(320)->nonQueued();
        $this->addMediaConversion('card')->width(960)->height(640)->nonQueued();
        $this->addMediaConversion('wide')->width(1920)->height(1080)->nonQueued();
    }
}
