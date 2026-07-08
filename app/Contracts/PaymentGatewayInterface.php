<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order): string;

    public function verifyTransaction(array $data): bool;

    public function extractTransactionId(array $data): ?string;

    public function extractStatus(array $data): string;
}
