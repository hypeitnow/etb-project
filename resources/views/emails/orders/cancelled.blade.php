@component('mail::message')
# Zamówienie #{{ $order->id }} zostało anulowane

Twoje zamówienie zostało anulowane.

@if($reason)
## Powód anulowania
{{ $reason }}
@endif

**Kwota zamówienia:** {{ $order->displayTotal() }}

Jeśli płatność została już zrealizowana, środki zostaną zwrócone zgodnie z polityką zwrotów.

W razie pytań skontaktuj się z nami.

Pozdrawiamy,<br>
Zespół ETB
@endcomponent
