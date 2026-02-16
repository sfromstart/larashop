<div>
    <button wire:click="toggle"
            wire:loading.attr="disabled"
            class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg border transition
                   {{ $isWishlisted
                       ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100'
                       : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-red-500' }}">
        <svg wire:loading.remove wire:target="toggle"
             class="w-5 h-5 transition-transform {{ $isWishlisted ? 'scale-110' : '' }}"
             fill="{{ $isWishlisted ? 'currentColor' : 'none' }}"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <svg wire:loading wire:target="toggle" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-sm font-medium">{{ $isWishlisted ? '위시리스트 제거' : '위시리스트 추가' }}</span>
    </button>
</div>
