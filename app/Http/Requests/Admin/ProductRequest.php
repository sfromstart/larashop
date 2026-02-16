<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_new' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],
            'primary_image_index' => ['nullable', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required_with:options', 'string', 'max:100'],
            'options.*.values' => ['required_with:options', 'array', 'min:1'],
            'options.*.values.*.value' => ['required', 'string', 'max:100'],
            'options.*.values.*.price_modifier' => ['nullable', 'numeric'],
            'options.*.values.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => '카테고리를 선택해주세요.',
            'name.required' => '상품명을 입력해주세요.',
            'slug.required' => '슬러그를 입력해주세요.',
            'slug.unique' => '이미 사용 중인 슬러그입니다.',
            'price.required' => '판매가를 입력해주세요.',
            'price.min' => '판매가는 0 이상이어야 합니다.',
            'stock_quantity.required' => '재고수량을 입력해주세요.',
            'images.max' => '이미지는 최대 10개까지 업로드 가능합니다.',
            'images.*.image' => '이미지 파일만 업로드 가능합니다.',
            'images.*.max' => '이미지 크기는 5MB 이하만 가능합니다.',
        ];
    }
}
