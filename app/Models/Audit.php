<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUpdate(): bool
    {
        return $this->event === 'updated';
    }

    public function changedFields(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        return collect($this->new_values)
            ->keys()
            ->filter(fn ($key) => array_key_exists($key, $this->old_values))
            ->values()
            ->all();
    }
}
