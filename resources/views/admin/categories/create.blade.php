@extends('layouts.admin')

@section('title', '카테고리 등록')
@section('page-title', '카테고리 등록')

@section('content')
    <div class="max-w-2xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">카테고리 정보</h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Parent Category --}}
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">상위 카테고리</label>
                        <select name="parent_id" id="parent_id"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            <option value="">최상위 카테고리</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">카테고리명 <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="카테고리명을 입력하세요">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">슬러그 <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                               placeholder="url-friendly-name">
                        <p class="mt-1 text-xs text-gray-500">URL에 사용됩니다. 영문 소문자, 숫자, 대시(-)만 사용하세요.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">설명</label>
                        <textarea name="description" id="description" rows="3"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                  placeholder="카테고리 설명을 입력하세요">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Image --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">카테고리 이미지</label>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100">
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">정렬 순서</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="block w-32 rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

                {{-- SEO Section --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">SEO 설정</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">메타 타이틀</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                   placeholder="검색엔진에 표시될 제목">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">메타 설명</label>
                            <textarea name="meta_description" id="meta_description" rows="2"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm"
                                      placeholder="검색엔진에 표시될 설명">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}"
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
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9가-힣\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            const slugField = document.getElementById('slug');
            if (!slugField.dataset.manual) {
                slugField.value = slug;
            }
        });
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    </script>
    @endpush
@endsection
