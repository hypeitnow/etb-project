@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="{{ $tournament->type === \App\Models\ThreeXThreeTournament::TYPE_ORGANIZED ? route('three-x-three.tournaments.index') : route('schedule.3x3') }}" class="text-sm font-semibold text-yellow-400 hover:text-yellow-300">Wroc do listy turniejow</a>

    <article class="mt-8 overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950 shadow-2xl">
        @if ($tournament->image_path)
            <img src="{{ asset('storage/'.$tournament->image_path) }}" alt="{{ $tournament->name }}" class="aspect-[16/7] w-full object-cover">
        @endif
        <div class="p-6 sm:p-10">
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">{{ $tournament->date?->format('d.m.Y') }} / {{ $tournament->location }}</p>
            <h1 class="mt-3 text-4xl font-black text-white">{{ $tournament->name }}</h1>

            @if ($tournament->description)
                <p class="mt-5 text-lg leading-8 text-zinc-300">{{ $tournament->description }}</p>
            @endif

            @if ($tournament->categories->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($tournament->categories as $category)
                        <span class="rounded-full bg-yellow-400/10 px-3 py-1.5 text-sm font-black text-yellow-300 ring-1 ring-yellow-400/30">{{ $category->category->label() }}</span>
                    @endforeach
                </div>
            @endif

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded border border-zinc-800 bg-zinc-900 p-4">
                    <dt class="text-xs uppercase tracking-widest text-zinc-500">Status</dt>
                    <dd class="mt-1 font-bold text-white">{{ $tournament->status === \App\Models\ThreeXThreeTournament::STATUS_FINISHED ? 'Zakonczony' : 'Nadchodzacy' }}</dd>
                </div>
                <div class="rounded border border-zinc-800 bg-zinc-900 p-4">
                    <dt class="text-xs uppercase tracking-widest text-zinc-500">Organizator</dt>
                    <dd class="mt-1 font-bold text-white">{{ $tournament->organizer ?: '-' }}</dd>
                </div>
            </dl>

            @if ($tournament->type === \App\Models\ThreeXThreeTournament::TYPE_ORGANIZED)
                <div class="mt-8 border-t border-zinc-800 pt-8">
                    <h2 class="text-2xl font-black text-white">Zapisy</h2>

                    @if ($tournament->registration_mode === \App\Models\ThreeXThreeTournament::REGISTRATION_EXTERNAL && $tournament->registration_url)
                        <a href="{{ $tournament->registration_url }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300">Zapisy pod linkiem</a>
                    @elseif ($tournament->acceptsInternalRegistrations())
                        @auth
                            <form method="POST" action="{{ route('three-x-three.tournaments.teams.store', $tournament) }}" enctype="multipart/form-data" class="mt-5 grid gap-4 rounded border border-zinc-800 bg-zinc-900 p-5 sm:grid-cols-2">
                                @csrf
                                <label class="block">
                                    <span class="text-sm font-bold text-zinc-200">Nazwa druzyny</span>
                                    <input name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded border-zinc-700 bg-zinc-950 text-white">
                                    @error('name')
                                        <span class="mt-1 block text-sm text-red-300">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block">
                                    <span class="text-sm font-bold text-zinc-200">Kategoria</span>
                                    <select name="category" required class="mt-1 w-full rounded border-zinc-700 bg-zinc-950 text-white">
                                        @foreach ($tournament->categories as $category)
                                            <option value="{{ $category->category->value }}" @selected(old('category') === $category->category->value)>{{ $category->category->label() }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="block sm:col-span-2">
                                    <span class="text-sm font-bold text-zinc-200">Logo druzyny</span>
                                    <input name="logo" type="file" accept="image/*" class="mt-1 w-full rounded border border-zinc-700 bg-zinc-950 text-sm text-white file:mr-4 file:border-0 file:bg-zinc-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                                </label>

                                <div class="sm:col-span-2">
                                    <span class="text-sm font-bold text-zinc-200">Sklad druzyny</span>
                                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                        @for ($i = 0; $i < (int) $tournament->team_size; $i++)
                                            <input name="players[{{ $i }}][name]" required value="{{ old("players.$i.name") }}" placeholder="Zawodnik {{ $i + 1 }}" class="w-full rounded border-zinc-700 bg-zinc-950 text-white">
                                        @endfor
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <button class="rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300">Zglos druzyne</button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="mt-4 inline-flex rounded bg-yellow-400 px-5 py-3 text-sm font-black text-black hover:bg-yellow-300">Zaloguj sie, aby zapisac druzyne</a>
                        @endauth
                    @else
                        <p class="mt-3 text-zinc-400">Zapisy nie sa obecnie dostepne.</p>
                    @endif
                </div>
            @endif

            @if ($tournament->teams->isNotEmpty())
                <div class="mt-8 border-t border-zinc-800 pt-8">
                    <h2 class="text-2xl font-black text-white">Zgloszone druzyny</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($tournament->teams as $team)
                            <div class="rounded border border-zinc-800 bg-zinc-900 p-4">
                                <div class="flex items-center gap-3">
                                    @if ($team->logo_path)
                                        <img src="{{ asset('storage/'.$team->logo_path) }}" alt="{{ $team->name }}" class="h-12 w-12 rounded bg-white object-contain">
                                    @endif
                                    <div>
                                        <p class="font-black text-white">{{ $team->name }}</p>
                                        <p class="text-xs font-bold uppercase text-yellow-300">{{ $team->category->label() }}</p>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-zinc-400">{{ $team->players->pluck('name')->join(', ') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($tournament->matches->isNotEmpty())
                <div class="mt-8 border-t border-zinc-800 pt-8">
                    <h2 class="text-2xl font-black text-white">Przebieg turnieju</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($tournament->matches->sortBy([['stage', 'asc'], ['sort_order', 'asc']]) as $match)
                            <div class="grid gap-3 rounded border border-zinc-800 bg-zinc-900 p-4 sm:grid-cols-[1fr_auto_1fr] sm:items-center">
                                <p class="font-bold text-white">{{ $match->teamOne?->name ?? 'Druzyna 1' }}</p>
                                <p class="text-center text-xl font-black text-yellow-300">{{ $match->team_one_score ?? '-' }}:{{ $match->team_two_score ?? '-' }}</p>
                                <p class="font-bold text-white sm:text-right">{{ $match->teamTwo?->name ?? 'Druzyna 2' }}</p>
                                <p class="text-xs uppercase tracking-widest text-zinc-500 sm:col-span-3">{{ $match->stage === 'playoff' ? 'Playoff' : ($match->group?->name ?? 'Grupa') }}{{ $match->round_label ? ' - '.$match->round_label : '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>
</section>
@endsection
