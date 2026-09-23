<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\SerializesTranslations;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CompanyMilestone extends Model
{
    use HasTranslations, Orderable, RecordsActivity, SerializesTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'year',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
