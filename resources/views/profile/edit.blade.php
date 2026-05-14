@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-12 text-gray-900"
         x-data="{ openModal: null, matchFilter: 'all', newsFilter: 'all' }"
         @keydown.escape.window="openModal = null">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Nie udało się zapisać formularza.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold">Twoje konto</h3>

                <div class="grid gap-6 md:grid-cols-2">
                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.update-password-form')
                </div>
            </section>

            @if ($isAdmin)
                <section class="rounded border border-gray-200 bg-white p-6 shadow-sm"
                         x-data="adminUserSearch(@js(route('admin.users.search')))">
                    <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Zarządzanie użytkownikami</h3>
                            <p class="text-sm text-gray-600">Lista pokazuje 5 rekordów na stronę. Role można zmienić bez wchodzenia w szczegóły konta.</p>
                        </div>

                        <div class="relative w-full lg:w-80">
                            <input type="search"
                                   x-model="query"
                                   @input.debounce.350ms="search"
                                   placeholder="Szukaj po nazwie lub e-mailu"
                                   class="w-full rounded border-gray-300 pr-10 text-sm">

                            <div x-show="results.length"
                                 x-cloak
                                 class="absolute right-0 z-30 mt-2 w-full overflow-hidden rounded border border-gray-200 bg-white shadow-lg">
                                <template x-for="user in results" :key="user.id">
                                    <button type="button"
                                            class="block w-full px-3 py-2 text-left text-sm hover:bg-yellow-50"
                                            @click="focusUser(user)">
                                        <span class="block font-semibold" x-text="user.name"></span>
                                        <span class="block text-xs text-gray-500" x-text="user.email"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="max-h-[34rem] space-y-3 overflow-y-auto pr-2">
                        @foreach ($users as $managedUser)
                            <article id="managed-user-{{ $managedUser->id }}" class="rounded border border-gray-200 bg-gray-50 p-4 transition">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-950">{{ $managedUser->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $managedUser->email }}</p>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $managedUser->role }}</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('admin.users.role.update', $managedUser) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <select name="role" class="rounded border-gray-300 text-sm">
                                                @foreach ($availableRoles as $role)
                                                    <option value="{{ $role }}" @selected($managedUser->role === $role)>
                                                        {{ match ($role) {
                                                            \App\Models\User::ROLE_ADMIN => 'Administrator',
                                                            \App\Models\User::ROLE_EMPLOYEE => 'Pracownik',
                                                            \App\Models\User::ROLE_ATHLETE => 'Zawodnik',
                                                            default => 'Kibic',
                                                        } }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button class="rounded bg-yellow-500 px-3 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                                                Zapisz
                                            </button>
                                        </form>

                                        <a href="#managed-user-{{ $managedUser->id }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-gray-100">
                                            Szczegóły
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        {{ $users->links() }}
                    </div>
                </section>
            @endif

            @if ($isAdmin || $isEmployee)
                <section class="rounded border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Mecze</h3>
                            <p class="text-sm text-gray-600">Dodawanie, edycja, usuwanie i filtrowanie meczów ETB.</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <select x-model="matchFilter" class="rounded border-gray-300 text-sm">
                                <option value="all">Wszystkie</option>
                                <option value="upcoming">Nadchodzące</option>
                                <option value="finished">Zakończone</option>
                            </select>
                            <button type="button"
                                    class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400"
                                    @click="openModal = 'match-create'">
                                Dodaj mecz
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div x-show="matchFilter === 'all' || matchFilter === 'upcoming'">
                            <h4 class="mb-3 text-base font-semibold">Mecze nadchodzące</h4>
                            <div class="space-y-3">
                                @forelse ($upcomingMatches as $match)
                                    @include('profile.partials.match-card', ['match' => $match])
                                @empty
                                    <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">Brak nadchodzących meczów.</p>
                                @endforelse
                            </div>
                        </div>

                        <div x-show="matchFilter === 'all' || matchFilter === 'finished'">
                            <h4 class="mb-3 text-base font-semibold">Mecze zakończone</h4>
                            <div class="space-y-3">
                                @forelse ($finishedMatches as $match)
                                    @include('profile.partials.match-card', ['match' => $match])
                                @empty
                                    <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">Brak zakończonych meczów.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded border border-gray-200 bg-white p-6 shadow-sm" x-data="newsLightbox()">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Aktualności</h3>
                            <p class="text-sm text-gray-600">Publikuj od razu albo ustaw datę przyszłej publikacji.</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <select x-model="newsFilter" class="rounded border-gray-300 text-sm">
                                <option value="all">Wszystkie</option>
                                <option value="published">Opublikowane</option>
                                <option value="scheduled">Zaplanowane</option>
                            </select>
                            <button type="button" class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400" @click="openModal = 'news-create'">
                                Dodaj aktualność
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach ([['items' => $publishedNews, 'type' => 'published', 'title' => 'Opublikowane'], ['items' => $scheduledNews, 'type' => 'scheduled', 'title' => 'Zaplanowane']] as $group)
                            <div x-show="newsFilter === 'all' || newsFilter === '{{ $group['type'] }}'">
                                <h4 class="mb-3 font-semibold">{{ $group['title'] }}</h4>
                                <div class="space-y-3">
                                    @forelse ($group['items'] as $item)
                                        <article class="rounded border border-gray-200 bg-gray-50 p-4">
                                            <div class="flex gap-4">
                                                @if ($item->main_image_path)
                                                    <img src="{{ asset('storage/'.$item->main_image_path) }}" alt="{{ $item->title }}" class="h-20 w-24 rounded object-cover">
                                                @endif
                                                <div class="min-w-0 flex-1">
                                                    <h5 class="font-semibold">{{ $item->title }}</h5>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $item->publish_at?->format('d.m.Y H:i') ?? 'Publikacja natychmiastowa' }}
                                                    </p>
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        @foreach ($item->images->take(5) as $image)
                                                            <button type="button" @click="open(@js(asset('storage/'.$image->path)))" class="relative">
                                                                <img src="{{ asset('storage/'.$image->path) }}" alt="Miniatura galerii" class="h-12 w-12 rounded object-cover">
                                                                @if ($loop->last && $item->images->count() > 5)
                                                                    <span class="absolute inset-0 flex items-center justify-center rounded bg-black/60 text-sm font-bold text-white">+{{ $item->images->count() - 5 }}</span>
                                                                @endif
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        <a href="{{ route('news.show', $item) }}" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-gray-100">Podgląd</a>
                                                        <button type="button" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-gray-100" @click="openModal = 'news-edit-{{ $item->id }}'">Edytuj</button>
                                                        <form method="POST" action="{{ route('news.destroy', $item) }}" onsubmit="return confirm('Czy na pewno usunąć tę aktualność?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="rounded border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">Brak aktualności.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="image" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4" @click="close()">
                        <img :src="image" alt="Zdjęcie z galerii" class="max-h-[90vh] max-w-full rounded bg-white object-contain">
                    </div>
                </section>

                <section class="rounded border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Zawodnicy</h3>
                            <p class="text-sm text-gray-600">Pełna edycja kart zawodników oraz zdjęć.</p>
                        </div>
                        <button type="button" class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400" @click="openModal = 'player-create'">
                            Dodaj zawodnika
                        </button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse ($players as $player)
                            <article class="rounded border border-gray-200 bg-gray-50 p-4">
                                <div class="flex gap-4">
                                    @if ($player->photo_path)
                                        <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->first_name }} {{ $player->last_name }}" class="h-24 w-20 rounded object-cover">
                                    @else
                                        <div class="flex h-24 w-20 items-center justify-center rounded bg-gray-200 text-xs font-semibold text-gray-500">Zdjęcie</div>
                                    @endif
                                    <div>
                                        <p class="text-2xl font-black">#{{ $player->number }}</p>
                                        <h4 class="font-semibold">{{ $player->first_name }} {{ $player->last_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $player->position }}</p>
                                        <p class="text-sm text-gray-600">{{ $player->height }} cm · {{ $player->weight }} kg</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('players.show', $player) }}" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-gray-100">Szczegóły</a>
                                    <button type="button" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-gray-100" @click="openModal = 'player-edit-{{ $player->id }}'">Edytuj</button>
                                    <form method="POST" action="{{ route('players.destroy', $player) }}" onsubmit="return confirm('Czy na pewno usunąć tego zawodnika?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">Brak zawodników.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>

        @if ($isAdmin || $isEmployee)
            <div x-show="openModal === 'match-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj mecz</h4>
                    <form method="POST" action="{{ route('matches.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @include('profile.partials.match-form-fields')
                        <div class="flex justify-between gap-3 pt-2">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Dodaj mecz</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($upcomingMatches->concat($finishedMatches) as $match)
                <div x-show="openModal === 'match-edit-{{ $match->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                        <h4 class="mb-4 text-lg font-semibold">Edytuj mecz</h4>
                        <form method="POST" action="{{ route('matches.update', $match) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            @include('profile.partials.match-form-fields', ['match' => $match])
                            <div class="flex justify-between gap-3 pt-2">
                                <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                                <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz zmiany</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <div x-show="openModal === 'news-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj aktualność</h4>
                    <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @include('profile.partials.news-form-fields')
                        <div class="flex justify-between">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($publishedNews->concat($scheduledNews) as $item)
                <div x-show="openModal === 'news-edit-{{ $item->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                        <h4 class="mb-4 text-lg font-semibold">Edytuj aktualność</h4>
                        <form method="POST" action="{{ route('news.update', $item) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            @include('profile.partials.news-form-fields', ['item' => $item])
                            <div class="flex justify-between">
                                <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                                <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz zmiany</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <div x-show="openModal === 'player-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj zawodnika</h4>
                    <form method="POST" action="{{ route('players.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @include('profile.partials.player-form-fields')
                        <div class="flex justify-between">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($players as $player)
                <div x-show="openModal === 'player-edit-{{ $player->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded bg-white p-6 shadow-xl" @click.outside="openModal = null">
                        <h4 class="mb-4 text-lg font-semibold">Edytuj zawodnika</h4>
                        <form method="POST" action="{{ route('players.update', $player) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            @include('profile.partials.player-form-fields', ['player' => $player])
                            <div class="flex justify-between">
                                <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                                <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz zmiany</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
