@php
    $player = $player ?? null;
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-gray-700">Imię</span>
        <input name="first_name" required value="{{ old('first_name', $player?->first_name) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Nazwisko</span>
        <input name="last_name" required value="{{ old('last_name', $player?->last_name) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Numer</span>
        <input name="number" type="number" required min="0" max="99" value="{{ old('number', $player?->number) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Pozycja</span>
        <input name="position" required value="{{ old('position', $player?->position) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Data urodzenia</span>
        <input name="date_of_birth" type="date" required value="{{ old('date_of_birth', $player?->date_of_birth?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Zdjęcie zawodnika</span>
        <input name="photo" type="file" accept="image/*" class="mt-1 w-full rounded border border-gray-300 bg-white text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Wzrost w cm</span>
        <input name="height" type="number" min="100" max="250" value="{{ old('height', $player?->height) }}" class="mt-1 w-full rounded border-gray-300">
    </label>

    <label class="block">
        <span class="text-sm font-medium text-gray-700">Waga w kg</span>
        <input name="weight" type="number" min="40" max="200" value="{{ old('weight', $player?->weight) }}" class="mt-1 w-full rounded border-gray-300">
    </label>
</div>
