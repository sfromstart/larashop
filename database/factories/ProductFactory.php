<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    protected static array $koreanProductNames = [
        '프리미엄 코튼 티셔츠',
        '슬림핏 청바지',
        '가죽 크로스백',
        '무선 블루투스 이어폰',
        '스테인리스 텀블러',
        '오가닉 그래놀라',
        '히알루론산 세럼',
        '러닝화 에어맥스',
        '캐시미어 머플러',
        '스마트 체중계',
        '접이식 우산',
        '원목 책상',
        '에어프라이어',
        '캔버스 스니커즈',
        '실크 파자마 세트',
        '휴대용 보조배터리',
        '유기농 꿀',
        '향수 오드뚜왈렛',
        '요가 매트',
        '노트북 파우치',
        '스테인리스 프라이팬',
        'LED 무드등',
        '면 양말 세트',
        '비타민C 영양제',
        '천연 핸드크림',
        '미니 가습기',
        '데일리 백팩',
        '드라이 플라워 세트',
        '볼캡 모자',
        '핸드메이드 비누',
        '미니 블루투스 스피커',
        '오버사이즈 후드티',
        '골든 목걸이',
        '접이식 테이블',
        '아로마 디퓨저',
        '레더 지갑',
        '스포츠 선글라스',
        '세라믹 머그컵',
        '초경량 캐리어',
        '전기 면도기',
    ];

    protected static int $productIndex = 0;

    public function definition(): array
    {
        $name = self::$koreanProductNames[self::$productIndex % count(self::$koreanProductNames)];
        self::$productIndex++;

        $price = fake()->randomElement([
            9900, 12900, 15000, 19900, 24900, 29900,
            35000, 39900, 45000, 49900, 59900, 69900,
            79900, 89900, 99000, 129000, 159000, 199000,
            249000, 299000,
        ]);

        $hasComparePrice = fake()->boolean(40);

        return [
            'category_id' => Category::inRandomOrder()->first()?->id,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'short_description' => fake()->sentence(10),
            'description' => fake()->paragraphs(3, true),
            'price' => $price,
            'compare_price' => $hasComparePrice ? (int) ($price * fake()->randomFloat(2, 1.1, 1.5)) : null,
            'sku' => strtoupper(Str::random(3)) . '-' . fake()->unique()->numerify('######'),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'weight' => fake()->numberBetween(100, 5000),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'is_new' => fake()->boolean(30),
            'meta_title' => $name,
            'meta_description' => fake()->sentence(),
            'view_count' => fake()->numberBetween(0, 5000),
            'sold_count' => fake()->numberBetween(0, 500),
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
