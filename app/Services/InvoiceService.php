<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generate(Order $order): Invoice
    {
        $order->loadMissing('items.product', 'items.variantSize', 'user');

        $number = Invoice::generateNumber($order);

        $sellerName = config('invoice.seller.name', 'ETB Sklep');
        $sellerAddress = config('invoice.seller.address', 'ul. Przykładowa 1, 00-001 Warszawa');
        $sellerNip = config('invoice.seller.nip', '1234567890');

        $buyerName = $order->shipping_address['name'] ?? $order->user->name;
        $address = $order->shipping_address;
        $buyerAddress = $address
            ? sprintf('%s, %s %s, %s', $address['street'] ?? '', $address['postal_code'] ?? '', $address['city'] ?? '', $address['country'] ?? '')
            : '';
        $buyerNip = $order->shipping_address['nip'] ?? '';

        $pdf = Pdf::loadView('invoices.invoice', [
            'order' => $order,
            'number' => $number,
            'sellerName' => $sellerName,
            'sellerAddress' => $sellerAddress,
            'sellerNip' => $sellerNip,
            'buyerName' => $buyerName,
            'buyerAddress' => $buyerAddress,
            'buyerNip' => $buyerNip,
        ]);

        $filename = sprintf('invoice_%s_%s.pdf', str_replace('/', '-', $number), $order->id);
        $path = 'invoices/'.$filename;
        Storage::disk('local')->put($path, $pdf->output());

        return Invoice::create([
            'order_id' => $order->id,
            'number' => $number,
            'seller_name' => $sellerName,
            'seller_address' => $sellerAddress,
            'seller_nip' => $sellerNip,
            'buyer_name' => $buyerName,
            'buyer_address' => $buyerAddress,
            'buyer_nip' => $buyerNip,
            'total_net_grosze' => $order->total_net_grosze ?? 0,
            'total_vat_grosze' => $order->total_vat_grosze ?? 0,
            'total_gross_grosze' => $order->total_grosze,
            'pdf_path' => $path,
            'issued_at' => now(),
        ]);
    }
}
