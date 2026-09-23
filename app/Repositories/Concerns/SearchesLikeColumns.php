<?php

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait SearchesLikeColumns
{
    /**
     * Поиск по набору колонок. Переводимые поля лежат в JSON, поэтому
     * сравнение идёт по сырому значению колонки — этого достаточно для подсказок.
     *
     * @param  array<int, string>  $columns
     */
    protected function applyLike(Builder $query, string $term, array $columns): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $pattern = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        foreach ($columns as $index => $column) {
            $index === 0
                ? $query->where($column, 'like', $pattern)
                : $query->orWhere($column, 'like', $pattern);
        }

        return $query;
    }
}
