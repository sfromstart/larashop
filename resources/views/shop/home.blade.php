@extends('layouts.shop')

@section('title', '홈')

@section('content')

    {{-- 히어로 섹션 --}}
    <section class="relative min-h-[80vh] flex items-center justify-center bg-gray-900 overflow-hidden">
        {{-- 배경 이미지 --}}
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Hero Background" class="w-full h-full object-cover opacity-80">
            {{-- 가독성을 위한 진한 오버레이 --}}
            <div class="absolute inset-0 bg-black/40"></div>
        </div>
        
        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white tracking-tighter mb-8 drop-shadow-2xl">
                {{ \App\Models\Setting::get('hero_title', 'The New Collection') }}
            </h1>
            <p class="text-lg md:text-2xl text-white/95 mb-12 tracking-wide font-light max-w-3xl mx-auto drop-shadow-lg leading-relaxed">
                {{ \App\Models\Setting::get('hero_subtitle', 'Discover the latest trends in fashion and lifestyle. Elevate your everyday style.') }}
            </p>
            <a href="{{ route('shop.products.index') }}"
               class="inline-block px-12 py-5 bg-white text-black font-bold uppercase tracking-widest text-sm hover:bg-black hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                지금 쇼핑하기
            </a>
        </div>
    </section>

    {{-- 카테고리 (텍스트 중심) --}}
    @if($categories->count() > 0)
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            @foreach($categories->take(4) as $category)
                <a href="{{ route('shop.products.category', $category->slug) }}" class="group block text-center">
                    <div class="relative aspect-[3/4] mb-6 overflow-hidden bg-gray-100">
                         @if($category->image)
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <img src="https://picsum.photos/seed/{{ $category->id }}cat/300/400" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 uppercase tracking-widest group-hover:underline underline-offset-4 decoration-1">{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 추천상품 --}}
    @if($featuredProducts->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tighter uppercase mb-4">추천 상품</h2>
                <div class="w-12 h-0.5 bg-black mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
                @foreach($featuredProducts as $product)
                    <x-shop.product-card :product="$product" />
                @endforeach
            </div>
            
            <div class="text-center mt-16">
                 <a href="{{ route('shop.products.index') }}" class="inline-block border-b border-black pb-1 text-sm uppercase tracking-widest hover:text-gray-600 transition-colors">
                    전체 상품 보기
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- 미니멀 배너 --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="block text-sm font-bold text-gray-500 tracking-[0.2em] uppercase mb-4">한정 특가</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tighter mb-6">필수 워드로브 <br> 클래식</h2>
            <p class="text-gray-600 mb-10 leading-relaxed max-w-xl mx-auto">
                엄선된 타임리스 아이템을 만나보세요. 프리미엄 소재로 제작되어 오래도록 함께할 수 있습니다.
            </p>
            <a href="{{ route('shop.products.index') }}" class="inline-block px-8 py-3 border border-black text-black font-medium uppercase tracking-widest text-xs hover:bg-black hover:text-white transition-colors">
                컬렉션 둘러보기
            </a>
        </div>
    </section>

    {{-- 신상품 --}}
    @if($newProducts->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="flex items-end justify-between mb-10 border-b border-gray-100 pb-4">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tighter uppercase">신상품</h2>
                 <a href="{{ route('shop.products.index', ['sort' => 'latest']) }}" class="text-xs uppercase tracking-widest font-bold hover:underline">전체 보기</a>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
                @foreach($newProducts as $product)
                    <x-shop.product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 서비스 특징 (아이콘 없이 텍스트만) --}}
    <section class="border-t border-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="px-4 py-4">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2">무료 배송</h4>
                    <p class="text-sm text-gray-500">50,000원 이상 주문 시 무료배송</p>
                </div>
                <div class="px-4 py-4">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2">안전한 결제</h4>
                    <p class="text-sm text-gray-500">100% 안전한 결제 시스템</p>
                </div>
                <div class="px-4 py-4">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider mb-2">간편한 반품</h4>
                    <p class="text-sm text-gray-500">30일 이내 반품 가능</p>
                </div>
            </div>
        </div>
    </section>

@endsection
