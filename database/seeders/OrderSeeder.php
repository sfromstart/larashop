<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductOptionValue;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 일반 유저 가져오기 (admin 제외)
        $users = User::where('role', '!=', 'admin')->get();

        if ($users->isEmpty()) {
            $this->command->error('일반 유저가 없습니다. DatabaseSeeder를 먼저 실행하세요.');
            return;
        }

        // 상품 가져오기
        $products = Product::with(['primaryImage', 'options.values'])->where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command->error('상품이 없습니다. DatabaseSeeder를 먼저 실행하세요.');
            return;
        }

        // 옵션 있는 상품 / 없는 상품 구분
        $productsWithOptions = $products->filter(fn ($p) => $p->options->isNotEmpty());
        $productsWithoutOptions = $products->filter(fn ($p) => $p->options->isEmpty());

        $this->command->info("유저: {$users->count()}명, 상품: {$products->count()}개 (옵션상품: {$productsWithOptions->count()}개)");

        // ── 다양한 시나리오의 주문 20건 생성 ──
        $scenarios = [
            // 결제대기 주문들
            ['status' => 'pending', 'days_ago' => 0, 'items_count' => 1, 'with_option' => false],
            ['status' => 'pending', 'days_ago' => 1, 'items_count' => 2, 'with_option' => true],
            ['status' => 'pending', 'days_ago' => 0, 'items_count' => 3, 'with_option' => false],

            // 결제완료 주문들
            ['status' => 'paid', 'days_ago' => 1, 'items_count' => 1, 'with_option' => true],
            ['status' => 'paid', 'days_ago' => 2, 'items_count' => 2, 'with_option' => false],
            ['status' => 'paid', 'days_ago' => 1, 'items_count' => 1, 'with_option' => false],

            // 상품준비중
            ['status' => 'preparing', 'days_ago' => 2, 'items_count' => 2, 'with_option' => true],
            ['status' => 'preparing', 'days_ago' => 3, 'items_count' => 1, 'with_option' => false],

            // 배송중
            ['status' => 'shipped', 'days_ago' => 4, 'items_count' => 1, 'with_option' => true],
            ['status' => 'shipped', 'days_ago' => 3, 'items_count' => 2, 'with_option' => false],
            ['status' => 'shipped', 'days_ago' => 5, 'items_count' => 3, 'with_option' => true],

            // 배송완료
            ['status' => 'delivered', 'days_ago' => 7, 'items_count' => 2, 'with_option' => false],
            ['status' => 'delivered', 'days_ago' => 10, 'items_count' => 1, 'with_option' => true],
            ['status' => 'delivered', 'days_ago' => 14, 'items_count' => 2, 'with_option' => false],
            ['status' => 'delivered', 'days_ago' => 8, 'items_count' => 1, 'with_option' => false],
            ['status' => 'delivered', 'days_ago' => 20, 'items_count' => 3, 'with_option' => true],

            // 취소 주문
            ['status' => 'cancelled', 'days_ago' => 5, 'items_count' => 1, 'with_option' => false],
            ['status' => 'cancelled', 'days_ago' => 3, 'items_count' => 2, 'with_option' => true],

            // 환불 주문
            ['status' => 'refunded', 'days_ago' => 12, 'items_count' => 1, 'with_option' => false],

            // 큰 주문 (할인 적용)
            ['status' => 'paid', 'days_ago' => 1, 'items_count' => 4, 'with_option' => true, 'discount' => 5000],
        ];

        $addresses = [
            ['name' => '김철수', 'phone' => '010-1234-5678', 'postal' => '06035', 'addr' => '서울특별시 강남구 가로수길 50', 'detail' => '3층 301호'],
            ['name' => '이영희', 'phone' => '010-9876-5432', 'postal' => '03045', 'addr' => '서울특별시 종로구 세종대로 172', 'detail' => '1동 1503호'],
            ['name' => '박지민', 'phone' => '010-5555-1234', 'postal' => '48058', 'addr' => '부산광역시 해운대구 해운대해변로 264', 'detail' => 'A동 2001호'],
            ['name' => '최수현', 'phone' => '010-3333-7890', 'postal' => '41585', 'addr' => '대구광역시 북구 호국로 807', 'detail' => '201호'],
            ['name' => '정민호', 'phone' => '010-7777-4321', 'postal' => '61452', 'addr' => '광주광역시 동구 금남로 234', 'detail' => null],
        ];

        $memos = [
            '문 앞에 놓아주세요',
            '경비실에 맡겨주세요',
            '택배함에 넣어주세요',
            '배송 전 연락 부탁드립니다',
            '부재 시 연락 부탁드립니다',
            null,
        ];

        $trackingNumbers = [
            '1234567890123',
            '9876543210987',
            '5555666677778',
            '1111222233334',
            '4444555566667',
        ];

        $count = 0;
        foreach ($scenarios as $i => $scenario) {
            $user = $users->random();
            $address = $addresses[array_rand($addresses)];
            $createdAt = now()->subDays($scenario['days_ago'])->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // 주문 상품 선택
            $orderProducts = collect();
            for ($j = 0; $j < $scenario['items_count']; $j++) {
                if ($scenario['with_option'] && $productsWithOptions->isNotEmpty()) {
                    $orderProducts->push($productsWithOptions->random());
                } else {
                    $orderProducts->push($productsWithoutOptions->isNotEmpty() ? $productsWithoutOptions->random() : $products->random());
                }
            }

            // 소계 계산
            $subtotal = 0;
            $items = [];
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $unitPrice = (int) $product->price;

                // 옵션 값 설정
                $optionValues = null;
                if ($product->options->isNotEmpty()) {
                    $optionValues = [];
                    foreach ($product->options as $option) {
                        $value = $option->values->random();
                        $optionValues[$option->id] = $value->id;
                        $unitPrice += (int) $value->price_modifier;
                    }
                }

                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->primaryImage?->path,
                    'option_values' => $optionValues,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // 배송비 계산
            $shippingFee = $subtotal >= 50000 ? 0 : 3000;
            $discount = $scenario['discount'] ?? 0;
            $total = max(0, $subtotal + $shippingFee - $discount);

            // 상태별 타임스탬프
            $paidAt = null;
            $shippedAt = null;
            $deliveredAt = null;
            $cancelledAt = null;
            $trackingNumber = null;

            switch ($scenario['status']) {
                case 'paid':
                    $paidAt = $createdAt->copy()->addHours(rand(1, 12));
                    break;
                case 'preparing':
                    $paidAt = $createdAt->copy()->addHours(rand(1, 6));
                    break;
                case 'shipped':
                    $paidAt = $createdAt->copy()->addHours(rand(1, 6));
                    $shippedAt = $paidAt->copy()->addHours(rand(12, 48));
                    $trackingNumber = $trackingNumbers[array_rand($trackingNumbers)];
                    break;
                case 'delivered':
                    $paidAt = $createdAt->copy()->addHours(rand(1, 6));
                    $shippedAt = $paidAt->copy()->addHours(rand(12, 36));
                    $deliveredAt = $shippedAt->copy()->addDays(rand(1, 3));
                    $trackingNumber = $trackingNumbers[array_rand($trackingNumbers)];
                    break;
                case 'cancelled':
                    $cancelledAt = $createdAt->copy()->addHours(rand(1, 24));
                    break;
                case 'refunded':
                    $paidAt = $createdAt->copy()->addHours(rand(1, 6));
                    $cancelledAt = $paidAt->copy()->addDays(rand(1, 3));
                    break;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => $scenario['status'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'total' => $total,
                'shipping_name' => $address['name'],
                'shipping_phone' => $address['phone'],
                'shipping_postal_code' => $address['postal'],
                'shipping_address' => $address['addr'],
                'shipping_address_detail' => $address['detail'],
                'shipping_memo' => $memos[array_rand($memos)],
                'payment_method' => 'bank_transfer',
                'paid_at' => $paidAt,
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
                'cancelled_at' => $cancelledAt,
                'tracking_number' => $trackingNumber,
                'admin_memo' => $scenario['status'] === 'cancelled' ? '고객 요청에 의한 취소' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            $count++;
            $statusLabel = OrderStatus::tryFrom($scenario['status'])?->label() ?? $scenario['status'];
            $this->command->line("  [{$count}] #{$order->order_number} - {$address['name']} - {$statusLabel} - " . number_format($total) . "원 (상품 {$scenario['items_count']}개)");
        }

        $this->command->info("총 {$count}건의 테스트 주문이 생성되었습니다.");
    }
}
