<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'from_path',
        'to_path',
        'status_code',
        'is_active',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hits_count' => 'integer',
        'is_active' => 'boolean',
        'last_hit_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
