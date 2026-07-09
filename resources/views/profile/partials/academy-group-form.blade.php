@php
    $academyGroup = $academyGroup ?? null;
    $selectedColor = old('color', $academyGroup?->color ?? '#ef4444');
    $academyColorPresets = [
        '#ef4444',
        '#f97316',
        '#22c55e',
        '#14b8a6',
        '#3b82f6',
        '#6366f1',
        '#a855f7',
        '#ec4899',
        '#64748b',
        '#92400e',
    ];
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-bold text-slate-700">Nazwa sekcji</label>
        <input name="name" required value="{{ old('name', $academyGroup?->name) }}" placeholder="Juniorzy U17M" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-bold text-slate-700">Kod grupy</label>
        <input name="code" required value="{{ old('code', $academyGroup?->code) }}" placeholder="U17M" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div class="md:col-span-2" x-data="{ color: @js($selectedColor), customOpen: !@js(in_array($selectedColor, $academyColorPresets, true)) }">
        <label class="text-sm font-bold text-slate-700">Kolor kafelków</label>
        <input type="hidden" name="color" x-model="color">

        <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3">
            <div class="flex flex-wrap gap-2">
                @foreach ($academyColorPresets as $preset)
                    <button
                        type="button"
                        data-academy-color-preset="{{ $preset }}"
                        class="h-9 w-9 rounded-lg border-2 transition"
                        :class="color === '{{ $preset }}' ? 'border-slate-950 ring-2 ring-yellow-400' : 'border-white hover:border-slate-400'"
                        style="background-color: {{ $preset }}"
                        @click="color = '{{ $preset }}'; customOpen = false"
                        aria-label="Wybierz kolor {{ $preset }}"
                    ></button>
                @endforeach
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-yellow-50" @click="customOpen = !customOpen">
                    Wybierz inny kolor
                </button>
            </div>

            <div x-show="customOpen" x-cloak class="mt-3 rounded-lg border border-yellow-200 bg-white p-3">
                <label class="text-xs font-bold uppercase text-slate-500">Kolor niestandardowy</label>
                <div class="mt-2 flex items-center gap-3">
                    <input type="color" x-model="color" class="h-11 w-20 rounded-lg border-slate-300">
                    <p class="text-sm text-slate-600">Żółty jest zarezerwowany dla seniorów i można go wybrać tylko tutaj. Czarny i biały są niedozwolone.</p>
                </div>
            </div>
        </div>
    </div>
    <div>
        <label class="text-sm font-bold text-slate-700">Kolejność</label>
        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $academyGroup?->sort_order ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
</div>

<div>
    <label class="text-sm font-bold text-slate-700">Opis sekcji</label>
    <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Opis grupy widoczny na stronie sekcji.">{{ old('description', $academyGroup?->description) }}</textarea>
</div>

<label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-yellow-500" @checked(old('is_active', $academyGroup?->is_active ?? true))>
    Sekcja widoczna publicznie
</label>
