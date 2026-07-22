<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariantSize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->category = Category::factory()->create();
});

it('creates a product with factory', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $this->assertDatabaseHas('products', ['id' => $product->id]);
    expect($product->slug)->not->toBeEmpty();
});

it('auto-generates slug on create', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product Name',
        'slug' => '',
        'category_id' => $this->category->id,
    ]);

    expect($product->slug)->toBe('test-product-name');
});

it('belongs to a category', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    expect($product->category)->toBeInstanceOf(Category::class);
    expect($product->category->id)->toBe($this->category->id);
});

it('has variant sizes', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $variant = ProductVariantSize::factory()->create(['product_id' => $product->id]);

    expect($product->variantSizes)->toHaveCount(1);
    expect($variant->product->id)->toBe($product->id);
});

it('displays price in PLN format', function () {
    $product = Product::factory()->create([
        'price_grosze' => 2999,
        'category_id' => $this->category->id,
    ]);

    expect($product->displayPrice())->toBe('29,99 zł');
});

it('admin can create product via API', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.products.store'), [
            'name' => 'New Product',
            'price_grosze' => 4999,
            'category_id' => $this->category->id,
            'stock_qty' => 10,
            'is_physical' => true,
            'is_published' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('products', ['name' => 'New Product']);
});

it('guest cannot access product admin', function () {
    $response = $this->get(route('admin.products.create'));
    $response->assertRedirect(route('login'));
});
