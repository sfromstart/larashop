<aside class="w-full lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- 프로필 요약 --}}
        <div class="p-5 border-b border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-3">
                <span class="text-xl font-bold text-indigo-600">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->email }}</p>
        </div>

        {{-- 메뉴 --}}
        <nav class="p-3">
            <a href="{{ route('shop.account.dashboard') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('shop.account.dashboard') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>대시보드</span>
            </a>
            <a href="{{ route('shop.account.orders') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('shop.account.orders') || request()->routeIs('shop.account.order-detail') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>주문내역</span>
            </a>
            <a href="{{ route('shop.wishlist.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span>위시리스트</span>
            </a>
            <a href="{{ route('shop.account.profile') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('shop.account.profile') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>프로필 수정</span>
            </a>
            <div class="my-2 border-t border-gray-100"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>로그아웃</span>
                </button>
            </form>
        </nav>
    </div>
</aside>
