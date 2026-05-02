@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100 min-h-screen text-gray-900"
         x-data="{ openModal: null }"
         @keydown.escape.window="openModal = null">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 👤 PROFILE -->
            <div class="p-6 bg-white border border-gray-200 shadow rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Twoje konto</h3>

                <div class="grid md:grid-cols-2 gap-6">
                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- 👑 ADMIN USERS -->
            @if ($isAdmin)
                <div class="p-6 bg-white border border-gray-200 shadow rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Zarządzanie użytkownikami</h3>

                    <table class="min-w-full text-sm border">
                        <tbody>
                        @foreach ($users as $managedUser)
                            <tr class="border-t">
                                <td class="p-2">{{ $managedUser->name }}</td>
                                <td class="p-2">{{ $managedUser->email }}</td>
                                <td class="p-2">
                                    <form method="POST" action="{{ route('admin.users.role.update', $managedUser) }}">
                                        @csrf
                                        @method('PATCH')

                                        <select name="role" class="border rounded px-2 py-1">
                                            @foreach ($availableRoles as $role)
                                                <option value="{{ $role }}" @selected($managedUser->role === $role)>
                                                    {{ strtoupper($role) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button class="ml-2 px-3 py-1 bg-yellow-500 text-black rounded">
                                            Zapisz
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- ⚙️ PANEL -->
            @if ($isAdmin || $isEmployee)
                @foreach ([['Matches','matches','opponent'],['News','news','title'],['Players','players','first_name']] as [$label,$key,$field])
                    <div class="p-6 bg-white border border-gray-200 shadow rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Latest {{ $label }}</h3>

                            <button class="px-3 py-2 bg-indigo-600 text-white rounded"
                                    @click="openModal='{{ $key }}'">
                                Add
                            </button>
                        </div>

                        @forelse ($$key as $item)
                            <div class="text-sm mb-1">
                                {{ $key === 'players'
                                    ? $item->first_name.' '.$item->last_name
                                    : $item->$field }}
                            </div>
                        @empty
                            <div>Brak {{ strtolower($label) }}.</div>
                        @endforelse
                    </div>
                @endforeach
            @endif

        </div>

        <!-- ===================== -->
        <!-- 🧩 MODALS -->
        <!-- ===================== -->

        @if ($isAdmin || $isEmployee)

            <!-- MATCH -->
            <div x-show="openModal === 'matches'" x-transition
                 class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                <div @click.outside="openModal = null"
                     class="bg-white p-6 rounded-lg w-full max-w-lg">

                    <h4 class="font-semibold mb-4">Add match</h4>

                    <form method="POST" action="{{ route('matches.store') }}" class="space-y-3">
                        @csrf

                        <input name="opponent" required placeholder="Opponent" class="w-full border rounded p-2" />
                        <input type="datetime-local" name="match_date" required class="w-full border rounded p-2" />
                        <input name="location" required placeholder="Location" class="w-full border rounded p-2" />
                        <input name="result" placeholder="Result" class="w-full border rounded p-2" />
                        <input type="datetime-local" name="publish_at" class="w-full border rounded p-2" />

                        <div class="flex justify-between">
                            <button type="button" @click="openModal = null">Cancel</button>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- NEWS -->
            <div x-show="openModal === 'news'" x-transition
                 class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                <div @click.outside="openModal = null"
                     class="bg-white p-6 rounded-lg w-full max-w-lg">

                    <h4 class="font-semibold mb-4">Add news</h4>

                    <form method="POST" action="{{ route('news.store') }}" class="space-y-3">
                        @csrf

                        <input name="title" required class="w-full border rounded p-2" />
                        <textarea name="content" required class="w-full border rounded p-2"></textarea>
                        <input type="datetime-local" name="publish_at" class="w-full border rounded p-2" />

                        <div class="flex justify-between">
                            <button type="button" @click="openModal = null">Cancel</button>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PLAYER -->
            <div x-show="openModal === 'players'" x-transition
                 class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                <div @click.outside="openModal = null"
                     class="bg-white p-6 rounded-lg w-full max-w-lg">

                    <h4 class="font-semibold mb-4">Add player</h4>

                    <form method="POST" action="{{ route('players.store') }}" class="space-y-3">
                        @csrf

                        <input name="first_name" required placeholder="First name" class="w-full border rounded p-2" />
                        <input name="last_name" required placeholder="Last name" class="w-full border rounded p-2" />
                        <input name="position" required placeholder="Position" class="w-full border rounded p-2" />
                        <input type="number" name="number" required min="0" max="99" placeholder="Number" class="w-full border rounded p-2" />
                        <input type="date" name="date_of_birth" required class="w-full border rounded p-2" />
                        <input type="number" name="height" min="100" max="250" placeholder="Height" class="w-full border rounded p-2" />
                        <input type="number" name="weight" min="40" max="200" placeholder="Weight" class="w-full border rounded p-2" />

                        <div class="flex justify-between">
                            <button type="button" @click="openModal = null">Cancel</button>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        @endif

    </div>
@endsection
