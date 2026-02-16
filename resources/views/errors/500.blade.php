@extends('layouts.shop')

@section('title', '서버 오류')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
    <div class="text-center">
        <div class="mb-8">
            <span class="text-9xl font-extrabold text-orange-100">500</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">서버 오류가 발생했습니다</h1>
        <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">
            일시적인 서버 오류가 발생했습니다.
            잠시 후 다시 시도해 주세요. 문제가 지속되면 고객센터로 문의해 주세요.
        </p>

        <div class="bg-gray-50 rounded-xl p-6 mb-8 max-w-md mx-auto">
            <p class="text-sm text-gray-600 mb-1">고객센터</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Setting::get('cs_phone', '1588-0000') }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ \App\Models\Setting::get('cs_hours', '평일 09:00 ~ 18:00') }}</p>
        </div>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('shop.home') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                홈으로 가기
            </a>
            <button onclick="window.location.reload()"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                다시 시도
            </button>
        </div>
    </div>
</div>
@endsection
