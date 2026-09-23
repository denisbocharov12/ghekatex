<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Общие для контентных моделей скоупы: активность и порядок вывода.
 */
trait Orderable
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Следующая позиция в списке — используется менеджерами при создании. */
    public static function nextSortOrder(): int
    {
        return (int) static::query()->max('sort_order') + 1;
    }
}
