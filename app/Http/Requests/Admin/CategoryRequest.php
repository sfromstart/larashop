<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '카테고리명을 입력해주세요.',
            'slug.required' => '슬러그를 입력해주세요.',
            'slug.unique' => '이미 사용 중인 슬러그입니다.',
            'slug.alpha_dash' => '슬러그는 영문, 숫자, 대시(-), 밑줄(_)만 사용 가능합니다.',
            'image.image' => '이미지 파일만 업로드 가능합니다.',
            'image.max' => '이미지 크기는 2MB 이하만 가능합니다.',
        ];
    }
}
