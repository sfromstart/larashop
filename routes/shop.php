<?php

use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\WishlistController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('/shop/category/{category:slug}', [ProductController::class, 'category'])->name('products.category');
    Route::get('/shop/product/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/shop/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    Route::middleware('auth')->group(function () {
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/order-complete/{order}', [OrderController::class, 'complete'])->name('order.complete');
        Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
        Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
        Route::get('/account/orders/{order}', [AccountController::class, 'orderDetail'])->name('account.order-detail');
        Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.update-profile');
    });
});

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// 정적 페이지
Route::get('/terms', fn () => view('pages.terms'))->name('page.terms');
Route::get('/privacy', fn () => view('pages.privacy'))->name('page.privacy');
Route::get('/shipping-info', fn () => view('pages.shipping'))->name('page.shipping');
