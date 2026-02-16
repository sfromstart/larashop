@extends('layouts.admin')

@section('title', '쿠폰 수정')
@section('page-title', '쿠폰 수정')

@section('content')
    <div class="max-w-2xl">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">쿠폰 수정</h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">쿠폰 코드 <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm font-mono uppercase">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">쿠폰명 <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $coupon->name) }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type & Value --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">할인 유형 <span class="text-red-500">*</span></label>
                            <select name="type" id="type" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>정액 할인 (원)</option>
                                <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>정률 할인 (%)</option>
                            </select>
                        </div>
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">할인 값 <span class="text-red-500">*</span></label>
                            <input type="number" name="value" id="value" value="{{ old('value', $coupon->value) }}" required min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Constraints --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-1">최소 주문금액</label>
                            <div class="relative">
                                <input type="number" name="minimum_order_amount" id="minimum_order_amount"
                                       value="{{ old('minimum_order_amount', $coupon->minimum_order_amount) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                        <div>
                            <label for="maximum_discount" class="block text-sm font-medium text-gray-700 mb-1">최대 할인금액</label>
                            <div class="relative">
                                <input type="number" name="maximum_discount" id="maximum_discount"
                                       value="{{ old('maximum_discount', $coupon->maximum_discount) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">총 사용 횟수 제한</label>
                            <input type="number" name="usage_limit" id="usage_limit"
                                   value="{{ old('usage_limit', $coupon->usage_limit) }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="제한없음">
                            <p class="mt-1 text-xs text-gray-500">현재 사용 횟수: {{ $coupon->used_count }}회</p>
                        </div>
                        <div>
                            <label for="per_user_limit" class="block text-sm font-medium text-gray-700 mb-1">1인당 사용 횟수</label>
                            <input type="number" name="per_user_limit" id="per_user_limit"
                                   value="{{ old('per_user_limit', $coupon->per_user_limit) }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="제한없음">
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">시작일</label>
                            <input type="datetime-local" name="starts_at" id="starts_at"
                                   value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        </div>
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">만료일</label>
                            <input type="datetime-local" name="expires_at" id="expires_at"
                                   value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Active --}}
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">활성화</label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.coupons.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        취소
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors">
                        수정하기
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
