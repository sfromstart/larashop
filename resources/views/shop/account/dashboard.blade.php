@extends('layouts.shop')

@section('title', '마이페이지')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-shop.breadcrumb :items="[['label' => '마이페이지']]" />

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- 사이드바 --}}
        @include('shop.account._sidebar')

        {{-- 메인 콘텐츠 --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">마이페이지</h1>

            {{-- 인사말 --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 md:p-8 text-white mb-8">
                <h2 class="text-xl font-bold mb-1">안녕하세요, {{ auth()->user()->name }}님!</h2>
                <p class="text-indigo-100 text-sm">{{ \App\Models\Setting::get('site_name', 'LaraShop') }}에서 즐거운 쇼핑 되세요.</p>
            </div>

            {{-- 주문 현황 --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ $orderStats['total'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">전체 주문</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-yellow-500">{{ $orderStats['pending'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">결제대기</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-purple-500">{{ $orderStats['shipped'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">배송중</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-green-500">{{ $orderStats['delivered'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">배송완료</p>
                </div>
            </div>

            {{-- 빠른 메뉴 --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('shop.wishlist.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">위시리스트</p>
                        <p class="text-sm text-gray-400">{{ $wishlistCount }}개</p>
                    </div>
                </a>
                <a href="{{ route('shop.account.orders') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">주문내역</p>
                        <p class="text-sm text-gray-400">{{ $orderStats['total'] }}건</p>
                    </div>
                </a>
                <a href="{{ route('shop.account.profile') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">프로필 수정</p>
                        <p class="text-sm text-gray-400">내 정보 관리</p>
                    </div>
                </a>
            </div>

            {{-- 최근 주문 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">최근 주문</h3>
                    <a href="{{ route('shop.account.orders') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">전체보기</a>
                </div>
                @if($recentOrders->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('shop.account.order-detail', $order) }}" class="flex items-center justify-between p-5 hover:bg-gray-50 transition">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('Y.m.d H:i') }} / {{ $order->items->count() }}개 상품</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->formatted_total }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @php
                                            $statusColor = \App\Enums\OrderStatus::tryFrom($order->status)?->color() ?? 'gray';
                                        @endphp
                                        bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>아직 주문 내역이 없습니다.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
