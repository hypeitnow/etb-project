<?php

namespace App\Services;

use App\Contracts\ShippingProviderInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class DpdShippingProvider implements ShippingProviderInterface
{
    private string $apiKey;

    private string $login;

    private string $password;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('shipping.dpd.api_key', '');
        $this->login = config('shipping.dpd.login', '');
        $this->password = config('shipping.dpd.password', '');
        $this->baseUrl = config('shipping.dpd.base_url', 'https://dpd.com.pl/api');
    }

    public function calculateCost(array $cartItems, array $address): int
    {
        $country = $address['country'] ?? 'Polska';
        $isDomestic = in_array(strtolower($country), ['polska', 'poland', 'pl'], true);

        if ($isDomestic) {
            return 1490;
        }

        return 4990;
    }

    public function createShipment(Order $order): array
    {
        Log::info('DPD shipment creation (stub)', ['order_id' => $order->id]);

        return [
            'tracking_number' => 'DPD-'.$order->id.'-'.str()->upper(substr(md5((string) $order->id), 0, 8)),
            'label_url' => '#',
            'status' => 'created',
        ];
    }

    public function getTrackingStatus(string $trackingNumber): string
    {
        Log::info('DPD tracking status (stub)', ['tracking' => $trackingNumber]);

        return 'in_transit';
    }

    public function generateLabel(Order $order): string
    {
        Log::info('DPD label generation (stub)', ['order_id' => $order->id]);

        return '#LABEL-STUB-DPD-'.$order->id;
    }
}
