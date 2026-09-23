<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/** Альбом медиа-галереи: съёмка с производства, лукбук, выставка. */
class MediaAlbum extends Model
{
    use HasSeo, HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = ['slug', 'title', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function items(): HasMany
    {
        return $this->hasMany(MediaItem::class, 'album_id')->orderBy('sort_order');
    }
}
