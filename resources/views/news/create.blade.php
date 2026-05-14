@extends('layouts.app')

@section('content')
    <div class="bg-gray-100 py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded border border-gray-200 bg-white p-6 shadow-sm">
                <h1 class="mb-6 text-2xl font-semibold">Dodaj aktualność</h1>
                <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @include('profile.partials.news-form-fields')
                    <button class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Zapisz</button>
                </form>
            </div>
        </div>
    </div>
@endsection
