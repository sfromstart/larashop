<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected static array $koreanCategories = [
        '의류', '신발', '가방', '액세서리', '전자제품',
        '생활용품', '식품', '뷰티', '스포츠', '도서',
        '가구', '주방용품', '완구', '반려동물', '자동차용품',
    ];

    protected static int $categoryIndex = 0;

    public function definition(): array
    {
        $name = self::$koreanCategories[self::$categoryIndex % count(self::$koreanCategories)] ?? fake()->word();
        self::$categoryIndex++;

        return [
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => fake()->sentence(),
            'image' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'meta_title' => $name,
            'meta_description' => fake()->sentence(),
        ];
    }

    public function child(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
