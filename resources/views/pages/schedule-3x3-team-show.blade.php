@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('three-x-three.tournaments.show', $team->tournament) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-yellow-300 hover:text-yellow-200">
            &larr; Wróć do turnieju
        </a>
        <p class="mt-6 text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">Drużyna 3x3</p>
        <h1 class="mt-2 text-4xl font-black text-white">{{ $team->name }}</h1>
        <p class="mt-3 text-zinc-300">{{ $team->tournament?->name }} · {{ $team->category?->label() ?? $team->category }}</p>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <article class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">
            <p class="text-sm font-bold uppercase text-zinc-400">Zwycięstwa</p>
            <p class="mt-2 text-4xl font-black text-yellow-400">{{ $stats['wins'] }}</p>
        </article>
        <article class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">
            <p class="text-sm font-bold uppercase text-zinc-400">Porażki</p>
            <p class="mt-2 text-4xl font-black text-yellow-400">{{ $stats['losses'] }}</p>
        </article>
        <article class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">
            <p class="text-sm font-bold uppercase text-zinc-400">Skuteczność</p>
            <p class="mt-2 text-4xl font-black text-yellow-400">{{ rtrim(rtrim(number_format($stats['win_rate'], 1, ',', ''), '0'), ',') }}%</p>
        </article>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_24rem]">
        <section>
            <h2 class="text-2xl font-black text-white">Historia meczów</h2>
            <div class="mt-4 space-y-3">
                @forelse ($stats['matches'] as $match)
                    <article class="rounded-lg border border-zinc-800 bg-zinc-950 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-bold text-yellow-400">{{ $match->tournament?->name }}</p>
                                <h3 class="mt-1 font-black text-white">
                                    {{ $match->teamOne?->name ?? $match->team_one_placeholder ?? 'Drużyna 1' }}
                                    -
                                    {{ $match->teamTwo?->name ?? $match->team_two_placeholder ?? 'Drużyna 2' }}
                                </h3>
                                <p class="text-sm text-zinc-400">{{ $match->played_at?->format('d.m.Y H:i') ?? 'Termin do ustalenia' }}</p>
                            </div>
                            @if ($match->hasResult())
                                <p class="text-3xl font-black text-white">{{ $match->team_one_score }}:{{ $match->team_two_score }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-zinc-700 p-5 text-zinc-400">Ta drużyna nie ma jeszcze zapisanych meczów.</p>
                @endforelse
            </div>
        </section>

        <aside class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">
            <h2 class="text-2xl font-black text-white">Ostatni skład</h2>
            <div class="mt-4 space-y-2">
                @forelse ($stats['latest_roster'] as $player)
                    <p class="rounded border border-zinc-800 bg-black px-3 py-2 text-zinc-200">{{ $player->name }}</p>
                @empty
                    <p class="text-zinc-400">Brak zapisanych zawodników.</p>
                @endforelse
            </div>
        </aside>
    </div>
</section>
@endsection
