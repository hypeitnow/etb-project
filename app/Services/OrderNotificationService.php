<?php

namespace App\Services;

use App\Jobs\SendOrderCancelledNotificationJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Jobs\SendOrderShippedNotificationJob;
use App\Models\Order;

class OrderNotificationService
{
    public function sendConfirmation(Order $order): void
    {
        SendOrderConfirmationJob::dispatch($order);
    }

    public function sendShipped(Order $order): void
    {
        SendOrderShippedNotificationJob::dispatch($order);
    }

    public function sendCancelled(Order $order, ?string $reason = null): void
    {
        SendOrderCancelledNotificationJob::dispatch($order, $reason);
    }

    public function notifyStatusChange(Order $order, string $fromStatus, string $toStatus, ?string $note = null): void
    {
        match ($toStatus) {
            Order::STATUS_PAID => $this->sendConfirmation($order),
            Order::STATUS_SHIPPED => $this->sendShipped($order),
            Order::STATUS_CANCELLED => $this->sendCancelled($order, $note),
            default => null,
        };
    }
}
