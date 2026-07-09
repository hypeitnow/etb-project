@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    @include('pages.partials.static-content-section', [
        'sectionId' => 'three-x-three-team',
        'eyebrow' => 'Rozgrywki',
        'title' => 'Zespół',
    ])
</section>
@endsection
