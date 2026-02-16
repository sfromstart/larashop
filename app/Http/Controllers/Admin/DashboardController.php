<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Order::whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        $monthlySales = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        $totalProducts = Product::count();
        $totalUsers = User::where('role', '!=', 'admin')->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $bestSellers = Product::select('products.*')
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $recentReviews = Review::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todaySales',
            'monthlySales',
            'totalProducts',
            'totalUsers',
            'recentOrders',
            'bestSellers',
            'recentReviews'
        ));
    }
}
