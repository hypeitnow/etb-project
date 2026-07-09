@php
    $sectionId = $sectionId ?? null;
    $eyebrow = $eyebrow ?? null;
    $title = $title ?? 'Sekcja';
    $description = $description ?? 'Sekcja gotowa do dodawania treści, tekstu, zdjęć i materiałów wideo.';
    $panelTitle = $panelTitle ?? 'Panel treści';
    $panelText = $panelText ?? 'Tutaj można osadzać artykuły, galerie, listy zawodników i inne moduły.';
    $actionUrl = $actionUrl ?? null;
    $actionLabel = $actionLabel ?? null;
@endphp

<section @if($sectionId) id="{{ $sectionId }}" @endif class="scroll-mt-28">
    <div class="mb-6">
        @if ($eyebrow)
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">{{ $eyebrow }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-black text-white">{{ $title }}</h2>
        <p class="mt-3 max-w-3xl text-zinc-300">{{ $description }}</p>
    </div>

    <div class="rounded-lg border border-zinc-700 bg-zinc-900 p-6">
        <h3 class="font-semibold text-white">{{ $panelTitle }}</h3>
        <p class="mt-2 text-sm leading-6 text-zinc-400">{{ $panelText }}</p>

        @if ($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="mt-5 inline-flex rounded bg-yellow-400 px-4 py-2 text-sm font-black text-black transition hover:bg-yellow-300">
                {{ $actionLabel }}
            </a>
        @endif
    </div>
</section>
