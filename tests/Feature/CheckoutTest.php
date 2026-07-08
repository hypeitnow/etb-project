<?php

use App\Contracts\PaymentGatewayInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->product = Product::factory()->create([
        'price_grosze' => 2999,
        'is_physical' => true,
    ]);

    session()->start();
});

it('redirects guest to login on checkout shipping', function () {
    $response = $this->get(route('checkout.shipping'));

    $response->assertRedirect(route('login'));
});

it('redirects to shop if cart is empty on shipping step', function () {
    $response = $this->actingAs($this->user)
        ->get(route('checkout.shipping'));

    $response->assertRedirect(route('shop.index'));
});

it('shows shipping form with items in cart', function () {
    app(CartService::class)->addItem($this->user, $this->product->id, null, 1);

    $response = $this->actingAs($this->user)
        ->get(route('checkout.shipping'));

    $response->assertOk();
    $response->assertSee(__('Dostawa'));
});

it('stores shipping address and method', function () {
    app(CartService::class)->addItem($this->user, $this->product->id, null, 1);

    $response = $this->actingAs($this->user)
        ->post(route('checkout.shipping'), [
            'shipping_method' => 'inpost_locker',
            'address' => [
                'street' => 'Testowa 1',
                'city' => 'Łódź',
                'postal_code' => '90-001',
                'country' => 'Polska',
            ],
        ]);

    $response->assertRedirect(route('checkout.payment'));

    $cart = Cart::where('user_id', $this->user->id)->first();
    expect($cart->shipping_method)->toBe('inpost_locker');
    expect($cart->shipping_address['street'])->toBe('Testowa 1');
});

it('shows payment page with items', function () {
    app(CartService::class)->addItem($this->user, $this->product->id, null, 1);
    $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
    $cart->update(['shipping_method' => 'inpost_locker', 'shipping_address' => ['street' => 'Testowa 1', 'city' => 'Łódź', 'postal_code' => '90-001', 'country' => 'Polska']]);

    $response = $this->actingAs($this->user)
        ->get(route('checkout.payment'));

    $response->assertOk();
    $response->assertSee(__('Przelewy24'));
});

it('creates order and redirects to payment gateway', function () {
    $gateway = Mockery::mock(PaymentGatewayInterface::class);
    $gateway->shouldReceive('createPayment')->once()->andReturn('test-token-123');
    $this->app->instance(PaymentGatewayInterface::class, $gateway);

    app(CartService::class)->addItem($this->user, $this->product->id, null, 1);
    $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
    $cart->update(['shipping_method' => 'inpost_locker', 'shipping_address' => ['street' => 'Testowa 1', 'city' => 'Łódź', 'postal_code' => '90-001', 'country' => 'Polska']]);

    $response = $this->actingAs($this->user)
        ->post(route('checkout.place'));

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
});

it('confirmation page requires auth', function () {
    $order = Order::factory()->create();

    $response = $this->get(route('checkout.confirmation', $order));

    $response->assertRedirect(route('login'));
});

it('confirmation page shows order details', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('checkout.confirmation', $order));

    $response->assertOk();
});

it('webhook marks order as paid', function () {
    $gateway = Mockery::mock(PaymentGatewayInterface::class);
    $gateway->shouldReceive('verifyTransaction')->once()->andReturn(true);
    $this->app->instance(PaymentGatewayInterface::class, $gateway);

    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);

    $response = $this->post(route('payment.przelewy24.webhook'), [
        'merchantId' => 123,
        'posId' => 123,
        'sessionId' => $order->id.'|abc123',
        'amount' => 2999,
        'originAmount' => 2999,
        'currency' => 'PLN',
        'orderId' => 99999,
        'methodId' => 1,
        'statement' => 'P24 test',
        'sign' => 'dummy',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_PAID,
    ]);
});

it('skips shipping for digital products', function () {
    $digitalProduct = Product::factory()->create([
        'price_grosze' => 999,
        'is_physical' => false,
    ]);
    app(CartService::class)->addItem($this->user, $digitalProduct->id, null, 1);

    $response = $this->actingAs($this->user)
        ->get(route('checkout.shipping'));

    $response->assertOk();
    $response->assertSee(__('produkty cyfrowe'));
});

it('prevents accessing another users confirmation page', function () {
    $otherUser = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)
        ->get(route('checkout.confirmation', $order));

    $response->assertForbidden();
});
