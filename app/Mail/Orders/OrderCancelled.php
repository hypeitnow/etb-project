<?php

namespace App\Mail\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order, public readonly ?string $reason = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Zamówienie #{$this->order->id} zostało anulowane",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.cancelled',
        );
    }
}
