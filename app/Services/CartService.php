<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create the current user's cart.
     * On login, merge session cart into user cart.
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $userCart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );

            // 세션 카트가 있으면 머지
            $this->mergeSessionCart($userCart);

            return $userCart->load('items.product.primaryImage');
        }

        $sessionId = Session::getId();
        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );

        return $cart->load('items.product.primaryImage');
    }

    /**
     * Merge session cart items into user cart on login.
     */
    protected function mergeSessionCart(Cart $userCart): void
    {
        $sessionId = Session::getId();

        $sessionCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (!$sessionCart || $sessionCart->items->isEmpty()) {
            return;
        }

        foreach ($sessionCart->items as $sessionItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $sessionItem->product_id)
                ->where('option_values', $sessionItem->getRawOriginal('option_values'))
                ->first();

            if ($existingItem) {
                // 같은 상품+옵션이면 수량 합산
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $sessionItem->quantity,
                ]);
            } else {
                // 새 상품이면 이동
                $sessionItem->update([
                    'cart_id' => $userCart->id,
                ]);
            }
        }

        // 세션 카트 정리 (남은 아이템 삭제 및 카트 삭제)
        $sessionCart->items()->delete();
        $sessionCart->delete();
    }

    /**
     * Add a product to the cart.
     */
    public function addItem(Product $product, int $quantity = 1, ?array $optionValues = null): CartItem
    {
        $cart = $this->getCart();

        // Check if the same product with the same options already exists
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('option_values', $optionValues ? json_encode($optionValues) : null)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
            ]);

            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'option_values' => $optionValues,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $this->removeItem($cartItem);
            return $cartItem;
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh();
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(CartItem $cartItem): bool
    {
        return $cartItem->delete();
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    /**
     * Get the cart total price.
     */
    public function getTotal(): int
    {
        $cart = $this->getCart();

        return $cart->items->sum(fn (CartItem $item) => $item->subtotal);
    }

    /**
     * Get the total number of items in the cart.
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();

        return $cart->items->sum('quantity');
    }
}
