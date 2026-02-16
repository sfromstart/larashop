<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'latest';

    #[Url]
    public int $minPrice = 0;

    #[Url]
    public int $maxPrice = 0;

    #[Url]
    public string $viewMode = 'grid';

    #[Url]
    public int $perPage = 12;

    public ?string $categorySlug = null;

    public function mount(?string $categorySlug = null, ?string $searchQuery = null): void
    {
        $this->categorySlug = $categorySlug;

        if ($searchQuery) {
            $this->search = $searchQuery;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function updatingMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatingMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'sort', 'minPrice', 'maxPrice']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()
            ->with(['primaryImage', 'category', 'approvedReviews']);

        // 카테고리 필터
        if ($this->categorySlug) {
            $category = Category::where('slug', $this->categorySlug)->first();
            if ($category) {
                $childIds = $category->children()->pluck('id')->toArray();
                $categoryIds = array_merge([$category->id], $childIds);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 검색 필터
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('short_description', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // 가격 필터
        if ($this->minPrice > 0) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice > 0) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // 정렬
        $query = match ($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            'popular' => $query->orderByDesc('sold_count'),
            'rating' => $query->withAvg('approvedReviews', 'rating')->orderByDesc('approved_reviews_avg_rating'),
            default => $query->latest(),
        };

        $products = $query->paginate($this->perPage);

        // 사이드바용 카테고리 목록
        $categories = Category::active()
            ->root()
            ->orderBy('sort_order')
            ->withCount(['products' => fn ($q) => $q->active()])
            ->with(['children' => fn ($q) => $q->active()->withCount(['products' => fn ($q2) => $q2->active()])])
            ->get();

        return view('livewire.shop.product-filter', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
