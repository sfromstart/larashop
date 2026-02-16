@props(['product'])

<div class="group">
    {{-- 이미지 영역 --}}
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100 mb-4">
        <a href="{{ route('shop.products.show', $product->slug) }}">
            @if($product->primaryImageUrl)
                <img src="{{ $product->primaryImageUrl }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
            @else
                <img src="https://picsum.photos/seed/{{ $product->id }}/400/500"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
            @endif
        </a>

        {{-- 뱃지 --}}
        <div class="absolute top-3 left-3 flex flex-col space-y-1.5 pointer-events-none">
            @if($product->is_new)
                <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold tracking-widest uppercase bg-white text-black">신상품</span>
            @endif
            @if($product->is_on_sale)
                <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold tracking-widest uppercase bg-red-600 text-white">-{{ $product->discount_percent }}%</span>
            @endif
        </div>

        {{-- 품절 뱃지 --}}
        @if($product->stock_quantity <= 0)
            <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                <span class="px-4 py-2 border border-black text-black text-xs font-bold uppercase tracking-widest">품절</span>
            </div>
        @endif

        {{-- 호버 액션 (하단에서 올라오기) --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
             <livewire:shop.add-to-cart :product="$product" :quick="true" :wire:key="'add-to-cart-'.$product->id" />
        </div>

        {{-- 위시리스트 버튼 --}}
        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
             <livewire:shop.wishlist-button :productId="$product->id" :key="'wishlist-card-'.$product->id" />
        </div>
    </div>

    {{-- 상품 정보 --}}
    <div class="text-center">
        {{-- 카테고리 --}}
        @if($product->category)
            <a href="{{ route('shop.products.category', $product->category->slug) }}"
               class="text-xs text-gray-400 hover:text-gray-900 uppercase tracking-wider mb-1 block transition">
                {{ $product->category->name }}
            </a>
        @endif

        {{-- 상품명 --}}
        <h3 class="text-base font-normal text-gray-900 mb-1">
            <a href="{{ route('shop.products.show', $product->slug) }}" class="hover:underline decoration-1 underline-offset-4">
                {{ $product->name }}
            </a>
        </h3>

        {{-- 가격 --}}
        <div class="flex items-center justify-center space-x-2 text-sm font-medium">
            @if($product->is_on_sale)
                <span class="text-gray-400 line-through">{{ number_format($product->compare_price) }}</span>
                <span class="text-red-600">{{ number_format($product->price) }}</span>
            @else
                <span class="text-gray-900">{{ number_format($product->price) }}</span>
            @endif
        </div>
    </div>
</div>
