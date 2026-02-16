@extends('layouts.shop')

@section('title', '페이지를 찾을 수 없습니다')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
    <div class="text-center">
        {{-- 404 아이콘 --}}
        <div class="mb-8">
            <span class="text-9xl font-extrabold text-indigo-100">404</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">페이지를 찾을 수 없습니다</h1>
        <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">
            요청하신 페이지가 존재하지 않거나 이동되었을 수 있습니다.
            주소를 다시 확인해 주세요.
        </p>

        {{-- 검색바 --}}
        <div class="max-w-md mx-auto mb-10">
            <form action="{{ route('shop.products.search') }}" method="GET" class="flex">
                <input type="text"
                       name="q"
                       placeholder="상품을 검색해 보세요..."
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
                <button type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 transition font-medium text-sm">
                    검색
                </button>
            </form>
        </div>

        {{-- 네비게이션 링크 --}}
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('shop.home') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                홈으로 가기
            </a>
            <a href="{{ route('shop.products.index') }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                전체상품 보기
            </a>
        </div>
    </div>

    {{-- 추천 상품 --}}
    @php
        $recommendedProducts = \App\Models\Product::active()
            ->featured()
            ->with(['primaryImage', 'category', 'approvedReviews'])
            ->inRandomOrder()
            ->take(4)
            ->get();
    @endphp

    @if($recommendedProducts->count() > 0)
        <div class="mt-20">
            <h2 class="text-xl font-bold text-gray-900 text-center mb-8">이런 상품은 어떠세요?</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($recommendedProducts as $product)
                    <x-shop.product-card :product="$product" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
