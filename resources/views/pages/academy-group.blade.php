@extends('layouts.app')

@section('content')
<section class="bg-yellow-400 text-black">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('academy') }}" class="inline-flex items-center gap-2 rounded-lg bg-black px-4 py-2 text-sm font-bold text-yellow-400 hover:bg-zinc-900">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Wróć do Akademii
        </a>

        <div class="mt-6 rounded-lg border-2 border-black bg-black p-6 text-yellow-400" style="--group-color: {{ $group->color }}">
            <span class="rounded-full px-4 py-1 text-sm font-black text-slate-950" style="background-color: var(--group-color)">{{ $group->code }}</span>
            <h1 class="mt-4 text-3xl font-black sm:text-4xl">{{ $group->name }}</h1>
            @if ($group->description)
                <p class="mt-3 max-w-3xl text-yellow-100">{{ $group->description }}</p>
            @endif
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_22rem]">
            <div class="space-y-6">
                <section class="rounded-lg border-2 border-black bg-white p-5 text-slate-950">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black">Najbliższe treningi</h2>
                            <p class="text-sm text-slate-600">Terminy, prowadzący i miejsca zajęć tej grupy.</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($trainings as $training)
                            <article class="rounded-lg border border-slate-200 p-4 {{ $training->isCancelled() ? 'bg-slate-100' : 'bg-white' }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-500">{{ $training->starts_at->format('d.m.Y') }} · {{ $training->timeRange() }}</p>
                                        <h3 class="text-lg font-black {{ $training->isCancelled() ? 'line-through decoration-2' : '' }}">{{ $training->title ?: 'Trening' }}</h3>
                                        <p class="mt-1 text-sm text-slate-600">{{ $training->location ?: 'Miejsce do ustalenia' }} · {{ $training->trainer_name ?: 'Trener do ustalenia' }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $training->isCancelled() ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $training->statusLabel() }}</span>
                                </div>
                                @if ($training->description)
                                    <p class="mt-3 text-sm text-slate-700">{{ $training->description }}</p>
                                @endif
                                @if ($training->isCancelled())
                                    <p class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-800">Powód odwołania: {{ $training->cancelled_reason }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak najbliższych treningów tej grupy.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border-2 border-black bg-white p-5 text-slate-950">
                    <h2 class="text-xl font-black">Komunikaty</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($group->messages as $message)
                            <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-500">{{ $message->published_at?->format('d.m.Y H:i') }}</p>
                                <h3 class="mt-1 font-black">{{ $message->title }}</h3>
                                <p class="mt-2 text-sm text-slate-700">{{ $message->body }}</p>
                            </article>
                        @empty
                            <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak aktualnych komunikatów.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-4">
                <section class="rounded-lg border-2 border-black bg-black p-5 text-yellow-400">
                    <h2 class="text-xl font-black">Trenerzy</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($group->trainers as $trainer)
                            <article class="rounded-lg border border-yellow-400/40 bg-zinc-950 p-4">
                                <h3 class="font-black">{{ $trainer->name }}</h3>
                                @if ($trainer->role)
                                    <p class="text-sm font-bold text-yellow-200">{{ $trainer->role }}</p>
                                @endif
                                <div class="mt-3 space-y-1 text-sm text-yellow-100">
                                    @if ($trainer->phone)
                                        <p>Tel. {{ $trainer->phone }}</p>
                                    @endif
                                    @if ($trainer->email)
                                        <p>{{ $trainer->email }}</p>
                                    @endif
                                </div>
                                @if ($trainer->bio)
                                    <p class="mt-3 text-sm text-yellow-100">{{ $trainer->bio }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-lg border border-dashed border-yellow-400/50 p-4 text-sm text-yellow-100">Brak wpisanych trenerów.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
@endsection
