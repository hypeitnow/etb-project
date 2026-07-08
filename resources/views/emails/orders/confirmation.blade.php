@component('mail::message')
# Potwierdzenie zamówienia #{{ $order->id }}

Dziękujemy za złożenie zamówienia w sklepie ETB!

## Szczegóły zamówienia

| Produkt | Ilość | Cena |
|---------|-------|------|
@foreach($order->items as $item)
| {{ $item->product->name ?? '[usunięty]' }}@if($item->variantSize) ({{ $item->variantSize->size_label }})@endif | {{ $item->qty }} | {{ number_format($item->unit_price_grosze / 100, 2, ',', '') }} zł |
@endforeach

**Razem:** {{ $order->displayTotal() }}

@if($order->shipping_address)
## Adres dostawy
- Ulica: {{ $order->shipping_address['street'] ?? '' }}
- Kod pocztowy: {{ $order->shipping_address['postal_code'] ?? '' }}
- Miasto: {{ $order->shipping_address['city'] ?? '' }}
- Metoda: {{ $order->shipping_method ?? '—' }}
@endif

Twoje zamówienie oczekuje na płatność. Po zaksięgowaniu przelewu otrzymasz e-mail z potwierdzeniem.

Pozdrawiamy,<br>
Zespół ETB
@endcomponent
