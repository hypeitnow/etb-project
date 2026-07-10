@extends('layouts.app')

@section('content')
<div class="bg-black text-white">
    <section class="bg-zinc-950 py-16 border-b border-zinc-800/50">
        <div class="mx-auto max-w-7xl px-6">
            <p class="text-xs font-black uppercase tracking-[0.28em] text-yellow-400">Sklep</p>
            <h1 class="mt-2 text-4xl font-black uppercase md:text-5xl">Kupuj ETB</h1>
            <p class="mt-4 max-w-2xl text-base text-zinc-400">Sprawdź nasze produkty — koszulki, akcesoria i gadżety klubowe.</p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-6">
            @if($categories->isNotEmpty())
                <div class="mb-8 flex flex-wrap gap-3">
                    <a href="{{ route('shop.index') }}" class="rounded-full border border-zinc-700 px-5 py-2 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-yellow-400 hover:text-black {{ ! request('category') ? 'bg-yellow-400 text-black' : '' }}">Wszystkie</a>
                    @foreach($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->id]) }}"
                           class="rounded-full border border-zinc-700 px-5 py-2 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-yellow-400 hover:text-black {{ request('category') == $category->id ? 'bg-yellow-400 text-black' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if($products->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-700 bg-zinc-900/50 p-12 text-center">
                    <p class="text-lg font-bold text-zinc-400">Brak produktów w sklepie</p>
                    <p class="mt-2 text-sm text-zinc-500">Produkty pojawią się tutaj po dodaniu ich w panelu administracyjnym.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        <a href="{{ route('shop.show', $product) }}" class="group overflow-hidden rounded-xl bg-zinc-900 border border-zinc-800 transition-all hover:-translate-y-1 hover:border-yellow-400/50 hover:shadow-xl hover:shadow-yellow-400/5">
                            <div class="aspect-square bg-zinc-800 overflow-hidden flex items-center justify-center">
                                @if($product->images && $img = $product->images[0] ?? null)
                                    <img src="{{ asset('storage/'.$img) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <span class="text-4xl font-black text-zinc-700">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="p-5">
                                <p class="text-xs font-black uppercase tracking-wider text-yellow-400">{{ $product->category?->name }}</p>
                                <h3 class="mt-2 text-lg font-black">{{ $product->name }}</h3>
                                <p class="mt-2 text-sm text-zinc-400 line-clamp-2">{{ $product->description }}</p>
                                <p class="mt-4 text-xl font-black text-yellow-400">{{ $product->displayPrice() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
