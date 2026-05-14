@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="{{ route('schedule.3x3') }}" class="text-sm font-semibold text-yellow-400 hover:text-yellow-300">← Wróć do terminarza 3x3</a>

    <article class="mt-8 overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950 shadow-2xl">
        @if ($tournament->image_path)
            <img src="{{ asset('storage/'.$tournament->image_path) }}" alt="{{ $tournament->name }}" class="aspect-[16/7] w-full object-cover">
        @endif
        <div class="p-6 sm:p-10">
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">{{ $tournament->date?->format('d.m.Y') }} · {{ $tournament->location }}</p>
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
                    <dd class="mt-1 font-bold text-white">{{ $tournament->status === \App\Models\ThreeXThreeTournament::STATUS_FINISHED ? 'Zakończony' : 'Nadchodzący' }}</dd>
                </div>
                <div class="rounded border border-zinc-800 bg-zinc-900 p-4">
                    <dt class="text-xs uppercase tracking-widest text-zinc-500">Organizator</dt>
                    <dd class="mt-1 font-bold text-white">{{ $tournament->organizer ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </article>
</section>
@endsection
