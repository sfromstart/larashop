<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;
    public bool $quick = false;
    public int $quantity = 1;
    public array $selectedOptions = [];

    public function mount(Product $product, bool $quick = false): void
    {
        $this->product = $product;
        $this->quick = $quick;

        // 옵션 기본값 설정
        if ($product->relationLoaded('options')) {
            foreach ($product->options as $option) {
                $firstValue = $option->values->first();
                if ($firstValue) {
                    $this->selectedOptions[$option->id] = (string) $firstValue->id;
                }
            }
        }
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->product->stock_quantity) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        if ($this->product->stock_quantity <= 0) {
            $this->dispatch('toast', message: '품절된 상품입니다.', type: 'error');
            return;
        }

        if ($this->quantity > $this->product->stock_quantity) {
            $this->dispatch('toast', message: '재고가 부족합니다. (잔여: ' . $this->product->stock_quantity . '개)', type: 'error');
            return;
        }

        // 옵션 값 준비
        $optionValues = null;
        if (!empty($this->selectedOptions)) {
            $optionValues = [];
            foreach ($this->selectedOptions as $optionId => $valueId) {
                if ($valueId) {
                    $optionValues[$optionId] = $valueId;
                }
            }
            if (empty($optionValues)) {
                $optionValues = null;
            }
        }

        $cartService = app(CartService::class);
        $cartService->addItem($this->product, $this->quantity, $optionValues);

        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: '장바구니에 상품을 담았습니다.', type: 'success');
    }

    public function quickAdd(): void
    {
        // 옵션이 있는 상품은 상세페이지로 이동
        if ($this->product->options->count() > 0) {
            $this->redirect(route('shop.products.show', $this->product->slug), navigate: false);
            return;
        }

        if ($this->product->stock_quantity <= 0) {
            $this->dispatch('toast', message: '품절된 상품입니다.', type: 'error');
            return;
        }

        $cartService = app(CartService::class);
        $cartService->addItem($this->product, 1);

        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: '장바구니에 상품을 담았습니다.', type: 'success');
    }

    public function render()
    {
        return view('livewire.shop.add-to-cart');
    }
}
