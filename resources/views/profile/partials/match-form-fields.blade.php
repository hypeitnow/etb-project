@php
    $match = $match ?? null;
    $isHome = filter_var(old('is_home', $match?->is_home ?? true), FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-gray-700">Przeciwnik</span>
        <input name="opponent_name"
               type="text"
               required
               value="{{ old('opponent_name', $match?->opponent_name) }}"
               class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Data meczu</span>
        <input name="match_date"
               type="datetime-local"
               required
               value="{{ old('match_date', $match?->match_date?->format('Y-m-d\TH:i')) }}"
               class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block md:col-span-2">
        <span class="text-sm font-medium text-gray-700">Lokalizacja</span>
        <input name="location"
               type="text"
               required
               value="{{ old('location', $match?->location) }}"
               class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="flex items-center gap-2 rounded border border-gray-200 p-3 md:col-span-2">
        <input name="is_home"
               type="checkbox"
               value="1"
               class="rounded border-gray-300 text-indigo-600"
               @checked($isHome)>
        <span class="text-sm font-medium text-gray-700">Mecz u siebie</span>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Nasze punkty</span>
        <input name="our_score"
               type="number"
               min="0"
               max="999"
               value="{{ old('our_score', $match?->our_score) }}"
               class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Punkty przeciwnika</span>
        <input name="opponent_score"
               type="number"
               min="0"
               max="999"
               value="{{ old('opponent_score', $match?->opponent_score) }}"
               class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block md:col-span-2">
        <span class="text-sm font-medium text-gray-700">Logo przeciwnika</span>
        <input name="opponent_logo"
               type="file"
               accept="image/*"
               class="mt-1 w-full rounded border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
        @if ($match?->opponent_logo)
            <span class="mt-1 block text-xs text-gray-500">Obecne logo zostanie zachowane, jeśli nie wybierzesz nowego pliku.</span>
        @endif
    </label>
</div>
