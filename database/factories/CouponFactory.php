<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percent', 'fixed']);

        return [
            'code' => strtoupper(fake()->unique()->bothify('??##??')),
            'name' => fake()->randomElement(['신규가입 쿠폰', '여름 할인', '무료배송 쿠폰', '감사 쿠폰', 'VIP 쿠폰']),
            'type' => $type,
            'value' => $type === 'percent' ? fake()->randomElement([5, 10, 15, 20]) : fake()->randomElement([1000, 2000, 3000, 5000]),
            'minimum_order_amount' => fake()->randomElement([0, 10000, 20000, 30000, 50000]),
            'maximum_discount' => $type === 'percent' ? fake()->randomElement([5000, 10000, 20000]) : null,
            'usage_limit' => fake()->optional()->numberBetween(50, 500),
            'used_count' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subDays(10),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ];
    }
}
