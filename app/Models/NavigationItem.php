<?php

namespace App\Models;

use App\Enums\NavigationMenu;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class NavigationItem extends Model
{
    use HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['label'];

    protected $fillable = [
        'menu',
        'parent_id',
        'label',
        'route_name',
        'route_params',
        'url',
        'icon',
        'opens_in_new_tab',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'route_params' => 'array',
        'opens_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'menu' => NavigationMenu::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }
}
