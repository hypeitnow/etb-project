@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8" x-data="newsLightbox()">
    <a href="{{ route('news.index') }}" class="text-sm font-semibold text-yellow-400 hover:text-yellow-300">← Wróć do aktualności</a>

    @if ($isPreview ?? false)
        <div class="mt-6 rounded border border-yellow-400/50 bg-yellow-400/10 p-4 text-sm font-semibold text-yellow-200">
            Podgląd administratora. Ten artykuł nie musi być jeszcze widoczny publicznie.
        </div>
    @endif

    <article class="mt-8 overflow-hidden rounded-lg border border-zinc-800 bg-zinc-950 shadow-2xl">
        @if ($news->main_image_path)
            <img src="{{ asset('storage/'.$news->main_image_path) }}" alt="{{ $news->title }}" class="aspect-[16/7] w-full object-cover">
        @endif

        <div class="p-6 sm:p-10">
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">{{ ($news->publish_at ?? $news->created_at)?->format('d.m.Y H:i') }}</p>
            <h1 class="mt-3 text-4xl font-black text-white">{{ $news->title }}</h1>
            <div class="prose prose-invert mt-8 max-w-none text-zinc-300">{!! nl2br(e($news->content)) !!}</div>

            @if ($news->images->isNotEmpty())
                <div class="mt-10">
                    <h2 class="mb-4 text-2xl font-black text-white">Galeria</h2>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                        @foreach ($news->images->take(5) as $image)
                            <button type="button" class="relative aspect-square overflow-hidden rounded border border-zinc-800" @click="open(@js(asset('storage/'.$image->path)))">
                                <img src="{{ asset('storage/'.$image->path) }}" alt="Zdjęcie galerii" class="h-full w-full object-cover">
                                @if ($loop->last && $news->images->count() > 5)
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/70 text-3xl font-black text-white">+{{ $news->images->count() - 5 }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>

    <div x-show="image" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4" @click="close()">
        <img :src="image" alt="Zdjęcie galerii" class="max-h-[90vh] max-w-full rounded bg-white object-contain">
    </div>
</section>
@endsection
