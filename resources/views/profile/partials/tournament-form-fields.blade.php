@php
    use App\Enums\ThreeXThreeCategory;
    use App\Models\ThreeXThreeTournament;

    $tournament = $tournament ?? null;
    $selectedCategories = collect(old('categories', $tournament?->categories?->map(fn ($category) => $category->category->value)->all() ?? []));
    $selectedType = old('type', $tournament?->type ?? ThreeXThreeTournament::TYPE_PARTICIPATING);
    $selectedRegistrationMode = old('registration_mode', $tournament?->registration_mode ?? ThreeXThreeTournament::REGISTRATION_NONE);
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
        <span class="text-sm font-medium text-gray-700">Typ turnieju</span>
        <select name="type" required class="mt-1 w-full rounded border-gray-300">
            <option value="{{ ThreeXThreeTournament::TYPE_PARTICIPATING }}" @selected($selectedType === ThreeXThreeTournament::TYPE_PARTICIPATING)>Turniej, w ktorym gramy</option>
            <option value="{{ ThreeXThreeTournament::TYPE_ORGANIZED }}" @selected($selectedType === ThreeXThreeTournament::TYPE_ORGANIZED)>Turniej organizowany przez ETB</option>
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Tryb zapisow</span>
        <select name="registration_mode" required class="mt-1 w-full rounded border-gray-300">
            <option value="{{ ThreeXThreeTournament::REGISTRATION_NONE }}" @selected($selectedRegistrationMode === ThreeXThreeTournament::REGISTRATION_NONE)>Brak zapisow</option>
            <option value="{{ ThreeXThreeTournament::REGISTRATION_EXTERNAL }}" @selected($selectedRegistrationMode === ThreeXThreeTournament::REGISTRATION_EXTERNAL)>Zapisy pod linkiem</option>
            <option value="{{ ThreeXThreeTournament::REGISTRATION_INTERNAL }}" @selected($selectedRegistrationMode === ThreeXThreeTournament::REGISTRATION_INTERNAL)>Zapisy na stronie ETB</option>
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Link do zapisow</span>
        <input name="registration_url" type="url" value="{{ old('registration_url', $tournament?->registration_url) }}" placeholder="https://play.fiba3x3.com/..." class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Liczba zawodnikow w druzynie</span>
        <input name="team_size" type="number" min="2" max="12" value="{{ old('team_size', $tournament?->team_size) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
        <input name="registration_enabled" type="checkbox" value="1" class="rounded border-gray-300 text-yellow-500" @checked(old('registration_enabled', $tournament?->registration_enabled ?? false))>
        <span class="text-sm font-medium text-gray-700">Udostepnij zapisy na stronie</span>
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
