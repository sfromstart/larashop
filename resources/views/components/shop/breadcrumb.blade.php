@props(['items' => []])

@php
    $breadcrumbItems = [
        ['label' => '홈', 'url' => route('shop.home')]
    ];
    foreach ($items as $item) {
        if ($item) {
            $breadcrumbItems[] = $item;
        }
    }
@endphp

<nav class="flex items-center space-x-2 text-sm text-gray-500 py-4" aria-label="Breadcrumb">
    @foreach($breadcrumbItems as $index => $item)
        @if($index > 0)
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif

        @if(isset($item['url']) && $item['url'])
            <a href="{{ $item['url'] }}" class="hover:text-indigo-600 transition">
                @if($index === 0)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                @else
                    {{ $item['label'] }}
                @endif
            </a>
        @else
            <span class="text-gray-800 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>

{{-- BreadcrumbList JSON-LD --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($breadcrumbItems)->map(function ($item, $index) {
        $element = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'],
        ];
        if (isset($item['url']) && $item['url']) {
            $element['item'] = $item['url'];
        }
        return $element;
    })->toArray(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
