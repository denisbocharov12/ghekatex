<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/** Вид обработки: вышивка, печать, стирка, плиссировка. */
class Treatment extends Model
{
    use HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = ['slug', 'name', 'description', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
