<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Informacje o profilu</h2>
        <p class="mt-1 text-sm text-gray-600">Zaktualizuj dane konta oraz adres e-mail.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nazwa" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Adres e-mail" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @if ($user->role === \App\Models\User::ROLE_FAN)
            <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                <input type="checkbox" name="marketing_email_consent" value="1" class="mt-1 rounded border-slate-300 text-yellow-500 focus:ring-yellow-500" @checked(old('marketing_email_consent', $user->fanProfile?->marketing_email_consent))>
                <span>
                    <span class="block text-sm font-bold text-slate-900">Zgoda na wiadomości marketingowe</span>
                    <span class="mt-1 block text-sm text-slate-600">Chcę otrzymywać informacje promocyjne, newsletter i wiadomości ETB na mój adres e-mail.</span>
                </span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('marketing_email_consent')" />
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Zapisz</x-primary-button>
        </div>
    </form>
</section>
