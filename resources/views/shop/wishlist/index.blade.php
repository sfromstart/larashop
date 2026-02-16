@extends('layouts.shop')

@section('title', '위시리스트')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-shop.breadcrumb :items="[['label' => '위시리스트']]" />

    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">위시리스트</h1>

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($wishlists as $wishlist)
                @if($wishlist->product)
                    <x-shop.product-card :product="$wishlist->product" />
                @endif
            @endforeach
        </div>

        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
            <svg class="mx-auto w-20 h-20 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">위시리스트가 비어있습니다</h2>
            <p class="text-gray-400 mb-8">마음에 드는 상품을 위시리스트에 추가해 보세요.</p>
            <a href="{{ route('shop.products.index') }}" class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                쇼핑하러 가기
            </a>
        </div>
    @endif
</div>
@endsection
