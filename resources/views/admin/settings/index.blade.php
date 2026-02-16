@extends('layouts.admin')

@section('title', '사이트 설정')
@section('page-title', '사이트 설정')

@section('content')
    <form action="{{ route('admin.settings.update') }}" method="POST"
          x-data="{ activeTab: '{{ request('tab', 'general') }}' }">
        @csrf

        {{-- Tabs --}}
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <button type="button" @click="activeTab = 'general'"
                        class="pb-3 text-sm font-medium border-b-2 transition-colors"
                        :class="activeTab === 'general' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    기본 설정
                </button>
                <button type="button" @click="activeTab = 'shopping'"
                        class="pb-3 text-sm font-medium border-b-2 transition-colors"
                        :class="activeTab === 'shopping' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    쇼핑 설정
                </button>
                <button type="button" @click="activeTab = 'seo'"
                        class="pb-3 text-sm font-medium border-b-2 transition-colors"
                        :class="activeTab === 'seo' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    SEO 설정
                </button>
            </nav>
        </div>

        {{-- General Tab --}}
        <div x-show="activeTab === 'general'" x-transition>
            <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">기본 설정</h2>
                    <p class="mt-1 text-sm text-gray-500">쇼핑몰의 기본 정보를 설정합니다.</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">쇼핑몰명 <span class="text-red-500">*</span></label>
                        <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">쇼핑몰 설명</label>
                        <textarea name="site_description" id="site_description" rows="3"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">{{ old('site_description', $settings['site_description']) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-1">대표 전화번호</label>
                            <input type="text" name="site_phone" id="site_phone" value="{{ old('site_phone', $settings['site_phone']) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="02-1234-5678">
                        </div>
                        <div>
                            <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">대표 이메일</label>
                            <input type="email" name="site_email" id="site_email" value="{{ old('site_email', $settings['site_email']) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="info@larashop.com">
                            @error('site_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="site_address" class="block text-sm font-medium text-gray-700 mb-1">사업장 주소</label>
                        <input type="text" name="site_address" id="site_address" value="{{ old('site_address', $settings['site_address']) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="서울특별시 강남구...">
                    </div>

                    <div>
                        <label for="site_business_number" class="block text-sm font-medium text-gray-700 mb-1">사업자등록번호</label>
                        <input type="text" name="site_business_number" id="site_business_number" value="{{ old('site_business_number', $settings['site_business_number']) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="123-45-67890">
                    </div>
                </div>
            </div>
        </div>

        {{-- Shopping Tab --}}
        <div x-show="activeTab === 'shopping'" x-transition>
            <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">쇼핑 설정</h2>
                    <p class="mt-1 text-sm text-gray-500">배송비, 포인트 등 쇼핑 관련 설정을 관리합니다.</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="default_shipping_fee" class="block text-sm font-medium text-gray-700 mb-1">기본 배송비</label>
                            <div class="relative">
                                <input type="number" name="default_shipping_fee" id="default_shipping_fee"
                                       value="{{ old('default_shipping_fee', $settings['default_shipping_fee']) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                        <div>
                            <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-1">무료배송 기준금액</label>
                            <div class="relative">
                                <input type="number" name="free_shipping_threshold" id="free_shipping_threshold"
                                       value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">이 금액 이상 구매 시 배송비 무료</p>
                        </div>
                    </div>

                    <div>
                        <label for="point_rate" class="block text-sm font-medium text-gray-700 mb-1">포인트 적립률</label>
                        <div class="relative w-32">
                            <input type="number" name="point_rate" id="point_rate"
                                   value="{{ old('point_rate', $settings['point_rate']) }}" min="0" max="100" step="0.1"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">결제 금액 대비 적립 비율</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">최소 주문금액</label>
                            <div class="relative">
                                <input type="number" name="min_order_amount" id="min_order_amount"
                                       value="{{ old('min_order_amount', $settings['min_order_amount']) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                        </div>
                        <div>
                            <label for="max_order_quantity" class="block text-sm font-medium text-gray-700 mb-1">최대 주문수량 (1상품)</label>
                            <div class="relative">
                                <input type="number" name="max_order_quantity" id="max_order_quantity"
                                       value="{{ old('max_order_quantity', $settings['max_order_quantity']) }}" min="1"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">개</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEO Tab --}}
        <div x-show="activeTab === 'seo'" x-transition>
            <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">SEO 설정</h2>
                    <p class="mt-1 text-sm text-gray-500">검색엔진 최적화 및 분석 도구를 설정합니다.</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">기본 메타 타이틀</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $settings['meta_title']) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="LaraShop - 온라인 쇼핑몰">
                        <p class="mt-1 text-xs text-gray-500">검색 결과에 표시되는 기본 제목 (권장: 50-60자)</p>
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">기본 메타 설명</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                  placeholder="쇼핑몰의 기본 설명을 입력하세요">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">검색 결과에 표시되는 기본 설명 (권장: 150-160자)</p>
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">메타 키워드</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords']) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="쇼핑몰, 온라인쇼핑, ...">
                        <p class="mt-1 text-xs text-gray-500">쉼표(,)로 구분하여 입력하세요</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">분석 도구</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                                <input type="text" name="google_analytics_id" id="google_analytics_id"
                                       value="{{ old('google_analytics_id', $settings['google_analytics_id']) }}"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                       placeholder="G-XXXXXXXXXX">
                            </div>
                            <div>
                                <label for="naver_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">네이버 애널리틱스 ID</label>
                                <input type="text" name="naver_analytics_id" id="naver_analytics_id"
                                       value="{{ old('naver_analytics_id', $settings['naver_analytics_id']) }}"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                       placeholder="네이버 사이트 ID">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="mt-6 max-w-2xl flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors">
                설정 저장
            </button>
        </div>
    </form>
@endsection
