<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Advantage extends Model
{
    use HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'description', 'value_suffix'];

    protected $fillable = [
        'icon',
        'title',
        'description',
        'value',
        'value_suffix',
        'is_counter',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_counter' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
