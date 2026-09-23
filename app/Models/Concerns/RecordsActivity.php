<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Запись изменений в журнал действий.
 *
 * Пишем только изменённые поля и не логируем пустые правки — иначе журнал
 * заполняется шумом от сохранений без изменений.
 */
trait RecordsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('content')
            ->setDescriptionForEvent(fn (string $event) => match ($event) {
                'created' => 'Создано: '.class_basename($this),
                'updated' => 'Изменено: '.class_basename($this),
                'deleted' => 'Удалено: '.class_basename($this),
                default => $event.': '.class_basename($this),
            });
    }
}
