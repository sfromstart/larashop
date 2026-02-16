@extends('layouts.admin')

@section('title', '주문 관리')
@section('page-title', '주문 관리')

@php
    $currentSort = request('sort', 'created_at');
    $currentDirection = request('direction', 'desc');

    $paymentLabels = [
        'bank_transfer' => '무통장입금',
        'card' => '카드결제',
    ];
@endphp

@section('content')
    {{-- Summary Dashboard --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
        <a href="{{ route('admin.orders.index', array_filter(request()->except('status', 'page'))) }}"
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow {{ !request('status') ? 'ring-2 ring-slate-400' : '' }}">
            <p class="text-xs font-medium text-gray-500 mb-1">전체</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($summary['total_count']) }}</p>
        </a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 mb-1">매출</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($summary['total_revenue']) }}<span class="text-xs font-normal">원</span></p>
        </div>
        @php
            $statusCards = [
                ['key' => 'pending', 'label' => '결제대기', 'color' => 'yellow'],
                ['key' => 'paid', 'label' => '결제완료', 'color' => 'blue'],
                ['key' => 'preparing', 'label' => '상품준비', 'color' => 'indigo'],
                ['key' => 'shipped', 'label' => '배송중', 'color' => 'purple'],
                ['key' => 'delivered', 'label' => '배송완료', 'color' => 'green'],
                ['key' => 'cancelled', 'label' => '취소', 'color' => 'red'],
            ];
        @endphp
        @foreach($statusCards as $card)
            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $card['key']])) }}"
               class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow {{ request('status') === $card['key'] ? 'ring-2 ring-' . $card['color'] . '-400' : '' }}">
                <p class="text-xs font-medium text-{{ $card['color'] }}-600 mb-1">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $summary[$card['key']] }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="orderManager()">
        {{-- Filters --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                {{-- Preserve sort --}}
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif

                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">검색</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="주문번호, 고객명, 전화번호..."
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                </div>

                {{-- Status --}}
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">주문상태</label>
                    <select name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        <option value="">전체</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Payment Method --}}
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">결제방법</label>
                    <select name="payment_method" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        <option value="">전체</option>
                        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>무통장입금</option>
                        <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>카드결제</option>
                    </select>
                </div>

                {{-- Date From --}}
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">시작일</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                </div>

                {{-- Date To --}}
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">종료일</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-slate-700 rounded-lg hover:bg-slate-600 transition-colors">
                        검색
                    </button>
                    <a href="{{ route('admin.orders.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        초기화
                    </a>
                </div>
            </form>
        </div>

        {{-- Action Bar --}}
        <div class="px-6 py-3 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                {{-- Bulk Actions --}}
                <div x-show="selectedOrders.length > 0" x-cloak class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-700" x-text="selectedOrders.length + '건 선택'"></span>
                    <select x-model="bulkStatus" class="rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm py-1.5">
                        <option value="">상태 선택</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <button @click="submitBulkUpdate()" :disabled="!bulkStatus"
                            class="px-3 py-1.5 text-sm font-medium text-white bg-slate-700 rounded-lg hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        일괄 변경
                    </button>
                </div>
                <span x-show="selectedOrders.length === 0" class="text-sm text-gray-500">
                    주문 목록 <span class="font-medium">({{ $orders->total() }}건)</span>
                </span>
            </div>

            {{-- Excel Download --}}
            <a href="{{ route('admin.orders.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                엑셀 다운로드
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" @change="toggleAll($event)" :checked="allSelected"
                                   class="rounded border-gray-300 text-slate-600 focus:ring-slate-500">
                        </th>
                        @php
                            $sortableColumns = [
                                'order_number' => '주문번호',
                                'total' => '금액',
                                'status' => '상태',
                                'payment_method' => '결제방법',
                                'created_at' => '일시',
                            ];
                        @endphp
                        @foreach($sortableColumns as $col => $label)
                            @php
                                $isActive = $currentSort === $col;
                                $nextDir = ($isActive && $currentDirection === 'asc') ? 'desc' : 'asc';
                                $sortUrl = request()->fullUrlWithQuery(['sort' => $col, 'direction' => $nextDir, 'page' => null]);
                            @endphp
                            @if($col === 'order_number')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ $sortUrl }}" class="inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                                        {{ $label }}
                                        @if($isActive)
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                                @if($currentDirection === 'asc')
                                                    <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.707a1 1 0 01-1.414-1.414z"/>
                                                @else
                                                    <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.707a1 1 0 011.414 1.414z"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                            @elseif($col === 'total')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="1">
                                    고객
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ $sortUrl }}" class="inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                                        {{ $label }}
                                        @if($isActive)
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                                @if($currentDirection === 'asc')
                                                    <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.707a1 1 0 01-1.414-1.414z"/>
                                                @else
                                                    <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.707a1 1 0 011.414 1.414z"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                            @else
                                <th class="px-6 py-3 {{ $col === 'status' ? 'text-center' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ $sortUrl }}" class="inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
                                        {{ $label }}
                                        @if($isActive)
                                            <svg class="w-3.5 h-3.5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                                @if($currentDirection === 'asc')
                                                    <path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.707a1 1 0 01-1.414-1.414z"/>
                                                @else
                                                    <path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.707a1 1 0 011.414 1.414z"/>
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                            @endif
                        @endforeach
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">관리</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50" :class="selectedOrders.includes({{ $order->id }}) ? 'bg-slate-50' : ''">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" value="{{ $order->id }}" @change="toggleOrder({{ $order->id }})"
                                       :checked="selectedOrders.includes({{ $order->id }})"
                                       class="rounded border-gray-300 text-slate-600 focus:ring-slate-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->shipping_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->user?->name ?? '-' }} / {{ $order->shipping_phone }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ number_format($order->total) }}원</p>
                                    @if($order->discount_amount > 0)
                                        <p class="text-xs text-red-500">-{{ number_format($order->discount_amount) }}원 할인</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusEnum = \App\Enums\OrderStatus::tryFrom($order->status);
                                    $color = $statusEnum?->color() ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $statusEnum?->label() ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paymentLabels[$order->payment_method] ?? ($order->payment_method ?? '-') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                    상세보기
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                주문 내역이 없습니다.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Bulk Update Form (hidden) --}}
    <form id="bulkUpdateForm" action="{{ route('admin.orders.bulk-status') }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="bulkStatusInput">
    </form>

@push('scripts')
<script>
    function orderManager() {
        return {
            selectedOrders: [],
            bulkStatus: '',
            orderIds: @json($orders->pluck('id')->toArray()),

            get allSelected() {
                return this.orderIds.length > 0 && this.orderIds.every(id => this.selectedOrders.includes(id));
            },

            toggleAll(event) {
                if (event.target.checked) {
                    this.selectedOrders = [...this.orderIds];
                } else {
                    this.selectedOrders = [];
                }
            },

            toggleOrder(id) {
                const idx = this.selectedOrders.indexOf(id);
                if (idx > -1) {
                    this.selectedOrders.splice(idx, 1);
                } else {
                    this.selectedOrders.push(id);
                }
            },

            submitBulkUpdate() {
                if (!this.bulkStatus || this.selectedOrders.length === 0) return;
                if (!confirm(this.selectedOrders.length + '건의 주문 상태를 변경하시겠습니까?')) return;

                const form = document.getElementById('bulkUpdateForm');
                document.getElementById('bulkStatusInput').value = this.bulkStatus;

                // Add order IDs
                this.selectedOrders.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                form.submit();
            }
        };
    }
</script>
@endpush
@endsection
