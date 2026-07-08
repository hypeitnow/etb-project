<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 20px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 11px; }
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { vertical-align: top; padding: 5px 10px; }
        .info-table .label { font-weight: bold; width: 120px; color: #555; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background: #f3f4f6; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .items-table .right { text-align: right; }
        .totals { width: 300px; margin-left: auto; }
        .totals td { padding: 4px 8px; }
        .totals .right { text-align: right; }
        .totals .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; padding-top: 6px; }
        .footer { margin-top: 40px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Faktura VAT</h1>
        <p>Nr {{ $number }}</p>
        <p>Data wystawienia: {{ now()->format('d.m.Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Sprzedawca:</td>
            <td>{{ $sellerName }}<br>{{ $sellerAddress }}<br>NIP: {{ $sellerNip }}</td>
        </tr>
        <tr>
            <td class="label">Nabywca:</td>
            <td>{{ $buyerName }}<br>{{ $buyerAddress }}@if($buyerNip)<br>NIP: {{ $buyerNip }}@endif</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Lp.</th>
                <th>Nazwa</th>
                <th>Rozmiar</th>
                <th class="right">Ilość</th>
                <th class="right">Cena netto</th>
                <th class="right">VAT</th>
                <th class="right">Wartość netto</th>
                <th class="right">Wartość brutto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $item->product->name ?? '[usunięty]' }}</td>
                    <td>{{ $item->variantSize->size_label ?? '—' }}</td>
                    <td class="right">{{ $item->qty }}</td>
                    <td class="right">{{ number_format(($item->net_price_grosze ?? 0) / 100, 2, ',', ' ') }} zł</td>
                    <td class="right">{{ $item->vat_rate ?? 23 }}%</td>
                    <td class="right">{{ number_format((($item->net_price_grosze ?? 0) * $item->qty) / 100, 2, ',', ' ') }} zł</td>
                    <td class="right">{{ number_format((($item->gross_price_grosze ?? 0) * $item->qty) / 100, 2, ',', ' ') }} zł</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Razem netto:</td>
            <td class="right">{{ number_format(($order->total_net_grosze ?? 0) / 100, 2, ',', ' ') }} zł</td>
        </tr>
        <tr>
            <td>W tym VAT:</td>
            <td class="right">{{ number_format(($order->total_vat_grosze ?? 0) / 100, 2, ',', ' ') }} zł</td>
        </tr>
        <tr>
            <td>Dostawa:</td>
            <td class="right">{{ number_format(($order->shipping_grosze ?? 0) / 100, 2, ',', ' ') }} zł</td>
        </tr>
        <tr class="grand-total">
            <td>Razem brutto:</td>
            <td class="right">{{ number_format(($order->total_grosze + $order->shipping_grosze) / 100, 2, ',', ' ') }} zł</td>
        </tr>
    </table>

    <div class="footer">
        Wygenerowano automatycznie — {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>
