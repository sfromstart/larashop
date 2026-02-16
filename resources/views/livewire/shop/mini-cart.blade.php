<div class="relative" x-data="{ open: @entangle('isOpen') }">
    {{-- 카트 아이콘 + 뱃지 --}}
    <button @click="open = !open" class="relative text-gray-600 hover:text-indigo-600 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        @if($itemCount > 0)
            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                {{ $itemCount > 99 ? '99+' : $itemCount }}
            </span>
        @endif
    </button>

    {{-- 오버레이 --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/30 z-40"
         x-cloak>
    </div>

    {{-- 사이드 패널 --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-50 flex flex-col"
         x-cloak>

        {{-- 헤더 --}}
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">장바구니 <span class="text-indigo-600">({{ $itemCount }})</span></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- 아이템 리스트 --}}
        <div class="flex-1 overflow-y-auto p-5">
            @if($cart->items->count() > 0)
                <div class="space-y-4">
                    @foreach($cart->items as $item)
                        <div class="flex space-x-3" wire:key="mini-cart-item-{{ $item->id }}">
                            {{-- 이미지 --}}
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->product && $item->product->primaryImageUrl)
                                    <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product->name ?? '' }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- 상품 정보 --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-800 truncate">
                                    @if($item->product)
                                        <a href="{{ route('shop.products.show', $item->product->slug) }}" @click="open = false" class="hover:text-indigo-600 transition">
                                            {{ $item->product->name }}
                                        </a>
                                    @else
                                        삭제된 상품
                                    @endif
                                </h4>
                                @if($item->option_values)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        @foreach($item->option_values as $optId => $valId)
                                            @if(isset($optionValueMap[$valId]))
                                                {{ $optionValueMap[$valId]->option->name }}: {{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}
                                            @endif
                                        @endforeach
                                    </p>
                                @endif
                                <div class="flex items-center justify-between mt-1.5">
                                    <p class="text-sm">
                                        <span class="font-medium text-gray-700">{{ number_format($item->unit_price) }}원</span>
                                        <span class="text-xs text-gray-400 mx-1">x</span>
                                        <span class="text-gray-500">{{ $item->quantity }}</span>
                                    </p>
                                    <button wire:click="removeItem({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="removeItem({{ $item->id }})"
                                            class="text-gray-400 hover:text-red-500 transition p-1"
                                            title="삭제">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">장바구니가 비어있습니다</p>
                </div>
            @endif
        </div>

        {{-- 하단: 합계 및 버튼 --}}
        @if($cart->items->count() > 0)
            <div class="border-t border-gray-100 p-5 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-600">합계</span>
                    <span class="text-lg font-bold text-gray-900">{{ number_format($total) }}원</span>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('shop.cart.index') }}"
                       @click="open = false"
                       class="block w-full py-3 text-center text-sm font-semibold text-indigo-600 border border-indigo-600 rounded-xl hover:bg-indigo-50 transition">
                        장바구니 보기
                    </a>
                    <a href="{{ route('shop.checkout.index') }}"
                       @click="open = false"
                       class="block w-full py-3 text-center text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                        결제하기
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
