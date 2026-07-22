<?php

namespace App\Jobs;

use App\Mail\Orders\OrderCancelled;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderCancelledNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Order $order, public readonly ?string $reason = null) {}

    public function handle(): void
    {
        Mail::to($this->order->user->email)->send(new OrderCancelled($this->order, $this->reason));
    }
}
