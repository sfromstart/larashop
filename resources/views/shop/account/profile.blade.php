@extends('layouts.shop')

@section('title', '프로필 수정')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-shop.breadcrumb :items="[
        ['label' => '마이페이지', 'url' => route('shop.account.dashboard')],
        ['label' => '프로필 수정'],
    ]" />

    <div class="flex flex-col lg:flex-row gap-8">
        @include('shop.account._sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">프로필 수정</h1>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <form action="{{ route('shop.account.update-profile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- 이름 --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름</label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 이메일 --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일</label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 전화번호 --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">전화번호</label>
                            <input type="tel" name="phone" id="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="010-0000-0000">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                            저장하기
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
