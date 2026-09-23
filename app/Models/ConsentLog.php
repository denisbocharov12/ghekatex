<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * След согласия на cookie. Храним хеш IP и анонимный идентификатор —
 * этого достаточно для доказательства согласия по GDPR и не создаёт
 * лишних персональных данных.
 */
class ConsentLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'anonymous_id',
        'ip_hash',
        'categories',
        'policy_version',
        'locale',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'created_at' => 'datetime',
    ];
}
