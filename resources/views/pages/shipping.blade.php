@extends('layouts.shop')

@section('title', '배송안내')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <x-shop.breadcrumb :items="[
        ['label' => '배송안내'],
    ]" />

    <h1 class="text-3xl font-bold text-gray-900 mb-8">배송안내</h1>

    <div class="space-y-8">
        {{-- 배송 정보 카드 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">무료배송</h3>
                    <p class="text-sm text-gray-500">50,000원 이상 구매 시<br>무료배송</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">빠른배송</h3>
                    <p class="text-sm text-gray-500">평일 오후 2시 이전 결제 시<br>당일 발송</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">안전포장</h3>
                    <p class="text-sm text-gray-500">꼼꼼한 포장으로<br>안전하게 배송</p>
                </div>
            </div>
        </div>

        {{-- 상세 안내 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 prose prose-gray max-w-none">
            <h2>배송 방법 및 비용</h2>
            <table>
                <thead>
                    <tr>
                        <th>구분</th>
                        <th>내용</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>배송업체</td>
                        <td>CJ대한통운, 로젠택배</td>
                    </tr>
                    <tr>
                        <td>배송비</td>
                        <td>기본 3,000원 (50,000원 이상 구매 시 무료)</td>
                    </tr>
                    <tr>
                        <td>제주/도서산간</td>
                        <td>추가 배송비 3,000원</td>
                    </tr>
                    <tr>
                        <td>배송기간</td>
                        <td>결제 완료 후 1~3 영업일 (주말/공휴일 제외)</td>
                    </tr>
                </tbody>
            </table>

            <h2>배송 처리 기준</h2>
            <ul>
                <li>평일(월~금) 오후 2시 이전 결제 완료 건: 당일 발송</li>
                <li>평일(월~금) 오후 2시 이후 결제 완료 건: 다음 영업일 발송</li>
                <li>토/일/공휴일 결제 건: 다음 영업일 발송</li>
                <li>주문제작 상품: 별도 안내된 기간에 따름</li>
            </ul>

            <h2>교환/반품 안내</h2>
            <table>
                <thead>
                    <tr>
                        <th>구분</th>
                        <th>내용</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>교환/반품 신청기간</td>
                        <td>상품 수령 후 7일 이내</td>
                    </tr>
                    <tr>
                        <td>교환/반품 배송비</td>
                        <td>단순변심: 왕복 6,000원 (구매자 부담)<br>상품 불량: 무료 (판매자 부담)</td>
                    </tr>
                    <tr>
                        <td>환불 처리</td>
                        <td>반품 상품 확인 후 3영업일 이내</td>
                    </tr>
                </tbody>
            </table>

            <h2>교환/반품이 불가능한 경우</h2>
            <ul>
                <li>고객님의 책임 있는 사유로 상품이 훼손된 경우</li>
                <li>포장을 개봉하여 사용하거나 일부 소비한 경우</li>
                <li>시간이 경과하여 재판매가 곤란할 정도로 상품의 가치가 감소한 경우</li>
                <li>복제가 가능한 상품의 포장을 훼손한 경우</li>
                <li>주문제작 상품의 경우</li>
            </ul>

            <h2>고객센터</h2>
            <p>배송 관련 문의사항은 고객센터로 연락해 주세요.</p>
            <ul>
                <li>전화: {{ \App\Models\Setting::get('cs_phone', '1588-0000') }}</li>
                <li>이메일: {{ \App\Models\Setting::get('cs_email', 'help@larashop.kr') }}</li>
                <li>운영시간: {{ \App\Models\Setting::get('cs_hours', '평일 09:00 ~ 18:00') }} (점심시간 12:00 ~ 13:00)</li>
            </ul>
        </div>
    </div>
</div>
@endsection
