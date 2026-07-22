@extends('layouts.app')

@section('content')
<section class="bg-zinc-950 py-12 text-white">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-yellow-400">{{ __('Oficjalny sklep') }}</p>
                <h1 class="text-3xl font-bold md:text-4xl">{{ __('Sklep ETB') }}</h1>
            </div>
            <a href="{{ route('cart.index') }}" class="inline-flex w-fit items-center gap-2 rounded border border-yellow-400 px-4 py-2 font-semibold text-yellow-300 hover:bg-yellow-400 hover:text-black">
                {{ __('Koszyk') }}
            </a>
        </div>

        @if($categories->isNotEmpty())
            <div class="mb-8 flex flex-wrap gap-2">
                @foreach($categories as $category)
                    <span class="rounded border border-zinc-700 bg-zinc-900 px-3 py-1 text-sm text-zinc-200">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        @if($products->count() === 0)
            <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-8 text-center">
                <h2 class="mb-2 text-xl font-semibold">{{ __('Brak produktów') }}</h2>
                <p class="text-zinc-400">{{ __('W sklepie nie ma jeszcze opublikowanych produktów.') }}</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    @php
                        $image = is_array($product->images) ? ($product->images[0] ?? null) : null;
                        $hasVariants = $product->variantSizes->isNotEmpty();
                        $hasAvailableVariant = $product->variantSizes->contains(fn ($variant) => $variant->stock_qty > 0);
                    @endphp
                    <article class="overflow-hidden rounded-lg border border-zinc-800 bg-zinc-900">
                        <a href="{{ route('shop.show', $product) }}" class="block">
                            <div class="flex aspect-square items-center justify-center bg-zinc-800">
                                @if($image)
                                    <img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="px-4 text-center text-sm text-zinc-400">{{ __('Zdjęcie produktu') }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="flex min-h-56 flex-col p-5">
                            <div class="mb-3">
                                @if($product->category)
                                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-yellow-400">{{ $product->category->name }}</p>
                                @endif
                                <h2 class="text-lg font-semibold leading-snug">
                                    <a href="{{ route('shop.show', $product) }}" class="hover:text-yellow-300">{{ $product->name }}</a>
                                </h2>
                            </div>

                            @if($product->description)
                                <p class="mb-4 line-clamp-3 text-sm text-zinc-400">{{ $product->description }}</p>
                            @endif

                            <div class="mt-auto flex items-center justify-between gap-3">
                                <span class="text-xl font-bold text-yellow-300">{{ $product->displayPrice() }}</span>
                                @if($hasVariants && $hasAvailableVariant)
                                    <a href="{{ route('shop.show', $product) }}" class="rounded bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
                                        {{ __('Wybierz') }}
                                    </a>
                                @elseif(! $hasVariants && ($product->stock_qty > 0 || ! $product->is_physical))
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="rounded bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
                                            {{ __('Do koszyka') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="rounded border border-zinc-700 px-3 py-2 text-sm text-zinc-400">{{ __('Brak w magazynie') }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
