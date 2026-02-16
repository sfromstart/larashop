<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductOptionValue;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart();

        // 카트가 비어있으면 장바구니로 리다이렉트
        if ($cart->items->count() === 0) {
            return redirect()->route('shop.cart.index')->with('error', '장바구니가 비어있습니다.');
        }

        $cart->load('items.product.primaryImage');

        $user = $request->user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();
        $defaultAddress = $user->defaultAddress;

        $subtotal = $cart->items->sum(fn ($item) => $item->subtotal);
        $shippingFee = $this->calculateShippingFee($subtotal);
        $optionValueMap = ProductOptionValue::resolveForItems($cart->items);

        return view('shop.checkout.index', compact(
            'cart',
            'addresses',
            'defaultAddress',
            'subtotal',
            'shippingFee',
            'optionValueMap',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:50'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_address_detail' => ['nullable', 'string', 'max:255'],
            'shipping_memo' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'string', 'in:bank_transfer'],
            'coupon_code' => ['nullable', 'string'],
            'agree' => ['required', 'accepted'],
        ]);

        $cartService = app(CartService::class);
        $cart = $cartService->getCart();

        if ($cart->items->count() === 0) {
            return redirect()->route('shop.cart.index')->with('error', '장바구니가 비어있습니다.');
        }

        $cart->load('items.product');

        $subtotal = $cart->items->sum(fn ($item) => $item->subtotal);
        $shippingFee = $this->calculateShippingFee($subtotal);

        // 쿠폰 처리
        $discountAmount = 0;
        if (!empty($validated['coupon_code'])) {
            $couponService = app(CouponService::class);
            $result = $couponService->validate($validated['coupon_code'], $subtotal);
            if ($result['valid'] && $result['coupon']) {
                $discountAmount = $couponService->apply($result['coupon'], $subtotal);
                $couponService->markUsed($result['coupon']);
            }
        }

        $shippingData = [
            'name' => $validated['shipping_name'],
            'phone' => $validated['shipping_phone'],
            'postal_code' => $validated['shipping_postal_code'],
            'address' => $validated['shipping_address'],
            'address_detail' => $validated['shipping_address_detail'] ?? null,
            'memo' => $validated['shipping_memo'] ?? null,
        ];

        $orderService = app(OrderService::class);
        $order = $orderService->createFromCart(
            cart: $cart,
            shippingData: $shippingData,
            paymentMethod: $validated['payment_method'],
            shippingFee: $shippingFee,
            discountAmount: $discountAmount,
        );

        return redirect()->route('shop.order.complete', $order)->with('success', '주문이 완료되었습니다.');
    }

    protected function calculateShippingFee(int $subtotal): int
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $shippingFee = (int) Setting::get('shop.shipping_fee', 3000);
        $freeShippingMin = (int) Setting::get('shop.free_shipping_min', 50000);

        if ($freeShippingMin > 0 && $subtotal >= $freeShippingMin) {
            return 0;
        }

        return $shippingFee;
    }
}
