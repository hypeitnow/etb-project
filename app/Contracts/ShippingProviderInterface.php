<?php

namespace App\Contracts;

use App\Models\Order;

interface ShippingProviderInterface
{
    public function calculateCost(array $cartItems, array $address): int;

    public function createShipment(Order $order): array;

    public function getTrackingStatus(string $trackingNumber): string;

    public function generateLabel(Order $order): string;
}
