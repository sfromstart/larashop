<div>
    <div class="flex flex-col lg:flex-row gap-12 pb-24">
        {{-- 사이드바 필터 --}}
        <aside class="w-full lg:w-64 flex-shrink-0" x-data="{ showMobileFilter: false }">
            {{-- 모바일 필터 토글 --}}
            <button @click="showMobileFilter = !showMobileFilter"
                    class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-gray-50 mb-8 uppercase tracking-widest text-xs font-bold">
                <span>상품 필터</span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showMobileFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div :class="{ 'hidden': !showMobileFilter }" class="lg:block space-y-10">
                {{-- 카테고리 필터 --}}
                <div>
                    <h3 class="font-bold text-gray-900 uppercase tracking-widest text-xs mb-6 pb-2 border-b border-gray-200">카테고리</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('shop.products.index') }}"
                               class="text-sm {{ !$categorySlug ? 'text-black font-bold underline underline-offset-4' : 'text-gray-500 hover:text-black' }} transition block">
                                전체 상품
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('shop.products.category', $cat->slug) }}"
                                   class="flex items-center justify-between text-sm {{ $categorySlug === $cat->slug ? 'text-black font-bold underline underline-offset-4' : 'text-gray-500 hover:text-black' }} transition">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-xs text-gray-400">({{ $cat->products_count }})</span>
                                </a>
                                @if($cat->children->count() > 0)
                                    <ul class="pl-4 mt-2 space-y-2 border-l border-gray-100">
                                        @foreach($cat->children as $child)
                                            <li>
                                                <a href="{{ route('shop.products.category', $child->slug) }}"
                                                   class="flex items-center justify-between text-sm {{ $categorySlug === $child->slug ? 'text-black font-medium' : 'text-gray-400 hover:text-black' }} transition">
                                                    <span>{{ $child->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- 가격 필터 --}}
                <div>
                    <h3 class="font-bold text-gray-900 uppercase tracking-widest text-xs mb-6 pb-2 border-b border-gray-200">가격</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" wire:model.live.debounce.500ms="minPrice"
                                       class="w-full px-3 py-2 bg-gray-50 border-0 text-sm focus:ring-1 focus:ring-black placeholder-gray-400"
                                       placeholder="최소" min="0">
                            </div>
                            <div>
                                <input type="number" wire:model.live.debounce.500ms="maxPrice"
                                       class="w-full px-3 py-2 bg-gray-50 border-0 text-sm focus:ring-1 focus:ring-black placeholder-gray-400"
                                       placeholder="최대" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 필터 초기화 --}}
                <button wire:click="resetFilters"
                        class="text-xs text-gray-400 uppercase tracking-widest hover:text-black transition border-b border-transparent hover:border-black pb-0.5">
                    필터 초기화
                </button>
            </div>
        </aside>

        {{-- 상품 목록 --}}
        <div class="flex-1 min-w-0">
            {{-- 정렬/뷰 모드 바 --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-10 pb-4 border-b border-gray-100">
                <p class="text-sm text-gray-500">
                    총 {{ $products->total() }}개 중 <span class="text-black font-medium">{{ $products->count() }}</span>개 표시
                </p>

                <div class="flex items-center space-x-6">
                    {{-- 정렬 --}}
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 uppercase tracking-wider mr-2">정렬:</span>
                        <select wire:model.live="sort"
                                class="border-none text-sm text-gray-900 font-medium focus:ring-0 py-0 pl-0 pr-8 bg-transparent cursor-pointer">
                            <option value="latest">최신순</option>
                            <option value="price_asc">낮은 가격순</option>
                            <option value="price_desc">높은 가격순</option>
                            <option value="popular">인기순</option>
                        </select>
                    </div>

                    {{-- 뷰 모드 --}}
                    <div class="hidden sm:flex items-center space-x-2">
                        <button wire:click="$set('viewMode', 'grid')"
                                class="{{ $viewMode === 'grid' ? 'text-black' : 'text-gray-300 hover:text-gray-500' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </button>
                        <button wire:click="$set('viewMode', 'list')"
                                class="{{ $viewMode === 'list' ? 'text-black' : 'text-gray-300 hover:text-gray-500' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 로딩 인디케이터 --}}
            <div wire:loading class="w-full text-center py-20">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            </div>

            {{-- 상품 그리드 --}}
            <div wire:loading.remove>
                @if($products->count() > 0)
                    @if($viewMode === 'grid')
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-10">
                            @foreach($products as $product)
                                <x-shop.product-card :product="$product" />
                            @endforeach
                        </div>
                    @else
                        {{-- 리스트 뷰 --}}
                        <div class="space-y-8">
                            @foreach($products as $product)
                                <div class="group flex flex-col sm:flex-row gap-6">
                                    {{-- 이미지 --}}
                                    <div class="sm:w-64 flex-shrink-0">
                                        <a href="{{ route('shop.products.show', $product->slug) }}" class="block aspect-[3/4] overflow-hidden bg-gray-100 relative">
                                            @if($product->primaryImageUrl)
                                                <img src="{{ $product->primaryImageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
                                            @else
                                                <img src="https://picsum.photos/seed/{{ $product->id }}/400/500" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
                                            @endif
                                            
                                            @if($product->stock_quantity <= 0)
                                                <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                                    <span class="px-4 py-2 border border-black text-black text-xs font-bold uppercase tracking-widest">품절</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    
                                    {{-- 정보 --}}
                                    <div class="flex-1 py-2">
                                        @if($product->category)
                                            <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-xs text-gray-400 hover:text-black uppercase tracking-wider mb-2 block transition">{{ $product->category->name }}</a>
                                        @endif
                                        
                                        <h3 class="text-xl font-normal text-gray-900 mb-3">
                                            <a href="{{ route('shop.products.show', $product->slug) }}" class="hover:underline decoration-1 underline-offset-4">{{ $product->name }}</a>
                                        </h3>
                                        
                                        <div class="text-lg font-medium mb-4">
                                            @if($product->is_on_sale)
                                                <span class="text-gray-400 line-through text-base mr-2">{{ number_format($product->compare_price) }}</span>
                                                <span class="text-red-600">{{ number_format($product->price) }}</span>
                                            @else
                                                <span class="text-gray-900">{{ number_format($product->price) }}</span>
                                            @endif
                                        </div>
                                        
                                        @if($product->short_description)
                                            <p class="text-gray-600 mb-6 font-light leading-relaxed max-w-xl">{{ $product->short_description }}</p>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4">
                                             <livewire:shop.add-to-cart :product="$product" :quick="true" :wire:key="'add-to-cart-list-'.$product->id" />
                                             
                                             <livewire:shop.wishlist-button :productId="$product->id" :key="'wishlist-list-'.$product->id" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 페이지네이션 --}}
                    <div class="mt-16">
                        {{ $products->links() }}
                    </div>
                @else
                    {{-- 결과 없음 --}}
                    <div class="text-center py-32 bg-gray-50">
                        <p class="text-gray-500 mb-6">선택하신 조건에 맞는 상품이 없습니다.</p>
                        <button wire:click="resetFilters" class="inline-block border-b border-black pb-1 text-sm uppercase tracking-widest hover:text-gray-600 transition-colors">
                            필터 초기화
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
