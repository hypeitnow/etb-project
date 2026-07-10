@php($message = $message ?? null)

<div>
    <label class="text-sm font-bold text-slate-700">Tytuł</label>
    <input name="title" required value="{{ old('title', $message?->title) }}" class="mt-1 w-full rounded-lg border-slate-300">
</div>

<div>
    <label class="text-sm font-bold text-slate-700">Komunikat</label>
    <textarea name="body" required rows="5" class="mt-1 w-full rounded-lg border-slate-300">{{ old('body', $message?->body) }}</textarea>
</div>

<div>
    <label class="text-sm font-bold text-slate-700">Data publikacji</label>
    <input name="published_at" type="datetime-local" value="{{ old('published_at', $message?->published_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-lg border-slate-300">
</div>

<label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
    <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-yellow-500" @checked(old('is_published', $message?->is_published ?? true))>
    Opublikowany
</label>
