@extends('layouts.shop')

@section('title', '주문하기')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-12 uppercase tracking-wide text-center">주문하기</h1>

    <div class="max-w-6xl mx-auto">
        <form action="{{ route('shop.checkout.store') }}" method="POST" x-data="checkoutForm()" @submit.prevent="submitForm">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-16">
                {{-- 좌측: 배송 정보 --}}
                <div class="flex-1">
                    <div class="mb-10">
                        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6 pb-2 border-b border-gray-200">배송 정보</h2>
                        
                        {{-- 저장된 주소 선택 --}}
                        @if($addresses->count() > 0)
                            <div class="mb-8">
                                <label class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">저장된 주소</label>
                                <div class="relative">
                                    <select x-model="selectedAddressId" @change="selectAddress()" class="w-full appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition cursor-pointer">
                                        <option value="">직접 입력</option>
                                        @foreach($addresses as $addr)
                                            <option value="{{ $addr->id }}"
                                                    data-name="{{ $addr->recipient_name }}"
                                                    data-phone="{{ $addr->phone }}"
                                                    data-postal="{{ $addr->postal_code }}"
                                                    data-address="{{ $addr->address }}"
                                                    data-detail="{{ $addr->address_detail }}"
                                                    {{ $defaultAddress && $defaultAddress->id === $addr->id ? 'selected' : '' }}>
                                                {{ $addr->label ?: $addr->recipient_name }} - {{ $addr->full_address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            {{-- 수취인 --}}
                            <div>
                                <label for="shipping_name" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">수취인 <span class="text-red-500">*</span></label>
                                <input type="text" name="shipping_name" id="shipping_name" x-model="form.shipping_name"
                                       value="{{ old('shipping_name', $defaultAddress?->recipient_name ?? auth()->user()->name) }}"
                                       class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-3 px-4 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition" required>
                                @error('shipping_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- 연락처 --}}
                            <div>
                                <label for="shipping_phone" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">연락처 <span class="text-red-500">*</span></label>
                                <input type="tel" name="shipping_phone" id="shipping_phone" x-model="form.shipping_phone"
                                       value="{{ old('shipping_phone', $defaultAddress?->phone ?? auth()->user()->phone) }}"
                                       placeholder="010-0000-0000"
                                       class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-3 px-4 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition" required>
                                @error('shipping_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- 우편번호 --}}
                            <div>
                                <label for="shipping_postal_code" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">우편번호 <span class="text-red-500">*</span></label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" x-model="form.shipping_postal_code"
                                       value="{{ old('shipping_postal_code', $defaultAddress?->postal_code) }}"
                                       class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-3 px-4 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition" required>
                                @error('shipping_postal_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- 주소 --}}
                            <div class="md:col-span-2">
                                <label for="shipping_address" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">주소 <span class="text-red-500">*</span></label>
                                <input type="text" name="shipping_address" id="shipping_address" x-model="form.shipping_address"
                                       value="{{ old('shipping_address', $defaultAddress?->address) }}"
                                       class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-3 px-4 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition" required>
                                @error('shipping_address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- 상세주소 --}}
                            <div class="md:col-span-2">
                                <label for="shipping_address_detail" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">상세주소</label>
                                <input type="text" name="shipping_address_detail" id="shipping_address_detail" x-model="form.shipping_address_detail"
                                       value="{{ old('shipping_address_detail', $defaultAddress?->address_detail) }}"
                                       class="w-full bg-gray-50 border border-gray-200 text-gray-900 py-3 px-4 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition">
                                @error('shipping_address_detail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- 배송 메모 --}}
                            <div class="md:col-span-2">
                                <label for="shipping_memo" class="block text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">배송 메모</label>
                                <div class="relative">
                                    <select name="shipping_memo" id="shipping_memo" x-model="form.shipping_memo" class="w-full appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-none focus:outline-none focus:bg-white focus:border-black focus:ring-0 transition cursor-pointer">
                                        <option value="">배송 메모를 선택하세요</option>
                                        <option value="문 앞에 놓아주세요">문 앞에 놓아주세요</option>
                                        <option value="경비실에 맡겨주세요">경비실에 맡겨주세요</option>
                                        <option value="택배함에 넣어주세요">택배함에 넣어주세요</option>
                                        <option value="배송 전 연락 부탁드립니다">배송 전 연락 부탁드립니다</option>
                                        <option value="부재 시 연락 부탁드립니다">부재 시 연락 부탁드립니다</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 결제 방법 --}}
                    <div class="mb-10">
                        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6 pb-2 border-b border-gray-200">결제 방법</h2>
                        <div class="space-y-4">
                            <label class="flex items-center p-4 border border-black bg-gray-50 cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" checked class="text-black focus:ring-0 bg-white border-gray-400">
                                <div class="ml-4">
                                    <span class="text-sm font-bold text-gray-900 uppercase tracking-wide block">무통장 입금</span>
                                    <p class="text-xs text-gray-500 mt-1">주문 후 입금 안내를 확인해 주세요.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 우측: 주문 요약 --}}
                <div class="lg:w-96">
                    <div class="bg-gray-50 p-8 rounded-none sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6 pb-2 border-b border-gray-200">주문 요약</h2>

                        {{-- 주문 상품 목록 --}}
                        <div class="space-y-4 mb-8">
                            @foreach($cart->items as $item)
                                <div class="flex gap-4">
                                    <div class="w-16 h-20 bg-gray-200 flex-shrink-0">
                                        @if($item->product && $item->product->primaryImageUrl)
                                            <img src="{{ $item->product->primaryImageUrl }}" alt="{{ $item->product->name ?? '' }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name ?? '상품' }}</p>
                                        @if($item->option_values)
                                            <p class="text-xs text-gray-500 mb-1">
                                                @foreach($item->option_values as $optId => $valId)
                                                    @if(isset($optionValueMap[$valId])){{ $optionValueMap[$valId]->value }}{{ !$loop->last ? ', ' : '' }}@endif
                                                @endforeach
                                            </p>
                                        @endif
                                        <div class="flex justify-between items-center mt-1">
                                            <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ number_format($item->unit_price) }}</p>
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($item->subtotal) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- 금액 합계 --}}
                        <div class="space-y-3 mb-8 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide text-xs">소계</span>
                                <span class="font-medium text-gray-900">{{ number_format($subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide text-xs">배송비</span>
                                <span class="font-medium text-gray-900">{{ $shippingFee > 0 ? number_format($shippingFee) : '무료' }}</span>
                            </div>
                            <div class="pt-4 mt-4 border-t border-gray-200 flex justify-between items-end">
                                <span class="font-bold text-gray-900 uppercase tracking-widest">합계</span>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($subtotal + $shippingFee) }}</span>
                            </div>
                        </div>

                        {{-- 동의 체크 + 주문 버튼 --}}
                        <div class="space-y-4">
                            <label class="flex items-start space-x-3 cursor-pointer group">
                                <input type="checkbox" name="agree" x-model="agreed" class="mt-0.5 rounded-none border-gray-300 text-black focus:ring-0">
                                <span class="text-xs text-gray-500 group-hover:text-gray-900 transition leading-tight">이용약관 및 개인정보 처리방침에 동의합니다 <span class="text-red-500">*</span></span>
                            </label>
                            @error('agree')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                            <button type="submit"
                                    :disabled="!agreed || submitting"
                                    class="w-full py-4 bg-black text-white text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                                <template x-if="!submitting">
                                    <span>주문하기</span>
                                </template>
                                <template x-if="submitting">
                                    <span class="flex items-center space-x-2">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span>처리 중...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function checkoutForm() {
        return {
            agreed: false,
            submitting: false,
            couponCode: '',
            selectedAddressId: '{{ $defaultAddress?->id ?? '' }}',
            form: {
                shipping_name: '{{ old('shipping_name', $defaultAddress?->recipient_name ?? auth()->user()->name) }}',
                shipping_phone: '{{ old('shipping_phone', $defaultAddress?->phone ?? auth()->user()->phone ?? '') }}',
                shipping_postal_code: '{{ old('shipping_postal_code', $defaultAddress?->postal_code ?? '') }}',
                shipping_address: '{{ old('shipping_address', $defaultAddress?->address ?? '') }}',
                shipping_address_detail: '{{ old('shipping_address_detail', $defaultAddress?->address_detail ?? '') }}',
                shipping_memo: '{{ old('shipping_memo', '') }}',
            },
            selectAddress() {
                const select = document.querySelector('select[x-model="selectedAddressId"]');
                const option = select.selectedOptions[0];
                if (this.selectedAddressId && option) {
                    this.form.shipping_name = option.dataset.name || '';
                    this.form.shipping_phone = option.dataset.phone || '';
                    this.form.shipping_postal_code = option.dataset.postal || '';
                    this.form.shipping_address = option.dataset.address || '';
                    this.form.shipping_address_detail = option.dataset.detail || '';
                }
            },
            submitForm() {
                if (!this.agreed) return;
                this.submitting = true;
                this.$el.submit();
            }
        }
    }
</script>
@endpush
@endsection
