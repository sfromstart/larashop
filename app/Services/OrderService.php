<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create an order from a cart.
     */
    public function createFromCart(
        Cart $cart,
        array $shippingData,
        string $paymentMethod = 'bank_transfer',
        int $shippingFee = 0,
        int $discountAmount = 0,
    ): Order {
        return DB::transaction(function () use ($cart, $shippingData, $paymentMethod, $shippingFee, $discountAmount) {
            $cart->load('items.product.primaryImage');

            $subtotal = $cart->items->sum(fn ($item) => $item->unit_price * $item->quantity);
            $total = $subtotal + $shippingFee - $discountAmount;

            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Pending->value,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total' => max(0, $total),
                'shipping_name' => $shippingData['name'],
                'shipping_phone' => $shippingData['phone'],
                'shipping_postal_code' => $shippingData['postal_code'],
                'shipping_address' => $shippingData['address'],
                'shipping_address_detail' => $shippingData['address_detail'] ?? null,
                'shipping_memo' => $shippingData['memo'] ?? null,
                'payment_method' => $paymentMethod,
            ]);

            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->primaryImage?->path,
                    'option_values' => $cartItem->option_values,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->unit_price * $cartItem->quantity,
                ]);

                // Decrease stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                $cartItem->product->increment('sold_count', $cartItem->quantity);
            }

            // Clear the cart
            $cart->items()->delete();

            return $order->load('items');
        });
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $data = ['status' => $status->value];

        switch ($status) {
            case OrderStatus::Paid:
                $data['paid_at'] = now();
                break;
            case OrderStatus::Shipped:
                $data['shipped_at'] = now();
                break;
            case OrderStatus::Delivered:
                $data['delivered_at'] = now();
                break;
            case OrderStatus::Cancelled:
                $data['cancelled_at'] = now();
                break;
        }

        $order->update($data);

        return $order->fresh();
    }

    /**
     * Cancel an order and restore stock.
     */
    public function cancel(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->load('items');

            // Restore stock quantities
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $item->product?->increment('stock_quantity', $item->quantity);
                    $item->product?->decrement('sold_count', $item->quantity);
                }
            }

            return $this->updateStatus($order, OrderStatus::Cancelled);
        });
    }
}
