@php
    use App\Models\MatchGame;

    $logo = $match?->opponent_logo ?: $match?->opponent?->logo_path;
@endphp

@if($match)
<a href="{{ route('schedule.matches.show', $match) }}" class="group rounded-lg border border-zinc-800 bg-zinc-950 p-5 shadow-xl transition hover:-translate-y-1 hover:border-yellow-400/70">
    @isset($label)
        <p class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-yellow-400">{{ $label }}</p>
    @endisset
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded bg-white p-2">
                @if ($logo)
                    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $match->opponent_name }}" class="max-h-full max-w-full object-contain">
                @else
                    <span class="text-xs font-black text-zinc-500">LOGO</span>
                @endif
            </div>
            <div>
                <h3 class="text-xl font-black text-white">{{ $match->opponent_name }}</h3>
                <p class="text-sm font-semibold text-zinc-400">{{ $match->is_home ? 'Domowy' : 'Wyjazdowy' }} · {{ $match->location }}</p>
            </div>
        </div>
        <span class="rounded bg-zinc-900 px-2 py-1 text-xs font-bold uppercase text-yellow-400">{{ $match->status === MatchGame::STATUS_FINISHED ? 'Zakończony' : 'Nadchodzący' }}</span>
    </div>

    <div class="mt-6 flex items-end justify-between gap-4">
        <div>
            <p class="text-sm uppercase tracking-widest text-zinc-500">{{ $match->match_date?->format('d.m.Y') }}</p>
            <p class="mt-1 text-2xl font-black text-white">{{ $match->match_date?->format('H:i') }}</p>
        </div>

        @if ($match->status === MatchGame::STATUS_FINISHED)
            <div class="text-right">
                <p class="text-3xl font-black {{ $match->isWin() ? 'text-emerald-400' : 'text-red-400' }}">{{ $match->our_score }}:{{ $match->opponent_score }}</p>
                <p class="text-xs font-bold uppercase tracking-widest {{ $match->isWin() ? 'text-emerald-400' : 'text-red-400' }}">{{ $match->isWin() ? 'Zwycięstwo' : 'Porażka' }}</p>
            </div>
        @elseif ($match->ticketSalesActive())
            <span class="rounded bg-yellow-400 px-3 py-2 text-sm font-black text-black">Kup bilety</span>
        @endif
    </div>
</a>
@endif
