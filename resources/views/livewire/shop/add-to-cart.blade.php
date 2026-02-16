<div>
    @if($quick)
        {{-- Quick Add: 작은 카트 아이콘 버튼 --}}
        <button wire:click="quickAdd"
                wire:loading.attr="disabled"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white shadow-md text-gray-600 hover:bg-indigo-600 hover:text-white transition-all duration-200 disabled:opacity-50"
                title="장바구니 담기"
                @if($product->stock_quantity <= 0) disabled @endif>
            <svg wire:loading.remove wire:target="quickAdd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <svg wire:loading wire:target="quickAdd" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </button>
    @else
        {{-- Full Mode: 옵션 + 수량 + 장바구니 버튼 --}}

        {{-- 옵션 선택 --}}
        @if($product->options->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($product->options as $option)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $option->name }}</label>
                        <select wire:model.live="selectedOptions.{{ $option->id }}"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">선택하세요</option>
                            @foreach($option->values as $value)
                                <option value="{{ $value->id }}"
                                        @if($value->stock_quantity !== null && $value->stock_quantity <= 0) disabled @endif>
                                    {{ $value->value }}
                                    @if($value->price_modifier > 0)
                                        (+{{ number_format($value->price_modifier) }}원)
                                    @elseif($value->price_modifier < 0)
                                        ({{ number_format($value->price_modifier) }}원)
                                    @endif
                                    @if($value->stock_quantity !== null && $value->stock_quantity <= 0)
                                        - 품절
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 수량 선택 --}}
        <div class="flex items-center space-x-4 mb-6">
            <span class="text-sm font-medium text-gray-700">수량</span>
            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                <button wire:click="decrementQuantity"
                        class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed"
                        @if($quantity <= 1) disabled @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>
                <span class="w-14 h-10 flex items-center justify-center text-sm font-medium border-x border-gray-300">
                    {{ $quantity }}
                </span>
                <button wire:click="incrementQuantity"
                        class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed"
                        @if($quantity >= $product->stock_quantity) disabled @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
            @if($product->stock_quantity > 0 && $product->stock_quantity <= ($product->low_stock_threshold ?? 5))
                <span class="text-xs text-orange-500">잔여 {{ $product->stock_quantity }}개</span>
            @endif
        </div>

        {{-- 장바구니 담기 버튼 --}}
        <button wire:click="addToCart"
                wire:loading.attr="disabled"
                class="w-full py-3.5 px-6 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                @if($product->stock_quantity <= 0) disabled @endif>
            <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <svg wire:loading wire:target="addToCart" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>{{ $product->stock_quantity > 0 ? '장바구니 담기' : '품절' }}</span>
        </button>
    @endif
</div>
