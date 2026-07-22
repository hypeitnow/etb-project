<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function jpk(Request $request): StreamedResponse
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $orders = Order::where('status', Order::STATUS_PAID)
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->with('items.product', 'user')
            ->get();

        $callback = function () use ($orders, $month, $year): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['JPK_FA', sprintf('Sprzedaż %02d/%d', $month, $year), now()->format('Y-m-d')], ';');

            fputcsv($handle, [], ';');

            fputcsv($handle, ['Lp.', 'Nr faktury', 'Data wystawienia', 'Data sprzedaży', 'Nabywca', 'NIP nabywcy', 'Adres nabywcy', 'Wartość netto', 'VAT', 'Wartość brutto', 'Waluta'], ';');

            foreach ($orders as $idx => $order) {
                $invoice = $order->invoice;
                fputcsv($handle, [
                    $idx + 1,
                    $invoice?->number ?? '—',
                    $invoice?->issued_at?->format('Y-m-d') ?? $order->paid_at?->format('Y-m-d') ?? '—',
                    $order->paid_at?->format('Y-m-d') ?? '—',
                    $order->user->name,
                    $invoice?->buyer_nip ?? '',
                    $invoice?->buyer_address ?? '',
                    number_format(($order->total_net_grosze ?? 0) / 100, 2, '.', ''),
                    number_format(($order->total_vat_grosze ?? 0) / 100, 2, '.', ''),
                    number_format($order->total_grosze / 100, 2, '.', ''),
                    'PLN',
                ], ';');
            }

            fclose($handle);
        };

        $filename = sprintf('jpk_fa_%02d_%d.csv', $month, $year);

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
