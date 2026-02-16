<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Seo extends Component
{
    public string $title;
    public string $description;
    public string $canonical;
    public string $ogImage;
    public string $ogType;
    public string $robots;
    public array $jsonLd;

    public function __construct(?array $seo = null)
    {
        $siteName = \App\Models\Setting::get('site_name', 'LaraShop');

        $this->title = $seo['title'] ?? $siteName;
        $this->description = $seo['description'] ?? \App\Models\Setting::get('site_description', 'LaraShop - 최고의 온라인 쇼핑몰');
        $this->canonical = $seo['canonical'] ?? '';
        $this->ogImage = $seo['ogImage'] ?? '';
        $this->ogType = $seo['ogType'] ?? 'website';
        $this->robots = $seo['robots'] ?? 'index, follow';
        $this->jsonLd = $seo['jsonLd'] ?? [];
    }

    public function render(): View|Closure|string
    {
        return view('components.seo');
    }
}
