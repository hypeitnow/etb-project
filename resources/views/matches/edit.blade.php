@extends('layouts.main')

@section('title', 'Edytuj mecz')

@section('content')
    <div class="mx-auto max-w-3xl px-6 py-10">
        <h1 class="mb-6 text-3xl font-bold">Edytuj mecz</h1>

        <form method="POST" action="{{ route('matches.update', $match) }}" enctype="multipart/form-data" class="space-y-4 rounded-lg border bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('profile.partials.match-form-fields', ['match' => $match])

            <button type="submit" class="rounded bg-black px-4 py-2 font-semibold text-white hover:bg-gray-800">
                Zapisz zmiany
            </button>
        </form>
    </div>
@endsection
