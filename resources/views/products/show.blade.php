@extends('layouts.app')

@section('content')
<div class="bg-black text-white min-h-screen">
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-6">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-zinc-400 hover:text-yellow-400 transition mb-8">
                <span aria-hidden="true">←</span> Powrót do sklepu
            </a>

            <div class="grid gap-12 lg:grid-cols-2">
                <div class="aspect-square rounded-xl bg-zinc-900 border border-zinc-800 overflow-hidden flex items-center justify-center">
                    @if($product->images && $img = $product->images[0] ?? null)
                        <img src="{{ asset('storage/'.$img) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-6xl font-black text-zinc-700">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-yellow-400">{{ $product->category?->name }}</p>
                    <h1 class="mt-3 text-4xl font-black uppercase">{{ $product->name }}</h1>
                    <p class="mt-6 text-sm text-zinc-400 leading-relaxed">{{ $product->description }}</p>

                    <div class="mt-8 border-t border-zinc-800 pt-8">
                        <p class="text-3xl font-black text-yellow-400">{{ $product->displayPrice() }}</p>
                        <p class="mt-2 text-sm text-zinc-500">brutto (w tym 23% VAT)</p>
                    </div>

                    @if($product->variantSizes->isNotEmpty())
                        <div class="mt-8">
                            <p class="text-sm font-bold uppercase tracking-wider text-zinc-300 mb-4">Dostępne rozmiary</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($product->variantSizes as $variant)
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-3 text-center min-w-[80px]">
                                        <p class="text-lg font-black text-white">{{ $variant->size_label }}</p>
                                        <p class="text-xs text-zinc-500">{{ $variant->stock_qty > 0 ? 'Dostępny' : 'Brak' }}</p>
                                        @if($variant->extra_price_grosze > 0)
                                            <p class="text-xs text-yellow-400">+{{ number_format($variant->extra_price_grosze / 100, 2, ',', '') }} zł</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-10">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex flex-wrap items-end gap-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div>
                                <label for="qty" class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Ilość</label>
                                <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ $product->stock_qty > 0 ? $product->stock_qty : 99 }}" class="w-20 rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-3 text-center text-lg font-bold text-white">
                            </div>
                            <button type="submit" class="rounded-lg bg-yellow-400 px-8 py-3 text-sm font-black uppercase text-black hover:bg-white transition-all shadow-lg shadow-yellow-400/20">Dodaj do koszyka</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
