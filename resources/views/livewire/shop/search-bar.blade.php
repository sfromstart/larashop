<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <form wire:submit="goToSearch" class="relative">
        <input type="text"
               wire:model.live.debounce.300ms="query"
               @focus="open = true"
               class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-full border border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition"
               placeholder="상품을 검색하세요...">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        @if($query)
            <button type="button" wire:click="$set('query', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </form>

    {{-- 자동완성 결과 --}}
    @if($showResults && count($results) > 0)
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute top-full mt-2 w-full sm:w-80 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50" x-cloak>
            <div class="py-2">
                @foreach($results as $product)
                    <button wire:click="selectProduct('{{ $product->slug }}')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition text-left">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($product->primaryImageUrl)
                                <img src="{{ $product->primaryImageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://picsum.photos/seed/{{ $product->id }}/80/80" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-sm font-medium {{ $product->is_on_sale ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($product->price) }}원</p>
                        </div>
                    </button>
                @endforeach
            </div>
            <div class="border-t border-gray-100">
                <button wire:click="goToSearch" class="w-full px-4 py-3 text-sm text-indigo-600 hover:bg-indigo-50 transition text-center font-medium">
                    "{{ $query }}" 전체 검색 결과 보기
                </button>
            </div>
        </div>
    @endif
</div>
