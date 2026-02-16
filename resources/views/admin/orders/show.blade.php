@extends('layouts.admin')

@section('title', '주문 상세')
@section('page-title', '주문 상세 - ' . $order->order_number)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            주문 목록으로
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Order Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">주문 상품</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">상품</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">수량</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">단가</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">합계</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($item->product_image)
                                                <img src="{{ Storage::url($item->product_image) }}" alt="{{ $item->product_name }}"
                                                     class="w-10 h-10 rounded-lg object-cover border border-gray-200 mr-3 flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                                @if($item->option_values)
                                                    <p class="text-xs text-gray-500">
                                                        @foreach($item->option_values as $optId => $valId)
                                                            @if(isset($optionValueMap[$valId]))
                                                                {{ $optionValueMap[$valId]->option->name }}: {{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700">{{ number_format($item->unit_price) }}원</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ number_format($item->total_price) }}원</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">상품금액</td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">{{ number_format($order->subtotal) }}원</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">배송비</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-900">{{ number_format($order->shipping_fee) }}원</td>
                            </tr>
                            @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-sm text-red-600">할인</td>
                                    <td class="px-6 py-3 text-right text-sm text-red-600">-{{ number_format($order->discount_amount) }}원</td>
                                </tr>
                            @endif
                            <tr class="border-t-2 border-gray-300">
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900">총 결제금액</td>
                                <td class="px-6 py-3 text-right text-base font-bold text-gray-900">{{ number_format($order->total) }}원</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">배송 정보</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">받는분</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">연락처</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_phone }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-gray-500">주소</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                [{{ $order->shipping_postal_code }}] {{ $order->shipping_address }}
                                @if($order->shipping_address_detail)
                                    {{ $order->shipping_address_detail }}
                                @endif
                            </dd>
                        </div>
                        @if($order->shipping_memo)
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500">배송 메모</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_memo }}</dd>
                            </div>
                        @endif
                        @if($order->tracking_number)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">운송장번호</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($order->tracking_url)
                                        <a href="{{ $order->tracking_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            {{ $order->tracking_number }}
                                        </a>
                                    @else
                                        {{ $order->tracking_number }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">

            {{-- Status Change --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">주문 상태 변경</h2>
                </div>
                <div class="p-6">
                    @php
                        $currentStatus = \App\Enums\OrderStatus::tryFrom($order->status);
                        $currentColor = $currentStatus?->color() ?? 'gray';
                    @endphp
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">현재 상태:</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium ml-1
                            bg-{{ $currentColor }}-100 text-{{ $currentColor }}-800">
                            {{ $currentStatus?->label() ?? $order->status }}
                        </span>
                    </div>

                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">변경할 상태</label>
                            <select name="status" id="status"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ $order->status === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">운송장번호</label>
                            <input type="text" name="tracking_number" id="tracking_number" value="{{ $order->tracking_number }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="운송장번호 입력">
                        </div>

                        <div>
                            <label for="tracking_url" class="block text-sm font-medium text-gray-700 mb-1">배송추적 URL</label>
                            <input type="url" name="tracking_url" id="tracking_url" value="{{ $order->tracking_url }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="https://...">
                        </div>

                        <div>
                            <label for="admin_memo" class="block text-sm font-medium text-gray-700 mb-1">관리자 메모</label>
                            <textarea name="admin_memo" id="admin_memo" rows="3"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                      placeholder="관리자 메모를 입력하세요">{{ $order->admin_memo }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors">
                            상태 변경
                        </button>
                    </form>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">고객 정보</h2>
                </div>
                <div class="p-6">
                    @if($order->user)
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500">이름</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">이메일</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->user->email }}</dd>
                            </div>
                            @if($order->user->phone)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">전화번호</dt>
                                    <dd class="mt-0.5 text-sm text-gray-900">{{ $order->user->phone }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs font-medium text-gray-500">총 주문 횟수</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->user->orders()->count() }}회</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">탈퇴한 회원</p>
                    @endif
                </div>
            </div>

            {{-- Order Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">주문 정보</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">주문번호</dt>
                            <dd class="mt-0.5 text-sm font-mono text-gray-900">{{ $order->order_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">결제방법</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $order->payment_method ?? '-' }}</dd>
                        </div>
                        @if($order->payment_id)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">결제 ID</dt>
                                <dd class="mt-0.5 text-sm font-mono text-gray-900">{{ $order->payment_id }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500">주문일시</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $order->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        @if($order->paid_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">결제일시</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->paid_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($order->shipped_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">발송일시</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->shipped_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($order->delivered_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">배송완료일시</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ $order->delivered_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($order->cancelled_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">취소일시</dt>
                                <dd class="mt-0.5 text-sm text-red-600">{{ $order->cancelled_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($order->admin_memo)
                            <div class="pt-3 border-t border-gray-200">
                                <dt class="text-xs font-medium text-gray-500">관리자 메모</dt>
                                <dd class="mt-0.5 text-sm text-gray-700 whitespace-pre-line">{{ $order->admin_memo }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
