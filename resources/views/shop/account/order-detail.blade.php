@extends('layouts.shop')

@section('title', '주문 상세 - ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-shop.breadcrumb :items="[
        ['label' => '마이페이지', 'url' => route('shop.account.dashboard')],
        ['label' => '주문내역', 'url' => route('shop.account.orders')],
        ['label' => $order->order_number],
    ]" />

    <div class="flex flex-col lg:flex-row gap-8">
        @include('shop.account._sidebar')

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">주문 상세</h1>
                @php
                    $statusColor = \App\Enums\OrderStatus::tryFrom($order->status)?->color() ?? 'gray';
                @endphp
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                    {{ $order->status_label }}
                </span>
            </div>

            {{-- 주문 정보 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-4">주문 정보</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">주문번호:</span>
                            <span class="font-medium text-gray-800 ml-2">{{ $order->order_number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">주문일시:</span>
                            <span class="font-medium text-gray-800 ml-2">{{ $order->created_at->format('Y.m.d H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">결제방법:</span>
                            <span class="font-medium text-gray-800 ml-2">
                                @switch($order->payment_method)
                                    @case('bank_transfer') 무통장입금 @break
                                    @default {{ $order->payment_method ?? '-' }}
                                @endswitch
                            </span>
                        </div>
                        @if($order->paid_at)
                            <div>
                                <span class="text-gray-500">결제일시:</span>
                                <span class="font-medium text-gray-800 ml-2">{{ $order->paid_at->format('Y.m.d H:i') }}</span>
                            </div>
                        @endif
                        @if($order->tracking_number)
                            <div>
                                <span class="text-gray-500">운송장번호:</span>
                                @if($order->tracking_url)
                                    <a href="{{ $order->tracking_url }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-700 ml-2">{{ $order->tracking_number }}</a>
                                @else
                                    <span class="font-medium text-gray-800 ml-2">{{ $order->tracking_number }}</span>
                                @endif
                            </div>
                        @endif
                        @if($order->shipped_at)
                            <div>
                                <span class="text-gray-500">발송일:</span>
                                <span class="font-medium text-gray-800 ml-2">{{ $order->shipped_at->format('Y.m.d') }}</span>
                            </div>
                        @endif
                        @if($order->delivered_at)
                            <div>
                                <span class="text-gray-500">배송완료:</span>
                                <span class="font-medium text-gray-800 ml-2">{{ $order->delivered_at->format('Y.m.d') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 배송 정보 --}}
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-4">배송 정보</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-500">수령인:</span> <span class="text-gray-800 ml-2">{{ $order->shipping_name }}</span></p>
                        <p><span class="text-gray-500">연락처:</span> <span class="text-gray-800 ml-2">{{ $order->shipping_phone }}</span></p>
                        <p><span class="text-gray-500">주소:</span> <span class="text-gray-800 ml-2">({{ $order->shipping_postal_code }}) {{ $order->shipping_address }} {{ $order->shipping_address_detail }}</span></p>
                        @if($order->shipping_memo)
                            <p><span class="text-gray-500">배송메모:</span> <span class="text-gray-800 ml-2">{{ $order->shipping_memo }}</span></p>
                        @endif
                    </div>
                </div>

                {{-- 주문 상품 --}}
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">주문 상품</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name ?? '' }}" class="w-full h-full object-cover">
                                    @elseif($item->product && $item->product->primaryImageUrl)
                                        <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product_name ?? '' }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">
                                        @if($item->product)
                                            <a href="{{ route('shop.products.show', $item->product->slug) }}" class="hover:text-indigo-600 transition">{{ $item->product_name }}</a>
                                        @else
                                            {{ $item->product_name ?? '삭제된 상품' }}
                                        @endif
                                    </p>
                                    @if($item->option_values)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            @foreach($item->option_values as $optId => $valId)
                                                @if(isset($optionValueMap[$valId])){{ $optionValueMap[$valId]->option->name }}: {{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}@endif
                                            @endforeach
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">{{ number_format($item->unit_price) }}원 x {{ $item->quantity }}개</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($item->total_price) }}원</p>
                                    {{-- 리뷰 작성 버튼 (배송완료 시) --}}
                                    @if($order->status === 'delivered' && $item->product)
                                        @php
                                            $hasReview = \App\Models\Review::where('user_id', auth()->id())
                                                ->where('product_id', $item->product_id)
                                                ->where('order_id', $order->id)
                                                ->exists();
                                        @endphp
                                        @if(!$hasReview)
                                            <a href="{{ route('shop.products.show', $item->product->slug) }}#reviews"
                                               class="inline-flex items-center mt-1 text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                                리뷰 작성
                                            </a>
                                        @else
                                            <span class="inline-flex items-center mt-1 text-xs text-gray-400">리뷰 작성완료</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 결제 요약 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">결제 정보</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">상품 금액</span>
                        <span class="font-medium">{{ number_format($order->subtotal) }}원</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">배송비</span>
                        <span class="font-medium">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . '원' : '무료' }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-red-600">
                            <span>할인</span>
                            <span class="font-medium">-{{ number_format($order->discount_amount) }}원</span>
                        </div>
                    @endif
                    <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between">
                        <span class="font-semibold text-gray-900">총 결제 금액</span>
                        <span class="text-xl font-bold text-indigo-600">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
