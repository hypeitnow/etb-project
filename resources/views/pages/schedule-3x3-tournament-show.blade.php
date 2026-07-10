@extends('layouts.app')

@php
    use App\Models\ThreeXThreeTournament;
    use App\Models\ThreeXThreeTournamentMatch;

    $playoffMatches = $tournament->matches
        ->where('stage', ThreeXThreeTournamentMatch::STAGE_PLAYOFF)
        ->sortBy('sort_order')
        ->groupBy('round_label');
    $finalMatch = $tournament->matches
        ->where('stage', ThreeXThreeTournamentMatch::STAGE_PLAYOFF)
        ->where('round_label', 'Finał')
        ->sortByDesc('played_at')
        ->first();
    $championId = $finalMatch?->winnerId();
    $champion = $championId ? $tournament->teams->firstWhere('id', $championId) : null;
@endphp

@section('content')
<section class="bg-slate-950 py-10 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ $tournament->type === ThreeXThreeTournament::TYPE_ORGANIZED ? route('three-x-three.tournaments.index') : route('schedule.3x3') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-yellow-300 hover:text-yellow-200">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Wróć do listy turniejów
        </a>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-300">{{ $tournament->date?->format('d.m.Y') }} / {{ $tournament->location }}</p>
                <h1 class="mt-3 text-4xl font-black sm:text-5xl">{{ $tournament->name }}</h1>
                @if ($tournament->description)
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-300">{{ $tournament->description }}</p>
                @endif
            </div>

            <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-slate-400">Status</dt>
                        <dd class="mt-1 font-black">{{ $tournament->status === ThreeXThreeTournament::STATUS_FINISHED ? 'Zakończony' : 'Nadchodzący' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-slate-400">Organizator</dt>
                        <dd class="mt-1 font-black">{{ $tournament->organizer ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-slate-400">Drużyny</dt>
                        <dd class="mt-1 font-black">{{ $tournament->teams->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-slate-400">Mecze</dt>
                        <dd class="mt-1 font-black">{{ $tournament->matches->count() }}</dd>
                    </div>
                </dl>
                @if ($champion)
                    <div class="etb-tournament-champion mt-5 rounded-lg border border-yellow-300 bg-yellow-300 p-4 text-black">
                        <p class="flex items-center gap-2 text-xs font-black uppercase tracking-widest"><i data-lucide="trophy" class="h-4 w-4"></i>Zwycięzca turnieju</p>
                        <a href="{{ route('three-x-three.teams.show', $champion) }}" class="mt-1 block text-2xl font-black hover:underline">{{ $champion->name }}</a>
                    </div>
                @endif
            </div>
        </div>

        @if ($tournament->type === ThreeXThreeTournament::TYPE_ORGANIZED)
            <div class="mt-8 rounded-lg border border-white/10 bg-white/5 p-5">
                <h2 class="text-2xl font-black">Zapisy</h2>
                @if ($tournament->registration_mode === ThreeXThreeTournament::REGISTRATION_EXTERNAL && $tournament->registration_url)
                    <a href="{{ $tournament->registration_url }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300">Zapisy pod linkiem</a>
                @elseif ($tournament->acceptsInternalRegistrations())
                    @auth
                        <form method="POST" action="{{ route('three-x-three.tournaments.teams.store', $tournament) }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
                            @csrf
                            <label class="block">
                                <span class="text-sm font-bold text-slate-200">Nazwa drużyny</span>
                                <input name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded border-slate-700 bg-slate-950 text-white">
                            </label>
                            <label class="block">
                                <span class="text-sm font-bold text-slate-200">Kategoria</span>
                                <select name="category" required class="mt-1 w-full rounded border-slate-700 bg-slate-950 text-white">
                                    @foreach ($tournament->categories as $category)
                                        <option value="{{ $category->category->value }}" @selected(old('category') === $category->category->value)>{{ $category->category->label() }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block sm:col-span-2">
                                <span class="text-sm font-bold text-slate-200">Logo drużyny</span>
                                <input name="logo" type="file" accept="image/*" class="mt-1 w-full rounded border border-slate-700 bg-slate-950 text-sm text-white file:mr-4 file:border-0 file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                            </label>
                            <div class="sm:col-span-2">
                                <span class="text-sm font-bold text-slate-200">Skład drużyny</span>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    @for ($i = 0; $i < (int) $tournament->team_size; $i++)
                                        <input name="players[{{ $i }}][name]" required value="{{ old("players.$i.name") }}" placeholder="Zawodnik {{ $i + 1 }}" class="w-full rounded border-slate-700 bg-slate-950 text-white">
                                    @endfor
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <button class="inline-flex items-center gap-2 rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300"><i data-lucide="send" class="h-4 w-4"></i>Zgłoś drużynę</button>
                            </div>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="mt-4 inline-flex items-center gap-2 rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300"><i data-lucide="log-in" class="h-4 w-4"></i>Zaloguj się, aby zapisać drużynę</a>
                    @endauth
                @else
                    <p class="mt-3 text-slate-400">Zapisy nie są obecnie dostępne.</p>
                @endif
            </div>
        @endif
    </div>
</section>

<section class="bg-slate-100 py-10">
    <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
        <div>
            <h2 class="text-3xl font-black text-slate-950">Grupy</h2>
            <p class="mt-2 max-w-3xl text-sm text-slate-600">W widoku grupowym widać miejsce, kosze zdobyte i stracone, bilans oraz punkty FIBA. Zielone oznaczenie pokazuje obecne miejsca awansujące.</p>
        </div>

        <div class="space-y-6">
            @forelse ($tournament->groups->sortBy('sort_order') as $group)
                @php($rows = $groupTables[$group->id] ?? [])
                <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <header class="border-b border-slate-200 px-4 py-3">
                        <h3 class="text-xl font-black text-slate-950">{{ $group->name }}</h3>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 text-left">#</th>
                                    <th class="px-3 py-3 text-left">Drużyna</th>
                                    <th class="px-3 py-3 text-center">M</th>
                                    <th class="px-3 py-3 text-center">W</th>
                                    <th class="px-3 py-3 text-center">P</th>
                                    <th class="px-3 py-3 text-center">KZ</th>
                                    <th class="px-3 py-3 text-center">KS</th>
                                    <th class="px-3 py-3 text-center">Bilans</th>
                                    <th class="px-3 py-3 text-center">Pkt</th>
                                    <th class="px-3 py-3 text-left">Forma</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($rows as $index => $row)
                                    <tr class="{{ $index < 2 ? 'etb-advance-row bg-emerald-50' : 'etb-eliminated-row' }}">
                                        <td class="border-l-4 {{ $index < 2 ? 'border-emerald-500' : 'border-slate-300' }} px-3 py-3 font-black">{{ $index + 1 }}</td>
                                        <td class="px-3 py-3">
                                            <a href="{{ route('three-x-three.teams.show', $row['team']) }}" class="font-black text-slate-950 hover:text-yellow-700">{{ $row['team']->name }}</a>
                                        </td>
                                        <td class="px-3 py-3 text-center">{{ $row['played'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $row['wins'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $row['losses'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $row['points_for'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $row['points_against'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $row['point_diff'] }}</td>
                                        <td class="px-3 py-3 text-center font-black">{{ $row['fiba_points'] }}</td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex gap-1.5">
                                                @forelse ($row['form'] as $form)
                                                    <span title="{{ $form === 'W' ? 'Wygrana' : 'Porażka' }}" class="h-2.5 w-2.5 rounded-full {{ $form === 'W' ? 'bg-emerald-500' : 'bg-rose-400' }}"></span>
                                                @empty
                                                    <span class="text-xs text-slate-400">-</span>
                                                @endforelse
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="px-4 py-5 text-slate-500">Brak drużyn w grupie.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-slate-600">Grupy nie zostały jeszcze opublikowane.</div>
            @endforelse
        </div>

        <div>
            <h2 class="text-3xl font-black text-slate-950">Drabinka turnieju</h2>
            <div class="etb-bracket mt-5">
                @forelse ($playoffMatches as $round => $matches)
                    <section class="etb-bracket-round">
                        <h3>{{ $round ?: 'Faza pucharowa' }}</h3>
                        <div class="etb-bracket-matches">
                            @foreach ($matches as $match)
                                @php($winnerId = $match->winnerId())
                                <article class="etb-bracket-match {{ $match->round_label === 'Finał' && $winnerId ? 'etb-final-complete' : '' }}">
                                    <div class="text-[11px] font-semibold text-slate-500">
                                        {{ $match->played_at?->format('d.m.Y H:i') ?? 'Termin do ustalenia' }}
                                        @if ($match->court) / {{ $match->court }} @endif
                                    </div>
                                    <div class="mt-2 space-y-1">
                                        @foreach ([['team' => $match->teamOne, 'placeholder' => $match->team_one_placeholder, 'score' => $match->team_one_score], ['team' => $match->teamTwo, 'placeholder' => $match->team_two_placeholder, 'score' => $match->team_two_score]] as $side)
                                            @php($isWinner = $side['team'] && $winnerId === $side['team']->id)
                                            <div class="etb-bracket-team {{ $isWinner ? 'is-winner' : ($match->hasResult() ? 'is-loser' : '') }}">
                                                <span class="truncate">{{ $side['team']?->name ?? $side['placeholder'] ?? 'Do ustalenia' }}</span>
                                                <strong>{{ $side['score'] ?? '-' }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-slate-600">Drabinka nie została jeszcze opublikowana.</div>
                @endforelse
            </div>
        </div>

        @if ($tournament->teams->isNotEmpty())
            <div>
                <h2 class="flex items-center gap-3 text-3xl font-black text-slate-950"><i data-lucide="users" class="h-7 w-7 text-yellow-600"></i>Drużyny</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tournament->teams->sortBy('name') as $team)
                        <a href="{{ route('three-x-three.teams.show', $team) }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-yellow-400 hover:bg-yellow-50">
                            <p class="text-lg font-black text-slate-950">{{ $team->name }}</p>
                            <p class="mt-1 text-xs font-bold uppercase text-yellow-700">{{ $team->category->label() }} / {{ $team->group?->name ?? 'Bez grupy' }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ $team->players->pluck('name')->join(', ') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
