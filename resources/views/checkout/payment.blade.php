@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Płatność') }}</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Podsumowanie zamówienia') }}</h2>
        @foreach($items as $item)
            <div class="flex justify-between py-2 border-b">
                <span>{{ $item->product->name }} × {{ $item->qty }}</span>
                <span>{{ number_format($item->subtotal_grosze / 100, 2, ',', '') }} zł</span>
            </div>
        @endforeach

        @if($cart?->shipping_method)
            <div class="flex justify-between py-2 border-b text-gray-600">
                <span>{{ __('Dostawa') }} ({{ $cart->shipping_method }})</span>
                <span>{{ number_format($shippingGrosze / 100, 2, ',', '') }} zł</span>
            </div>
        @endif

        <div class="flex justify-between py-2 font-bold text-lg">
            <span>{{ __('Do zapłaty') }}</span>
            <span>{{ number_format(($totalGrosze + $shippingGrosze) / 100, 2, ',', '') }} zł</span>
        </div>
    </div>

    @if($cart?->shipping_address)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-semibold mb-2">{{ __('Adres dostawy') }}</h3>
            <p>{{ $cart['shipping_address']['street'] ?? '' }}</p>
            <p>{{ $cart['shipping_address']['postal_code'] ?? '' }} {{ $cart['shipping_address']['city'] ?? '' }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}" class="text-center">
        @csrf
        <button type="submit" class="bg-yellow-400 text-black px-8 py-4 rounded-lg font-bold text-lg hover:bg-yellow-500 inline-flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>
            </svg>
            {{ __('Zapłać z Przelewy24') }}
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('checkout.shipping') }}" class="text-gray-600 hover:underline">&larr; {{ __('Powrót do dostawy') }}</a>
    </div>
</div>
@endsection
