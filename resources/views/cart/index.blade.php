@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Koszyk') }}</h1>

    @if($items->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <p class="text-gray-500 text-lg mb-4">{{ __('Twój koszyk jest pusty.') }}</p>
            <a href="{{ route('shop.index') }}" class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-500 inline-block">
                &larr; {{ __('Przejdź do sklepu') }}
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('cart.update') }}">
            @csrf
            <div class="bg-white rounded-lg shadow">
                <table class="w-full">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3">{{ __('Produkt') }}</th>
                            <th class="text-center px-4 py-3">{{ __('Ilość') }}</th>
                            <th class="text-right px-4 py-3">{{ __('Cena') }}</th>
                            <th class="text-right px-4 py-3">{{ __('Suma') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->product->name }}</div>
                                    @if($item->variant)
                                        <div class="text-sm text-gray-500">{{ $item->variant->size }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product->id }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][variant_size_id]" value="{{ $item->variant?->id }}">
                                    <input type="number" name="items[{{ $loop->index }}][qty]" value="{{ $item->qty }}"
                                           min="0" max="99"
                                           class="w-16 text-center border rounded px-2 py-1">
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->unit_price_grosze / 100, 2, ',', '') }} zł</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($item->subtotal_grosze / 100, 2, ',', '') }} zł</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('cart.remove') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                        <input type="hidden" name="variant_size_id" value="{{ $item->variant?->id }}">
                                        <button class="text-red-500 hover:text-red-700">{{ __('Usuń') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <div class="text-2xl font-bold">
                    {{ __('Razem') }}: {{ number_format($totalGrosze / 100, 2, ',', '') }} zł
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="border border-gray-400 px-4 py-2 rounded font-semibold hover:bg-gray-100">
                        {{ __('Aktualizuj koszyk') }}
                    </button>
                    <a href="{{ route('checkout.shipping') }}"
                       class="bg-yellow-400 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500">
                        {{ __('Przejdź do kasy') }} &rarr;
                    </a>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
