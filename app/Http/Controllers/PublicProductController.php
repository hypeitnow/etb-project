<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'variantSizes'])
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        $categories = Category::whereHas('products', fn ($query) => $query->where('is_published', true))
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_published, 404);

        $product->load(['category', 'variantSizes' => fn ($query) => $query->orderBy('size_label')]);

        return view('products.show', compact('product'));
    }
}
