<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Preparing = 'preparing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => '결제대기',
            self::Paid => '결제완료',
            self::Preparing => '상품준비중',
            self::Shipped => '배송중',
            self::Delivered => '배송완료',
            self::Cancelled => '주문취소',
            self::Refunded => '환불완료',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Paid => 'blue',
            self::Preparing => 'indigo',
            self::Shipped => 'purple',
            self::Delivered => 'green',
            self::Cancelled => 'red',
            self::Refunded => 'gray',
        };
    }
}
