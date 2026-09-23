<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Правка строки интерфейса из админки. Лежит поверх JSON-словарей,
 * поэтому релиз с новыми текстами не затирает работу редактора.
 */
class TranslationOverride extends Model
{
    protected $fillable = ['locale', 'group', 'key', 'value'];
}
