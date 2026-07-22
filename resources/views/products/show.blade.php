@extends('layouts.app')

@section('content')
<section class="bg-zinc-950 py-12 text-white">
    <div class="mx-auto max-w-7xl px-6">
        <a href="{{ route('shop.index') }}" class="mb-8 inline-flex items-center text-sm font-semibold text-yellow-300 hover:text-yellow-200">
            &larr; {{ __('Wróć do sklepu') }}
        </a>

        @php
            $image = is_array($product->images) ? ($product->images[0] ?? null) : null;
            $availableVariants = $product->variantSizes->filter(fn ($variant) => $variant->stock_qty > 0);
            $available = $product->variantSizes->isNotEmpty()
                ? $availableVariants->isNotEmpty()
                : (! $product->is_physical || $product->stock_qty > 0);
        @endphp

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(360px,480px)] lg:items-start">
            <div class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900">
                <div class="flex aspect-square items-center justify-center bg-zinc-800">
                    @if($image)
                        <img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="px-6 text-center text-zinc-400">{{ __('Zdjęcie produktu') }}</span>
                    @endif
                </div>
            </div>

            <div>
                @if($product->category)
                    <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-yellow-400">{{ $product->category->name }}</p>
                @endif
                <h1 class="mb-4 text-3xl font-bold md:text-4xl">{{ $product->name }}</h1>
                <p class="mb-6 text-3xl font-bold text-yellow-300">{{ $product->displayPrice() }}</p>

                @if($product->description)
                    <div class="prose prose-invert mb-8 max-w-none text-zinc-300">
                        <p>{{ $product->description }}</p>
                    </div>
                @endif

                <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-6">
                    @if($available)
                        <form method="POST" action="{{ route('cart.add') }}" class="space-y-5">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            @if($product->variantSizes->isNotEmpty())
                                <label class="block">
                                    <span class="mb-2 block text-sm font-semibold text-zinc-200">{{ __('Rozmiar') }}</span>
                                    <select name="variant_size_id" class="w-full rounded border border-zinc-700 bg-black px-3 py-2 text-white focus:border-yellow-400 focus:outline-none">
                                        @foreach($availableVariants as $variant)
                                            <option value="{{ $variant->id }}">
                                                {{ $variant->size_label }}
                                                @if($variant->extra_price_grosze > 0)
                                                    (+{{ number_format($variant->extra_price_grosze / 100, 2, ',', '') }} zł)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif

                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold text-zinc-200">{{ __('Ilość') }}</span>
                                <input type="number" name="qty" value="1" min="1" max="99" class="w-28 rounded border border-zinc-700 bg-black px-3 py-2 text-white focus:border-yellow-400 focus:outline-none">
                            </label>

                            <button type="submit" class="w-full rounded bg-yellow-400 px-5 py-3 font-semibold text-black hover:bg-yellow-300">
                                {{ __('Dodaj do koszyka') }}
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <h2 class="mb-2 text-xl font-semibold">{{ __('Produkt niedostępny') }}</h2>
                            <p class="text-zinc-400">{{ __('Ten produkt jest obecnie niedostępny w magazynie.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
