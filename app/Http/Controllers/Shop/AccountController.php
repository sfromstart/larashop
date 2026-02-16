<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $recentOrders = $user->orders()
            ->with('items.product.primaryImage')
            ->latest()
            ->take(5)
            ->get();

        $orderStats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'shipped' => $user->orders()->where('status', 'shipped')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
        ];

        $wishlistCount = $user->wishlists()->count();
        $reviewCount = $user->reviews()->count();

        return view('shop.account.dashboard', compact(
            'recentOrders',
            'orderStats',
            'wishlistCount',
            'reviewCount'
        ));
    }

    public function orders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product.primaryImage')
            ->latest()
            ->paginate(10);

        return view('shop.account.orders', compact('orders'));
    }

    public function orderDetail(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load('items.product.primaryImage');
        $optionValueMap = ProductOptionValue::resolveForItems($order->items);

        return view('shop.account.order-detail', compact('order', 'optionValueMap'));
    }

    public function profile(Request $request)
    {
        return view('shop.account.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('shop.account.profile')->with('success', '프로필이 수정되었습니다.');
    }
}
