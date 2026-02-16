<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class SeoService
{
    private string $title = '';
    private string $description = '';
    private string $canonical = '';
    private string $ogImage = '';
    private string $ogType = 'website';
    private string $robots = 'index, follow';
    private array $jsonLd = [];

    // ── Chaining Methods ──

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = mb_substr(strip_tags($description), 0, 160);
        return $this;
    }

    public function setCanonical(string $canonical): static
    {
        $this->canonical = $canonical;
        return $this;
    }

    public function setOgImage(string $ogImage): static
    {
        $this->ogImage = $ogImage;
        return $this;
    }

    public function setOgType(string $ogType): static
    {
        $this->ogType = $ogType;
        return $this;
    }

    public function setRobots(string $robots): static
    {
        $this->robots = $robots;
        return $this;
    }

    public function addJsonLd(array $schema): static
    {
        $this->jsonLd[] = $schema;
        return $this;
    }

    // ── Shortcut Methods ──

    public function setProduct(Product $product): static
    {
        $siteName = Setting::get('site_name', 'LaraShop');

        $this->setTitle(($product->meta_title ?: $product->name) . ' - ' . $siteName);
        $this->setDescription($product->meta_description ?: ($product->short_description ?: $product->name));
        $this->setCanonical(route('shop.products.show', $product->slug));
        $this->setOgType('product');

        if ($product->primaryImageUrl) {
            $this->setOgImage($product->primaryImageUrl);
        }

        // Product JSON-LD
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?: $product->name,
            'sku' => $product->sku ?: '',
            'brand' => [
                '@type' => 'Brand',
                'name' => $siteName,
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('shop.products.show', $product->slug),
                'priceCurrency' => 'KRW',
                'price' => (string) $product->price,
                'availability' => $product->stock_quantity > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                ],
            ],
        ];

        // Images array
        $images = [];
        if ($product->relationLoaded('images') && $product->images->count() > 0) {
            foreach ($product->images as $image) {
                $images[] = $image->path;
            }
        } elseif ($product->primaryImageUrl) {
            $images[] = $product->primaryImageUrl;
        }
        if (!empty($images)) {
            $schema['image'] = $images;
        }

        // Aggregate rating
        $reviewCount = $product->approvedReviews()->count();
        if ($reviewCount > 0) {
            $avgRating = round($product->approvedReviews()->avg('rating') ?? 0, 1);
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $avgRating,
                'reviewCount' => (string) $reviewCount,
                'bestRating' => '5',
                'worstRating' => '1',
            ];

            // Individual reviews (up to 5)
            $reviews = $product->approvedReviews()->with('user')->latest()->take(5)->get();
            if ($reviews->count() > 0) {
                $schema['review'] = $reviews->map(function ($review) {
                    return [
                        '@type' => 'Review',
                        'author' => [
                            '@type' => 'Person',
                            'name' => $review->user->name ?? '익명',
                        ],
                        'datePublished' => $review->created_at->toIso8601String(),
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (string) $review->rating,
                            'bestRating' => '5',
                            'worstRating' => '1',
                        ],
                        'reviewBody' => $review->content,
                    ];
                })->toArray();
            }
        }

        $this->addJsonLd($schema);

        return $this;
    }

    public function setCategory(Category $category): static
    {
        $siteName = Setting::get('site_name', 'LaraShop');

        $this->setTitle(($category->meta_title ?: $category->name) . ' - ' . $siteName);
        $this->setDescription($category->meta_description ?: ($category->description ?: $category->name . ' 카테고리 상품 목록'));
        $this->setCanonical(route('shop.products.category', $category->slug));

        if ($category->image) {
            $this->setOgImage($category->image);
        }

        // ItemList JSON-LD
        $products = $category->products()->active()->with('primaryImage')->take(10)->get();
        $listItems = [];
        foreach ($products as $index => $product) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => route('shop.products.show', $product->slug),
                'name' => $product->name,
            ];
        }

        $this->addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $category->name,
            'description' => $category->description ?: $category->name . ' 카테고리',
            'numberOfItems' => count($listItems),
            'itemListElement' => $listItems,
        ]);

        return $this;
    }

    public function setHome(): static
    {
        $siteName = Setting::get('site_name', 'LaraShop');
        $siteDescription = Setting::get('site_description', '최고의 온라인 쇼핑몰');
        $siteUrl = config('app.url');

        $this->setTitle($siteName . ' - ' . $siteDescription);
        $this->setDescription($siteDescription);
        $this->setCanonical($siteUrl);

        // Organization
        $this->addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'description' => $siteDescription,
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => Setting::get('cs_phone', '1588-0000'),
                'contactType' => 'customer service',
                'availableLanguage' => 'Korean',
            ],
        ]);

        // WebSite + SearchAction
        $this->addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('shop.products.search') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ]);

        return $this;
    }

    public function setProductList(?string $title = null): static
    {
        $siteName = Setting::get('site_name', 'LaraShop');

        $this->setTitle(($title ?: '전체상품') . ' - ' . $siteName);
        $this->setDescription(($title ?: '전체상품') . ' - ' . $siteName . '에서 다양한 상품을 만나보세요.');
        $this->setCanonical(route('shop.products.index'));

        return $this;
    }

    // ── Output Methods ──

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'ogImage' => $this->ogImage,
            'ogType' => $this->ogType,
            'robots' => $this->robots,
            'jsonLd' => $this->jsonLd,
        ];
    }

    public function render(): array
    {
        return $this->toArray();
    }
}
