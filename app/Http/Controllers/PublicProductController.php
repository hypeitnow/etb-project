<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        $categories = Category::has('products')->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_published, 404);

        return view('products.show', compact('product'));
    }
}
