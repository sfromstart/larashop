<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Status filter
        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        // Stock filter
        if ($request->input('stock') === 'in_stock') {
            $query->where('stock_quantity', '>', 0);
        } elseif ($request->input('stock') === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($request->input('stock') === 'low_stock') {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                  ->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSorts = ['name', 'price', 'stock_quantity', 'sold_count', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('children')
            ->root()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['is_active'] = $request->boolean('is_active');
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_new'] = $request->boolean('is_new');

            unset($data['images'], $data['primary_image_index'], $data['options']);

            $product = Product::create($data);

            // Handle images
            if ($request->hasFile('images')) {
                $primaryIndex = (int) $request->input('primary_image_index', 0);
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'alt_text' => $product->name,
                        'sort_order' => $index,
                        'is_primary' => $index === $primaryIndex,
                    ]);
                }
            }

            // Handle options
            if ($request->input('options')) {
                foreach ($request->input('options') as $sortOrder => $optionData) {
                    if (empty($optionData['name'])) continue;

                    $option = ProductOption::create([
                        'product_id' => $product->id,
                        'name' => $optionData['name'],
                        'sort_order' => $sortOrder,
                    ]);

                    if (!empty($optionData['values'])) {
                        foreach ($optionData['values'] as $valueOrder => $valueData) {
                            if (empty($valueData['value'])) continue;

                            ProductOptionValue::create([
                                'product_option_id' => $option->id,
                                'value' => $valueData['value'],
                                'price_modifier' => $valueData['price_modifier'] ?? 0,
                                'stock_quantity' => $valueData['stock_quantity'] ?? null,
                                'sort_order' => $valueOrder,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', '상품이 등록되었습니다.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'options.values']);

        $categories = Category::with('children')
            ->root()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $data = $request->validated();
            $data['is_active'] = $request->boolean('is_active');
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_new'] = $request->boolean('is_new');

            unset($data['images'], $data['primary_image_index'], $data['options']);

            $product->update($data);

            // Handle deleted images
            if ($deletedImages = $request->input('delete_images', [])) {
                foreach ($deletedImages as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id === $product->id) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    }
                }
            }

            // Handle new images
            if ($request->hasFile('images')) {
                $maxSort = $product->images()->max('sort_order') ?? -1;
                $primaryIndex = $request->input('primary_image_index');

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'alt_text' => $product->name,
                        'sort_order' => $maxSort + $index + 1,
                        'is_primary' => false,
                    ]);
                }
            }

            // Handle primary image selection
            if ($primaryImageId = $request->input('primary_image_id')) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $primaryImageId)->update(['is_primary' => true]);
            }

            // Handle options - remove existing and recreate
            foreach ($product->options as $option) {
                $option->values()->delete();
                $option->delete();
            }

            if ($request->input('options')) {
                foreach ($request->input('options') as $sortOrder => $optionData) {
                    if (empty($optionData['name'])) continue;

                    $option = ProductOption::create([
                        'product_id' => $product->id,
                        'name' => $optionData['name'],
                        'sort_order' => $sortOrder,
                    ]);

                    if (!empty($optionData['values'])) {
                        foreach ($optionData['values'] as $valueOrder => $valueData) {
                            if (empty($valueData['value'])) continue;

                            ProductOptionValue::create([
                                'product_option_id' => $option->id,
                                'value' => $valueData['value'],
                                'price_modifier' => $valueData['price_modifier'] ?? 0,
                                'stock_quantity' => $valueData['stock_quantity'] ?? null,
                                'sort_order' => $valueOrder,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', '상품이 수정되었습니다.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            // Delete images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            // Delete options and values
            foreach ($product->options as $option) {
                $option->values()->delete();
                $option->delete();
            }

            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', '상품이 삭제되었습니다.');
    }
}
