<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function complete(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load('items.product.primaryImage');
        $optionValueMap = ProductOptionValue::resolveForItems($order->items);

        return view('shop.order.complete', compact('order', 'optionValueMap'));
    }
}
