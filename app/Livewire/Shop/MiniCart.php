<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use App\Models\ProductOptionValue;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class MiniCart extends Component
{
    public bool $isOpen = false;

    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // 이벤트가 들어오면 컴포넌트가 재렌더됨
    }

    public function toggleCart(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function openCart(): void
    {
        $this->isOpen = true;
    }

    public function closeCart(): void
    {
        $this->isOpen = false;
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
    }

    public function getCartProperty()
    {
        return app(CartService::class)->getCart();
    }

    public function getItemCountProperty(): int
    {
        return $this->cart->items->sum('quantity');
    }

    public function getTotalProperty(): int
    {
        return $this->cart->items->sum(fn ($item) => $item->subtotal);
    }

    public function render()
    {
        $cart = $this->cart;
        $optionValueMap = ProductOptionValue::resolveForItems($cart->items);

        return view('livewire.shop.mini-cart', [
            'cart' => $cart,
            'itemCount' => $this->itemCount,
            'total' => $this->total,
            'optionValueMap' => $optionValueMap,
        ]);
    }
}
