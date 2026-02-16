<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 상품 테이블 인덱스
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'products_active_featured_index');
            $table->index(['is_active', 'is_new'], 'products_active_new_index');
            $table->index(['is_active', 'sold_count'], 'products_active_sold_index');
            $table->index(['is_active', 'created_at'], 'products_active_created_index');
            $table->index(['category_id', 'is_active'], 'products_category_active_index');
        });

        // 카테고리 테이블 인덱스
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'parent_id', 'sort_order'], 'categories_active_parent_sort_index');
        });

        // 리뷰 테이블 인덱스
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['product_id', 'is_approved', 'created_at'], 'reviews_product_approved_index');
        });

        // 주문 테이블 인덱스
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_index');
        });

        // 위시리스트 인덱스
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index(['user_id', 'product_id'], 'wishlists_user_product_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_featured_index');
            $table->dropIndex('products_active_new_index');
            $table->dropIndex('products_active_sold_index');
            $table->dropIndex('products_active_created_index');
            $table->dropIndex('products_category_active_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_parent_sort_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_product_approved_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_index');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('wishlists_user_product_index');
        });
    }
};
