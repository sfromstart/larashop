<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'option_values',
        'quantity',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'option_values' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:0',
        ];
    }

    // ── Relations ──

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ──

    public function getSubtotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }
}
