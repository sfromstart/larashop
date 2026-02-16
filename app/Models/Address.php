<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'postal_code',
        'address',
        'address_detail',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──

    public function getFullAddressAttribute(): string
    {
        $full = "({$this->postal_code}) {$this->address}";

        if ($this->address_detail) {
            $full .= " {$this->address_detail}";
        }

        return $full;
    }
}
