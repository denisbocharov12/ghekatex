<?php

namespace App\Data\Schemas;

/**
 * Описание полей раздела: что переводится, что пишется как есть,
 * а что является списком-структурой с переводами внутри элементов.
 *
 * Схема заменяет два десятка почти одинаковых DTO и мапперов: правила
 * нормализации у всех разделов совпадают, различается только набор полей.
 */
final class FieldSchema
{
    /**
     * @param  array<int, string>  $translatable  переводимые поля модели
     * @param  array<int, string>  $plain  поля, которые пишутся как есть
     * @param  array<string, string>  $casts  поле => bool|int|float|array|date
     * @param  array<string, array{translatable: array<int, string>, plain?: array<int, string>, required?: string}>  $rows
     *                                                                                                                       списки-структуры: поле => описание элемента
     * @param  array<int, string>  $singleMedia  одиночные медиа-коллекции
     * @param  array<int, string>  $relations  связи many-to-many: имя связи
     */
    public function __construct(
        public readonly array $translatable = [],
        public readonly array $plain = [],
        public readonly array $casts = [],
        public readonly array $rows = [],
        public readonly array $singleMedia = [],
        public readonly array $relations = [],
    ) {}
}
