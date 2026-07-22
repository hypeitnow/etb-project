<?php

namespace App\Services;

use App\Contracts\ShippingProviderInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class InPostShippingProvider implements ShippingProviderInterface
{
    private string $apiToken;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('shipping.inpost.api_token', '');
        $this->baseUrl = config('shipping.inpost.base_url', 'https://api.inpost.pl');
    }

    public function calculateCost(array $cartItems, array $address): int
    {
        $method = $address['shipping_method'] ?? 'courier';

        return match ($method) {
            'inpost_locker' => 1290,
            'inpost_courier' => 1590,
            default => 1490,
        };
    }

    public function createShipment(Order $order): array
    {
        Log::info('InPost shipment creation (stub)', ['order_id' => $order->id]);

        return [
            'tracking_number' => 'INPOST-'.$order->id.'-'.str()->upper(substr(md5((string) $order->id), 0, 8)),
            'label_url' => '#',
            'status' => 'created',
        ];
    }

    public function getTrackingStatus(string $trackingNumber): string
    {
        Log::info('InPost tracking status (stub)', ['tracking' => $trackingNumber]);

        return 'in_transit';
    }

    public function generateLabel(Order $order): string
    {
        Log::info('InPost label generation (stub)', ['order_id' => $order->id]);

        return '#LABEL-STUB-'.$order->id;
    }
}
