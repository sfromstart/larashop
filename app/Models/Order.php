<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total',
        'shipping_name',
        'shipping_phone',
        'shipping_postal_code',
        'shipping_address',
        'shipping_address_detail',
        'shipping_memo',
        'payment_method',
        'payment_id',
        'paid_at',
        'tracking_number',
        'tracking_url',
        'admin_memo',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:0',
            'shipping_fee' => 'decimal:0',
            'discount_amount' => 'decimal:0',
            'total' => 'decimal:0',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors ──

    public function getStatusLabelAttribute(): string
    {
        return OrderStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total) . '원';
    }

    // ── Static Methods ──

    public static function generateOrderNumber(): string
    {
        $prefix = now()->format('Ymd');
        $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $number = $prefix . $random;

        while (static::where('order_number', $number)->exists()) {
            $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $number = $prefix . $random;
        }

        return $number;
    }
}
