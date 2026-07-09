@php($trainer = $trainer ?? null)

<div
    x-data="academyTrainerForm({
        searchUrl: @js(route('admin.academy.trainers.suggestions')),
        initialName: @js(old('name', $trainer?->name)),
        initialPhone: @js(old('phone', $trainer?->phone)),
        initialEmail: @js(old('email', $trainer?->email)),
        initialRole: @js(old('role', $trainer?->role)),
    })"
    @keydown.window.enter="closeNoticeOnKey($event)"
    @keydown.window.space="closeNoticeOnKey($event)"
>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="relative">
            <label class="text-sm font-bold text-slate-700">Imię i nazwisko</label>
            <input
                name="name"
                required
                x-model="name"
                @input.debounce.200ms="searchTrainers"
                @focus="searchTrainers"
                autocomplete="off"
                class="mt-1 w-full rounded-lg border-slate-300"
            >
            <div x-show="suggestionsOpen && suggestions.length" x-cloak class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl">
                <template x-for="suggestion in suggestions" :key="`${suggestion.name}-${suggestion.phone || ''}`">
                    <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-yellow-50" @click="selectTrainer(suggestion)">
                        <span class="block font-black" x-text="suggestion.name"></span>
                        <span class="block text-xs text-slate-500">
                            <span x-text="suggestion.role || 'Trener'"></span>
                            <span x-show="suggestion.phone"> · <span x-text="suggestion.phone"></span></span>
                        </span>
                    </button>
                </template>
            </div>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Rola</label>
            <input name="role" x-model="role" placeholder="Trener główny" class="mt-1 w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Email</label>
            <input name="email" type="email" x-model="email" class="mt-1 w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Telefon</label>
            <input name="phone" x-model="phone" class="mt-1 w-full rounded-lg border-slate-300">
            <p class="mt-1 text-xs text-slate-500">Numer będzie widoczny publicznie dopiero na stronie konkretnej grupy.</p>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Kolejność</label>
            <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $trainer?->sort_order ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-300">
        </div>
    </div>

    <div class="mt-4">
        <label class="text-sm font-bold text-slate-700">Notka</label>
        <textarea name="bio" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('bio', $trainer?->bio) }}</textarea>
    </div>

    <div x-show="noticeOpen" x-cloak x-transition class="fixed left-1/2 top-24 z-[70] w-[min(28rem,calc(100vw-2rem))] -translate-x-1/2 overflow-hidden rounded-xl border border-yellow-300 bg-slate-950 text-white shadow-2xl">
        <button type="button" class="absolute right-3 top-3 rounded p-1 text-yellow-200 hover:bg-white/10" @click="closeNotice" aria-label="Zamknij">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
        <div class="p-5 text-center">
            <p class="font-black text-yellow-300">Upewnij się, że numer kontaktowy do trenera jest poprawny zanim opublikujesz grupę w akademii.</p>
            <button type="button" class="mt-4 rounded-lg bg-yellow-400 px-5 py-2 text-sm font-black text-black hover:bg-yellow-300" @click="closeNotice">OK</button>
        </div>
        <div class="h-1 bg-white/10">
            <div class="h-full bg-yellow-400" :style="`width: ${noticeProgress}%`"></div>
        </div>
    </div>
</div>
