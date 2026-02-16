<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 프로덕션이 아닌 환경에서 Lazy Loading 방지
        Model::preventLazyLoading(!app()->isProduction());

        // 카테고리 메뉴 캐싱
        View::composer('layouts.shop', function ($view) {
            $rootCategories = Cache::remember('shop.root_categories', 3600, function () {
                return Category::active()
                    ->root()
                    ->orderBy('sort_order')
                    ->with('children')
                    ->get();
            });

            $view->with('rootCategories', $rootCategories);
        });
    }
}
