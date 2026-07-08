<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariantSize;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->cartService = app(CartService::class);
    $this->product = Product::factory()->create([
        'price_grosze' => 2999,
        'is_physical' => true,
    ]);
});

it('returns 0 count for guest with empty cart', function () {
    $count = $this->cartService->getItemCount(null);

    expect($count)->toBe(0);
});

it('adds item to guest cart', function () {
    $this->cartService->addItem(null, $this->product->id, null, 2);

    expect($this->cartService->getItemCount(null))->toBe(2);
});

it('adds item to authenticated user cart', function () {
    $user = User::factory()->create();

    $this->cartService->addItem($user, $this->product->id, null, 3);

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $this->product->id,
        'qty' => 3,
    ]);
    expect($this->cartService->getItemCount($user))->toBe(3);
});

it('increments quantity when same item added twice', function () {
    $user = User::factory()->create();

    $this->cartService->addItem($user, $this->product->id, null, 2);
    $this->cartService->addItem($user, $this->product->id, null, 3);

    $items = Cart::where('user_id', $user->id)->first()->items;
    expect($items->first()->qty)->toBe(5);
    expect($items->count())->toBe(1);
});

it('removes item from user cart', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 1);

    $this->cartService->removeItem($user, $this->product->id, null);

    expect($this->cartService->getItemCount($user))->toBe(0);
});

it('updates quantity in user cart', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 2);

    $this->cartService->updateQty($user, $this->product->id, null, 5);

    $items = Cart::where('user_id', $user->id)->first()->items;
    expect($items->first()->qty)->toBe(5);
});

it('removes item when qty set to 0', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 1);

    $this->cartService->updateQty($user, $this->product->id, null, 0);

    expect($this->cartService->getItemCount($user))->toBe(0);
});

it('clears all items from user cart', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 1);

    $this->cartService->clear($user);

    expect($this->cartService->getItemCount($user))->toBe(0);
});

it('merges guest cart into user cart on login', function () {
    $this->cartService->addItem(null, $this->product->id, null, 2);
    $user = User::factory()->create();

    $this->cartService->mergeGuestIntoUser($user);

    expect($this->cartService->getItemCount($user))->toBe(2);
    expect($this->cartService->getItemCount(null))->toBe(0);
});

it('handles variant sizes in cart', function () {
    $variant = ProductVariantSize::factory()->create([
        'product_id' => $this->product->id,
        'extra_price_grosze' => 1000,
    ]);
    $user = User::factory()->create();

    $this->cartService->addItem($user, $this->product->id, $variant->id, 1);

    $items = $this->cartService->getItems($user);
    expect($items->first()->variant->id)->toBe($variant->id);
    expect($items->first()->unit_price_grosze)->toBe(3999);
});

it('returns total in grosze', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 2);

    expect($this->cartService->totalGrosze($user))->toBe(5998);
});

it('guest can access cart page', function () {
    $response = $this->get(route('cart.index'));

    $response->assertOk();
});

it('add to cart endpoint works', function () {
    $response = $this->post(route('cart.add'), [
        'product_id' => $this->product->id,
        'qty' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('cart badge returns JSON count', function () {
    $user = User::factory()->create();
    $this->cartService->addItem($user, $this->product->id, null, 3);

    $response = $this->actingAs($user)->get(route('cart.badge'));

    $response->assertOk();
    expect($response->json('count'))->toBe(3);
});
