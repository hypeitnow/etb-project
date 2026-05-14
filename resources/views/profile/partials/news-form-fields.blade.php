@php
    $item = $item ?? null;
@endphp

<div class="space-y-4">
    <label class="block">
        <span class="text-sm font-medium text-gray-700">Tytuł</span>
        <input name="title" required value="{{ old('title', $item?->title) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Treść</span>
        <textarea name="content" required rows="8" class="mt-1 w-full rounded border-gray-300">{{ old('content', $item?->content) }}</textarea>
    </label>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-gray-700">Data publikacji</span>
            <input name="publish_at" type="datetime-local" value="{{ old('publish_at', $item?->publish_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded border-gray-300">
        </label>

        <label class="block">
            <span class="text-sm font-medium text-gray-700">Zdjęcie główne</span>
            <input name="main_image" type="file" accept="image/*" class="mt-1 w-full rounded border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Galeria zdjęć, maksymalnie 15 plików</span>
        <input name="gallery[]" type="file" accept="image/*" multiple class="mt-1 w-full rounded border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
    </label>
</div>
