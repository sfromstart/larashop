@extends('layouts.admin')

@section('title', '상품 수정')
@section('page-title', '상품 수정')

@section('content')
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
          x-data="productEditForm()" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column (2 cols) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">기본 정보</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">상품명 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                   x-model="name"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">슬러그 <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" required
                                   x-model="slug"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">짧은 설명</label>
                            <textarea name="short_description" id="short_description" rows="2"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">상품 상세설명</label>
                            <textarea name="description" id="description" rows="8"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Existing Images --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">상품 이미지</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Existing images --}}
                        @if($product->images->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">현재 이미지</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($product->images as $image)
                                        <div class="relative group rounded-lg overflow-hidden border-2 transition-colors
                                                    {{ $image->is_primary ? 'border-blue-500' : 'border-gray-200' }}">
                                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text }}"
                                                 class="w-full h-32 object-cover">
                                            @if($image->is_primary)
                                                <span class="absolute top-1 left-1 px-1.5 py-0.5 text-xs font-medium bg-blue-500 text-white rounded">대표</span>
                                            @endif
                                            <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 p-1.5 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" name="primary_image_id" value="{{ $image->id }}"
                                                           {{ $image->is_primary ? 'checked' : '' }}
                                                           class="h-3 w-3 text-blue-600">
                                                    <span class="ml-1 text-xs text-white">대표</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"
                                                           class="h-3 w-3 text-red-600 rounded">
                                                    <span class="ml-1 text-xs text-white">삭제</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- New images --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">새 이미지 추가</label>
                            <input type="file" name="images[]" id="images" accept="image/*" multiple
                                   @change="handleImageUpload($event)"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100">
                            <p class="mt-1 text-xs text-gray-500">최대 10개 이미지, 각 5MB 이하</p>
                        </div>

                        {{-- New image preview --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-show="newImagePreview.length > 0">
                            <template x-for="(img, index) in newImagePreview" :key="index">
                                <div class="relative rounded-lg overflow-hidden border-2 border-gray-200">
                                    <img :src="img" alt="미리보기" class="w-full h-32 object-cover">
                                    <span class="absolute top-1 left-1 px-1.5 py-0.5 text-xs font-medium bg-green-500 text-white rounded">신규</span>
                                </div>
                            </template>
                        </div>

                        @error('images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Options --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900">상품 옵션</h2>
                        <button type="button" @click="addOption()"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            옵션 추가
                        </button>
                    </div>
                    <div class="p-6">
                        <template x-if="options.length === 0">
                            <p class="text-sm text-gray-500 text-center py-4">등록된 옵션이 없습니다.</p>
                        </template>

                        <div class="space-y-6">
                            <template x-for="(option, optIndex) in options" :key="optIndex">
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex-1 mr-3">
                                            <input type="text"
                                                   :name="'options['+optIndex+'][name]'"
                                                   x-model="option.name"
                                                   placeholder="옵션명 (예: 색상, 사이즈)"
                                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                        </div>
                                        <button type="button" @click="removeOption(optIndex)"
                                                class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="space-y-2 ml-4">
                                        <template x-for="(val, valIndex) in option.values" :key="valIndex">
                                            <div class="flex items-center gap-2">
                                                <input type="text"
                                                       :name="'options['+optIndex+'][values]['+valIndex+'][value]'"
                                                       x-model="val.value"
                                                       placeholder="값 (예: 빨강)"
                                                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                                <input type="number"
                                                       :name="'options['+optIndex+'][values]['+valIndex+'][price_modifier]'"
                                                       x-model="val.price_modifier"
                                                       placeholder="추가금액"
                                                       class="w-28 rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                                <input type="number"
                                                       :name="'options['+optIndex+'][values]['+valIndex+'][stock_quantity]'"
                                                       x-model="val.stock_quantity"
                                                       placeholder="재고"
                                                       class="w-24 rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                                                <button type="button" @click="removeOptionValue(optIndex, valIndex)"
                                                        class="text-gray-400 hover:text-red-500 p-1 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="addOptionValue(optIndex)"
                                                class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mt-1">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            옵션값 추가
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">SEO 설정</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">메타 타이틀</label>
                            <input type="text" name="meta_title" id="meta_title"
                                   value="{{ old('meta_title', $product->meta_title) }}"
                                   x-model="metaTitle"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">메타 설명</label>
                            <textarea name="meta_description" id="meta_description" rows="2"
                                      x-model="metaDescription"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>

                        {{-- SEO Preview --}}
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2 font-medium">검색엔진 미리보기</p>
                            <p class="text-blue-700 text-base font-medium truncate" x-text="metaTitle || name || '페이지 제목'"></p>
                            <p class="text-green-700 text-xs mt-0.5" x-text="'{{ url('/products') }}/' + (slug || 'slug')"></p>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2" x-text="metaDescription || '메타 설명이 여기에 표시됩니다.'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (1 col) --}}
            <div class="space-y-6">

                {{-- Publish --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">발행</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">활성화 (판매중)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                   class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700">추천 상품</label>
                        </div>
                        <div class="flex items-center">
                            <input type="hidden" name="is_new" value="0">
                            <input type="checkbox" name="is_new" id="is_new" value="1"
                                   {{ old('is_new', $product->is_new) ? 'checked' : '' }}
                                   class="h-4 w-4 text-slate-600 focus:ring-slate-500 border-gray-300 rounded">
                            <label for="is_new" class="ml-2 text-sm text-gray-700">신상품</label>
                        </div>
                        <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                            <p>조회수: {{ number_format($product->view_count) }}</p>
                            <p>판매수: {{ number_format($product->sold_count) }}</p>
                            <p>등록일: {{ $product->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                        <button type="submit"
                                class="w-full px-4 py-2.5 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors">
                            상품 수정
                        </button>
                    </div>
                </div>

                {{-- Category --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">카테고리 <span class="text-red-500">*</span></h2>
                    </div>
                    <div class="p-6">
                        <select name="category_id" id="category_id" required
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            <option value="">카테고리 선택</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;-- {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">가격</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">판매가 <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="compare_price" class="block text-sm font-medium text-gray-700 mb-1">정가 (할인 전)</label>
                            <div class="relative">
                                <input type="number" name="compare_price" id="compare_price" value="{{ old('compare_price', $product->compare_price) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">원</span>
                            </div>
                            @error('compare_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Inventory --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">재고</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">재고 수량 <span class="text-red-500">*</span></label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                            @error('stock_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">재고 부족 기준</label>
                            <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">무게 (g)</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" min="0"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function productEditForm() {
            return {
                name: @json(old('name', $product->name)),
                slug: @json(old('slug', $product->slug)),
                metaTitle: @json(old('meta_title', $product->meta_title ?? '')),
                metaDescription: @json(old('meta_description', $product->meta_description ?? '')),
                newImagePreview: [],
                options: @json($product->options->map(function ($opt) {
                    return [
                        'name' => $opt->name,
                        'values' => $opt->values->map(function ($val) {
                            return [
                                'value' => $val->value,
                                'price_modifier' => $val->price_modifier,
                                'stock_quantity' => $val->stock_quantity,
                            ];
                        })->toArray(),
                    ];
                })->toArray()),

                handleImageUpload(event) {
                    this.newImagePreview = [];
                    const files = event.target.files;
                    for (let i = 0; i < files.length; i++) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.newImagePreview.push(e.target.result);
                        };
                        reader.readAsDataURL(files[i]);
                    }
                },

                addOption() {
                    this.options.push({
                        name: '',
                        values: [{ value: '', price_modifier: 0, stock_quantity: null }]
                    });
                },

                removeOption(index) {
                    this.options.splice(index, 1);
                },

                addOptionValue(optIndex) {
                    this.options[optIndex].values.push({ value: '', price_modifier: 0, stock_quantity: null });
                },

                removeOptionValue(optIndex, valIndex) {
                    if (this.options[optIndex].values.length > 1) {
                        this.options[optIndex].values.splice(valIndex, 1);
                    }
                }
            };
        }
    </script>
    @endpush
@endsection
