@component('mail::message')
# Zamówienie #{{ $order->id }} zostało wysłane!

Twoje zamówienie zostało nadane i jest w drodze do Ciebie.

## Dane przesyłki
@if($order->tracking_number)
**Numer przesyłki:** {{ $order->tracking_number }}
@endif
**Metoda dostawy:** {{ $order->shipping_method ?? '—' }}

@if($order->shipping_address)
## Adres dostawy
- Ulica: {{ $order->shipping_address['street'] ?? '' }}
- Kod pocztowy: {{ $order->shipping_address['postal_code'] ?? '' }}
- Miasto: {{ $order->shipping_address['city'] ?? '' }}
@endif

**Kwota zamówienia:** {{ $order->displayTotal() }}

Dziękujemy za zakupy w sklepie ETB!

Pozdrawiamy,<br>
Zespół ETB
@endcomponent
