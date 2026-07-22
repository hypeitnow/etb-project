<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariantSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('products', 'public');
            }
            $product->update(['images' => $paths]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Produkt został dodany.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $variants = $product->variantSizes()->orderBy('size_label')->get();

        return view('admin.products.edit', compact('product', 'categories', 'variants'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        if ($request->hasFile('images')) {
            $paths = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('products', 'public');
            }
            $product->update(['images' => $paths]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Produkt został zaktualizowany.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produkt został usunięty.');
    }

    public function addVariant(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'size_label' => ['required', 'string', 'max:50'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'extra_price_grosze' => ['required', 'integer', 'min:0'],
        ]);

        $product->variantSizes()->create($validated);

        return back()->with('success', 'Rozmiar został dodany.');
    }

    public function removeVariant(Product $product, ProductVariantSize $variant): RedirectResponse
    {
        $variant->delete();

        return back()->with('success', 'Rozmiar został usunięty.');
    }
}
