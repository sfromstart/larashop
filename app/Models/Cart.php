<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ── Accessors ──

    public function getTotalAttribute(): int
    {
        return $this->items->sum(fn (CartItem $item) => $item->subtotal);
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
