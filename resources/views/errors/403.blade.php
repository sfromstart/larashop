@extends('layouts.shop')

@section('title', '접근 권한이 없습니다')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
    <div class="text-center">
        <div class="mb-8">
            <span class="text-9xl font-extrabold text-red-100">403</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">접근 권한이 없습니다</h1>
        <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">
            이 페이지에 접근할 권한이 없습니다.
            로그인 상태를 확인하시거나 관리자에게 문의해 주세요.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('shop.home') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                홈으로 가기
            </a>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                이전 페이지
            </a>
        </div>
    </div>
</div>
@endsection
