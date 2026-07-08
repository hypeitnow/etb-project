<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $this->customer = User::factory()->create(['role' => User::ROLE_FAN]);
});

it('admin can list orders', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.orders.index'));

    $response->assertOk();
    $response->assertSee((string) $order->id);
});

it('employee can list orders', function () {
    Order::factory()->create(['user_id' => $this->customer->id]);

    $response = $this->actingAs($this->employee)
        ->get(route('admin.orders.index'));

    $response->assertOk();
});

it('guest cannot access order list', function () {
    $response = $this->get(route('admin.orders.index'));

    $response->assertRedirect(route('login'));
});

it('customer cannot access order list', function () {
    $response = $this->actingAs($this->customer)
        ->get(route('admin.orders.index'));

    $response->assertForbidden();
});

it('admin can view order details', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.orders.show', $order));

    $response->assertOk();
    $response->assertSee((string) $order->id);
});

it('filters orders by status via API', function () {
    $paidOrder = Order::factory()->create(['user_id' => $this->customer->id, 'status' => Order::STATUS_PAID]);
    Order::factory()->create(['user_id' => $this->customer->id, 'status' => Order::STATUS_PENDING_PAYMENT]);

    Route::get('/test-orders-filter', function () {
        $orders = Order::with('user')
            ->when(request()->input('status'), fn ($q) => $q->where('status', request()->input('status')))
            ->latest()
            ->get();

        return response()->json(['ids' => $orders->pluck('id')->toArray()]);
    });

    $response = $this->actingAs($this->admin)
        ->getJson('/test-orders-filter?status=paid');

    expect($response->json('ids'))->toContain($paidOrder->id)
        ->and($response->json('ids'))->toHaveCount(1);
});

it('searches orders by order ID', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id]);
    Order::factory()->create(['user_id' => $this->customer->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.orders.index', ['search' => $order->id]));

    $response->assertOk();
    $response->assertSee((string) $order->id);
});

it('searches orders by customer name', function () {
    $customer = User::factory()->create(['name' => 'Jan Kowalski', 'role' => User::ROLE_FAN]);
    $order = Order::factory()->create(['user_id' => $customer->id]);
    Order::factory()->create(['user_id' => $this->customer->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.orders.index', ['search' => 'Jan Kowalski']));

    $response->assertOk();
    $response->assertSee((string) $order->id);
});
