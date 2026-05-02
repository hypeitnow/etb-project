<article class="rounded-lg border border-gray-200 bg-gray-50 p-4">
    <div class="flex gap-4">
        @if ($match->opponent_logo)
            <img src="{{ asset('storage/'.$match->opponent_logo) }}"
                 alt="Logo przeciwnika {{ $match->opponent_name }}"
                 class="h-14 w-14 rounded bg-white object-contain p-1 ring-1 ring-gray-200">
        @else
            <div class="flex h-14 w-14 items-center justify-center rounded bg-white text-xs font-semibold text-gray-500 ring-1 ring-gray-200">
                Logo
            </div>
        @endif

        <div class="min-w-0 flex-1">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h5 class="font-semibold">{{ $match->opponent_name }}</h5>
                    <p class="text-sm text-gray-600">
                        {{ $match->match_date->format('d.m.Y H:i') }}
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                    {{ $match->is_home ? 'U siebie' : 'Wyjazd' }}
                </span>
            </div>

            <dl class="mt-3 grid gap-2 text-sm text-gray-700">
                <div>
                    <dt class="font-medium text-gray-900">Lokalizacja</dt>
                    <dd>{{ $match->location }}</dd>
                </div>

                @if ($match->hasResult())
                    <div>
                        <dt class="font-medium text-gray-900">Wynik</dt>
                        <dd>{{ $match->resultLabel() }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-4 flex flex-wrap gap-2">
                @can('update', $match)
                    <button type="button"
                            class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-gray-100"
                            @click="openModal = 'match-edit-{{ $match->id }}'">
                        Edytuj
                    </button>
                @endcan

                @can('delete', $match)
                    <form method="POST"
                          action="{{ route('matches.destroy', $match) }}"
                          onsubmit="return confirm('Czy na pewno usunąć ten mecz?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded border border-red-200 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">
                            Usuń
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</article>
