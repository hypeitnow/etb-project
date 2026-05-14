@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $articleImage = function ($item) {
        $path = $item->main_image_path ?: $item->images->first()?->path;

        return $path ? asset('storage/'.$path) : null;
    };
@endphp

@section('content')
<div class="bg-zinc-100 text-zinc-950">
    <section
        class="relative min-h-[520px] overflow-hidden bg-zinc-950 text-white"
        x-data="{ active: 0, total: {{ max($heroNews->count(), 1) }} }"
        x-init="setInterval(() => active = (active + 1) % total, 5000)"
    >
        @forelse($heroNews as $item)
            @php($image = $articleImage($item))
            <a
                href="{{ route('news.show', $item) }}"
                class="absolute inset-0 transition-opacity duration-700"
                x-show="active === {{ $loop->index }}"
                x-transition.opacity
            >
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full bg-[radial-gradient(circle_at_20%_20%,#facc15_0,#facc15_18%,#0a0a0a_18%,#0a0a0a_100%)]"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-black/20"></div>
                <div class="absolute inset-x-0 bottom-0 mx-auto max-w-7xl px-6 pb-16">
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-yellow-400">{{ $item->publish_at?->format('d.m.Y') ?? $item->created_at?->format('d.m.Y') }}</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-black uppercase leading-tight md:text-6xl">{{ $item->title }}</h1>
                    <p class="mt-5 max-w-2xl text-base font-semibold text-zinc-200 md:text-lg">{{ $item->excerpt ?: Str::limit(strip_tags($item->content), 150) }}</p>
                </div>
            </a>
        @empty
            <div class="absolute inset-0 bg-zinc-950"></div>
            <div class="relative mx-auto flex min-h-[520px] max-w-7xl items-end px-6 pb-16">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-yellow-400">ETB Lodz</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-black uppercase leading-tight md:text-6xl">Oficjalna strona klubu</h1>
                    <p class="mt-5 max-w-2xl text-base font-semibold text-zinc-200 md:text-lg">Wkrotce pojawia sie tutaj najnowsze informacje, galerie i zapowiedzi.</p>
                </div>
            </div>
        @endforelse

        @if($heroNews->count() > 1)
            <div class="absolute bottom-8 right-6 flex gap-3 md:right-20">
                @foreach($heroNews as $item)
                    <button type="button" class="h-1.5 w-16 rounded-full bg-white/35 transition" :class="{ 'bg-yellow-400': active === {{ $loop->index }} }" @click="active = {{ $loop->index }}">
                        <span class="sr-only">{{ $item->title }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <section class="bg-zinc-100 py-14">
        <div class="mx-auto max-w-7xl px-6">
            <p class="text-xs font-black uppercase tracking-[0.28em] text-zinc-600">Mecze pierwszej druzyny</p>
            <h2 class="mt-2 text-4xl font-black uppercase italic text-zinc-950">Terminarz</h2>
            <div class="mt-8 grid gap-5 lg:grid-cols-3">
                @include('pages.partials.match-public-card', ['match' => $lastFinishedMatch, 'label' => 'Ostatni mecz'])

                @foreach($upcomingMatches as $match)
                    @include('pages.partials.match-public-card', ['match' => $match, 'label' => $loop->first ? 'Najbliższy mecz' : 'Kolejny mecz'])
                @endforeach

                @if(! $lastFinishedMatch && $upcomingMatches->isEmpty())
                    <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-zinc-600">Terminarz zostanie uzupełniony po dodaniu meczów w panelu.</div>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-white py-14">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-yellow-600">Aktualności</p>
                    <h2 class="mt-2 text-4xl font-black uppercase text-zinc-950">Z życia drużyny</h2>
                </div>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 rounded bg-zinc-950 px-5 py-3 text-sm font-black uppercase text-white hover:bg-yellow-400 hover:text-black">Wszystkie aktualności <span aria-hidden="true">→</span></a>
            </div>

            <div class="mt-8 grid gap-5 lg:grid-cols-2">
                @foreach($featuredArticles as $item)
                    @php($image = $articleImage($item))
                    <a href="{{ route('news.show', $item) }}" class="group overflow-hidden rounded-lg bg-zinc-950 text-white">
                        <div class="aspect-[16/9] bg-zinc-900">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-yellow-400">{{ $item->publish_at?->format('d.m.Y') ?? $item->created_at?->format('d.m.Y') }}</p>
                            <h3 class="mt-3 text-2xl font-black">{{ $item->title }}</h3>
                            <p class="mt-3 text-sm text-zinc-300">{{ $item->excerpt ?: Str::limit(strip_tags($item->content), 130) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                @foreach($moreArticles as $item)
                    <a href="{{ route('news.show', $item) }}" class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-yellow-400">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">{{ $item->publish_at?->format('d.m.Y') ?? $item->created_at?->format('d.m.Y') }}</p>
                        <h3 class="mt-3 text-lg font-black text-zinc-950">{{ $item->title }}</h3>
                        <p class="mt-3 text-sm text-zinc-600">{{ $item->excerpt ?: Str::limit(strip_tags($item->content), 95) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-zinc-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-yellow-400">Pierwsza piątka</p>
                    <h2 class="mt-2 text-4xl font-black uppercase">Skład ETB</h2>
                </div>
                <a href="{{ route('team.players') }}" class="inline-flex items-center gap-2 rounded bg-yellow-400 px-5 py-3 text-sm font-black uppercase text-black hover:bg-white">Pełny skład <span aria-hidden="true">→</span></a>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                @forelse($startingFive as $player)
                    <a href="{{ route('team.players.show', $player) }}" class="group overflow-hidden rounded-lg bg-zinc-900">
                        <div class="aspect-[4/5] bg-zinc-800">
                            @if($player->photo_path)
                                <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->full_name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-sm font-black text-yellow-400">#{{ $player->number }}</p>
                            <h3 class="mt-1 text-xl font-black">{{ $player->full_name }}</h3>
                            <p class="mt-1 text-sm text-zinc-400">{{ $player->positionLabel() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-zinc-700 p-8 text-zinc-400 sm:col-span-2 lg:col-span-5">Zaznacz zawodników jako pierwszą piątkę w panelu admina, a pojawią się tutaj.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-yellow-400 py-12 text-black">
        <a href="{{ route('academy') }}" class="mx-auto flex max-w-7xl flex-col gap-6 px-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.28em]">Akademia ETB</p>
                <h2 class="mt-2 text-4xl font-black uppercase md:text-5xl">Kochasz koszykowke? Dołącz do nas</h2>
            </div>
            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-black text-3xl font-black text-yellow-400" aria-hidden="true">→</span>
        </a>
    </section>
</div>
@endsection
