@php
    $sectionId = $sectionId ?? null;
    $headingLevel = $headingLevel ?? 1;
@endphp

<section @if($sectionId) id="{{ $sectionId }}" @endif class="scroll-mt-28" x-data="{ tab: @js($selectedView) }">
    <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">ETB Basket</p>
            @if ($headingLevel === 1)
                <h1 class="mt-2 text-4xl font-black text-white">Terminarz</h1>
            @else
                <h2 class="mt-2 text-3xl font-black text-white">Terminarz</h2>
            @endif
        </div>

        <form method="GET" action="{{ route('schedule') }}" class="grid gap-3 rounded-lg border border-zinc-800 bg-zinc-950 p-4 sm:grid-cols-3">
            <select name="season" class="rounded border-zinc-700 bg-zinc-900 text-sm text-white">
                <option value="">Wszystkie sezony</option>
                @foreach ($seasons as $season)
                    <option value="{{ $season }}" @selected($selectedSeason === $season)>{{ $season }}</option>
                @endforeach
            </select>
            <select name="view" class="rounded border-zinc-700 bg-zinc-900 text-sm text-white">
                <option value="all" @selected($selectedView === 'all')>Wszystkie</option>
                <option value="upcoming" @selected($selectedView === 'upcoming')>Nadchodzące</option>
                <option value="finished" @selected($selectedView === 'finished')>Zakończone</option>
            </select>
            <select name="sort" class="rounded border-zinc-700 bg-zinc-900 text-sm text-white">
                <option value="asc" @selected($selectedSort === 'asc')>Od najbliższych</option>
                <option value="desc" @selected($selectedSort === 'desc')>Od najnowszych</option>
            </select>
            <button class="rounded bg-yellow-400 px-4 py-2 text-sm font-black text-black transition hover:bg-yellow-300 sm:col-span-3">Filtruj terminarz</button>
        </form>
    </div>

    <div class="mb-8 flex flex-wrap gap-2">
        @foreach ([['all', 'Wszystkie'], ['upcoming', 'Nadchodzące'], ['finished', 'Zakończone']] as [$value, $label])
            <button type="button" @click="tab = '{{ $value }}'" :class="tab === '{{ $value }}' ? 'bg-yellow-400 text-black' : 'border-zinc-700 bg-zinc-950 text-white'" class="rounded border px-4 py-2 text-sm font-bold transition">{{ $label }}</button>
        @endforeach
    </div>

    <div class="space-y-12">
        <section x-show="tab === 'all' || tab === 'upcoming'">
            <h3 class="mb-5 border-l-4 border-yellow-400 pl-4 text-2xl font-black text-white">Mecze nadchodzące</h3>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($upcomingMatches as $match)
                    @include('pages.partials.match-public-card', ['match' => $match])
                @empty
                    <p class="rounded border border-dashed border-zinc-700 p-6 text-zinc-400">Brak nadchodzących meczów.</p>
                @endforelse
            </div>
        </section>

        <section x-show="tab === 'all' || tab === 'finished'">
            <h3 class="mb-5 border-l-4 border-yellow-400 pl-4 text-2xl font-black text-white">Mecze zakończone</h3>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($finishedMatches as $match)
                    @include('pages.partials.match-public-card', ['match' => $match])
                @empty
                    <p class="rounded border border-dashed border-zinc-700 p-6 text-zinc-400">Brak zakończonych meczów.</p>
                @endforelse
            </div>
        </section>
    </div>
</section>
