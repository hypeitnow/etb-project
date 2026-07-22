@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Dostawa') }}</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">{{ __('Podsumowanie koszyka') }}</h2>
        @foreach($items as $item)
            <div class="flex justify-between py-2 border-b">
                <span>{{ $item->product->name }} × {{ $item->qty }}</span>
                <span>{{ number_format($item->subtotal_grosze / 100, 2, ',', '') }} zł</span>
            </div>
        @endforeach
        <div class="flex justify-between py-2 font-bold text-lg">
            <span>{{ __('Razem') }}</span>
            <span>{{ number_format($totalGrosze / 100, 2, ',', '') }} zł</span>
        </div>
    </div>

    <form method="POST" action="{{ route('checkout.shipping') }}" class="space-y-6">
        @csrf

        @if($needsShipping)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">{{ __('Metoda dostawy') }}</h2>

                <div class="space-y-3">
                    @foreach(config('shipping.methods', []) as $value => $method)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="shipping_method" value="{{ $value }}" @checked($loop->first)>
                            <div>
                                <span class="font-medium">{{ $method['label'] }}</span>
                                <span class="text-gray-500 ml-2">
                                    @if($method['price_grosze'] > 0)
                                        {{ number_format($method['price_grosze'] / 100, 2, ',', '') }} zł
                                    @else
                                        {{ __('Bezpłatna') }}
                                    @endif
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">{{ __('Adres dostawy') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Ulica i numer') }}</label>
                        <input type="text" name="address[street]" value="{{ old('address.street') }}" class="w-full border rounded px-3 py-2" required>
                        @error('address.street') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('Miasto') }}</label>
                        <input type="text" name="address[city]" value="{{ old('address.city') }}" class="w-full border rounded px-3 py-2" required>
                        @error('address.city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('Kod pocztowy') }}</label>
                        <input type="text" name="address[postal_code]" value="{{ old('address.postal_code') }}" class="w-full border rounded px-3 py-2" required>
                        @error('address.postal_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Kraj') }}</label>
                        <input type="text" name="address[country]" value="{{ old('address.country', 'Polska') }}" class="w-full border rounded px-3 py-2" required>
                        @error('address.country') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-800">
                {{ __('Twoje zamówienie zawiera tylko produkty cyfrowe — pomijamy krok dostawy.') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <a href="{{ route('cart.index') }}" class="text-gray-600 hover:underline">&larr; {{ __('Powrót do koszyka') }}</a>
            <button type="submit" class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-500">
                {{ __('Dalej: Płatność') }} &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
