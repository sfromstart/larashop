@extends('layouts.admin')

@section('title', '쿠폰 등록')
@section('page-title', '쿠폰 등록')

@section('content')
    <div class="max-w-2xl">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">쿠폰 정보</h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">쿠폰 코드 <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   class="block flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm font-mono uppercase"
                                   placeholder="SUMMER2025">
                            <button type="button" onclick="generateCode()"
                                    class="px-3 py-2 text-xs font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors whitespace-nowrap">
                                자동생성
                            </button>
                        </div>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">쿠폰명 <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="여름 특별 할인 쿠폰">
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
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>정액 할인 (원)</option>
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>정률 할인 (%)</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">할인 값 <span class="text-red-500">*</span></label>
                            <input type="number" name="value" id="value" value="{{ old('value') }}" required min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="0">
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
                                <input type="number" name="minimum_order_amount" id="minimum_order_amount" value="{{ old('minimum_order_amount') }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8"
                                       placeholder="0">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                        <div>
                            <label for="maximum_discount" class="block text-sm font-medium text-gray-700 mb-1">최대 할인금액</label>
                            <div class="relative">
                                <input type="number" name="maximum_discount" id="maximum_discount" value="{{ old('maximum_discount') }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8"
                                       placeholder="0">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">총 사용 횟수 제한</label>
                            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="제한없음">
                            <p class="mt-1 text-xs text-gray-500">비워두면 무제한</p>
                        </div>
                        <div>
                            <label for="per_user_limit" class="block text-sm font-medium text-gray-700 mb-1">1인당 사용 횟수</label>
                            <input type="number" name="per_user_limit" id="per_user_limit" value="{{ old('per_user_limit') }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="제한없음">
                            <p class="mt-1 text-xs text-gray-500">비워두면 무제한</p>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">시작일</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        </div>
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">만료일</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
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
                               {{ old('is_active', true) ? 'checked' : '' }}
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
                        등록하기
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = code;
        }
    </script>
    @endpush
@endsection
