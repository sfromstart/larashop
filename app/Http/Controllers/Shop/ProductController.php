<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\SeoService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $seo = (new SeoService())->setProductList('전체상품')->toArray();

        return view('shop.products.index', [
            'pageTitle' => '전체상품',
            'categorySlug' => null,
            'seo' => $seo,
        ]);
    }

    public function category(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $seo = (new SeoService())->setCategory($category)->toArray();

        return view('shop.products.index', [
            'pageTitle' => $category->name,
            'category' => $category,
            'categorySlug' => $category->slug,
            'seo' => $seo,
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->increment('view_count');

        $product->load([
            'category',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'options.values',
            'approvedReviews' => fn ($q) => $q->with('user')->latest()->take(10),
        ]);

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['primaryImage', 'category', 'approvedReviews'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        $reviewStats = [
            'average' => round($product->approvedReviews()->avg('rating') ?? 0, 1),
            'total' => $product->approvedReviews()->count(),
            'distribution' => [],
        ];

        for ($i = 5; $i >= 1; $i--) {
            $reviewStats['distribution'][$i] = $product->approvedReviews()->where('rating', $i)->count();
        }

        $seo = (new SeoService())->setProduct($product)->toArray();

        return view('shop.products.show', compact('product', 'relatedProducts', 'reviewStats', 'seo'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $seo = (new SeoService())
            ->setProductList("'{$query}' 검색결과")
            ->setRobots('noindex, follow')
            ->toArray();

        return view('shop.products.index', [
            'pageTitle' => "'{$query}' 검색결과",
            'categorySlug' => null,
            'searchQuery' => $query,
            'seo' => $seo,
        ]);
    }
}
