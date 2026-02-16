<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:0',
            'minimum_order_amount' => 'decimal:0',
            'maximum_discount' => 'decimal:0',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'per_user_limit' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ──

    public function scopeValid($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    // ── Methods ──

    public function calculateDiscount(int $orderAmount): int
    {
        if ($orderAmount < $this->minimum_order_amount) {
            return 0;
        }

        $discount = match ($this->type) {
            'percent' => (int) round($orderAmount * ($this->value / 100)),
            'fixed' => (int) $this->value,
            default => 0,
        };

        if ($this->maximum_discount !== null && $discount > $this->maximum_discount) {
            $discount = (int) $this->maximum_discount;
        }

        return min($discount, $orderAmount);
    }
}
