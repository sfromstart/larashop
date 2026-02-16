<?php

namespace App\Livewire\Shop;

use App\Models\Wishlist;
use Livewire\Component;

class WishlistButton extends Component
{
    public int $productId;
    public bool $isWishlisted = false;

    public function mount(int $productId): void
    {
        $this->productId = $productId;

        if (auth()->check()) {
            $this->isWishlisted = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->productId)
                ->exists();
        }
    }

    public function toggle(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: false);
            return;
        }

        if ($this->isWishlisted) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->productId)
                ->delete();

            $this->isWishlisted = false;
            $this->dispatch('toast', message: '위시리스트에서 제거되었습니다.', type: 'info');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $this->productId,
            ]);

            $this->isWishlisted = true;
            $this->dispatch('toast', message: '위시리스트에 추가되었습니다.', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.shop.wishlist-button');
    }
}
