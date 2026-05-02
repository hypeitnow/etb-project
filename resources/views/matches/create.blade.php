@extends('layouts.main')

@section('title', 'Dodaj mecz')

@section('content')
    <div class="mx-auto max-w-3xl px-6 py-10">
        <h1 class="mb-6 text-3xl font-bold">Dodaj mecz</h1>

        <form method="POST" action="{{ route('matches.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-lg border bg-white p-6 shadow-sm">
            @csrf
            @include('profile.partials.match-form-fields')

            <button type="submit" class="rounded bg-black px-4 py-2 font-semibold text-white hover:bg-gray-800">
                Dodaj mecz
            </button>
        </form>
    </div>
@endsection
