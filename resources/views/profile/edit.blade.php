@extends('layouts.app')

@php
    use App\Models\TeamMatch;
    use App\Models\ThreeXThreeTournament;

    $isPanelUser = $isAdmin || $isEmployee;
    $publishedNewsCount = $publishedNews->count();
    $scheduledNewsCount = $scheduledNews->count();
    $matchesCount = $upcomingMatches->count() + $finishedMatches->count();
    $usersCount = method_exists($users, 'total') ? $users->total() : $users->count();
@endphp

@section('content')
<div class="min-h-screen bg-slate-100 text-slate-950"
     x-data="adminPanel({
        currentAccount: @js(['name' => $user->name, 'email' => $user->email, 'role' => $user->role]),
        unreadCount: @js($unreadNotificationsCount),
     })"
     @keydown.escape.window="openModal = null">
    @if ($isPanelUser)
        <div class="grid min-h-screen lg:grid-cols-[18rem_1fr]">
            <aside class="bg-slate-950 text-white">
                <div class="sticky top-0 flex h-screen flex-col px-5 py-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-yellow-400 text-xl font-black text-black">ETB</span>
                        <span>
                            <span class="block text-2xl font-black leading-5 text-yellow-400">ETB</span>
                            <span class="block text-xs font-bold uppercase tracking-[0.22em] text-white">Admin</span>
                        </span>
                    </a>

                    <nav class="mt-8 space-y-6 text-sm">
                        <div>
                            <a href="#dashboard" class="flex items-center gap-3 rounded-lg bg-yellow-400 px-4 py-3 font-black text-black">
                                <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                                Pulpit
                            </a>
                        </div>

                        <div>
                            <p class="px-3 text-xs font-bold uppercase tracking-widest text-slate-400">Zarządzanie</p>
                            <div class="mt-3 space-y-1">
                                @if ($isAdmin)
                                    <a href="#users" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="users" class="h-4 w-4"></i>Użytkownicy</a>
                                @endif
                                <a href="#matches" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="calendar-days" class="h-4 w-4"></i>Mecze</a>
                                <a href="#news" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="newspaper" class="h-4 w-4"></i>Aktualności</a>
                                <a href="#players" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="user-round" class="h-4 w-4"></i>Zawodnicy</a>
                                <a href="#staff" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="user-cog" class="h-4 w-4"></i>Sztab szkoleniowy</a>
                                <a href="#three-x-three" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="circle-dot" class="h-4 w-4"></i>Drużyna 3x3</a>
                                <a href="#tournaments" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="trophy" class="h-4 w-4"></i>Turnieje 3x3</a>
                            </div>
                        </div>

                        <div>
                            <p class="px-3 text-xs font-bold uppercase tracking-widest text-slate-400">Terminarz</p>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('schedule.lzkosz') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="calendar" class="h-4 w-4"></i>Terminarz ŁZKosz</a>
                                <a href="{{ route('schedule.3x3') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="calendar-range" class="h-4 w-4"></i>Terminarz 3x3</a>
                                <a href="#sponsors" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="handshake" class="h-4 w-4"></i>Sponsorzy</a>
                            </div>
                        </div>
                    </nav>

                    <div class="mt-auto border-t border-white/10 pt-5">
                        <a href="#account" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-200 transition hover:bg-yellow-400 hover:text-black"><i data-lucide="settings" class="h-4 w-4"></i>Profil</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-slate-200 transition hover:bg-yellow-400 hover:text-black">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                                Wyloguj
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div>
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-slate-950 px-4 py-4 text-white shadow-sm sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <form class="relative w-full lg:max-w-md" role="search" @submit.prevent>
                            <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                            <input type="search" x-model="panelSearch" @input.debounce.150ms="searchPanel" placeholder="Szukaj w panelu admina..." class="w-full rounded-full border border-white/15 bg-white/5 py-2.5 pl-11 pr-4 text-sm text-white placeholder:text-slate-400 focus:border-yellow-400 focus:ring-yellow-400">
                        </form>
                        <div class="flex items-center gap-4">
                            <div class="relative" @click.outside="notificationsOpen = false">
                                <button type="button" class="relative inline-flex rounded-full p-2 transition hover:bg-white/10" @click="notificationsOpen = !notificationsOpen">
                                    <i data-lucide="bell" class="h-5 w-5 transition hover:animate-[admin-bell-ring_700ms_ease-in-out]"></i>
                                    <span x-show="unreadCount > 0" x-text="notificationBadge" class="absolute -right-1 -top-1 rounded-full bg-yellow-400 px-1.5 text-[10px] font-black text-black"></span>
                                </button>

                                <div x-show="notificationsOpen" x-cloak x-transition class="absolute right-0 z-50 mt-3 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-950 shadow-2xl">
                                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                                        <div>
                                            <h3 class="font-black">Powiadomienia</h3>
                                            <p class="text-xs text-slate-500">Globalny dziennik zmian pracowników.</p>
                                        </div>
                                        <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-black text-yellow-900" x-text="notificationBadge"></span>
                                    </div>

                                    <div class="max-h-[28rem] divide-y divide-slate-100 overflow-y-auto">
                                        @forelse ($adminNotifications as $notification)
                                            <article class="p-4 transition hover:bg-yellow-50">
                                                <div class="flex items-start justify-between gap-3">
                                                    <button type="button" class="min-w-0 text-left" @click="previewNotification = {{ Js::from([
                                                        'title' => $notification->subject_label,
                                                        'description' => $notification->description,
                                                        'actor' => $notification->actor?->name ?? 'System',
                                                        'date' => $notification->created_at?->format('d.m.Y H:i'),
                                                        'status' => $notification->isAccepted() ? 'Zaakceptowane' : 'Oczekuje',
                                                    ]) }}">
                                                        <p class="font-black">{{ $notification->subject_label }}</p>
                                                        <p class="mt-1 text-sm text-slate-600">{{ $notification->description }}</p>
                                                        <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->format('d.m.Y H:i') }} · {{ $notification->actor?->name ?? 'System' }}</p>
                                                    </button>
                                                    <div class="flex shrink-0 gap-1">
                                                        <form method="POST" action="{{ route('admin.notifications.accept', $notification) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button title="Akceptuj" class="rounded-lg border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50"><i data-lucide="check" class="h-4 w-4"></i></button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button title="Oznacz jako przeczytane" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50"><i data-lucide="mail-check" class="h-4 w-4"></i></button>
                                                        </form>
                                                        @if ($isAdmin || $notification->actor_id === $user->id)
                                                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Usunąć to powiadomienie?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button title="Usuń" class="rounded-lg border border-red-200 bg-white p-2 text-red-700 hover:bg-red-50"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </article>
                                        @empty
                                            <p class="p-4 text-sm text-slate-500">Brak powiadomień.</p>
                                        @endforelse
                                    </div>

                                    <div x-show="previewNotification" x-cloak class="border-t border-slate-200 bg-slate-50 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-yellow-700">Podgląd zmiany</p>
                                                <h4 class="mt-1 font-black" x-text="previewNotification?.title"></h4>
                                            </div>
                                            <button type="button" class="rounded p-1 hover:bg-slate-200" @click="previewNotification = null"><i data-lucide="x" class="h-4 w-4"></i></button>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-700" x-text="previewNotification?.description"></p>
                                        <p class="mt-3 text-xs text-slate-500"><span x-text="previewNotification?.actor"></span> · <span x-text="previewNotification?.date"></span> · <span x-text="previewNotification?.status"></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative" @click.outside="accountOpen = false">
                                <button type="button" class="flex items-center gap-2 rounded-full px-2 py-1 transition hover:bg-white/10" @click="accountOpen = !accountOpen">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-900">
                                        <i data-lucide="user-round" class="h-5 w-5"></i>
                                    </span>
                                    <span class="font-semibold">{{ $isAdmin ? 'Admin' : 'Pracownik' }}</span>
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </button>

                                <div x-show="accountOpen" x-cloak x-transition class="absolute right-0 z-50 mt-3 w-72 overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-950 shadow-2xl">
                                    <div class="border-b border-slate-200 p-4">
                                        <p class="font-black">{{ $user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                    </div>
                                    <div class="p-3">
                                        <p class="px-2 text-xs font-bold uppercase tracking-wide text-slate-500">Konta zapisane na tym urządzeniu</p>
                                        <template x-for="account in savedAccounts" :key="account.email">
                                            <button type="button" class="mt-2 flex w-full items-center gap-3 rounded-lg px-2 py-2 text-left hover:bg-yellow-50" @click="switchAccount(account)">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-xs font-black text-white" x-text="account.name.slice(0,2).toUpperCase()"></span>
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-bold" x-text="account.name"></span>
                                                    <span class="block truncate text-xs text-slate-500" x-text="account.email"></span>
                                                </span>
                                            </button>
                                        </template>
                                        <button type="button" class="mt-3 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50" @click="saveCurrentAccount">Zapisz bieżące konto na urządzeniu</button>
                                        <p class="mt-2 text-xs text-slate-500">Przełączenie prowadzi do logowania z wybranym adresem. Hasła nie są zapisywane.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main id="dashboard" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
                    @endif

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

                    <section>
                        <h1 class="text-3xl font-black text-slate-950">Pulpit administratora</h1>
                        <p class="mt-1 text-sm text-slate-600">Witaj ponownie! Oto najważniejsze informacje z systemu.</p>
                    </section>

                    <section class="overflow-hidden rounded-lg border border-yellow-300 bg-yellow-50 p-5 shadow-sm">
                        <div class="grid gap-5 xl:grid-cols-[1.2fr_repeat(4,1fr)]">
                            <div class="flex items-center gap-5">
                                <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-yellow-300 text-slate-950">
                                    <i data-lucide="users-round" class="h-10 w-10"></i>
                                </span>
                                <div>
                                    <h2 class="text-lg font-black">Witaj ponownie, {{ $isAdmin ? 'Administratorze' : 'Pracowniku' }}!</h2>
                                    <p class="mt-1 text-sm text-slate-600">Miło Cię znowu widzieć w panelu zarządzania ETB.</p>
                                </div>
                            </div>

                            @foreach ([
                                ['icon' => 'users', 'value' => $usersCount, 'label' => 'Użytkowników', 'hint' => 'Wszystkich kont'],
                                ['icon' => 'calendar-days', 'value' => $matchesCount, 'label' => 'Mecze', 'hint' => 'W tym sezonie'],
                                ['icon' => 'newspaper', 'value' => $publishedNewsCount, 'label' => 'Aktualności', 'hint' => 'Opublikowanych'],
                                ['icon' => 'trophy', 'value' => $threeXThreeTournaments->count(), 'label' => 'Turnieje 3x3', 'hint' => 'Zaplanowanych i zakończonych'],
                                ['icon' => 'handshake', 'value' => $sponsors->count(), 'label' => 'Sponsorzy', 'hint' => 'W bazie partnerow'],
                            ] as $stat)
                                <article class="rounded-lg bg-white/80 p-5 shadow-sm">
                                    <div class="flex items-start gap-3">
                                        <i data-lucide="{{ $stat['icon'] }}" class="mt-1 h-7 w-7 text-yellow-500"></i>
                                        <div>
                                            <p class="text-2xl font-black">{{ $stat['value'] }}</p>
                                            <p class="text-sm font-bold">{{ $stat['label'] }}</p>
                                            <p class="mt-2 text-sm text-slate-500">{{ $stat['hint'] }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    @if ($isAdmin)
                        <section id="users" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" x-data="adminUserSearch(@js(route('admin.users.search')))">
                            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h2 class="text-xl font-black">Zarządzanie użytkownikami</h2>
                                    <p class="text-sm text-slate-600">Ostatnio dodani użytkownicy w systemie.</p>
                                </div>
                                <div class="relative w-full sm:w-80">
                                    <input type="search" x-model="query" @input.debounce.350ms="search" placeholder="Szukaj po nazwie lub e-mailu" class="w-full rounded-lg border-slate-300 pr-10 text-sm">
                                    <div x-show="results.length" x-cloak class="absolute right-0 z-30 mt-2 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
                                        <template x-for="user in results" :key="user.id">
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-yellow-50" @click="focusUser(user)">
                                                <span class="block font-semibold" x-text="user.name"></span>
                                                <span class="block text-xs text-slate-500" x-text="user.email"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-scroll-list overflow-x-auto">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3">Użytkownik</th>
                                            <th class="px-3 py-3">E-mail</th>
                                            <th class="px-3 py-3">Rola</th>
                                            <th class="px-3 py-3">Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($users as $managedUser)
                                            <tr id="managed-user-{{ $managedUser->id }}" data-admin-search class="etb-admin-card">
                                                <td class="px-3 py-3 font-semibold">
                                                    <span class="mr-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-xs font-black text-white">{{ str($managedUser->name)->substr(0, 2)->upper() }}</span>
                                                    {{ $managedUser->name }}
                                                </td>
                                                <td class="px-3 py-3 text-slate-600">{{ $managedUser->email }}</td>
                                                <td class="px-3 py-3">
                                                    <form method="POST" action="{{ route('admin.users.role.update', $managedUser) }}" class="flex flex-wrap items-center gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="role" class="rounded-lg border-slate-300 text-sm">
                                                            @foreach ($availableRoles as $role)
                                                                <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ $role }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="rounded-lg bg-yellow-400 px-3 py-2 text-xs font-black text-black hover:bg-yellow-300">Zapisz</button>
                                                    </form>
                                                </td>
                                                <td class="px-3 py-3">
                                                    <a href="#managed-user-{{ $managedUser->id }}" class="inline-flex rounded-lg border border-slate-200 bg-white p-2 hover:bg-yellow-50"><i data-lucide="eye" class="h-4 w-4"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-5">{{ $users->links() }}</div>
                        </section>
                    @endif

                    <section id="matches" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Mecze</h2>
                                <p class="text-sm text-slate-600">Nadchodzące i ostatnio zakończone mecze.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <select x-model="matchFilter" class="rounded-lg border-slate-300 text-sm">
                                    <option value="all">Wszystkie mecze</option>
                                    <option value="upcoming">Nadchodzące</option>
                                    <option value="finished">Zakończone</option>
                                </select>
                                <button type="button" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300" @click="openModal = 'match-create'">Dodaj mecz</button>
                            </div>
                        </div>

                        <div class="grid gap-6 xl:grid-cols-2">
                            <div x-show="matchFilter === 'all' || matchFilter === 'upcoming'">
                                <h3 class="mb-3 font-black">Nadchodzące mecze</h3>
                                <div class="admin-scroll-list space-y-3">
                                    @forelse ($upcomingMatches as $match)
                                        @include('profile.partials.match-card', ['match' => $match])
                                    @empty
                                        <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak nadchodzących meczów.</p>
                                    @endforelse
                                </div>
                            </div>
                            <div x-show="matchFilter === 'all' || matchFilter === 'finished'">
                                <h3 class="mb-3 font-black">Ostatnio zakończone mecze</h3>
                                <div class="admin-scroll-list space-y-3">
                                    @forelse ($finishedMatches as $match)
                                        @include('profile.partials.match-card', ['match' => $match])
                                    @empty
                                        <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak zakończonych meczów.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="news" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" x-data="newsLightbox()">
                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Aktualności</h2>
                                <p class="text-sm text-slate-600">Publikuj od razu albo ustaw datę przyszłej publikacji.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <select x-model="newsFilter" class="rounded-lg border-slate-300 text-sm">
                                    <option value="all">Wszystkie</option>
                                    <option value="published">Opublikowane</option>
                                    <option value="scheduled">Zaplanowane</option>
                                </select>
                                <button type="button" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300" @click="openModal = 'news-create'">Dodaj aktualność</button>
                            </div>
                        </div>

                        <div class="grid gap-6 xl:grid-cols-2">
                            @foreach ([['items' => $publishedNews, 'type' => 'published', 'title' => 'Opublikowane'], ['items' => $scheduledNews, 'type' => 'scheduled', 'title' => 'Zaplanowane']] as $group)
                                <div x-show="newsFilter === 'all' || newsFilter === '{{ $group['type'] }}'">
                                    <h3 class="mb-3 font-black">{{ $group['title'] }}</h3>
                                    <div class="admin-scroll-list space-y-3">
                                        @forelse ($group['items'] as $item)
                                            <article data-admin-search class="etb-admin-card rounded-lg border border-slate-200 bg-slate-50 p-4">
                                                <div class="flex gap-4">
                                                    @if ($item->main_image_path)
                                                        <img src="{{ asset('storage/'.$item->main_image_path) }}" alt="{{ $item->title }}" class="h-20 w-24 rounded-lg object-cover">
                                                    @endif
                                                    <div class="min-w-0 flex-1">
                                                        <h4 class="font-black">{{ $item->title }}</h4>
                                                        <p class="text-sm text-slate-600">{{ $item->publish_at?->format('d.m.Y H:i') ?? 'Publikacja natychmiastowa' }}</p>
                                                        <div class="mt-3 flex flex-wrap gap-2">
                                                            <a href="{{ route('admin.news.preview', $item) }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50">Podgląd</a>
                                                            @if ($group['type'] === 'scheduled')
                                                                <button type="button" class="rounded-lg bg-yellow-400 px-3 py-1.5 text-sm font-black text-black hover:bg-yellow-300" @click="publishAction = '{{ route('admin.news.publish', $item) }}'; openModal = 'news-publish-confirm'">Opublikuj</button>
                                                            @endif
                                                            <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50" @click="openModal = 'news-edit-{{ $item->id }}'">Edytuj</button>
                                                            <form method="POST" action="{{ route('news.destroy', $item) }}" onsubmit="return confirm('Czy na pewno usunąć tę aktualność?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        @empty
                                            <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak aktualności.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    @foreach ([
                        ['id' => 'players', 'title' => 'Zawodnicy', 'subtitle' => 'Pełna edycja kart zawodników oraz zdjęć.', 'button' => 'Dodaj zawodnika', 'modal' => 'player-create', 'items' => $players, 'type' => 'player'],
                        ['id' => 'staff', 'title' => 'Sztab szkoleniowy', 'subtitle' => 'Karty publicznej sekcji sztabu.', 'button' => 'Dodaj osobę', 'modal' => 'staff-create', 'items' => $staff, 'type' => 'staff'],
                        ['id' => 'three-x-three', 'title' => 'Drużyna 3x3', 'subtitle' => 'Osobne karty zawodników i trenera 3x3.', 'button' => 'Dodaj osobę', 'modal' => '3x3-member-create', 'items' => $threeXThreeMembers, 'type' => 'member'],
                        ['id' => 'tournaments', 'title' => 'Turnieje 3x3', 'subtitle' => 'Dodawanie, edycja, usuwanie i filtrowanie turniejów.', 'button' => 'Dodaj turniej', 'modal' => '3x3-tournament-create', 'items' => $threeXThreeTournaments, 'type' => 'tournament'],
                    ] as $section)
                        <section id="{{ $section['id'] }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-xl font-black">{{ $section['title'] }}</h2>
                                    <p class="text-sm text-slate-600">{{ $section['subtitle'] }}</p>
                                </div>
                                <button type="button" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300" @click="openModal = '{{ $section['modal'] }}'">{{ $section['button'] }}</button>
                            </div>

                            <div class="admin-scroll-list grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                @forelse ($section['items'] as $item)
                                    <article data-admin-search class="etb-admin-card rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        @if ($section['type'] === 'player')
                                            <div class="flex gap-4">
                                                @if ($item->photo_path)
                                                    <img src="{{ asset('storage/'.$item->photo_path) }}" alt="{{ $item->full_name }}" class="h-24 w-20 rounded-lg object-cover">
                                                @else
                                                    <div class="flex h-24 w-20 items-center justify-center rounded-lg bg-slate-200 text-xs font-bold text-slate-500">ETB</div>
                                                @endif
                                                <div>
                                                    <p class="text-2xl font-black">#{{ $item->number }}</p>
                                                    <h3 class="font-black">{{ $item->full_name }}</h3>
                                                    <p class="text-sm text-slate-600">{{ $item->positionLabel() }}</p>
                                                    <p class="text-sm text-slate-600">{{ $item->height }} cm · {{ $item->weight }} kg</p>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <a href="{{ route('players.show', $item) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50">Szczegóły</a>
                                                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50" @click="openModal = 'player-edit-{{ $item->id }}'">Edytuj</button>
                                                <form method="POST" action="{{ route('players.destroy', $item) }}" onsubmit="return confirm('Czy na pewno usunąć tego zawodnika?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                                </form>
                                            </div>
                                        @elseif ($section['type'] === 'tournament')
                                            <h3 class="font-black">{{ $item->name }}</h3>
                                            <p class="text-sm text-slate-600">{{ $item->date?->format('d.m.Y') }} · {{ $item->location }}</p>
                                            <p class="mt-1 text-xs font-bold uppercase text-slate-500">{{ $item->status === ThreeXThreeTournament::STATUS_FINISHED ? 'Zakończony' : 'Nadchodzący' }}</p>
                                            @if ($item->categories->isNotEmpty())
                                                <div class="mt-3 flex flex-wrap gap-1.5">
                                                    @foreach ($item->categories as $category)
                                                        <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-bold text-yellow-900">{{ $category->category->label() }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <a href="{{ route('three-x-three.tournaments.show', $item) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50">Podgląd</a>
                                                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50" @click="openModal = '3x3-tournament-edit-{{ $item->id }}'">Edytuj</button>
                                                <form method="POST" action="{{ route('tournaments.destroy', $item) }}" onsubmit="return confirm('Czy na pewno usunąć ten turniej?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="flex gap-4">
                                                @if ($item->photo_path)
                                                    <img src="{{ asset('storage/'.$item->photo_path) }}" alt="{{ $item->name }}" class="h-24 w-20 rounded-lg object-cover">
                                                @else
                                                    <div class="flex h-24 w-20 items-center justify-center rounded-lg bg-slate-200 text-xs font-bold text-slate-500">ETB</div>
                                                @endif
                                                <div>
                                                    <h3 class="font-black">{{ $item->name }}</h3>
                                                    <p class="text-sm text-slate-600">{{ $item->role }}</p>
                                                    @if (($section['type'] === 'member') && $item->is_coach)
                                                        <p class="mt-1 text-xs font-bold uppercase text-yellow-700">Trener</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50" @click="openModal = '{{ $section['type'] === 'staff' ? 'staff-edit' : '3x3-member-edit' }}-{{ $item->id }}'">Edytuj</button>
                                                <form method="POST" action="{{ $section['type'] === 'staff' ? route('staff.destroy', $item) : route('members.destroy', $item) }}" onsubmit="return confirm('Czy na pewno usunąć ten rekord?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usuń</button>
                                                </form>
                                            </div>
                                        @endif
                                    </article>
                                @empty
                                    <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">Brak rekordów w tej sekcji.</p>
                                @endforelse
                            </div>
                        </section>
                    @endforeach

                    <section id="sponsors" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Sponsorzy</h2>
                                <p class="text-sm text-slate-600">Logo, linki i typy partnerow widoczne w stopce kazdej strony.</p>
                            </div>
                            <button type="button" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300" @click="openModal = 'sponsor-create'">Dodaj sponsora</button>
                        </div>

                        <div class="admin-scroll-list grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @forelse ($sponsors as $sponsor)
                                <article data-admin-search class="etb-admin-card rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex gap-4">
                                        <div class="flex h-20 w-28 shrink-0 items-center justify-center rounded-lg bg-white p-3">
                                            <img src="{{ asset('storage/'.$sponsor->logo_path) }}" alt="{{ $sponsor->name }}" class="max-h-14 w-full object-contain">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="truncate font-black">{{ $sponsor->name }}</h3>
                                            <p class="text-sm font-semibold text-yellow-700">{{ $sponsor->typeLabel() }}</p>
                                            <a href="{{ $sponsor->url }}" target="_blank" rel="noopener noreferrer" class="block truncate text-sm text-slate-600 hover:text-yellow-700">{{ $sponsor->url }}</a>
                                            <p class="mt-1 text-xs font-bold uppercase {{ $sponsor->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $sponsor->is_active ? 'Widoczny' : 'Ukryty' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-yellow-50" @click="openModal = 'sponsor-edit-{{ $sponsor->id }}'">Edytuj</button>
                                        <form method="POST" action="{{ route('sponsors.destroy', $sponsor) }}" onsubmit="return confirm('Czy na pewno usunac tego sponsora?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Usun</button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600 md:col-span-2 xl:col-span-3">Brak sponsorow. Dodaj pierwszego partnera, aby pojawil sie w stopce strony.</p>
                            @endforelse
                        </div>
                    </section>

                    <section id="account" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-4 text-xl font-black">Profil i konto</h2>
                        <div class="grid gap-6 xl:grid-cols-2">
                            @include('profile.partials.update-profile-information-form')
                            @include('profile.partials.update-password-form')
                        </div>
                    </section>
                </main>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
            <section class="rounded-lg border border-slate-200 bg-white p-6 text-slate-950 shadow-sm">
                <h1 class="text-2xl font-black">Twoje konto</h1>
                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.update-password-form')
                </div>
            </section>
        </div>
    @endif

    @if ($isPanelUser)
        <div x-show="openModal === 'match-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj mecz</h4>
                <form method="POST" action="{{ route('matches.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.match-form-fields')
                    <div class="flex justify-between gap-3 pt-2">
                        <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                        <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Dodaj mecz</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($upcomingMatches->concat($finishedMatches) as $match)
            <div x-show="openModal === 'match-edit-{{ $match->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj mecz</h4>
                    <form method="POST" action="{{ route('matches.update', $match) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.match-form-fields', ['match' => $match])
                        <div class="flex justify-between gap-3 pt-2">
                            <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === 'news-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj aktualność</h4>
                <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.news-form-fields')
                    <div class="flex justify-between">
                        <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                        <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openModal === 'news-publish-confirm'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="text-lg font-black">Publikacja artykułu</h4>
                <p class="mt-3 text-sm text-slate-600">Czy na pewno chcesz opublikować ten artykuł?</p>
                <form method="POST" :action="publishAction" class="mt-6 flex justify-between gap-3">
                    @csrf
                    @method('PATCH')
                    <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null; publishAction = null">Anuluj</button>
                    <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Opublikuj</button>
                </form>
            </div>
        </div>

        @foreach ($publishedNews->concat($scheduledNews) as $item)
            <div x-show="openModal === 'news-edit-{{ $item->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj aktualność</h4>
                    <form method="POST" action="{{ route('news.update', $item) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.news-form-fields', ['item' => $item])
                        <div class="flex justify-between">
                            <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === 'player-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj zawodnika</h4>
                <form method="POST" action="{{ route('players.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.player-form-fields')
                    <div class="flex justify-between">
                        <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                        <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($players as $player)
            <div x-show="openModal === 'player-edit-{{ $player->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj zawodnika</h4>
                    <form method="POST" action="{{ route('players.update', $player) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.player-form-fields', ['player' => $player])
                        <div class="flex justify-between">
                            <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button>
                            <button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === 'staff-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj osobę do sztabu</h4>
                <form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.media-card-form-fields')
                    <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button></div>
                </form>
            </div>
        </div>

        @foreach ($staff as $person)
            <div x-show="openModal === 'staff-edit-{{ $person->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj osobę w sztabie</h4>
                    <form method="POST" action="{{ route('staff.update', $person) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.media-card-form-fields', ['item' => $person])
                        <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button></div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === '3x3-member-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj osobę do drużyny 3x3</h4>
                <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.media-card-form-fields', ['withCoach' => true])
                    <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button></div>
                </form>
            </div>
        </div>

        @foreach ($threeXThreeMembers as $member)
            <div x-show="openModal === '3x3-member-edit-{{ $member->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj osobę w drużynie 3x3</h4>
                    <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.media-card-form-fields', ['item' => $member, 'withCoach' => true])
                        <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button></div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === '3x3-tournament-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj turniej 3x3</h4>
                <form method="POST" action="{{ route('tournaments.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.tournament-form-fields')
                    <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button></div>
                </form>
            </div>
        </div>

        @foreach ($threeXThreeTournaments as $tournament)
            <div x-show="openModal === '3x3-tournament-edit-{{ $tournament->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj turniej 3x3</h4>
                    <form method="POST" action="{{ route('tournaments.update', $tournament) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.tournament-form-fields', ['tournament' => $tournament])
                        <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button></div>
                    </form>
                </div>
            </div>
        @endforeach

        <div x-show="openModal === 'sponsor-create'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                <h4 class="mb-4 text-lg font-black">Dodaj sponsora</h4>
                <form method="POST" action="{{ route('sponsors.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @include('profile.partials.sponsor-form-fields')
                    <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz</button></div>
                </form>
            </div>
        </div>

        @foreach ($sponsors as $sponsor)
            <div x-show="openModal === 'sponsor-edit-{{ $sponsor->id }}'" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 text-slate-950 shadow-xl" @click.outside="openModal = null">
                    <h4 class="mb-4 text-lg font-black">Edytuj sponsora</h4>
                    <form method="POST" action="{{ route('sponsors.update', $sponsor) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('profile.partials.sponsor-form-fields', ['sponsor' => $sponsor])
                        <div class="flex justify-between"><button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" @click="openModal = null">Anuluj</button><button class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black text-black hover:bg-yellow-300">Zapisz zmiany</button></div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
