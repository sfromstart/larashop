<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    protected static array $reviewTitles = [
        '정말 좋아요!',
        '가성비 최고',
        '만족합니다',
        '추천합니다',
        '재구매 의사 있어요',
        '기대 이상이에요',
        '보통이에요',
        '별로예요',
        '배송이 빨라요',
        '품질이 좋네요',
        '색상이 예뻐요',
        '사이즈가 딱 맞아요',
        '선물용으로 좋아요',
        '가격 대비 괜찮아요',
        '다시 살 의향 없어요',
    ];

    protected static array $reviewContents = [
        '배송도 빠르고 상품 품질도 정말 좋습니다. 다음에도 또 구매할 예정이에요.',
        '가격 대비 품질이 아주 좋습니다. 주변에도 추천했어요.',
        '생각했던 것보다 훨씬 좋네요. 만족합니다!',
        '색상이 사진과 거의 동일합니다. 마감도 깔끔해요.',
        '사이즈가 정확하고 착용감이 좋습니다.',
        '선물용으로 구매했는데 받으신 분이 아주 좋아하셨습니다.',
        '포장이 꼼꼼하게 되어 왔어요. 제품 상태 완벽합니다.',
        '사용하기 편리하고 디자인도 예뻐요. 가성비 좋습니다.',
        '기대했던 것보다는 조금 아쉽지만 가격 생각하면 괜찮은 것 같아요.',
        '빠른 배송 감사합니다. 상품도 마음에 들어요.',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'order_id' => null,
            'rating' => fake()->numberBetween(3, 5),
            'title' => fake()->randomElement(self::$reviewTitles),
            'content' => fake()->randomElement(self::$reviewContents),
            'is_approved' => fake()->boolean(70),
            'admin_reply' => fake()->boolean(20) ? '리뷰 감사합니다! 좋은 경험을 하셨다니 기쁩니다.' : null,
            'replied_at' => null,
            'helpful_count' => fake()->numberBetween(0, 30),
        ];
    }
}
