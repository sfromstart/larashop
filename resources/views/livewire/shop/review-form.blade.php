<div>
    @auth
        @if($submitted)
            {{-- 제출 완료 --}}
            <div class="bg-green-50 rounded-xl border border-green-200 p-6 text-center">
                <svg class="mx-auto w-12 h-12 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-green-800 mb-1">리뷰가 등록되었습니다!</h3>
                <p class="text-sm text-green-600">관리자 승인 후 리뷰가 표시됩니다.</p>
            </div>
        @else
            {{-- 리뷰 작성 폼 --}}
            <div class="border-t border-gray-200 pt-8 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">리뷰 작성</h3>

                <form wire:submit="submit" class="space-y-5">
                    {{-- 별점 선택 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">별점</label>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="setRating({{ $i }})"
                                        class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-8 h-8 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-200' }} transition-colors cursor-pointer"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <span class="ml-2 text-sm text-gray-500">{{ $rating }}점</span>
                        </div>
                        @error('rating')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- 제목 --}}
                    <div>
                        <label for="review-title" class="block text-sm font-medium text-gray-700 mb-2">제목 <span class="text-gray-400">(선택)</span></label>
                        <input type="text" wire:model="title" id="review-title"
                               placeholder="리뷰 제목을 입력하세요"
                               maxlength="100"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- 내용 --}}
                    <div>
                        <label for="review-content" class="block text-sm font-medium text-gray-700 mb-2">내용 <span class="text-red-500">*</span></label>
                        <textarea wire:model="content" id="review-content"
                                  rows="4"
                                  placeholder="상품에 대한 솔직한 리뷰를 작성해주세요. (최소 10자)"
                                  maxlength="2000"
                                  class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
                        <div class="flex items-center justify-between mt-1">
                            @error('content')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                            <span class="text-xs text-gray-400 ml-auto">{{ mb_strlen($content) }}/2000</span>
                        </div>
                    </div>

                    {{-- 등록 버튼 --}}
                    <div class="flex justify-end">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition disabled:opacity-50 flex items-center space-x-2">
                            <svg wire:loading wire:target="submit" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>리뷰 등록</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @else
        {{-- 비로그인 시 --}}
        <div class="border-t border-gray-200 pt-8 mt-8 text-center">
            <p class="text-gray-500 mb-4">리뷰를 작성하려면 로그인이 필요합니다.</p>
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                로그인하기
            </a>
        </div>
    @endauth
</div>
