<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $this->customer = User::factory()->create(['role' => User::ROLE_FAN]);
});

it('admin can transition order from pending to paid', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_PAID,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_PAID,
    ]);
});

it('admin can transition order from paid to shipped with tracking number', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PAID,
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_SHIPPED,
            'tracking_number' => 'PL123456789',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_SHIPPED,
        'tracking_number' => 'PL123456789',
    ]);
});

it('admin can cancel an order with a note', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PAID,
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_CANCELLED,
            'note' => 'Klient zrezygnował z zamówienia.',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_CANCELLED,
    ]);
    $this->assertDatabaseHas('order_status_logs', [
        'order_id' => $order->id,
        'from_status' => Order::STATUS_PAID,
        'to_status' => Order::STATUS_CANCELLED,
        'note' => 'Klient zrezygnował z zamówienia.',
        'user_id' => $this->admin->id,
    ]);
});

it('status transition creates a log entry', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);

    $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_PAID,
        ]);

    $this->assertDatabaseHas('order_status_logs', [
        'order_id' => $order->id,
        'from_status' => Order::STATUS_PENDING_PAYMENT,
        'to_status' => Order::STATUS_PAID,
        'user_id' => $this->admin->id,
    ]);
});

it('stock is decremented when order is marked paid', function () {
    $product = Product::factory()->create([
        'stock_qty' => 10,
        'is_physical' => true,
    ]);
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'qty' => 3,
        'unit_price_grosze' => $product->price_grosze,
        'variant_size_id' => null,
    ]);

    $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_PAID,
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock_qty' => 7,
    ]);
});

it('stock is incremented when order is cancelled after payment', function () {
    $product = Product::factory()->create([
        'stock_qty' => 7,
        'is_physical' => true,
    ]);
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PAID,
    ]);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'qty' => 3,
        'unit_price_grosze' => $product->price_grosze,
        'variant_size_id' => null,
    ]);

    $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_CANCELLED,
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock_qty' => 10,
    ]);
});

it('customer cannot transition order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);

    $response = $this->actingAs($this->customer)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_PAID,
        ]);

    $response->assertForbidden();
});

it('order can transition from failed back to pending_payment', function () {
    $order = Order::factory()->create([
        'user_id' => $this->customer->id,
        'status' => Order::STATUS_FAILED,
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('admin.orders.transition', $order), [
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => Order::STATUS_PENDING_PAYMENT,
    ]);
});
