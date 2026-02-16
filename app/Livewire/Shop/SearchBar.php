<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';
    public bool $showResults = false;

    public function updatedQuery(): void
    {
        $this->showResults = mb_strlen($this->query) >= 2;
    }

    public function selectProduct(string $slug): void
    {
        $this->redirect(route('shop.products.show', $slug), navigate: false);
    }

    public function goToSearch(): void
    {
        if ($this->query) {
            $this->redirect(route('shop.products.search', ['q' => $this->query]), navigate: false);
        }
    }

    public function render()
    {
        $results = [];

        if (mb_strlen($this->query) >= 2) {
            $results = Product::active()
                ->where('name', 'like', "%{$this->query}%")
                ->with('primaryImage')
                ->take(5)
                ->get();
        }

        return view('livewire.shop.search-bar', [
            'results' => $results,
        ]);
    }
}
