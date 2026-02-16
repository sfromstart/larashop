<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\SeoService;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with(['primaryImage', 'category', 'approvedReviews'])
            ->latest()
            ->take(8)
            ->get();

        $newProducts = Product::active()
            ->new()
            ->with(['primaryImage', 'category', 'approvedReviews'])
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->with(['primaryImage', 'category', 'approvedReviews'])
            ->orderByDesc('sold_count')
            ->take(8)
            ->get();

        $categories = Category::active()
            ->root()
            ->orderBy('sort_order')
            ->withCount(['products' => fn ($q) => $q->active()])
            ->take(8)
            ->get();

        $seo = (new SeoService())->setHome()->toArray();

        return view('shop.home', compact(
            'featuredProducts',
            'newProducts',
            'bestSellers',
            'categories',
            'seo'
        ));
    }
}
