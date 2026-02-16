<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::active()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $categories = Category::active()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        return response()
            ->view('sitemap.index', compact('products', 'categories'))
            ->header('Content-Type', 'text/xml');
    }
}
