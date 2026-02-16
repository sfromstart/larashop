@extends('layouts.admin')

@section('title', '쿠폰 관리')
@section('page-title', '쿠폰 관리')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">
                쿠폰 목록
                <span class="text-sm font-normal text-gray-500 ml-1">({{ $coupons->total() }}개)</span>
            </h2>
            <a href="{{ route('admin.coupons.create') }}"
               class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                쿠폰 등록
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">쿠폰 코드</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">쿠폰명</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">할인</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">사용/제한</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">기간</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">관리</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $coupon->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">
                                    @if($coupon->type === 'percent')
                                        {{ number_format($coupon->value) }}%
                                    @else
                                        {{ number_format($coupon->value) }}원
                                    @endif
                                </span>
                                @if($coupon->minimum_order_amount > 0)
                                    <span class="block text-xs text-gray-500">최소 {{ number_format($coupon->minimum_order_amount) }}원</span>
                                @endif
                                @if($coupon->maximum_discount)
                                    <span class="block text-xs text-gray-500">최대 {{ number_format($coupon->maximum_discount) }}원</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                {{ $coupon->used_count }}
                                / {{ $coupon->usage_limit ?? '무제한' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($coupon->starts_at || $coupon->expires_at)
                                    <div>
                                        @if($coupon->starts_at)
                                            <span>{{ $coupon->starts_at->format('Y-m-d') }}</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                        <span>~</span>
                                        @if($coupon->expires_at)
                                            <span>{{ $coupon->expires_at->format('Y-m-d') }}</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                    @if($coupon->expires_at && $coupon->expires_at->isPast())
                                        <span class="text-xs text-red-500">만료됨</span>
                                    @endif
                                @else
                                    <span>상시</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($coupon->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">활성</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">비활성</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        수정
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                          onsubmit="return confirm('정말 삭제하시겠습니까?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                            삭제
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                등록된 쿠폰이 없습니다.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
@endsection
