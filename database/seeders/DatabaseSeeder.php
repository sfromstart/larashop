<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Admin User ──
        User::factory()->create([
            'name' => '관리자',
            'email' => 'admin@larashop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '010-0000-0000',
            'point_balance' => 0,
        ]);

        // ── 2. Regular Users (5) ──
        $users = User::factory(5)->create();

        // ── 3. Categories: 8 root categories with 2-3 children each ──
        $rootCategories = [
            '의류' => ['티셔츠', '바지', '아우터'],
            '신발' => ['운동화', '구두', '슬리퍼'],
            '가방' => ['백팩', '크로스백'],
            '전자제품' => ['이어폰', '스피커', '충전기'],
            '생활용품' => ['주방', '욕실'],
            '식품' => ['건강식품', '간식', '음료'],
            '뷰티' => ['스킨케어', '메이크업', '향수'],
            '스포츠' => ['요가', '러닝'],
        ];

        $allChildCategories = collect();

        $sortOrder = 0;
        foreach ($rootCategories as $rootName => $childNames) {
            $rootSlug = Str::slug($rootName, '-');
            // Ensure unique slug
            if (Category::where('slug', $rootSlug)->exists()) {
                $rootSlug .= '-' . Str::random(3);
            }

            $root = Category::create([
                'name' => $rootName,
                'slug' => $rootSlug,
                'description' => "{$rootName} 카테고리입니다.",
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'meta_title' => $rootName,
            ]);

            $childSort = 0;
            foreach ($childNames as $childName) {
                $childSlug = Str::slug($rootName . '-' . $childName, '-');
                if (Category::where('slug', $childSlug)->exists()) {
                    $childSlug .= '-' . Str::random(3);
                }

                $child = Category::create([
                    'parent_id' => $root->id,
                    'name' => $childName,
                    'slug' => $childSlug,
                    'description' => "{$childName} 카테고리입니다.",
                    'sort_order' => $childSort++,
                    'is_active' => true,
                    'meta_title' => $childName,
                ]);

                $allChildCategories->push($child);
            }
        }

        // ── 4. Products (50) with images, 30% with options ──
        // 상품명 → 카테고리 매핑 (올바른 분류)
        $productCategoryMap = [
            '프리미엄 코튼 티셔츠' => '티셔츠',
            '슬림핏 청바지' => '바지',
            '가죽 크로스백' => '크로스백',
            '무선 블루투스 이어폰' => '이어폰',
            '스테인리스 텀블러' => '주방',
            '오가닉 그래놀라' => '건강식품',
            '히알루론산 세럼' => '스킨케어',
            '러닝화 에어맥스' => '운동화',
            '캐시미어 머플러' => '아우터',
            '스마트 체중계' => '러닝',
            '접이식 우산' => '욕실',
            '원목 책상' => '주방',
            '에어프라이어' => '주방',
            '캔버스 스니커즈' => '운동화',
            '실크 파자마 세트' => '아우터',
            '휴대용 보조배터리' => '충전기',
            '유기농 꿀' => '건강식품',
            '향수 오드뚜왈렛' => '향수',
            '요가 매트' => '요가',
            '노트북 파우치' => '백팩',
            '스테인리스 프라이팬' => '주방',
            'LED 무드등' => '욕실',
            '면 양말 세트' => '티셔츠',
            '비타민C 영양제' => '건강식품',
            '천연 핸드크림' => '스킨케어',
            '미니 가습기' => '욕실',
            '데일리 백팩' => '백팩',
            '드라이 플라워 세트' => '욕실',
            '볼캡 모자' => '아우터',
            '핸드메이드 비누' => '욕실',
            '미니 블루투스 스피커' => '스피커',
            '오버사이즈 후드티' => '티셔츠',
            '골든 목걸이' => '크로스백',
            '접이식 테이블' => '주방',
            '아로마 디퓨저' => '욕실',
            '레더 지갑' => '크로스백',
            '스포츠 선글라스' => '러닝',
            '세라믹 머그컵' => '주방',
            '초경량 캐리어' => '백팩',
            '전기 면도기' => '욕실',
        ];
        $categoryNameToId = Category::pluck('id', 'name')->toArray();

        $products = Product::factory(50)
            ->sequence(function ($sequence) use ($productCategoryMap, $categoryNameToId, $allChildCategories) {
                $names = array_keys($productCategoryMap);
                $name = $names[$sequence->index % count($names)];
                $catName = $productCategoryMap[$name];
                return [
                    'category_id' => $categoryNameToId[$catName] ?? $allChildCategories->random()->id,
                ];
            })
            ->create();

        foreach ($products as $product) {
            // Create 2-4 images per product
            $imageCount = rand(2, 4);
            $picsumId = rand(1, 800);

            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => "https://picsum.photos/id/" . ($picsumId + $i) . "/640/640",
                    'alt_text' => $product->name . " 이미지 " . ($i + 1),
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }

            // 30% of products have options
            if (rand(1, 100) <= 30) {
                $optionTypes = [
                    '색상' => ['블랙', '화이트', '네이비', '베이지', '그레이'],
                    '사이즈' => ['S', 'M', 'L', 'XL'],
                ];

                $selectedOptions = array_rand($optionTypes, rand(1, 2));
                if (!is_array($selectedOptions)) {
                    $selectedOptions = [$selectedOptions];
                }

                $optionSort = 0;
                foreach ($selectedOptions as $optionName) {
                    $option = ProductOption::create([
                        'product_id' => $product->id,
                        'name' => $optionName,
                        'sort_order' => $optionSort++,
                    ]);

                    $values = $optionTypes[$optionName];
                    $valueSort = 0;
                    foreach ($values as $value) {
                        $priceModifier = $optionName === '사이즈' && in_array($value, ['XL'])
                            ? rand(1, 3) * 1000
                            : 0;

                        ProductOptionValue::create([
                            'product_option_id' => $option->id,
                            'value' => $value,
                            'price_modifier' => $priceModifier,
                            'stock_quantity' => rand(0, 50),
                            'sort_order' => $valueSort++,
                        ]);
                    }
                }
            }
        }

        // ── 5. Reviews (100) ──
        Review::factory(100)
            ->sequence(fn ($sequence) => [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
            ])
            ->create();

        // ── 6. Coupons (3 specific) ──
        Coupon::create([
            'code' => 'WELCOME10',
            'name' => '신규가입 10% 할인',
            'type' => 'percent',
            'value' => 10,
            'minimum_order_amount' => 20000,
            'maximum_discount' => 10000,
            'usage_limit' => null,
            'used_count' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SUMMER5000',
            'name' => '여름 5,000원 할인',
            'type' => 'fixed',
            'value' => 5000,
            'minimum_order_amount' => 30000,
            'maximum_discount' => null,
            'usage_limit' => 200,
            'used_count' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subWeek(),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'name' => '무료배송 쿠폰',
            'type' => 'fixed',
            'value' => 3000,
            'minimum_order_amount' => 15000,
            'maximum_discount' => null,
            'usage_limit' => 500,
            'used_count' => 0,
            'per_user_limit' => 2,
            'starts_at' => now()->subDays(5),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        // ── 7. Settings Defaults ──
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'LaraShop'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Laravel 기반 쇼핑몰'],
            ['group' => 'general', 'key' => 'site_logo', 'value' => null],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => null],
            ['group' => 'shipping', 'key' => 'default_shipping_fee', 'value' => '3000'],
            ['group' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '50000'],
            ['group' => 'order', 'key' => 'order_prefix', 'value' => 'LS'],
            ['group' => 'order', 'key' => 'bank_info', 'value' => '국민은행 123-456-789012 (주)라라샵'],
            ['group' => 'point', 'key' => 'point_rate', 'value' => '1'],
            ['group' => 'point', 'key' => 'review_point', 'value' => '500'],
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'LaraShop - 온라인 쇼핑몰'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Laravel로 만든 최고의 온라인 쇼핑몰'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
