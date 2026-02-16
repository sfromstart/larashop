@extends('layouts.shop')

@section('title', $pageTitle)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
    {{-- 페이지 제목 --}}
    <div class="text-center mb-16">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tighter uppercase mb-4">{{ $pageTitle }}</h1>
        <div class="w-12 h-0.5 bg-black mx-auto"></div>
        
        {{-- 브레드크럼 (선택사항, 깔끔하게) --}}
        @if(isset($category))
            <div class="mt-4 text-sm text-gray-500">
                <a href="{{ route('shop.products.index') }}" class="hover:text-black transition">Shop</a>
                <span class="mx-2">/</span>
                <span class="text-black">{{ $category->name }}</span>
            </div>
        @endif
    </div>

    {{-- Livewire 필터 컴포넌트 --}}
    <livewire:shop.product-filter
        :categorySlug="$categorySlug ?? null"
        :searchQuery="$searchQuery ?? null" />
</div>
@endsection
