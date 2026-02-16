@extends('layouts.shop')

@section('title', '주문 완료')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
    
    {{-- 성공 아이콘 (미니멀) --}}
    <div class="mb-8">
        <svg class="w-16 h-16 mx-auto text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 uppercase tracking-wide">감사합니다</h1>
    <p class="text-xl text-gray-900 mb-2 font-light">주문이 접수되었습니다.</p>
    <p class="text-sm text-gray-500 mb-12">주문번호 #{{ $order->order_number }}</p>

    {{-- 결제 안내 --}}
    @if($order->payment_method === 'bank_transfer')
        <div class="bg-gray-50 p-8 mb-12 text-left">
            <h3 class="font-bold text-gray-900 mb-4 uppercase tracking-widest text-xs border-b border-gray-200 pb-2">입금 안내</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">입금 계좌</span>
                    <span class="font-medium text-gray-900">{{ \App\Models\Setting::get('bank_info', 'Kookmin Bank 000-000-000000 (LaraShop)') }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">예금주</span>
                    <span class="font-medium text-gray-900">(LaraShop)</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">입금자명</span>
                    <span class="font-medium text-gray-900">{{ $order->shipping_name }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">입금액</span>
                    <span class="font-bold text-gray-900 text-lg">{{ $order->formatted_total }}</span>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-400">* 24시간 이내에 입금해 주세요.</p>
        </div>
    @endif

    <div class="text-left border-t border-black pt-12">
        <h3 class="font-bold text-gray-900 mb-8 uppercase tracking-widest text-center">주문 상세</h3>
        
        <div class="space-y-6 mb-12">
            @foreach($order->items as $item)
                <div class="flex items-center gap-6">
                    <div class="w-20 h-24 bg-gray-100 flex-shrink-0">
                        @if($item->product_image)
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @elseif($item->product && $item->product->primaryImageUrl)
                            <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-medium text-gray-900">{{ $item->product_name }}</h4>
                        @if($item->option_values)
                            <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">
                                @foreach($item->option_values as $optId => $valId)
                                    @if(isset($optionValueMap[$valId])){{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}@endif
                                @endforeach
                            </p>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">{{ number_format($item->unit_price) }} x {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-medium text-gray-900">{{ number_format($item->total_price) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 pt-6 space-y-3 text-sm max-w-sm ml-auto">
            <div class="flex justify-between">
                <span class="text-gray-500 uppercase tracking-wide text-xs">소계</span>
                <span class="font-medium">{{ number_format($order->subtotal) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 uppercase tracking-wide text-xs">배송비</span>
                <span class="font-medium">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) : '무료' }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span class="uppercase tracking-wide text-xs">할인</span>
                    <span>-{{ number_format($order->discount_amount) }}</span>
                </div>
            @endif
            <div class="flex justify-between pt-4 border-t border-black items-end">
                <span class="font-bold text-gray-900 uppercase tracking-widest">합계</span>
                <span class="text-xl font-bold text-gray-900">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    {{-- 배송 정보 요약 --}}
    <div class="mt-16 text-left">
        <h3 class="font-bold text-gray-900 mb-6 uppercase tracking-widest text-xs border-b border-gray-200 pb-2">배송 정보</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-gray-600">
            <div>
                <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">수취인</span>
                <p class="text-gray-900">{{ $order->shipping_name }} / {{ $order->shipping_phone }}</p>
            </div>
            <div>
                <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">주소</span>
                <p class="text-gray-900">({{ $order->shipping_postal_code }}) {{ $order->shipping_address }} {{ $order->shipping_address_detail }}</p>
            </div>
            @if($order->shipping_memo)
                <div class="md:col-span-2">
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-1">메모</span>
                    <p class="text-gray-900">{{ $order->shipping_memo }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- 액션 버튼 --}}
    <div class="mt-16 flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('shop.products.index') }}" class="px-8 py-4 bg-black text-white text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition min-w-[200px]">
            쇼핑 계속하기
        </a>
        <a href="{{ route('shop.account.order-detail', $order) }}" class="px-8 py-4 bg-white border border-gray-300 text-black text-sm font-bold uppercase tracking-widest hover:bg-gray-50 transition min-w-[200px]">
            주문 상세 보기
        </a>
    </div>

</div>
@endsection
