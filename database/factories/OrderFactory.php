<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => Order::STATUS_PENDING_PAYMENT,
            'total_grosze' => $this->faker->numberBetween(1000, 50000),
            'shipping_grosze' => 0,
            'shipping_method' => null,
            'shipping_address' => null,
            'paid_at' => null,
            'idempotency_key' => null,
            'payment_session_id' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }
}
