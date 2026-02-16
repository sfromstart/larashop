@extends('layouts.shop')

{{-- SEO는 SeoService에서 처리됨 --}}

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-16">
    {{-- 브레드크럼 --}}
    <x-shop.breadcrumb :items="[
        ['label' => 'Shop', 'url' => route('shop.products.index')],
        $product->category ? ['label' => $product->category->name, 'url' => route('shop.products.category', $product->category->slug)] : null,
        ['label' => $product->name],
    ]" />

    {{-- 상품 상세 정보 --}}
    <div class="mt-8 mb-20" x-data="productGallery()">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
            {{-- 이미지 갤러리 --}}
            <div class="space-y-4">
                {{-- 메인 이미지 --}}
                <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                    @if($product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                            <img x-show="activeImage === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 src="{{ $image->path }}"
                                 alt="{{ $image->alt_text ?: $product->name }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                        @endforeach
                    @else
                        <img src="https://picsum.photos/seed/{{ $product->id }}/600/800"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @endif

                    {{-- 뱃지 --}}
                    <div class="absolute top-4 left-4 flex flex-col space-y-2 pointer-events-none">
                        @if($product->is_new)
                             <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold tracking-widest uppercase bg-white text-black">신상품</span>
                        @endif
                        @if($product->is_on_sale)
                            <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold tracking-widest uppercase bg-red-600 text-white">-{{ $product->discount_percent }}%</span>
                        @endif
                    </div>

                    {{-- 이전/다음 화살표 --}}
                    @if($product->images->count() > 1)
                        <button @click="prevImage()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center hover:bg-black/10 transition rounded-full">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center hover:bg-black/10 transition rounded-full">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                </div>

                {{-- 썸네일 목록 --}}
                @if($product->images->count() > 1)
                    <div class="flex space-x-3 overflow-x-auto pb-2">
                        @foreach($product->images as $index => $image)
                            <button @click="activeImage = {{ $index }}"
                                    class="flex-shrink-0 w-20 h-24 overflow-hidden transitio border border-transparent"
                                    :class="activeImage === {{ $index }} ? 'border-black' : 'hover:border-gray-300'">
                                <img src="{{ $image->path }}" alt="{{ $image->alt_text ?: $product->name }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 상품 정보 --}}
            <div class="flex flex-col h-full pt-2">
                {{-- 카테고리 --}}
                @if($product->category)
                    <a href="{{ route('shop.products.category', $product->category->slug) }}" class="text-xs text-gray-500 hover:text-black uppercase tracking-widest mb-3 block transition">
                        {{ $product->category->name }}
                    </a>
                @endif

                {{-- 상품명 --}}
                <h1 class="text-3xl md:text-4xl font-normal text-gray-900 mb-4">{{ $product->name }}</h1>

                {{-- 가격 --}}
                <div class="flex items-baseline space-x-3 mb-6">
                    @if($product->is_on_sale)
                        <span class="text-lg text-gray-400 line-through">{{ number_format($product->compare_price) }}</span>
                    @endif
                    <p class="text-2xl font-medium {{ $product->is_on_sale ? 'text-red-600' : 'text-gray-900' }}">
                        {{ number_format($product->price) }}
                    </p>
                </div>

                {{-- 별점 --}}
                @if($reviewStats['total'] > 0)
                    <div class="flex items-center space-x-2 mb-8 cursor-pointer hover:opacity-70 transition" @click="$dispatch('scroll-to-reviews')">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($reviewStats['average']) ? 'text-black' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 underline underline-offset-4">{{ $reviewStats['total'] }}개의 리뷰</span>
                    </div>
                @endif

                {{-- 간략 설명 --}}
                @if($product->short_description)
                    <p class="text-gray-600 mb-8 leading-relaxed font-light">{{ $product->short_description }}</p>
                @endif

                {{-- 상품 옵션 + 수량 + 장바구니 (Livewire 컴포넌트) --}}
                <div class="border-t border-gray-100 pt-8 mb-8">
                     <livewire:shop.add-to-cart :product="$product" />
                </div>

                {{-- 추가 정보 (SKU 등) --}}
                <div class="space-y-2 text-xs text-gray-500 uppercase tracking-wider">
                    @if($product->sku)
                        <p><span class="text-gray-900 w-24 inline-block">SKU</span> {{ $product->sku }}</p>
                    @endif
                    @if($product->weight)
                        <p><span class="text-gray-900 w-24 inline-block">무게</span> {{ number_format($product->weight) }}g</p>
                    @endif
                     <div class="pt-4 flex items-center space-x-6">
                        <livewire:shop.wishlist-button :productId="$product->id" />
                        <button class="flex items-center text-gray-500 hover:text-black transition">
                             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                             공유
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 탭 영역: 상세설명 / 리뷰 --}}
    <div x-data="{ activeTab: 'description' }" @scroll-to-reviews.window="activeTab = 'reviews'; $el.scrollIntoView({behavior: 'smooth'})">
        {{-- 탭 헤더 --}}
        <div class="border-t border-gray-200 mb-12 flex justify-center">
            <nav class="flex space-x-12 -mt-px">
                <button @click="activeTab = 'description'"
                        :class="activeTab === 'description' ? 'border-black text-black' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="py-4 border-t-2 font-medium text-sm uppercase tracking-widest transition">
                    상세설명
                </button>
                <button @click="activeTab = 'reviews'"
                        :class="activeTab === 'reviews' ? 'border-black text-black' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="py-4 border-t-2 font-medium text-sm uppercase tracking-widest transition">
                    리뷰 ({{ $reviewStats['total'] }})
                </button>
            </nav>
        </div>

        {{-- 상세설명 탭 --}}
        <div x-show="activeTab === 'description'" class="max-w-4xl mx-auto">
            @if($product->description)
                <div class="prose prose-gray max-w-none prose-img:rounded-none prose-headings:font-normal prose-a:text-black hover:prose-a:text-gray-600">
                    {!! nl2br(e($product->description)) !!}
                </div>
            @else
                <p class="text-gray-500 text-center py-12">상세설명이 없습니다.</p>
            @endif
        </div>

        {{-- 리뷰 탭 --}}
        <div x-show="activeTab === 'reviews'" class="max-w-4xl mx-auto" x-cloak>
            @if($reviewStats['total'] > 0)
                <div class="space-y-12">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
                         <div class="text-center md:text-left">
                             <h3 class="text-2xl font-normal text-gray-900 mb-2">고객 리뷰</h3>
                             <div class="flex items-center justify-center md:justify-start space-x-4 mb-4">
                                <span class="text-5xl font-light">{{ $reviewStats['average'] }}</span>
                                <div class="flex flex-col">
                                     <div class="flex text-black">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($reviewStats['average']) ? 'text-black' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $reviewStats['total'] }}개의 리뷰 기반</span>
                                </div>
                             </div>
                         </div>
                         
                         {{-- 리뷰 작성 폼 --}}
                        <livewire:shop.review-form :product-id="$product->id" />
                     </div>

                    @foreach($product->approvedReviews as $review)
                        <div class="border-b border-gray-100 pb-8 last:border-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-bold text-gray-900">{{ $review->user->name }}</span>
                                    <div class="flex text-black scale-75 origin-left">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-black' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($review->title)
                                <h4 class="font-medium text-gray-900 mb-2">{{ $review->title }}</h4>
                            @endif
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $review->content }}</p>
                            @if($review->admin_reply)
                                <div class="mt-4 pl-4 border-l-2 border-gray-200">
                                    <p class="text-xs font-bold text-gray-900 mb-1">관리자 답변</p>
                                    <p class="text-sm text-gray-500">{{ $review->admin_reply }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <h3 class="font-medium text-gray-900 mb-2">아직 리뷰가 없습니다</h3>
                    <p class="text-sm text-gray-500 mb-8">첫 번째 리뷰를 작성해 보세요.</p>
                     <livewire:shop.review-form :product-id="$product->id" />
                </div>
            @endif
        </div>
    </div>

    {{-- 관련 상품 --}}
    @if($relatedProducts->count() > 0)
    <section class="mb-16 pt-16 border-t border-gray-100">
        <h2 class="text-2xl font-normal text-center mb-12 uppercase tracking-wide">이런 상품은 어떠세요</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relProduct)
                <x-shop.product-card :product="$relProduct" />
            @endforeach
        </div>
    </section>
    @endif
</div>

@push('scripts')
<script>
    function productGallery() {
        return {
            activeImage: 0,
            totalImages: {{ $product->images->count() ?: 1 }},
            prevImage() {
                this.activeImage = (this.activeImage > 0) ? this.activeImage - 1 : this.totalImages - 1;
            },
            nextImage() {
                this.activeImage = (this.activeImage < this.totalImages - 1) ? this.activeImage + 1 : 0;
            }
        }
    }
</script>
@endpush
@endsection
