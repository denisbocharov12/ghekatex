<?php

namespace App\Models;

use App\Enums\ContactRequestSource;
use App\Enums\ContactRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** Заявка с любой формы сайта. */
class ContactRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'country',
        'subject',
        'message',
        'source',
        'related_type',
        'related_id',
        'locale',
        'status',
        'ip_hash',
        'user_agent',
        'handled_by',
        'handled_at',
        'admin_note',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
        'status' => ContactRequestStatus::class,
        'source' => ContactRequestSource::class,
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', ContactRequestStatus::New);
    }
}
