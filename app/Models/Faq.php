<?php

namespace App\Models;

use App\Enums\FaqGroup;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/** Частый вопрос: короткий вопрос и развёрнутый ответ, оба переводимые. */
class Faq extends Model
{
    use HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['question', 'answer'];

    protected $fillable = ['group', 'question', 'answer', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'group' => FaqGroup::class,
    ];

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
