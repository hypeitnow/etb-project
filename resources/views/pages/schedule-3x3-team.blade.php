@extends('layouts.app')

@php
    $latestTournament = $teams->sortByDesc(fn ($entry) => $entry->tournament?->date)->first()?->tournament;
@endphp

@section('content')
<section class="bg-slate-950 py-10 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ $latestTournament ? route('three-x-three.tournaments.show', $latestTournament) : route('schedule.3x3') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-yellow-300 hover:text-yellow-200">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Wróć do turnieju
        </a>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_26rem] lg:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-300">Drużyna 3x3</p>
                <h1 class="mt-3 text-4xl font-black sm:text-5xl">{{ $team->name }}</h1>
                <p class="mt-4 max-w-2xl text-slate-300">Historia obejmuje wszystkie mecze tej drużyny zapisane w bazie, z podziałem na turnieje. Najwyżej pokazujemy najnowszy zapisany skład turniejowy.</p>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                    <p class="text-3xl font-black text-yellow-300">{{ $wins }}</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-400">Wygrane</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                    <p class="text-3xl font-black text-yellow-300">{{ $losses }}</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-400">Porażki</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                    <p class="text-3xl font-black text-yellow-300">{{ $win_rate }}%</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-400">Wygranych</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-100 py-10">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[22rem_1fr] lg:px-8">
        <aside class="space-y-6">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="flex items-center gap-2 text-xl font-black text-slate-950"><i data-lucide="users" class="h-5 w-5 text-yellow-600"></i>Aktualny skład</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $latestTournament?->name ?? 'Brak turnieju' }}</p>
                <div class="mt-4 space-y-2">
                    @forelse ($latest_roster as $player)
                        <div class="rounded border border-slate-200 bg-slate-50 px-3 py-2 font-semibold text-slate-800">{{ $player->name }}</div>
                    @empty
                        <p class="text-sm text-slate-500">Brak składów w bazie.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-slate-950">Turnieje</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($teams->sortByDesc(fn ($entry) => $entry->tournament?->date) as $entry)
                        <a href="{{ route('three-x-three.tournaments.show', $entry->tournament) }}" class="block rounded border border-slate-200 bg-slate-50 px-3 py-2 transition hover:border-yellow-400 hover:bg-yellow-50">
                            <span class="block font-bold text-slate-950">{{ $entry->tournament?->name }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $entry->tournament?->date?->format('d.m.Y') }} / {{ $entry->group?->name ?? 'Bez grupy' }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Brak turniejów.</p>
                    @endforelse
                </div>
            </section>
        </aside>

        <main class="space-y-6">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-slate-950">Mecze</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($matches as $match)
                        @php
                            $isTeamOne = $teams->pluck('id')->contains($match->team_one_id);
                            $ownTeam = $isTeamOne ? $match->teamOne : $match->teamTwo;
                            $opponent = $isTeamOne ? $match->teamTwo : $match->teamOne;
                            $ownScore = $isTeamOne ? $match->team_one_score : $match->team_two_score;
                            $opponentScore = $isTeamOne ? $match->team_two_score : $match->team_one_score;
                            $won = $match->winnerId() && $teams->pluck('id')->contains($match->winnerId());
                            $lost = $match->loserId() && $teams->pluck('id')->contains($match->loserId());
                        @endphp
                        <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-yellow-700">{{ $match->tournament?->name }} / {{ $match->stage === 'playoff' ? ($match->round_label ?: 'Faza pucharowa') : ($match->group?->name ?? 'Grupa') }}</p>
                                    <h3 class="mt-1 text-lg font-black text-slate-950">{{ $ownTeam?->name ?? $team->name }} - {{ $opponent?->name ?? 'Do ustalenia' }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $match->played_at?->format('d.m.Y H:i') ?? 'Termin do ustalenia' }} @if($match->court) / {{ $match->court }} @endif</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-lg bg-white px-4 py-2 text-2xl font-black text-slate-950">{{ $ownScore ?? '-' }}:{{ $opponentScore ?? '-' }}</span>
                                    @if ($won)
                                        <span class="etb-advance-badge rounded-full bg-emerald-100 px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-700">Wygrana</span>
                                    @elseif ($lost)
                                        <span class="etb-eliminated-badge rounded-full bg-rose-100 px-3 py-1 text-xs font-black uppercase tracking-wide text-rose-700">Porażka</span>
                                    @else
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-black uppercase tracking-wide text-slate-600">Oczekuje</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 text-sm text-slate-600 md:grid-cols-2">
                                <div>
                                    <p class="font-bold text-slate-800">Skład w tym turnieju</p>
                                    <p>{{ $ownTeam?->players?->pluck('name')->join(', ') ?: 'Brak danych' }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Skład rywala</p>
                                    <p>{{ $opponent?->players?->pluck('name')->join(', ') ?: 'Brak danych' }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-lg border border-dashed border-slate-300 p-5 text-slate-600">Brak meczów tej drużyny w bazie.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</section>
@endsection
