<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    /**
     * Validate a coupon code against an order amount.
     *
     * @return array{valid: bool, message: string, coupon: Coupon|null}
     */
    public function validate(string $code, int $orderAmount): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => '유효하지 않은 쿠폰 코드입니다.', 'coupon' => null];
        }

        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => '비활성화된 쿠폰입니다.', 'coupon' => null];
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return ['valid' => false, 'message' => '아직 사용 기간이 아닙니다.', 'coupon' => null];
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return ['valid' => false, 'message' => '만료된 쿠폰입니다.', 'coupon' => null];
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => '쿠폰 사용 한도가 초과되었습니다.', 'coupon' => null];
        }

        if ($orderAmount < $coupon->minimum_order_amount) {
            $min = number_format($coupon->minimum_order_amount);
            return ['valid' => false, 'message' => "최소 주문금액 {$min}원 이상부터 사용 가능합니다.", 'coupon' => null];
        }

        return ['valid' => true, 'message' => '사용 가능한 쿠폰입니다.', 'coupon' => $coupon];
    }

    /**
     * Apply a coupon to an order amount and return the discount.
     */
    public function apply(Coupon $coupon, int $orderAmount): int
    {
        return $coupon->calculateDiscount($orderAmount);
    }

    /**
     * Mark a coupon as used (increment the used count).
     */
    public function markUsed(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
