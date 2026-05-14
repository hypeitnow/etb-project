@php
    use App\Enums\ThreeXThreeCategory;
    use App\Models\ThreeXThreeTournament;

    $tournament = $tournament ?? null;
    $selectedCategories = collect(old('categories', $tournament?->categories?->map(fn ($category) => $category->category->value)->all() ?? []));
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-gray-700">Nazwa</span>
        <input name="name" required value="{{ old('name', $tournament?->name) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Data</span>
        <input name="date" type="date" required value="{{ old('date', $tournament?->date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Lokalizacja</span>
        <input name="location" required value="{{ old('location', $tournament?->location) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Status</span>
        <select name="status" required class="mt-1 w-full rounded border-gray-300">
            <option value="{{ ThreeXThreeTournament::STATUS_UPCOMING }}" @selected(old('status', $tournament?->status ?? ThreeXThreeTournament::STATUS_UPCOMING) === ThreeXThreeTournament::STATUS_UPCOMING)>Nadchodzący</option>
            <option value="{{ ThreeXThreeTournament::STATUS_FINISHED }}" @selected(old('status', $tournament?->status) === ThreeXThreeTournament::STATUS_FINISHED)>Zakończony</option>
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Organizator</span>
        <input name="organizer" value="{{ old('organizer', $tournament?->organizer) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Grafika turnieju</span>
        <input name="image" type="file" accept="image/*" class="mt-1 w-full rounded border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
    </label>

    <div class="md:col-span-2">
        <span class="text-sm font-medium text-gray-700">Kategorie</span>
        <div class="mt-2 space-y-4 rounded border border-gray-200 bg-gray-50 p-4">
            @foreach (ThreeXThreeCategory::groupedOptions() as $group => $categories)
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-gray-500">{{ $group }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $value => $label)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="categories[]" value="{{ $value }}" class="peer sr-only" @checked($selectedCategories->contains($value))>
                                <span class="block rounded-full border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 transition peer-checked:border-yellow-500 peer-checked:bg-yellow-100 peer-checked:text-yellow-900">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <label class="block md:col-span-2">
        <span class="text-sm font-medium text-gray-700">Opis</span>
        <textarea name="description" rows="4" class="mt-1 w-full rounded border-gray-300">{{ old('description', $tournament?->description) }}</textarea>
    </label>
</div>
