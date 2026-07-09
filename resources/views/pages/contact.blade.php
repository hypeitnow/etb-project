@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-10">
        <p class="text-sm font-bold uppercase tracking-[0.25em] text-yellow-400">ETB Basket</p>
        <h1 class="mt-2 text-4xl font-black text-white">Kontakt</h1>
    </div>

    @include('pages.partials.club-section-content', ['clubSection' => $clubSection, 'sectionId' => 'contact'])
</section>
@endsection
