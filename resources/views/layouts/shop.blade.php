<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($seo))
        <x-seo :seo="$seo" />
    @else
        <title>@yield('title', \App\Models\Setting::get('site_name', 'LaraShop')) - {{ \App\Models\Setting::get('site_name', 'LaraShop') }}</title>
        <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('site_description', 'LaraShop - 최고의 온라인 쇼핑몰'))">
    @endif

    @yield('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Noto Sans KR', sans-serif; }
    </style>

    {{-- Google Analytics --}}
    @if(\App\Models\Setting::get('google_analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ \App\Models\Setting::get('google_analytics_id') }}');
    </script>
    @endif

    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-800">

    {{-- 상단 안내바 --}}
    <div class="bg-gray-900 text-gray-300 text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <p class="hidden sm:block">
                <svg class="inline-block w-4 h-4 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                {{ \App\Models\Setting::get('free_shipping_notice', '50,000원 이상 구매 시 무료배송') }}
            </p>
            <div class="flex items-center space-x-4 ml-auto">
                @auth
                    <a href="{{ route('shop.account.dashboard') }}" class="hover:text-white transition">마이페이지</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-white transition">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white transition">로그인</a>
                    <a href="{{ route('register') }}" class="hover:text-white transition">회원가입</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- 메인 헤더 --}}
    {{-- 메인 헤더 --}}
    <header class="bg-white sticky top-0 z-50 border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                {{-- 좌측 네비게이션 (데스크톱) --}}
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('shop.products.index') }}" class="text-sm font-medium text-gray-900 hover:text-gray-600 tracking-wide uppercase transition">Shop</a>
                    @isset($rootCategories)
                        @foreach($rootCategories->take(2) as $cat)
                            <a href="{{ route('shop.products.category', $cat->slug) }}" class="text-sm font-medium text-gray-900 hover:text-gray-600 tracking-wide uppercase transition">{{ $cat->name }}</a>
                        @endforeach
                    @endisset
                </nav>

                {{-- 모바일 메뉴 버튼 --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-900 hover:text-gray-600">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- 중앙 로고 --}}
                <a href="{{ route('shop.home') }}" class="flex-shrink-0 absolute left-1/2 transform -translate-x-1/2">
                    <span class="text-3xl font-bold text-gray-900 tracking-tighter">{{ \App\Models\Setting::get('site_name', 'schön.') }}</span>
                </a>

                {{-- 우측 아이콘 --}}
                <div class="flex items-center space-x-6">
                    {{-- 검색 --}}
                    <div class="hidden md:block">
                        <livewire:shop.search-bar />
                    </div>

                    {{-- 위시리스트 --}}
                    @auth
                        <a href="{{ route('shop.wishlist.index') }}" class="text-gray-900 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </a>
                    @endauth

                    {{-- 마이페이지 --}}
                    @auth
                        <a href="{{ route('shop.account.dashboard') }}" class="text-gray-900 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-900 hover:text-gray-600 transition text-sm font-medium tracking-wide uppercase">로그인</a>
                    @endauth

                    @auth
                        @if(auth()->user()->role === 'admin')
                            {{-- 관리자 주문관리 --}}
                            <a href="{{ route('admin.orders.index') }}" class="text-gray-900 hover:text-gray-600 transition" title="주문관리">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </a>
                        @else
                            {{-- 미니카트 (일반 유저만) --}}
                            <livewire:shop.mini-cart />
                        @endif
                    @else
                        {{-- 비로그인 미니카트 --}}
                        <livewire:shop.mini-cart />
                    @endauth
                </div>
            </div>
        </div>

        {{-- 모바일 메뉴 --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden border-t border-gray-100 bg-white absolute w-full z-50" x-cloak>
            <div class="px-4 py-6 space-y-4">
                <div class="mb-6">
                    <livewire:shop.search-bar />
                </div>
                <a href="{{ route('shop.home') }}" class="block text-lg font-medium text-gray-900 hover:text-gray-600">홈</a>
                <a href="{{ route('shop.products.index') }}" class="block text-lg font-medium text-gray-900 hover:text-gray-600">Shop</a>
                @isset($rootCategories)
                    @foreach($rootCategories as $cat)
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center justify-between w-full text-lg font-medium text-gray-900 hover:text-gray-600">
                                {{ $cat->name }}
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" class="pl-4 mt-2 space-y-2">
                                <a href="{{ route('shop.products.category', $cat->slug) }}" class="block text-base text-gray-600 hover:text-gray-900">{{ $cat->name }} 전체</a>
                                @foreach($cat->children as $child)
                                    <a href="{{ route('shop.products.category', $child->slug) }}" class="block text-base text-gray-600 hover:text-gray-900">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endisset
                <hr class="border-gray-100 my-4">
                @auth
                    <a href="{{ route('shop.account.dashboard') }}" class="block text-base font-medium text-gray-900">마이페이지</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left text-base font-medium text-gray-600 hover:text-gray-900 mt-2">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-base font-medium text-gray-900">로그인</a>
                    <a href="{{ route('register') }}" class="block text-base font-medium text-gray-900 mt-2">회원가입</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- 플래시 메시지 --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
        </div>
    @endif

    {{-- 메인 컨텐츠 --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- 푸터 --}}
    <footer class="bg-gray-50 pt-16 pb-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                {{-- 브랜드 정보 --}}
                <div class="col-span-1 md:col-span-1">
                    <span class="text-2xl font-bold text-gray-900 tracking-tighter block mb-6">{{ \App\Models\Setting::get('site_name', 'schön.') }}</span>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        {{ \App\Models\Setting::get('site_description', 'The premier destination for luxury fashion and lifestyle. Curated for the modern individual.') }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-gray-900 transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-900 transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>

                {{-- 링크 컬럼 --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-6">Shop</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('shop.products.index') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">전체 상품</a></li>
                        <li><a href="{{ route('shop.products.index', ['sort' => 'latest']) }}" class="text-sm text-gray-500 hover:text-gray-900 transition">신상품</a></li>
                        <li><a href="{{ route('shop.products.index', ['sort' => 'popular']) }}" class="text-sm text-gray-500 hover:text-gray-900 transition">베스트셀러</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-6">고객지원</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('page.shipping') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">배송 및 반품</a></li>
                        <li><a href="{{ route('page.terms') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">이용약관</a></li>
                        <li><a href="{{ route('page.privacy') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">개인정보처리방침</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-6">연락처</h3>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li>{{ \App\Models\Setting::get('cs_email', 'help@schon.com') }}</li>
                        <li>{{ \App\Models\Setting::get('cs_phone', '+1 (555) 123-4567') }}</li>
                        <li class="pt-2">평일 오전 9시~오후 6시</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'schön.') }}. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    {{-- 결제 아이콘이나 기타 하단 링크 추가 가능 --}}
                </div>
            </div>
        </div>
    </footer>

    {{-- 토스트 알림 --}}
    <x-toast />

    @livewireScripts
    @stack('scripts')
</body>
</html>
