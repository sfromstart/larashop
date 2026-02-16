<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 mb-12 uppercase tracking-wide text-center">장바구니</h1>

        @if($cartItems->count() > 0)
            <div class="flex flex-col lg:flex-row gap-16">
                {{-- 장바구니 목록 --}}
                <div class="flex-1">
                    {{-- 데스크탑 헤더 (미니멀) --}}
                    <div class="hidden md:flex border-b border-black pb-4 mb-6">
                        <div class="flex-1 text-xs font-bold uppercase tracking-widest text-gray-900">상품</div>
                        <div class="w-32 text-center text-xs font-bold uppercase tracking-widest text-gray-900">수량</div>
                        <div class="w-32 text-right text-xs font-bold uppercase tracking-widest text-gray-900">합계</div>
                        <div class="w-10"></div>
                    </div>

                    <div class="space-y-8">
                        @foreach($cartItems as $item)
                            <div class="flex flex-col md:flex-row items-center gap-6 pb-8 border-b border-gray-100 last:border-0" wire:key="cart-item-{{ $item->id }}">
                                {{-- 상품 정보 --}}
                                <div class="flex-1 flex items-center w-full">
                                    <div class="w-24 h-32 bg-gray-100 mr-6 flex-shrink-0">
                                        @if($item->product && $item->product->primaryImageUrl)
                                            <a href="{{ route('shop.products.show', $item->product->slug) }}">
                                                <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product->name ?? '' }}" class="w-full h-full object-cover">
                                            </a>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->product)
                                            <a href="{{ route('shop.products.show', $item->product->slug) }}" class="text-base font-medium text-gray-900 hover:text-gray-600 transition block mb-1">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            <span class="text-base text-gray-400">판매 불가 상품</span>
                                        @endif
                                        
                                        @if($item->option_values)
                                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                                                @foreach($item->option_values as $optId => $valId)
                                                    @if(isset($optionValueMap[$valId]))
                                                        {{ $optionValueMap[$valId]->option->name }}: {{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                        <p class="text-sm text-gray-500">{{ number_format($item->unit_price) }}</p>
                                    </div>
                                </div>

                                {{-- 수량 조절 --}}
                                <div class="w-full md:w-32 flex justify-center">
                                    <div class="flex items-center border border-gray-200">
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-black hover:bg-gray-50 transition"
                                                @if($item->quantity <= 1) disabled @endif>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="w-8 h-8 flex items-center justify-center text-sm font-medium text-gray-900">
                                            {{ $item->quantity }}
                                        </span>
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-black hover:bg-gray-50 transition"
                                                @if($item->product && $item->quantity >= $item->product->stock_quantity) disabled @endif>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- 합계 --}}
                                <div class="w-full md:w-32 text-center md:text-right flex justify-between md:block items-center">
                                    <span class="md:hidden text-xs uppercase text-gray-500">합계</span>
                                    <span class="text-base font-medium text-gray-900">{{ number_format($item->subtotal) }}</span>
                                </div>

                                {{-- 삭제 버튼 --}}
                                <div class="w-full md:w-10 text-center md:text-right">
                                    <button wire:click="removeItem({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                            class="text-gray-400 hover:text-black transition"
                                            title="삭제">
                                        <svg class="w-5 h-5 mx-auto md:ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 쇼핑 계속하기 링크 --}}
                    <div class="mt-8">
                        <a href="{{ route('shop.products.index') }}" class="inline-block text-sm text-gray-500 hover:text-black border-b border-transparent hover:border-black pb-0.5 transition uppercase tracking-wider">
                            &larr; 쇼핑 계속하기
                        </a>
                    </div>
                </div>

                {{-- 주문 요약 --}}
                <div class="lg:w-96">
                    <div class="bg-gray-50 p-8 rounded-none">
                        <h3 class="font-bold text-gray-900 uppercase tracking-widest mb-6 pb-2 border-b border-gray-200">주문 요약</h3>

                        {{-- 쿠폰 입력 --}}
                        <div class="mb-8">
                            @if($couponValid)
                                <div class="flex items-center justify-between p-3 bg-white border border-green-200">
                                    <div>
                                        <p class="text-sm font-bold text-green-800">{{ $couponCode }}</p>
                                        <p class="text-xs text-green-600 mt-0.5">-{{ number_format($discountAmount) }} 할인</p>
                                    </div>
                                    <button wire:click="removeCoupon" class="text-gray-400 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @else
                                <div class="flex">
                                    <input type="text" wire:model="couponCode"
                                           placeholder="쿠폰 코드"
                                           class="flex-1 bg-white border-gray-200 text-sm focus:border-black focus:ring-0 placeholder-gray-400">
                                    <button wire:click="applyCoupon"
                                            wire:loading.attr="disabled"
                                            class="px-4 py-2 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                                        적용
                                    </button>
                                </div>
                                @if($couponMessage && !$couponValid)
                                    <p class="mt-2 text-xs text-red-600">{{ $couponMessage }}</p>
                                @endif
                            @endif
                        </div>

                        {{-- 금액 --}}
                        <div class="space-y-4 text-sm mb-8">
                            <div class="flex justify-between">
                                <span class="text-gray-600">소계</span>
                                <span class="font-medium text-gray-900">{{ number_format($this->subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">배송비</span>
                                <span class="font-medium text-gray-900">{{ $this->shippingFee > 0 ? number_format($this->shippingFee) : '무료' }}</span>
                            </div>
                            @if($discountAmount > 0)
                                <div class="flex justify-between text-red-600">
                                    <span>할인</span>
                                    <span class="font-medium">-{{ number_format($discountAmount) }}</span>
                                </div>
                            @endif
                            <div class="pt-4 mt-4 border-t border-gray-200 flex justify-between items-end">
                                <span class="font-bold text-gray-900 uppercase tracking-widest">합계</span>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($this->total) }}</span>
                            </div>
                        </div>

                        @php
                            $freeShippingMin = (int) \App\Models\Setting::get('shop.free_shipping_min', 50000);
                        @endphp
                        @if($this->shippingFee > 0 && $freeShippingMin > 0)
                            <p class="mb-6 text-xs text-gray-500 text-center">
                                {{ number_format($freeShippingMin - $this->subtotal) }}원 더 구매하시면 무료배송
                            </p>
                        @endif

                        <a href="{{ route('shop.checkout.index') }}" class="block w-full py-4 bg-black text-white text-center text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                            주문하기
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- 빈 장바구니 --}}
            <div class="text-center py-32">
                <h2 class="text-2xl font-light text-gray-900 mb-4 uppercase tracking-widest">장바구니가 비어있습니다</h2>
                <p class="text-gray-500 mb-8 font-light">아직 장바구니에 담은 상품이 없습니다.</p>
                <a href="{{ route('shop.products.index') }}" class="inline-block border-b border-black pb-1 text-sm font-bold uppercase tracking-widest hover:text-gray-600 hover:border-gray-600 transition">
                    쇼핑 시작하기
                </a>
            </div>
        @endif
    </div>
</div>
