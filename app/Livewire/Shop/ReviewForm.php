<?php

namespace App\Livewire\Shop;

use App\Models\Review;
use Livewire\Component;

class ReviewForm extends Component
{
    public int $productId;
    public int $rating = 5;
    public string $title = '';
    public string $content = '';
    public bool $submitted = false;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
    }

    public function setRating(int $rating): void
    {
        $this->rating = max(1, min(5, $rating));
    }

    public function submit(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: false);
            return;
        }

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'content.required' => '리뷰 내용을 입력해주세요.',
            'content.min' => '리뷰는 최소 10자 이상 작성해주세요.',
        ]);

        // 중복 리뷰 체크
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if ($existingReview) {
            $this->dispatch('toast', message: '이미 이 상품에 대한 리뷰를 작성하셨습니다.', type: 'error');
            return;
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $this->productId,
            'rating' => $this->rating,
            'title' => $this->title ?: null,
            'content' => $this->content,
            'is_approved' => false,
        ]);

        $this->submitted = true;
        $this->dispatch('toast', message: '리뷰가 등록되었습니다. 관리자 승인 후 표시됩니다.', type: 'success');
    }

    public function render()
    {
        return view('livewire.shop.review-form');
    }
}
