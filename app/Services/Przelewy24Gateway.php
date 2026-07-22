<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class Przelewy24Gateway implements PaymentGatewayInterface
{
    private string $merchantId;

    private string $posId;

    private string $crcKey;

    private string $apiKey;

    private string $baseUrl;

    private string $returnUrl;

    private string $statusUrl;

    public function __construct()
    {
        $this->merchantId = config('przelewy24.merchant_id');
        $this->posId = config('przelewy24.pos_id');
        $this->crcKey = config('przelewy24.crc_key');
        $this->apiKey = config('przelewy24.api_key');
        $this->baseUrl = config('przelewy24.base_url', 'https://sandbox.przelewy24.pl');
        $this->returnUrl = config('przelewy24.return_url');
        $this->statusUrl = config('przelewy24.status_url');
    }

    public function createPayment(Order $order): string
    {
        $sessionId = $order->id.'|'.uniqid('', true);
        $order->update(['payment_session_id' => $sessionId]);

        $amount = $order->total_grosze + $order->shipping_grosze;

        $payload = [
            'merchantId' => (int) $this->merchantId,
            'posId' => (int) $this->posId,
            'sessionId' => $sessionId,
            'amount' => $amount,
            'currency' => 'PLN',
            'description' => "Zamówienie #{$order->id}",
            'email' => $order->user->email,
            'country' => 'PL',
            'language' => 'pl',
            'urlReturn' => $this->returnUrl,
            'urlStatus' => $this->statusUrl,
            'sign' => $this->sign($sessionId, $amount),
        ];

        $response = Http::withBasicAuth($this->posId, $this->apiKey)
            ->post("{$this->baseUrl}/api/v1/transaction/register", $payload);

        if (! $response->successful()) {
            Log::error('Przelewy24 register failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Nie udało się utworzyć płatności Przelewy24.');
        }

        $data = $response->json();

        return $data['data']['token'];
    }

    public function verifyTransaction(array $data): bool
    {
        $requiredFields = ['merchantId', 'posId', 'sessionId', 'amount', 'originAmount', 'currency', 'orderId', 'methodId', 'statement', 'sign'];
        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                return false;
            }
        }

        $expectedSign = $this->sign(
            $data['sessionId'],
            (int) $data['amount'],
            $data['currency'],
            (int) $data['orderId'],
        );

        if ($data['sign'] !== $expectedSign) {
            return false;
        }

        $verifyResponse = Http::withBasicAuth($this->posId, $this->apiKey)
            ->put("{$this->baseUrl}/api/v1/transaction/verify", [
                'merchantId' => (int) $this->merchantId,
                'posId' => (int) $this->posId,
                'sessionId' => $data['sessionId'],
                'amount' => (int) $data['amount'],
                'currency' => $data['currency'],
                'orderId' => (int) $data['orderId'],
                'sign' => $this->sign(
                    $data['sessionId'],
                    (int) $data['amount'],
                    $data['currency'],
                    (int) $data['orderId'],
                ),
            ]);

        return $verifyResponse->successful();
    }

    public function extractTransactionId(array $data): ?string
    {
        return $data['orderId'] ?? null;
    }

    public function extractStatus(array $data): string
    {
        if (isset($data['status'])) {
            return $data['status'];
        }

        return Order::STATUS_FAILED;
    }

    private function sign(string $sessionId, int $amount, ?string $currency = 'PLN', ?int $orderId = null): string
    {
        $parts = [
            json_encode([
                'sessionId' => $sessionId,
                'merchantId' => (int) $this->merchantId,
                'amount' => $amount,
                'currency' => $currency,
                'crc' => $this->crcKey,
            ]),
        ];

        return hash('sha384', implode('|', $parts));
    }
}
