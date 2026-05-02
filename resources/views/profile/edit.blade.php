@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-12 text-gray-900"
         x-data="{ openModal: null }"
         @keydown.escape.window="openModal = null">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Nie udało się zapisać formularza.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-semibold">Twoje konto</h3>

                <div class="grid gap-6 md:grid-cols-2">
                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.update-password-form')
                </div>
            </section>

            @if ($isAdmin)
                <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold">Zarządzanie użytkownikami</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $managedUser)
                                <tr>
                                    <td class="py-3 pr-4 font-medium">{{ $managedUser->name }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $managedUser->email }}</td>
                                    <td class="py-3">
                                        <form method="POST" action="{{ route('admin.users.role.update', $managedUser) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <select name="role" class="rounded border-gray-300 text-sm">
                                                @foreach ($availableRoles as $role)
                                                    <option value="{{ $role }}" @selected($managedUser->role === $role)>
                                                        {{ strtoupper($role) }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button class="rounded bg-yellow-500 px-3 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                                                Zapisz
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if ($isAdmin || $isEmployee)
                <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Mecze</h3>
                            <p class="text-sm text-gray-600">Dodawaj, edytuj i usuwaj mecze widoczne w serwisie.</p>
                        </div>

                        <button type="button"
                                class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                                @click="openModal = 'match-create'">
                            Dodaj mecz
                        </button>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-base font-semibold">Nadchodzące mecze</h4>

                            <div class="space-y-3">
                                @forelse ($upcomingMatches as $match)
                                    @include('profile.partials.match-card', ['match' => $match])
                                @empty
                                    <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">
                                        Brak nadchodzących meczów.
                                    </p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-3 text-base font-semibold">Zakończone mecze</h4>

                            <div class="space-y-3">
                                @forelse ($finishedMatches as $match)
                                    @include('profile.partials.match-card', ['match' => $match])
                                @empty
                                    <p class="rounded border border-dashed border-gray-300 p-4 text-sm text-gray-600">
                                        Brak zakończonych meczów.
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Aktualności</h3>
                            <button type="button"
                                    class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                                    @click="openModal = 'news'">
                                Dodaj
                            </button>
                        </div>

                        <div class="space-y-2 text-sm">
                            @forelse ($news as $item)
                                <p>{{ $item->title }}</p>
                            @empty
                                <p class="text-gray-600">Brak aktualności.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Zawodnicy</h3>
                            <button type="button"
                                    class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                                    @click="openModal = 'players'">
                                Dodaj
                            </button>
                        </div>

                        <div class="space-y-2 text-sm">
                            @forelse ($players as $player)
                                <p>{{ $player->first_name }} {{ $player->last_name }}</p>
                            @empty
                                <p class="text-gray-600">Brak zawodników.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            @endif
        </div>

        @if ($isAdmin || $isEmployee)
            <div x-show="openModal === 'match-create'"
                 x-cloak
                 x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl"
                     @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj mecz</h4>

                    <form method="POST" action="{{ route('matches.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @include('profile.partials.match-form-fields')

                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">
                                Anuluj
                            </button>
                            <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                Dodaj mecz
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($upcomingMatches->concat($finishedMatches) as $match)
                <div x-show="openModal === 'match-edit-{{ $match->id }}'"
                     x-cloak
                     x-transition
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl"
                         @click.outside="openModal = null">
                        <h4 class="mb-4 text-lg font-semibold">Edytuj mecz</h4>

                        <form method="POST" action="{{ route('matches.update', $match) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            @include('profile.partials.match-form-fields', ['match' => $match])

                            <div class="flex items-center justify-between gap-3 pt-2">
                                <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">
                                    Anuluj
                                </button>
                                <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    Zapisz zmiany
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <div x-show="openModal === 'news'"
                 x-cloak
                 x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj aktualność</h4>

                    <form method="POST" action="{{ route('news.store') }}" class="space-y-3">
                        @csrf
                        <input name="title" required placeholder="Tytuł" class="w-full rounded border-gray-300" />
                        <textarea name="content" required placeholder="Treść" class="w-full rounded border-gray-300"></textarea>
                        <input type="datetime-local" name="publish_at" class="w-full rounded border-gray-300" />

                        <div class="flex justify-between">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Zapisz</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="openModal === 'players'"
                 x-cloak
                 x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-semibold">Dodaj zawodnika</h4>

                    <form method="POST" action="{{ route('players.store') }}" class="space-y-3">
                        @csrf
                        <input name="first_name" required placeholder="Imię" class="w-full rounded border-gray-300" />
                        <input name="last_name" required placeholder="Nazwisko" class="w-full rounded border-gray-300" />
                        <input name="position" required placeholder="Pozycja" class="w-full rounded border-gray-300" />
                        <input type="number" name="number" required min="0" max="99" placeholder="Numer" class="w-full rounded border-gray-300" />
                        <input type="date" name="date_of_birth" required class="w-full rounded border-gray-300" />
                        <input type="number" name="height" min="100" max="250" placeholder="Wzrost" class="w-full rounded border-gray-300" />
                        <input type="number" name="weight" min="40" max="200" placeholder="Waga" class="w-full rounded border-gray-300" />

                        <div class="flex justify-between">
                            <button type="button" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Zapisz</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
