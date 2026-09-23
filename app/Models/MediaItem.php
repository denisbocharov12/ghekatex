<?php

namespace App\Models;

use App\Enums\MediaItemType;
use App\Enums\VideoProvider;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class MediaItem extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'caption'];

    protected $fillable = [
        'album_id',
        'type',
        'title',
        'caption',
        'video_provider',
        'video_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'type' => MediaItemType::class,
        'video_provider' => VideoProvider::class,
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(MediaAlbum::class, 'album_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
        // Постер нужен и для локального видео, и для ролика с внешнего хостинга
        $this->addMediaCollection('poster')->singleFile()->acceptsMimeTypes(config('ghekatex.media.image_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(480)->height(320)->nonQueued();
        $this->addMediaConversion('full')->width(1920)->height(1280)->nonQueued();
    }
}
