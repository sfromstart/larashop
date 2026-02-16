<?php

namespace App\Livewire\Shop;

use App\Models\ProductOptionValue;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\CouponService;
use Livewire\Component;

class CartPage extends Component
{
    public string $couponCode = '';
    public string $couponMessage = '';
    public bool $couponValid = false;
    public int $discountAmount = 0;

    public function getCartProperty()
    {
        return app(CartService::class)->getCart();
    }

    public function getSubtotalProperty(): int
    {
        return $this->cart->items->sum(fn ($item) => $item->subtotal);
    }

    public function getShippingFeeProperty(): int
    {
        if ($this->subtotal <= 0) {
            return 0;
        }

        $shippingFee = (int) Setting::get('shop.shipping_fee', 3000);
        $freeShippingMin = (int) Setting::get('shop.free_shipping_min', 50000);

        if ($freeShippingMin > 0 && $this->subtotal >= $freeShippingMin) {
            return 0;
        }

        return $shippingFee;
    }

    public function getTotalProperty(): int
    {
        return max(0, $this->subtotal + $this->shippingFee - $this->discountAmount);
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart();
        $item = $cart->items()->find($itemId);

        if (!$item) {
            return;
        }

        if ($quantity <= 0) {
            $this->removeItem($itemId);
            return;
        }

        if ($item->product && $quantity > $item->product->stock_quantity) {
            $this->dispatch('toast', message: '재고가 부족합니다. (잔여: ' . $item->product->stock_quantity . '개)', type: 'error');
            return;
        }

        $cartService->updateQuantity($item, $quantity);

        // 쿠폰 적용 중이면 할인 재계산
        if ($this->couponValid) {
            $this->recalculateCoupon();
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $itemId): void
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart();
        $item = $cart->items()->find($itemId);

        if ($item) {
            $cartService->removeItem($item);
            $this->dispatch('toast', message: '상품이 장바구니에서 제거되었습니다.', type: 'info');
        }

        // 쿠폰 적용 중이면 할인 재계산
        if ($this->couponValid) {
            $this->recalculateCoupon();
        }

        $this->dispatch('cart-updated');
    }

    public function applyCoupon(): void
    {
        if (empty(trim($this->couponCode))) {
            $this->couponMessage = '쿠폰 코드를 입력해주세요.';
            $this->couponValid = false;
            return;
        }

        $couponService = app(CouponService::class);
        $result = $couponService->validate($this->couponCode, $this->subtotal);

        $this->couponValid = $result['valid'];
        $this->couponMessage = $result['message'];

        if ($result['valid'] && $result['coupon']) {
            $this->discountAmount = $couponService->apply($result['coupon'], $this->subtotal);
            $this->couponMessage = '쿠폰이 적용되었습니다. (-' . number_format($this->discountAmount) . '원)';
        } else {
            $this->discountAmount = 0;
        }
    }

    public function removeCoupon(): void
    {
        $this->couponCode = '';
        $this->couponMessage = '';
        $this->couponValid = false;
        $this->discountAmount = 0;
    }

    protected function recalculateCoupon(): void
    {
        if (!$this->couponValid || empty($this->couponCode)) {
            return;
        }

        $couponService = app(CouponService::class);
        // cart property를 다시 불러오기 위해 subtotal을 직접 재계산
        $cart = app(CartService::class)->getCart();
        $newSubtotal = $cart->items->sum(fn ($item) => $item->subtotal);

        $result = $couponService->validate($this->couponCode, $newSubtotal);

        if ($result['valid'] && $result['coupon']) {
            $this->discountAmount = $couponService->apply($result['coupon'], $newSubtotal);
        } else {
            $this->removeCoupon();
        }
    }

    public function render()
    {
        $cart = $this->cart;
        $cartItems = $cart->items;
        $optionValueMap = ProductOptionValue::resolveForItems($cartItems);

        return view('livewire.shop.cart-page', [
            'cart' => $cart,
            'cartItems' => $cartItems,
            'optionValueMap' => $optionValueMap,
        ]);
    }
}
