<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlists = $request->user()
            ->wishlists()
            ->with(['product.primaryImage', 'product.category', 'product.approvedReviews'])
            ->latest('created_at')
            ->paginate(12);

        return view('shop.wishlist.index', compact('wishlists'));
    }
}
