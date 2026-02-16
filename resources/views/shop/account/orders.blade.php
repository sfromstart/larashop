@extends('layouts.shop')

@section('title', '주문내역')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-shop.breadcrumb :items="[
        ['label' => '마이페이지', 'url' => route('shop.account.dashboard')],
        ['label' => '주문내역'],
    ]" />

    <div class="flex flex-col lg:flex-row gap-8">
        @include('shop.account._sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">주문내역</h1>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            {{-- 주문 헤더 --}}
                            <div class="flex flex-wrap items-center justify-between p-5 bg-gray-50 border-b border-gray-100 gap-4">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('Y.m.d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    @php
                                        $statusColor = \App\Enums\OrderStatus::tryFrom($order->status)?->color() ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                                        {{ $order->status_label }}
                                    </span>
                                    <a href="{{ route('shop.account.order-detail', $order) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">상세보기</a>
                                </div>
                            </div>

                            {{-- 주문 상품 미리보기 --}}
                            <div class="p-5">
                                <div class="flex items-center space-x-4">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                            @if($item->product && $item->product->primaryImageUrl)
                                                <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product_name ?? '' }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="https://picsum.photos/seed/item{{ $item->id }}/100/100" alt="" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <span class="text-sm text-gray-400">외 {{ $order->items->count() - 3 }}개</span>
                                    @endif
                                    <div class="ml-auto text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $order->formatted_total }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->items->count() }}개 상품</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">주문 내역이 없습니다</h3>
                    <p class="text-sm text-gray-400 mb-6">아직 주문하신 상품이 없습니다.</p>
                    <a href="{{ route('shop.products.index') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        쇼핑하러 가기
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
